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

class OxygenSaturationController extends Controller
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

            // Determine date range based on time period
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
                'index' => 'oxygen_saturation_activity_index',
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
                                ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]], // Valid Oxygen Saturation range
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
                                'avg_saturation' => ['avg' => ['field' => 'saturation']],
                                'min_saturation' => ['min' => ['field' => 'saturation']],
                                'max_saturation' => ['max' => ['field' => 'saturation']],
                            ],
                        ],
                        'avg_saturation' => ['avg' => ['field' => 'saturation']],
                        'min_saturation' => ['min' => ['field' => 'saturation']],
                        'max_saturation' => ['max' => ['field' => 'saturation']],
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
                            'saturation' => $bucket['avg_saturation']['value'] ?? 0,
                        ],
                    ];
                })->toArray();

                // Generate time slots based on the time period
                $timeSlots = match ($timePeriod) {
                    'day' => collect(range(0, 23))->mapWithKeys(fn($h) => [sprintf('%02d:00', $h) => ['saturation' => 0]])->toArray(),
                    'week' => collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])->mapWithKeys(fn($d) => [$d => ['saturation' => 0]])->toArray(),
                    'month' => collect(range(1, 5))->mapWithKeys(fn($w) => ['Week ' . $w => ['saturation' => 0]])->toArray(),
                    'year' => collect(range(1, 12))->mapWithKeys(fn($m) => [Carbon::create()->month($m)->format('F') => ['saturation' => 0]])->toArray(),
                    default => [],
                };

                // Merge grouped data with time slots
                foreach ($timeSlots as $key => &$values) {
                    if (isset($groupedData[$key])) {
                        $values['saturation'] = $groupedData[$key]['saturation'];
                    }
                }

                // Prepare chart data
                $chartData = collect($timeSlots)->map(fn($values, $time) => [
                    'time' => $time,
                    'count' => $values['saturation'],
                ])->values()->toArray();

                // Extract summary metrics
                $averageSaturation = $aggregations['avg_saturation']['value'] ?? 0;
                $minSaturation = $aggregations['min_saturation']['value'] ?? 0;
                $maxSaturation = $aggregations['max_saturation']['value'] ?? 0;
            } catch (Exception $e) {
                // Log error and keep default values
                Log::error('Elasticsearch oxygen saturation query failed', ['error' => $e->getMessage()]);
                $chartData = [];
                $averageSaturation = 0;
                $minSaturation = 0;
                $maxSaturation = 0;
            }

            // Prepare response data
            $result = [
                'summary_dashboard' => [
                    'count' => $averageSaturation,
                    'chart' => $chartData,
                    'lowest_oxygen_saturation' => $minSaturation,
                    'highest_oxygen_saturation' => $maxSaturation,
                    'average_saturation' => $averageSaturation,
                ],
                'most_recent_yesterday' => [
                    'recent_saturation' => strval($averageSaturation),
                    'range' => "{$minSaturation} %~{$maxSaturation} %",
                    'daily_average' => strval($averageSaturation),
                    'low_oxygen_time' => '0', // Placeholder
                    'sleep' => '0', // Placeholder
                    'apnea_occurrences' => '0', // Placeholder
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
        // Log::info('synchActivityOxygen request from smartphone : ' . json_encode($request->json()->all()));
        try {
            $auth = Auth::guard('api')->user();

            $date = now();
            $data = [
                'device_address' => $request->input('device_address'),
                'user_customer_id' => $auth->id,
                'saturation' => $request->input('saturation'),
                'activity_type' => $request->input('activity_type'),
                'recorded_at' => now(),
                'measureId' => $request->measureId ? $request->measureId : null,
                'is_coming_from_gateway' => false,
                'patient_id' => null,
                'is_history' => false,
                'serial_number_gateway' => null,
            ];
            $this->elasticsearch->save($data, 'oxygen_saturation_activity_index');
            ActivityService::insertAllActivityUser([
                'recorded_at' => $date,
                'device_address' => $request->input('device_address'),
                'is_coming_from_gateway' => false,
                'measureId' => $request->measureId ? $request->measureId : null,
                'user_customer_id' => $auth->id,
                'saturation' => $request->input('saturation'),
                'os_activity_type' => $request->input('activity_type'),
                'request_body' => json_encode($request->all())
            ]);


            if ($request->history_data) {
                if (count($request->history_data) > 0) {
                    ActivitySyncHistoryJob::dispatch($request->history_data, 'oxygen_saturation', $request->all(), $auth->id);
                }
            }

            $activty = new \stdClass();
            $activty->saturation = $request->saturation;
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

            // Check if the user is an in-patient
            $patient = PatientHospital::where('status', 'IN_PATIENT')
                ->where('user_customer_id', $auth->id)
                ->first();
            if ($patient) {
                $filterBy = 'patient_id';
                $filterValue = $patient->id;
            }

            // Construct Elasticsearch query
            $query = [
                'index' => 'oxygen_saturation_activity_index', // Ensure this matches your ES index
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => [$filterBy => $filterValue]]
                            ],
                        ],
                    ],
                    'sort' => [['recorded_at' => ['order' => 'desc']]],
                    'from' => ($page - 1) * $perPage,
                    'size' => $perPage
                ]
            ];

            // Try executing Elasticsearch query
            try {
                $response = $this->elasticsearch->search($query);
                $records = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);
                $totalRecords = $response['hits']['total']['value'] ?? 0;
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on fetching Oxygen Saturation records: ' . $e->getMessage());
                $records = collect();
                $totalRecords = 0;
            }

            // Paginate the results manually
            $result = [
                'total' => $totalRecords,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($totalRecords / $perPage),
                'data' => $records,
            ];

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => $result,
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