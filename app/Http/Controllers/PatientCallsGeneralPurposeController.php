<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientCallReportGeneralPurpose;
use App\Models\UserManagementGeneralPurpose;

class PatientCallsGeneralPurposeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $users = UserManagementGeneralPurpose::get();
        $userCustomerId = $users->filter(function ($user) {
            return !is_null($user->user_customer_id);
        })->pluck('user_customer_id');

        $query = PatientCallReportGeneralPurpose::whereIn('user_customer_id', $userCustomerId)->orderBy('created_at', 'desc');
        if ($request->filled('search')) {
            $query = $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('main_symptom', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('notification_time', '>=', $request->start_date_range)
                    ->whereDate('notification_time', '<=', $request->end_date_range);
            }
        }
        $datas = $query->paginate($perPage);
        return view('general-purpose.patient-call-history.index', ['datas' => $datas]);
    }

    public function getWithUserCustomerId(Request $request, $id)
    {
        $perPage = $request->input('limit', 10);
        $query = PatientCallReportGeneralPurpose::where('user_customer_id', $id)->orderBy('created_at', 'desc');
        if ($request->filled('search')) {
            $query = $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('main_symptom', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('notification_time', '>=', $request->start_date_range)
                    ->whereDate('notification_time', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);
        return response()->json([
            'data' => $datas,
            'success' => true,
        ]);
    }

    public function getPatientCallReport(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = PatientCallReportGeneralPurpose::orderBy('created_at', 'desc');
        if ($request->filled('search')) {
            $query = $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('main_symptom', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('notification_time', '>=', $request->start_date_range)
                    ->whereDate('notification_time', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);
        return response()->json([
            'data' => $datas,
            'success' => true,
        ]);
    }

    public function show($id)
    {
        $data = PatientCallReportGeneralPurpose::with(['userCustomer'])->findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function createContent(Request $request, $id)
    {
        $data = PatientCallReportGeneralPurpose::findOrFail($id);
        $data->content = $request->cause_and_actions;
        $data->confirmation_time = now();
        $data->action_time = now();
        $data->save();

        return response()->json(['status' => 'success']);
    }
}