<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function cryptoCurrency()
    {
        return $this->belongsTo(CryptoCurrency::class, 'crypto_currencies_id');
    }

    public function getBalanceAttribute()
    {
        return floatval($this->attributes['balance']);
    }

}
