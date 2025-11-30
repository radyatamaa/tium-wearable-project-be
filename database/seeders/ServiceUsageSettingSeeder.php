<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceUsageSetting;

class ServiceUsageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServiceUsageSetting::create([
            'service_classification' => '베이직', // basic
            'service_classification_desc' => '베이직',
            'number_of_user_from' => 1,
            'number_of_user_to' => 50,
            'usage_fee_monthly' => 300000,
            'fees_used_year' => 3000000,
            'service_usage_fee' => '0'
        ]);

        ServiceUsageSetting::create([
            'service_classification' => '플러스',
            'service_classification_desc' => '플러스', // plus
            'number_of_user_from' => 51,
            'number_of_user_to' => 100,
            'usage_fee_monthly' => 500000,
            'fees_used_year' => 5000000,
            'service_usage_fee' => '0'
        ]);

        ServiceUsageSetting::create([
            'service_classification' => '프리미엄',
            'service_classification_desc' => '프리미엄', // premium
            'number_of_user_from' => 101,
            'number_of_user_to' => 300,
            'usage_fee_monthly' => 1000000,
            'fees_used_year' => 10000000,
            'service_usage_fee' => '0'
        ]);
    }
}