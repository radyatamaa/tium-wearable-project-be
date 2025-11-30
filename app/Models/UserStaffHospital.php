<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class UserStaffHospital extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'age',
        'job_title',
        'gender',
        'department',
        'address',
        'position',
        'contact1',
        'employee_number',
        'contact2',
        'birthdate',
        'email',
        'photo_profile',
        'registration_date',
        'password',
        'user_general_purpose_id'
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