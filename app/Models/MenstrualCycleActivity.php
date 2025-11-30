<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenstrualCycleActivity extends Model
{
    use HasFactory;

    protected $table = 'menstrual_cycle_activities';
    protected $fillable = [
        'user_customer_id',
        'average_duration',
        'average_cycle',
        'start_date',
        'device_address', 
    ];
}