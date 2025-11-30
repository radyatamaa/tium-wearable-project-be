<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonInChargeInformationGeneralPurpose extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_general_purpose_id',
        'name',
        'job_title',
        'position',
        'department',
        'direct_number',
        'cell_phone_number',
        'email',
        'receive_email',
        'receive_sms',
        'file_attachments'
    ];

    public function user()
    {
        return $this->belongsTo(UserGeneralPurpose::class);
    }
}