<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\DetailedLocation;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DetailedLocationExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DetailedLocation::select('location_name','location_code','usage_status','registration_date')
        ->orderBy('created_at','desc')
        ->get();
    }

    public function headings(): array
    {
        return [
            '상세 로케이션명',
            '상위 로케이션 코드',
            '사용 여부',
            '등록일자'
        ];
    }
}