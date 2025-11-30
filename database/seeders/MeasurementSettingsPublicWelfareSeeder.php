<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MeasurementSettingsPublicWelfare;

class MeasurementSettingsPublicWelfareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MeasurementSettingsPublicWelfare::create([
            // Body Temp
            'body_temp_caution_min' => 34.5,
            'body_temp_caution_max' => 38.5,
            'body_temp_danger_min' => 33.5,
            'body_temp_danger_max' => 39.5,
            // Heart Rate
            'heart_rate_caution_min' => 60,
            'heart_rate_caution_max' => 120,
            'heart_rate_danger_min' => 50,
            'heart_rate_danger_max' => 160,
            // Respiratory Rate
            'respiratory_rate_caution_min' => 15,
            'respiratory_rate_caution_max' => 22,
            'respiratory_rate_danger_min' => 10,
            'respiratory_rate_danger_max' => 30,
            // Blood Pressure - Systolic
            'blood_pressure_systolic_caution_min' => 60,
            'blood_pressure_systolic_caution_max' => 140,
            'blood_pressure_systolic_danger_min' => 50,
            'blood_pressure_systolic_danger_max' => 100,
            // Blood Pressure - Diastolic
            'blood_pressure_diastolic_caution_min' => 50,
            'blood_pressure_diastolic_caution_max' => 160,
            'blood_pressure_diastolic_danger_min' => 40,
            'blood_pressure_diastolic_danger_max' => 120,
            // Oxygen Saturation
            'oxygen_saturation_caution_min' => 95,
            'oxygen_saturation_caution_max' => 85,
            // External Temperature Criteria
            'external_temp_default_value' => 35,
        ]);
    }
}