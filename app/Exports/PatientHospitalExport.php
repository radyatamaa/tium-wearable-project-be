<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\PatientHospital;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PatientHospitalExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $userGenPurposeId;

    public function __construct($userGenPurposeId)
    {
        $this->userGenPurposeId = $userGenPurposeId;
    }

    public function collection()
    {
        return PatientHospital::select('name', 'patient_code', 'gender', 'age', 'hospital_room', 'doctor_in_charge', 'nurse_in_charge')
            ->where('status', 'IN_PATIENT')
            ->where('user_general_purpose_id', $this->userGenPurposeId)
            ->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            '대상자명',
            '대상자코드',
            '성별',
            '나이',
            '입원실',
            '주치의',
            '담당간호사'
        ];
    }
}