<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellStoreRequest;
use App\Models\CryptoCurrency;
use App\Models\CryptoMethod;
use App\Models\FiatCurrency;
use App\Models\FiatSendGateway;
use App\Models\SellRequest;
use App\Traits\CalculateFees;
use App\Traits\CryptoWalletGenerate;
use Carbon\Carbon;
use Facades\App\Services\BasicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class SellController extends Controller
{
    use CryptoWalletGenerate, CalculateFees;

    public function __construct()
    {
        $this->theme = template();
    }

    public function getSellCurrency()
    {
        $sendCurrencies = CryptoCurrency::where('status', 1)->orderBy('sort_by', 'ASC')->get();
        $getCurrencies = FiatCurrency::where('status', 1)->orderBy('sort_by', 'ASC')->get();

        return response()->json([
            'sendCurrencies' => $sendCurrencies,
            'getCurrencies' => $getCurrencies,
            'selectedSendCurrency' => $sendCurrencies[0] ?? null,
            'selectedGetCurrency' => $getCurrencies[0] ?? null,
            'initialSendAmount' => isset($sendCurrencies[0]) ? (($sendCurrencies[0]->min_send + $sendCurrencies[0]->max_send) / 2) : 1,
        ]);
    }

    public function sellRequest(SellStoreRequest $request)
    {
        $sendCurrency = CryptoCurrency::where('status', 1)->findOrFail($request->exchangeSendCurrency);
        $getCurrency = FiatCurrency::where('status', 1)->findOrFail($request->exchangeGetCurrency);

        if ($sendCurrency->min_send > $request->exchangeSendAmount) {
            return back()->with('error', 'Min is ' . $sendCurrency->min_send . ' ' . $sendCurrency->code);
        }

        if ($sendCurrency->max_send < $request->exchangeSendAmount) {
            return back()->with('error', 'Max is ' . $sendCurrency->max_send . ' ' . $sendCurrency->code);
        }

        $sendAmount = $request->exchangeSendAmount;
        $exchangeRate = $sendCurrency->usd_rate / $getCurrency->usd_rate;
        $getAmount = $sendAmount * $exchangeRate;
        $processing_fee = $this->getFiatFees($getAmount, $getCurrency)['processingFees'];
        $finalAmount = $getAmount - $processing_fee;

        $sellRequest = SellRequest::create([
            'user_id' => auth()->id() ?? null,
            'send_currency_id' => $sendCurrency->id,
            'get_currency_id' => $getCurrency->id,
            'send_amount' => $sendAmount,
            'get_amount' => $getAmount,
            'exchange_rate' => $exchangeRate,
            'processing_fee' => $processing_fee,
            'final_amount' => $finalAmount,
            'utr' => uniqid('S'),
        ]);

        return redirect()->route('sellProcessing', $sellRequest->utr);
    }

    public function sellProcessing(SellStoreRequest $request, $utr)
    {
        $sellRequest = SellRequest::where(['status' => 0, 'utr' => $utr])->firstOrFail();
        if ($request->method() == 'GET') {
            return view($this->theme . 'user.sell.processing', compact('sellRequest'));
        } elseif ($request->method() == 'POST') {
            $sendCurrency = CryptoCurrency::where('status', 1)->findOrFail($request->exchangeSendCurrency);
            $getCurrency = FiatCurrency::where('status', 1)->findOrFail($request->exchangeGetCurrency);

            if ($sendCurrency->min_send > $request->exchangeSendAmount) {
                return back()->withInput()->with('error', 'Min is ' . $sendCurrency->min_send . ' ' . $sendCurrency->code);
            }

            if ($sendCurrency->max_send < $request->exchangeSendAmount) {
                return back()->withInput()->with('error', 'Max is ' . $sendCurrency->max_send . ' ' . $sendCurrency->code);
            }

            if (!$request->refund_wallet && basicControl()->refund_exchange_status) {
                return back()->withInput()->with('error', 'Refund wallet address is required');
            }

            $fiatSendGateway = FiatSendGateway::where('status', 1)->findOrFail($request->payment_method);
            $params = $fiatSendGateway->parameters;

            $rules = [];
            if ($params !== null) {
                foreach ($params as $key => $cus) {
                    $rules[$key] = [$cus->validation == 'required' ? $cus->validation : 'nullable'];
                    if ($cus->type === 'text') {
                        $rules[$key][] = 'max:255';
                    } elseif ($cus->type === 'number') {
                        $rules[$key][] = 'integer';
                    } elseif ($cus->type === 'textarea') {
                        $rules[$key][] = 'min:3';
                        $rules[$key][] = 'max:300';
                    }
                }
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $reqField = [];
            if ($params != null) {
                foreach ($request->all() as $k => $v) {
                    foreach ($params as $inKey => $inVal) {
                        if ($k == $inKey) {
                            $reqField[$inKey] = [
                                'field_label' => $inVal->field_label,
                                'field_name' => $inVal->field_name,
                                'validation' => $inVal->validation,
                                'field_value' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }

            $sendAmount = $request->exchangeSendAmount;
            $exchangeRate = $sendCurrency->usd_rate / $getCurrency->usd_rate;
            $getAmount = $sendAmount * $exchangeRate;
            $processing_fee = $this->getFiatFees($getAmount, $getCurrency)['processingFees'];
            $finalAmount = $getAmount - $processing_fee;

            $sellRequest->send_currency_id = $sendCurrency->id;
            $sellRequest->get_currency_id = $getCurrency->id;
            $sellRequest->send_amount = $sendAmount;
            $sellRequest->get_amount = $getAmount;
            $sellRequest->exchange_rate = $exchangeRate;
            $sellRequest->processing_fee = $processing_fee;
            $sellRequest->final_amount = $finalAmount;
            $sellRequest->status = 1;
            $sellRequest->fiat_send_gateway_id = $fiatSendGateway->id;
            $sellRequest->refund_wallet = $request->refund_wallet ?? null;
            $sellRequest->parameters = $reqField;
            $sellRequest->save();

            return redirect()->route('sellProcessingOverview', $sellRequest->utr);
        }
    }

    public function sellProcessingOverview($utr)
    {
        $sellRequest = SellRequest::where(['status' => 1, 'utr' => $utr])->firstOrFail();
        return view($this->theme . 'user.sell.processing-overview', compact('sellRequest'));
    }

    public function sellInitPayment(Request $request, $utr)
    {
        $sellRequest = SellRequest::where(['status' => 1, 'utr' => $utr])->firstOrFail();
        if ($request->method() == 'GET') {

            if (auth()->check() && $request->payByWallet == 'on') {
                $balanceUpdate = payByWallet($sellRequest->send_amount, $sellRequest->send_currency_id, 'deduct');

                if ($balanceUpdate['status'] == 'success') {
                    $sellRequest->status = 2;
                    $sellRequest->crypto_method_id = -1;
                    $sellRequest->save();

                    $amount = getBaseAmount($sellRequest->send_amount, optional($sellRequest->sendCurrency)->code, 'crypto');
                    $charge = getBaseAmount($sellRequest->processing_fee, optional($sellRequest->getCurrency)->code, 'fiat');

                    BasicService::makeTransaction($amount, $charge, '-', 'Crypto Deduct For Sell',
                        $sellRequest->id, SellRequest::class, $sellRequest->user_id, $sellRequest->send_amount, optional($sellRequest->sendCurrency)->code);

                    $this->sendAdminNotification($sellRequest, 'sell');
                    return redirect()->route('sellFinal', $sellRequest->utr);
                }

                return back()->with('error', $balanceUpdate['msg'] ?? 'Unprocessable Action');
            }

            if (!$sellRequest->admin_wallet) {
                $response = $this->getCryptoWallet($sellRequest->sendCurrency->code, 'sell');
                if (!$response['status']) {
                    return back()->with('error', 'Unable to generate an address. Please contact the administration for assistance.');
                }
                $sellRequest->admin_wallet = $response['message'];
                $sellRequest->save();
            }

            if (!$sellRequest->expire_time) {
                $sellRequest->expire_time = Carbon::now()->addMinutes(basicControl()->crypto_send_time);
                $sellRequest->save();
            }

            $cryptoMethod = CryptoMethod::select(['id', 'code', 'status'])->where('status', 1)->firstOrFail();

            if (!$sellRequest->crypto_method_id) {
                $sellRequest->crypto_method_id = $cryptoMethod->id;
                $sellRequest->save();
            }

            $data['isButtonShow'] = $cryptoMethod->code == 'manual';
            return view($this->theme . 'user.sell.init-payment', $data, compact('sellRequest'));
        } elseif ($request->method() == 'POST') {
            $sellRequest->status = 2;
            $sellRequest->save();

            $amount = getBaseAmount($sellRequest->send_amount, optional($sellRequest->sendCurrency)->code, 'crypto');
            $charge = getBaseAmount($sellRequest->processing_fee, optional($sellRequest->getCurrency)->code, 'fiat');

            BasicService::makeTransaction($amount, $charge, '-', 'Crypto Deposit For Sell',
                $sellRequest->id, SellRequest::class, $sellRequest->user_id, $sellRequest->send_amount, optional($sellRequest->sendCurrency)->code);

            $this->sendAdminNotification($sellRequest, 'sell');
            return redirect()->route('sellFinal', $sellRequest->utr);
        }
    }

    public function sellFinal($utr)
    {
        $sellRequest = SellRequest::where(['status' => 2, 'utr' => $utr])->firstOrFail();
        return view($this->theme . 'user.sell.final', compact('sellRequest'));
    }

    public function sellAutoRate(Request $request)
    {
        $sendCurrencies = CryptoCurrency::where('status', 1)->orderBy('sort_by', 'ASC')->get();
        $getCurrencies = FiatCurrency::where('status', 1)->orderBy('sort_by', 'ASC')->get();

        return response()->json([
            'sendCurrencies' => $sendCurrencies,
            'getCurrencies' => $getCurrencies,
            'initialSendAmount' => $request->sendAmount,
        ]);
    }

    public function getSellCurrencyMethodInfo(Request $request)
    {
        $getCurrencySendInfo = FiatSendGateway::where('status', 1)
            ->whereJsonContains('supported_currency', $request->getCurrencyCode)
            ->orderBy('name', 'asc')->get();

        return response()->json([
            'getCurrencySendInfo' => $getCurrencySendInfo,
        ]);
    }

    public function sellGetStatus($utr)
    {
        $sellRequest = SellRequest::select(['id', 'utr', 'status', 'expire_time'])->where('utr', $utr)->first();
        $route = route('sellFinal', $sellRequest->utr);
        if ($sellRequest && $sellRequest->status == 1) {
            if (Carbon::now() > $sellRequest->expire_time) {
                $sellRequest->status = 4;
                $sellRequest->save();
                $route = url('/');
            }
        }

        return response()->json([
            'sellRequest' => $sellRequest ?? null,
            'route' => $route
        ]);
    }
}
