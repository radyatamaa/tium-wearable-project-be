<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\PatientCallReportHospital;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PatientCallHistoryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PatientCallReportHospital::select('patient_name','call_occurrence_time','call_details','call_confirmation_time','ward')
        ->orderBy('created_at','desc')
        ->get();
    }

    public function headings(): array
    {
        return [
            '대상자명',
            '알림 발생 일시	',
            '알림 상세',
            '알림 확인 일시',
            '입원실',
        ];
    }
}