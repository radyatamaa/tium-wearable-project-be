<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class DevicesSmartWatchPublicWelfare extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial', 'name', 'registered_to', 'usage_status','registration_date',
        'user_general_purpose_id','user_customer_id'
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
}