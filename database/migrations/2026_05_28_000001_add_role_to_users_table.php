<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add role + referral columns before seeding so model events work
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'member'])->default('member')->after('email');
            $table->string('referral_code')->unique()->nullable()->after('role');
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete()->after('referral_code');
            $table->boolean('referral_rewarded')->default(false)->after('referred_by');
        });

        \App\Models\User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name'                 => env('ADMIN_NAME', 'Super Admin'),
                'email'                => env('ADMIN_EMAIL', 'admin@example.com'),
                'password'             => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role'                 => 'admin',
                'is_verified'          => true,
                'verification_status'  => 'approved',
                'balance'              => 0,
                'has_initial_deposit'  => true,
                'email_verified_at'    => now(),
            ]
        );

        \App\Models\SystemSetting::upsert([
            ['key' => 'initial_deposit_amount',               'value' => '375000',  'type' => 'integer', 'description' => 'Initial deposit amount required (IDR)'],
            ['key' => 'investment_full_amount',                'value' => '1500000', 'type' => 'integer', 'description' => 'Full investment amount per slot (IDR)'],
            ['key' => 'investment_installment_monthly_amount', 'value' => '125000',  'type' => 'integer', 'description' => 'Monthly installment amount (IDR)'],
            ['key' => 'investment_admin_fee_percentage',       'value' => '1',       'type' => 'float',   'description' => 'Admin fee percentage for investments (%)'],
            ['key' => 'investment_max_tenure_months',          'value' => '12',      'type' => 'integer', 'description' => 'Maximum installment tenure in months'],
            ['key' => 'minimum_withdrawal_amount',             'value' => '100000',  'type' => 'integer', 'description' => 'Minimum withdrawal amount (IDR)'],
            ['key' => 'minimum_investors_to_activate',         'value' => '5',       'type' => 'integer', 'description' => 'Minimum investors needed to activate a business'],
            ['key' => 'max_referral_depth',                    'value' => '5',       'type' => 'integer', 'description' => 'Maximum referral chain depth (levels)'],
            ['key' => 'referral_reward_level_1',               'value' => '37000',   'type' => 'integer', 'description' => 'Referral reward for Level 1 — direct referrer (IDR)'],
            ['key' => 'referral_reward_level_2',               'value' => '20000',   'type' => 'integer', 'description' => 'Referral reward for Level 2 (IDR)'],
            ['key' => 'referral_reward_level_3',               'value' => '10000',   'type' => 'integer', 'description' => 'Referral reward for Level 3 (IDR)'],
            ['key' => 'referral_reward_level_4',               'value' => '5000',    'type' => 'integer', 'description' => 'Referral reward for Level 4 (IDR)'],
            ['key' => 'referral_reward_level_5',               'value' => '3000',    'type' => 'integer', 'description' => 'Referral reward for Level 5 (IDR)'],
        ], uniqueBy: ['key'], update: ['value', 'type', 'description']);
    }

    public function down(): void
    {
        \App\Models\User::where('role', 'admin')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['role', 'referral_code', 'referred_by', 'referral_rewarded']);
        });
    }
};
