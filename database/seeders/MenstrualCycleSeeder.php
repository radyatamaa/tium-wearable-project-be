<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenstrualCycleActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MenstrualCycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding
        DB::table('menstrual_cycle_activities')->truncate();

        $faker = \Faker\Factory::create();
        $userIds = DB::table('user_customers')->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            MenstrualCycleActivity::create([
                'user_customer_id' => 1,
                'average_duration' => $faker->numberBetween(3, 7),
                'average_cycle' => $faker->numberBetween(25, 35),
                'start_date' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }
    }
}