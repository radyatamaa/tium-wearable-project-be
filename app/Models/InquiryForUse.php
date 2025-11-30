<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryForUse extends Model
{
    use HasFactory;

    protected $fillable = [
        'username', 
        'organization', 
        'user_type', 
        'inquirer', 
        'title', 
        'status', 
        'registration_date', 
        'response_date', 
        'responder', 
        'inquiry_details', 
        'response_details'
    ];
}