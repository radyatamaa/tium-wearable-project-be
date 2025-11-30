<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmergencyReportGeneralPurpose;

class MeasurementHistoryController extends Controller
{
    public function getWithUserCustomerId(Request $request,$id)
    {   
        $perPage = $request->input('limit', 10); 
        $datas = EmergencyReportGeneralPurpose::where('user_customer_id',$id)->orderBy('created_at','desc')->paginate($perPage);
        return response()->json([
            'data' => $datas,
            'success' => true,
        ]);
    }

    public function index(Request $request)
    {   
        $perPage = $request->input('limit', 10); 
        $datas = EmergencyReportGeneralPurpose::orderBy('created_at','desc')->paginate($perPage);
        return view('public-welfare.measurement_history',['datas' => $datas]);
    }

  
}