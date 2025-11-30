<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDataDistrict extends Model
{
    use HasFactory;

    protected $fillable = ['district_name', 'district_name_en', 'postal_code', 'city_id'];


    public function masterDataCity()
    {
        return $this->belongsTo(MasterDataCity::class, 'city_id');
    }
}