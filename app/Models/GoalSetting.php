<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalSetting extends Model
{
    use HasFactory;

    protected $table = 'goal_settings';
    protected $fillable = [
        'user_customer_id',
        'exercise_goal',
        'sleep_goal',
        'distance_goal',
        'calories_goal'
    ];
}