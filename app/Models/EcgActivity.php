<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcgActivity extends Model
{
    use HasFactory;

    protected $table = 'ecg_activities';
    protected $fillable = [
        'id',
        'user_customer_id',
        'ecg_data',
        'device_address',
        'recorded_at',
        'activity_type',
        'is_coming_from_gateway',
        'measureId',
        'bpm',
        'speed',
        'frequency',
        'patient_id'
    ];
}