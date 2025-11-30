<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientHospitalsDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data for patient_hospitals table
        $data = [
            [
                'patient_code' => 'PAT001',
                'name' => 'John Smith',
                'date_of_birth' => '1985-05-15',
                'age' => 39,
                'height_cm' => 175,
                'weight_kg' => 70,
                'detail_location_id' => 'DetailLoc001',
                'doctor_in_charge' => 'Dr. Lee',
                'nurse_in_charge' => 'Nurse Kim',
                'diagnostic_name' => 'Routine Checkup',
                'device_code' => 'DEV001',
                'contact_information' => 'john.smith@example.com',
                'emergency_contact_1' => 'Jane Smith (Spouse)',
                'emergency_contact_2' => 'James Smith (Sibling)',
                'user_general_purpose_id' => null,
                'detailed_location_id' => 1,
                'sub_location_id' => 1,
                'top_location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Insert data into patient_hospitals table
        DB::table('patient_hospitals')->insert($data);
    }
}