<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OxygenSaturationActivity extends Model
{
    use HasFactory;

    protected $table = 'oxygen_saturation_activities';
    protected $fillable = [
        'user_customer_id',
        'saturation',
        'activity_type',
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