<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SosContact extends Model
{
    use HasFactory;

    protected $table = 'sos_contacts';
    protected $fillable = [
        'id',
        'user_customer_id',
        'name',
        'mobile_phone'
    ];
}