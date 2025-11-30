<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\UserAdminHospital;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountHospitalExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return UserAdminHospital::select('user_id','name_person_in_charge','permission','registration_date')
        ->orderBy('created_at','desc')
        ->get();
    }

    public function headings(): array
    {
        return [
            '아이디',
            '담당자명',
            '계정권한',
            '등록일자'
        ];
    }
}