<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllActivityUser extends Model
{
    use HasFactory;

    protected $table = 'all_activity_users';
    protected $fillable = [
        'user_customer_id',
        'systolic',
        'diastolic',
        'pulse',
        'recorded_at',
        'device_address',
        'is_coming_from_gateway',
        'measureId',
        'patient_id',
        'is_history',

        'temperature',

        'saturation',
        'os_activity_type',

        'bpm',
        'hrv',
        'hr_activity_type',
        'cardiac_health_index',

        'respiratory_rate',
        'rr_activity_type',

        'ecg_data',
        'ecg_bpm',
        'ecg_speed',
        'ecg_frequency',
        'ecg_activity_type',
    ];
}