<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crypto_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreignId('crypto_currency_id')->index();
            $table->text('wallet_address')->nullable();
            $table->string('currency_code')->nullable();
            $table->decimal('amount', 18, 8)->default(0.00000000);
            $table->enum('type', ['automatic', 'manual'])->default('automatic');
            $table->boolean('status')->default(0)->comment("0=>initiate,1=>complete,2=>request,3=>rejected");
            $table->string('utr')->nullable();
            $table->text('proof')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_wallets');
    }
};
