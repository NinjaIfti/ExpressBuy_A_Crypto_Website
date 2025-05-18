<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiatCurrency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code', 'symbol', 'rate', 'usd_rate', 'processing_fee', 'processing_fee_type', 'min_send', 'max_send', 'image', 'driver', 'status', 'sort_by'];

    protected $appends = ['image_path', 'currency_name'];

    public function getImagePathAttribute()
    {
        return getFile($this->driver, $this->image);
    }

    public function getCurrencyNameAttribute()
    {
        return $this->code . ' - ' . $this->name;
    }
}
