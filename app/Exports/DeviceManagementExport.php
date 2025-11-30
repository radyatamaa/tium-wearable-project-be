<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\DevicesHospital;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeviceManagementExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DevicesHospital::select('device_code','usage_status','date_registration')
        ->orderBy('created_at','desc')
        ->get();
    }

    public function headings(): array
    {
        return [
            '기기코드',
            '사용 상태',
            '기기등록일'
        ];
    }
}