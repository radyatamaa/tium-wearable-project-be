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
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Services\ElasticSearchService;

class TemperatureController extends Controller
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
            $date = Carbon::parse($request->input('date'))->startOfDay();
            $timePeriod = $request->input('time_period', 'day'); // Default to 'day'

            // Determine start and end date based on time period
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

            // Check if custom date range is provided
            if ($request->input('start_date') && $request->input('end_date')) {
                $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
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

            // Elasticsearch Query
            $query = [
                'index' => 'body_temperature_activity_index',
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
                                ['term' => [$filterBy => $filterValue]],
                                ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]], // Valid body temperature range
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
                                'avg_temperature' => ['avg' => ['field' => 'temperature']],
                                'min_temperature' => ['min' => ['field' => 'temperature']],
                                'max_temperature' => ['max' => ['field' => 'temperature']],
                            ],
                        ],
                        'avg_temperature' => ['avg' => ['field' => 'temperature']],
                        'min_temperature' => ['min' => ['field' => 'temperature']],
                        'max_temperature' => ['max' => ['field' => 'temperature']],
                    ],
                    'size' => 0, // No need to fetch actual documents
                ],
            ];

            // Try to perform Elasticsearch search
            try {
                $response = $this->elasticsearch->search($query);
                $aggregations = $response['aggregations'];

                // Extract chart data
                $groupedData = collect($aggregations['group_by_time']['buckets'])->mapWithKeys(function ($bucket) {
                    $key = $bucket['key_as_string'];
                    return [
                        $key => [
                            'temperature' => $bucket['avg_temperature']['value'] ?? 0,
                        ],
                    ];
                })->toArray();

                // Generate time slots based on the time period
                $timeSlots = match ($timePeriod) {
                    'day' => collect(range(0, 23))->mapWithKeys(fn($h) => [sprintf('%02d:00', $h) => ['temperature' => 0]])->toArray(),
                    'week' => collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])->mapWithKeys(fn($d) => [$d => ['temperature' => 0]])->toArray(),
                    'month' => collect(range(1, 5))->mapWithKeys(fn($w) => ['Week ' . $w => ['temperature' => 0]])->toArray(),
                    'year' => collect(range(1, 12))->mapWithKeys(fn($m) => [Carbon::create()->month($m)->format('F') => ['temperature' => 0]])->toArray(),
                    default => [],
                };

                // Merge grouped data with time slots
                foreach ($timeSlots as $key => &$values) {
                    if (isset($groupedData[$key])) {
                        $values['temperature'] = $groupedData[$key]['temperature'];
                    }
                }

                // Prepare chart data
                $chartData = collect($timeSlots)->map(fn($values, $time) => [
                    'time' => $time,
                    'count' => $values['temperature'],
                ])->values()->toArray();

                // Extract summary metrics
                $averageTemperature = $aggregations['avg_temperature']['value'] ?? 0;
                $minTemperature = $aggregations['min_temperature']['value'] ?? 0;
                $maxTemperature = $aggregations['max_temperature']['value'] ?? 0;
            } catch (Exception $e) {
                // Log error and keep default values
                Log::error('Elasticsearch body temperature query failed', ['error' => $e->getMessage()]);
                $chartData = [];
                $averageTemperature = 0;
                $minTemperature = 0;
                $maxTemperature = 0;
            }

            // Prepare response data
            $result = [
                'summary_dashboard' => [
                    'count' => doubleval($averageTemperature),
                    'chart' => $chartData,
                    'lowest_body_temperature' => doubleval($minTemperature),
                    'highest_body_temperature' => doubleval($maxTemperature),
                    'average_body_temperature' => doubleval($averageTemperature),
                ],
            ];

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => $result,
                'time_stamp' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            Log::error('Something went wrong', ['detail' => $e->getMessage(), 'time_stamp' => now()]);
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong', 'data' => null, 'time_stamp' => now()], 500);
        }
    }




    public function storeActivity(Request $request)
    {
        // Log::info('synchActivityTemperature request from smartphone : ' . json_encode($request->json()->all()));
        try {
            $auth = Auth::guard('api')->user();

            $date = now();
            $data = [
                'device_address' => $request->input('device_address'),
                'temperature' => $request->input('temperature'),
                'recorded_at' => $date,
                'user_customer_id' => $auth->id,
                'measureId' => $request->measureId ? $request->measureId : null,
                'is_coming_from_gateway' => false,
                'patient_id' => null,
                'is_history' => false,
                'serial_number_gateway' => null,
            ];
            $this->elasticsearch->save($data, 'body_temperature_activity_index');

            ActivityService::insertAllActivityUser([
                'recorded_at' => $date,
                'device_address' => $request->input('device_address'),
                'is_coming_from_gateway' => false,
                'measureId' => $request->measureId ? $request->measureId : null,
                'user_customer_id' => $auth->id,
                'temperature' => $request->input('temperature'),
                'request_body' => json_encode($request->all())
            ]);

            if ($request->history_data) {
                if (count($request->history_data) > 0) {
                    ActivitySyncHistoryJob::dispatch($request->history_data, 'body_temperature', $request->all(), $auth->id);
                }
            }

            $activty = new \stdClass();
            $activty->temperature = $request->temperature;
            $activty->recorded_at = $date;
            $getCustomersEmergency = ActivityService::getCustomerEmergencyByCustomerIdGeneralPurpose($auth->id, $activty);
            // Log::info('Sending Emergency Customer General Purpose# ' . json_encode($getCustomersEmergency));
            if (count($getCustomersEmergency) > 0) {
                AlarmEmergencyGeneralPurposeJob::dispatch($getCustomersEmergency);
            }


            $getCustomersEmergency = ActivityService::getCustomerEmergencyByCustomerIdPublicWelfare($auth->id, $activty);
            // Log::info('Sending Emergency Customer Public Welfare# ' . json_encode($getCustomersEmergency));
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

            $filterBy = 'user_customer_id';
            $filterValue = $auth->id;

            // Determine whether the user is a patient
            $patient = PatientHospital::where('status', 'IN_PATIENT')
                ->where('user_customer_id', $auth->id)->first();
            if ($patient) {
                $filterBy = 'patient_id';
                $filterValue = $patient->id;
            }

            // Elasticsearch Query
            $query = [
                'index' => 'body_temperature_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => [$filterBy => $filterValue]]
                            ]
                        ]
                    ],
                    'sort' => [['recorded_at' => ['order' => 'desc']]],
                    'from' => ($page - 1) * $perPage,
                    'size' => $perPage
                ]
            ];

            // Perform Elasticsearch search with error handling
            try {
                $response = $this->elasticsearch->search($query);
                $records = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on fetching records: ' . $e->getMessage());
                $records = collect();
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => $records,
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