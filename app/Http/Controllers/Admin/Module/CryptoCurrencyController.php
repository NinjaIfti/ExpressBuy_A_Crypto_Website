<?php

namespace App\Http\Controllers\Admin\Module;

use App\Http\Controllers\Controller;
use App\Http\Requests\CryptoStoreRequest;
use App\Models\CryptoCurrency;
use App\Models\CryptoMethod;
use App\Traits\CurrencyRateUpdate;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CryptoCurrencyController extends Controller
{
    use Upload, CurrencyRateUpdate;

    public function cryptoList()
    {
        $data['currency'] = collect(CryptoCurrency::selectRaw('COUNT(id) AS totalCurrency')
            ->selectRaw('COUNT(CASE WHEN status = 1 THEN id END) AS activeCurrency')
            ->selectRaw('(COUNT(CASE WHEN status = 1 THEN id END) / COUNT(id)) * 100 AS activeCurrencyPercentage')
            ->selectRaw('COUNT(CASE WHEN status = 0 THEN id END) AS inActiveCurrency')
            ->selectRaw('(COUNT(CASE WHEN status = 0 THEN id END) / COUNT(id)) * 100 AS inActiveCurrencyPercentage')
            ->selectRaw('COUNT(CASE WHEN DATE(created_at) = CURRENT_DATE THEN id END) AS todayCurrency')
            ->selectRaw('(COUNT(CASE WHEN DATE(created_at) = CURRENT_DATE THEN id END) / COUNT(id)) * 100 AS todayCurrencyPercentage')
            ->selectRaw('COUNT(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN id END) AS thisMonthCurrency')
            ->selectRaw('(COUNT(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN id END) / COUNT(id)) * 100 AS thisMonthCurrencyPercentage')
            ->get()
            ->toArray())->collapse();
        return view('admin.currency.crypto.index', $data);
    }

    public function cryptoSort(Request $request)
    {
        $sortItems = $request->sort;
        foreach ($sortItems as $key => $value) {
            CryptoCurrency::where('code', $value)->update(['sort_by' => $key + 1]);
        }
    }

    public function cryptoListSearch(Request $request)
    {
        $search = $request->search['value'] ?? null;
        $filterName = $request->name;
        $filterStatus = $request->filterStatus;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;

        $currencies = CryptoCurrency::orderBy('sort_by', 'ASC')
            ->when(isset($filterName), function ($query) use ($filterName) {
                return $query->where('name', 'LIKE', '%' . $filterName . '%');
            })
            ->when(isset($filterStatus), function ($query) use ($filterStatus) {
                if ($filterStatus != "all") {
                    return $query->where('status', $filterStatus);
                }
            })
            ->when(!empty($request->filterDate) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->filterDate) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where(function ($subquery) use ($search) {
                    $subquery->where('name', 'LIKE', "%$search%")
                        ->orWhere('code', 'LIKE', "%$search%")
                        ->orWhere('symbol', 'LIKE', "%$search%")
                        ->orWhere('rate', 'LIKE', "%$search%")
                        ->orWhere('usd_rate', 'LIKE', "%$search%");
                });
            });
        return DataTables::of($currencies)
            ->addColumn('checkbox', function ($item) {
                return '<input type="checkbox" id="chk-' . $item->id . '"
                                       class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                                       data-id="' . $item->id . '">';

            })
            ->addColumn('name', function ($item) {
                $url = getFile($item->driver, $item->image);
                return '<a class="d-flex align-items-center me-2">
                                <div class="list-group-item">
                                 <i class="sortablejs-custom-handle bi-grip-horizontal list-group-icon"></i>
                                </div>
                                <div class="flex-shrink-0">
                                  <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img" src="' . $url . '" alt="Image Description">
                                  </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">' . $item->name . '</h5>
                                  <span class="fs-6 text-body">' . $item->symbol . ' - ' . $item->code . '</span>
                                </div>
                              </a>';

            })
            ->addColumn('rate', function ($item) {
                return '<div class="flex-grow-1 ms-3">
                                  <h5 class="text-hover-primary mb-0">1 ' . $item->code . ' = ' . number_format($item->rate) . ' ' . basicControl()->base_currency . '</h5>
                                  <span class="fs-6 text-body">1 ' . $item->code . ' = ' . number_format($item->usd_rate) . ' USD' . '</span>
                                </div>';
            })
            ->addColumn('service_fee', function ($item) {
                if ($item->service_fee_type == 'flat') {
                    return $item->code . ' ' . rtrim(rtrim($item->service_fee, 0), '.');
                } else {
                    return $item->code . ' ' . rtrim(rtrim($item->service_fee, 0), '.') . '% ';
                }
            })
            ->addColumn('network_fee', function ($item) {
                if ($item->network_fee_type == 'flat') {
                    return $item->code . ' ' . rtrim(rtrim($item->network_fee, 0), '.');
                } else {
                    return $item->code . ' ' . rtrim(rtrim($item->network_fee, 0), '.') . '% ';
                }
            })
            ->addColumn('min_max_send', function ($item) {
                return rtrim(rtrim($item->min_send, 0), '.') . ' ' . $item->code . ' - ' . rtrim(rtrim($item->max_send, 0), '.') . ' ' . $item->code;
            })
            ->addColumn('status', function ($item) {
                if ($item->status == 1) {
                    return '<span class="badge bg-soft-success text-success">
                    <span class="legend-indicator bg-success"></span>' . trans('Active') . '
                  </span>';

                } else {
                    return '<span class="badge bg-soft-danger text-danger">
                    <span class="legend-indicator bg-danger"></span>' . trans('In Active') . '
                  </span>';
                }
            })
            ->addColumn('created_at', function ($item) {
                return dateTime($item->created_at, basicControl()->date_time_format);
            })
            ->addColumn('action', function ($item) {
                if ($item->status) {
                    $statusBtn = "In-Active";
                    $statusChange = route('admin.cryptoStatusChange') . '?id=' . $item->id . '&status=in-active';
                } else {
                    $statusBtn = "Active";
                    $statusChange = route('admin.cryptoStatusChange') . '?id=' . $item->id . '&status=active';
                }
                $delete = route('admin.cryptoDelete', $item->id);
                $edit = route('admin.cryptoEdit') . '?id=' . $item->id;

                $html = '<div class="btn-group sortable" role="group" data-code ="' . $item->code . '">
                      <a href="' . $edit . '" class="btn btn-white btn-sm ">
                        <i class="fal fa-edit me-1"></i> ' . trans("Edit") . '
                      </a>';

                $html .= '<div class="btn-group">
                      <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty" id="userEditDropdown" data-bs-toggle="dropdown" aria-expanded="false"></button>
                      <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="userEditDropdown">
                        <a href="' . $statusChange . '" class="dropdown-item edit_user_btn">
                            <i class="fal fa-badge dropdown-item-icon"></i> ' . trans($statusBtn) . '
                        </a>
                        <a class="dropdown-item delete_btn" href="javascript:void(0)" data-bs-target="#delete"
                           data-bs-toggle="modal" data-route="' . $delete . '">
                          <i class="fal fa-trash dropdown-item-icon"></i> ' . trans("Delete") . '
                       </a>
                      </div>
                    </div>';

                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['checkbox', 'name', 'rate', 'service_fee', 'network_fee', 'min_max_send', 'status', 'created_at', 'action'])
            ->make(true);
    }

    public function cryptoCreate(CryptoStoreRequest $request)
    {
        if ($request->method() == 'GET') {
            $activeCryptoMethod = CryptoMethod::select('id', 'status', 'code')->where('status', 1)->firstOrFail();
            return view('admin.currency.crypto.create', compact('activeCryptoMethod'));
        } elseif ($request->method() == 'POST') {
            $currency = new CryptoCurrency();
            $fillData = $request->only($currency->getFillable());
            $fillData['usd_rate'] = getUSDRate($fillData['rate']);
            if ($request->hasFile('image')) {
                try {
                    $image = $this->fileUpload($request->image, config('filelocation.cryptoCurrency.path'), null, config('filelocation.cryptoCurrency.size'), 'webp', 60);
                    if ($image) {
                        $fillData['image'] = $image['path'];
                        $fillData['driver'] = $image['driver'];
                    }
                } catch (\Exception $e) {
                    return back()->withInput()->with('error', 'Image could not be uploaded');
                }
            }
            $currency->fill($fillData)->save();
            return back()->with('success', 'Crypto Currency Created Successfully');
        }
    }

    public function cryptoEdit(CryptoStoreRequest $request)
    {
        $currency = CryptoCurrency::findOrFail($request->id);
        if ($request->method() == 'GET') {
            return view('admin.currency.crypto.edit', compact('currency'));
        } elseif ($request->method() == 'POST') {
            $fillData = $request->only($currency->getFillable());
            $fillData['usd_rate'] = getUSDRate($fillData['rate']);
            if ($request->hasFile('image')) {
                try {
                    $image = $this->fileUpload($request->image, config('filelocation.cryptoCurrency.path'), null, config('filelocation.cryptoCurrency.size'), 'webp', 60);
                    if ($image) {
                        $fillData['image'] = $image['path'];
                        $fillData['driver'] = $image['driver'];
                    }
                } catch (\Exception $e) {
                    return back()->withInput()->with('error', 'Image could not be uploaded');
                }
            }
            $currency->fill($fillData)->save();
            return back()->with('success', 'Crypto Currency Updated Successfully');
        }
    }

    public function cryptoStatusChange(Request $request)
    {
        $currency = CryptoCurrency::findOrFail($request->id);
        if ($request->status == 'active') {
            $currency->status = 1;
        } else {
            $currency->status = 0;
        }
        $currency->save();
        return back()->with('success', 'Status ' . ucfirst($request->status) . ' Successfully');
    }

    public function cryptoDelete($id)
    {
        try {
            $currency = CryptoCurrency::findOrFail($id)->delete();
            $this->fileDelete($currency->driver, $currency->image);
            return back()->with('success', 'Deleted Successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cryptoMultipleDelete(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select row.');
            return response()->json(['error' => 1]);
        } else {
            CryptoCurrency::whereIn('id', $request->strIds)->get()->map(function ($query) {
                $this->fileDelete($query->driver, $query->image);
                $query->delete();
                return $query;
            });
            session()->flash('success', 'Crypto Currency has been deleted successfully');
            return response()->json(['success' => 1]);
        }
    }

    public function cryptoMultipleStatusChange(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select row.');
            return response()->json(['error' => 1]);
        } else {
            CryptoCurrency::whereIn('id', $request->strIds)->get()->map(function ($query) {
                if ($query->status) {
                    $query->status = 0;
                } else {
                    $query->status = 1;
                }
                $query->save();
                return $query;
            });
            session()->flash('success', 'Status Change successfully');
            return response()->json(['success' => 1]);
        }
    }

    public function cryptoMultipleRateUpdate(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select row.');
            return response()->json(['error' => 1]);
        } else {
            $currencies = CryptoCurrency::select(['id', 'code', 'rate', 'usd_rate'])->whereIn('id', $request->strIds)->get();
            $currencyCodes = implode(',', $currencies->pluck('code')->toArray());

            $response = $this->cryptoRateUpdate(basicControl()->base_currency, $currencyCodes);

            if ($response['status']) {
                foreach ($response['res'] as $apiRes) {
                    $apiCode = $apiRes[0]->symbol;
                    $apiRate = $apiRes[0]->quote->{basicControl()->base_currency}->price;
                    $matchingCurrencies = $currencies->where('code', $apiCode);

                    if ($matchingCurrencies->isNotEmpty()) {
                        $matchingCurrencies->each(function ($currency) use ($apiRate) {
                            $currency->update([
                                'rate' => $apiRate,
                                'usd_rate' => getUSDRate($apiRate),
                            ]);
                        });
                    }
                }
            } else {
                session()->flash('error', $response['res']);
            }

            session()->flash('success', 'Rate Updated successfully');
            return response()->json(['success' => 1]);
        }
    }
}
