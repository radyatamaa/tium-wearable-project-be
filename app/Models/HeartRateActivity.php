<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeartRateActivity extends Model
{
    use HasFactory;

    protected $table = 'heart_rate_activities';
    protected $fillable = [
        'id',
        'user_customer_id',
        'bpm',
        'hrv',
        'device_address',
        'recorded_at',
        'activity_type',
        'is_coming_from_gateway',
        'measureId',
        'patient_id',
        'is_history'
    ];

    public function userCustomer()
    {
        return $this->belongsTo(UserCustomer::class, 'user_customer_id');
    }

    public function patient()
    {
        return $this->belongsTo(PatientHospital::class, 'patient_id');
    }
}