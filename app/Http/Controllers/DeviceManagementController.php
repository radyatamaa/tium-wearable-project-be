<?php

namespace App\Http\Controllers;

use App\Models\DevicesSmartWatchGeneralPurpose;
use App\Models\DevicesSmartWatchPublicWelfare;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use App\Models\DevicesHospital;
use App\Models\DetailedLocation;
use App\Models\SubLocation;
use App\Models\TopLocation;
use App\Models\PatientHospital;
use App\Exports\DeviceManagementExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\UserGeneralPurposeScope;

class DeviceManagementController extends Controller
{
    public function downloadExcel(Request $request)
    {
        return Excel::download(new DeviceManagementExport, 'device-hospital-report' . ' ' . now() . '.xlsx');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = DevicesHospital::with(['detailedLocation', 'patientHospital']);


        if ($request->filled('search')) {
            if ($request->search_by == 'device_code') {
                $query = $query->where('device_code', 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('device_code', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('date_registration', '>=', $request->start_date_range)
                    ->whereDate('date_registration', '<=', $request->end_date_range);
            }
        }

        $datas = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $locations = DetailedLocation::get();
        return view('hospital.device-management.index', ['datas' => $datas, 'locations' => $locations]);
    }

    public function store(Request $request)
    {
        $device = DevicesHospital::where(DB::raw('LOWER(device_code)'), strtolower($request->device_code))->first();
        if ($device) {
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '장치 코드가 이미 존재합니다' : 'Device code already exists', 'data' => null]);
        }
        $checkDeviceInOtherSide = DeviceService::checkDeviceInOtherSide($request->device_code);
        if (!$checkDeviceInOtherSide['success']) {
            return response()->json($checkDeviceInOtherSide);
        }

        $detailedLocation = DetailedLocation::where('id', $request->location_id)->with(['subLocation'])->first();
        $subLocation = null;
        if ($detailedLocation) {
            $subLocation = SubLocation::where('id', $detailedLocation->subLocation->id)->with(['topLocation'])->first();
        }


        $data = DevicesHospital::create([
            'user_name' => '',
            'device_code' => $request->device_code,
            'usage_status' => false,
            'date_registration' => $request->registration_date,
            'detailed_location_id' => $detailedLocation->id,
            // 'sub_location_id' => $subLocation ? $subLocation->id : null,
            // 'top_location_id' => $subLocation ? $subLocation->topLocation->id : null,
            'patient_id' => null
        ]);

        return response()->json(['success' => true, 'message' => 'Device registered successfully']);
    }


    public function show($id)
    {
        $data = DevicesHospital::with(['patientHospital', 'subLocation', 'topLocation', 'detailedLocation'])->findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function destroy(Request $request)
    {
        $devicesUsed = [];
        foreach ($request->ids as $key => $id) {
            $check = DevicesHospital::where('id', $id)->first();
            if ($check->usage_status) {
                array_push($devicesUsed, $check->device_code);
            }
        }

        if (count($devicesUsed) > 0) {
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '장치 코드 ' . implode(', ', $devicesUsed) . '를 삭제할 수 없습니다. 해당 장치가 환자를 사용하고 있기 때문입니다.' : 'cant delete device code ' . implode(', ', $devicesUsed) . ' cause the device its being using the patient', 'data' => null]);
        }
        $ids = $request->ids;
        DevicesHospital::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'success']);
    }
}