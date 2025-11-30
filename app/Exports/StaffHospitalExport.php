<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\UserStaffHospital;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StaffHospitalExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return UserStaffHospital::select('name','job_title','employee_number','gender','age','department','contact1')
        ->orderBy('created_at','desc')
        ->get();
    }

    public function headings(): array
    {
        return [
            '의료진명',
            '직무구분',
            '사번',
            '성별',
            '나이',
            '소속과목',
            '연락처'
        ];
    }
}