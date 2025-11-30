<?php

namespace App\Http\Controllers;

use App\Models\PatientCallReportGeneralPurpose;
use App\Models\PatientCallReportHospital;
use App\Models\PatientHospital;
use App\Models\UserCustomer;
use App\Services\ElasticSearchService;
use Exception;
use Illuminate\Http\Request;
use App\Models\EmergencyReportGeneralPurpose;
use Illuminate\Support\Facades\Log;
use App\Exports\PatientDataVitalHospitalExport;
use Maatwebsite\Excel\Facades\Excel;

class MeasurementReportController extends Controller
{

    protected $elasticsearch;
    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticsearch = $elasticSearchService;
    }

    public function indexBloodPressure(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $searchQuery = [];

        // Elasticsearch Query Structure
        $query = [
            'index' => 'blood_pressure_activity_index',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => []
                    ]
                ],
                'sort' => [
                    ['recorded_at' => ['order' => 'desc']]
                ],
                'size' => $perPage,
                'from' => ($page - 1) * $perPage
            ]
        ];

        // Handle Search Filtering
        if ($request->filled('search')) {
            $searchQuery = [
                'match_phrase_prefix' => [
                    $request->search_by ?: 'device_address' => $request->search
                ]
            ];
            $query['body']['query']['bool']['must'][] = $searchQuery;
        }

        // Handle Date Range Filtering
        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $dateRangeQuery = [
                    'range' => [
                        'created_at' => [
                            'gte' => $request->start_date_range,
                            'lte' => $request->end_date_range
                        ]
                    ]
                ];
                $query['body']['query']['bool']['must'][] = $dateRangeQuery;
            }
        }

        try {
            $response = $this->elasticsearch->search($query);
            $totalRecords = $response['hits']['total']['value'] ?? 0;
            $results = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);

            // Fetch user customers and patients from MySQL
            $userIds = $results->pluck('user_customer_id')->unique()->filter()->toArray();
            $patientIds = $results->pluck('patient_id')->unique()->filter()->toArray();

            $userCustomers = UserCustomer::whereIn('id', $userIds)->get()->keyBy('id');
            $patients = PatientHospital::whereIn('id', $patientIds)->get()->keyBy('id');

            // Attach User and Patient Data
            $results = $results->map(function ($item) use ($userCustomers, $patients) {
                $item['id'] = 1;
                $item['userCustomer'] = null;
                if (isset($item['user_customer_id'])) {
                    $item['userCustomer'] = $userCustomers[$item['user_customer_id']] ?? null;
                }

                $item['patient'] = null;
                if (isset($item['patient_id'])) {
                    $item['patient'] = $patients[$item['patient_id']] ?? null;
                }
                return $item;
            });

        } catch (\Exception $e) {
            Log::error('Elasticsearch error on Blood Pressure Activity: ' . $e->getMessage());
            $results = collect();
            $totalRecords = 0;
        }

        // **Manually Build Pagination Object** to Mimic Laravel's Paginate
        $datas = new \Illuminate\Pagination\LengthAwarePaginator(
            $results, // Data
            $totalRecords, // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Query params
        );

        return view('log-blood-pressure.index', compact('datas'));
    }



    public function indexTemperature(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $searchQuery = [];

        // Elasticsearch Query Structure
        $query = [
            'index' => 'body_temperature_activity_index',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => []
                    ]
                ],
                'sort' => [
                    ['recorded_at' => ['order' => 'desc']]
                ],
                'size' => $perPage,
                'from' => ($page - 1) * $perPage
            ]
        ];

        // Handle Search Filtering
        if ($request->filled('search')) {
            $searchQuery = [
                'match_phrase_prefix' => [
                    $request->search_by ?: 'device_address' => $request->search
                ]
            ];
            $query['body']['query']['bool']['must'][] = $searchQuery;
        }

        // Handle Date Range Filtering
        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $dateRangeQuery = [
                    'range' => [
                        'recorded_at' => [
                            'gte' => $request->start_date_range,
                            'lte' => $request->end_date_range
                        ]
                    ]
                ];
                $query['body']['query']['bool']['must'][] = $dateRangeQuery;
            }
        }

        try {
            $response = $this->elasticsearch->search($query);
            $totalRecords = $response['hits']['total']['value'] ?? 0;
            $results = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);

            // Fetch user customers and patients from MySQL
            $userIds = $results->pluck('user_customer_id')->unique()->filter()->toArray();
            $patientIds = $results->pluck('patient_id')->unique()->filter()->toArray();

            $userCustomers = UserCustomer::whereIn('id', $userIds)->get()->keyBy('id');
            $patients = PatientHospital::whereIn('id', $patientIds)->get()->keyBy('id');

            // Attach User and Patient Data
            $results = $results->map(function ($item) use ($userCustomers, $patients) {
                $item['id'] = 1;
                $item['userCustomer'] = null;
                if (isset($item['user_customer_id'])) {
                    $item['userCustomer'] = $userCustomers[$item['user_customer_id']] ?? null;
                }

                $item['patient'] = null;
                if (isset($item['patient_id'])) {
                    $item['patient'] = $patients[$item['patient_id']] ?? null;
                }return $item;
            });

        } catch (\Exception $e) {
            Log::error('Elasticsearch error on Temperature Activity: ' . $e->getMessage());
            $results = collect();
            $totalRecords = 0;
        }

        // **Manually Build Pagination Object** to Mimic Laravel's Paginate
        $datas = new \Illuminate\Pagination\LengthAwarePaginator(
            $results, // Data
            $totalRecords, // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Query params
        );

        return view('log-temperature.index', compact('datas'));
    }



    public function indexHeartRate(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $searchQuery = [];

        // Elasticsearch Query Structure
        $query = [
            'index' => 'heart_rate_activity_index',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => []
                    ]
                ],
                'sort' => [
                    ['recorded_at' => ['order' => 'desc']]
                ],
                'size' => $perPage,
                'from' => ($page - 1) * $perPage
            ]
        ];

        // Handle Search Filtering
        if ($request->filled('search')) {
            $searchQuery = [
                'match_phrase_prefix' => [
                    $request->search_by ?: 'device_address' => $request->search
                ]
            ];
            $query['body']['query']['bool']['must'][] = $searchQuery;
        }

        // Handle Date Range Filtering
        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $dateRangeQuery = [
                    'range' => [
                        'recorded_at' => [
                            'gte' => $request->start_date_range,
                            'lte' => $request->end_date_range
                        ]
                    ]
                ];
                $query['body']['query']['bool']['must'][] = $dateRangeQuery;
            }
        }

        try {
            $response = $this->elasticsearch->search($query);
            $totalRecords = $response['hits']['total']['value'] ?? 0;
            $results = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);

            // Fetch user customers and patients from MySQL
            $userIds = $results->pluck('user_customer_id')->unique()->filter()->toArray();
            $patientIds = $results->pluck('patient_id')->unique()->filter()->toArray();

            $userCustomers = UserCustomer::whereIn('id', $userIds)->get()->keyBy('id');
            $patients = PatientHospital::whereIn('id', $patientIds)->get()->keyBy('id');

            // Attach User and Patient Data
            $results = $results->map(function ($item) use ($userCustomers, $patients) {
                $item['id'] = 1;
                $item['userCustomer'] = null;
                if (isset($item['user_customer_id'])) {
                    $item['userCustomer'] = $userCustomers[$item['user_customer_id']] ?? null;
                }

                $item['patient'] = null;
                if (isset($item['patient_id'])) {
                    $item['patient'] = $patients[$item['patient_id']] ?? null;
                }return $item;
            });

        } catch (\Exception $e) {
            Log::error('Elasticsearch error on Heart Rate Activity: ' . $e->getMessage());
            $results = collect();
            $totalRecords = 0;
        }

        // **Manually Build Pagination Object** to Mimic Laravel's Paginate
        $datas = new \Illuminate\Pagination\LengthAwarePaginator(
            $results, // Data
            $totalRecords, // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Query params
        );

        return view('log-heart-rate.index', compact('datas'));
    }



    public function indexOxygen(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $searchQuery = [];

        // Elasticsearch Query Structure
        $query = [
            'index' => 'oxygen_saturation_activity_index',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => []
                    ]
                ],
                'sort' => [
                    ['recorded_at' => ['order' => 'desc']]
                ],
                'size' => $perPage,
                'from' => ($page - 1) * $perPage
            ]
        ];

        // Handle Search Filtering
        if ($request->filled('search')) {
            $searchQuery = [
                'match_phrase_prefix' => [
                    $request->search_by ?: 'device_address' => $request->search
                ]
            ];
            $query['body']['query']['bool']['must'][] = $searchQuery;
        }

        // Handle Date Range Filtering
        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $dateRangeQuery = [
                    'range' => [
                        'recorded_at' => [
                            'gte' => $request->start_date_range,
                            'lte' => $request->end_date_range
                        ]
                    ]
                ];
                $query['body']['query']['bool']['must'][] = $dateRangeQuery;
            }
        }

        try {
            $response = $this->elasticsearch->search($query);
            $totalRecords = $response['hits']['total']['value'] ?? 0;
            $results = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);

            // Fetch user customers and patients from MySQL
            $userIds = $results->pluck('user_customer_id')->unique()->filter()->toArray();
            $patientIds = $results->pluck('patient_id')->unique()->filter()->toArray();

            $userCustomers = UserCustomer::whereIn('id', $userIds)->get()->keyBy('id');
            $patients = PatientHospital::whereIn('id', $patientIds)->get()->keyBy('id');

            // Attach User and Patient Data
            $results = $results->map(function ($item) use ($userCustomers, $patients) {
                $item['id'] = 1;
                $item['userCustomer'] = null;
                if (isset($item['user_customer_id'])) {
                    $item['userCustomer'] = $userCustomers[$item['user_customer_id']] ?? null;
                }

                $item['patient'] = null;
                if (isset($item['patient_id'])) {
                    $item['patient'] = $patients[$item['patient_id']] ?? null;
                }return $item;
            });

        } catch (\Exception $e) {
            Log::error('Elasticsearch error on Oxygen Saturation Activity: ' . $e->getMessage());
            $results = collect();
            $totalRecords = 0;
        }

        // **Manually Build Pagination Object** to Mimic Laravel's Paginate
        $datas = new \Illuminate\Pagination\LengthAwarePaginator(
            $results, // Data
            $totalRecords, // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Query params
        );

        return view('log-oxygen.index', compact('datas'));
    }


    public function indexPatientCallHospital(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = PatientCallReportHospital::orderBy('created_at', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by != '') {
                $query = $query->where($request->search_by, 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('patient_name', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('call_occurrence_time', '>=', $request->start_date_range)
                    ->whereDate('call_occurrence_time', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);

        return view('log-patient-call-hospital.index', compact('datas'));
    }

    public function indexPatientCallPublicWelfare(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = PatientCallReportGeneralPurpose::orderBy('created_at', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by != '') {
                $query = $query->where($request->search_by, 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('name', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('notification_time', '>=', $request->start_date_range)
                    ->whereDate('notification_time', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);

        return view('log-patient-call-public-welfare.index', compact('datas'));
    }

    // public function indexPatientCallGeneralPurpose(Request $request)
    // {
    //     $perPage = $request->input('limit', 10);
    //     $query = PatientCallReportGeneralPurpose::orderBy('created_at', 'desc');


    //     if ($request->filled('search')) {
    //         if ($request->search_by != '') {
    //             $query = $query->where($request->search_by, 'like', '%' . $request->search . '%');
    //         } else {
    //             $query = $query->where('name', 'like', '%' . $request->search . '%');
    //         }
    //     }

    //     if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
    //         if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
    //             $query = $query->whereDate('notification_time', '>=', $request->start_date_range)
    //                 ->whereDate('notification_time', '<=', $request->end_date_range);
    //         }
    //     }

    //     $datas = $query->paginate($perPage);

    //     return view('log-patient-call-general-purpose.index', compact('datas'));
    // }

    public function indexPatientReport(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $query = PatientHospital::with(['subscriptionInformationGeneralPurpose'])->orderBy('created_at', 'desc');


        if ($request->filled('search')) {
            if ($request->search_by != '') {
                $query = $query->where($request->search_by, 'like', '%' . $request->search . '%');
            } else {
                $query = $query->where('name', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $query = $query->whereDate('created_at', '>=', $request->start_date_range)
                    ->whereDate('created_at', '<=', $request->end_date_range);
            }
        }

        $datas = $query->paginate($perPage);

        return view('patient-report.index', compact('datas'));
    }

    public function downloadExcelDataVital(Request $request, $id)
    {
        $patient = PatientHospital::where('id', $id)->first();

        $fileName = 'data-vital-' . $patient->name . '-report' . ' ' . $request->query('end_date_range') . '.xlsx';
        if ($request->filled('type')) {
            $fileName = 'data-vital-' . $patient->name . '-report' . ' ' . $request->query('start_date_range') . ' - ' . $request->query('end_date_range') . '.xlsx';

        }

        return Excel::download(new PatientDataVitalHospitalExport($request, $id, $this->elasticsearch), $fileName);
    }
}