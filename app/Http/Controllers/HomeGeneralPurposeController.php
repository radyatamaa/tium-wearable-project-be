<?php

namespace App\Http\Controllers;

use App\Models\PatientCallReportGeneralPurpose;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\UserGeneralPurpose;
use App\Models\UserManagementGeneralPurpose;
use App\Models\EmergencyReportGeneralPurpose;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminGeneralPurpose;

class HomeGeneralPurposeController extends Controller
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
        $users = UserManagementGeneralPurpose::orderBy('created_at', 'desc')->get();
        $userCustomerId = $users->filter(function ($user) {
            return !is_null($user->user_customer_id);
        })->pluck('user_customer_id');
        // $alerts = EmergencyReportGeneralPurpose::whereIn('user_customer_id', $userCustomerId)->whereNull('confirmation_time')->orderBy('created_at', 'desc')->get();
        $alerts = [];
        $calls = PatientCallReportGeneralPurpose::whereIn('user_customer_id', $userCustomerId)->whereNull('confirmation_time')->orderBy('created_at', 'desc')->get();
        // $totalUsers = UserManagementGeneralPurpose::count();
        // $totalUsersToday = UserManagementGeneralPurpose::whereDate('created_at', '=', Carbon::today())->count();
        $totalUsers = 0;
        $totalUsersToday = 0;
        return view('general-purpose.dashboard', compact('users', 'alerts', 'calls', 'totalUsers', 'totalUsersToday'));
    }

    public function changePassword(Request $request)
    {
        $auth = Auth::guard('generalPurpose')->user();
        $user = AdminGeneralPurpose::findOrFail($auth->id);
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(['success' => true, 'message' => 'updated password successfully']);
    }


    public function navData()
    {
        $users = UserManagementGeneralPurpose::orderBy('created_at', 'desc')->get();
        $userCustomerId = $users->filter(function ($user) {
            return !is_null($user->user_customer_id);
        })->pluck('user_customer_id');

        $emergencyNeedConfirmCount = EmergencyReportGeneralPurpose::with(['userCustomer'])->whereIn('user_customer_id', $userCustomerId)->whereNull('confirmation_time')->count();
        $emergencyCompletedConfirmCount = EmergencyReportGeneralPurpose::with(['userCustomer'])->whereIn('user_customer_id', $userCustomerId)->whereNotNull('confirmation_time')->count();

        $patientCallNeedConfirmCount = PatientCallReportGeneralPurpose::with(['userCustomer'])->whereIn('user_customer_id', $userCustomerId)->whereNull('confirmation_time')->count();
        $patientCallCompletedConfirmCount = PatientCallReportGeneralPurpose::with(['userCustomer'])->whereIn('user_customer_id', $userCustomerId)->whereNotNull('confirmation_time')->count();


        $data = [
            'emergencyNeedConfirmCount' => $emergencyNeedConfirmCount,
            'emergencyCompletedConfirmCount' => $emergencyCompletedConfirmCount,
            'patientCallNeedConfirmCount' => $patientCallNeedConfirmCount,
            'patientCallCompletedConfirmCount' => $patientCallCompletedConfirmCount,
        ];

        return response()->json(['data' => $data]);
    }

    public function patientCallList(Request $request)
    {
        $users = UserManagementGeneralPurpose::orderBy('created_at', 'desc')->get();
        $userCustomerId = $users->filter(function ($user) {
            return !is_null($user->user_customer_id);
        })->pluck('user_customer_id');
        $perPage = $request->input('limit', 100);
        $calls = PatientCallReportGeneralPurpose::whereIn('user_customer_id', $userCustomerId)
            ->whereNull('confirmation_time')
            ->orderBy('created_at', 'desc')->paginate($perPage);


        return response()->json(['datas' => $calls]);
    }

    public function userManagementList(Request $request)
    {
        $perPage = $request->input('limit', 100);
        $users = UserManagementGeneralPurpose::orderBy('created_at', 'desc')->paginate($perPage);
        $totalUsers = UserManagementGeneralPurpose::count();
        $totalUsersToday = UserManagementGeneralPurpose::whereDate('created_at', '=', Carbon::today())->count();

        return response()->json(['datas' => $users, 'totalUsers' => $totalUsers, 'totalUsersToday' => $totalUsersToday]);
    }
}