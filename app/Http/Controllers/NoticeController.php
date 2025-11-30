<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Notice;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = Notice::orderBy('registration_date', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by != '') {
                $query = $query->where($request->search_by, 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('announcement_title', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('registration_date', '>=', $request->start_date_range)
                    ->whereDate('registration_date', '<=', $request->end_date_range);
            }
        }

        $notice = $query->paginate($perPage);

        return view('notice.index', ['notices' => $notice]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if ($data['status'] == '전시중') {
            Notice::where('status', '전시중')->update(['status' => '표시되지 않음']);
        }
        $data['registration_date'] = now();
        $notice = Notice::create($data);
        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $notice = Notice::findOrFail($id);
        return response()->json($notice);
    }

    public function update(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);
        $data = $request->all();
        if ($data['status'] == '전시중') {
            Notice::where('status', '전시중')->update(['status' => '표시되지 않음']);
        }
        $notice->update($data);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notice = Notice::findOrFail($id);
        $notice->delete();
        return response()->json(['success' => true]);
    }

    public function deleteSelected(Request $request)
    {
        Notice::whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true]);
    }
}