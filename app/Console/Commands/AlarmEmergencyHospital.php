<?php

namespace App\Console\Commands;

use App\Jobs\AlarmEmergencyHospitalJob;
use App\Models\MeasurementSettingCriteriaPatientHospital;
use App\Models\PatientHospital;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlarmEmergencyHospital extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alarm:emergency-hospital';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $patients = $this->getPatientsEmergency();
        // Log::info('Sending Emergency Patient # ' . json_encode($patients));
        AlarmEmergencyHospitalJob::dispatch($patients);
    }

    // public function getPatientsEmergency()
    // {
    //     $result = [];
    //     // Fetch the latest blood pressure data excluding zeros
    //     $latestBloodPressureRecords = BloodPressureActivity::whereNotNull('patient_id')
    //         ->where('systolic', '>=', 70)
    //         ->where('systolic', '<=', 200)
    //         ->where('diastolic', '>=', 40)
    //         ->where('diastolic', '<=', 120)
    //         ->whereDate('recorded_at', '=', date('Y-m-d'))
    //         ->orderBy('recorded_at', 'desc')
    //         ->get()
    //         ->groupBy('patient_id')
    //         ->map(function ($group) {
    //             return $group->first();
    //         });

    //     foreach ($latestBloodPressureRecords as $record) {
    //         $stateSystolic = $this->determineState($record->patient_id, $record->systolic, 'blood_pressure_systolic');
    //         $stateDiastolic = $this->determineState($record->patient_id, $record->diastolic, 'blood_pressure_diastolic');

    //         if ($stateSystolic == 'danger' || $stateSystolic == 'caution' || $stateDiastolic == 'danger' || $stateDiastolic == 'caution') {
    //             $patient = PatientHospital::where('id', $record->patient_id)->where('status', 'IN_PATIENT')->first();
    //             if ($patient) {
    //                 if ($stateSystolic == 'danger' || $stateSystolic == 'caution') {
    //                     $alert_details = config('app.lang') != 'en' ? '혈압 수축기' : 'Blood pressure Systolic';
    //                     array_push($result, [
    //                         'recorded_at' => Carbon::parse($record->recorded_at)->format('Y-m-d'),
    //                         'name' => $patient->name,
    //                         'alert_details' => $alert_details . ' ' . $record->systolic,
    //                         'ward' => $patient->hospital_room,
    //                         'alert_type' => $stateSystolic,
    //                         'doctor_in_charge' => $patient->doctor_in_charge,
    //                         'nurse_in_charge' => $patient->nurse_in_charge,
    //                         'user_general_purpose_id' => $patient->user_general_purpose_id,
    //                         'patient_id' => $patient->id,
    //                     ]);
    //                 }

    //                 if ($stateDiastolic == 'danger' || $stateDiastolic == 'caution') {
    //                     $alert_details = config('app.lang') != 'en' ? '혈압 이완기' : 'Blood pressure Diastolic';
    //                     array_push($result, [
    //                         'recorded_at' => Carbon::parse($record->recorded_at)->format('Y-m-d'),
    //                         'name' => $patient->name,
    //                         'alert_details' => $alert_details . ' ' . $record->diastolic,
    //                         'ward' => $patient->hospital_room,
    //                         'alert_type' => $stateDiastolic,
    //                         'doctor_in_charge' => $patient->doctor_in_charge,
    //                         'nurse_in_charge' => $patient->nurse_in_charge,
    //                         'user_general_purpose_id' => $patient->user_general_purpose_id,
    //                         'patient_id' => $patient->id,
    //                     ]);
    //                 }
    //             }
    //         }
    //     }



    //     $latestheartRates = HeartRateActivity::whereNotNull('patient_id')
    //         ->where('bpm', '>=', 40)
    //         ->where('bpm', '<=', 180)
    //         ->whereDate('recorded_at', '=', date('Y-m-d'))
    //         ->orderBy('recorded_at', 'desc')
    //         ->get()
    //         ->groupBy('patient_id')
    //         ->map(function ($group) {
    //             return $group->first();
    //         });

    //     foreach ($latestheartRates as $record) {
    //         $state = $this->determineState($record->patient_id, $record->bpm, 'heart_rate');
    //         if ($state == 'danger' || $state == 'caution') {
    //             $patient = PatientHospital::where('id', $record->patient_id)->where('status', 'IN_PATIENT')->first();
    //             if ($patient) {
    //                 $alert_details = config('app.lang') != 'en' ? '심박수' : 'Heart rate';
    //                 array_push($result, [
    //                     'recorded_at' => Carbon::parse($record->recorded_at)->format('Y-m-d'),
    //                     'name' => $patient->name,
    //                     'alert_details' => $alert_details . ' ' . $record->bpm,
    //                     'ward' => $patient->hospital_room,
    //                     'alert_type' => $state,
    //                     'doctor_in_charge' => $patient->doctor_in_charge,
    //                     'nurse_in_charge' => $patient->nurse_in_charge,
    //                     'user_general_purpose_id' => $patient->user_general_purpose_id,
    //                     'patient_id' => $patient->id,
    //                 ]);
    //             }
    //         }
    //     }

    //     // Fetch all oxygen saturation data for the patient excluding zeros
    //     $latestOxygenSaturations = OxygenSaturationActivity::whereNotNull('patient_id')
    //         ->where('saturation', '>=', 75)
    //         ->where('saturation', '<=', 100)
    //         ->whereDate('recorded_at', '=', date('Y-m-d'))
    //         ->orderBy('recorded_at', 'desc')
    //         ->get()
    //         ->groupBy('patient_id')
    //         ->map(function ($group) {
    //             return $group->first();
    //         });

    //     foreach ($latestOxygenSaturations as $record) {
    //         $state = $this->determineState($record->patient_id, $record->saturation, 'oxygen_saturation');
    //         if ($state == 'danger' || $state == 'caution') {
    //             $patient = PatientHospital::where('id', $record->patient_id)->where('status', 'IN_PATIENT')->first();
    //             if ($patient) {
    //                 $alert_details = config('app.lang') != 'en' ? '산소포화도' : 'Oxygen saturation';
    //                 array_push($result, [
    //                     'recorded_at' => Carbon::parse($record->recorded_at)->format('Y-m-d'),
    //                     'name' => $patient->name,
    //                     'alert_details' => $alert_details . ' ' . $record->saturation,
    //                     'ward' => $patient->hospital_room,
    //                     'alert_type' => $state,
    //                     'doctor_in_charge' => $patient->doctor_in_charge,
    //                     'nurse_in_charge' => $patient->nurse_in_charge,
    //                     'user_general_purpose_id' => $patient->user_general_purpose_id,
    //                     'patient_id' => $patient->id,
    //                 ]);
    //             }
    //         }
    //     }

    //     // Fetch all body temperature data for the patient excluding zeros
    //     $latestTemperatures = BodyTemperatureActivity::whereNotNull('patient_id')
    //         ->where('temperature', '>=', 25)
    //         ->where('temperature', '<=', 42)
    //         ->whereDate('recorded_at', '=', date('Y-m-d'))
    //         ->orderBy('recorded_at', 'desc')
    //         ->get()
    //         ->groupBy('patient_id')
    //         ->map(function ($group) {
    //             return $group->first();
    //         });

    //     foreach ($latestTemperatures as $record) {
    //         $state = $this->determineState($record->patient_id, $record->temperature, 'body_temperature');
    //         if ($state == 'danger' || $state == 'caution') {
    //             $patient = PatientHospital::where('id', $record->patient_id)->where('status', 'IN_PATIENT')->first();
    //             if ($patient) {
    //                 $alert_details = config('app.lang') != 'en' ? '체온' : 'Body temperature';
    //                 array_push($result, [
    //                     'recorded_at' => Carbon::parse($record->recorded_at)->format('Y-m-d'),
    //                     'name' => $patient->name,
    //                     'alert_details' => $alert_details . ' ' . $record->temperature,
    //                     'ward' => $patient->hospital_room,
    //                     'alert_type' => $state,
    //                     'doctor_in_charge' => $patient->doctor_in_charge,
    //                     'nurse_in_charge' => $patient->nurse_in_charge,
    //                     'user_general_purpose_id' => $patient->user_general_purpose_id,
    //                     'patient_id' => $patient->id,
    //                 ]);
    //             }
    //         }
    //     }

    //     return $result;
    // }


    public function getPatientsEmergency()
    {
        $result = [];
        $today = Carbon::now()->toDateString();

        // Common query parameters
        $dateFilter = [
            'range' => [
                'recorded_at' => [
                    'gte' => $today,
                    'lte' => $today
                ]
            ]
        ];

        // Define Elasticsearch queries for different measurements
        $queries = [
            'blood_pressure' => [
                'index' => 'blood_pressure_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['exists' => ['field' => 'patient_id']],
                                ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]],
                                ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]]
                            ]
                        ]
                    ],
                    'size' => 10000,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ],
            'heart_rate' => [
                'index' => 'heart_rate_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['exists' => ['field' => 'patient_id']],
                                ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]]
                            ]
                        ]
                    ],
                    'size' => 10000,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ],
            'oxygen_saturation' => [
                'index' => 'oxygen_saturation_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['exists' => ['field' => 'patient_id']],
                                ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]]
                            ]
                        ]
                    ],
                    'size' => 10000,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ],
            'body_temperature' => [
                'index' => 'body_temperature_activity_index',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                $dateFilter,
                                ['exists' => ['field' => 'patient_id']],
                                ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]]
                            ]
                        ]
                    ],
                    'size' => 10000,
                    'sort' => [['recorded_at' => ['order' => 'desc']]]
                ]
            ]
        ];

        // Execute all Elasticsearch queries
        foreach ($queries as $type => $query) {
            try {
                $response = app('elasticsearch')->search($query);
                $records = collect($response['hits']['hits'])->map(fn($hit) => $hit['_source'])->groupBy('patient_id')->map(fn($group) => $group->first());

                foreach ($records as $record) {
                    $state = match ($type) {
                        'blood_pressure' => [
                            'systolic' => $this->determineState($record['patient_id'], $record['systolic'], 'blood_pressure_systolic'),
                            'diastolic' => $this->determineState($record['patient_id'], $record['diastolic'], 'blood_pressure_diastolic')
                        ],
                        'heart_rate' => $this->determineState($record['patient_id'], $record['bpm'], 'heart_rate'),
                        'oxygen_saturation' => $this->determineState($record['patient_id'], $record['saturation'], 'oxygen_saturation'),
                        'body_temperature' => $this->determineState($record['patient_id'], $record['temperature'], 'body_temperature'),
                    };

                    if (is_array($state)) {
                        $alertConditions = array_filter($state, fn($s) => in_array($s, ['danger', 'caution']));
                    } else {
                        $alertConditions = in_array($state, ['danger', 'caution']) ? [$state] : [];
                    }

                    if (!empty($alertConditions)) {
                        $patient = PatientHospital::where('id', $record['patient_id'])->where('status', 'IN_PATIENT')->first();
                        if ($patient) {
                            foreach ($alertConditions as $key => $alertState) {
                                $alertDetails = match ($type) {
                                    'blood_pressure' => config('app.lang') != 'en' ? ($key == 'systolic' ? '혈압 수축기' : '혈압 이완기') : ($key == 'systolic' ? 'Blood pressure Systolic' : 'Blood pressure Diastolic'),
                                    'heart_rate' => config('app.lang') != 'en' ? '심박수' : 'Heart rate',
                                    'oxygen_saturation' => config('app.lang') != 'en' ? '산소포화도' : 'Oxygen saturation',
                                    'body_temperature' => config('app.lang') != 'en' ? '체온' : 'Body temperature',
                                };

                                array_push($result, [
                                    'recorded_at' => Carbon::parse($record['recorded_at'])->format('Y-m-d'),
                                    'name' => $patient->name,
                                    'alert_details' => $alertDetails . ' ' . ($key == 'systolic' || $key == 'diastolic' ? $record[$key] : $record[$type]),
                                    'ward' => $patient->hospital_room,
                                    'alert_type' => $alertState,
                                    'doctor_in_charge' => $patient->doctor_in_charge,
                                    'nurse_in_charge' => $patient->nurse_in_charge,
                                    'user_general_purpose_id' => $patient->user_general_purpose_id,
                                    'patient_id' => $patient->id,
                                ]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Elasticsearch error on $type data: " . $e->getMessage());
            }
        }

        return $result;
    }


    private function determineState($patient_id, $value1, $key, $value2 = 0)
    {
        // Fetch the measurement settings criteria for the patient
        $criteria = MeasurementSettingCriteriaPatientHospital::where('patient_id', $patient_id)->first();

        $defaultValues = [
            'heart_rate_good_health_value1' => 60,
            'heart_rate_good_health_value2' => 100,
            'heart_rate_caution_value1' => 101,
            'heart_rate_caution_value2' => 110,
            'heart_rate_dangers_value1' => 111,
            'heart_rate_dangers_value2' => 130,
            'oxygen_saturation_good_health_value1' => 95,
            'oxygen_saturation_good_health_value2' => 100,
            'oxygen_saturation_caution_value1' => 91,
            'oxygen_saturation_caution_value2' => 94,
            'oxygen_saturation_dangers_value1' => 90,
            'oxygen_saturation_dangers_value2' => 90,
            'body_temperature_good_health_value1' => 36.1,
            'body_temperature_good_health_value2' => 37.2,
            'body_temperature_caution_value1' => 37.3,
            'body_temperature_caution_value2' => 38.0,
            'body_temperature_dangers_value1' => 38.1,
            'body_temperature_dangers_value2' => 39.0,
            'blood_pressure_systolic_good_min' => 90,
            'blood_pressure_systolic_good_max' => 120,
            'blood_pressure_systolic_caution_min' => 121,
            'blood_pressure_systolic_caution_max' => 140,
            'blood_pressure_systolic_danger_min' => 141,
            'blood_pressure_systolic_danger_max' => 180,
            'blood_pressure_diastolic_good_min' => 60,
            'blood_pressure_diastolic_good_max' => 80,
            'blood_pressure_diastolic_caution_min' => 81,
            'blood_pressure_diastolic_caution_max' => 90,
            'blood_pressure_diastolic_danger_min' => 91,
            'blood_pressure_diastolic_danger_max' => 120,
        ];

        if (!$criteria) {
            $defaultValues['patient_id'] = $patient_id;
            MeasurementSettingCriteriaPatientHospital::create($defaultValues);
        }
        // If criteria exists, override the default values
        $goodMin = $criteria ? $criteria[$key . '_good_health_value1'] : $defaultValues[$key . '_good_health_value1'];
        $goodMax = $criteria ? $criteria[$key . '_good_health_value2'] : $defaultValues[$key . '_good_health_value2'];
        $cautionMin = $criteria ? $criteria[$key . '_caution_value1'] : $defaultValues[$key . '_caution_value1'];
        $cautionMax = $criteria ? $criteria[$key . '_caution_value2'] : $defaultValues[$key . '_caution_value2'];
        $dangerMin = $criteria ? $criteria[$key . '_dangers_value1'] : $defaultValues[$key . '_dangers_value1'];
        $dangerMax = $criteria ? $criteria[$key . '_dangers_value2'] : $defaultValues[$key . '_dangers_value2'];

        if ($key == 'blood_pressure' && $value2) {
            // Adjust for systolic and diastolic pressure
            $systolicGoodMin = $criteria ? $criteria['blood_pressure_systolic_good_min'] : $defaultValues['blood_pressure_systolic_good_min'];
            $systolicGoodMax = $criteria ? $criteria['blood_pressure_systolic_good_max'] : $defaultValues['blood_pressure_systolic_good_max'];
            $systolicCautionMin = $criteria ? $criteria['blood_pressure_systolic_caution_min'] : $defaultValues['blood_pressure_systolic_caution_min'];
            $systolicCautionMax = $criteria ? $criteria['blood_pressure_systolic_caution_max'] : $defaultValues['blood_pressure_systolic_caution_max'];
            $systolicDangerMin = $criteria ? $criteria['blood_pressure_systolic_danger_min'] : $defaultValues['blood_pressure_systolic_danger_min'];
            $systolicDangerMax = $criteria ? $criteria['blood_pressure_systolic_danger_max'] : $defaultValues['blood_pressure_systolic_danger_max'];

            $diastolicGoodMin = $criteria ? $criteria['blood_pressure_diastolic_good_min'] : $defaultValues['blood_pressure_diastolic_good_min'];
            $diastolicGoodMax = $criteria ? $criteria['blood_pressure_diastolic_good_max'] : $defaultValues['blood_pressure_diastolic_good_max'];
            $diastolicCautionMin = $criteria ? $criteria['blood_pressure_diastolic_caution_min'] : $defaultValues['blood_pressure_diastolic_caution_min'];
            $diastolicCautionMax = $criteria ? $criteria['blood_pressure_diastolic_caution_max'] : $defaultValues['blood_pressure_diastolic_caution_max'];
            $diastolicDangerMin = $criteria ? $criteria['blood_pressure_diastolic_danger_min'] : $defaultValues['blood_pressure_diastolic_danger_min'];
            $diastolicDangerMax = $criteria ? $criteria['blood_pressure_diastolic_danger_max'] : $defaultValues['blood_pressure_diastolic_danger_max'];

            if ($value1 >= $systolicGoodMin && $value1 <= $systolicGoodMax && $value2 >= $diastolicGoodMin && $value2 <= $diastolicGoodMax) {
                return 'good';
            } elseif ($value1 >= $systolicCautionMin && $value1 <= $systolicCautionMax && $value2 >= $diastolicCautionMin && $value2 <= $diastolicCautionMax) {
                return 'caution';
            } elseif ($value1 >= $systolicDangerMin && $value1 <= $systolicDangerMax && $value2 >= $diastolicDangerMin && $value2 <= $diastolicDangerMax) {
                return 'danger';
            } else {
                return '';
            }
        } else if ($key == 'blood_pressure_systolic') {
            // Adjust for systolic and diastolic pressure
            $systolicGoodMin = $criteria ? $criteria['blood_pressure_systolic_good_min'] : $defaultValues['blood_pressure_systolic_good_min'];
            $systolicGoodMax = $criteria ? $criteria['blood_pressure_systolic_good_max'] : $defaultValues['blood_pressure_systolic_good_max'];
            $systolicCautionMin = $criteria ? $criteria['blood_pressure_systolic_caution_min'] : $defaultValues['blood_pressure_systolic_caution_min'];
            $systolicCautionMax = $criteria ? $criteria['blood_pressure_systolic_caution_max'] : $defaultValues['blood_pressure_systolic_caution_max'];
            $systolicDangerMin = $criteria ? $criteria['blood_pressure_systolic_danger_min'] : $defaultValues['blood_pressure_systolic_danger_min'];
            $systolicDangerMax = $criteria ? $criteria['blood_pressure_systolic_danger_max'] : $defaultValues['blood_pressure_systolic_danger_max'];

            if ($value1 >= $systolicGoodMin && $value1 <= $systolicGoodMax) {
                return 'good';
            } elseif ($value1 >= $systolicCautionMin && $value1 <= $systolicCautionMax) {
                return 'caution';
            } elseif ($value1 >= $systolicDangerMin && $value1 <= $systolicDangerMax) {
                return 'danger';
            } else {
                return '';
            }
        } else if ($key == 'blood_pressure_diastolic') {
            // Adjust for systolic and diastolic pressure
            $diastolicGoodMin = $criteria ? $criteria['blood_pressure_diastolic_good_min'] : $defaultValues['blood_pressure_diastolic_good_min'];
            $diastolicGoodMax = $criteria ? $criteria['blood_pressure_diastolic_good_max'] : $defaultValues['blood_pressure_diastolic_good_max'];
            $diastolicCautionMin = $criteria ? $criteria['blood_pressure_diastolic_caution_min'] : $defaultValues['blood_pressure_diastolic_caution_min'];
            $diastolicCautionMax = $criteria ? $criteria['blood_pressure_diastolic_caution_max'] : $defaultValues['blood_pressure_diastolic_caution_max'];
            $diastolicDangerMin = $criteria ? $criteria['blood_pressure_diastolic_danger_min'] : $defaultValues['blood_pressure_diastolic_danger_min'];
            $diastolicDangerMax = $criteria ? $criteria['blood_pressure_diastolic_danger_max'] : $defaultValues['blood_pressure_diastolic_danger_max'];

            if ($value1 >= $diastolicGoodMin && $value1 <= $diastolicGoodMax) {
                return 'good';
            } elseif ($value1 >= $diastolicCautionMin && $value1 <= $diastolicCautionMax) {
                return 'caution';
            } elseif ($value1 >= $diastolicDangerMin && $value1 <= $diastolicDangerMax) {
                return 'danger';
            } else {
                return '';
            }
        } else {
            if ($value1 >= $goodMin && $value1 <= $goodMax) {
                return 'good';
            } elseif ($value1 >= $cautionMin && $value1 <= $cautionMax) {
                return 'caution';
            } elseif ($value1 >= $dangerMin && $value1 <= $dangerMax) {
                return 'danger';
            } else {
                return '';
            }
        }
    }


    private function detemineColorOfState($state)
    {
        if ($state == 'good') {
            return '#3CBAFF';
        } else if ($state == 'caution') {
            return '#EEAD3C';
        } else if ($state == 'danger') {
            return '#FC565D';
        } else {
            return '#869CAF';
        }
    }
}