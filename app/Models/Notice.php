<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Notice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status', 
        'admin_level',
        'admin_id',
        'admin_name',
        'registration_date',
        'announcement_title',
        'Announcement_details',
        'service_usage_fee_the_entire',
        'service_usage_fee_exhibit_on',
        'service_usage_fee_suspension_of_exhibition',
    ];
}