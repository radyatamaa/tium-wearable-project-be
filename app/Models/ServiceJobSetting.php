<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceJobSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_classification',
        'service_classification_desc',
        'job_title',
        'position',
        'status_by_service',
        'color_dashboard',
        'icon'
    ];
}