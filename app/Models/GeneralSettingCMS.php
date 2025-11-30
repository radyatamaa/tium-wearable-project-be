<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSettingCMS extends Model
{
    use HasFactory;

    protected $table = 'general_setting_cms';
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
    ];
}