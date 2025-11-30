<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmergencyReportHospital;
use Illuminate\Support\Facades\DB;

class EmergencyReportHospitalDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('emergency_report_hospitals')->truncate();
        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 50; $i++) {
            EmergencyReportHospital::create([
                'alert_type' => '긴급알림' . $i,
                'alert_details' => '산소포화도 위험' . $i,
                'alert_occurrence_time' => now(),
                'patient_name' => '홍길동' . $i,
                'ward' => '503호',
                'doctor_in_charge' => '진단의학의 홍길동' . $i,
                'nurse_in_charge' => '진단의학의 김길동' . $i,
                'medical_staff' => '진단의학의 민길동' . $i,
                'cause_and_actions' => '긴급조치 필요' . $i,
                'patient_id' => 3,
            ]);
        }
    }
}