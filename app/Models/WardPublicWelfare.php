<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;
class WardPublicWelfare extends Model
{
    use HasFactory;

    protected $table = 'ward_public_welfare';

    protected $fillable = [
        'ward_number',
        'ward_name',
        'usage_status',
        'registration_date',
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