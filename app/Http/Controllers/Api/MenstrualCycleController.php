<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenstrualCycleActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Services\ElasticSearchService;

class MenstrualCycleController extends Controller
{
    protected $elasticsearch;
    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticsearch = $elasticSearchService;
    }

    public function getSummary(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();
            $date = Carbon::parse($request->input('date', now()))->startOfDay();

            // Fetch menstrual cycle data
            $menstrualCycleData = MenstrualCycleActivity::where('user_customer_id', $auth->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $averageDuration = $menstrualCycleData->average_duration ?? 0;
            $averageCycle = $menstrualCycleData->average_cycle ?? 0;
            $lastMenstrualStartDate = $menstrualCycleData->start_date ?? '--';

            // Calculate next period estimate
            if ($lastMenstrualStartDate !== '--' && $averageCycle !== '--') {
                $nextPeriodEstimate = Carbon::parse($lastMenstrualStartDate)->addDays($averageCycle)->format('Y-m-d');
            } else {
                $nextPeriodEstimate = '--';
            }

            $result = [
                'average_duration' => $averageDuration,
                'average_cycle' => $averageCycle,
                'last_menstrual_start_date' => Carbon::parse($lastMenstrualStartDate)->format('Y-m-d'),
                'next_period_estimate' => $nextPeriodEstimate,
            ];

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => $result,
                'time_stamp' => now(),
            ]);
        } catch (Exception $e) {
            $message = 'Something went wrong';
            $time_stamp = now();

            Log::error($message, ['detail' => (string) $e->getMessage(), 'time_stamp' => $time_stamp]);
            return response()->json([
                'status_code' => 500,
                'message' => $message,
                'data' => null,
                'time_stamp' => $time_stamp,
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Log::info('synchActivityMenstrualCycle request from smartphone : ' . json_encode($request->json()->all()));
        try {
            $auth = Auth::guard('api')->user();

            $menstrualCycle = MenstrualCycleActivity::create([
                'device_address' => $request->input('device_address'),
                'user_customer_id' => $auth->id,
                'average_duration' => $request->input('average_duration'),
                'average_cycle' => $request->input('average_cycle'),
                'start_date' => $request->input('last_menstrual_start_date'),
            ]);

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => null,
                'time_stamp' => now(),
            ], 200);
        } catch (Exception $e) {
            $message = 'Something went wrong';
            $time_stamp = now();

            Log::error($message, ['detail' => (string) $e->getMessage(), 'time_stamp' => $time_stamp]);
            return response()->json([
                'status_code' => 500,
                'message' => $message,
                'data' => null,
                'time_stamp' => $time_stamp,
            ], 500);
        }
    }
}