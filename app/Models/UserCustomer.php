<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class UserCustomer extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'gender',
        'birth_date',
        'height',
        'weight',
        'phone_number',
        'smartwatch_device',
        'doctor_name',
        'room_number',
        'note',
        'naver_id',
        'kakao_id',
    ];

    public function getAge()
    {
        return Carbon::parse($this->birth_date)->age;
    }
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'height' => 'string',
        'weight' => 'string'
    ];

    public function emergencyReportGeneralPurpose()
    {
        return $this->hasOne(EmergencyReportGeneralPurpose::class, 'user_customer_id');
    }

    public function toArray()
    {
        $array = parent::toArray();

        // Pastikan semua kolom muncul meskipun bernilai null
        foreach ($this->fillable as $column) {
            if (!array_key_exists($column, $array) && $column != 'password') {
                $array[$column] = null;
            }
        }

        return $array;
    }
}