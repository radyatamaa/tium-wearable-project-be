<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ServiceUsageSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_classification',
        'service_classification_desc',
        'number_of_user_from',
        'number_of_user_to',
        'usage_fee_monthly',
        'fees_used_year',
        'service_usage_fee'
    ];
}