<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaloriesActivity extends Model
{
    use HasFactory;

    protected $table = 'calories_activities';
    protected $fillable = [
        'id',
        'user_customer_id',
        'calories',
        'step',
        'distance',
        'recorded_at',
        'device_address'
    ];
}