<?php

namespace App\Http\Controllers;

use App\Models\EmergencyReportHospital;
use App\Exports\EmergencyNotifHistoryExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class EmergencyAlertsController extends Controller
{
    public function downloadExcel(Request $request)
    {
        return Excel::download(new EmergencyNotifHistoryExport, 'emergency-alert-report' . ' ' . now() . '.xlsx');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = EmergencyReportHospital::orderBy('created_at', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by == 'alarm_time') {
                $query = $query->where('alert_occurrence_time', 'like', '%' . $request->search . '%');
            } else if ($request->search_by == 'alarm_details') {
                $query = $query->where('alert_details', 'like', '%' . $request->search . '%');
            } else if ($request->search_by == 'confirmation_time') {
                $query = $query->where('alert_confirmation_time', 'like', '%' . $request->search . '%');
            } else if ($request->search_by == 'ward') {
                $query = $query->where('ward', 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('patient_name', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('alert_occurrence_time', '>=', $request->start_date_range)
                    ->whereDate('alert_occurrence_time', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);
        return view('hospital.emergency-alerts.index', ['datas' => $datas]);
    }

    public function show($id)
    {
        $data = EmergencyReportHospital::with(['patientHospital'])->findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        EmergencyReportHospital::whereIn('id', $ids)->delete();

        return response()->json(['status' => 'success']);
    }

    public function createContent(Request $request, $id)
    {
        $data = EmergencyReportHospital::with(['patientHospital'])->findOrFail($id);
        $data->cause_and_actions = $request->cause_and_actions;
        $data->alert_confirmation_time = now();
        $data->completed_time = now();
        $data->save();

        return response()->json(['status' => 'success']);
    }
}