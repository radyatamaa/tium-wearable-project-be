<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class UserStaffPublicWelfare extends Model
{
    use HasFactory;

    protected $table = 'user_staff_public_welfare';

    protected $fillable = [
        'name',
        'job_category',
        'position',
        'employee_id',
        'hire_date',
        'date_of_birth',
        'age',
        'gender',
        'address',
        'contact1',
        'contact2',
        'email',
        'welfare_beneficiary',
        'user_id',
        'photo_profile',
        'password',
        'user_general_purpose_id'
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
                if($user) {
                    if(!$user->is_administrator_tium) {
                        $model->user_general_purpose_id = $user->user_general_purpose_id;
                    }    
                }
            }
        });
    }
}