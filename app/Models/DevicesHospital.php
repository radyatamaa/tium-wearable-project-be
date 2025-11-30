<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class DevicesHospital extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date_registration',
        'user_name',
        'device_code',
        'usage_status',
        'user_general_purpose_id',
        'detailed_location_id',
        'sub_location_id',
        'top_location_id',
        'patient_id'
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

    public function detailedLocation()
    {
        return $this->belongsTo(DetailedLocation::class);
    }
    
    public function patientHospital()
    {
        return $this->belongsTo(PatientHospital::class, 'patient_id');
    }

    public function subLocation()
    {
        return $this->belongsTo(SubLocation::class);
    }

    public function topLocation()
    {
        return $this->belongsTo(TopLocation::class);
    }
}