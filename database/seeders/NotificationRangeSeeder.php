<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationRangeSetting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationRangeSeeder extends Seeder
{
    public function run()
    {
        // Truncate the table before seeding
        DB::table('notification_range_settings')->truncate();

        $userIds = DB::table('user_customers')->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            NotificationRangeSetting::create([
                'user_customer_id' => $userId,
                'heart_rate_min' => 60,
                'heart_rate_max' => 100,
                'body_temperature_min' => 36.0,
                'body_temperature_max' => 37.5,
                'oxygen_saturation_min' => 90,
                'oxygen_saturation_max' => 100,
                'blood_pressure_min' => 70,
                'blood_pressure_max' => 120,
                'respiration_min' => 12,
                'respiration_max' => 20,
            ]);
        }
    }
}