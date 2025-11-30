<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class AdminPublicWelfare extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'classification',
        'department',
        'contact',
        'registration_date',
        'user_general_purpose_id',
        'is_administrator_company',
        'is_administrator_tium',
        'password'
    ];

    protected $hidden = [
        'password',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new UserGeneralPurposeScope);

        static::creating(function ($model) {
            if ($user = Auth::user()) {
                // Check each guard
                $user = null;
                if (Auth::guard('publicWelfare')->check()) {
                    $user = Auth::guard('publicWelfare')->user();
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