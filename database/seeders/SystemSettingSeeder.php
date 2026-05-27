<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'initial_deposit_amount',
                'value' => '375000',
                'type' => 'integer',
                'description' => 'Initial deposit amount required (IDR)',
            ],
            [
                'key' => 'investment_full_amount',
                'value' => '1500000',
                'type' => 'integer',
                'description' => 'Full investment amount per slot (IDR)',
            ],
            [
                'key' => 'investment_installment_monthly_amount',
                'value' => '125000',
                'type' => 'integer',
                'description' => 'Monthly installment amount (IDR)',
            ],
            [
                'key' => 'investment_admin_fee_percentage',
                'value' => '1',
                'type' => 'float',
                'description' => 'Admin fee percentage for investments (%)',
            ],
            [
                'key' => 'investment_max_tenure_months',
                'value' => '12',
                'type' => 'integer',
                'description' => 'Maximum installment tenure in months',
            ],
            [
                'key' => 'minimum_withdrawal_amount',
                'value' => '100000',
                'type' => 'integer',
                'description' => 'Minimum withdrawal amount (IDR)',
            ],
            [
                'key' => 'minimum_investors_to_activate',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Minimum investors needed to activate a business',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
