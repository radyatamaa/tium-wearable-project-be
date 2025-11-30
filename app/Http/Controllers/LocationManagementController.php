<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TopLocation;
use App\Models\SubLocation;
use App\Models\DetailedLocation;
use App\Exports\TopLocationExport;
use App\Exports\SubLocationExport;
use App\Exports\DetailedLocationExport;
use Maatwebsite\Excel\Facades\Excel;

class LocationManagementController extends Controller
{
    public function topDownloadExcel(Request $request)
    {
        return Excel::download(new TopLocationExport, 'top-location-report' . ' ' . now() . '.xlsx');
    }
    public function SubDownloadExcel(Request $request)
    {
        return Excel::download(new SubLocationExport, 'sub-location-report' . ' ' . now() . '.xlsx');
    }
    public function DetailedDownloadExcel(Request $request)
    {
        return Excel::download(new DetailedLocationExport, 'detailed-location-report' . ' ' . now() . '.xlsx');
    }

    public function topIndex(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = TopLocation::orderBy('created_at', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by == 'top_location_name') {
                $query = $query->where('location_name', 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('location_code', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('registration_date', '>=', $request->start_date_range)
                    ->whereDate('registration_date', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);
        return view('hospital.location-management.top-location', ['datas' => $datas]);
    }
    public function topStore(Request $request)
    {
        $checkID = TopLocation::where('location_code', $request->location_code)->first();
        if ($checkID) {
            return response()->json(['success' => false, 'message' => 'location_code already exists', 'account' => null]);
        }
        $account = TopLocation::create([
            'location_name' => $request->location_name,
            'location_code' => $request->location_code,
            'usage_status' => $request->usage_status,
            'registration_date' => $request->registration_date,
        ]);

        return response()->json(['success' => true, 'message' => 'TopLocation registered successfully']);
    }

    public function topShow($id)
    {
        $location = TopLocation::findOrFail($id);
        return response()->json(['location' => $location]);
    }

    public function topDestroy(Request $request)
    {
        $ids = $request->ids;
        TopLocation::whereIn('id', $ids)->delete();

        return response()->json(['status' => 'success']);
    }

    // //////////// sub location
    public function subIndex(Request $request)
    {
        $topLocations = TopLocation::get();
        $perPage = $request->input('limit', 10);
        $query = SubLocation::orderBy('created_at', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by == 'sub_location_name') {
                $query = $query->where('location_name', 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('location_code', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('registration_date', '>=', $request->start_date_range)
                    ->whereDate('registration_date', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);
        return view('hospital.location-management.sub-location', ['datas' => $datas, 'topLocations' => $topLocations]);
    }

    public function subStore(Request $request)
    {
        $checkID = SubLocation::where('location_code', $request->location_code)->first();
        if ($checkID) {
            return response()->json(['success' => false, 'message' => 'location_code already exists', 'account' => null]);
        }
        $account = SubLocation::create([
            'location_name' => $request->location_name,
            'location_code' => $request->location_code,
            'usage_status' => $request->usage_status,
            'registration_date' => $request->registration_date,
            'top_location_id' => $request->top_location_id
        ]);

        return response()->json(['success' => true, 'message' => 'SubLocation registered successfully']);
    }

    public function subShow($id)
    {
        $location = SubLocation::findOrFail($id);
        return response()->json(['location' => $location]);
    }

    public function subDestroy(Request $request)
    {
        $ids = $request->ids;
        SubLocation::whereIn('id', $ids)->delete();

        return response()->json(['status' => 'success']);
    }

    // //////////// detailed location
    public function detailedIndex(Request $request)
    {
        $subLocations = SubLocation::get();
        $perPage = $request->input('limit', 10);
        $query = DetailedLocation::orderBy('created_at', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by == 'detailed_location_name') {
                $query = $query->where('location_name', 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('location_code', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('registration_date', '>=', $request->start_date_range)
                    ->whereDate('registration_date', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);
        return view('hospital.location-management.detailed-location', ['datas' => $datas, 'subLocations' => $subLocations]);
    }

    public function detailedStore(Request $request)
    {
        $checkID = DetailedLocation::where('location_code', $request->user_id)->first();
        if ($checkID) {
            return response()->json(['success' => false, 'message' => 'location_code already exists', 'account' => null]);
        }
        $account = DetailedLocation::create([
            'location_name' => $request->location_name,
            'location_code' => $request->location_code,
            'usage_status' => $request->usage_status,
            'registration_date' => $request->registration_date,
            'sub_location_id' => $request->sub_location_id
        ]);

        return response()->json(['success' => true, 'message' => 'DetailedLocation registered successfully']);
    }

    public function detailedShow($id)
    {
        $location = DetailedLocation::findOrFail($id);
        return response()->json(['location' => $location]);
    }

    public function detailedDestroy(Request $request)
    {
        $ids = $request->ids;
        DetailedLocation::whereIn('id', $ids)->delete();

        return response()->json(['status' => 'success']);
    }
}