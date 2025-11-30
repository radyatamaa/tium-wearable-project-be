<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BloodPressureActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BloodPressureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding
        DB::table('blood_pressure_activities')->truncate();

        $faker = \Faker\Factory::create();

        $userIds = DB::table('user_customers')->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            BloodPressureActivity::create([
                'user_customer_id' => 1,
                'systolic' => $faker->numberBetween(90, 140),
                'diastolic' => $faker->numberBetween(60, 90),
                'pulse' => $faker->numberBetween(60, 100),
                'recorded_at' => Carbon::now()->addMinutes(rand(0, 59))->addSeconds(rand(0, 59)),
            ]);
        }
    }
}