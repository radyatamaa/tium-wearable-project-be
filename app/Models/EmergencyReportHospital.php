<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class EmergencyReportHospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'alert_occurrence_time',
        'alert_details',
        'alert_confirmation_time',
        'ward',
        'alert_type',
        'doctor_in_charge',
        'nurse_in_charge',
        'medical_staff',
        'cause_and_actions',
        'user_general_purpose_id',
        'patient_id',
        'measureId'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new UserGeneralPurposeScope);

        // static::creating(function ($model) {
        //     if ($user = Auth::user()) {
        //         $model->user_general_purpose_id = $user->user_general_purpose_id;
        //     }
        // });
    }

    public function patientHospital()
    {
        return $this->belongsTo(PatientHospital::class, 'patient_id');
    }
}