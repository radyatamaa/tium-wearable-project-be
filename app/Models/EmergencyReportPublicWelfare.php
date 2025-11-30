<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class EmergencyReportPublicWelfare extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_customer_id',
        'assortment',
        'notification_time',
        'content',
        'confirmation_time',
        'action_time',
        'name',
        'main_symptom',
        'gender',
        'age',
        'contact',
        'affiliations',
        'user_general_purpose_id',
        'measureId'
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
    
    public function userCustomer()
    {
        return $this->belongsTo(UserCustomer::class,'user_customer_id');
    }
}