<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\DetailedLocation;
use App\Models\SubLocation;
use App\Models\UserStaffHospital;
use App\Models\PatientHospital;
use App\Models\EmergencyReportHospital;
use App\Models\PatientCallReportHospital;

class HomeHospitalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Set your desired timezone
        date_default_timezone_set('Asia/Seoul');

        // Get the current date and time using Carbon
        $now = Carbon::now();

        // Format the date as 'YYYY.MM.DD'
        $date = $now->format('Y.m.d');

        // Format the time as 'A h:i' (AM/PM format with hour and minute)
        $time = $now->format('A h:i');

        $inpatientCount = PatientHospital::where('status', 'IN_PATIENT')->count();
        $inpatientCountDay = PatientHospital::where('status', 'IN_PATIENT')->whereDate('created_at', now())->count();

        $dischargeCount = PatientHospital::where('status', 'DISCHARGED')->count();
        $dischargeCountDay = PatientHospital::where('status', 'DISCHARGED')->whereDate('discharge_date', now())->count();


        return view(
            'hospital.dashboard',
            [
                'now_date' => $date,
                'now_time' => $time,
                'inpatientCount' => $inpatientCount,
                'inpatientCountDay' => $inpatientCountDay,
                'dischargeCount' => $dischargeCount,
                'dischargeCountDay' => $dischargeCountDay,
            ]
        );
    }

    public function navData()
    {
        $emergencyNeedConfirmCount = EmergencyReportHospital::with(['patientHospital'])->whereNull('alert_confirmation_time')->count();
        $emergencyCompletedConfirmCount = EmergencyReportHospital::with(['patientHospital'])->whereNotNull('alert_confirmation_time')->count();

        $patientCallNeedConfirmCount = PatientCallReportHospital::with(['patientHospital'])->whereNull('call_confirmation_time')->count();
        $patientCallCompletedConfirmCount = PatientCallReportHospital::with(['patientHospital'])->whereNotNull('call_confirmation_time')->count();


        $data = [
            'emergencyNeedConfirmCount' => $emergencyNeedConfirmCount,
            'emergencyCompletedConfirmCount' => $emergencyCompletedConfirmCount,
            'patientCallNeedConfirmCount' => $patientCallNeedConfirmCount,
            'patientCallCompletedConfirmCount' => $patientCallCompletedConfirmCount,
        ];

        return response()->json(['data' => $data]);
    }

    public function emergencyList(Request $request)
    {
        $perPage = $request->input('limit', 100);
        $emergency = EmergencyReportHospital::with(['patientHospital'])->whereNull('alert_confirmation_time')->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json(['datas' => $emergency]);
    }

    public function patientCallList(Request $request)
    {
        $perPage = $request->input('limit', 100);
        $patientCall = PatientCallReportHospital::with(['patientHospital'])->whereNull('call_confirmation_time')->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json(['datas' => $patientCall]);
    }

    public function summaryCount(Request $request)
    {
        $now = Carbon::now();
        // Format the time as 'A h:i' (AM/PM format with hour and minute)
        $time = $now->format('A h:i');

        $emergencyCountDay = EmergencyReportHospital::with(['patientHospital'])->whereNull('alert_confirmation_time')->count();
        $emergencyCount = EmergencyReportHospital::with(['patientHospital'])->whereNotNull('alert_confirmation_time')->count();

        $patientCallDay = PatientCallReportHospital::with(['patientHospital'])->whereNull('call_confirmation_time')->count();
        $patientCallCount = PatientCallReportHospital::with(['patientHospital'])->whereNotNull('call_confirmation_time')->count();

        return response()->json([
            'now_time' => $time,
            'emergencyCount' => $emergencyCount,
            'emergencyCountDay' => $emergencyCountDay,
            'patientCallCount' => $patientCallCount,
            'patientCallDay' => $patientCallDay,
        ]);
    }
}