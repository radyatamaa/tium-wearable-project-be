<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MemberStatus extends Model
{
    use HasFactory;

    protected $table = 'member_status';

    protected $fillable = [
        'field_of_help',
        'worker',
        'service_of_use',
        'last_payment_date',
        'another_payment_date'
    ];
    public function user()
    {
        return $this->belongsTo(UserGeneralPurpose::class);
    }
}