<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BuyRequest;
use App\Models\CryptoCurrency;
use App\Models\CryptoMethod;
use App\Models\CryptoWallet;
use App\Models\Deposit;
use App\Models\ExchangeRequest;
use App\Models\Gateway;
use App\Models\Kyc;
use App\Models\ReferralBonus;
use App\Models\SellRequest;
use App\Models\Transaction;
use App\Models\UserKyc;
use App\Traits\CryptoWalletGenerate;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class HomeController extends Controller
{
    use Upload, CryptoWalletGenerate;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
        $this->theme = template();
    }

    public function saveToken(Request $request)
    {
        Auth::user()
            ->fireBaseToken()
            ->create([
                'token' => $request->token,
            ]);
        return response()->json([
            'msg' => 'token saved successfully.',
        ]);
    }

    public function index()
    {
        $data['user'] = Auth::user();
        $data['baseColor'] = basicControl()->primary_color;
        $data['firebaseNotify'] = config('firebase');
        return view($this->theme . 'user.dashboard', $data);
    }

    public function getRecords()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $exchangeRequestQuery = $this->exchangeRequestQuery()->where('user_id', auth()->id());


        $exchangeRecord = collect((clone $exchangeRequestQuery)
            ->whereIn('status', ['2', '3', '5', '6'])
            ->selectRaw('COUNT(id) AS totalExchange')
            ->selectRaw('(COUNT(CASE WHEN status = 2 THEN id END)) AS pendingExchange')
            ->selectRaw('(COUNT(CASE WHEN status = 2 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysPendingPercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->selectRaw('(COUNT(CASE WHEN status = 3 THEN id END)) AS completeExchange')
            ->selectRaw('(COUNT(CASE WHEN status = 3 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysCompletePercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->selectRaw('(COUNT(CASE WHEN status = 5 THEN id END)) AS cancelExchange')
            ->selectRaw('(COUNT(CASE WHEN status = 5 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysCancelPercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->selectRaw('(COUNT(CASE WHEN status = 6 THEN id END)) AS refundExchange')
            ->selectRaw('(COUNT(CASE WHEN status = 6 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysRefundPercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->get()
            ->makeHidden(['tracking_status', 'admin_status', 'user_status'])
            ->toArray())->collapse();

        $buyRequestQuery = $this->buyRequestQuery();

        $buyRecord = collect((clone $buyRequestQuery)->where('user_id', auth()->id())->whereIn('status', [2, 3, 5, 6])->selectRaw('COUNT(id) AS totalBuy')
            ->selectRaw('(COUNT(CASE WHEN status = 2 THEN id END)) AS pendingBuy')
            ->selectRaw('(COUNT(CASE WHEN status = 2 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysPendingPercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->selectRaw('(COUNT(CASE WHEN status = 3 THEN id END)) AS completeBuy')
            ->selectRaw('(COUNT(CASE WHEN status = 3 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysCompletePercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->selectRaw('(COUNT(CASE WHEN status = 5 THEN id END)) AS cancelBuy')
            ->selectRaw('(COUNT(CASE WHEN status = 5 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysCancelPercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->selectRaw('(COUNT(CASE WHEN status = 6 THEN id END)) AS refundBuy')
            ->selectRaw('(COUNT(CASE WHEN status = 6 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysRefundPercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->get()
            ->makeHidden(['tracking_status', 'admin_status', 'user_status'])
            ->toArray())->collapse();

        $sellRequestQuery = $this->sellRequestQuery();

        $sellRecord = collect((clone $sellRequestQuery)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['2', '3', '5', '6'])
            ->selectRaw('COUNT(id) AS totalSell')
            ->selectRaw('(COUNT(CASE WHEN status = 2 THEN id END)) AS pendingSell')
            ->selectRaw('(COUNT(CASE WHEN status = 2 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysPendingPercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->selectRaw('(COUNT(CASE WHEN status = 3 THEN id END)) AS completeSell')
            ->selectRaw('(COUNT(CASE WHEN status = 3 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysCompletePercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->selectRaw('(COUNT(CASE WHEN status = 5 THEN id END)) AS cancelSell')
            ->selectRaw('(COUNT(CASE WHEN status = 5 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysCancelPercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->selectRaw('(COUNT(CASE WHEN status = 6 THEN id END)) AS refundSell')
            ->selectRaw('(COUNT(CASE WHEN status = 6 AND created_at >= ? THEN id END) / COUNT(CASE WHEN created_at >= ? THEN id END)) * 100 AS last30DaysRefundPercentage', [$thirtyDaysAgo, $thirtyDaysAgo])
            ->get()
            ->makeHidden(['tracking_status', 'admin_status', 'user_status'])
            ->toArray())->collapse();

        return response()->json([
            'totalExchange' => fractionNumber($exchangeRecord['totalExchange'], false),
            'pendingExchange' => fractionNumber($exchangeRecord['pendingExchange'], false),
            'last30DaysPendingPercentage' => fractionNumber($exchangeRecord['last30DaysPendingPercentage']),
            'completeExchange' => fractionNumber($exchangeRecord['completeExchange'], false),
            'last30DaysCompletePercentage' => fractionNumber($exchangeRecord['last30DaysCompletePercentage']),
            'cancelExchange' => fractionNumber($exchangeRecord['cancelExchange'], false),
            'last30DaysCancelPercentage' => fractionNumber($exchangeRecord['last30DaysCancelPercentage']),
            'refundExchange' => fractionNumber($exchangeRecord['refundExchange'], false),
            'last30DaysRefundPercentage' => fractionNumber($exchangeRecord['last30DaysRefundPercentage']),

            'totalBuy' => fractionNumber($buyRecord['totalBuy'], false, false),
            'pendingBuy' => fractionNumber($buyRecord['pendingBuy'], false),
            'last30DaysPendingPercentageBuy' => fractionNumber($buyRecord['last30DaysPendingPercentage']),
            'completeBuy' => fractionNumber($buyRecord['completeBuy'], false),
            'last30DaysCompletePercentageBuy' => fractionNumber($buyRecord['last30DaysCompletePercentage']),
            'cancelBuy' => fractionNumber($buyRecord['cancelBuy'], false),
            'last30DaysCancelPercentageBuy' => fractionNumber($buyRecord['last30DaysCancelPercentage']),
            'refundBuy' => fractionNumber($buyRecord['refundBuy'], false),
            'last30DaysRefundPercentageBuy' => fractionNumber($buyRecord['last30DaysRefundPercentage']),

            'totalSell' => fractionNumber($sellRecord['totalSell'], false),
            'pendingSell' => fractionNumber($sellRecord['pendingSell'], false),
            'last30DaysPendingPercentageSell' => fractionNumber($sellRecord['last30DaysPendingPercentage']),
            'completeSell' => fractionNumber($sellRecord['completeSell'], false),
            'last30DaysCompletePercentageSell' => fractionNumber($sellRecord['last30DaysCompletePercentage']),
            'cancelSell' => fractionNumber($sellRecord['cancelSell'], false),
            'last30DaysCancelPercentageSell' => fractionNumber($sellRecord['last30DaysCancelPercentage']),
            'refundSell' => fractionNumber($sellRecord['refundSell'], false),
            'last30DaysRefundPercentageSell' => fractionNumber($sellRecord['last30DaysRefundPercentage']),
        ]);
    }

    public function chartExchangeFigures()
    {
        $exchangeRequestQuery = $this->exchangeRequestQuery()->where('user_id', auth()->id());

        $exchangeRecord = collect((clone $exchangeRequestQuery)
            ->whereIn('status', [2, 3, 5, 6])
            ->selectRaw('COUNT(id) AS totalExchange')
            ->selectRaw('(COUNT(CASE WHEN status = 2 THEN id END)) AS pendingExchange')
            ->selectRaw('(COUNT(CASE WHEN status = 3 THEN id END)) AS completeExchange')
            ->selectRaw('(COUNT(CASE WHEN status = 5 THEN id END)) AS cancelExchange')
            ->selectRaw('(COUNT(CASE WHEN status = 6 THEN id END)) AS refundExchange')
            ->get()
            ->toArray())->collapse();

        $data['horizontalBarChatExchange'] = [$exchangeRecord['totalExchange'], $exchangeRecord['pendingExchange'], $exchangeRecord['completeExchange'],
            $exchangeRecord['cancelExchange'], $exchangeRecord['refundExchange']];

        return response()->json(['exchangeFigures' => $data]);
    }

    public function chartBuyFigures()
    {
        $buyRequestQuery = $this->buyRequestQuery();

        $buyRecord = collect((clone $buyRequestQuery)->where('user_id', auth()->id())
            ->whereIn('status', ['2', '3', '5', '6'])
            ->selectRaw('COUNT(id) AS totalBuy')
            ->selectRaw('(COUNT(CASE WHEN status = 2 THEN id END)) AS pendingBuy')
            ->selectRaw('(COUNT(CASE WHEN status = 3 THEN id END)) AS completeBuy')
            ->selectRaw('(COUNT(CASE WHEN status = 5 THEN id END)) AS cancelBuy')
            ->selectRaw('(COUNT(CASE WHEN status = 6 THEN id END)) AS refundBuy')
            ->get()
            ->toArray())->collapse();

        $data['horizontalBarChatBuy'] = [$buyRecord['totalBuy'], $buyRecord['pendingBuy'], $buyRecord['completeBuy'],
            $buyRecord['cancelBuy'], $buyRecord['refundBuy']];

        return response()->json(['buyFigures' => $data]);
    }

    public function chartSellFigures()
    {
        $sellRequestQuery = $this->sellRequestQuery();
        $sellRecord = collect((clone $sellRequestQuery)->where('user_id', auth()->id())
            ->whereIn('status', ['2', '3', '5', '6'])
            ->selectRaw('COUNT(id) AS totalSell')
            ->selectRaw('(COUNT(CASE WHEN status = 2 THEN id END)) AS pendingSell')
            ->selectRaw('(COUNT(CASE WHEN status = 3 THEN id END)) AS completeSell')
            ->selectRaw('(COUNT(CASE WHEN status = 5 THEN id END)) AS cancelSell')
            ->selectRaw('(COUNT(CASE WHEN status = 6 THEN id END)) AS refundSell')
            ->get()
            ->toArray())->collapse();

        $data['horizontalBarChatSell'] = [$sellRecord['totalSell'], $sellRecord['pendingSell'], $sellRecord['completeSell'],
            $sellRecord['cancelSell'], $sellRecord['refundSell']];

        return response()->json(['sellFigures' => $data]);
    }

    public function chartExchangeMovements()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        $exchangeRequestQuery = $this->exchangeRequestQuery();

        $exchangeRequests = (clone $exchangeRequestQuery)->where('user_id', auth()->id())->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->whereIn('status', [2, 3, 5, 6])
            ->whereMonth('created_at', '<=', $currentMonth)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $exchangeMovements = [];

        foreach ($exchangeRequests as $exchangeRequest) {
            $month = date("M", mktime(0, 0, 0, $exchangeRequest->month, 1)); // Convert month number to month name
            $exchangeMovements[$month] = $exchangeRequest->total; // Store month-wise total exchanges
        }
        return response()->json(['exchangeMovements' => $exchangeMovements]);
    }

    public function exchangeRequestQuery()
    {
        return ExchangeRequest::query();
    }

    public function buyRequestQuery()
    {
        return BuyRequest::query();
    }

    public function sellRequestQuery()
    {
        return SellRequest::query();
    }

    public function chartBuyMovements()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        $buyRequestQuery = $this->buyRequestQuery();
        $buyRequests = (clone $buyRequestQuery)->where('user_id', auth()->id())->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->whereIn('status', ['2', '3', '5', '6'])
            ->whereMonth('created_at', '<=', $currentMonth)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $buyMovements = [];

        foreach ($buyRequests as $buyRequest) {
            $month = date("M", mktime(0, 0, 0, $buyRequest->month, 1)); // Convert month number to month name
            $buyMovements[$month] = $buyRequest->total; // Store month-wise total exchanges
        }
        return response()->json(['buyMovements' => $buyMovements]);
    }

    public function chartSellMovements()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        $sellRequestBaseQuery = $this->sellRequestQuery();
        $sellRequests = (clone $sellRequestBaseQuery)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereIn('status', ['2', '3', '5', '6'])
            ->where('user_id', auth()->id())
            ->whereMonth('created_at', '<=', $currentMonth)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $sellMovements = [];

        foreach ($sellRequests as $sellRequest) {
            $month = date("M", mktime(0, 0, 0, $sellRequest->month, 1)); // Convert month number to month name
            $sellMovements[$month] = $sellRequest->total; // Store month-wise total exchanges
        }
        return response()->json(['sellMovements' => $sellMovements]);
    }

    function getDataForTimeRange($start, $end, $type)
    {
        $hours = [];
        $counts = [];

        for ($i = 0; $i < 24; $i++) {
            if ($i % 2 == 0) {
                $hour = $start->copy()->subHours($i + 1);
                $formattedHour = $hour->format('hA');
                $hours[] = $formattedHour;

                if ($type == 'exchange') {
                    $count = DB::table('exchange_requests')
                        ->where('user_id', auth()->id())
                        ->whereIn('status', ['2', '3', '5', '6'])
                        ->where('updated_at', '>=', $hour)
                        ->where('updated_at', '<', $hour->copy()->addHours(2))
                        ->count();
                } elseif ($type == 'buy') {
                    $count = DB::table('buy_requests')
                        ->where('user_id', auth()->id())
                        ->whereIn('status', ['2', '3', '5', '6'])
                        ->where('updated_at', '>=', $hour)
                        ->where('updated_at', '<', $hour->copy()->addHours(2))
                        ->count();
                } elseif ($type == 'sell') {
                    $count = DB::table('sell_requests')
                        ->where('user_id', auth()->id())
                        ->whereIn('status', ['2', '3', '5', '6'])
                        ->where('updated_at', '>=', $hour)
                        ->where('updated_at', '<', $hour->copy()->addHours(2))
                        ->count();
                }
                $counts[] = $count;
            }
        }
        $hours = array_reverse($hours);
        $counts = array_reverse($counts);

        $data[] = [
            'hours' => $hours,
            'counts' => $counts,
        ];
        return $data;
    }

    public function kycShow($slug, $id)
    {
        $data['kycs'] = Kyc::where('status', 1)->get();
        $data['kyc'] = Kyc::where('status', 1)->findOrFail($id);
        return view($this->theme . 'user.kyc.show', $data);
    }

    public function kycVerificationSubmit(Request $request, $id)
    {
        $kyc = Kyc::where('status', 1)->findOrFail($id);
        try {
            $params = $kyc->input_form;
            $reqData = $request->except('_token', '_method');
            $rules = [];
            if ($params !== null) {
                foreach ($params as $key => $cus) {
                    $rules[$key] = [$cus->validation == 'required' ? $cus->validation : 'nullable'];
                    if ($cus->type == 'file') {
                        $rules[$key][] = 'image';
                        $rules[$key][] = 'mimes:jpeg,jpg,png';
                        $rules[$key][] = 'max:2048';
                    } elseif ($cus->type == 'text') {
                        $rules[$key][] = 'max:191';
                    } elseif ($cus->type == 'number') {
                        $rules[$key][] = 'integer';
                    } elseif ($cus->type == 'textarea') {
                        $rules[$key][] = 'min:3';
                        $rules[$key][] = 'max:300';
                    }
                }
            }

            $validator = Validator::make($reqData, $rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $reqField = [];
            foreach ($request->except('_token', '_method', 'type') as $k => $v) {
                foreach ($params as $inKey => $inVal) {
                    if ($k == $inKey) {
                        if ($inVal->type == 'file' && $request->hasFile($inKey)) {
                            try {
                                $file = $this->fileUpload($request[$inKey], config('filelocation.kyc.path'), null, null, 'webp', 60);
                                $reqField[$inKey] = [
                                    'field_name' => $inVal->field_name,
                                    'field_value' => $file['path'],
                                    'field_driver' => $file['driver'],
                                    'validation' => $inVal->validation,
                                    'type' => $inVal->type,
                                ];
                            } catch (\Exception $exp) {
                                session()->flash('error', 'Could not upload your ' . $inKey);
                                return back()->withInput();
                            }
                        } else {
                            $reqField[$inKey] = [
                                'field_name' => $inVal->field_name,
                                'validation' => $inVal->validation,
                                'field_value' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }

            UserKyc::create([
                'user_id' => auth()->id(),
                'kyc_id' => $kyc->id,
                'kyc_type' => $kyc->name,
                'kyc_info' => $reqField
            ]);

            return back()->with('success', 'KYC Sent Successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function verificationCenter()
    {
        $data['userKycs'] = UserKyc::own()->latest()->get();
        return view($this->theme . 'user.kyc.verification-center', $data);
    }


    public function addFund()
    {
        $data['basic'] = basicControl();
        $data['gateways'] = Gateway::where('status', 1)->orderBy('sort_by', 'ASC')->get();
        return view($this->theme . 'user.fund.add_fund', $data);
    }


    public function fund(Request $request)
    {
        $basic = basicControl();
        $userId = Auth::id();
        $funds = Deposit::with(['depositable', 'gateway'])
            ->where('user_id', $userId)
            ->where('payment_method_id', '>', 999)
            ->orderBy('id', 'desc')
            ->latest()->paginate($basic->paginate);
        return view($this->theme . 'user.fund.index', compact('funds'));

    }


    public function transaction(Request $request)
    {
        $search = $request->all();
        $dateSearch = $request->datetrx;
        $date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);
        $userId = Auth::id();
        $transactions = Transaction::where('user_id', $userId)
            ->when(@$search['transaction_id'], function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', "%{$search['transaction_id']}%");
            })
            ->when(@$search['remark'], function ($query) use ($search) {
                return $query->where('remarks', 'LIKE', "%{$search['remark']}%");
            })
            ->when($date == 1, function ($query) use ($dateSearch) {
                return $query->whereDate("created_at", $dateSearch);
            })
            ->orderBy('id', 'desc')
            ->paginate(basicControl()->paginate);
        return view($this->theme . 'user.transaction.index', compact('transactions'));
    }

    public function cryptoDeposit($currencyCode = null)
    {
        $data['crypto'] = CryptoCurrency::select('id', 'code', 'name')
            ->where('code', $currencyCode)->firstOrFail();

        $data['addresses'] = CryptoWallet::where('user_id', auth()->id())->where('status', '!=', 0)
            ->orderBy('id', 'desc')->get();
        $data['cryptoMethod'] = CryptoMethod::where('status', 1)->firstOrFail();

        if ($data['cryptoMethod']->code == 'manual' && !$data['cryptoMethod']->field_name) {
            $data['cryptoMethod']->field_name = 'Transaction ID';
            $data['cryptoMethod']->save();
        }
        return view($this->theme . 'user.deposit.generate', $data);
    }

    public function addressGenerate(Request $request)
    {
        $crypto = CryptoCurrency::select('id', 'code', 'name')
            ->where('code', $request->code)->first();
        if ($crypto) {
            $address = $this->getCryptoWallet($crypto->code, 'deposit');
            if ($address['status']) {
                $cryptoWallet = CryptoWallet::create([
                    'user_id' => auth()->id(),
                    'crypto_currency_id' => $crypto->id,
                    'wallet_address' => $address['message'],
                    'currency_code' => $crypto->code,
                    'type' => 'manual',
                    'utr' => uniqid('D'),
                ]);

                return response()->json([
                    'status' => 'success',
                    'address' => $cryptoWallet->wallet_address,
                    'crypto_wallet_id' => $cryptoWallet->id
                ]);
            }
        }

        return response()->json(['status' => 'error']);
    }

    public function depositConfirm(Request $request)
    {
        $this->validate($request, [
            'proof' => 'required',
            'crypto_wallet_id' => 'required',
        ]);

        $cryptoWallet = CryptoWallet::where('status', 0)->findOrFail($request->crypto_wallet_id);
        $cryptoWallet->proof = $request->proof;
        $cryptoWallet->status = 2;
        $cryptoWallet->save();

        return back()->with('success', 'Deposit request send to the admin');
    }

    public function referral()
    {
        $userId = Auth::id();
        $data['title'] = "My Referrals";
        $data['directReferralUsers'] = getDirectReferralUsers($userId);
        return view(template() . 'user.referral.referral', $data);
    }

    public function referralBonus(Request $request)
    {

        $remark = $request->remark;
        $filterDate = explode('to', $request->date_range);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;
        $commission_type = $request->type;


        $referrals = ReferralBonus::query()->with(['user:id,username,firstname,lastname,image,image_driver'])
            ->where('from_user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->when(!empty($request->date_range) && $endDate == null, function ($query) use ($startDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $query->whereDate('created_at', $startDate);
            })
            ->when(!empty($request->date_range) && $endDate != null, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $endDate = Carbon::parse(trim($endDate))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when(!empty($remark), function ($query) use ($remark) {
                return $query->where('remarks', $remark);
            })
            ->when(!empty($commission_type), function ($query) use ($commission_type) {
                return $query->where('commission_type', $commission_type);
            })
            ->paginate(15);

        return view(template() . 'user.referral.referral_bonus', compact('referrals'));
    }

    public function getReferralsBonus()
    {
        $referrals = ReferralBonus::where('from_user_id', Auth::id())
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total')
            ->groupBy('month', 'year')
            ->get();
        return response()->json([
            'referrals' => $this->formatChartData($referrals),
        ]);
    }

    public function getReferralUser(Request $request)
    {
        $data = getDirectReferralUsers($request->userId);
        $directReferralUsers = $data->map(function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'count_direct_referral' => count(getDirectReferralUsers($user->id)),
                'joined_at' => dateTime($user->created_at),
            ];
        });

        return response()->json(['data' => $directReferralUsers]);
    }

    private function formatChartData($data)
    {
        $formattedData = array_fill(0, 12, 0);
        foreach ($data as $item) {
            $formattedData[$item->month - 1] = getAmount($item->total);
        }
        return $formattedData;
    }
}
