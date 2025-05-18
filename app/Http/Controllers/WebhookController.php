<?php

namespace App\Http\Controllers;

use App\Models\CryptoMethod;
use App\Models\CryptoWallet;
use App\Models\ExchangeRequest;
use App\Models\SellRequest;
use App\Traits\CryptoWalletGenerate;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    use CryptoWalletGenerate;

    public function webhookResponse(Request $request, $code, $type = 'exchange')
    {
        $cryptoMethod = CryptoMethod::where('code', $code)->first();
        if ($type == 'exchange') {
            $object = ExchangeRequest::where(['status' => 1, 'admin_wallet' => $request->address])->first();
        } elseif ($type == 'sell') {
            $object = SellRequest::where(['status' => 1, 'admin_wallet' => $request->address])->first();
        } elseif ($type == 'deposit') {
            $object = CryptoWallet::where(['status' => 0, 'wallet_address' => $request->address])->first();
        }

        $methodObj = 'Facades\\App\\Services\\CryptoMethod\\' . $code . '\\Service';
        $data = $methodObj::webhookUpdate($request, $object, $cryptoMethod, $type);

        return response()->json($data, 200);
    }

    public function withdrawWebhookResponse(Request $request, $code, $utr, $type)
    {
        return response()->json('ok', 200);
    }
}
