<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserKyc extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'kyc_info' => 'object',
    ];

    public function scopeOwn($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatus($type = null)
    {
        if ($this->status == 0) {
            return !$type ? '<span class="badge text-bg-dark">Pending</span>' : 'Pending';
        } elseif ($this->status == 1) {
            return !$type ? '<span class="badge text-bg-primary">Verified</span>' : 'Verified';
        } else {
            return !$type ? '<span class="badge text-bg-danger">Rejected</span>' : 'Rejected';
        }
    }

    public function kycInfoShow()
    {
        $kycInfo = [];
        foreach ($this->kyc_info as $info) {
            if ($info->type == 'file') {
                $kycInfo[] = [
                    'name' => stringToTitle($info->field_name),
                    'value' => getFile($info->field_driver, $info->field_value),
                    'type' => $info->type
                ];
            } else {
                $kycInfo[] = [
                    'name' => stringToTitle($info->field_name),
                    'value' => $info->field_value,
                    'type' => $info->type
                ];
            }
        }
        return $kycInfo;
    }
}
