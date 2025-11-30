<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDataCity extends Model
{
    use HasFactory;

    protected $fillable = ['city_name', 'city_name_en'];
}