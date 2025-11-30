<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding
        DB::table('notices')->truncate();

        $faker = \Faker\Factory::create();

        $adminLevels = [
            '일반관리자', // General Administrator
            '중간 관리자', // middle administrator
            '최종 관리자' // Final Administrator
        ];
        $statuses = [
            '전시중' ,// show, 
            '표시되지 않음' // hide
        ];

        foreach (range(1, 100) as $index) {
            DB::table('notices')->insert([
                'status' => $faker->randomElement($statuses),
                'admin_level' => $faker->randomElement($adminLevels),
                'admin_id' => $faker->email,
                'admin_name' => $faker->name,
                'registration_date' => Carbon::now()->subDays(rand(0, 365)),
                'announcement_title' => $faker->sentence,
                'announcement_details' => $faker->paragraph,
                'service_usage_fee_the_entire' => $faker->numberBetween(1, 100) . ' 개',
                'service_usage_fee_exhibit_on' => $faker->numberBetween(1, 100) . ' 개',
                'service_usage_fee_suspension_of_exhibition' => $faker->numberBetween(1, 100) . ' 개',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}