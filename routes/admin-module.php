<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Module\CryptoCurrencyController;
use App\Http\Controllers\Admin\Module\FiatCurrencyController;
use App\Http\Controllers\Admin\Module\CoinAnnounceController;
use App\Http\Controllers\Admin\Module\CryptoMethodController;
use App\Http\Controllers\Admin\Module\ExchangeController;
use App\Http\Controllers\Admin\Module\BuyController;
use App\Http\Controllers\Admin\Module\SellController;
use App\Http\Controllers\Admin\Module\FiatSendGatewayController;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::middleware(['auth:admin', 'demo'])->group(function () {

        Route::controller(CryptoCurrencyController::class)->group(function () {
            Route::get('crypto/list', 'cryptoList')->name('cryptoList');
            Route::get('crypto/list/search', 'cryptoListSearch')->name('cryptoListSearch');
            Route::any('crypto/create', 'cryptoCreate')->name('cryptoCreate');
            Route::any('crypto/edit', 'cryptoEdit')->name('cryptoEdit');
            Route::get('crypto/status-change', 'cryptoStatusChange')->name('cryptoStatusChange');
            Route::delete('crypto/delete/{id}', 'cryptoDelete')->name('cryptoDelete');
            Route::post('crypto/multiple-delete', 'cryptoMultipleDelete')->name('cryptoMultipleDelete');
            Route::post('crypto/multiple-status-change', 'cryptoMultipleStatusChange')->name('cryptoMultipleStatusChange');
            Route::post('crypto/multiple-rate-update', 'cryptoMultipleRateUpdate')->name('cryptoMultipleRateUpdate');
            Route::post('crypto/sort', 'cryptoSort')->name('cryptoSort');
        });

        Route::controller(FiatCurrencyController::class)->group(function () {
            Route::get('fiat/list', 'fiatList')->name('fiatList');
            Route::get('fiat/list/search', 'fiatListSearch')->name('fiatListSearch');
            Route::any('fiat/create', 'fiatCreate')->name('fiatCreate');
            Route::any('fiat/edit', 'fiatEdit')->name('fiatEdit');
            Route::get('fiat/status-change', 'fiatStatusChange')->name('fiatStatusChange');
            Route::delete('fiat/delete/{id}', 'fiatDelete')->name('fiatDelete');
            Route::post('fiat/multiple-delete', 'fiatMultipleDelete')->name('fiatMultipleDelete');
            Route::post('fiat/multiple-status-change', 'fiatMultipleStatusChange')->name('fiatMultipleStatusChange');
            Route::post('fiat/multiple-rate-update', 'fiatMultipleRateUpdate')->name('fiatMultipleRateUpdate');
            Route::post('fiat/sort', 'fiatSort')->name('fiatSort');
        });

        Route::controller(CoinAnnounceController::class)->group(function () {
            Route::get('coin-announce/list', 'coinAnnounceList')->name('coinAnnounceList');
            Route::get('coin-announce/list/search', 'coinAnnounceSearch')->name('coinAnnounceSearch');
            Route::any('coin-announce/create', 'coinAnnounceCreate')->name('coinAnnounceCreate');
            Route::any('coin-announce/edit', 'coinAnnounceEdit')->name('coinAnnounceEdit');
            Route::get('coin-announce/status-change', 'coinAnnounceStatusChange')->name('coinAnnounceStatusChange');
            Route::delete('coin-announce/delete/{id}', 'coinAnnounceDelete')->name('coinAnnounceDelete');
            Route::post('coin-announce/multiple-delete', 'coinAnnounceMultipleDelete')->name('coinAnnounceMultipleDelete');
            Route::post('coin-announce/multiple-status-change', 'coinAnnounceMultipleStatusChange')->name('coinAnnounceMultipleStatusChange');
        });

        Route::controller(CryptoMethodController::class)->group(function () {
            Route::get('crypto-method/list', 'cryptoMethodList')->name('cryptoMethodList');
            Route::get('crypto-method/list/search', 'cryptoMethodSearch')->name('cryptoMethodSearch');
            Route::any('crypto-method/edit', 'cryptoMethodEdit')->name('cryptoMethodEdit');
            Route::any('crypto-method/manual/set-address', 'cryptoMethodSetAddress')->name('cryptoMethodSetAddress');
            Route::post('crypto-method/manual/field-change', 'cryptoMethodFieldChange')->name('cryptoMethodFieldChange');
            Route::get('crypto-method/status-change', 'cryptoMethodStatusChange')->name('cryptoMethodStatusChange');
        });

        Route::controller(ExchangeController::class)->group(function () {
            Route::get('exchange/list', 'exchangeList')->name('exchangeList');
            Route::get('exchange/list/search', 'exchangeListSearch')->name('exchangeListSearch');
            Route::get('exchange/view', 'exchangeView')->name('exchangeView');
            Route::get('exchange/rate-floating/{utr}', 'exchangeRateFloating')->name('exchangeRateFloating');
            Route::delete('exchange/delete/{id}', 'exchangeDelete')->name('exchangeDelete');
            Route::post('exchange/multiple-delete', 'exchangeMultipleDelete')->name('exchangeMultipleDelete');

            Route::post('exchange/send-confirm/{utr}', 'exchangeSend')->name('exchangeSend');
            Route::post('exchange/cancel-confirm/{utr}', 'exchangeCancel')->name('exchangeCancel');
            Route::post('exchange/refund-confirm/{utr}', 'exchangeRefund')->name('exchangeRefund');
        });

        Route::controller(BuyController::class)->group(function () {
            Route::get('buy/list', 'buyList')->name('buyList');
            Route::get('buy/list/search', 'buyListSearch')->name('buyListSearch');
            Route::get('buy/view', 'buyView')->name('buyView');
            Route::delete('buy/delete/{id}', 'buyDelete')->name('buyDelete');
            Route::post('buy/multiple-delete', 'buyMultipleDelete')->name('buyMultipleDelete');

            Route::post('buy/send-confirm/{utr}', 'buySend')->name('buySend');
            Route::post('buy/cancel-confirm/{utr}', 'buyCancel')->name('buyCancel');
            Route::post('buy/refund-confirm/{utr}', 'buyRefund')->name('buyRefund');
        });

        Route::controller(SellController::class)->group(function () {
            Route::get('sell/list', 'sellList')->name('sellList');
            Route::get('sell/list/search', 'sellListSearch')->name('sellListSearch');
            Route::get('sell/view', 'sellView')->name('sellView');
            Route::delete('sell/delete/{id}', 'sellDelete')->name('sellDelete');
            Route::post('sell/multiple-delete', 'sellMultipleDelete')->name('sellMultipleDelete');

            Route::post('sell/send-confirm/{utr}', 'sellSend')->name('sellSend');
            Route::post('sell/cancel-confirm/{utr}', 'sellCancel')->name('sellCancel');
            Route::post('sell/refund-confirm/{utr}', 'sellRefund')->name('sellRefund');
        });

        Route::controller(FiatSendGatewayController::class)->group(function () {
            Route::get('fiat-send-gateway', 'index')->name('fiatSendGatewayIndex');
            Route::get('fiat-send-gateway/create', 'create')->name('fiatSendGatewayCreate');
            Route::post('fiat-send-gateway/store', 'store')->name('fiatSendGatewayStore');
            Route::get('fiat-send-gateway/edit/{id}', 'edit')->name('fiatSendGatewayEdit');
            Route::put('fiat-send-gateway/update/{id}', 'update')->name('fiatSendGatewayUpdate');
            Route::post('fiat-send-gateway/status-change', 'statusChange')->name('fiatSendGatewayStatusChange');
        });
    });
});



