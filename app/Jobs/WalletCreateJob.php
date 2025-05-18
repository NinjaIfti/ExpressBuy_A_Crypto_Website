<?php

namespace App\Jobs;

use App\Models\CryptoCurrency;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WalletCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cryptos = CryptoCurrency::where('status', 1)->orderBy('sort_by', 'ASC')->pluck('id');

        if ($cryptos->isNotEmpty()) {
            foreach ($cryptos as $cryptoId) {
                Wallet::firstOrCreate([
                    'user_id' => $this->userId,
                    'crypto_currencies_id' => $cryptoId
                ], [
                    'balance' => 0.00
                ]);
            }
        }

    }
}
