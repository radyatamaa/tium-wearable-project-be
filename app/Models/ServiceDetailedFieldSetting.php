<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ServiceDetailedFieldSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_classification',
        'service_classification_desc',
        'medical_subject',
        'detailed_field_by_service'
    ];
}