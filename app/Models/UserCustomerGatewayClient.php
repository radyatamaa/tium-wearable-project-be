<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserCustomerGatewayClient extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory,SoftDeletes;

    protected $fillable = [
        'serial_number_device',
        'secret_key',
        'digit'
    ];

    protected $hidden = [
        'secret_key',
    ];
}