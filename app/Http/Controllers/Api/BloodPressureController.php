<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ActivitySyncHistoryJob;
use App\Jobs\AlarmEmergencyGeneralPurposeJob;
use App\Jobs\AlarmEmergencyPublicWelfareJob;
use App\Models\AllActivityUser;
use App\Models\PatientHospital;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCustomer;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use App\Services\ElasticSearchService;

class BloodPressureController extends Controller
{
    protected $elasticsearch;
    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticsearch = $elasticSearchService;
    }

    public function getSummaryBloodPressure(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();
            $date = Carbon::parse($request->input('date'))->startOfDay();
            $timePeriod = $request->input('time_period', 'day'); // Default to 'day'

            // Define start and end date based on time period
            if ($timePeriod === 'week') {
                $startDate = $date->copy()->startOfWeek();
                $endDate = $date->copy()->endOfWeek();
            } elseif ($timePeriod === 'month') {
                $startDate = $date->copy()->startOfMonth();
                $endDate = $date->copy()->endOfMonth();
            } elseif ($timePeriod === 'year') {
                $startDate = $date->copy()->startOfYear();
                $endDate = $date->copy()->endOfYear();
            } else {
                $startDate = $date;
                $endDate = $date->copy()->endOfDay();
            }

            // Check for specific start_date and end_date parameters
            if ($request->input('start_date') && $request->input('end_date')) {
                $startDate = Carbon::parse($request->input('start_date'));
                $endDate = Carbon::parse($request->input('end_date'));
            }

            // Determine filtering field
            $filterBy = 'user_customer_id';
            $filterValue = $auth->id;
            $patient = PatientHospital::where('status', 'IN_PATIENT')
                ->where('user_customer_id', $auth->id)->first();
            if ($patient) {
                $filterBy = 'patient_id';
                $filterValue = $patient->id;
            }

            // Default values for response
            $chartData = [];
            $systolicAvg = $diastolicAvg = $lowestSystolic = $highestSystolic = $lowestDiastolic = $highestDiastolic = 0;
            $averagePulse = $lowestPulse = $highestPulse = 0;

            // Elasticsearch Query for blood pressure
            $query = [
                'index' => 'blood_pressure_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'range' => [
                                        'recorded_at' => [
                                            'gte' => $startDate->toIso8601String(),
                                            'lte' => $endDate->toIso8601String(),
                                            'format' => 'strict_date_optional_time'
                                        ],
                                    ]
                                ],
                                ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]],
                                ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]],
                                ['term' => [$filterBy => $filterValue]]
                            ],
                        ],
                    ],
                    'aggs' => [
                        'group_by_time' => [
                            'date_histogram' => [
                                'field' => 'recorded_at',
                                'calendar_interval' => $timePeriod === 'day' ? 'hour' : ($timePeriod === 'week' ? 'day' : ($timePeriod === 'month' ? 'week' : 'month')),
                                'format' => $timePeriod === 'day' ? 'HH:00' : ($timePeriod === 'week' ? 'EEEE' : ($timePeriod === 'month' ? "'Week' w" : 'MMMM')),
                                'time_zone' => 'Asia/Seoul',
                            ],
                            'aggs' => [
                                'avg_systolic' => ['avg' => ['field' => 'systolic']],
                                'avg_diastolic' => ['avg' => ['field' => 'diastolic']],
                            ],
                        ],
                        'avg_systolic' => ['avg' => ['field' => 'systolic']],
                        'avg_diastolic' => ['avg' => ['field' => 'diastolic']],
                        'min_systolic' => ['min' => ['field' => 'systolic']],
                        'max_systolic' => ['max' => ['field' => 'systolic']],
                        'min_diastolic' => ['min' => ['field' => 'diastolic']],
                        'max_diastolic' => ['max' => ['field' => 'diastolic']],
                    ],
                    'size' => 0, // No need to fetch actual documents
                ],
            ];

            // Try to perform Elasticsearch search for blood pressure data
            try {
                $response = $this->elasticsearch->search($query);
                $aggregations = $response['aggregations'];

                // Extract chart data
                $groupedData = collect($aggregations['group_by_time']['buckets'])->mapWithKeys(function ($bucket) use ($timePeriod) {
                    $key = $bucket['key_as_string'];
                    return [
                        $key => [
                            'systolic' => $bucket['avg_systolic']['value'] ?? 0,
                            'diastolic' => $bucket['avg_diastolic']['value'] ?? 0,
                        ],
                    ];
                })->toArray();

                // Generate time slots based on the time period
                $timeSlots = match ($timePeriod) {
                    'day' => collect(range(0, 23))->mapWithKeys(fn($h) => [sprintf('%02d:00', $h) => ['systolic' => 0, 'diastolic' => 0]])->toArray(),
                    'week' => collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])->mapWithKeys(fn($d) => [$d => ['systolic' => 0, 'diastolic' => 0]])->toArray(),
                    'month' => collect(range(1, 5))->mapWithKeys(fn($w) => ['Week ' . $w => ['systolic' => 0, 'diastolic' => 0]])->toArray(),
                    'year' => collect(range(1, 12))->mapWithKeys(fn($m) => [Carbon::create()->month($m)->format('F') => ['systolic' => 0, 'diastolic' => 0]])->toArray(),
                    default => [],
                };

                // Merge grouped data with time slots
                foreach ($timeSlots as $key => &$values) {
                    if (isset($groupedData[$key])) {
                        $values['systolic'] = $groupedData[$key]['systolic'];
                        $values['diastolic'] = $groupedData[$key]['diastolic'];
                    }
                }

                // Prepare chart data
                $chartData = collect($timeSlots)->map(fn($values, $time) => [
                    'time' => $time,
                    'systolic' => $values['systolic'],
                    'diastolic' => $values['diastolic'],
                ])->values()->toArray();

                // Extract summary metrics
                $systolicAvg = $aggregations['avg_systolic']['value'] ?? 0;
                $diastolicAvg = $aggregations['avg_diastolic']['value'] ?? 0;
                $lowestSystolic = $aggregations['min_systolic']['value'] ?? 0;
                $highestSystolic = $aggregations['max_systolic']['value'] ?? 0;
                $lowestDiastolic = $aggregations['min_diastolic']['value'] ?? 0;
                $highestDiastolic = $aggregations['max_diastolic']['value'] ?? 0;
            } catch (Exception $e) {
                // Log the error but keep default values
                Log::error('Elasticsearch blood pressure query failed', ['error' => $e->getMessage()]);
            }

            // Elasticsearch Query for pulse data
            $pulseQuery = $query;
            $pulseQuery['body']['query']['bool']['must'][] = ['range' => ['pulse' => ['gte' => 60, 'lte' => 100]]];
            $pulseQuery['body']['aggs']['avg_pulse'] = ['avg' => ['field' => 'pulse']];
            $pulseQuery['body']['aggs']['min_pulse'] = ['min' => ['field' => 'pulse']];
            $pulseQuery['body']['aggs']['max_pulse'] = ['max' => ['field' => 'pulse']];

            // Try to perform Elasticsearch search for pulse data
            try {
                $pulseResponse = $this->elasticsearch->search($pulseQuery);
                $pulseAggregations = $pulseResponse['aggregations'];
                $averagePulse = $pulseAggregations['avg_pulse']['value'] ?? 0;
                $lowestPulse = $pulseAggregations['min_pulse']['value'] ?? 0;
                $highestPulse = $pulseAggregations['max_pulse']['value'] ?? 0;
            } catch (Exception $e) {
                // Log the error but keep default values
                Log::error('Elasticsearch pulse query failed', ['error' => $e->getMessage()]);
            }

            // Return response
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    'summary_dashboard' => [
                        'count' => intval($averagePulse),
                        'chart' => $chartData,
                        'lowest_systolic' => strval($lowestSystolic),
                        'highest_systolic' => strval($highestSystolic),
                        'systolic_avg' => round($systolicAvg),
                        'highest_diastolic' => strval($highestDiastolic),
                        'lowest_diastolic' => strval($lowestDiastolic),
                        'diastolic_avg' => round($diastolicAvg),
                        'lowest_pulse' => strval($lowestPulse),
                        'highest_pulse' => strval($highestPulse),
                        'pulse' => intval($averagePulse),
                    ],
                ],
                'time_stamp' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            $message = 'Something went wrong';
            Log::error($message, ['detail' => (string) $e->getMessage(), 'time_stamp' => now()]);

            return response()->json([
                'status_code' => 500,
                'message' => $message,
                'data' => null,
                'time_stamp' => now(),
            ], 500);
        }
    }



    public function storeBloodPressureActivity(Request $request)
    {
        // Log::info('storeBloodPressureActivity request from smartphone : ' . json_encode($request->json()->all()));
        try {
            $auth = Auth::guard('api')->user();

            $date = now();
            $bp = [
                'systolic' => $request->systolic,
                'diastolic' => $request->diastolic,
                'pulse' => $request->pulse,
                'recorded_at' => $date,
                'device_address' => $request->input('device_address'),
                'is_coming_from_gateway' => false,
                'measureId' => $request->measureId ? $request->measureId : null,
                'patient_id' => null,
                'user_customer_id' => $auth->id,
                'serial_number_gateway' => null,
            ];

            $this->elasticsearch->save($bp, 'blood_pressure_activity_index');
            ActivityService::insertAllActivityUser([
                'recorded_at' => $date,
                'device_address' => $request->input('device_address'),
                'is_coming_from_gateway' => false,
                'measureId' => $request->measureId ? $request->measureId : null,
                'user_customer_id' => $auth->id,
                'systolic' => $request->systolic,
                'diastolic' => $request->diastolic,
                'pulse' => $request->pulse,
                'request_body' => json_encode($request->all())
            ]);
            if ($request->history_data_systolic && $request->history_data_diastolic) {
                if (count($request->history_data_systolic) > 0 && count($request->history_data_diastolic) > 0) {
                    $historyData = [$request->history_data_systolic, $request->history_data_diastolic];
                    ActivitySyncHistoryJob::dispatch($historyData, 'blood_pressure', $request->all(), $auth->id);
                }
            }

            $activty = new \stdClass();
            $activty->systolic = $request->systolic;
            $activty->diastolic = $request->diastolic;
            $activty->recorded_at = $date;
            $getCustomersEmergency = ActivityService::getCustomerEmergencyByCustomerIdGeneralPurpose($auth->id, $activty, true);
            // Log::info('Sending Emergency Customer General Purpose# ' . json_encode($getCustomersEmergency));
            if (count($getCustomersEmergency) > 0) {
                AlarmEmergencyGeneralPurposeJob::dispatch($getCustomersEmergency);
            }


            $getCustomersEmergency = ActivityService::getCustomerEmergencyByCustomerIdPublicWelfare($auth->id, $activty);
            Log::info('Sending Emergency Customer Public Welfare# ' . json_encode($getCustomersEmergency));
            if (count($getCustomersEmergency) > 0) {
                AlarmEmergencyPublicWelfareJob::dispatch($getCustomersEmergency);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => null,
                'time_stamp' => now(),
            ]);
        } catch (Exception $e) {
            $message = 'something wrong';
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

    public function getRecords(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $perPage = $request->input('per_page', 10); // Default to 10 records per page
            $page = $request->input('page', 1); // Default to the first page
            $from = ($page - 1) * $perPage; // Calculate offset for pagination

            $filterBy = 'user_customer_id';
            $filterValue = $auth->id;

            $patient = PatientHospital::where('status', 'IN_PATIENT')
                ->where('user_customer_id', $auth->id)->first();
            if ($patient) {
                $filterBy = 'patient_id';
                $filterValue = $patient->id;
            }

            // Elasticsearch query
            $query = [
                'index' => 'blood_pressure_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => [$filterBy => $filterValue]]
                            ]
                        ]
                    ],
                    'size' => $perPage,
                    'from' => $from,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ];

            // Execute Elasticsearch search
            try {
                $response = $this->elasticsearch->search($query);
                $records = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);

                $totalRecords = $response['hits']['total']['value'] ?? 0; // Total records count
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on getRecords: ' . $e->getMessage());
                $records = collect(); // Empty collection in case of error
                $totalRecords = 0;
            }

            // Prepare paginated response format
            $paginatedResult = [
                'current_page' => $page,
                'data' => $records,
                'first_page_url' => url()->current() . '?page=1&per_page=' . $perPage,
                'from' => $from + 1,
                'last_page' => ceil($totalRecords / $perPage),
                'last_page_url' => url()->current() . '?page=' . ceil($totalRecords / $perPage) . '&per_page=' . $perPage,
                'next_page_url' => $page < ceil($totalRecords / $perPage) ? url()->current() . '?page=' . ($page + 1) . '&per_page=' . $perPage : null,
                'path' => url()->current(),
                'per_page' => $perPage,
                'prev_page_url' => $page > 1 ? url()->current() . '?page=' . ($page - 1) . '&per_page=' . $perPage : null,
                'to' => $from + count($records),
                'total' => $totalRecords
            ];

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => $paginatedResult,
                'time_stamp' => now()->toISOString(),
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

}