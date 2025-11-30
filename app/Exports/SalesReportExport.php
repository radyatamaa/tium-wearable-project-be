<?php

namespace App\Exports;

use App\Models\SalesReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return SalesReport::selectRaw('
            DATE(payment_date) as date,
            COUNT(*) as total_number_of_payments,
            SUM(total_amount) as total_payment_amount,
            SUM(CASE WHEN payment_method = "Credit Card" THEN total_amount ELSE 0 END) as credit_card,
            SUM(CASE WHEN payment_method = "Bank Transfer" THEN total_amount ELSE 0 END) as bank_transfer,
            SUM(CASE WHEN payment_method = "Virtual Account" THEN total_amount ELSE 0 END) as virtual_account,
            SUM(CASE WHEN payment_method = "Real-Time Account Transfer" THEN total_amount ELSE 0 END) as real_time_account_transfer,
            SUM(total_amount) * 0.01 as payment_agency_fee
        ')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();
    }

    public function headings(): array
    {
        return [
            '일자',
            '총 결제금액',
            '결제건수',
            '신용카드',
            '무통장입금',
            '가상계좌',
            '실시간 계좌이체',
            '결제대행 수수료'
        ];
    }
}