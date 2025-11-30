<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PatientCallReportHospital;
use Illuminate\Support\Facades\DB;

class PatientReportHospitalDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('patient_call_report_hospitals')->truncate();
        for ($i = 1; $i <= 50; $i++) {
            PatientCallReportHospital::create([
                'patient_name' => '홍길동 '. $i,
                'call_occurrence_time' => now(),
                'call_details' => '산소포화도 위험',
                'call_confirmation_time' => now()->addMinutes(5),
                'ward' => '305호'. $i,
                'call_type' => '긴급호출 '. $i,
                'doctor_in_charge' => '진단의학의 김길동 '. $i,
                'nurse_in_charge' => '진단의학의 민길동 '. $i,
                'medical_staff' => '진단의학의 박길동 '. $i,
                'cause_and_actions' => '긴급조치 필요 '. $i,
                'patient_id' => 3,
            ]);
        }
    }
}