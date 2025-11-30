<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicesSmartWatchUserCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_code',
        'usage_status',
        'user_customer_id'
    ];
}