<?php

namespace App\Http\Controllers;

use App\Models\Scopes\UserGeneralPurposeScope;
use App\Models\ServiceJobSetting;
use App\Models\SubscriptionInformationGeneralPurpose;
use App\Models\UserGeneralPurpose;
use App\Models\UserCustomer;
use App\Services\ActivityService;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use App\Models\MeasurementSettingsPublicWelfare;
use App\Models\DevicesSmartWatchPublicWelfare;
use App\Models\AdminPublicWelfare;
use App\Models\UserStaffPublicWelfare;
use App\Models\WardPublicWelfare;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\UserManagementPublicWelfare;

class SettingPublicWelfareController extends Controller
{
    public function alarmSetting(Request $request)
    {
        // Fetch the settings from the database
        $settings = ActivityService::measurementSettingByPatientIdPublicWelfare($request->auth->user_general_purpose_id);
        $objectSettings = json_decode(json_encode($settings));
        return view('public-welfare.setting.index', ['menu' => 'alarm-setting', 'settings' => $objectSettings]);
    }

    public function setAlarmSetting(Request $request)
    {
        // Update the settings in the database
        $settings = MeasurementSettingsPublicWelfare::updateOrCreate(
            ['user_general_purpose_id' => $request->auth->user_general_purpose_id], // Matching condition
            $request->all() // Data to update or insert
        );
        return response()->json(['success' => true, 'message' => 'Settings updated successfully']);
    }

    public function smartWatch(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = DevicesSmartWatchPublicWelfare::orderBy('created_at', 'desc');
        if ($request->filled('search')) {
            $query = $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('serial', 'like', '%' . $request->search . '%');
        }
        $datas = $query->paginate($perPage);
        return view('public-welfare.setting.index', ['menu' => 'smartwatch', 'datas' => $datas]);
    }

    public function smartWatchShow($id)
    {
        $device = DevicesSmartWatchPublicWelfare::findOrFail($id);
        return response()->json($device);
    }

