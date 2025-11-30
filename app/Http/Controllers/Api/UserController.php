<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AlarmEmergencyGeneralPurposeJob;
use App\Jobs\AlarmEmergencyHospitalJob;
use App\Jobs\AlarmEmergencyPublicWelfareJob;
use App\Models\AllActivityUser;
use App\Models\DevicesSmartWatchPublicWelfare;
use App\Models\DevicesSmartWatchUserCustomer;
use App\Models\FCMToken;
use App\Models\MeasurementSettingCriteriaPatientHospital;
use App\Models\PatientCallReportPublicWelfare;
use App\Models\UserManagementGeneralPurpose;
use App\Models\UserManagementPublicWelfare;
use App\Services\ActivityService;
use App\Services\ElasticSearchService;
use App\Services\VitalSignsFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCustomer;
use App\Models\SosContact;
use App\Models\NotificationRangeSetting;
use App\Models\GoalSetting;
use App\Models\SleepActivity;
use App\Models\MenstrualCycleActivity;
use App\Models\UserCustomerGatewayClient;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\PatientHospital;
use App\Models\EmergencyReportHospital;
use App\Models\PatientCallReportHospital;
use App\Models\DevicesHospital;
use App\Services\NotificationService;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use \GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use function GuzzleHttp\json_encode;

class UserController extends Controller
{
    protected $elasticsearch;
    protected $notificationService;
    protected $vitalSignsFilterService;

    public function __construct(
        NotificationService $notificationService,
        ElasticSearchService $elasticSearchService,
        VitalSignsFilterService $vitalSignsFilterService,
    ) {
        $this->elasticsearch = $elasticSearchService;
        $this->notificationService = $notificationService;
        $this->vitalSignsFilterService = $vitalSignsFilterService;
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);

