<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationRangeSetting extends Model
{
    use HasFactory;

    protected $table = 'notification_range_settings';
    protected $fillable = [
        'user_customer_id',
        'heart_rate_min',
        'heart_rate_max',
        'body_temperature_min',
        'body_temperature_max',
        'oxygen_saturation_min',
        'oxygen_saturation_max',
        'blood_pressure_min',
        'blood_pressure_max',
        'respiration_min',
        'respiration_max',
    ];
}