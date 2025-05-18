<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoMethod extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'parameters', 'extra_parameters', 'description', 'status', 'is_automatic', 'field_name'];
    protected $casts = [
        'parameters' => 'object',
        'extra_parameters' => 'object',
    ];
}
