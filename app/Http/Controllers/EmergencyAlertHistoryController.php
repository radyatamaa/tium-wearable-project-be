<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserGeneralPurpose;
use App\Models\UserManagementGeneralPurpose;
use App\Models\EmergencyReportGeneralPurpose;

class EmergencyAlertHistoryController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $datas = EmergencyReportGeneralPurpose::orderBy('created_at', 'desc')->paginate($perPage);
        return view('public-welfare.emergency_alert_history', ['datas' => $datas]);
    }

    public function getWithUserCustomerId(Request $request, $id)
    {
        $perPage = $request->input('limit', 10);
        $datas = EmergencyReportGeneralPurpose::where('user_customer_id', $id)->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json([
            'data' => $datas,
            'success' => true,
        ]);
    }


}