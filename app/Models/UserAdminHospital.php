<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class UserAdminHospital extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'password',
        'name_person_in_charge',
        'permission',
        'registration_date',
        'contact_person_position',
        'contact_number',
        'email',
        'user_general_purpose_id',
        'is_administrator_company',
        'is_administrator_tium'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new UserGeneralPurposeScope);

        static::creating(function ($model) {
            if ($user = Auth::user()) {
                // Check each guard
                $user = null;
                if (Auth::guard('hospital')->check()) {
                    $user = Auth::guard('hospital')->user();
                }
                if ($user) {
                    if (!$user->is_administrator_tium) {
                        $model->user_general_purpose_id = $user->user_general_purpose_id;
                    }
                }
            }
        });
    }
}