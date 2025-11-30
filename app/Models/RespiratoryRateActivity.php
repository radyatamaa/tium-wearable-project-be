<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespiratoryRateActivity extends Model
{
    use HasFactory;

    protected $table = 'respiratory_rate_activities';
    protected $fillable = [
        'user_customer_id',
        'rate',
        'activity_type',
        'recorded_at',
        'device_address',
        'is_coming_from_gateway',
        'measureId',
        'is_history'
    ];
}