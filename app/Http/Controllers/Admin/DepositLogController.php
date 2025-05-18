<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CryptoWallet;
use App\Traits\CryptoWalletGenerate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DepositLogController extends Controller
{
    use CryptoWalletGenerate;

    public function index()
    {
        $depositRecord = CryptoWallet::selectRaw('COUNT(id) AS totalDepositLog')
            ->selectRaw('COUNT(CASE WHEN status = 1 THEN id END) AS depositSuccess')
            ->selectRaw('(COUNT(CASE WHEN status = 1 THEN id END) / COUNT(id)) * 100 AS depositSuccessPercentage')
            ->selectRaw('COUNT(CASE WHEN status = 2 THEN id END) AS pendingDeposit')
            ->selectRaw('(COUNT(CASE WHEN status = 2 THEN id END) / COUNT(id)) * 100 AS pendingDepositPercentage')
            ->selectRaw('COUNT(CASE WHEN status = 3 THEN id END) AS cancelDeposit')
            ->selectRaw('(COUNT(CASE WHEN status = 3 THEN id END) / COUNT(id)) * 100 AS cancelDepositPercentage')
            ->get()
            ->toArray();

        return view('admin.deposit.logs', compact('depositRecord'));
    }

    public function search(Request $request)
    {
        $filterTransactionId = $request->filterTransactionID;
        $filterStatus = $request->filterStatus;
        $search = $request->search['value'] ?? null;

        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $deposit = CryptoWallet::with('user')->orderBy('id', 'desc')
            ->where('status', '!=', 0)
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where(function ($subquery) use ($search) {
                    $subquery->where('utr', 'LIKE', "%$search%")
                        ->orWhere('wallet_address', "%$search%")
                        ->orWhere('currency_code', "%$search%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('firstname', 'LIKE', "%$search%")
                                ->orWhere('lastname', 'LIKE', "%{$search}%")
                                ->orWhere('username', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when(!empty($filterTransactionId), function ($query) use ($filterTransactionId) {
                return $query->where('utr', $filterTransactionId);
            })
            ->when(isset($filterStatus), function ($query) use ($filterStatus) {
                if ($filterStatus == "all") {
                    return $query->where('status', '!=', null);
                }
                return $query->where('status', $filterStatus);
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });


        return DataTables::of($deposit)
            ->addColumn('trx', function ($item) {
                return $item->utr;
            })
            ->addColumn('amount', function ($item) {
                if ($item->amount == 0) {
                    return '?' . ' ' . $item->currency_code;
                } else {
                    return rtrim(rtrim($item->amount, 0), '.') . ' ' . $item->currency_code;
                }
            })
            ->addColumn('address', function ($item) {
                $element = "referralsKeyCode" . $item->id;
                return '<div class="input-group input-group-sm input-group-merge table-input-group">
                        <input id="' . $element . '" type="text" class="form-control" readonly value="' . $item->wallet_address . '">
                        <a class="js-clipboard input-group-append input-group-text" onclick="copyFunction(\'' . $element . '\')" href="javascript:void(0)" title="Copy to clipboard">
                            <i id="referralsKeyCodeIcon' . $item->id . '" class="bi-clipboard"></i>
                        </a>
                    </div>';
            })
            ->addColumn('user', function ($item) {
                $url = route('admin.user.view.profile', $item->user_id);
                return '<a class="d-flex align-items-center me-2" href="' . $url . '">
                                <div class="flex-shrink-0">
                                  ' . optional($item->user)->profilePicture() . '
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . optional($item->user)->firstname . ' ' . optional($item->user)->lastname . '</h5>
                                  <span class="fs-6 text-body">' . optional($item->user)->username . '</span>
                                </div>
                              </a>';
            })
            ->addColumn('status', function ($item) {
                if ($item->status == 1) {
                    return '<span class="badge bg-soft-success text-success">' . trans('Successful') . '</span>';
                } else if ($item->status == 2) {
                    return '<span class="badge bg-soft-warning text-warning">' . trans('Pending') . '</span>';
                } else if ($item->status == 3) {
                    return '<span class="badge bg-soft-danger text-danger">' . trans('Cancel') . '</span>';
                }
            })
            ->addColumn('date', function ($item) {
                return dateTime($item->created_at);
            })
            ->addColumn('action', function ($item) {
                if ($item->type == 'manual') {
                    $icon = $item->status == 2 ? 'pencil' : 'eye';
                    return "<button type='button' class='btn btn-white btn-sm edit_btn' data-bs-target='#accountInvoiceReceiptModal'
                data-info='$item->proof'
                data-code='$item->currency_code'
                data-status='$item->status'
                data-action='" . route('admin.deposit.action', $item->id) . "'
                data-bs-toggle='modal'
                data-bs-target='#accountInvoiceReceiptModal'>  <i class='bi-$icon fill me-1'></i> </button>";
                } else {
                    return '-';
                }

            })
            ->rawColumns(['trx', 'amount', 'address', 'user', 'status', 'date', 'action'])->make(true);
    }

    public function action(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(['1', '3'])],
            'amount' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $crypto_wallet = CryptoWallet::where('id', $id)->whereIn('status', [2])->firstOrFail();

        if ($request->status == '1') {
            if (!$request->amount) {
                return back()->with('error', 'Please give a amount');
            }

            $crypto_wallet->amount = $request->amount;
            $crypto_wallet->status = 1;
            $crypto_wallet->save();

            $this->walletUpgration($crypto_wallet, 'deposit', $crypto_wallet->amount);

        } elseif ($request->status == '3') {
            $crypto_wallet->status = 3;
            $crypto_wallet->save();
        }
        return back();
    }
}
