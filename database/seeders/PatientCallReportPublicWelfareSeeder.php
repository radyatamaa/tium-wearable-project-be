<?php

namespace Database\Seeders;

use App\Models\PatientCallReportPublicWelfare;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmergencyReportPublicWelfare;
use App\Models\UserCustomer;
use Illuminate\Support\Facades\DB;

class PatientCallReportPublicWelfareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('patient_call_report_public_welfares')->truncate();
        $faker = \Faker\Factory::create();

        $user = UserCustomer::first();
        for ($i = 1; $i <= 10; $i++) {
            PatientCallReportPublicWelfare::create([
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