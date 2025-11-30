<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class AdditionalInformationGeneralPurpose extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_general_purpose_id', 'business_number', 'corporate_number', 'type_of_business', 'business_type', 'email_receiving_tax_invoice', 'receive_email', 'data_document_output'
    ];

    public function user()
    {
        return $this->belongsTo(UserGeneralPurpose::class);
    }
}