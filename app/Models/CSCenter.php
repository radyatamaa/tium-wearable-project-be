<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CSCenter extends Model
{
    use HasFactory;

    protected $table = 'cs_center';
    protected $fillable = [
        'registration_date',
        'name',
        'email',
        'title',
        'content',
    ];
}