    public function smartWatchUpdate(Request $request, $id)
    {

        $device = DevicesSmartWatchPublicWelfare::findOrFail($id);
        if ($device->usage_status) {
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '기기가 사용 중이므로 기기를 업데이트할 수 없습니다.' : 'cant update device since the device is being used user', 'data' => null]);
        }
        if ($request->serial != $device->serial) {
            $deviceCheck = DevicesSmartWatchPublicWelfare::where(DB::raw('LOWER(serial)'), strtolower($request->serial))->first();
            if ($deviceCheck) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '장치 코드가 이미 존재합니다' : 'Device code already exists', 'data' => null]);
            }
            $checkDeviceInOtherSide = DeviceService::checkDeviceInOtherSide($request->serial);
            if (!$checkDeviceInOtherSide['success']) {
                return response()->json($checkDeviceInOtherSide);
            }
        }
        $device->update($request->all());
        return response()->json(['success' => true, 'message' => 'Device updated successfully']);
    }

    public function smartWatchStore(Request $request)
    {
        $device = DevicesSmartWatchPublicWelfare::where(DB::raw('LOWER(serial)'), strtolower($request->serial))->first();
        if ($device) {
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '장치 코드가 이미 존재합니다' : 'Device code already exists', 'data' => null]);
        }
        $checkDeviceInOtherSide = DeviceService::checkDeviceInOtherSide($request->serial);
        if (!$checkDeviceInOtherSide['success']) {
            return response()->json($checkDeviceInOtherSide);
        }

        $data = $request->all();
        $data['registration_date'] = now();
        $data['usage_status'] = false;
        // $data['usage_status'] = false;
        DevicesSmartWatchPublicWelfare::create($data);
        return response()->json(['success' => true, 'message' => 'Device registered successfully']);
    }

    public function smartWatchDestroy(Request $request)
    {
        $devicesUsed = [];
        foreach ($request->ids as $key => $id) {
            $check = DevicesSmartWatchPublicWelfare::where('id', $id)->first();
            if ($check->usage_status) {
                array_push($devicesUsed, $check->serial);
            }
        }

        if (count($devicesUsed) > 0) {
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '장치 코드 ' . implode(', ', $devicesUsed) . '를 삭제할 수 없습니다. 해당 장치가 사용자 사용하고 있기 때문입니다.' : 'cant delete device code ' . implode(', ', $devicesUsed) . ' cause the device its being using the user', 'data' => null]);
        }
        $ids = $request->ids;
        DevicesSmartWatchPublicWelfare::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'success']);
    }

    public function adminManagement(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = AdminPublicWelfare::whereNull('is_administrator_company');
        if ($request->filled('search')) {
            $query = $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('user_id', 'like', '%' . $request->search . '%');
        }
        $datas = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $jobTitles = [];
        $positions = [];
        $departments = [];
        $subscriptionInformationGeneralPurpose = SubscriptionInformationGeneralPurpose::where('user_general_purpose_id', $request->auth->user_general_purpose_id)->first();
        if ($subscriptionInformationGeneralPurpose) {
            $departments = explode(' | ', $subscriptionInformationGeneralPurpose->selected_subjects);
        }

        $userGeneralPurpose = UserGeneralPurpose::where('id', $request->auth->user_general_purpose_id)->first();
        if ($userGeneralPurpose) {
            $masterJobs = ServiceJobSetting::where('service_classification', $userGeneralPurpose->user_type)->first();
            if ($masterJobs) {
                $jobTitles = explode(',', $masterJobs->job_title);
                $positions = explode(',', $masterJobs->position);
            }
        }
        return view('public-welfare.setting.index', [
            'menu' => 'admin-management',
            'datas' => $datas,
            'jobTitles' => $jobTitles,
            'positions' => $positions,
            'departments' => $departments,
        ]);
    }

    public function adminManagementShow($id)
    {
        $admin = AdminPublicWelfare::findOrFail($id);
        return response()->json($admin);
    }

    public function adminManagementUpdate(Request $request, $id)
    {
        $admin = AdminPublicWelfare::findOrFail($id);
        $checkID = AdminPublicWelfare::withoutGlobalScope(UserGeneralPurposeScope::class)->where('user_id', $request->user_id)->first();
        if ($checkID) {
            if ($checkID->id != $id) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '이미 존재하는 ID이거나 다른 회사에서 사용하고 있는 ID입니다.' : 'This ID already exists or is being used by another company.', 'data' => null]);
            }
        }
        $admin->update($request->all());
        return response()->json(['success' => true, 'message' => 'Admin updated successfully']);
    }

    public function adminManagementStore(Request $request)
    {
        $checkID = AdminPublicWelfare::withoutGlobalScope(UserGeneralPurposeScope::class)->where('user_id', $request->user_id)->first();
        if ($checkID) {
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '이미 존재하는 ID이거나 다른 회사에서 사용하고 있는 ID입니다.' : 'This ID already exists or is being used by another company.', 'data' => null]);
        }
        $data = $request->all();
        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        }
        $data['registration_date'] = now();
        AdminPublicWelfare::create($data);
        return response()->json(['success' => true, 'message' => 'Admin registered successfully']);
    }

    public function adminManagementChangePassword(Request $request, $id)
    {
        $admin = AdminPublicWelfare::findOrFail($id);
        $admin->password = Hash::make($request->password);
        $admin->save();
        return response()->json(['success' => true, 'message' => 'Admin updated password successfully']);
    }

    public function adminManagementDestroy(Request $request)
    {
        $ids = $request->ids;
        AdminPublicWelfare::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'success']);
    }

    public function ward(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = WardPublicWelfare::orderBy('created_at', 'desc');
        if ($request->filled('search')) {
            $query = $query->where('ward_number', 'like', '%' . $request->search . '%')
                ->orWhere('ward_name', 'like', '%' . $request->search . '%');
        }
        $datas = $query->paginate($perPage);
        return view('public-welfare.setting.index', ['menu' => 'ward', 'datas' => $datas]);
    }

    public function wardShow($id)
    {
        $ward = WardPublicWelfare::find($id);
        return response()->json($ward);
    }

    public function wardUpdate(Request $request, $id)
    {
        $ward = WardPublicWelfare::find($id);
        $ward->ward_number = $request->ward_number;
        $ward->ward_name = $request->ward_name;
        $ward->usage_status = $request->usage_status;
        $ward->save();

        return response()->json(['success' => true, 'message' => 'Ward updated successfully']);
    }

    public function wardStore(Request $request)
    {
        $ward = new WardPublicWelfare();
        $ward->ward_number = $request->ward_number;
        $ward->ward_name = $request->ward_name;
        $ward->usage_status = $request->usage_status;
        $ward->registration_date = now();
        $ward->save();

        return response()->json(['success' => true, 'message' => 'Ward registered successfully']);
    }

    public function wardDestroy(Request $request)
    {
        $ids = $request->ids;
        WardPublicWelfare::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'success']);
    }

    public function staffManagement(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = UserStaffPublicWelfare::orderBy('created_at', 'desc');
        if ($request->filled('search')) {
            $query = $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('user_id', 'like', '%' . $request->search . '%');
        }

        $datas = $query->paginate($perPage);
        return view('public-welfare.setting.index', ['menu' => 'staff-management', 'datas' => $datas]);
    }

    public function staffManagementShow($id)
    {
        $user = UserStaffPublicWelfare::find($id);
        return response()->json($user);
    }

    public function staffManagementUpdate(Request $request, $id)
    {
        $user = UserStaffPublicWelfare::find($id);
        $data = $request->all();

        // $checkID = UserStaffPublicWelfare::where('user_id', $request->user_id)->first();
        // if ($checkID) {
        //     if ($checkID->id != $id) {
        //         return response()->json(['success' => false, 'message' => '아이디가 이미 존재합니다.']);
        //     }
        // }

        if ($request->hasFile('photo_profile')) {
            $photoPath = $request->file('photo_profile')->store('profiles', 'public');
            $data['photo_profile'] = $photoPath;
        }

        $user->update($data);
        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function staffManagementStore(Request $request)
    {
        $data = $request->all();

        $checkID = UserStaffPublicWelfare::where('user_id', $request->user_id)->first();
        if ($checkID) {
            return response()->json(['success' => false, 'message' => '아이디가 이미 존재합니다.']);
        }

        if ($request->hasFile('photo_profile')) {
            $photoPath = $request->file('photo_profile')->store('profiles', 'public');
            $data['photo_profile'] = $photoPath;
        }

        UserStaffPublicWelfare::create($data);
        return response()->json(['success' => true, 'message' => 'User registered successfully']);
    }

    public function staffManagementChangePassword(Request $request, $id)
    {
        $admin = UserStaffPublicWelfare::findOrFail($id);
        $admin->password = Hash::make($request->password);
        $admin->save();
        return response()->json(['success' => true, 'message' => 'Admin updated password successfully']);
    }

    public function staffManagementDestroy(Request $request)
    {
        $ids = $request->ids;
        UserStaffPublicWelfare::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'success']);
    }

    public function userManagement(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = UserManagementPublicWelfare::orderBy('created_at', 'desc');
        if ($request->filled('search')) {
            $query = $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        $datas = $query->paginate($perPage);
        return view('public-welfare.setting.index', ['menu' => 'user-management', 'datas' => $datas, 'devices' => []]);
    }

    public function userManagementShow($id)
    {
        $data = UserManagementPublicWelfare::findOrFail($id);
        return response()->json($data);
    }

    public function userManagementUpdate(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $dataUpdate = UserManagementPublicWelfare::findOrFail($id);
            $checkUser = UserCustomer::where('email', $request->email)->first();
            // if (!$checkUser) {
            //     return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '사용자 이메일을 찾을 수 없습니다. 먼저 스마트폰에서 사용자를 등록해 주세요.' : 'Your email cannot be found. First, register as a user on your smartphone.', 'data' => null]);
            // }
            $data = $request->all();

            $device = DevicesSmartWatchPublicWelfare::where('serial', $request->device_code)->first();

            if ($checkUser) {
                if ($dataUpdate->user_customer_id != $checkUser->id) {
                    return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '사용자는 이미 당사 애플리케이션에 등록되어 있으므로 다른 이메일을 사용하세요.' : 'Please use a different email address as you are already registered with our application.', 'data' => null]);
                }
                $data['user_customer_id'] = $checkUser->id;
                if ($device) {
                    if ($device->usage_status == true && $device->user_customer_id != $checkUser->id) {
                        return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '다른 사용자가 해당 장치를 사용했습니다.' : 'Another user has used the device.', 'data' => null]);
                    }

                    if ($dataUpdate->device_code != $request->device_code) {
                        $oldDevice = DevicesSmartWatchPublicWelfare::where('serial', $dataUpdate->device_code)->first();
                        if ($oldDevice) {
                            $oldDevice->user_customer_id = null;
                            $oldDevice->usage_status = false;
                            $oldDevice->save();
                        }
                    }

                    $device->user_customer_id = $checkUser->id;
                    $device->usage_status = true;
                    $device->save();
                }
            }

            $dataUpdate->update($data);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Admin updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function userManagementStore(Request $request)
    {
        DB::beginTransaction();

        try {
            $checkUser = UserCustomer::where('email', $request->email)->first();
            // if (!$checkUser) {
            //     return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '사용자 이메일을 찾을 수 없습니다. 먼저 스마트폰에서 사용자를 등록해 주세요.' : 'Your email cannot be found. First, register as a user on your smartphone.', 'data' => null]);
            // }
            $data = $request->all();

            unset($data['password']);

            if ($checkUser) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '사용자는 이미 당사 애플리케이션에 등록되어 있으므로 다른 이메일을 사용하세요.' : 'Please use a different email address as you are already registered with our application.', 'data' => null]);
                // $data['user_customer_id'] = $checkUser->id;
                // $checkUser->password = Hash::make($request->password);
                // $checkUser->name = $request->name;
                // $checkUser->gender = $request->gender;
                // $checkUser->birth_date = $request->birth_date;
                // $checkUser->phone_number = $request->contact_information;

                // $checkUser->save();
            } else {
                $userCustomer = UserCustomer::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'gender' => $request->gender,
                    'birth_date' => $request->birth_date,
                    'height' => 0,
                    'weight' => 0,
                    'phone_number' => $request->contact_information,
                    'doctor_name' => null,
                    'room_number' => null,
                ]);

                $data['user_customer_id'] = $userCustomer->id;
            }

            $device = DevicesSmartWatchPublicWelfare::where('serial', $request->device_code)->first();
            if ($device) {
                if ($device->usage_status == true) {
                    return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '다른 사용자가 해당 장치를 사용했습니다.' : 'Another user has used the device.', 'data' => null]);
                }
                $device->user_customer_id = $data['user_customer_id'];
                $device->usage_status = true;
                $device->save();
            }

            UserManagementPublicWelfare::create($data);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Admin registered successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function userManagementDestroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->ids;

            foreach ($ids as $id) {
                $user = UserManagementPublicWelfare::find($id);
                if ($user->user_customer_id) {
                    $oldDevice = DevicesSmartWatchPublicWelfare::where('user_customer_id', $user->user_customer_id)->first();
                    if ($oldDevice) {
                        $oldDevice->user_customer_id = null;
                        $oldDevice->usage_status = false;
                        $oldDevice->save();
                    }

                    UserCustomer::where('id', $user->user_customer_id)->delete();
                }
            }

            UserManagementPublicWelfare::whereIn('id', $ids)->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => '']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '실패한' : 'failed', 'error' => $e->getMessage()]);
        }
    }

    public function getDevices(Request $request)
    {
        $devices = DevicesSmartWatchPublicWelfare::where('usage_status', false)->get();

        return response()->json(['devices' => $devices]);
    }
}