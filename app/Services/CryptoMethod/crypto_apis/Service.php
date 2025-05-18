<?php

namespace App\Services\CryptoMethod\crypto_apis;


use App\Library\CryptoAPIs;
use App\Models\CryptoCurrency;
use App\Traits\CryptoWalletGenerate;

class Service
{
    use CryptoWalletGenerate;

    public function prepareData($activeMethod, $cryptoCode, $type = 'exchange')
    {
        $cp = new CryptoAPIs();
        $cp->Setup($activeMethod->parameters->api_key, $activeMethod->parameters->wallet_id, 'testnet');
        $crypto = CryptoCurrency::where('code', $cryptoCode)->first();
        $callbackUrl = route('depositCallback', [$activeMethod->code, $type]);
        if ($crypto) {
            $result = $cp->GetAddress($crypto->name, $callbackUrl);
        }
        return null;
    }

    public function webhookUpdate($request, $object, $cryptoMethod, $type)
    {
        if (isset($request->data['event']) && $request->data['event'] == 'ADDRESS_COINS_TRANSACTION_CONFIRMED') {
            $sendAmount = $request->data['item']['amount'];
            $this->walletUpgration($object, $type, $sendAmount);
        }

        return "200 OK";
    }
}

