<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_name',
        'address',
        'person_in_charge',
        'transaction_id',
        'service_used',
        'total_amount',
        'payment_method',
        'payment_date',
    ];
}