<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasurementSettingCriteriaPatientHospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_pressure_good_health_value1',
        'blood_pressure_good_health_value2',
        'blood_pressure_caution_value1',
        'blood_pressure_caution_value2',
        'blood_pressure_dangers_value1',
        'blood_pressure_dangers_value2',
        'blood_pressure_two_weeks_value1',
        'blood_pressure_two_weeks_value2',
        'heart_rate_good_health_value1',
        'heart_rate_good_health_value2',
        'heart_rate_caution_value1',
        'heart_rate_caution_value2',
        'heart_rate_dangers_value1',
        'heart_rate_dangers_value2',
        'heart_rate_two_weeks_value1',
        'heart_rate_two_weeks_value2',
        'oxygen_saturation_good_health_value1',
        'oxygen_saturation_good_health_value2',
        'oxygen_saturation_caution_value1',
        'oxygen_saturation_caution_value2',
        'oxygen_saturation_dangers_value1',
        'oxygen_saturation_dangers_value2',
        'oxygen_saturation_two_weeks_value1',
        'oxygen_saturation_two_weeks_value2',
        'body_temperature_good_health_value1',
        'body_temperature_good_health_value2',
        'body_temperature_caution_value1',
        'body_temperature_caution_value2',
        'body_temperature_dangers_value1',
        'body_temperature_dangers_value2',
        'body_temperature_two_weeks_value1',
        'body_temperature_two_weeks_value2',
        'respiratory_good_health_value1',
        'respiratory_good_health_value2',
        'respiratory_caution_value1',
        'respiratory_caution_value2',
        'respiratory_dangers_value1',
        'respiratory_dangers_value2',
        'respiratory_two_weeks_value1',
        'respiratory_two_weeks_value2',
        'patient_id',
        'blood_pressure_systolic_good_min',
        'blood_pressure_systolic_good_max',
        'blood_pressure_systolic_caution_min',
        'blood_pressure_systolic_caution_max',
        'blood_pressure_systolic_danger_min',
        'blood_pressure_systolic_danger_max',
        'blood_pressure_diastolic_good_min',
        'blood_pressure_diastolic_good_max',
        'blood_pressure_diastolic_caution_min',
        'blood_pressure_diastolic_caution_max',
        'blood_pressure_diastolic_danger_min',
        'blood_pressure_diastolic_danger_max',
        'ecg_good_min',
        'ecg_good_max',
        'ecg_caution_min',
        'ecg_caution_max',
        'ecg_danger_min',
        'ecg_danger_max',
    ];

    protected $hidden = [
        'blood_pressure_good_health_value1',
        'blood_pressure_good_health_value2',
        'blood_pressure_caution_value1',
        'blood_pressure_caution_value2',
        'blood_pressure_dangers_value1',
        'blood_pressure_dangers_value2',
        'blood_pressure_two_weeks_value1',
        'blood_pressure_two_weeks_value2',

        'heart_rate_two_weeks_value1',
        'heart_rate_two_weeks_value2',

        'oxygen_saturation_two_weeks_value1',
        'oxygen_saturation_two_weeks_value2',

        'body_temperature_two_weeks_value1',
        'body_temperature_two_weeks_value2',

        'respiratory_two_weeks_value1',
        'respiratory_two_weeks_value2',
    ];
}