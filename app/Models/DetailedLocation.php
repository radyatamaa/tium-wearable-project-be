<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailedLocation extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'location_name',
        'location_code',
        'usage_status',
        'registration_date',
        'hospital_id',
        'user_general_purpose_id',
        'sub_location_id'
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

    public function subLocation()
    {
        return $this->belongsTo(SubLocation::class, 'sub_location_id');
    }
}