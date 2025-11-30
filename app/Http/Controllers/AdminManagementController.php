<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\UserAdminHospital;
use App\Models\AdminGeneralPurpose;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminManagementController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = User::orderBy('registration_date', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by != '') {
                $query = $query->where($request->search_by, 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('administrator_level', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('registration_date', '>=', $request->start_date_range)
                    ->whereDate('registration_date', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);
        return view('admin-management.index', ['admins' => $datas]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $checkID = User::where('user_id', $request->user_id)->first();
            if ($checkID) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다.' : 'The ID already exists.', 'data' => null]);
            }

            $data = $request->all();
            $data['photo'] = null;
            if ($request->hasFile('fileUpload')) {
                $path = $request->file('fileUpload')->store('photos', 'public');
                $data['photo'] = $path;
            }

            $data['password'] = Hash::make($data['password']);
            $data['registration_date'] = date('y-m-d');
            $admin = User::create($data);

            // // init user all apps for administrator Tium
            // $administratorHospitalCompany = UserAdminHospital::create([
            //     'user_id' => $data['user_id'],
            //     'password' => $data['password'],
            //     'name_person_in_charge' => $data['name'],
            //     'permission' => 'Admin',
            //     'registration_date' => now(),
            //     'contact_person_position' => $data['phone_number'] ?? null,
            //     'contact_number' => $data['phone_number'] ?? null,
            //     'email' => $data['email'] ?? null,
            //     'is_administrator_tium' => true,
            // ]);

            // $administratorCompany = AdminGeneralPurpose::create([
            //     'user_id' => $data['user_id'],
            //     'name' => $data['name'],
            //     'classification' => 'Admin',
            //     'department' => '',
            //     'contact' => $data['phone_number'] ?? null,
            //     'registration_date' => now(),
            //     'is_administrator_tium' => true,
            // ]);

            DB::commit();
            return response()->json(['success' => true, 'admin' => $admin]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '실패한' : 'Failed', 'data' => null]);
        }
    }

    public function show($id)
    {
        $admin = User::findOrFail($id);
        return response()->json($admin);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $admin = User::findOrFail($id);
            $checkID = User::where('user_id', $request->user_id)->first();
            if ($checkID && $checkID->id != $id) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다.' : 'The ID already exists.', 'data' => null]);
            }

            if ($request->hasFile('fileUpload')) {
                $path = $request->file('fileUpload')->store('photos', 'public');
                $request->merge(['photo' => $path]);
            }

            $data = $request->all();
            if ($request->filled('password') && $request->password != '**********') {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $admin->update($data);

            // // Handle init administrator tium
            // $administratorTiumForHospital = UserAdminHospital::where('user_id', $request->user_id)
            //     ->where('is_administrator_tium', true)
            //     ->first();

            // if ($administratorTiumForHospital) {
            //     $administratorTiumForHospital->fill([
            //         'contact_person_position' => $request->filled('phone_number') ? $request->input('phone_number') : $administratorTiumForHospital->contact_person_position,
            //         'contact_number' => $request->filled('phone_number') ? $request->input('phone_number') : $administratorTiumForHospital->contact_person_position,
            //         'email' => $request->filled('email') ? $request->input('email') : $administratorTiumForHospital->email,
            //     ]);

            //     if ($request->filled('password')) {
            //         $administratorTiumForHospital->password = Hash::make($request->input('password'));
            //     }
            //     $administratorTiumForHospital->save();
            // }

            // $administratorTiumForGenPurpose = AdminGeneralPurpose::where('user_id', $request->user_id)
            //     ->where('is_administrator_tium', true)
            //     ->first();

            // if ($administratorTiumForGenPurpose) {
            //     $administratorTiumForGenPurpose->fill([
            //         'contact' => $request->filled('phone_number') ? $request->input('phone_number') : $administratorTiumForGenPurpose->contact,
            //     ]);

            //     if ($request->filled('password')) {
            //         $administratorTiumForGenPurpose->password = Hash::make($request->input('password'));
            //     }
            //     $administratorTiumForGenPurpose->save();
            // }

            DB::commit();
            return response()->json(['success' => true, 'admin' => $admin]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '실패한' : 'Failed', 'data' => null]);
        }
    }

    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return response()->json(['success' => true]);
    }

    public function deleteSelected(Request $request)
    {
        User::whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true]);
    }
}