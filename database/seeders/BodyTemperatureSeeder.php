<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BodyTemperatureActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BodyTemperatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding
        DB::table('body_temperature_activities')->truncate();

        $faker = \Faker\Factory::create();

        $userIds = DB::table('user_customers')->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            BodyTemperatureActivity::create([
                'user_customer_id' => 1,
                'temperature' => $faker->randomFloat(2, 35.5, 39.5),
                'recorded_at' => Carbon::now()->addMinutes(rand(0, 59))->addSeconds(rand(0, 59)),
            ]);
        }
    }
}