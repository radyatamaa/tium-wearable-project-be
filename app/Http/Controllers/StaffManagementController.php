<?php

namespace App\Http\Controllers;

use App\Models\Scopes\UserGeneralPurposeScope;
use App\Models\SubscriptionInformationGeneralPurpose;
use App\Models\UserGeneralPurpose;
use Illuminate\Http\Request;
use App\Models\UserStaffHospital;
use App\Models\ServiceJobSetting;
use Illuminate\Support\Facades\Hash;
use App\Exports\StaffHospitalExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class StaffManagementController extends Controller
{
    public function downloadExcel(Request $request)
    {
        return Excel::download(new StaffHospitalExport, 'staff-account-report' . ' ' . now() . '.xlsx');
    }
    public function index(Request $request)
    {
        $auth = Auth::guard('hospital')->user();
        if (Auth::guard('hospital')->check()) {
            $auth = Auth::guard('hospital')->user();
        } else if (Auth::guard('hospitalStaff')->check()) {
            $auth = Auth::guard('hospitalStaff')->user();
        }
        $jobTitles = [];
        $positions = [];
        $departments = [];

        $subscriptionInformationGeneralPurpose = SubscriptionInformationGeneralPurpose::where('user_general_purpose_id', $auth->user_general_purpose_id)->first();
        if ($subscriptionInformationGeneralPurpose) {
            $departments = explode(' | ', $subscriptionInformationGeneralPurpose->selected_subjects);
        }

        $userGeneralPurpose = UserGeneralPurpose::where('id', $auth->user_general_purpose_id)->first();
        if ($userGeneralPurpose) {
            $masterJobs = ServiceJobSetting::where('service_classification', $userGeneralPurpose->user_type)->first();
            if ($masterJobs) {
                $jobTitles = explode(',', $masterJobs->job_title);
                $positions = explode(',', $masterJobs->position);
            }
        }

        $perPage = $request->input('limit', 10);
        $query = UserStaffHospital::orderBy('created_at', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by == '') {
                $query = $query->where('name', 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where($request->search_by, 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('registration_date', '>=', $request->start_date_range)
                    ->whereDate('registration_date', '<=', $request->end_date_range);
            }
        }

        $staffs = $query->paginate($perPage);
        return view('hospital.staff-management.index', [
            'staffs' => $staffs,
            'jobTitles' => $jobTitles,
            'positions' => $positions,
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $checkID = UserStaffHospital::withoutGlobalScope(UserGeneralPurposeScope::class)->where('user_id', $request->user_id)->first();
        if ($checkID) {
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '이미 존재하는 ID이거나 다른 회사에서 사용하고 있는 ID입니다.' : 'This ID already exists or is being used by another company.', 'data' => null]);
        }
        $data = $request->all();
        // $validated = $request->validate([
        //     'registration_date' => 'required|date',
        //     'birthdate' => 'required|date',
        //     'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        //     'age' => 'required|integer',
        //     'name' => 'required|string|max:255',
        //     'gender' => 'required|string|max:10',
        //     'job_title' => 'required|string|max:255',
        //     'department' => 'required|string|max:255',
        //     'address' => 'required|string|max:255',
        //     'position' => 'required|string|max:255',
        //     'contact1' => 'required|string|max:20',
        //     'employee_number' => 'required|string|max:20|unique:user_staff_hospitals,employee_number',
        //     'contact2' => 'nullable|string|max:20',
        //     'email' => 'required|string|email|max:255',
        // ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $data['photo_profile'] = $path;
        }

        $data['password'] = Hash::make($request->password);

        UserStaffHospital::create($data);

        return response()->json(['success' => true, 'message' => 'Staff saved successfully!']);
    }

    public function update(Request $request, $id)
    {
        $checkID = UserStaffHospital::withoutGlobalScope(UserGeneralPurposeScope::class)->where('user_id', $request->user_id)->first();
        if ($checkID && $checkID->id != $id) {
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '이미 존재하는 ID이거나 다른 회사에서 사용하고 있는 ID입니다.' : 'This ID already exists or is being used by another company.', 'data' => null]);
        }
        $data = $request->all();
        // $validated = $request->validate([
        //     'registration_date' => 'required|date',
        //     'birthdate' => 'required|date',
        //     'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        //     'age' => 'required|integer',
        //     'name' => 'required|string|max:255',
        //     'gender' => 'required|string|max:10',
        //     'job_title' => 'required|string|max:255',
        //     'department' => 'required|string|max:255',
        //     'address' => 'required|string|max:255',
        //     'position' => 'required|string|max:255',
        //     'contact1' => 'required|string|max:20',
        //     'employee_number' => 'required|string|max:20|unique:user_staff_hospitals,employee_number',
        //     'contact2' => 'nullable|string|max:20',
        //     'email' => 'required|string|email|max:255',
        // ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $data['photo_profile'] = $path;
        }

        if ($request->password && $request->password != '**********') {
            $data['password'] = Hash::make($request->password);
        }

        UserStaffHospital::find($id)->update($data);

        return response()->json(['success' => true, 'message' => 'Staff saved successfully!']);
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        UserStaffHospital::whereIn('id', $ids)->delete();
        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $staff = UserStaffHospital::findOrFail($id);
        return response()->json($staff);
    }
}