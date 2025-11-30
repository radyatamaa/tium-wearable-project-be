<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\EmergencyReportHospital;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmergencyNotifHistoryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return EmergencyReportHospital::select('patient_name','alert_occurrence_time','alert_details','alert_confirmation_time','ward')
        ->orderBy('created_at','desc')
        ->get();
    }

    public function headings(): array
    {
        return [
            '대상자명',
            '알림 발생 일시',
            '알림 상세',
            '알림 확인 일시',
            '입원실'
        ];
    }
}