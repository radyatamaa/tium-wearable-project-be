<?php

namespace App\Http\Controllers;

use App\Exports\PatientDataVitalHospitalExport;
use App\Models\UserCustomer;
use App\Services\ActivityService;
use App\Services\ElasticSearchService;
use Illuminate\Http\Request;
use App\Models\DetailedLocation;
use App\Models\SubLocation;
use App\Models\TopLocation;
use App\Models\UserStaffHospital;
use App\Models\PatientHospital;
use App\Models\MeasurementSettingCriteriaPatientHospital;
use App\Models\DevicesHospital;
use Carbon\Carbon;
use App\Models\EmergencyReportHospital;
use App\Models\PatientCallReportHospital;
use App\Exports\PatientHospitalExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;

class InpatientsController extends Controller
{
    protected $elasticsearch;
    protected $notificationService;

    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticsearch = $elasticSearchService;
    }
    public function index(Request $request)
    {
        $toplocations = TopLocation::get();
        $sublocations = SubLocation::get();
        $locations = DetailedLocation::get();


        $doctors = UserStaffHospital::where('position', '의사')->get();
        $nurses = UserStaffHospital::where('position', '간호사')->get();

        $perPage = $request->input('limit', 10);
        $query = PatientHospital::where('status', 'IN_PATIENT');


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
                $query = $query->whereDate('created_at', '>=', $request->start_date_range)
                    ->whereDate('created_at', '<=', $request->end_date_range);
            }
        }

        $datas = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return view(
            'hospital.inpatients.index',
            [
                'toplocations' => $toplocations,
                'sublocations' => $sublocations,
                'locations' => $locations,
                'doctors' => $doctors,
                'nurses' => $nurses,
                'datas' => $datas,
                'devices' => [],
            ]
        );
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $device = DevicesHospital::where('device_code', $request->device_code)->first();
            if (!$device) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '디바이스 코드가 환자에서 발견되지 않았습니다' : 'Device code not found in the patient', 'data' => null]);
            }

            if ($device->usage_status == true && $device->patient_id != $id) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '장치가 다른 환자에게 사용되었습니다' : 'The device has been used on other patients', 'data' => null]);
            }

            $patient = PatientHospital::where('id', $id)->first();

            $locationId = $request->detailed_location_id;
            $subLocationId = $request->sub_location_id;
            $topLocationId = $request->top_location_id;

            $doctor = UserStaffHospital::where('id', $request->doctor_in_charge_id)->first();
            $nurse = UserStaffHospital::where('id', $request->nurse_in_charge_id)->first();


            $userCustomerId = $patient->user_customer_id;
            $userCustomer = UserCustomer::where('id', $userCustomerId)->first();
            if ($userCustomer && $userCustomerId) {
                $check = UserCustomer::where('email', $request->user_id)->first();
                if ($check) {
                    if ($check->id != $userCustomer->id)
                        return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다' : 'ID already exists', 'data' => null]);
                }
                $userCustomer->name = $request->name;
                $userCustomer->email = $request->user_id;
                if ($request->password && $request->password != '**********') {
                    $userCustomer->password = Hash::make($request->password);
                }
                $userCustomer->gender = $request->gender;
                $userCustomer->birth_date = $request->date_of_birth;
                $userCustomer->height = $request->height_cm;
                $userCustomer->weight = $request->weight_kg;
                $userCustomer->phone_number = $request->contact_information;
                $userCustomer->doctor_name = $doctor->name;
                $userCustomer->room_number = $request->hospital_room;
                $userCustomer->save();
            } elseif ($request->user_id && !$userCustomerId) {
                $userCustomer = UserCustomer::where('email', $request->user_id)->first();
                if ($userCustomer) {
                    return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다' : 'ID already exists', 'data' => null]);
                }

                $userCustomer = UserCustomer::create([
                    'name' => $request->name,
                    'email' => $request->user_id,
                    'password' => Hash::make($request->password),
                    'gender' => $request->gender,
                    'birth_date' => $request->date_of_birth,
                    'height' => $request->height_cm,
                    'weight' => $request->weight_kg,
                    'phone_number' => $request->contact_information,
                    'doctor_name' => $doctor->name,
                    'room_number' => $request->hospital_room,
                ]);

                $userCustomerId = $userCustomer->id;
            }



            if ($patient->device_code != $request->device_code) {
                $oldDevice = DevicesHospital::where('device_code', $patient->device_code)->first();
                if ($oldDevice) {
                    $oldDevice->user_name = '';
                    $oldDevice->patient_id = null;
                    $oldDevice->usage_status = false;
                    $oldDevice->save();
                }
            }
            $patient->patient_code = $request->patient_code;
            $patient->name = $request->name;
            $patient->date_of_birth = $request->date_of_birth;
            $patient->age = $request->age;
            $patient->height_cm = $request->height_cm;
            $patient->weight_kg = $request->weight_kg;
            $patient->doctor_in_charge = $doctor->name;
            $patient->nurse_in_charge = $nurse->name;
            $patient->diagnostic_name = $request->diagnostic_name;
            $patient->device_code = $request->device_code;
            $patient->contact_information = $request->contact_information;
            $patient->emergency_contact_1 = $request->emergency_contact_1;
            $patient->emergency_contact_2 = $request->emergency_contact_2;
            $patient->detailed_location_id = $locationId;
            $patient->sub_location_id = $subLocationId;
            $patient->top_location_id = $topLocationId;
            $patient->doctor_in_charge_id = $request->doctor_in_charge_id;
            $patient->nurse_in_charge_id = $request->nurse_in_charge_id;
            $patient->hospital_room = $request->hospital_room;
            $patient->gender = $request->gender;
            $patient->user_customer_id = $userCustomerId;
            $patient->save();

            $device->usage_status = true;
            $device->user_name = $request->name;
            $device->patient_id = $patient->id;
            $device->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => config('app.lang') != 'en' ? '환자 정보가 성공적으로 업데이트되었습니다' : 'Patient information updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '실패한' : 'failed', 'error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $device = DevicesHospital::where('device_code', $request->device_code)->first();
            if (!$device) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '디바이스 코드가 환자에서 발견되지 않았습니다' : 'Device code not found in the patient', 'data' => null]);
            }

            if ($device->usage_status == true) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '장치가 다른 환자에게 사용되었습니다' : 'The device has been used on other patients', 'data' => null]);
            }

            $locationId = $request->detailed_location_id;
            $subLocationId = $request->sub_location_id;
            $topLocationId = $request->top_location_id;

            $doctor = UserStaffHospital::where('id', $request->doctor_in_charge_id)->first();
            $nurse = UserStaffHospital::where('id', $request->nurse_in_charge_id)->first();

            $userCustomerId = null;
            if ($request->user_id) {
                $userCustomer = UserCustomer::where('email', $request->user_id)->first();
                if ($userCustomer) {
                    return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다' : 'ID already exists', 'data' => null]);
                }

                $userCustomer = UserCustomer::create([
                    'name' => $request->name,
                    'email' => $request->user_id,
                    'password' => Hash::make($request->password),
                    'gender' => $request->gender,
                    'birth_date' => $request->date_of_birth,
                    'height' => $request->height_cm,
                    'weight' => $request->weight_kg,
                    'phone_number' => $request->contact_information,
                    'doctor_name' => $doctor->name,
                    'room_number' => $request->hospital_room,
                ]);

                $userCustomerId = $userCustomer->id;
            }

            $patientHospital = PatientHospital::create([
                'patient_code' => $request->patient_code,
                'name' => $request->name,
                'date_of_birth' => $request->date_of_birth,
                'age' => $request->age,
                'height_cm' => $request->height_cm,
                'weight_kg' => $request->weight_kg,
                'doctor_in_charge' => $doctor->name,
                'nurse_in_charge' => $nurse->name,
                'diagnostic_name' => $request->diagnostic_name,
                'device_code' => $request->device_code,
                'contact_information' => $request->contact_information,
                'emergency_contact_1' => $request->emergency_contact_1,
                'emergency_contact_2' => $request->emergency_contact_2,
                'detailed_location_id' => $locationId,
                'sub_location_id' => $subLocationId,
                'top_location_id' => $topLocationId,
                'doctor_in_charge_id' => $request->doctor_in_charge_id,
                'nurse_in_charge_id' => $request->nurse_in_charge_id,
                'hospital_room' => $request->hospital_room,
                'gender' => $request->gender,
                'status' => 'IN_PATIENT',
                'user_customer_id' => $userCustomerId,
            ]);

            $device->user_name = $request->name;
            $device->patient_id = $patientHospital->id;
            $device->usage_status = true;
            $device->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => config('app.lang') != 'en' ? '환자 등록이 성공적으로 완료되었습니다' : 'Patient registration completed successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '실패한' : 'failed', 'error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $data = PatientHospital::with(['doctorInCharge', 'nurseInCharge', 'userCustomer'])->findOrFail($id);
        return response()->json([
            'data' => $data,
            'measurementSettingPatient' => ActivityService::measurementSettingByPatientIdHospital($id),
            'defaultValuesMeasurementSetting' => ActivityService::defaultValuesMeasurementSetting(),
        ]);
    }

    public function emergencyHistoryPatient(Request $request, $id)
    {
        $perPage = $request->input('limit', 10);
        $datas = EmergencyReportHospital::where('patient_id', $id)->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json(['success' => true, 'datas' => $datas]);
    }

    public function patientCallHistoryPatient(Request $request, $id)
    {
        $perPage = $request->input('limit', 10);
        $datas = PatientCallReportHospital::where('patient_id', $id)->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json(['success' => true, 'datas' => $datas]);
    }


    public function detailEmergencyHistoryPatient(Request $request, $id)
    {
        $datas = EmergencyReportHospital::where('id', $id)->first();

        return response()->json(['success' => true, 'item' => $datas]);
    }

    public function detailPatientCallHistoryPatient(Request $request, $id)
    {
        $datas = PatientCallReportHospital::where('id', $id)->first();

        return response()->json(['success' => true, 'item' => $datas]);
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->ids;

            foreach ($ids as $id) {
                $oldDevice = DevicesHospital::where('patient_id', $id)->first();
                if ($oldDevice) {
                    $oldDevice->user_name = '';
                    $oldDevice->patient_id = null;
                    $oldDevice->usage_status = false;
                    $oldDevice->save();
                }
            }

            PatientHospital::whereIn('id', $ids)->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function dischargePatient(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->ids;

            foreach ($ids as $id) {
                $oldDevice = DevicesHospital::where('patient_id', $id)->first();
                if ($oldDevice) {
                    $oldDevice->user_name = '';
                    $oldDevice->patient_id = null;
                    $oldDevice->usage_status = false;
                    $oldDevice->save();
                }

                $patient = PatientHospital::where('id', $id)->first();
                if ($patient) {
                    $patient->status = 'DISCHARGED';
                    $patient->discharge_date = now();
                    $patient->save();
                }
            }

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function returnInPatient(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->ids;


            foreach ($ids as $id) {
                $patient = PatientHospital::where('id', $id)->first();
                if ($patient) {
                    $patient->status = 'IN_PATIENT';
                    $patient->discharge_date = null;
                    $patient->save();
                }

                $oldDevice = DevicesHospital::
                    where('device_code', $patient->device_code)
                    ->where('usage_status', false)
                    ->first();
                if ($oldDevice) {
                    $oldDevice->user_name = $patient->name;
                    $oldDevice->patient_id = $patient->id;
                    $oldDevice->usage_status = true;
                    $oldDevice->save();
                } else {
                    DB::rollBack();
                    return response()->json(['status' => 'failed', 'message' => '해당 장치를 찾을 수 없거나 이미 다른 환자에게 사용되었기 때문에 환자는 반환될 수 없었습니다.']);
                }

            }

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
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

    // old code 
    // public function getChartData(Request $request, $id)
    // {
    //     try {
    //         $now = Carbon::now()->endOfDay()->toIso8601String();
    //         $yesterday = Carbon::now()->startOfDay()->toIso8601String();

    //         $activityIndices = [
    //             'blood_pressure' => 'blood_pressure_activity_index',
    //             'heart_rate' => 'heart_rate_activity_index',
    //             'oxygen_saturation' => 'oxygen_saturation_activity_index',
    //             'body_temperature' => 'body_temperature_activity_index',
    //             'respiratory_rate' => 'respiratory_rate_activity_index',
    //             'ecg' => 'ecg_activity_index',
    //         ];

    //         $chartData = [];
    //         $timeIntervals = [];

    //         $activity_type = $request->input('activity_type', '');
    //         if ($activity_type) {
    //             $activityIndices = array(
    //                 $activity_type => $activityIndices[$activity_type]
    //             );
    //         }

    //         $chartType = $request->input('type', 'hourly');

    //         if ($chartType == 'ten_minutes') {
    //             for ($i = 0; $i < 24 * 6; $i++) {
    //                 $timeIntervals[] = Carbon::now()->subMinutes(10 * $i)->format('H:i');
    //             }
    //             $timeIntervals = array_reverse($timeIntervals);
    //         } else {
    //             $start = new DateTime('00:00');
    //             $end = new DateTime('23:00');
    //             while ($start <= $end) {
    //                 $timeIntervals[] = $start->format('H:i');
    //                 $start->modify('+1 hour');
    //             }
    //         }

    //         foreach ($activityIndices as $type => $index) {
    //             $mustFilters = [
    //                 ['term' => ['patient_id' => $id]],
    //                 ['range' => ['recorded_at' => ['gte' => $yesterday, 'lte' => $now]]]
    //             ];

    //             switch ($type) {
    //                 case 'heart_rate':
    //                 case 'ecg':
    //                     $mustFilters[] = ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]];
    //                     break;
    //                 case 'body_temperature':
    //                     $mustFilters[] = ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]];
    //                     break;
    //                 case 'blood_pressure':
    //                     $mustFilters[] = ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]];
    //                     $mustFilters[] = ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]];
    //                     break;
    //                 case 'oxygen_saturation':
    //                     $mustFilters[] = ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]];
    //                     break;
    //                 case 'respiratory_rate':
    //                     $mustFilters[] = ['range' => ['rate' => ['gte' => 6, 'lte' => 40]]];
    //                     break;
    //             }

    //             $query = [
    //                 'index' => $index,
    //                 'body' => [
    //                     'query' => [
    //                         'bool' => [
    //                             'must' => $mustFilters,
    //                         ],
    //                     ],
    //                     'size' => 10000,
    //                     'sort' => [['recorded_at' => ['order' => 'asc']]],
    //                 ],
    //             ];

    //             try {
    //                 $response = $this->elasticsearch->client->search($query);
    //                 $hits = $response['hits']['hits'] ?? [];
    //             } catch (Exception $e) {
    //                 $hits = [];
    //             }

    //             $data = collect($hits)->map(fn($hit) => $hit['_source'])
    //                 ->groupBy(fn($item) => Carbon::parse($item['recorded_at'])->format('H:00'));

    //             $aggregatedData = [];

    //             foreach ($timeIntervals as $interval) {
    //                 $group = $data->get($interval, collect())->filter(fn($item) => match ($type) {
    //                     'blood_pressure' => isset($item['systolic'], $item['diastolic']) && $item['systolic'] > 0 && $item['diastolic'] > 0,
    //                     'heart_rate', 'ecg' => isset($item['bpm']) && $item['bpm'] > 0,
    //                     'oxygen_saturation' => isset($item['saturation']) && $item['saturation'] > 0,
    //                     'body_temperature' => isset($item['temperature']) && $item['temperature'] > 0,
    //                     'respiratory_rate' => isset($item['rate']) && $item['rate'] > 0,
    //                     default => false,
    //                 });

    //                 if ($group->isNotEmpty()) {
    //                     switch ($type) {
    //                         case 'blood_pressure':
    //                             $systolicAvg = round($group->avg('systolic'));
    //                             $diastolicAvg = round($group->avg('diastolic'));
    //                             $systolicMax = $group->max('systolic');
    //                             $diastolicMax = $group->max('diastolic');
    //                             $systolicMin = $group->min('systolic');
    //                             $diastolicMin = $group->min('diastolic');

    //                             $systolicState = ActivityService::determineStateHospital($id, $systolicAvg, 'blood_pressure_systolic');
    //                             $diastolicState = ActivityService::determineStateHospital($id, $diastolicAvg, 'blood_pressure_diastolic');
    //                             $state = ActivityService::determineStateHospital($id, $systolicAvg, 'blood_pressure', $diastolicAvg);

    //                             $aggregatedData[$interval] = [
    //                                 'systolic' => $systolicAvg,
    //                                 'diastolic' => $diastolicAvg,
    //                                 'avg' => "$systolicAvg/$diastolicAvg",
    //                                 'highest' => "$systolicMax/$diastolicMax",
    //                                 'lowest' => "$systolicMin/$diastolicMin",
    //                                 'highest_systolic' => $systolicMax,
    //                                 'lowest_systolic' => $systolicMin,
    //                                 'highest_diastolic' => $diastolicMax,
    //                                 'lowest_diastolic' => $diastolicMin,
    //                                 'state' => $state,
    //                                 'state_systolic' => $systolicState,
    //                                 'state_diastolic' => $diastolicState,
    //                                 'color_state_systolic' => ActivityService::detemineColorOfStateHospital($systolicState),
    //                                 'color_state_diastolic' => ActivityService::detemineColorOfStateHospital($diastolicState),
    //                             ];
    //                             break;
    //                         case 'heart_rate':
    //                         case 'ecg':
    //                             $bpmAvg = round($group->avg('bpm'));
    //                             $highest = $group->max('bpm');
    //                             $lowest = $group->min('bpm');
    //                             $state = ActivityService::determineStateHospital($id, $bpmAvg, $type);

    //                             $aggregatedData[$interval] = [
    //                                 'bpm' => $bpmAvg,
    //                                 'avg' => $bpmAvg,
    //                                 'highest' => $highest,
    //                                 'lowest' => $lowest,
    //                                 'state' => $state,
    //                                 'color_state' => ActivityService::detemineColorOfStateHospital($state),
    //                             ];
    //                             break;
    //                         case 'oxygen_saturation':
    //                             $saturationAvg = round($group->avg('saturation'));
    //                             $highest = $group->max('saturation');
    //                             $lowest = $group->min('saturation');
    //                             $state = ActivityService::determineStateHospital($id, $saturationAvg, $type);

    //                             $aggregatedData[$interval] = [
    //                                 'saturation' => $saturationAvg,
    //                                 'avg' => $saturationAvg,
    //                                 'highest' => $highest,
    //                                 'lowest' => $lowest,
    //                                 'state' => $state,
    //                                 'color_state' => ActivityService::detemineColorOfStateHospital($state),
    //                             ];
    //                             break;
    //                         case 'body_temperature':
    //                             $temperatureAvg = round($group->avg('temperature'));
    //                             $highest = $group->max('temperature');
    //                             $lowest = $group->min('temperature');
    //                             $state = ActivityService::determineStateHospital($id, $temperatureAvg, $type);

    //                             $aggregatedData[$interval] = [
    //                                 'temperature' => $temperatureAvg,
    //                                 'avg' => $temperatureAvg,
    //                                 'highest' => $highest,
    //                                 'lowest' => $lowest,
    //                                 'state' => $state,
    //                                 'color_state' => ActivityService::detemineColorOfStateHospital($state),
    //                             ];
    //                             break;
    //                         case 'respiratory_rate':
    //                             $rateAvg = round($group->avg('rate'));
    //                             $highest = $group->max('rate');
    //                             $lowest = $group->min('rate');
    //                             $state = ActivityService::determineStateHospital($id, $rateAvg, 'respiratory');

    //                             $aggregatedData[$interval] = [
    //                                 'rate' => $rateAvg,
    //                                 'avg' => $rateAvg,
    //                                 'highest' => $highest,
    //                                 'lowest' => $lowest,
    //                                 'state' => $state,
    //                                 'color_state' => ActivityService::detemineColorOfStateHospital($state),
    //                             ];
    //                             break;
    //                     }
    //                 } else {
    //                     $aggregatedData[$interval] = match ($type) {
    //                         'blood_pressure' => [
    //                             'systolic' => 50,
    //                             'diastolic' => 50,
    //                             'avg' => '0/0',
    //                             'highest' => '0/0',
    //                             'lowest' => '0/0',
    //                             'highest_systolic' => 50,
    //                             'lowest_systolic' => 50,
    //                             'highest_diastolic' => 50,
    //                             'lowest_diastolic' => 50,
    //                             'state' => '',
    //                             'state_systolic' => '',
    //                             'state_diastolic' => '',
    //                             'color_state_systolic' => '#3CBAFF',
    //                             'color_state_diastolic' => '#3CBAFF',
    //                         ],
    //                         default => null,
    //                     };
    //                 }
    //             }

    //             $chartData[$type] = $aggregatedData;

    //             // Calculate overall daily average
    //             switch ($type) {
    //                 case 'blood_pressure':
    //                     $systolicAvg = collect($hits)->avg('_source.systolic');
    //                     $diastolicAvg = collect($hits)->avg('_source.diastolic');
    //                     if ($systolicAvg > 0 && $diastolicAvg > 0) {
    //                         $chartData[$type]['avg_daily'] = "$systolicAvg/$diastolicAvg";
    //                     }
    //                     break;
    //                 case 'heart_rate':
    //                 case 'ecg':
    //                     $chartData[$type]['avg_daily'] = collect($hits)->avg('_source.bpm') ?: null;
    //                     break;
    //                 case 'oxygen_saturation':
    //                     $chartData[$type]['avg_daily'] = collect($hits)->avg('_source.saturation') ?: null;
    //                     break;
    //                 case 'body_temperature':
    //                     $chartData[$type]['avg_daily'] = collect($hits)->avg('_source.temperature') ?: null;
    //                     break;
    //                 case 'respiratory_rate':
    //                     $chartData[$type]['avg_daily'] = collect($hits)->avg('_source.rate') ?: null;
    //                     break;
    //             }
    //         }

    //         return response()->json($chartData);
    //     } catch (\Exception $e) {
    //         return;
    //     }

    // }

    public function getChartData(Request $request, $id)
    {
        try {
            // Tetapkan rentang tanggal (default: hari ini)
            $startDate = $request->query('start_date_range')
                ? Carbon::parse($request->query('start_date_range'))->startOfDay()->toIso8601String()
                : Carbon::now()->startOfDay()->toIso8601String();

            $endDate = $request->query('end_date_range')
                ? Carbon::parse($request->query('end_date_range'))->endOfDay()->toIso8601String()
                : Carbon::now()->endOfDay()->toIso8601String();

            $chartData = [];

            $activityIndices = [
                'blood_pressure' => 'blood_pressure_activity_index',
                'heart_rate' => 'heart_rate_activity_index',
                'oxygen_saturation' => 'oxygen_saturation_activity_index',
                'body_temperature' => 'body_temperature_activity_index',
                'respiratory_rate' => 'respiratory_rate_activity_index',
                'ecg' => 'ecg_activity_index',
            ];

            // Filter berdasarkan activity_type jika disediakan
            $activity_type = $request->input('activity_type', '');
            if ($activity_type) {
                $activityIndices = [
                    $activity_type => $activityIndices[$activity_type]
                ];
            }

            // Tentukan interval aggregasi berdasarkan parameter 'type'
            if ($request->input('type', 'hourly') == 'ten_minutes') {
                $calendarInterval = '10m';
                $format = 'HH:mm';
            } else {
                $calendarInterval = 'hour';
                $format = 'HH:00';
            }

            // Gunakan extended_bounds dalam bentuk epoch millisecond
            $extendedMin = Carbon::parse($startDate)->timestamp * 1000;
            $extendedMax = Carbon::parse($endDate)->timestamp * 1000;

            foreach ($activityIndices as $type => $index) {
                $mustFilters = [
                    ['term' => ['patient_id' => $id]],
                    ['range' => ['recorded_at' => ['gte' => $startDate, 'lte' => $endDate]]]
                ];

                // Filter tambahan berdasarkan tipe activity
                switch ($type) {
                    case 'heart_rate':
                    case 'ecg':
                        $mustFilters[] = ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]];
                        break;
                    case 'body_temperature':
                        $mustFilters[] = ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]];
                        break;
                    case 'blood_pressure':
                        $mustFilters[] = ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]];
                        $mustFilters[] = ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]];
                        break;
                    case 'oxygen_saturation':
                        $mustFilters[] = ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]];
                        break;
                    case 'respiratory_rate':
                        $mustFilters[] = ['range' => ['rate' => ['gte' => 6, 'lte' => 40]]];
                        break;
                }

                // Susun aggregations untuk grouping berdasarkan waktu dengan extended_bounds
                if ($type === 'blood_pressure') {
                    $aggs = [
                        'group_by_time' => [
                            'date_histogram' => [
                                'field' => 'recorded_at',
                                'calendar_interval' => $calendarInterval,
                                'format' => $format,
                                'order' => ['_key' => 'asc'],
                                'min_doc_count' => 0,
                                'extended_bounds' => [
                                    'min' => $extendedMin,
                                    'max' => $extendedMax,
                                ],
                                'time_zone' => 'Asia/Seoul'
                            ],
                            'aggs' => [
                                'avg_systolic' => ['avg' => ['field' => 'systolic']],
                                'max_systolic' => ['max' => ['field' => 'systolic']],
                                'min_systolic' => ['min' => ['field' => 'systolic']],
                                'avg_diastolic' => ['avg' => ['field' => 'diastolic']],
                                'max_diastolic' => ['max' => ['field' => 'diastolic']],
                                'min_diastolic' => ['min' => ['field' => 'diastolic']],
                            ]
                        ],
                        // Overall aggregasi sebagai sibling
                        'avg_systolic_overall' => ['avg' => ['field' => 'systolic']],
                        'avg_diastolic_overall' => ['avg' => ['field' => 'diastolic']]
                    ];
                } else {
                    $field = match ($type) {
                        'heart_rate', 'ecg' => 'bpm',
                        'oxygen_saturation' => 'saturation',
                        'body_temperature' => 'temperature',
                        'respiratory_rate' => 'rate',
                        default => 'value'
                    };

                    $aggs = [
                        'group_by_time' => [
                            'date_histogram' => [
                                'field' => 'recorded_at',
                                'calendar_interval' => $calendarInterval,
                                'format' => $format,
                                'order' => ['_key' => 'asc'],
                                'min_doc_count' => 0,
                                'extended_bounds' => [
                                    'min' => $extendedMin,
                                    'max' => $extendedMax,
                                ],
                                'time_zone' => 'Asia/Seoul'
                            ],
                            'aggs' => [
                                'avg_value' => ['avg' => ['field' => $field]],
                                'max_value' => ['max' => ['field' => $field]],
                                'min_value' => ['min' => ['field' => $field]],
                            ]
                        ],
                        'avg_value_overall' => ['avg' => ['field' => $field]]
                    ];
                }

                $query = [
                    'index' => $index,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $mustFilters
                            ]
                        ],
                        'aggs' => $aggs,
                        'size' => 0 // Tidak mengambil raw hits, hanya aggregations
                    ]
                ];

                try {
                    $response = $this->elasticsearch->client->search($query);
                    $buckets = $response['aggregations']['group_by_time']['buckets'] ?? [];
                } catch (Exception $e) {
                    $buckets = [];
                    if (!isset($response['aggregations'])) {
                        $response['aggregations'] = [];
                    }
                }

                $aggregatedData = [];

                // Proses setiap bucket hasil aggregasi
                foreach ($buckets as $bucket) {
                    $interval = $bucket['key_as_string'];

                    // Jika bucket kosong, assign nilai default
                    if ($bucket['doc_count'] == 0) {
                        $aggregatedData[$interval] = match ($type) {
                            'blood_pressure' => [
                                'systolic' => 50,
                                'diastolic' => 50,
                                'avg' => '0/0',
                                'highest' => '0/0',
                                'lowest' => '0/0',
                                'highest_systolic' => 50,
                                'lowest_systolic' => 50,
                                'highest_diastolic' => 50,
                                'lowest_diastolic' => 50,
                                'state' => '',
                                'state_systolic' => '',
                                'state_diastolic' => '',
                                'color_state_systolic' => '#3CBAFF',
                                'color_state_diastolic' => '#3CBAFF',
                            ],
                            default => null,
                        };
                        continue;
                    }

                    if ($type === 'blood_pressure') {
                        $systolicAvg = round($bucket['avg_systolic']['value'] ?? 0);
                        $diastolicAvg = round($bucket['avg_diastolic']['value'] ?? 0);
                        $systolicMax = round($bucket['max_systolic']['value'] ?? 0);
                        $diastolicMax = round($bucket['max_diastolic']['value'] ?? 0);
                        $systolicMin = round($bucket['min_systolic']['value'] ?? 0);
                        $diastolicMin = round($bucket['min_diastolic']['value'] ?? 0);

                        $systolicState = ActivityService::determineStateHospital($id, $systolicAvg, 'blood_pressure_systolic');
                        $diastolicState = ActivityService::determineStateHospital($id, $diastolicAvg, 'blood_pressure_diastolic');
                        $state = ActivityService::determineStateHospital($id, $systolicAvg, 'blood_pressure', $diastolicAvg);

                        $aggregatedData[$interval] = [
                            'systolic' => $systolicAvg,
                            'diastolic' => $diastolicAvg,
                            'avg' => "$systolicAvg/$diastolicAvg",
                            'highest' => "$systolicMax/$diastolicMax",
                            'lowest' => "$systolicMin/$diastolicMin",
                            'highest_systolic' => $systolicMax,
                            'lowest_systolic' => $systolicMin,
                            'highest_diastolic' => $diastolicMax,
                            'lowest_diastolic' => $diastolicMin,
                            'state' => $state,
                            'state_systolic' => $systolicState,
                            'state_diastolic' => $diastolicState,
                            'color_state_systolic' => ActivityService::detemineColorOfStateHospital($systolicState),
                            'color_state_diastolic' => ActivityService::detemineColorOfStateHospital($diastolicState),
                        ];
                    } else {
                        $avgValue = round($bucket['avg_value']['value'] ?? 0);
                        $highestValue = round($bucket['max_value']['value'] ?? 0);
                        $lowestValue = round($bucket['min_value']['value'] ?? 0);
                        $determineType = ($type === 'respiratory_rate') ? 'respiratory' : $type;
                        $state = ActivityService::determineStateHospital($id, $avgValue, $determineType);
                        $colorState = ActivityService::detemineColorOfStateHospital($state);

                        if ($type === 'heart_rate' || $type === 'ecg') {
                            $aggregatedData[$interval] = [
                                'bpm' => $avgValue,
                                'avg' => $avgValue,
                                'highest' => $highestValue,
                                'lowest' => $lowestValue,
                                'state' => $state,
                                'color_state' => $colorState,
                            ];
                        } elseif ($type === 'oxygen_saturation') {
                            $aggregatedData[$interval] = [
                                'saturation' => $avgValue,
                                'avg' => $avgValue,
                                'highest' => $highestValue,
                                'lowest' => $lowestValue,
                                'state' => $state,
                                'color_state' => $colorState,
                            ];
                        } elseif ($type === 'body_temperature') {
                            $aggregatedData[$interval] = [
                                'temperature' => $avgValue,
                                'avg' => $avgValue,
                                'highest' => $highestValue,
                                'lowest' => $lowestValue,
                                'state' => $state,
                                'color_state' => $colorState,
                            ];
                        } elseif ($type === 'respiratory_rate') {
                            $aggregatedData[$interval] = [
                                'rate' => $avgValue,
                                'avg' => $avgValue,
                                'highest' => $highestValue,
                                'lowest' => $lowestValue,
                                'state' => $state,
                                'color_state' => $colorState,
                            ];
                        } else {
                            $aggregatedData[$interval] = [
                                'avg' => $avgValue,
                                'highest' => $highestValue,
                                'lowest' => $lowestValue,
                                'state' => $state,
                                'color_state' => $colorState,
                            ];
                        }
                    }
                }

                $chartData[$type] = $aggregatedData;

                // Hitung overall daily average dari aggregasi overall
                if ($type === 'blood_pressure') {
                    $systolicOverall = round($response['aggregations']['avg_systolic_overall']['value'] ?? 0);
                    $diastolicOverall = round($response['aggregations']['avg_diastolic_overall']['value'] ?? 0);
                    if ($systolicOverall > 0 && $diastolicOverall > 0) {
                        $chartData[$type]['avg_daily'] = "$systolicOverall/$diastolicOverall";
                    }
                } else {
                    $avgOverall = round($response['aggregations']['avg_value_overall']['value'] ?? 0);
                    $chartData[$type]['avg_daily'] = $avgOverall ?: null;
                }
            }

            return response()->json($chartData);
        } catch (\Exception $e) {
            return;
        }
    }

    public function getChartDataTable(Request $request, $id)
    {
        try {
            $startDate = $request->query('start_date_range')
                ? Carbon::parse($request->query('start_date_range'))->startOfDay()->toIso8601String()
                : Carbon::now()->startOfDay()->toIso8601String();

            $endDate = $request->query('end_date_range')
                ? Carbon::parse($request->query('end_date_range'))->endOfDay()->toIso8601String()
                : Carbon::now()->endOfDay()->toIso8601String();

            // Pagination parameters
            $page = $request->query('page', 1); // Default halaman pertama
            $size = $request->query('size', 10); // Default 10 item per halaman
            $from = ($page - 1) * $size;

            $chartData = [];

            $activityIndices = [
                'blood_pressure' => 'blood_pressure_activity_index',
                'heart_rate' => 'heart_rate_activity_index',
                'oxygen_saturation' => 'oxygen_saturation_activity_index',
                'body_temperature' => 'body_temperature_activity_index',
                'respiratory_rate' => 'respiratory_rate_activity_index',
                'ecg' => 'ecg_activity_index',
            ];

            $activity_type = $request->input('activity_type', '');
            if ($activity_type) {
                $activityIndices = array(
                    $activity_type => $activityIndices[$activity_type]
                );
            }

            foreach ($activityIndices as $type => $index) {
                $mustFilters = [
                    ['term' => ['patient_id' => $id]],
                    ['range' => ['recorded_at' => ['gte' => $startDate, 'lte' => $endDate]]]
                ];

                switch ($type) {
                    case 'heart_rate':
                    case 'ecg':
                        $mustFilters[] = ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]];
                        break;
                    case 'body_temperature':
                        $mustFilters[] = ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]];
                        break;
                    case 'blood_pressure':
                        $mustFilters[] = ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]];
                        $mustFilters[] = ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]];
                        break;
                    case 'oxygen_saturation':
                        $mustFilters[] = ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]];
                        break;
                    case 'respiratory_rate':
                        $mustFilters[] = ['range' => ['rate' => ['gte' => 6, 'lte' => 40]]];
                        break;
                }

                // **Updated Elasticsearch Query for Grouping**
                $query = [
                    'index' => $index,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $mustFilters,
                            ],
                        ],
                        'aggs' => [
                            'group_by_time' => [
                                'date_histogram' => [
                                    'field' => 'recorded_at',
                                    'calendar_interval' => 'minute',  // Default grouping by minute
                                    'format' => 'HH:mm',  // Default format (if no start_date_range)
                                    'order' => ['_key' => 'desc'],
                                    'min_doc_count' => 1,
                                    'time_zone' => 'Asia/Seoul'
                                ],
                                'aggs' => [
                                    'avg_systolic' => ['avg' => ['field' => 'systolic']],
                                    'max_systolic' => ['max' => ['field' => 'systolic']],
                                    'min_systolic' => ['min' => ['field' => 'systolic']],
                                    'avg_diastolic' => ['avg' => ['field' => 'diastolic']],
                                    'max_diastolic' => ['max' => ['field' => 'diastolic']],
                                    'min_diastolic' => ['min' => ['field' => 'diastolic']],
                                    'avg_value' => [
                                        'avg' => [
                                            'field' => match ($type) {
                                                'heart_rate', 'ecg' => 'bpm',
                                                'oxygen_saturation' => 'saturation',
                                                'body_temperature' => 'temperature',
                                                'respiratory_rate' => 'rate',
                                                default => 'value'
                                            }
                                        ]
                                    ],
                                    'max_value' => [
                                        'max' => [
                                            'field' => match ($type) {
                                                'heart_rate', 'ecg' => 'bpm',
                                                'oxygen_saturation' => 'saturation',
                                                'body_temperature' => 'temperature',
                                                'respiratory_rate' => 'rate',
                                                default => 'value'
                                            }
                                        ]
                                    ],
                                    'min_value' => [
                                        'min' => [
                                            'field' => match ($type) {
                                                'heart_rate', 'ecg' => 'bpm',
                                                'oxygen_saturation' => 'saturation',
                                                'body_temperature' => 'temperature',
                                                'respiratory_rate' => 'rate',
                                                default => 'value'
                                            }
                                        ]
                                    ],
                                ]
                            ]
                        ],
                        'size' => 0 // No raw hits, only aggregations
                    ]
                ];

                // **Modify Grouping Format Based on Request Parameters**
                if ($request->query('start_date_range') && $request->query('end_date_range')) {
                    // If there is a date range, group by hour (YYYY-MM-DD HH:00)
                    $query['body']['aggs']['group_by_time']['date_histogram']['calendar_interval'] = 'hour';
                    $query['body']['aggs']['group_by_time']['date_histogram']['format'] = 'yyyy-MM-dd HH:00'; // Hourly format
                } else {
                    // No start_date_range, default to minute grouping (HH:mm)
                    $query['body']['aggs']['group_by_time']['date_histogram']['calendar_interval'] = 'minute';
                    $query['body']['aggs']['group_by_time']['date_histogram']['format'] = 'HH:mm'; // Minute-level format
                }




                try {
                    $response = $this->elasticsearch->client->search($query);
                    $buckets = array_slice($response['aggregations']['group_by_time']['buckets'] ?? [], $from, $size);
                } catch (Exception $e) {
                    $buckets = [];
                }

                $aggregatedData = [];

                foreach ($buckets as $bucket) {
                    $interval = $bucket['key_as_string'];

                    if ($type === 'blood_pressure') {
                        $systolicAvg = round($bucket['avg_systolic']['value'] ?? 0);
                        $diastolicAvg = round($bucket['avg_diastolic']['value'] ?? 0);
                        $systolicMax = round($bucket['max_systolic']['value'] ?? 0);
                        $diastolicMax = round($bucket['max_diastolic']['value'] ?? 0);
                        $systolicMin = round($bucket['min_systolic']['value'] ?? 0);
                        $diastolicMin = round($bucket['min_diastolic']['value'] ?? 0);

                        $systolicState = ActivityService::determineStateHospital($id, $systolicAvg, 'blood_pressure_systolic');
                        $diastolicState = ActivityService::determineStateHospital($id, $diastolicAvg, 'blood_pressure_diastolic');
                        $state = ActivityService::determineStateHospital($id, $systolicAvg, 'blood_pressure', $diastolicAvg);

                        $aggregatedData[$interval] = [
                            'systolic' => $systolicAvg,
                            'diastolic' => $diastolicAvg,
                            'avg' => "$systolicAvg/$diastolicAvg",
                            'highest' => "$systolicMax/$diastolicMax",
                            'lowest' => "$systolicMin/$diastolicMin",
                            'highest_systolic' => $systolicMax,
                            'lowest_systolic' => $systolicMin,
                            'highest_diastolic' => $diastolicMax,
                            'lowest_diastolic' => $diastolicMin,
                            'state' => $state,
                            'state_systolic' => $systolicState,
                            'state_diastolic' => $diastolicState,
                            'color_state_systolic' => ActivityService::detemineColorOfStateHospital($systolicState),
                            'color_state_diastolic' => ActivityService::detemineColorOfStateHospital($diastolicState),
                        ];
                    } else {
                        $avgValue = round($bucket['avg_value']['value'] ?? 0);
                        $highestValue = round($bucket['max_value']['value'] ?? 0);
                        $lowestValue = round($bucket['min_value']['value'] ?? 0);
                        if ($type == 'respiratory_rate') {
                            $type = 'respiratory';
                        }
                        $state = ActivityService::determineStateHospital($id, $avgValue, $type);
                        $colorState = ActivityService::detemineColorOfStateHospital($state);

                        $aggregatedData[$interval] = [
                            'avg' => $avgValue,
                            'highest' => $highestValue,
                            'lowest' => $lowestValue,
                            'state' => $state,
                            'color_state' => $colorState
                        ];
                    }
                }

                $chartData[$type] = $aggregatedData;
            }

            return response()->json($chartData);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }



    public function downloadExcel(Request $request)
    {
        $auth = Auth::guard('hospital')->user();
        return Excel::download(new PatientHospitalExport($auth->user_general_purpose_id), 'inpatient-report' . ' ' . now() . '.xlsx');
    }

    public function downloadExcelDataVital(Request $request, $id)
    {
        $auth = Auth::guard('hospital')->user();
        $patient = PatientHospital::where('id', $id)->first();

        $fileName = 'data-vital-' . $patient->name . '-report' . ' ' . $request->query('end_date_range') . '.xlsx';
        if ($request->filled('type')) {
            $fileName = 'data-vital-' . $patient->name . '-report' . ' ' . $request->query('start_date_range') . ' - ' . $request->query('end_date_range') . '.xlsx';

        }

        return Excel::download(new PatientDataVitalHospitalExport($request, $id, $this->elasticsearch), $fileName);
    }

    public function getDevices(Request $request)
    {
        $devices = DevicesHospital::where('usage_status', false)->get();

        return response()->json(['devices' => $devices]);
    }
}