            $user = UserCustomer::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status_code' => 401,
                    'message' => 'Invalid login details',
                    'data' => null,
                    'time_stamp' => now(),
                ], 401);
            }

            $tokenResult = $user->createToken('auth_token');
            $token = $tokenResult->token;
            $token->expires_at = now()->addWeeks(1); // Example of setting token expiration to 1 week
            $token->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    'token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expired_at' => $token->expires_at,
                    'user' => $user
                ],
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

    public function generate_secret_key($serial_number, $predefined_string)
    {
        // Concatenate the serial number with the predefined string
        $concatenated_string = $serial_number . $predefined_string;

        // Perform SHA-256 hashing
        $secret_key = hash('sha256', $concatenated_string);

        return $secret_key;
    }

    public function loginWithSecretKey(Request $request)
    {
        try {
            $request->validate([
                'sn' => 'required',
                'secret' => 'required',
            ]);

            $user = UserCustomerGatewayClient::where('serial_number_device', $request->sn)->first();
            if (!$user) {
                return response()->json([
                    'status_code' => 401,
                    'status' => 'failed',
                    'error' => 'Invalid credentials',
                ], 401);
            }

            $secret_key = $this->generate_secret_key($request->sn, $user->digit);

            if ($secret_key != $request->secret) {
                return response()->json([
                    'status_code' => 401,
                    'status' => 'failed',
                    'error' => 'Invalid credentials',
                ], 401);
            }


            $tokenResult = $user->createToken('auth_token');
            $token = $tokenResult->token;
            $token->expires_at = now()->addWeeks(1); // Example of setting token expiration to 1 week
            $token->save();

            return response()->json([
                'status_code' => 200,
                'status' => 'success',
                'message' => 'success',
                'token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expired_at' => $token->expires_at->format('Y-m-d H:i:s'),
                'error' => null,
            ]);
        } catch (Exception $e) {
            $message = 'something wrong';
            $time_stamp = now();

            Log::error($message, ['detail' => (string) $e->getMessage(), 'time_stamp' => $time_stamp]);
            return response()->json([
                'status_code' => 500,
                'status' => 'failed',
                'error' => $message,
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $checkEmail = UserCustomer::where('email', $request->email)->first();
            if ($checkEmail) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'duplicated_user',
                    'data' => null,
                    'time_stamp' => now(),
                ], 200);
            }
            $user = UserCustomer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'height' => $request->height,
                'weight' => $request->weight,
                'note' => $request->note,
            ]);

            $goalSetting = GoalSetting::create([
                'user_customer_id' => $user->id,
                'exercise_goal' => $request->exercise_goal,
                'sleep_goal' => $request->sleep_goal,
            ]);

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

    public function update(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $user = UserCustomer::where('id', $auth->id)->first();
            if (!$user) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'data not found',
                    'data' => null,
                    'time_stamp' => now(),
                ], 404);
            }

            $user->name = $request->name ? $request->name : $user->name;
            $user->email = $request->email ? $request->email : $user->email;
            $user->password = $request->password ? Hash::make($request->password) : $user->password;
            $user->gender = $request->gender ? $request->gender : $user->gender;
            $user->birth_date = $request->birth_date ? $request->birth_date : $user->birth_date;
            $user->height = $request->height ? $request->height : $user->height;
            $user->weight = $request->weight ? $request->weight : $user->weight;
            $user->note = $request->note ? $request->note : $user->note;
            $user->save();

            if ($request->filled('exercise_goal') || $request->filled('sleep_goal'))
                $goals = GoalSetting::updateOrCreate(
                    ['user_customer_id' => $auth->id],
                    [
                        'user_customer_id' => $auth->id,
                        'exercise_goal' => $request->exercise_goal,
                        'sleep_goal' => $request->sleep_goal,
                    ]
                );

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

    public function getProfile(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $user = UserCustomer::where('id', $auth->id)->first();
            if (!$user) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'data not found',
                    'data' => null,
                    'time_stamp' => now(),
                ], 404);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    'user' => $user,
                ],
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

    public function getSOSContact(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $result = SosContact::where('user_customer_id', $auth->id)->get();

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => $result,
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

    public function upSertSOSContact(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            foreach ($request->contacts as $contact) {
                if (isset($contact['id'])) {
                    $getContact = SosContact::where('id', $contact['id'])->first();
                    if ($getContact) {
                        $getContact->name = $contact['name'];
                        $getContact->mobile_phone = $contact['mobile_phone'];
                        $getContact->save();
                        continue;
                    }
                }

                $user = SosContact::create([
                    'name' => $contact['name'],
                    'mobile_phone' => $contact['mobile_phone'],
                    'user_customer_id' => $auth->id
                ]);
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

    public function deleteSOSContact($id)
    {
        try {
            $auth = Auth::guard('api')->user();

            $user = SosContact::where('user_customer_id', $auth->id)->where('id', $id)->delete();

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

    public function getRangeSetting(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $ranges = NotificationRangeSetting::where('user_customer_id', $auth->id)->first();

            if (!$ranges) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'data not found',
                    'data' => null,
                    'time_stamp' => now(),
                ], 404);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => $ranges,
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

    public function setRangeSetting(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            // $request->validate([
            //     'heart_rate_min' => 'required|integer',
            //     'heart_rate_max' => 'required|integer',
            //     'body_temperature_min' => 'required|numeric',
            //     'body_temperature_max' => 'required|numeric',
            //     'oxygen_saturation_min' => 'required|integer',
            //     'oxygen_saturation_max' => 'required|integer',
            //     'blood_pressure_min' => 'required|integer',
            //     'blood_pressure_max' => 'required|integer',
            //     'respiration_min' => 'required|integer',
            //     'respiration_max' => 'required|integer',
            // ]);

            $ranges = NotificationRangeSetting::updateOrCreate(
                ['user_customer_id' => $auth->id],
                $request->all()
            );

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => null,
                'time_stamp' => now()->toISOString(),
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

    public function getGoalsSetting(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $goals = GoalSetting::where('user_customer_id', $auth->id)->first();

            if (!$goals) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'data not found',
                    'data' => null,
                    'time_stamp' => now()->toISOString(),
                ], 404);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => $goals,
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

    public function setGoalsSetting(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            // $request->validate([
            //     'exercise_goal' => 'required|integer',
            //     'sleep_goal' => 'required|integer',
            // ]);

            $goals = GoalSetting::updateOrCreate(
                ['user_customer_id' => $auth->id],
                $request->all()
            );

            return response()->json([
                'status_code' => 200,
                'message' => 'succcess',
                'data' => null,
                'time_stamp' => now()->toISOString(),
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

    public function getSummary(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();
            $date = Carbon::parse($request->input('date', now()))->startOfDay();
            $dateFormatted = $date->format('Y-m-d');

            // Patient information
            $patient = PatientHospital::where('user_customer_id', $auth->id)->first();

            // Common date filter for Elasticsearch queries
            $dateFilter = [
                'range' => [
                    'recorded_at' => [
                        'gte' => $dateFormatted . 'T00:00:00',  // Start of the day
                        'lt' => Carbon::parse($dateFormatted)->endOfDay()->format('Y-m-d') . 'T23:59:59',  // End of the day
                    ]
                ]
            ];

            // Determine if filtering by patient_id is required
            $filterBy = 'user_customer_id';
            $filterValue = $auth->id;
            if ($patient) {
                $filterBy = 'patient_id';
                $filterValue = $patient->id;
            }

            // ---- Calories Activity Query ----
            $activityLogQuery = [
                'index' => 'calories_activity_index',  // Adjust the index name as per your setup
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['term' => [$filterBy => $filterValue]]
                            ]
                        ]
                    ],
                    'size' => 1,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ];

            try {
                $activityLogResponse = $this->elasticsearch->search($activityLogQuery);
                $activityLog = collect($activityLogResponse['hits']['hits'])->first()['_source'] ?? null;
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on Calories Activity: ' . $e->getMessage());
                $activityLog = null;
            }

            $calories = $activityLog['calories'] ?? 0;
            $step = $activityLog['step'] ?? 0;
            $distance = $activityLog['distance'] ?? 0;

            // ---- Heart Rate Activity Query ----
            $heartRateQuery = [
                'index' => 'heart_rate_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]],
                                ['term' => [$filterBy => $filterValue]]
                            ]
                        ]
                    ],
                    'size' => 1,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ];

            try {
                $heartRateResponse = $this->elasticsearch->search($heartRateQuery);
                $heartRate = collect($heartRateResponse['hits']['hits'])->first()['_source']['bpm'] ?? 0;
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on Heart Rate Activity: ' . $e->getMessage());
                $heartRate = 0;
            }

            // ---- Body Temperature Activity Query ----
            $bodyTemperatureQuery = [
                'index' => 'body_temperature_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]],
                                ['term' => [$filterBy => $filterValue]]
                            ]
                        ]
                    ],
                    'size' => 1,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ];

            try {
                $bodyTemperatureResponse = $this->elasticsearch->search($bodyTemperatureQuery);
                $bodyTemperature = collect($bodyTemperatureResponse['hits']['hits'])->first()['_source']['temperature'] ?? 0.0;
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on Body Temperature Activity: ' . $e->getMessage());
                $bodyTemperature = 0.0;
            }

            // ---- Blood Pressure Activity Query ----
            $bloodPressureQuery = [
                'index' => 'blood_pressure_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]],
                                ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]],
                                ['term' => [$filterBy => $filterValue]]
                            ]
                        ]
                    ],
                    'size' => 1,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ];

            try {
                $bloodPressureResponse = $this->elasticsearch->search($bloodPressureQuery);
                $bloodPressure = collect($bloodPressureResponse['hits']['hits'])->first()['_source'] ?? null;
                $bloodPressureData = [
                    'max' => $bloodPressure['systolic'] ?? 0,
                    'min' => $bloodPressure['diastolic'] ?? 0,
                ];
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on Blood Pressure Activity: ' . $e->getMessage());
                $bloodPressureData = [
                    'max' => 0,
                    'min' => 0,
                ];
            }

            // ---- Oxygen Saturation Activity Query ----
            $oxygenSaturationQuery = [
                'index' => 'oxygen_saturation_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]],
                                ['term' => [$filterBy => $filterValue]]
                            ]
                        ]
                    ],
                    'size' => 1,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ];

            try {
                $oxygenSaturationResponse = $this->elasticsearch->search($oxygenSaturationQuery);
                $oxygenSaturation = collect($oxygenSaturationResponse['hits']['hits'])->first()['_source']['saturation'] ?? 0;
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on Oxygen Saturation Activity: ' . $e->getMessage());
                $oxygenSaturation = 0;
            }

            // ---- Respiratory Rate Activity Query ----
            $respiratoryRateQuery = [
                'index' => 'respiratory_rate_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['range' => ['rate' => ['gte' => 6, 'lte' => 40]]],
                                ['term' => [$filterBy => $filterValue]]
                            ]
                        ]
                    ],
                    'size' => 1,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ];

            try {
                $respiratoryRateResponse = $this->elasticsearch->search($respiratoryRateQuery);
                $respiratoryRate = collect($respiratoryRateResponse['hits']['hits'])->first()['_source']['rate'] ?? 0;
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on Respiratory Rate Activity: ' . $e->getMessage());
                $respiratoryRate = 0;
            }

            // ---- Sleep Activity Query ----
            $sleepQuery = [
                'index' => 'sleep_activities_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['term' => [$filterBy => $filterValue]]
                            ]
                        ]
                    ],
                    'size' => 1,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ];

            try {
                $sleepResponse = $this->elasticsearch->search($sleepQuery);
                $sleep = collect($sleepResponse['hits']['hits'])->first()['_source']['total_sleep'] ?? '';
            } catch (\Exception $e) {
                Log::error('Elasticsearch error on Sleep Activity: ' . $e->getMessage());
                $sleep = '';
            }

            // ---- Menstrual Cycle Activity Query ----
            // $menstrualCycleQuery = [
            //     'index' => 'menstrual_cycle_activity_index',
            //     'body' => [
            //         'query' => [
            //             'bool' => [
            //                 'must' => [
            //                     ['term' => [$filterBy => $filterValue]]
            //                 ]
            //             ]
            //         ],
            //         'size' => 1,
            //         'sort' => [['start_date' => ['order' => 'desc']]]
            //     ]
            // ];

            // try {
            //     $menstrualCycleResponse = $this->elasticsearch->search($menstrualCycleQuery);
            //     $menstrualCycleData = collect($menstrualCycleResponse['hits']['hits'])->first()['_source'] ?? null;
            // } catch (\Exception $e) {
            //     Log::error('Elasticsearch error on Menstrual Cycle Activity: ' . $e->getMessage());
            //     $menstrualCycleData = null;
            // }

            // Menstrual Cycle
            $menstrualCycleData = MenstrualCycleActivity::where('user_customer_id', $auth->id)
                ->orderBy('start_date', 'desc')
                ->first();
            $menstrualCycle = $menstrualCycleData ? $menstrualCycleData->average_cycle . ' days' : '';


            // Return the final response
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    'calories' => $calories,
                    'steps' => $step,
                    'distance' => $distance,
                    'heart_rate' => $heartRate,
                    'body_temperature' => $bodyTemperature,
                    'blood_pressure' => $bloodPressureData,
                    'oxygen_saturation' => $oxygenSaturation,
                    'respiratory_rate' => $respiratoryRate,
                    'sleep' => $sleep,
                    'menstrual_cycle' => $menstrualCycleData,
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



    public function synchReportedActivity(Request $request)
    {
        $auth = Auth::guard('api_gateway')->user();
        // Log::info('synchReportedActivity request from gateway user : ' . json_encode($auth));
        $data = $request->json()->all();
        // Log::info('synchReportedActivity request from gateway : ' . json_encode($request->json()->all()));

        $isAllZero = true; // Flag untuk mengecek apakah semua measurement bernilai nol
        $dateLog = now()->format('Y-m-d');
        $logEntries = []; // Menyimpan data log yang akan dimasukkan ke dalam file

        try {
            foreach ($data as $watch => $measurements) {
                foreach ($measurements as $measurement) {
                    if ($measurement['pr'] != 0 || $measurement['bt'] != 0 || $measurement['sbp'] != 0 || $measurement['dbp'] != 0 || $measurement['os'] != 0) {
                        $isAllZero = false;
                    }

                    if ($measurement['pr'] == 0 || $measurement['os'] == 0) {
                        $isAllZero = true;
                    }

                    // Simpan data ke dalam log entries
                    $logEntries[] = [
                        'datetime' => now()->format('Y-m-d H:i:s'),
                        'watch' => $watch,
                        'measurement' => $measurement
                    ];

                    $gatewaySN = $auth->serial_number_device;
                    $this->vitalSignsFilterService->filterVitalSigns($measurement, $watch, $gatewaySN);
                }
            }

            // // Tentukan nama file log berdasarkan kondisi measurement
            // $logFileName = $isAllZero ? "{$dateLog}-not-warm-watch.txt" : "{$dateLog}-warm-watch.txt";
            // $logFilePath = storage_path("logs/{$logFileName}");

            // // Format log dengan datetime dan simpan ke dalam file
            // foreach ($logEntries as $entry) {
            //     $logString = "[" . $entry['datetime'] . "] " . json_encode($entry, JSON_PRETTY_PRINT);
            //     file_put_contents($logFilePath, $logString . PHP_EOL, FILE_APPEND);
            // }

            return response()->json([
                'status_code' => 200,
                'status' => 'success',
                'message' => 'Data reported successfully"',
                'error' => null,
            ]);
        } catch (Exception $e) {
            $message = 'Something went wrong';
            $time_stamp = now();

            Log::error($message, ['detail' => $e, 'time_stamp' => $time_stamp]);
            return response()->json([
                'status_code' => 500,
                'status' => 'failed',
                'error' => $message,
            ], 500);
        }
    }

    public function sosCallGateway(Request $request)
    {
        $auth = Auth::guard('api_gateway')->user();
        $gatewaySN = $auth->serial_number_device;
        // Log::info('sosCallGateway request from gateway : ' . json_encode($request->json()->all()));
        $device = DevicesHospital::where(DB::raw('LOWER(device_code)'), strtolower($request->macaddress))->first();

        if ($device) {
            $checkDeviceAddress = PatientHospital::with(['detailedLocation', 'doctorInCharge', 'nurseInCharge'])->where('id', $device->patient_id)->first();

            if ($checkDeviceAddress) {
                $now = date('Y-m-d H:i:s');
                $minInterval = 30; // 30 detik dalam detik

                $lastPatientCall = PatientCallReportHospital::where('patient_id', $checkDeviceAddress->id)
                    ->orderBy('call_occurrence_time', 'desc')
                    ->first();

                // Cek jika tidak ada record sebelumnya atau sudah lebih dari 30 detik
                if (!$lastPatientCall || (strtotime($now) - strtotime($lastPatientCall->call_occurrence_time)) >= $minInterval) {
                    $patientCall = [
                        'patient_name' => $checkDeviceAddress->name,
                        'call_occurrence_time' => now(),
                        'call_details' => config('app.lang') != 'en' ? __('환자에게 전화하다') : __('Call a patient'),
                        'call_confirmation_time' => null,
                        'ward' => $checkDeviceAddress->detailedLocation ? $checkDeviceAddress->detailedLocation->location_name .
                            ' ' . $checkDeviceAddress->hospital_room : $checkDeviceAddress->hospital_room,
                        'call_type' => '',
                        'doctor_in_charge' => $checkDeviceAddress->doctorInCharge ? $checkDeviceAddress->doctorInCharge->name : '-',
                        'nurse_in_charge' => $checkDeviceAddress->nurseInCharge ? $checkDeviceAddress->nurseInCharge->name : '-',
                        'medical_staff' => '',
                        'cause_and_actions' => '',
                        'user_general_purpose_id' => $checkDeviceAddress->user_general_purpose_id,
                        'patient_id' => $checkDeviceAddress->id,
                        'serial_number_gateway' => $gatewaySN,
                    ];

                    $insertPatientCall = PatientCallReportHospital::create($patientCall);

                    // Log::info('Sending Call Patient # ' . json_encode($patientCall));
                    $deviceToken = '$fcmToken->fcm_token';
                    $title = "CALL_PATIENT_HOSPITAL";
                    $body = $insertPatientCall->id;
                    $soundUrl = asset('black/sounds/mixkit-bell-notification-933.wav');
                    $sendNotif = $this->notificationService->sendNotification($deviceToken, $title, $body, $soundUrl);
                }
            }
        }

        $devicePublicWelfare = DevicesSmartWatchPublicWelfare::where(DB::raw('LOWER(serial)'), strtolower($request->macaddress))->first();

        if ($devicePublicWelfare) {
            $checkDevicePublicWelfare = UserManagementPublicWelfare::where('user_customer_id', $devicePublicWelfare->user_customer_id)->first();
            if ($checkDevicePublicWelfare) {
                $now = date('Y-m-d H:i:s');
                $minInterval = 30; // 30 detik dalam detik

                $lastPatientCall = PatientCallReportPublicWelfare::where('user_customer_id', $checkDevicePublicWelfare->user_customer_id)
                    ->orderBy('notification_time', 'desc')
                    ->first();

                // Cek jika tidak ada record sebelumnya atau sudah lebih dari 30 detik
                if (!$lastPatientCall || (strtotime($now) - strtotime($lastPatientCall->call_occurrence_time)) >= $minInterval) {
                    $patientCall = [
                        'user_customer_id' => $checkDevicePublicWelfare->user_customer_id,
                        'assortment' => '',
                        'notification_time' => now(),
                        'content' => '',
                        'confirmation_time' => null,
                        'action_time' => null,
                        'name' => $checkDevicePublicWelfare->name,
                        'main_symptom' => config('app.lang') != 'en' ? __('환자에게 전화하다') : __('Call a patient'),
                        'gender' => $checkDevicePublicWelfare->gender,
                        'age' => $checkDevicePublicWelfare->age,
                        'contact' => $checkDevicePublicWelfare->contact_information,
                        'affiliations' => '',
                        'user_general_purpose_id' => $checkDevicePublicWelfare->user_general_purpose_id,
                        'serial_number_gateway' => $gatewaySN,
                    ];

                    $insertPatientCall = PatientCallReportPublicWelfare::create($patientCall);
                    // Log::info('Sending Call Patient Public Welfare# ' . json_encode($patientCall));
                    $title = "CALL_PATIENT_PUBLIC_WELFARE";
                    $body = $insertPatientCall->id;
                    $sendNotif = $this->notificationService->sendNotification('', $title, $body, '');
                }

            }
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
            'data' => null,
            'time_stamp' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function getReportActivity(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            // Common Filters
            $filters = [];

            if ($request->filled('is_history')) {
                $filters[] = ['term' => ['is_history' => $request->is_history]];
            }

            if ($request->filled('is_coming_from_gateway')) {
                $filters[] = ['term' => ['is_coming_from_gateway' => $request->is_coming_from_gateway]];
            }

            if ($request->filled('device_address')) {
                $filters[] = ['term' => ['device_address' => $request->device_address]];
            }

            if ($request->filled('user_customer_id')) {
                $filters[] = ['term' => ['user_customer_id' => $request->user_customer_id]];
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $filters[] = [
                    'range' => [
                        'created_at' => [
                            'gte' => Carbon::parse($request->start_date)->toIso8601String(),
                            'lte' => Carbon::parse($request->end_date)->toIso8601String(),
                        ]
                    ]
                ];
            }

            // Define Elasticsearch queries for each activity type
            $indices = [
                'heart_rate_activity_index' => 'heart_rate_activities',
                'body_temperature_activity_index' => 'body_temperature_activities',
                'blood_pressure_activity_index' => 'blood_pressure_activities',
                'oxygen_saturation_activity_index' => 'oxygen_saturation_activities',
                'respiratory_rate_activity_index' => 'respiratory_rate_activities',
                'ecg_activity_index' => 'ecg_activities'
            ];

            $result = [];

            foreach ($indices as $index => $key) {
                $query = [
                    'index' => $index,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $filters
                            ]
                        ],
                        'size' => $limit,
                        'sort' => [['recorded_at' => ['order' => 'desc']]]
                    ]
                ];

                // Execute Elasticsearch search
                try {
                    $response = $this->elasticsearch->search($query);
                    $result[$key] = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']);
                } catch (\Exception $e) {
                    Log::error("Elasticsearch error on {$key}: " . $e->getMessage());
                    $result[$key] = collect(); // Empty collection in case of error
                }
            }

            return response()->json($result);

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


    public function updatePassword(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $user = UserCustomer::where('id', $auth->id)->first();
            if (!$user) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'data not found',
                    'data' => null,
                    'time_stamp' => now(),
                ], 404);
            }

            // Compare the provided password with the hashed password
            if (!Hash::check($request->input('current_password'), $user->password)) {
                // Passwords doesnt match
                return response()->json([
                    'status_code' => 200,
                    'message' => 'current_password_doesnt_match',
                    'data' => null,
                    'time_stamp' => now(),
                ], 200);
            }


            $user->password = $request->new_password ? Hash::make($request->new_password) : $user->password;
            $user->save();

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

    public function checkEmail(Request $request)
    {
        try {
            $checkEmail = UserCustomer::where('email', $request->email)->first();
            if ($checkEmail) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'user_exists',
                    'data' => null,
                    'time_stamp' => now(),
                ], 200);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'user_no_exists',
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

    public function deleteAccount(Request $request)
    {
        $auth = Auth::guard('api')->user();

        try {
            DB::transaction(function () use ($auth) {
                $customer = UserCustomer::where('id', $auth->id)->first();
                if ($customer) {
                    $customer->delete();
                }

                $patient = PatientHospital::where('user_customer_id', $auth->id)->first();
                if ($patient) {
                    $patient->delete();
                }

                $userManagementPW = UserManagementPublicWelfare::where('user_customer_id', $auth->id)->first();
                if ($userManagementPW) {
                    $userManagementPW->delete();
                }

                $userManagementGp = UserManagementGeneralPurpose::where('user_customer_id', $auth->id)->first();
                if ($userManagementGp) {
                    $userManagementGp->delete();
                }
            });

            return response()->json([
                'status_code' => 200,
                'message' => 'success',
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

    public function validateSSOGoogle(Request $request)
    {
        $accessToken = $request->input('accessToken'); // Get access token from request

        Log::info('validateSSOGoogle Request# ' . json_encode($request));
        Log::info('validateSSOGoogle Request $accessToken # ' . $accessToken);

        if (!$accessToken) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Access Token is required'
            ], 400);
        }

        try {
            // Validate token using Google's OAuth2 Token Info API
            $response = Http::get("https://www.googleapis.com/oauth2/v3/tokeninfo", [
                'access_token' => $accessToken
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status_code' => 401,
                    'message' => 'Invalid Access Token',
                    'error' => $response->json()
                ], 401);
            }

            $userInfo = $response->json();

            Log::info('validateSSOGoogle Request $userInfo # ' . json_encode($userInfo));

            // Check if audience (aud) matches your Google Client ID
            $expectedClientIdDev = config('app.google.client_id_dev');
            $expectedClientIdProd = config('app.google.client_id_prod');
            if ($userInfo['aud'] !== $expectedClientIdDev && $userInfo['aud'] !== $expectedClientIdProd) {
                return response()->json([
                    'status_code' => 403,
                    'message' => 'Access Token is not valid for this application'
                ], 403);
            }

            $isNewUser = false;
            // Check if user exists in your database
            $userCustomer = UserCustomer::where('email', $userInfo['email'])
                ->whereNull('naver_id')
                ->whereNull('kakao_id')
                ->first();

            if (!$userCustomer) {
                $isNewUser = true;
                $userCustomer = UserCustomer::create([
                    'name' => $userInfo['email'],
                    'email' => $userInfo['email'],
                    'password' => bcrypt(Str::random(20)),
                ]);

                $goalSetting = GoalSetting::create([
                    'user_customer_id' => $userCustomer->id,
                    'exercise_goal' => 0,
                    'sleep_goal' => 0,
                ]);
            }

            // Generate Laravel access token for user authentication
            $tokenResult = $userCustomer->createToken('auth_token');
            $token = $tokenResult->token;
            $token->expires_at = now()->addWeeks(1);
            $token->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    'token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expired_at' => $token->expires_at,
                    'is_new_user' => $isNewUser,
                    'user' => $userCustomer
                ],
                'time_stamp' => now(),
            ]);
        } catch (\Exception $e) {
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

    public function validateSSONaver(Request $request)
    {
        try {
            // Get the access token from the request
            $accessToken = $request->input('code');
            $accessToken = str_replace(' ', '+', $accessToken);
            if (!$accessToken) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Access token is required',
                    'data' => null,
                    'time_stamp' => now(),
                ], 400);
            }

            // Initialize Guzzle HTTP client
            $client = new Client();

            // Call Naver API to get user info
            $response = $client->request('GET', 'https://openapi.naver.com/v1/nid/me', [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody(), true);

            // Check if Naver API returned success
            if ($statusCode !== 200 || $responseBody['resultcode'] !== "00") {
                return response()->json([
                    'status_code' => 401,
                    'message' => 'Failed to validate Naver user',
                    'data' => $responseBody,
                    'time_stamp' => now(),
                ], 401);
            }

            // Extract user information from the API response
            $naverUser = $responseBody['response'];
            $email = $naverUser['email'] ?? null;

            if (!$email) {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'Email is required but not provided by Naver',
                    'data' => null,
                    'time_stamp' => now(),
                ], 422);
            }

            // Find or create the user in the database
            $isNewUser = false;
            $userCustomer = UserCustomer::where('naver_id', $naverUser['id'])->first();

            if (!$userCustomer) {
                $isNewUser = true;
                $userCustomer = UserCustomer::create([
                    'name' => $naverUser['name'] ?? 'Unknown',
                    'email' => $email,
                    'password' => bcrypt(Str::random(20)), // Generate a random password
                    'naver_id' => $naverUser['id']
                ]);

                $goalSetting = GoalSetting::create([
                    'user_customer_id' => $userCustomer->id,
                    'exercise_goal' => 0,
                    'sleep_goal' => 0,
                ]);
            }

            // Create a personal access token
            $tokenResult = $userCustomer->createToken('auth_token');
            $token = $tokenResult->token;

            // Set token expiration to 1 week
            $token->expires_at = now()->addWeeks(1);
            $token->save();

            // Return a success response with token details and user information
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    'token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expired_at' => $token->expires_at,
                    'is_new_user' => $isNewUser,
                    'user' => $userCustomer
                ],
                'time_stamp' => now(),
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Log request errors and return an appropriate response
            Log::error('Naver API request failed', ['detail' => $e->getMessage()]);
            return response()->json([
                'status_code' => 401,
                'message' => 'unauthorize',
                'data' => null,
                'time_stamp' => now(),
            ], 401);
        } catch (\Exception $e) {
            // Handle unexpected errors
            Log::error('Unexpected error occurred', ['detail' => $e->getMessage()]);
            return response()->json([
                'status_code' => 500,
                'message' => 'something wrong',
                'data' => null,
                'time_stamp' => now(),
            ], 500);
        }
    }

    public function validateSSOKakao(Request $request)
    {
        try {
            $accessToken = $request->input('code'); // Pastikan kode diterima dari callback SSO Kakao

            // Tukarkan authorization code dengan access token
            $client = new \GuzzleHttp\Client();

            // Gunakan access token untuk mendapatkan data user
            try {
                $response = $client->get('https://kapi.kakao.com/v2/user/me', [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                    ],
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return response()->json([
                    'status_code' => 401,
                    'message' => 'Invalid or expired Kakao token',
                    'data' => null,
                    'time_stamp' => now(),
                ], 401);
            }

            $kakaoUser = json_decode($response->getBody()->getContents(), true);

            // $email = $kakaoUser['kakao_account']['email'];
            $name = $kakaoUser['properties']['nickname'] ?? 'Unknown User';

            $isNewUser = false;

            // Cek apakah user sudah terdaftar di database
            $userCustomer = UserCustomer::where('kakao_id', $kakaoUser['id'])->first();
            if (!$userCustomer) {
                $isNewUser = true;
                $userCustomer = UserCustomer::create([
                    'name' => $name,
                    'email' => '',
                    'password' => Str::random(20), // Set password random jika diperlukan
                    'kakao_id' => $kakaoUser['id']
                ]);

                $goalSetting = GoalSetting::create([
                    'user_customer_id' => $userCustomer->id,
                    'exercise_goal' => 0,
                    'sleep_goal' => 0,
                ]);
            }

            // Generate token untuk user
            $tokenResult = $userCustomer->createToken('auth_token');
            $token = $tokenResult->token;
            $token->expires_at = now()->addWeeks(1); // Set token expiration 1 minggu
            $token->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    'token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expired_at' => $token->expires_at,
                    'is_new_user' => $isNewUser,
                    'user' => $userCustomer,
                ],
                'time_stamp' => now(),
            ]);
        } catch (\Exception $e) {
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

    public function devicesConnect(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();
            $device = DevicesSmartWatchUserCustomer::
                where(DB::raw('LOWER(device_code)'), strtolower($request->device_address))
                ->first();
            if ($device) {
                $device->device_code = $request->device_address;
                $device->usage_status = true;
                $device->user_customer_id = $auth->id;
            } else {
                $device = new DevicesSmartWatchUserCustomer();
                $device->device_code = $request->device_address;
                $device->usage_status = true;
                $device->user_customer_id = $auth->id;
            }

            $device->save();


            // check another devices
            $checkDeviceUsed = DevicesSmartWatchUserCustomer::where('user_customer_id', $auth->id)
                ->where(DB::raw('LOWER(device_code)'), '<>', strtolower($request->device_address))
                ->first();

            if ($checkDeviceUsed) {
                $checkDeviceUsed->user_customer_id = null;
                $checkDeviceUsed->usage_status = false;
                $checkDeviceUsed->save();
            }

            return response()->json([
                'status_code' => 200,
                'status' => 'success',
                'message' => 'success"',
                'error' => null,
            ]);
        } catch (Exception $e) {
            $message = 'Something went wrong';
            $time_stamp = now();

            Log::error($message, ['detail' => (string) $e->getMessage(), 'time_stamp' => $time_stamp]);
            return response()->json([
                'status_code' => 500,
                'status' => 'failed',
                'error' => $message,
            ], 500);
        }
    }

    public function devicesDisconnect(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();
            $device = DevicesSmartWatchUserCustomer::where(DB::raw('LOWER(device_code)'), strtolower($request->device_address))
                ->first();

            if ($device) {
                $device->usage_status = false;
                $device->user_customer_id = null;
                $device->save();
            }

            return response()->json([
                'status_code' => 200,
                'status' => 'success',
                'message' => 'success"',
                'error' => null,
            ]);
        } catch (Exception $e) {
            $message = 'Something went wrong';
            $time_stamp = now();

            Log::error($message, ['detail' => (string) $e->getMessage(), 'time_stamp' => $time_stamp]);
            return response()->json([
                'status_code' => 500,
                'status' => 'failed',
                'error' => $message,
            ], 500);
        }
    }

    public function postActivities(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $date = $request->recorded_at;

            $data = [
                'device_address' => $request->input('device_address'),
                'user_customer_id' => $auth->id,
                'saturation' => $request->input('os_saturation'),
                'activity_type' => null,
                'recorded_at' => $date,
                'measureId' => null,
                'is_coming_from_gateway' => false,
                'patient_id' => null,
                'is_history' => false,
                'serial_number_gateway' => null,
            ];
            $this->elasticsearch->save($data, 'oxygen_saturation_activity_index');

            $data = [
                'user_customer_id' => $auth->id,
                'bpm' => $request->hr_bpm,
                'device_address' => $request->device_address,
                'recorded_at' => $date,
                'hrv' => null,
                'activity_type' => null,
                'cardiac_health_index' => null,
                'measureId' => null,
                'is_coming_from_gateway' => false,
                'patient_id' => null,
                'is_history' => false,
                'serial_number_gateway' => null,
            ];
            $this->elasticsearch->save($data, 'heart_rate_activity_index');


            ActivityService::insertAllActivityUser([
                'recorded_at' => $date,
                'device_address' => $request->device_address,
                'is_coming_from_gateway' => false,
                'measureId' => null,
                'user_customer_id' => $auth->id,
                'bpm' => $request->hr_bpm,
                'hrv' => null,
                'hr_activity_type' => null,
                'cardiac_health_index' => null,
                'saturation' => $request->os_saturation,
                'os_activity_type' => null,
                'request_body' => json_encode($request->all())
            ]);


            $activty = new \stdClass();
            $activty->bpm = $request->hr_bpm;
            $activty->saturation = $request->os_saturation;
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
                'status' => 'success',
                'message' => 'success"',
                'error' => null,
            ]);
        } catch (Exception $e) {
            $message = 'Something went wrong';
            $time_stamp = now();

            Log::error($message, ['detail' => (string) $e->getMessage(), 'time_stamp' => $time_stamp]);
            return response()->json([
                'status_code' => 500,
                'status' => 'failed',
                'error' => $message,
            ], 500);
        }
    }
}