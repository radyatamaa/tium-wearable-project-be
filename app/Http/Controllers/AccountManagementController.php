<?php

namespace App\Http\Controllers;

use App\Models\Scopes\UserGeneralPurposeScope;
use Illuminate\Http\Request;
use App\Models\UserAdminHospital;
use Illuminate\Support\Facades\Hash;
use App\Exports\AccountHospitalExport;
use Maatwebsite\Excel\Facades\Excel;

class AccountManagementController extends Controller
{
    public function downloadExcel(Request $request)
    {
        return Excel::download(new AccountHospitalExport, 'account-hospital-report' . ' ' . now() . '.xlsx');
    }
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = UserAdminHospital::whereNull('is_administrator_company');


        if ($request->filled('search')) {
            if ($request->search_by == 'name') {
                $query = $query->where('name_person_in_charge', 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('user_id', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('registration_date', '>=', $request->start_date_range)
                    ->whereDate('registration_date', '<=', $request->end_date_range);
            }
        }

        $admins = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('hospital.account-management.index', ['accounts' => $admins]);
    }

    public function store(Request $request)
    {
        $checkID = UserAdminHospital::withoutGlobalScope(UserGeneralPurposeScope::class)->where('user_id', $request->user_id)->first();
        if ($checkID) {
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '이미 존재하는 ID이거나 다른 회사에서 사용하고 있는 ID입니다.' : 'This ID already exists or is being used by another company.', 'data' => null]);
        }
        $account = UserAdminHospital::create([
            'user_id' => $request->user_id,
            'password' => Hash::make($request->password),
            'name_person_in_charge' => $request->name_person_in_charge,
            'permission' => $request->permission,
            'registration_date' => $request->registration_date,
            'contact_person_position' => $request->contact_person_position,
            'contact_number' => $request->contact,
            'email' => $request->email,
        ]);

        return response()->json(['success' => true, 'message' => 'Admin registered successfully']);
    }

    public function show($id)
    {
        $account = UserAdminHospital::findOrFail($id);
        return response()->json(['account' => $account]);
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        UserAdminHospital::whereIn('id', $ids)->delete();

        return response()->json(['success' => true]);
    }
}