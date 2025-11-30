<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;

class TopLocation extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'top_locations';

    protected $fillable = [
        'location_name',
        'location_code',
        'usage_status',
        'registration_date',
        'hospital_id',
        'user_general_purpose_id'
    ];

    protected $casts = [
        'usage_status' => 'boolean',
        'registration_date' => 'datetime'
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
    
    public $timestamps = true;
}