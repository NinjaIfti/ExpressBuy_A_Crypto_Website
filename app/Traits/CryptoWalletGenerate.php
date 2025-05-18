<?php

namespace App\Traits;

use App\Jobs\SendReferralBonusJob;
use App\Models\CryptoMethod;
use App\Models\CryptoWallet;
use App\Models\ExchangeRequest;
use App\Models\SellRequest;
use App\Models\Wallet;
use Facades\App\Services\BasicService;

trait CryptoWalletGenerate
{
    use SendNotification;

    public function getCryptoWallet($cryptoCode, $type = 'exchange')
    {
        $activeMethod = CryptoMethod::where('status', 1)->first();
        if (!$activeMethod) {
            return $this->errorMsg('Active crypto method not found');
        }

        $methodObj = 'Facades\\App\\Services\\CryptoMethod\\' . $activeMethod->code . '\\Service';
        $data = $methodObj::prepareData($activeMethod, $cryptoCode, $type);
        if ($data) {
            return $this->successMsg($data);
        }

        return $this->errorMsg('something went wrong');
    }

    public function walletUpgration($object, $type, $sendAmount): void
    {
        if ($type == 'exchange') {
            $object->status = 2;
            $object->save();
            $amount = getBaseAmount($object->send_amount, optional($object->sendCurrency)->code, 'crypto');
            $charge = getBaseAmount($object->service_fee + $object->network_fee, optional($object->getCurrency)->code, 'crypto');

            BasicService::makeTransaction($amount, $charge, '-', 'Crypto Deposit For Exchange',
                $object->id, ExchangeRequest::class, $object->user_id, $object->send_amount, optional($object->sendCurrency)->code);

            dispatch(new SendReferralBonusJob($object->user_id, 'exchange_commission', $amount));

            $this->sendAdminNotification($object, 'exchange');
        } elseif ($type == 'sell') {
            $object->status = 2;
            $object->save();

            $amount = getBaseAmount($object->send_amount, optional($object->sendCurrency)->code, 'crypto');
            $charge = getBaseAmount($object->processing_fee, optional($object->getCurrency)->code, 'fiat');

            BasicService::makeTransaction($amount, $charge, '-', 'Crypto Deposit For Sell',
                $object->id, SellRequest::class, $object->user_id, $object->send_amount, optional($object->sendCurrency)->code);

            $this->sendAdminNotification($object, 'sell');
        } elseif ($type == 'deposit') {
            $wallet = Wallet::firstOrCreate(
                [
                    'user_id' => $object->user_id,
                    'crypto_currencies_id' => $object->crypto_currency_id
                ],
                [
                    'balance' => 0
                ]
            );
            $wallet->balance += $sendAmount;
            $wallet->save();

            $amount = getBaseAmount($sendAmount, $object->currency_code, 'crypto');
            BasicService::makeTransaction($amount, 0, '-', 'Crypto Deposit To Account',
                $object->id, CryptoWallet::class, $object->user_id, $sendAmount, $object->currency_code);

            dispatch(new SendReferralBonusJob($object->user_id, 'deposit_commission', $amount));
        }
    }


    public function errorMsg($msg)
    {
        return [
            'status' => false,
            'message' => $msg,
        ];
    }

    public function successMsg($msg)
    {
        return [
            'status' => true,
            'message' => $msg,
        ];
    }
}
