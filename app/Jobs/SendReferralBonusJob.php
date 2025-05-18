<?php

namespace App\Jobs;

use App\Models\CryptoCurrency;
use App\Models\Referral;
use App\Models\ReferralBonus;
use App\Models\User;
use App\Traits\SendNotification;
use Facades\App\Services\BasicService;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReferralBonusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SendNotification;

    public $userId;
    public $type;
    public $amountInBase;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $type, $amountInBase = 0)
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->amountInBase = $amountInBase;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $basicControl = basicControl();
        $user = User::find($this->userId);
        $wallet = Wallet::where('user_id', $user->referral_id)->first();

        if ($wallet) {
            $cryptoCurrency = CryptoCurrency::select(['id', 'rate', 'code'])->find($wallet->crypto_currencies_id);
            if ($cryptoCurrency) {
                if ($this->type == 'registration_bonus' && $basicControl->registration_bonus) {
                    $this->sendJoiningBonus($basicControl, $user);
                    $this->sendJoiningReferralBonus($basicControl, $user, $wallet, $cryptoCurrency);
                } elseif ($basicControl->{$this->type}) {
                    $this->sendReferralBonus($user, $wallet, $cryptoCurrency);
                }
            }
        }
    }

    public function sendJoiningBonus($basicControl, $user)
    {
        $wallet = Wallet::where('user_id', $user->id)->first();
        if ($wallet) {
            $cryptoCurrency = CryptoCurrency::select(['id', 'rate', 'code'])->find($wallet->crypto_currencies_id);

            $cryptoAmount = $basicControl->registration_bonus_amount / $cryptoCurrency->rate;
            payByWallet($cryptoAmount, $wallet->crypto_currencies_id, 'add', $this->userId);
            $remark = 'Joining Bonus';
            BasicService::makeTransaction($basicControl->registration_bonus_amount, 0, '+', $remark,
                null, null, $this->userId, $cryptoAmount, $cryptoCurrency->code);

        }
    }

    public function sendJoiningReferralBonus($basicControl, $fromUser, $wallet, $cryptoCurrency)
    {
        if ($fromUser->referral) {
            if ($wallet) {
                $cryptoAmount = $basicControl->referral_user_bonus_amount / $cryptoCurrency->rate;
                payByWallet($cryptoAmount, $wallet->crypto_currencies_id, 'add', $fromUser->referral_id);
                $remark = 'Joining Referral Bonus';

                $referralBonus = BasicService::makeReferralBonus($fromUser->referral_id, $fromUser->id, $basicControl->referral_user_bonus_amount,
                    'Joining Referral Bonus', uniqid('R'), 'Joining Bonus From ' . $fromUser->username);

                BasicService::makeTransaction($basicControl->referral_user_bonus_amount, 0, '+', $remark,
                    $referralBonus->id, ReferralBonus::class, $fromUser->referral_id, $cryptoAmount, $cryptoCurrency->code);

                $this->sendReferralNotification($basicControl->registration_bonus_amount,
                    $cryptoCurrency->code, $fromUser->username, $fromUser->referral);
            }
        }
    }

    public function sendReferralBonus($fromUser, $wallet, $cryptoCurrency)
    {
        $amount = $this->amountInBase;
        $commissionType = $this->type;

        try {
            $userId = $fromUser->id;
            $i = 1;
            $level = Referral::where('commission_type', $commissionType)->count();

            while ($userId != "" || $userId != "0" || $i < $level) {
                $me = User::with('referral')->find($userId);
                $refer = $me->referral;
                if (!$refer) {
                    break;
                }
                $commission = Referral::where('commission_type', $commissionType)->where('level', $i)->first();
                if (!$commission) {
                    break;
                }
                if ($commission->amount_type == '%') {
                    $com = ($amount * $commission->commission) / 100;
                } else {
                    $com = $commission->commission;
                }

                $cryptoAmount = $com / $cryptoCurrency->rate;

                payByWallet($cryptoAmount, $wallet->crypto_currencies_id, 'add', $refer->id);
                $refer->total_commission += $com;
                $refer->save();

                $remarks = 'level' . ' ' . $i . ' ' . 'Referral bonus From' . ' ' . $fromUser->username;

                $referralBonus = BasicService::makeReferralBonus($fromUser->referral_id, $fromUser->id, $com,
                    $commissionType, uniqid('R'), $remarks);

                BasicService::makeTransaction($com, 0, '+', $remarks,
                    $referralBonus->id, ReferralBonus::class, $fromUser->referral_id, $cryptoAmount, $cryptoCurrency->code);

                $this->sendReferralNotification($com, $cryptoCurrency->code, $fromUser->username, $refer);

                $userId = $refer->id;
                $i++;
            }

        } catch (\Exception $exception) {

        }
    }
}
