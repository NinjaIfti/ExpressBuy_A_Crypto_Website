<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExchangeStoreRequest;
use App\Models\CryptoCurrency;
use App\Models\CryptoMethod;
use App\Models\ExchangeRequest;
use App\Traits\CalculateFees;
use App\Traits\CryptoWalletGenerate;
use App\Traits\SendNotification;
use Carbon\Carbon;
use Facades\App\Services\BasicService;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    use CryptoWalletGenerate, CalculateFees, SendNotification;

    public function __construct()
    {
        $this->theme = template();
    }

    public function getExchangeCurrency()
    {
        $queryClone = CryptoCurrency::query()->where('status', 1)->orderBy('sort_by', 'ASC')->get();
        $sendCurrencies = $queryClone;
        $getCurrencies = $queryClone;
        $secondObject = $getCurrencies->splice(1, 1);
        $getCurrencies = $getCurrencies->sortBy('sort_by');
        $getCurrencies = $secondObject->merge($getCurrencies);


        return response()->json([
            'sendCurrencies' => $sendCurrencies,
            'getCurrencies' => $getCurrencies,
            'selectedSendCurrency' => $sendCurrencies[0] ?? null,
            'selectedGetCurrency' => $getCurrencies[0] ?? null,
            'initialSendAmount' => isset($sendCurrencies[0]) ? (($sendCurrencies[0]->min_send + $sendCurrencies[0]->max_send) / 2) : 1,
        ]);
    }

    public function exchangeRequest(ExchangeStoreRequest $request)
    {
        $sendCurrency = CryptoCurrency::where('status', 1)->findOrFail($request->exchangeSendCurrency);
        $getCurrency = CryptoCurrency::where('status', 1)->findOrFail($request->exchangeGetCurrency);

        if ($sendCurrency->min_send > $request->exchangeSendAmount) {
            return back()->with('error', 'Min is ' . $sendCurrency->min_send . ' ' . $sendCurrency->code);
        }

        if ($sendCurrency->max_send < $request->exchangeSendAmount) {
            return back()->with('error', 'Max is ' . $sendCurrency->max_send . ' ' . $sendCurrency->code);
        }

        $sendAmount = $request->exchangeSendAmount;
        $exchangeRate = $sendCurrency->usd_rate / $getCurrency->usd_rate;
        $getAmount = $sendAmount * $exchangeRate;
        $service_fee = $this->getCryptoFees($getAmount, $getCurrency)['serviceFees'];
        $network_fee = $this->getCryptoFees($getAmount, $getCurrency)['networkFees'];
        $finalAmount = $getAmount - ($service_fee + $network_fee);

        $exchangeRequest = ExchangeRequest::create([
            'user_id' => auth()->id() ?? null,
            'send_currency_id' => $sendCurrency->id,
            'get_currency_id' => $getCurrency->id,
            'send_amount' => $sendAmount,
            'get_amount' => $getAmount,
            'exchange_rate' => $exchangeRate,
            'service_fee' => $service_fee,
            'network_fee' => $network_fee,
            'final_amount' => $finalAmount,
            'utr' => uniqid('E'),
        ]);

        return redirect()->route('exchangeProcessing', $exchangeRequest->utr);
    }

    public function exchangeProcessing(ExchangeStoreRequest $request, $utr)
    {
        $exchangeRequest = ExchangeRequest::where(['status' => 0, 'utr' => $utr])->firstOrFail();
        if ($request->method() == 'GET') {
            return view($this->theme . 'user.exchange.processing', compact('exchangeRequest'));
        } elseif ($request->method() == 'POST') {

            $sendCurrency = CryptoCurrency::where('status', 1)->findOrFail($request->exchangeSendCurrency);
            $getCurrency = CryptoCurrency::where('status', 1)->findOrFail($request->exchangeGetCurrency);

            if ($sendCurrency->min_send > $request->exchangeSendAmount) {
                return back()->withInput()->with('error', 'Min is ' . $sendCurrency->min_send . ' ' . $sendCurrency->code);
            }

            if ($sendCurrency->max_send < $request->exchangeSendAmount) {
                return back()->withInput()->with('error', 'Max is ' . $sendCurrency->max_send . ' ' . $sendCurrency->code);
            }

            if (!$request->destination_wallet) {
                return back()->withInput()->with('error', 'Destination wallet address is required');
            }

            if (!$request->refund_wallet && basicControl()->refund_exchange_status) {
                return back()->withInput()->with('error', 'Refund wallet address is required');
            }

            $sendAmount = $request->exchangeSendAmount;
            $exchangeRate = $sendCurrency->usd_rate / $getCurrency->usd_rate;
            $getAmount = $sendAmount * $exchangeRate;
            $service_fee = $this->getCryptoFees($getAmount, $getCurrency)['serviceFees'];
            $network_fee = $this->getCryptoFees($getAmount, $getCurrency)['networkFees'];
            $finalAmount = $getAmount - ($service_fee + $network_fee);

            $exchangeRequest->send_currency_id = $sendCurrency->id;
            $exchangeRequest->get_currency_id = $getCurrency->id;
            $exchangeRequest->send_amount = $sendAmount;
            $exchangeRequest->get_amount = $getAmount;
            $exchangeRequest->exchange_rate = $exchangeRate;
            $exchangeRequest->service_fee = $service_fee;
            $exchangeRequest->network_fee = $network_fee;
            $exchangeRequest->final_amount = $finalAmount;
            $exchangeRequest->status = 1;
            $exchangeRequest->rate_type = $request->rate_type;
            $exchangeRequest->destination_wallet = $request->destination_wallet;
            $exchangeRequest->refund_wallet = $request->refund_wallet ?? null;
            $exchangeRequest->save();

            return redirect()->route('exchangeProcessingOverview', $exchangeRequest->utr);
        }
    }

    public function exchangeProcessingOverview($utr)
    {
        $exchangeRequest = ExchangeRequest::where(['status' => 1, 'utr' => $utr])->firstOrFail();
        return view($this->theme . 'user.exchange.processing-overview', compact('exchangeRequest'));
    }

    public function exchangeInitPayment(Request $request, $utr)
    {
        $exchangeRequest = ExchangeRequest::where(['status' => 1, 'utr' => $utr])->firstOrFail();
        if ($request->method() == 'GET') {

            if (auth()->check() && $request->payByWallet == 'on') {
                $balanceUpdate = payByWallet($exchangeRequest->send_amount, $exchangeRequest->send_currency_id, 'deduct');

                if ($balanceUpdate['status'] == 'success') {
                    $exchangeRequest->status = 2;
                    $exchangeRequest->crypto_method_id = -1;
                    $exchangeRequest->save();

                    $amount = getBaseAmount($exchangeRequest->send_amount, optional($exchangeRequest->sendCurrency)->code, 'crypto');
                    $charge = getBaseAmount($exchangeRequest->service_fee + $exchangeRequest->network_fee, optional($exchangeRequest->getCurrency)->code, 'crypto');

                    BasicService::makeTransaction($amount, $charge, '-', 'Crypto Deduct For Exchange',
                        $exchangeRequest->id, ExchangeRequest::class, $exchangeRequest->user_id, $exchangeRequest->send_amount, optional($exchangeRequest->sendCurrency)->code);

                    $this->sendAdminNotification($exchangeRequest, 'exchange');

                    return redirect()->route('exchangeFinal', $exchangeRequest->utr);
                }

                return back()->with('error', $balanceUpdate['msg'] ?? 'Unprocessable Action');
            }

            if (!$exchangeRequest->admin_wallet) {
                $response = $this->getCryptoWallet($exchangeRequest->sendCurrency->code, 'exchange');
                if (!$response['status']) {
                    return back()->with('error', 'Unable to generate an address. Please contact the administration for assistance.');
                }
                $exchangeRequest->admin_wallet = $response['message'];
                $exchangeRequest->save();
            }

            if (!$exchangeRequest->expire_time) {
                $exchangeRequest->expire_time = Carbon::now()->addMinutes(basicControl()->crypto_send_time);
                $exchangeRequest->save();
            }

            $cryptoMethod = CryptoMethod::select(['id', 'code', 'status'])->where('status', 1)->firstOrFail();

            if (!$exchangeRequest->crypto_method_id) {
                $exchangeRequest->crypto_method_id = $cryptoMethod->id;
                $exchangeRequest->save();
            }

            $data['isButtonShow'] = $cryptoMethod->code == 'manual';
            return view($this->theme . 'user.exchange.init-payment', $data, compact('exchangeRequest'));
        } elseif ($request->method() == 'POST') {
            $exchangeRequest->status = 2;
            $exchangeRequest->save();

            $amount = getBaseAmount($exchangeRequest->send_amount, optional($exchangeRequest->sendCurrency)->code, 'crypto');
            $charge = getBaseAmount($exchangeRequest->service_fee + $exchangeRequest->network_fee, optional($exchangeRequest->getCurrency)->code, 'crypto');

            BasicService::makeTransaction($amount, $charge, '-', 'Manual Crypto Deposit For Exchange',
                $exchangeRequest->id, ExchangeRequest::class, $exchangeRequest->user_id, $exchangeRequest->send_amount, optional($exchangeRequest->sendCurrency)->code);

            $this->sendAdminNotification($exchangeRequest, 'exchange');
            return redirect()->route('exchangeFinal', $exchangeRequest->utr);
        }
    }

    public function exchangeFinal($utr)
    {
        $exchangeRequest = ExchangeRequest::where(['status' => 2, 'utr' => $utr])->firstOrFail();
        return view($this->theme . 'user.exchange.final', compact('exchangeRequest'));
    }

    public function exchangeAutoRate(Request $request)
    {
        $sendCurrencies = CryptoCurrency::where('status', 1)->orderBy('sort_by', 'ASC')->get();
        $getCurrencies = CryptoCurrency::where('status', 1)->orderBy('sort_by', 'ASC')->get();

        return response()->json([
            'sendCurrencies' => $sendCurrencies,
            'getCurrencies' => $getCurrencies,
            'initialSendAmount' => $request->sendAmount,
        ]);
    }

    public function exchangeGetStatus($utr)
    {
        $exchangeRequest = ExchangeRequest::select(['id', 'utr', 'status', 'expire_time'])->where('utr', $utr)->first();
        $route = route('exchangeFinal', $exchangeRequest->utr);
        if ($exchangeRequest && $exchangeRequest->status == 1) {
            if (Carbon::now() > $exchangeRequest->expire_time) {
                $exchangeRequest->status = 4;
                $exchangeRequest->save();
                $route = url('/');
            }
        }
        return response()->json([
            'exchangeRequest' => $exchangeRequest ?? null,
            'route' => $route
        ]);
    }
}
