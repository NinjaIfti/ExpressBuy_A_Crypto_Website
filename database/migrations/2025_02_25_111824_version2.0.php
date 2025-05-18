<?php

use App\Jobs\WalletCreateJob;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exchange_requests', function (Blueprint $table) {
            $table->text('admin_feedback')->nullable();
        });

        Schema::table('buy_requests', function (Blueprint $table) {
            $table->text('admin_feedback')->nullable();
        });

        Schema::table('sell_requests', function (Blueprint $table) {
            $table->text('admin_feedback')->nullable();
        });

        Schema::table('crypto_methods', function (Blueprint $table) {
            $table->text('field_name')->nullable()->comment('for manual payment proof');
        });

        Schema::table('basic_controls', function (Blueprint $table) {
            $table->boolean('deposit_commission')->default(0);
            $table->boolean('exchange_commission')->default(0);
            $table->float('registration_bonus_amount')->default(0.0);
            $table->float('referral_user_bonus_amount')->default(0.0);
            $table->boolean('registration_bonus')->default(0);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->float('total_commission')->default(0.00);
        });

        $senderEmail = basicControl()->sender_email;
        $languageId = \App\Models\Language::where('default_status', 1)->first()->id;

        DB::statement("
            INSERT INTO `notification_templates` (
                `id`, `language_id`, `name`, `template_key`, `email_from`,
                `subject`, `short_keys`, `email`, `sms`, `in_app`, `push`,
                `status`, `notify_for`, `lang_code`, `created_at`, `updated_at`
            ) VALUES (
                NULL, '$languageId', 'Referral Bonus', 'REFERRAL_BONUS', '$senderEmail',
                'Congrats! you get referral bonus',
                '{\"amount\":\"Amount\",\"baseCurrency\":\"Site Currency\",\"deposit_code\":\"Deposit Currency Code\",\"fromUser\":\"From User\"}',
                '[[amount]] [[baseCurrency]] credited for referral bonus from [[fromUser]]. Please check your [[deposit_code]] wallet.',
                '[[amount]] [[baseCurrency]] credited for referral bonus from [[fromUser]]. Please check your [[deposit_code]] wallet.',
                '[[amount]] [[baseCurrency]] credited for referral bonus from [[fromUser]]. Please check your [[deposit_code]] wallet.',
                '[[amount]] [[baseCurrency]] credited for referral bonus from [[fromUser]]. Please check your [[deposit_code]] wallet.',
                '{\"mail\":\"1\",\"sms\":\"1\",\"in_app\":\"1\",\"push\":\"1\"}',
                '0', 'en', '2021-08-03 00:05:43', '2024-01-21 16:40:05'
            )
        ");

        \App\Models\User::get()->map(function ($query) {
            dispatch(new WalletCreateJob($query->id));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_requests', function (Blueprint $table) {
            $table->dropColumn('admin_feedback');
        });

        Schema::table('buy_requests', function (Blueprint $table) {
            $table->dropColumn('admin_feedback');
        });

        Schema::table('sell_requests', function (Blueprint $table) {
            $table->dropColumn('admin_feedback');
        });

        Schema::table('crypto_methods', function (Blueprint $table) {
            $table->dropColumn('field_name');
        });

        Schema::table('basic_controls', function (Blueprint $table) {
            $table->dropColumn('deposit_commission');
            $table->dropColumn('exchange_commission');
            $table->dropColumn('registration_bonus_amount');
            $table->dropColumn('referral_user_bonus_amount');
            $table->dropColumn('registration_bonus');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_commission');
        });

        DB::table('notification_templates')
            ->where('template_key', 'REFERRAL_BONUS')
            ->delete();

    }
};
