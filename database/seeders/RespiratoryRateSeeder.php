<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RespiratoryRateActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RespiratoryRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding
        DB::table('respiratory_rate_activities')->truncate();

        $faker = \Faker\Factory::create();

        $userIds = DB::table('user_customers')->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            RespiratoryRateActivity::create([
                'user_customer_id' => 1,
                'rate' => $faker->randomFloat(2, 12, 30),
                'activity_type' => $faker->randomElement(['sleep']),
                'recorded_at' => Carbon::now()->addMinutes(rand(0, 59))->addSeconds(rand(0, 59)),
            ]);
        }
    }
}