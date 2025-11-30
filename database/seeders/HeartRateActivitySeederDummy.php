<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeartRateActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HeartRateActivitySeederDummy extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding
        DB::table('heart_rate_activities')->truncate();

        $faker = \Faker\Factory::create();

        $userIds = DB::table('user_customers')->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            HeartRateActivity::create([
                'user_customer_id' => 1,
                'bpm' => $faker->numberBetween(60, 180),
                'hrv' => $faker->randomFloat(2, 20, 100),
                'device_address' => $faker->macAddress,
                'recorded_at' => Carbon::now()->addMinutes(rand(0, 59))->addSeconds(rand(0, 59)),
                'activity_type' => $faker->randomElement(['resting', 'walking', 'exercise', 'sleep']),
            ]);
        }
    }
}