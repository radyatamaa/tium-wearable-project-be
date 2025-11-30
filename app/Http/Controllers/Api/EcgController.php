<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AlarmEmergencyGeneralPurposeJob;
use App\Jobs\AlarmEmergencyPublicWelfareJob;
use App\Models\AllActivityUser;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCustomer;
use App\Models\EcgActivity;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use App\Services\ElasticSearchService;

class EcgController extends Controller
{
    protected $elasticsearch;
    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticsearch = $elasticSearchService;
    }
    public function getSummaryEcg(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();
            $date = Carbon::parse($request->input('date'))->startOfDay();
            $timePeriod = $request->input('time_period', 'day'); // Default ke 'day'

            // Tentukan rentang tanggal berdasarkan periode waktu
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

            // Cek jika pengguna memberikan rentang tanggal kustom
            if ($request->input('start_date') && $request->input('end_date')) {
                $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            }

            // Elasticsearch Query
            $query = [
                'index' => 'ecg_activity_index',
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
                                'latest_ecg' => [
                                    'top_hits' => [
                                        'size' => 1,
                                        'sort' => [['recorded_at' => ['order' => 'desc']]],
                                        '_source' => ['ecg_data']
                                    ]
                                ],
                                'avg_bpm' => [
                                    'avg' => ['field' => 'bpm']
                                ],
                            ],
                        ],
                        'overall_avg_bpm' => [
                            'avg' => ['field' => 'bpm']
                        ]
                    ],
                    'size' => 0, // Tidak perlu mengambil data individual
                ],
            ];

            // Lakukan pencarian di Elasticsearch
            try {
                $response = $this->elasticsearch->search($query);
                $aggregations = $response['aggregations'];

                // Ambil data yang dikelompokkan berdasarkan waktu
                $groupedData = collect($aggregations['group_by_time']['buckets'])->mapWithKeys(function ($bucket) use ($timePeriod) {
                    $key = $bucket['key_as_string'];
                    $latestEcg = $bucket['latest_ecg']['hits']['hits'][0]['_source']['ecg_data'] ?? '[]';
                    $avgBpm = $bucket['avg_bpm']['value'] ?? 0;

                    return [
                        $key => [
                            'values' => json_decode($latestEcg, true) ?? [],
                            'bpm' => intval($avgBpm),
                        ],
                    ];
                })->toArray();

                // Inisialisasi struktur waktu
                $timeSlots = match ($timePeriod) {
                    'day' => collect(range(0, 23))->mapWithKeys(fn($h) => [sprintf('%02d:00', $h) => ['values' => [], 'bpm' => 0]])->toArray(),
                    'week' => collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])
                        ->mapWithKeys(fn($d) => [$d => ['values' => [], 'bpm' => 0]])->toArray(),
                    'month' => collect(range(1, 5))->mapWithKeys(fn($w) => ['Week ' . $w => ['values' => [], 'bpm' => 0]])->toArray(),
                    'year' => collect(range(1, 12))->mapWithKeys(fn($m) => [Carbon::create()->month($m)->format('F') => ['values' => [], 'bpm' => 0]])->toArray(),
                    default => [],
                };

                // Gabungkan data hasil pencarian dengan slot waktu
                foreach ($timeSlots as $key => &$values) {
                    if (isset($groupedData[$key])) {
                        $values['values'] = $groupedData[$key]['values'];
                        $values['bpm'] = $groupedData[$key]['bpm'];
                    }
                }

                // Format data untuk chart
                $chartData = collect($timeSlots)->map(fn($values, $time) => [
                    'time' => $time,
                    'values' => $values['values'],
                    'bpm' => $values['bpm']
                ])->values()->toArray();

                // Ambil nilai rata-rata keseluruhan bpm
                $overallBpm = intval($aggregations['overall_avg_bpm']['value'] ?? 0);

                // Format hasil akhir
                $result = [
                    'summary_dashboard' => [
                        'bpm' => $overallBpm,
                        'chart' => $chartData,
                    ],
                ];
            } catch (Exception $e) {
                Log::error('Elasticsearch ECG query failed', ['error' => $e->getMessage()]);

                // Jika terjadi error, tetap kembalikan struktur response dengan nilai default
                $result = [
                    'summary_dashboard' => [
                        'bpm' => 0,
                        'chart' => [],
                    ],
                ];
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => $result,
                'time_stamp' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            Log::error('Something went wrong', ['detail' => $e->getMessage(), 'time_stamp' => now()]);
            return response()->json([
                'status_code' => 500,
                'message' => 'Something went wrong',
                'data' => null,
                'time_stamp' => now(),
            ], 500);
        }
    }






    public function storeEcgActivity(Request $request)
    {
        // Log::info('synchActivityEcg request from smartphone : ' . json_encode($request->json()->all()));
        try {
            $auth = Auth::guard('api')->user();

            $date = now();
            $data = [
                'user_customer_id' => $auth->id,
                'ecg_data' => json_encode($request->ecg_data), // Assuming ecg_data is an array
                'device_address' => $request->device_address,
                'recorded_at' => $date,
                'activity_type' => $request->activity_type,
                'measureId' => $request->measureId ? $request->measureId : null,
                'bpm' => $request->bpm,
                'speed' => $request->speed,
                'frequency' => $request->frequency,
                'is_coming_from_gateway' => false,
                'patient_id' => null,
                'serial_number_gateway' => null,
            ];

            $ecgActivity = EcgActivity::create($data);
            $this->elasticsearch->save($data, 'ecg_activity_index');

            $activty = new \stdClass();
            $activty->bpmEcg = $request->bpm;
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

    public function getRecords(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $perPage = $request->input('per_page', 10); // Default to 10 records per page
            $page = $request->input('page', 1); // Default to the first page

            $date = Carbon::parse($request->input('date'))->startOfDay();
            $timePeriod = $request->input('time_period', ''); // Default to 'day'

            // Determine the start and end dates based on the time period
            if ($timePeriod === 'week') {
                $startDate = $date->copy()->startOfWeek();
                $endDate = $date->copy()->endOfWeek();
            } elseif ($timePeriod === 'month') {
                $startDate = $date->copy()->startOfMonth();
                $endDate = $date->copy()->endOfMonth();
            } elseif ($timePeriod === 'day') {
                // Default to 'day'
                $startDate = $date;
                $endDate = $date->copy()->endOfDay();
            } else {
                $startDate = null;
                $endDate = null;
            }

            // Custom date range provided by the user
            if ($request->input('start_date') && $request->input('end_date')) {
                $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            }

            $query = EcgActivity::where('user_customer_id', $auth->id);
            if ($startDate && $endDate) {
                $query = $query->whereBetween('recorded_at', [$startDate, $endDate]);
            }
            $result = $query
                ->orderBy('recorded_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $result = $result->toArray();

            foreach ($result['data'] as &$item) {
                $item['ecg_data'] = json_decode($item['ecg_data'], true);
            }
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