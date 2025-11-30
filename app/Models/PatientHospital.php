<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\Scopes\UserGeneralPurposeScope;

class PatientHospital extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_code',
        'name',
        'date_of_birth',
        'age',
        'height_cm',
        'weight_kg',
        'detail_location_id',
        'doctor_in_charge',
        'nurse_in_charge',
        'diagnostic_name',
        'device_code',
        'contact_information',
        'emergency_contact_1',
        'emergency_contact_2',
        'user_general_purpose_id',
        'detailed_location_id',
        'sub_location_id',
        'top_location_id',
        'doctor_in_charge_id',
        'nurse_in_charge_id',
        'hospital_room',
        'gender',
        'status',
        'discharge_date',
        'user_customer_id'
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

    public function userGeneralPurpose()
    {
        return $this->belongsTo(UserGeneralPurpose::class);
    }

    public function subscriptionInformationGeneralPurpose()
    {
        return $this->belongsTo(SubscriptionInformationGeneralPurpose::class, 'user_general_purpose_id');
    }

    public function detailedLocation()
    {
        return $this->belongsTo(DetailedLocation::class, 'detailed_location_id');
    }

    public function subLocation()
    {
        return $this->belongsTo(SubLocation::class, 'sub_location_id');
    }

    public function topLocation()
    {
        return $this->belongsTo(TopLocation::class, 'top_location_id');
    }

    public function doctorInCharge()
    {
        return $this->belongsTo(UserStaffHospital::class, 'doctor_in_charge_id');
    }

    public function nurseInCharge()
    {
        return $this->belongsTo(UserStaffHospital::class, 'nurse_in_charge_id');
    }

    public function userCustomer()
    {
        return $this->belongsTo(UserCustomer::class, 'user_customer_id');
    }
}