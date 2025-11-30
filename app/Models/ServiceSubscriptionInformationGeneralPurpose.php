<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ServiceSubscriptionInformationGeneralPurpose extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_general_purpose_id', 'subscription_service', 'start_date_of_use', 'expiration_date', 'number_of_service_users', 'usage_period_months', 'service_fee'
    ];

    public function user()
    {
        return $this->belongsTo(UserGeneralPurpose::class);
    }
}