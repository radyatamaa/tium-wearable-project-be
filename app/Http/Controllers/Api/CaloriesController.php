<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCustomer;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use App\Services\ElasticSearchService;

class CaloriesController extends Controller
{
    protected $elasticsearch;
    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticsearch = $elasticSearchService;
    }
    public function store(Request $request)
    {
        // Log::info('synchActivityCalories request from smartphone : ' . json_encode($request->json()->all()));
        try {
            $auth = Auth::guard('api')->user();

            $data = [
                'user_customer_id' => $auth->id,
                'device_address' => $request->device_address,
                'recorded_at' => now(),
                'calories' => $request->calories,
                'step' => $request->step,
                'distance' => $request->distance,
                'serial_number_gateway' => null,
            ];
            $this->elasticsearch->save($data, 'calories_activity_index');

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => null,
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

    public function getSummary(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();
            $date = Carbon::parse($request->input('date'))->startOfDay();
            $timePeriod = $request->input('time_period', 'day'); // Default to 'day'

            // Fetch user goal settings
            $goals = GoalSetting::where('user_customer_id', $auth->id)->first();

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

            // Elasticsearch Query
            $query = [
                'index' => 'calories_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'range' => [
                                        'recorded_at' => [
                                            'gte' => $startDate->toIso8601String(),
                                            'lte' => $endDate->toIso8601String(),
                                        ],
                                    ]
                                ],
                                ['term' => ['user_customer_id' => $auth->id]]
                            ],
                        ],
                    ],
                    'aggs' => [
                        'group_by_time' => [
                            'date_histogram' => [
                                'field' => 'recorded_at',
                                'calendar_interval' => match ($timePeriod) {
                                    'day' => 'hour',
                                    'week' => 'day',
                                    'month' => 'week',
                                    'year' => 'month',
                                    default => 'hour',
                                },
                                'format' => match ($timePeriod) {
                                    'day' => 'HH:00',
                                    'week' => 'EEEE',
                                    'month' => "'Week' w",
                                    'year' => 'MMMM',
                                    default => 'HH:00',
                                },
                                'time_zone' => 'Asia/Seoul',
                            ],
                            'aggs' => [
                                'avg_calories' => [
                                    'scripted_metric' => [
                                        'init_script' => "state.total = 0.0; state.count = 0;",
                                        'map_script' => "
                                            if (doc.containsKey('calories.keyword') && doc['calories.keyword'].size() > 0) {
                                                def value = doc['calories.keyword'].value;
                                                try {
                                                    state.total += Double.parseDouble(value);
                                                    state.count += 1;
                                                } catch (Exception e) {}
                                            }
                                        ",
                                        'combine_script' => "return state;",
                                        'reduce_script' => "
                                            double sum = 0.0;
                                            double count = 0;
                                            for (s in states) { 
                                                if (s != null && s.total != null && s.count != null) { 
                                                    sum += s.total; 
                                                    count += s.count; 
                                                }
                                            }
                                            return count > 0 ? sum / count : 0.0;
                                        "
                                    ]
                                ],
                                'avg_step' => [
                                    'scripted_metric' => [
                                        'init_script' => "state.total = 0.0; state.count = 0;",
                                        'map_script' => "
                                            if (doc.containsKey('step.keyword') && doc['step.keyword'].size() > 0) {
                                                def value = doc['step.keyword'].value;
                                                try {
                                                    state.total += Double.parseDouble(value);
                                                    state.count += 1;
                                                } catch (Exception e) {}
                                            }
                                        ",
                                        'combine_script' => "return state;",
                                        'reduce_script' => "
                                            double sum = 0.0;
                                            double count = 0;
                                            for (s in states) { 
                                                if (s != null && s.total != null && s.count != null) { 
                                                    sum += s.total; 
                                                    count += s.count; 
                                                }
                                            }
                                            return count > 0 ? sum / count : 0.0;
                                        "
                                    ]
                                ],
                                'avg_distance' => [
                                    'scripted_metric' => [
                                        'init_script' => "state.total = 0.0; state.count = 0;",
                                        'map_script' => "
                                            if (doc.containsKey('distance.keyword') && doc['distance.keyword'].size() > 0) {
                                                def value = doc['distance.keyword'].value;
                                                try {
                                                    state.total += Double.parseDouble(value);
                                                    state.count += 1;
                                                } catch (Exception e) {}
                                            }
                                        ",
                                        'combine_script' => "return state;",
                                        'reduce_script' => "
                                            double sum = 0.0;
                                            double count = 0;
                                            for (s in states) { 
                                                if (s != null && s.total != null && s.count != null) { 
                                                    sum += s.total; 
                                                    count += s.count; 
                                                }
                                            }
                                            return count > 0 ? sum / count : 0.0;
                                        "
                                    ]
                                ]
                            ],
                        ],
                        'avg_calories' => [
                            'scripted_metric' => [
                                'init_script' => "state.total = 0.0; state.count = 0;",
                                'map_script' => "
                                    if (doc.containsKey('calories.keyword') && doc['calories.keyword'].size() > 0) {
                                        def value = doc['calories.keyword'].value;
                                        try {
                                            state.total += Double.parseDouble(value);
                                            state.count += 1;
                                        } catch (Exception e) {
                                            // Ignore parsing errors
                                        }
                                    }
                                ",
                                'combine_script' => "return state;",
                                'reduce_script' => "
                                    double sum = 0.0;
                                    double count = 0;
                                    for (s in states) { 
                                        if (s != null && s.total != null && s.count != null) { 
                                            sum += s.total; 
                                            count += s.count; 
                                        }
                                    }
                                    return count > 0 ? sum / count : 0.0;
                                "
                            ]
                        ],
                        'avg_step' => [
                            'scripted_metric' => [
                                'init_script' => "state.total = 0.0; state.count = 0;",
                                'map_script' => "
                                    if (doc.containsKey('step.keyword') && doc['step.keyword'].size() > 0) {
                                        def value = doc['step.keyword'].value;
                                        try {
                                            state.total += Double.parseDouble(value);
                                            state.count += 1;
                                        } catch (Exception e) {
                                            // Ignore parsing errors
                                        }
                                    }
                                ",
                                'combine_script' => "return state;",
                                'reduce_script' => "
                                    double sum = 0.0;
                                    double count = 0;
                                    for (s in states) { 
                                        if (s != null && s.total != null && s.count != null) { 
                                            sum += s.total; 
                                            count += s.count; 
                                        }
                                    }
                                    return count > 0 ? sum / count : 0.0;
                                "
                            ]
                        ],
                        'avg_distance' => [
                            'scripted_metric' => [
                                'init_script' => "state.total = 0.0; state.count = 0;",
                                'map_script' => "
                                    if (doc.containsKey('distance.keyword') && doc['distance.keyword'].size() > 0) {
                                        def value = doc['distance.keyword'].value;
                                        try {
                                            state.total += Double.parseDouble(value);
                                            state.count += 1;
                                        } catch (Exception e) {
                                            // Ignore parsing errors
                                        }
                                    }
                                ",
                                'combine_script' => "return state;",
                                'reduce_script' => "
                                    double sum = 0.0;
                                    double count = 0;
                                    for (s in states) { 
                                        if (s != null && s.total != null && s.count != null) { 
                                            sum += s.total; 
                                            count += s.count; 
                                        }
                                    }
                                    return count > 0 ? sum / count : 0.0;
                                "
                            ]
                        ]
                    ],
                    'size' => 0,
                ],
            ];

            // Elasticsearch Query Execution
            try {
                $response = $this->elasticsearch->search($query);
                $aggregations = $response['aggregations'];

                // Extract grouped data
                $groupedData = collect($aggregations['group_by_time']['buckets'])->mapWithKeys(function ($bucket) {
                    return [
                        $bucket['key_as_string'] => [
                            'calories' => $bucket['avg_calories']['value'] ?? 0,
                            'step' => $bucket['avg_step']['value'] ?? 0,
                            'distance' => $bucket['avg_distance']['value'] ?? 0,
                        ],
                    ];
                })->toArray();

                $groupedDataAVG = collect($aggregations['group_by_time']['buckets'])->map(function ($bucket) {
                    return [
                        'avg_calories' => $bucket['avg_calories']['value'] ?? 0,
                        'avg_step' => $bucket['avg_step']['value'] ?? 0,
                        'avg_distance' => $bucket['avg_distance']['value'] ?? 0,
                    ];
                });

                // Generate time slots
                $timeSlots = match ($timePeriod) {
                    'day' => collect(range(0, 23))->mapWithKeys(fn($h) => [sprintf('%02d:00', $h) => ['calories' => 0, 'step' => 0, 'distance' => 0]])->toArray(),
                    'week' => collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])
                        ->mapWithKeys(fn($d) => [$d => ['calories' => 0, 'step' => 0, 'distance' => 0]])->toArray(),
                    'month' => collect(range(1, 5))->mapWithKeys(fn($w) => ['Week ' . $w => ['calories' => 0, 'step' => 0, 'distance' => 0]])->toArray(),
                    'year' => collect(range(1, 12))->mapWithKeys(fn($m) => [Carbon::create()->month($m)->format('F') => ['calories' => 0, 'step' => 0, 'distance' => 0]])->toArray(),
                    default => [],
                };

                // Merge grouped data with time slots
                foreach ($timeSlots as $key => &$values) {
                    if (isset($groupedData[$key])) {
                        $values['calories'] = $groupedData[$key]['calories'];
                        $values['step'] = $groupedData[$key]['step'];
                        $values['distance'] = $groupedData[$key]['distance'];
                    }
                }

                // Prepare chart data
                $chartDataCalories = collect($timeSlots)->map(fn($values, $time) => ['time' => $time, 'count' => $values['calories']])->values()->toArray();
                $chartDataStep = collect($timeSlots)->map(fn($values, $time) => ['time' => $time, 'count' => $values['step']])->values()->toArray();
                $chartDataDistance = collect($timeSlots)->map(fn($values, $time) => ['time' => $time, 'count' => $values['distance']])->values()->toArray();

                return response()->json([
                    'status_code' => 200,
                    'message' => 'Success',
                    'data' => [
                        'activity_log' => [
                            'average_calories' => $groupedDataAVG->avg('avg_calories'),
                            'average_step' => $groupedDataAVG->avg('avg_step'),
                            'average_distance' => $groupedDataAVG->avg('avg_distance'),
                            'target_calories' => $goals->target_calories ?? 0,
                            'target_step' => $goals->target_step ?? 0,
                            'target_distance' => $goals->target_distance ?? 0,
                            'average_respiratory_rate' => '0',
                            'minimum_respiratory_rate' => '0',
                            'maximum_respiratory_rate' => '0'
                        ],
                        'summary_dashboard_calories' => [
                            'current' => $groupedDataAVG->avg('avg_calories'),
                            'goal' => $goals->target_calories ?? 0,
                            'chart' => $chartDataCalories
                        ],
                        'summary_dashboard_step' => [
                            'current' => $groupedDataAVG->avg('avg_step'),
                            'goal' => $goals->target_step ?? 0,
                            'chart' => $chartDataStep
                        ],
                        'summary_dashboard_distance' => [
                            'current' => $groupedDataAVG->avg('avg_distance'),
                            'goal' => $goals->target_distance ?? 0,
                            'chart' => $chartDataDistance
                        ],
                    ],
                ]);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Error fetching summary data'], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing the request: ' . $e->getMessage(),
            ], 500);
        }
    }





}