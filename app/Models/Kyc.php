<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{

    use HasFactory;

    protected $guarded = ['id'];

    public $casts = ['input_form' => 'object'];

    public function userKycs()
    {
        return $this->hasMany(UserKyc::class, 'kyc_id');
    }

    public function kycPosition()
    {
        $isUserVerified = UserKyc::where('user_id', auth()->id())->where('kyc_id', $this->id)
            ->where('status', 1)->exists();
        if ($isUserVerified) {
            return 'verified';
        }
        return 'unverified';
    }

}
