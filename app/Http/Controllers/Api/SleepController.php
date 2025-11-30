<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SleepActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Services\ElasticSearchService;

class SleepController extends Controller
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

            // Determine start and end dates based on time period
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

            // Custom date range if provided
            if ($request->input('start_date') && $request->input('end_date')) {
                $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            }

            // Elasticsearch Query
            $query = [
                'index' => 'sleep_activities_index',
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
                                ['term' => ['user_customer_id' => $auth->id]],
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
                                'avg_sleep' => $this->getSleepAggregationScript('total_sleep')
                            ]
                        ],
                        'avg_total_sleep' => $this->getSleepAggregationScript('total_sleep'),
                        'avg_rem_sleep' => $this->getSleepAggregationScript('rem_sleep'),
                        'avg_light_sleep' => $this->getSleepAggregationScript('light_sleep'),
                        'avg_deep_sleep' => $this->getSleepAggregationScript('deep_sleep'),
                        'total_awakenings' => $this->getAwakeningsAggregationScript(),
                        'avg_onset_efficiency' => ['avg' => ['field' => 'onset_efficiency']],
                        'avg_sleep_efficiency' => ['avg' => ['field' => 'sleep_efficiency']],
                    ],
                    'size' => 0, // No need to fetch actual documents
                ],
            ];

            // Elasticsearch Query Execution
            try {
                $response = $this->elasticsearch->search($query);
                $aggregations = $response['aggregations'];

                // Extract values from aggregations
                $averageTotalSleep = $aggregations['avg_total_sleep']['value'] ?? 0;
                $averageREMSleep = $aggregations['avg_rem_sleep']['value'] ?? 0;
                $averageLightSleep = $aggregations['avg_light_sleep']['value'] ?? 0;
                $averageDeepSleep = $aggregations['avg_deep_sleep']['value'] ?? 0;
                $totalAwakenings = $aggregations['total_awakenings']['value'] ?? 0;
                $sleepOnsetEfficiency = $aggregations['avg_onset_efficiency']['value'] ?? 0;
                $sleepEfficiency = $aggregations['avg_sleep_efficiency']['value'] ?? 0;

                // Extract chart data
                $groupedData = collect($aggregations['group_by_time']['buckets'])->mapWithKeys(function ($bucket) {
                    $key = $bucket['key_as_string'];
                    return [
                        $key => [
                            'total_sleep' => $bucket['avg_sleep']['value'] ?? 0,
                        ],
                    ];
                })->toArray();

                // Generate time slots based on the time period
                $timeSlots = match ($timePeriod) {
                    'day' => collect(range(0, 23))->mapWithKeys(fn($h) => [sprintf('%02d:00', $h) => ['total_sleep' => 0]])->toArray(),
                    'week' => collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])->mapWithKeys(fn($d) => [$d => ['total_sleep' => 0]])->toArray(),
                    'month' => collect(range(1, 5))->mapWithKeys(fn($w) => ['Week ' . $w => ['total_sleep' => 0]])->toArray(),
                    'year' => collect(range(1, 12))->mapWithKeys(fn($m) => [Carbon::create()->month($m)->format('F') => ['total_sleep' => 0]])->toArray(),
                    default => [],
                };

                // Merge grouped data with time slots
                foreach ($timeSlots as $key => &$values) {
                    if (isset($groupedData[$key])) {
                        $values['total_sleep'] = $groupedData[$key]['total_sleep'];
                    }
                }

                // Prepare chart data
                $chartData = collect($timeSlots)->map(fn($values, $time) => [
                    'time' => $time,
                    'count' => $values['total_sleep'],
                ])->values()->toArray();
            } catch (Exception $e) {
                // Log the error but keep default values
                Log::error('Elasticsearch sleep query failed', ['error' => $e->getMessage()]);
                $chartData = [];
                $averageTotalSleep = 0;
                $averageREMSleep = 0;
                $averageLightSleep = 0;
                $averageDeepSleep = 0;
                $totalAwakenings = 0;
                $sleepOnsetEfficiency = 0;
                $sleepEfficiency = 0;
            }

            // Return response
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    'summary_dashboard' => [
                        'chart' => $chartData,
                    ],
                    'most_recent_yesterday' => [
                        'recent_sleep' => doubleval($averageTotalSleep),
                        'number_of_awakenings' => intval($totalAwakenings),
                        'average_rem_sleep' => doubleval($averageREMSleep),
                        'average_light_sleep' => doubleval($averageLightSleep),
                        'average_deep_sleep' => doubleval($averageDeepSleep),
                        'sleep_onset_efficiency' => doubleval($sleepOnsetEfficiency),
                        'sleep_efficiency' => doubleval($sleepEfficiency),
                    ]
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



    /**
     * Helper function to get scripted_metric aggregation for sleep duration in HH:MM format
     */

    private function getSleepAggregationScript($field)
    {
        return [
            'scripted_metric' => [
                'init_script' => "state.total = 0.0; state.count = 0;",
                'map_script' => "
                    if (doc.containsKey('$field.keyword') && doc['$field.keyword'].size() > 0) {
                        def value = String.valueOf(doc['$field.keyword'].value);
                        if (value.indexOf(\":\") > -1) { 
                            def hourPart = value.substring(0, value.indexOf(\":\")); 
                            def minutePart = value.substring(value.indexOf(\":\") + 1); 
                            try {
                                def hours = Integer.parseInt(hourPart);
                                def minutes = Integer.parseInt(minutePart);
                                state.total += hours + (minutes / 60.0);
                                state.count += 1;
                            } catch (Exception e) {
                                // Ignore parsing errors
                            }
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
        ];
    }

    /**
     * Helper function to get scripted_metric aggregation for awakenings (convert string to integer)
     */
    private function getAwakeningsAggregationScript()
    {
        return [
            'scripted_metric' => [
                'init_script' => "state.total = 0;",
                'map_script' => "
                    if (doc.containsKey('awakenings.keyword') && doc['awakenings.keyword'].size() > 0) {
                        def value = String.valueOf(doc['awakenings.keyword'].value);
                        try {
                            state.total += Integer.parseInt(value);
                        } catch (Exception e) {
                            // Ignore parsing errors
                        }
                    }
                ",
                'combine_script' => "return state.total;",
                'reduce_script' => "
                    int sum = 0;
                    for (s in states) { 
                        sum += s;
                    }
                    return sum;
                "
            ]
        ];
    }











    /**
     * Convert time string (HH:MM) to total hours.
     */
    private function timeToHours($time)
    {
        if (is_numeric($time)) {
            // Return the time if already in hours
            return $time;
        }

        if (is_string($time) && strpos($time, ':') !== false) {
            list($hours, $minutes) = explode(':', $time);
            return $hours + ($minutes / 60);
        }

        return 0;
    }




    public function storeActivity(Request $request)
    {
        // Log::info('synchActivitySleep request from smartphone : ' . json_encode($request->json()->all()));
        try {
            $auth = Auth::guard('api')->user();

            $data = [
                'device_address' => $request->input('device_address'),
                'total_sleep' => $request->input('total_sleep'),
                'awakenings' => $request->input('awakenings'),
                'bedtime' => $request->input('bedtime'),
                'rem_sleep' => $request->input('rem_sleep'),
                'light_sleep' => $request->input('light_sleep'),
                'deep_sleep' => $request->input('deep_sleep'),
                'onset_efficiency' => $request->input('onset_efficiency'),
                'sleep_efficiency' => $request->input('sleep_efficiency'),
                'recorded_at' => now(),
                'user_customer_id' => $auth->id
            ];

            $user = SleepActivity::create($data);
            $this->elasticsearch->save($data, 'sleep_activities_index');

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
}