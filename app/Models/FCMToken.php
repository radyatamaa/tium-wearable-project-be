<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FCMToken extends Model
{
    use HasFactory;

    protected $table = 'fcm_tokens';
    protected $fillable = [
        'device',
        'fcm_token',
        'notification_type'
    ];
}