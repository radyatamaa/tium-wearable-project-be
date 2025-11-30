<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionInformationGeneralPurpose extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_general_purpose_id',
        'membership_registration_date',
        'service_start_date',
        'service_expiration_date',
        'representative_contact_info',
        'fax_number',
        'id',
        'password',
        'organization_name',
        'address',
        'name_of_representative',
        'number_of_workers',
        'homepage',
        'hr_management_usage',
        'full_address',
        'postal_code',
        'city_id',
        'field_of_help',
        'selected_subjects'
    ];

    public function user()
    {
        return $this->belongsTo(UserGeneralPurpose::class);
    }
}