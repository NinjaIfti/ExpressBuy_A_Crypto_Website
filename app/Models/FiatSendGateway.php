<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiatSendGateway extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'parameters' => 'object',
        'supported_currency' => 'object'
    ];

    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        return getFile($this->driver, $this->image);
    }
}
