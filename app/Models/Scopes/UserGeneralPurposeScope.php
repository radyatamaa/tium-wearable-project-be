<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserGeneralPurposeScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if ($user = Auth::user()) {
            // Check each guard
            $user = null;
            if (Auth::guard('hospital')->check()) {
                $user = Auth::guard('hospital')->user();
            } elseif (Auth::guard('hospitalStaff')->check()) {
                $user = Auth::guard('hospitalStaff')->user();
            } elseif (Auth::guard('generalPurpose')->check()) {
                $user = Auth::guard('generalPurpose')->user();
            } elseif (Auth::guard('publicWelfare')->check()) {
                $user = Auth::guard('publicWelfare')->user();
            }
            if ($user) {
                // if (isset($user->is_administrator_tium)) {
                // if (!$user->is_administrator_tium && $user->user_id != "001") {
                //     $builder->where('user_general_purpose_id', $user->user_general_purpose_id);
                // }
                // // } 
                // else if ($user->employee_number && $user->user_id != "100") {
                //     $builder->where('user_general_purpose_id', $user->user_general_purpose_id);
                // }
                if ($user->user_general_purpose_id) {
                    $builder->where('user_general_purpose_id', $user->user_general_purpose_id);
                }

            }
        }
    }
}