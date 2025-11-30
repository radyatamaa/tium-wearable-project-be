<?php

namespace App\Http\Controllers;

use App\Models\CSCenter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\UserAdminHospital;
use App\Models\AdminGeneralPurpose;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CSCenterController extends Controller
{
    public function showLoginSSOForm()
    {
        return view('cscenter.login-sso');
    }
    public function showCSForm()
    {
        return view('cscenter.csform');
    }

    public function submitCSForm(Request $request)
    {
        $data = CSCenter::create([
            'registration_date' => now(),
            'name' => $request->name,
            'email' => $request->email,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json(['success' => true, 'message' => 'success']);
    }



    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = CSCenter::orderBy('registration_date', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by != '') {
                $query = $query->where($request->search_by, 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('title', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('registration_date', '>=', $request->start_date_range)
                    ->whereDate('registration_date', '<=', $request->end_date_range);
            }
        }

        $inquiries = $query->paginate($perPage);
        return view('cscenter.index', compact('inquiries'));
    }

    public function edit($id)
    {
        $inquiries = CSCenter::findOrFail($id);
        return response()->json($inquiries);
    }

    public function update(Request $request, $id)
    {
        $notice = CSCenter::findOrFail($id);
        $notice->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notice = CSCenter::findOrFail($id);
        $notice->delete();
        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        CSCenter::whereIn('id', $ids)->delete();
        return response()->json(['success' => true]);
    }
}