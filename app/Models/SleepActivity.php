<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SleepActivity extends Model
{
    use HasFactory;

    protected $table = 'sleep_activities';
    protected $fillable = [
        'user_customer_id',
        'total_sleep',
        'awakenings',
        'bedtime',
        'rem_sleep',
        'light_sleep',
        'deep_sleep',
        'onset_efficiency',
        'sleep_efficiency',
        'recorded_at',
        'device_address', 
    ];
}