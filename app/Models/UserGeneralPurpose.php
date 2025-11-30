<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserGeneralPurpose extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id','nickname','password', 'address', 'receive_email', 'receive_sms', 'data_output', 'member_status','user_type','photo_profile'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    public function subscriptionInformation()
    {
        return $this->hasOne(SubscriptionInformationGeneralPurpose::class, 'user_general_purpose_id');
    }

    public function personInChargeInformation()
    {
        return $this->hasOne(PersonInChargeInformationGeneralPurpose::class, 'user_general_purpose_id');
    }

    public function additionalInformation()
    {
        return $this->hasOne(AdditionalInformationGeneralPurpose::class, 'user_general_purpose_id');
    }

    public function serviceSubscriptionInformation()
    {
        return $this->hasOne(ServiceSubscriptionInformationGeneralPurpose::class, 'user_general_purpose_id');
    }
    public function MemberStatus()
    {
        return $this->hasOne(MemberStatus::class, 'user_general_purpose_id');
    }
}