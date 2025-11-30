<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodPressureActivity extends Model
{
    use HasFactory;
    protected $table = 'blood_pressure_activities';
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