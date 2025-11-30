<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class PatientCallReportHospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'call_occurrence_time',
        'call_details',
        'call_confirmation_time',
        'ward',
        'call_type',
        'doctor_in_charge',
        'nurse_in_charge',
        'medical_staff',
        'cause_and_actions',
        'user_general_purpose_id',
        'patient_id',
        'measureId',
        'serial_number_gateway'
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