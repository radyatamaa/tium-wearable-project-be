<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmergencyReportGeneralPurpose;
use App\Models\UserCustomer;
use Illuminate\Support\Facades\DB;

class EmergencyReportGeneralPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('emergency_report_general_purposes')->truncate();
        $faker = \Faker\Factory::create();

        $user = UserCustomer::first();
        for ($i = 1; $i <= 10; $i++) {
            EmergencyReportGeneralPurpose::create([
                'name' => $user->name,
                'main_symptom' => 'Symptom ' . $i,
                'gender' => $faker->randomElement(['F', 'M']),
                'age' => rand(18, 60),
                'contact' => $user->phone_number,
                'affiliations' => 'affiliations' . $i,
                'user_customer_id' => $user->id,
                'assortment' => $faker->randomElement(['danger', 'warning']),
                'content' => 'Content ' . $i,
                'confirmation_time' => null,
                'notification_time' => now(),
                'action_time' => null,
            ]);
        }
    }
}