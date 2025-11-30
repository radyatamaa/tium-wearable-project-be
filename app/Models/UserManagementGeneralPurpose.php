<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class UserManagementGeneralPurpose extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'age',
        'affiliations',
        'email',
        'birth_date',
        'position',
        'unit',
        'contact_information',
        'emergency_contact_1',
        'emergency_contact_2',
        'device_code',
        'user_customer_id',
        'user_general_purpose_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new UserGeneralPurposeScope);

        static::creating(function ($model) {
            if ($user = Auth::user()) {
                // Check each guard
                $user = null;
                if (Auth::guard('generalPurpose')->check()) {
                    $user = Auth::guard('generalPurpose')->user();
                }
                if ($user) {
                    if (!$user->is_administrator_tium) {
                        $model->user_general_purpose_id = $user->user_general_purpose_id;
                    }
                }
            }
        });
    }

    public function userCustomer()
    {
        return $this->belongsTo(UserCustomer::class, 'user_customer_id');
    }
}