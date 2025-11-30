<?php

namespace App\Http\Controllers;

use App\Models\UserManagementGeneralPurpose;
use App\Services\ElasticSearchService;
use Illuminate\Http\Request;
use App\Models\EmergencyReportGeneralPurpose;
use App\Models\SosContact;
use App\Models\MeasurementSettingsGeneralPurpose;
use Carbon\Carbon;
use PDF;
use App\Exports\BodyMeasurementExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Exception;

class PatientManagementGeneralPurposeController extends Controller
{
    protected $elasticsearch;

    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticsearch = $elasticSearchService;
    }

    public function index(Request $request, $id)
    {
        $userManagement = UserManagementGeneralPurpose::with('userCustomer')->where('user_customer_id', $id)->first();

        $data = [
            'basic_information' => [
                'user_customer_id' => $userManagement->userCustomer->id,
                'name' => $userManagement->name,
                'gender' => $userManagement->gender,
                'height' => $userManagement->userCustomer->height,
                'smartwatch_code' => $userManagement->device_code,
                'age' => $userManagement->age,
                'weight' => $userManagement->userCustomer->weight,
                'contact' => $userManagement->userCustomer->phone_number,
                'emergency_contact' => $userManagement->emergency_contact_1,
            ],
        ];

        $bmi = $this->getBodyMeasurementInformation($request, $id);
        $data['body_measurement_information'] = json_decode($bmi->getContent(), true);
        $data['body_measurement_information_chart'] = $this->getBodyMeasurementInformationChart($request, $id);

        return view('general-purpose.patient-management.index', ['data' => $data, 'id' => $id]);
    }

    public function getBodyMeasurementInformation(Request $request, $id)
    {
        $startDate = Carbon::now()->startOfDay();
        $endDate = $startDate->copy()->endOfDay();

        if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
            if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                $startDate = Carbon::parse($request->query('start_date_range'));
                $endDate = Carbon::parse($request->query('end_date_range'));
            }
        }

        $format = $request->query('format_time', 'H:i');
        $measurementSettingsGeneralPurpose = MeasurementSettingsGeneralPurpose::first();

        $indices = [
            'heart_rate' => 'heart_rate_activity_index',
            'body_temperature' => 'body_temperature_activity_index',
            'blood_pressure' => 'blood_pressure_activity_index',
            'oxygen_saturation' => 'oxygen_saturation_activity_index',
            'respiratory_rate' => 'respiratory_rate_activity_index',
            'ecg' => 'ecg_activity_index',
        ];

        $activities = [];

        foreach ($indices as $key => $index) {
            $query = [
                'index' => $index,
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
                                ['term' => ['user_customer_id' => $id]]
                            ]
                        ]
                    ],
                    'size' => 10000,
                    'sort' => [['recorded_at' => ['order' => 'desc']]],
                ]
            ];

            switch ($key) {
                case 'heart_rate':
                case 'ecg':
                    $query['body']['query']['bool']['must'][] = ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]];
                    break;
                case 'body_temperature':
                    $query['body']['query']['bool']['must'][] = ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]];
                    break;
                case 'blood_pressure':
                    $query['body']['query']['bool']['must'][] = ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]];
                    $query['body']['query']['bool']['must'][] = ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]];
                    break;
                case 'oxygen_saturation':
                    $query['body']['query']['bool']['must'][] = ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]];
                    break;
                case 'respiratory_rate':
                    $query['body']['query']['bool']['must'][] = ['range' => ['rate' => ['gte' => 6, 'lte' => 40]]];
                    break;
            }

            try {
                $response = $this->elasticsearch->search($query);
                $data = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);
            } catch (Exception $e) {
                Log::error("Elasticsearch error on {$key}: " . $e->getMessage());
                $data = collect();
            }

            foreach ($data as $activity) {
                $time = Carbon::parse($activity['recorded_at'])->format($format);
                $recordedAt = Carbon::parse($activity['recorded_at'])->toIso8601String();

                if (!isset($activities[$time])) {
                    $activities[$time] = [
                        'time' => $time,
                        'recorded_at' => $recordedAt,
                        'heart_rate' => null,
                        'body_temperature' => null,
                        'blood_pressure' => null,
                        'oxygen_saturation' => null,
                        'respiratory_rate' => null,
                        'ecg' => null,
                        'state' => ''
                    ];
                }

                $state = $this->determineState($activity, $measurementSettingsGeneralPurpose, $key);
                $activities[$time]['state'] = $state;

                switch ($key) {
                    case 'heart_rate':
                        $activities[$time]['heart_rate'] = $activity['bpm'];
                        break;
                    case 'body_temperature':
                        $activities[$time]['body_temperature'] = $activity['temperature'];
                        break;
                    case 'blood_pressure':
                        $activities[$time]['blood_pressure'] = $activity['systolic'] . ' / ' . $activity['diastolic'];
                        break;
                    case 'oxygen_saturation':
                        $activities[$time]['oxygen_saturation'] = $activity['saturation'];
                        break;
                    case 'respiratory_rate':
                        $activities[$time]['respiratory_rate'] = $activity['rate'];
                        break;
                    case 'ecg':
                        $activities[$time]['ecg'] = $activity['bpm'];
                        break;
                }
            }
        }

        if ($format == 'H:i') {
            krsort($activities);
        }

        return response()->json($activities);
    }

    public function downloadMeasurementInformationPDF(Request $request, $id)
    {
        ini_set('memory_limit', '256M');
        // Get the body measurement information
        $response = $this->getBodyMeasurementInformation($request, $id);

        // Decode the JSON response to get the data
        $activities = json_decode($response->getContent(), true);

        // Load the view and pass the activities data
        $pdf = PDF::loadView('pdf.general-purpose.body_measurement_history', compact('activities'));

        // Download the generated PDF
        return $pdf->download('body_measurement_information.pdf');
    }

    public function downloadMeasurementInformationExcel(Request $request, $id)
    {
        // Fetch the body measurement information
        $response = $this->getBodyMeasurementInformation($request, $id);
        $activities = json_decode($response->getContent(), true);

        // Create the Excel export
        return Excel::download(new BodyMeasurementExport($activities, ['recorded_at', 'state']), 'body_measurement_information.xlsx');
    }


    public function getBodyMeasurementInformationChart(Request $request, $id)
    {
        $today = Carbon::today()->toIso8601String();
        $measurementSettingsGeneralPurpose = MeasurementSettingsGeneralPurpose::first();

        $activityIndices = [
            'heart_rate' => 'heart_rate_activity_index',
            'body_temperature' => 'body_temperature_activity_index',
            'blood_pressure' => 'blood_pressure_activity_index',
            'oxygen_saturation' => 'oxygen_saturation_activity_index',
            'respiratory_rate' => 'respiratory_rate_activity_index',
            'ecg' => 'ecg_activity_index',
        ];

        $activities = [];

        foreach ($activityIndices as $key => $index) {
            // Construct the Elasticsearch query
            $query = [
                'index' => $index,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'range' => [
                                        'recorded_at' => [
                                            'gte' => Carbon::today()->startOfDay()->toIso8601String(),
                                            'lte' => Carbon::today()->endOfDay()->toIso8601String(),
                                        ]
                                    ]
                                ],
                                ['term' => ['user_customer_id' => $id]]
                            ]
                        ]
                    ],
                    'size' => 10000, // Get all matching records
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ];

            // Add specific range filters for each type
            switch ($key) {
                case 'heart_rate':
                    $query['body']['query']['bool']['must'][] = ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]];
                    break;
                case 'body_temperature':
                    $query['body']['query']['bool']['must'][] = ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]];
                    break;
                case 'blood_pressure':
                    $query['body']['query']['bool']['must'][] = ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]];
                    $query['body']['query']['bool']['must'][] = ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]];
                    break;
                case 'oxygen_saturation':
                    $query['body']['query']['bool']['must'][] = ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]];
                    break;
                case 'respiratory_rate':
                    $query['body']['query']['bool']['must'][] = ['range' => ['rate' => ['gte' => 6, 'lte' => 40]]];
                    break;
                case 'ecg':
                    $query['body']['query']['bool']['must'][] = ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]];
                    break;
            }

            // Execute the Elasticsearch query with try-catch for error handling
            try {
                $response = $this->elasticsearch->search($query);
                $data = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);
            } catch (\Exception $e) {
                Log::error("Elasticsearch error on $key Activity: " . $e->getMessage());
                $data = collect(); // Set to empty collection on error
            }

            foreach ($data as $activity) {
                $time = Carbon::parse($activity['recorded_at'])->format('H:i');
                $recordedAt = Carbon::parse($activity['recorded_at'])->toIso8601String();

                if (!isset($activities[$time])) {
                    $activities[$time] = [
                        'time' => $time,
                        'recorded_at' => $recordedAt,
                        'heart_rate' => null,
                        'body_temperature' => null,
                        'blood_pressure' => null,
                        'oxygen_saturation' => null,
                        'respiratory_rate' => null,
                        'ecg' => null,
                        'state' => ''
                    ];
                }

                $state = $this->determineState($activity, $measurementSettingsGeneralPurpose, $key);
                $activities[$time]['state'] = $state;

                switch ($key) {
                    case 'heart_rate':
                        $activities[$time]['heart_rate'] = $activity['bpm'];
                        break;
                    case 'body_temperature':
                        $activities[$time]['body_temperature'] = $activity['temperature'];
                        break;
                    case 'blood_pressure':
                        $activities[$time]['blood_pressure'] = $activity['systolic'] . ' / ' . $activity['diastolic'];
                        break;
                    case 'oxygen_saturation':
                        $activities[$time]['oxygen_saturation'] = $activity['saturation'];
                        break;
                    case 'respiratory_rate':
                        $activities[$time]['respiratory_rate'] = $activity['rate'];
                        break;
                    case 'ecg':
                        $activities[$time]['ecg'] = $activity['bpm'];
                        break;
                }
            }
        }

        krsort($activities);
        return array_values($activities);
    }


    private function determineState($activity, $settings, $type)
    {
        $state = 'normal';
        switch ($type) {
            case 'heart_rate':
                if ($activity['bpm'] <= $settings->heart_rate_caution_min || $activity['bpm'] >= $settings->heart_rate_caution_max) {
                    $state = 'caution';
                }
                if ($activity['bpm'] <= $settings->heart_rate_danger_min || $activity['bpm'] >= $settings->heart_rate_danger_max) {
                    $state = 'danger';
                }
                break;
            case 'body_temperature':
                if ($activity['temperature'] <= $settings->body_temp_caution_min || $activity['temperature'] >= $settings->body_temp_caution_max) {
                    $state = 'caution';
                }
                if ($activity['temperature'] <= $settings->body_temp_danger_min || $activity['temperature'] >= $settings->body_temp_danger_max) {
                    $state = 'danger';
                }
                break;
            case 'blood_pressure':
                if (
                    $activity['systolic'] <= $settings->blood_pressure_systolic_caution_min || $activity['diastolic'] <= $settings->blood_pressure_diastolic_caution_min ||
                    $activity['systolic'] >= $settings->blood_pressure_systolic_caution_max || $activity['diastolic'] >= $settings->blood_pressure_diastolic_caution_max
                ) {
                    $state = 'caution';
                }
                if (
                    $activity['systolic'] <= $settings->blood_pressure_systolic_danger_min || $activity['diastolic'] <= $settings->blood_pressure_diastolic_danger_min ||
                    $activity['systolic'] >= $settings->blood_pressure_systolic_danger_max || $activity['diastolic'] >= $settings->blood_pressure_diastolic_danger_max
                ) {
                    $state = 'danger';
                }
                break;
            case 'oxygen_saturation':
                if ($activity['saturation'] <= $settings->oxygen_saturation_caution_min || $activity['saturation'] >= $settings->oxygen_saturation_caution_max) {
                    $state = 'caution';
                }
                break;
            case 'respiratory_rate':
                if ($activity['rate'] <= $settings->respiratory_rate_caution_min || $activity['rate'] >= $settings->respiratory_rate_caution_max) {
                    $state = 'caution';
                }
                if ($activity['rate'] <= $settings->respiratory_rate_danger_min || $activity['rate'] >= $settings->respiratory_rate_danger_max) {
                    $state = 'danger';
                }
                break;
            case 'ecg':
                if ($activity['bpm'] <= $settings->ecg_caution_min || $activity['bpm'] >= $settings->ecg_caution_max) {
                    $state = 'caution';
                }
                if ($activity['bpm'] <= $settings->ecg_danger_min || $activity['bpm'] >= $settings->ecg_danger_max) {
                    $state = 'danger';
                }
                break;
        }
        return $state;
    }
}