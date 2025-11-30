<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosHistoryCall extends Model
{
    use HasFactory;

    protected $table = 'sos_history_calls';
    protected $fillable = [
        'id',
        'user_customer_id',
        'duration',
        'description',
        'name_contact_sos',
        'phone_number_contact_sos',
    ];
}