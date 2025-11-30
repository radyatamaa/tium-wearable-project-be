<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class MeasurementSettingsPublicWelfare extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_general_purpose_id',
        'body_temp_caution_min',
        'body_temp_caution_max',
        'body_temp_danger_min',
        'body_temp_danger_max',
        'heart_rate_caution_min',
        'heart_rate_caution_max',
        'heart_rate_danger_min',
        'heart_rate_danger_max',
        'respiratory_rate_caution_min',
        'respiratory_rate_caution_max',
        'respiratory_rate_danger_min',
        'respiratory_rate_danger_max',
        'blood_pressure_systolic_caution_min',
        'blood_pressure_systolic_caution_max',
        'blood_pressure_systolic_danger_min',
        'blood_pressure_systolic_danger_max',
        'blood_pressure_diastolic_caution_min',
        'blood_pressure_diastolic_caution_max',
        'blood_pressure_diastolic_danger_min',
        'blood_pressure_diastolic_danger_max',
        'oxygen_saturation_caution_min',
        'oxygen_saturation_caution_max',
        'oxygen_saturation_danger_min',
        'oxygen_saturation_danger_max',
        'external_temp_default_value',
        'ecg_caution_min',
        'ecg_caution_max',
        'ecg_danger_min',
        'ecg_danger_max',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new UserGeneralPurposeScope);

        static::creating(function ($model) {
            if ($user = Auth::user()) {
                $model->user_general_purpose_id = $user->user_general_purpose_id;
            }
        });
    }
}