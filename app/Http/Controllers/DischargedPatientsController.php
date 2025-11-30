<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\Request;
use App\Models\DetailedLocation;
use App\Models\UserStaffHospital;
use App\Models\PatientHospital;
use App\Models\MeasurementSettingCriteriaPatientHospital;
use App\Exports\PatientHospitalDischargedExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DischargedPatientsController extends Controller
{
    public function index(Request $request)
    {
        $locations = DetailedLocation::get();
        $userMedicalStaffs = UserStaffHospital::get();

        $perPage = $request->input('limit', 10);
        $query = PatientHospital::where('status', 'DISCHARGED');


        if ($request->filled('search')) {
            if ($request->search_by == 'patient_code') {
                $query = $query->where('patient_code', 'like', '%' . $request->search . '%');
            } else if ($request->search_by == 'gender') {
                $query = $query->where('gender', 'like', '%' . $request->search . '%');
            } else if ($request->search_by == 'age') {
                $query = $query->where('age', 'like', '%' . $request->search . '%');
            } else if ($request->search_by == 'ward') {
                $query = $query->where('hospital_room', 'like', '%' . $request->search . '%');
            } else if ($request->search_by == 'doctor_in_charge') {
                $query = $query->where('doctor_in_charge', 'like', '%' . $request->search . '%');
            } else if ($request->search_by == 'nurse_in_charge') {
                $query = $query->where('nurse_in_charge', 'like', '%' . $request->search . '%');
            } else if ($request->search_by == 'doctor_in_charge_or_nurse_in_charge') {
                $query = $query->whereRaw(DB::raw("(doctor_in_charge LIKE ? OR nurse_in_charge LIKE ?)"), ['%' . $request->search . '%', '%' . $request->search . '%']);

            } else {
                $query = $query->where('name', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('discharge_date', '>=', $request->start_date_range)
                    ->whereDate('discharge_date', '<=', $request->end_date_range);
            }
        }

        $datas = $query->orderBy('discharge_date', 'desc')->paginate($perPage);

        return view(
            'hospital.discharged-patients.index',
            [
                'locations' => $locations,
                'userMedicalStaffs' => $userMedicalStaffs,
                'datas' => $datas,
            ]
        );
    }


    public function show($id)
    {
        $data = PatientHospital::with(['doctorInCharge', 'nurseInCharge'])->findOrFail($id);
        return response()->json([
            'data' => $data,
            'measurementSettingPatient' => ActivityService::measurementSettingByPatientIdHospital($id),
            'defaultValuesMeasurementSetting' => ActivityService::defaultValuesMeasurementSetting(),
        ]);
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        PatientHospital::whereIn('id', $ids)->delete();

        return response()->json(['status' => 'success']);
    }

    public function setSettingMeasurement(Request $request)
    {
        $patientId = $request->input('patient_id');
        $measurementSettings = $request->except(['_token', 'patient_id']);

        $settings = MeasurementSettingCriteriaPatientHospital::updateOrCreate(
            ['patient_id' => $patientId],
            $measurementSettings
        );
        return response()->json(['success' => true, 'message' => 'save setting measurement successfully']);
    }

    public function downloadExcel(Request $request)
    {
        $auth = Auth::guard('hospital')->user();
        return Excel::download(new PatientHospitalDischargedExport($auth->user_general_purpose_id), 'discharged-report' . ' ' . now() . '.xlsx');
    }
}