<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SleepActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SleepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding
        DB::table('sleep_activities')->truncate();

        $faker = \Faker\Factory::create();

        $userIds = DB::table('user_customers')->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            SleepActivity::create([
                'user_customer_id' => 1,
                'total_sleep' => $faker->numberBetween(4, 10),
                'awakenings' => $faker->numberBetween(0, 5),
                'bedtime' => $faker->numberBetween(20, 60),
                'rem_sleep' => $faker->numberBetween(0, 4),
                'light_sleep' => $faker->numberBetween(0, 4),
                'deep_sleep' => $faker->numberBetween(0, 4),
                'onset_efficiency' => $faker->numberBetween(50, 100),
                'sleep_efficiency' => $faker->numberBetween(50, 100),
                'recorded_at' => Carbon::now()->addMinutes(rand(0, 59))->addSeconds(rand(0, 59)),
            ]);
        }
    }
}