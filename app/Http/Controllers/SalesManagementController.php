<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\SalesReport;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;

class SalesManagementController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        // Summary Data grouped by date with pagination
        $summary = SalesReport::selectRaw('
            DATE(payment_date) as date,
            COUNT(*) as total_number_of_payments,
            SUM(total_amount) as total_payment_amount,
            SUM(CASE WHEN payment_method = "Credit Card" THEN total_amount ELSE 0 END) as credit_card,
            SUM(CASE WHEN payment_method = "Bank Transfer" THEN total_amount ELSE 0 END) as bank_transfer,
            SUM(CASE WHEN payment_method = "Virtual Account" THEN total_amount ELSE 0 END) as virtual_account,
            SUM(CASE WHEN payment_method = "Real-Time Account Transfer" THEN total_amount ELSE 0 END) as real_time_account_transfer,
            SUM(total_amount) - SUM(total_amount) * 0.01 as sales_amount,
            SUM(total_amount) * 0.01 as payment_agency_fee
        ')
            ->groupBy('date')
            ->orderBy('date', 'desc');

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $summary->whereDate('payment_date', '>=', $request->start_date_range)
                    ->whereDate('payment_date', '<=', $request->end_date_range);
            }
        }

        $summary = $summary->paginate($perPage);

        return view('sales-management.index', compact('summary'));
    }

    public function details(Request $request, $date)
    {
        $perPage = $request->input('limit', 10);
        // Detailed Data for a specific date with pagination
        $salesReports = SalesReport::whereDate('payment_date', $date)
            ->orderBy('payment_date', 'desc')
            ->paginate($perPage);

        return response()->json($salesReports);
    }

    public function downloadExcel(Request $request)
    {
        return Excel::download(new SalesReportExport, 'sales-report' . ' ' . now() . '.xlsx');
    }
}