<?php

namespace App\Services;

use App\Jobs\AlarmEmergencyHospitalJob;
use App\Jobs\AlarmEmergencyPublicWelfareJob;
use App\Models\DevicesHospital;
use App\Models\DevicesSmartWatchPublicWelfare;
use App\Models\DevicesSmartWatchUserCustomer;
use App\Models\PatientHospital;
use App\Models\UserManagementPublicWelfare;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class VitalSignsFilterService
{
    protected $elasticsearch;

    public function __construct(
        ElasticSearchService $elasticSearchService,
    ) {
        $this->elasticsearch = $elasticSearchService;
    }


    public function filterVitalSigns(array $data, $watch, $gatewaySN)
    {
        $deviceMobile = DevicesSmartWatchUserCustomer::
            where(DB::raw('LOWER(device_code)'), strtolower($watch))
            ->where('usage_status', true)
            ->first();

        if ($deviceMobile) {
            return;
        }

        $device = DevicesHospital::where(DB::raw('LOWER(device_code)'), strtolower($watch))->first();
        $devicePublicWelfare = DevicesSmartWatchPublicWelfare::where(DB::raw('LOWER(serial)'), strtolower($watch))->first();

        // Store raw measurement data for logging
        $this->logData($data, $watch, $device, $devicePublicWelfare, $gatewaySN);

        // Step 1: Identify non-wearing state and flag it
        if ($this->isNonWearingState($data)) {
            Log::info("Detected non-wearing state, setting values to 0 with flag", $data);
            $this->setToZero($data);
            return;
        }

        // Step 2: Correct erroneous data based on expected measurement intervals
        $data = $this->correctMeasurementIntervals($data, $watch);
        if ($data === null) {
            Log::warning("Data interval error, skipping data", $data);
            return;
        }

        // Step 3: Outlier detection based on historical data from Elasticsearch
        $data = $this->detectOutliers($data, $watch);

        // Step 4: AI-Based Missing Value Correction with anomaly detection
        $aiPredictionNeeded = false;
        foreach (['pr', 'bt', 'sbp', 'dbp', 'os'] as $key) {
            if (
                $data[$key] == 0 ||
                (isset($data['previous_' . $key]) && abs($data[$key] - $data['previous_' . $key]) > 50)
            ) {
                Log::warning("Measurement anomaly detected for $key, replacing with AI prediction", $data);
                $aiPredictionNeeded = true;
            }
        }
        if ($aiPredictionNeeded) {
            $data = $this->predictMissingValues($data, $watch);
            $data['flag'] = 'predicted';
        }

        // Step 5: Detect sensor errors using median filtering on large jumps
        $data = $this->detectSensorErrors($data);

        // Save the (possibly corrected) measurement
        $this->saveMeasurement($data, $watch, $device, $devicePublicWelfare, $gatewaySN);
        // Step 6: Trigger multi-level emergency alerts if conditions are met
        $this->sendEmergencyNew($data, $device, $devicePublicWelfare);

        return;
    }

    private function isNonWearingState(array $data): bool
    {
        return (
            $data['sbp'] == 0 &&
            $data['dbp'] == 0 &&
            ($data['bt'] == 0 || $data['os'] == 0)
        );
    }

    // Updated to add a "non_wearing_detected" flag
    private function setToZero(array $data): array
    {
        $zeroedData = array_fill_keys(array_keys($data), 0);
        $zeroedData['flag'] = 'non_wearing_detected';
        return $zeroedData;
    }

    private function correctMeasurementIntervals(array $data, $deviceAddress)
    {
        $data = $this->getDataDiffTimeStampMeasurement($data, $deviceAddress);

        // // Validasi untuk interval pengukuran BPM dan Oksigen (3 detik ± 1 detik)
        // if (abs($data['pr_timestamp_diff']) > 4 || abs($data['os_timestamp_diff']) > 4) {
        //     return null;  // Data tidak valid, lebih dari 1 detik perbedaan
        // }

        // // Validasi untuk interval pengukuran Tekanan Darah dan Suhu Tubuh (5 menit ± 30 detik)
        // if (abs($data['sbp_timestamp_diff']) > 330 || abs($data['bt_timestamp_diff']) > 330) {
        //     return null;  // Data tidak valid, lebih dari 30 detik perbedaan
        // }

        return $data;
    }

    private function getDataDiffTimeStampMeasurement($data, $deviceAddress)
    {
        $recordedAtSend = Carbon::parse($data['timestamp']);
        $bp = $this->elasticsearch->getSingle('blood_pressure_activity_index', 'recorded_at', 'desc', [
            ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]],
            ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]],
            ['term' => ['is_coming_from_gateway' => true]],
            ['match' => ['device_address' => $deviceAddress]]
        ]);
        $data['sbp_timestamp_diff'] = 0;
        if ($bp) {
            $recordedAtLastBp = Carbon::parse($bp['recorded_at']);
            $timeDiffInSeconds = $recordedAtLastBp->diffInSeconds($recordedAtSend);
            $data['sbp_timestamp_diff'] = $timeDiffInSeconds;
        }


        $pr = $this->elasticsearch->getSingle('heart_rate_activity_index', 'recorded_at', 'desc', [
            ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]],
            ['term' => ['is_coming_from_gateway' => true]],
            ['match' => ['device_address' => $deviceAddress]]
        ]);

        $data['pr_timestamp_diff'] = 0;
        if ($pr) {
            $recordedAtLasttPr = Carbon::parse($pr['recorded_at']);
            $timeDiffInSeconds = $recordedAtLasttPr->diffInSeconds($recordedAtSend);
            $data['pr_timestamp_diff'] = $timeDiffInSeconds;
        }

        $bt = $this->elasticsearch->getSingle('body_temperature_activity_index', 'recorded_at', 'desc', [
            // ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]],
            ['range' => ['temperature' => ['gte' => 35, 'lte' => 40]]],
            ['term' => ['is_coming_from_gateway' => true]],
            ['match' => ['device_address' => $deviceAddress]]
        ]);

        $data['bt_timestamp_diff'] = 0;
        if ($bt) {
            $recordedAtLastBt = Carbon::parse($bt['recorded_at']);
            $timeDiffInSeconds = $recordedAtLastBt->diffInSeconds($recordedAtSend);
            $data['bt_timestamp_diff'] = $timeDiffInSeconds;
        }

        $os = $this->elasticsearch->getSingle('oxygen_saturation_activity_index', 'recorded_at', 'desc', [
            ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]],
            ['term' => ['is_coming_from_gateway' => true]],
            ['match' => ['device_address' => $deviceAddress]]
        ]);

        $data['os_timestamp_diff'] = 0;
        if ($os) {
            $recordedAtLastOs = Carbon::parse($os['recorded_at']);
            $timeDiffInSeconds = $recordedAtLastOs->diffInSeconds($recordedAtSend);
            $data['os_timestamp_diff'] = $timeDiffInSeconds;
        }

        return $data;
    }

    private function detectOutliers(array $data, $deviceAddress): array
    {
        // Step 1: Fetch the last 5 data points for each vital sign from Elasticsearch
        $history = [];
        foreach (['sbp', 'bt', 'os', 'pr'] as $key) {
            if ($key == 'sbp') {
                $historiesLastData5 = $this->elasticsearch->getList(
                    'blood_pressure_activity_index',
                    'recorded_at',
                    'desc',
                    [
                        ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]],
                        ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]],
                        ['term' => ['is_coming_from_gateway' => true]],
                        ['match' => ['device_address' => $deviceAddress]]
                    ],
                    5
                );
                $history['sbp'] = [];
                $history['dbp'] = [];
                foreach ($historiesLastData5 as $h) {
                    array_push($history['sbp'], $h['systolic']);
                    array_push($history['dbp'], $h['diastolic']);
                }
                $data['previous_sbp'] = count($historiesLastData5) > 0 ? $historiesLastData5[0]['systolic'] : 0;
                $data['previous_dbp'] = count($historiesLastData5) > 0 ? $historiesLastData5[0]['diastolic'] : 0;
            } else if ($key == 'pr') {
                $historiesLastData5 = $this->elasticsearch->getList('heart_rate_activity_index', 'recorded_at', 'desc', [
                    ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]],
                    ['term' => ['is_coming_from_gateway' => true]],
                    ['match' => ['device_address' => $deviceAddress]]
                ], 5);
                $history[$key] = [];
                foreach ($historiesLastData5 as $h) {
                    array_push($history[$key], $h['bpm']);
                }
                $data['previous_pr'] = count($historiesLastData5) > 0 ? $historiesLastData5[0]['bpm'] : 0;
            } else if ($key == 'os') {
                $historiesLastData5 = $this->elasticsearch->getList('oxygen_saturation_activity_index', 'recorded_at', 'desc', [
                    ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]],
                    ['term' => ['is_coming_from_gateway' => true]],
                    ['match' => ['device_address' => $deviceAddress]]
                ], 5);
                $history[$key] = [];
                foreach ($historiesLastData5 as $h) {
                    array_push($history[$key], $h['saturation']);
                }
                $data['previous_os'] = count($historiesLastData5) > 0 ? $historiesLastData5[0]['saturation'] : 0;
            } else if ($key == 'bt') {
                $historiesLastData5 = $this->elasticsearch->getList('body_temperature_activity_index', 'recorded_at', 'desc', [
                    ['range' => ['temperature' => ['gte' => 35, 'lte' => 40]]],
                    ['term' => ['is_coming_from_gateway' => true]],
                    ['match' => ['device_address' => $deviceAddress]]
                ], 5);
                $history[$key] = [];
                foreach ($historiesLastData5 as $h) {
                    array_push($history[$key], $h['temperature']);
                }
                $data['previous_bt'] = count($historiesLastData5) > 0 ? $historiesLastData5[0]['temperature'] : 0;
            }
        }
        foreach (['sbp', 'dbp', 'bt', 'os', 'pr'] as $key) {
            if (isset($history[$key]) && count($history[$key]) > 0) {
                $median = $this->calculateMedian($history[$key]);
                if (abs($data[$key] - $median) > 0.2 * $median) {
                    Log::info("Outlier detected for $key, replacing with median", ['original' => $data[$key], 'median' => $median]);
                    $data[$key] = $median;
                }
            }
        }
        return $data;
    }

    private function calculateMedian(array $values): float
    {
        sort($values);
        $count = count($values);
        $middle = floor($count / 2);
        return ($count % 2) ? $values[$middle] : ($values[$middle - 1] + $values[$middle]) / 2;
    }

    private function getHistoryMeasurement($deviceAddress)
    {
        $history = [];
        foreach (['sbp', 'bt', 'os', 'pr'] as $key) {
            if ($key == 'sbp') {
                $historiesLastData5 = $this->elasticsearch->getList(
                    'blood_pressure_activity_index',
                    'recorded_at',
                    'desc',
                    [
                        ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]],
                        ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]],
                        ['term' => ['is_coming_from_gateway' => true]],
                        ['match' => ['device_address' => $deviceAddress]],
                        [
                            'range' => [
                                'recorded_at' => [
                                    'gte' => 'now-60s/s',
                                    'lte' => 'now/s'
                                ]
                            ]
                        ]
                    ],
                    10000
                );
                $history['sbp'] = [];
                $history['dbp'] = [];
                foreach ($historiesLastData5 as $h) {
                    array_push($history['sbp'], $h['systolic']);
                    array_push($history['dbp'], $h['diastolic']);
                }
            } else if ($key == 'pr') {
                $historiesLastData5 = $this->elasticsearch->getList('heart_rate_activity_index', 'recorded_at', 'desc', [
                    ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]],
                    ['term' => ['is_coming_from_gateway' => true]],
                    ['match' => ['device_address' => $deviceAddress]],
                    [
                        'range' => [
                            'recorded_at' => [
                                'gte' => 'now-60s/s',
                                'lte' => 'now/s'
                            ]
                        ]
                    ]
                ], 10000);
                $history[$key] = [];
                foreach ($historiesLastData5 as $h) {
                    array_push($history[$key], $h['bpm']);
                }
            } else if ($key == 'os') {
                $historiesLastData5 = $this->elasticsearch->getList('oxygen_saturation_activity_index', 'recorded_at', 'desc', [
                    ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]],
                    ['term' => ['is_coming_from_gateway' => true]],
                    ['match' => ['device_address' => $deviceAddress]],
                    [
                        'range' => [
                            'recorded_at' => [
                                'gte' => 'now-60s/s',
                                'lte' => 'now/s'
                            ]
                        ]
                    ]
                ], 10000);
                $history[$key] = [];
                foreach ($historiesLastData5 as $h) {
                    array_push($history[$key], $h['saturation']);
                }
            } else if ($key == 'bt') {
                $historiesLastData5 = $this->elasticsearch->getList('body_temperature_activity_index', 'recorded_at', 'desc', [
                    ['range' => ['temperature' => ['gte' => 35, 'lte' => 40]]],
                    ['term' => ['is_coming_from_gateway' => true]],
                    ['match' => ['device_address' => $deviceAddress]],
                    [
                        'range' => [
                            'recorded_at' => [
                                'gte' => 'now-60s/s',
                                'lte' => 'now/s'
                            ]
                        ]
                    ]
                ], 10000);
                $history[$key] = [];
                foreach ($historiesLastData5 as $h) {
                    array_push($history[$key], $h['temperature']);
                }
            }
        }
        return $history;
    }
    private function predictMissingValues(array $data, $deviceAddress): array
    {
        $history = $this->getHistoryMeasurement($deviceAddress);
        $apiUrl = config('app.ai_service.host') . '/predict';
        $body = $history;
        $response = Http::post($apiUrl, $body);
        if ($response->successful()) {
            $predictedData = $response->json();
            foreach (['sbp', 'dbp', 'bt', 'os', 'pr'] as $key) {
                if ($data[$key] == 0 && isset($predictedData[$key]) && $predictedData[$key] > 1) {
                    $data[$key] = $predictedData[$key];
                }
            }
        }

        return $data;
    }

    // private function detectSensorErrors(array $data): array
    // {
    //     if (
    //         abs($data['bt'] - $data['previous_bt']) > 1 &&
    //         $data['previous_bt'] != 0
    //     ) {
    //         Log::warning("Sudden temperature change detected, possibly sensor error", $data);
    //         $data['bt'] = $data['previous_bt'];
    //     }

    //     if (
    //         $data['bt'] > 39 && ($data['pr'] >= 70 && $data['pr'] <= 90) && $data['os'] > 95 &&
    //         $data['previous_bt'] != 0
    //     ) {
    //         Log::warning("Temperature spike with normal BPM and Saturation, possibly sensor error", $data);
    //         $data['bt'] = $data['previous_bt'];
    //     }

    //     if (
    //         abs($data['bt'] - $data['previous_bt']) > 2 && ($data['pr'] == $data['previous_pr'] && $data['os'] == $data['previous_os'] &&
    //             $data['previous_bt'] != 0 && $data['previous_os'] != 0)
    //     ) {
    //         Log::warning("Temperature change without BPM or saturation change, likely sensor error", $data);
    //         $data['bt'] = $data['previous_bt'];
    //     }

    //     return $data;
    // }

    private function detectSensorErrors(array $data): array
    {
        foreach (['bt', 'pr', 'os', 'sbp', 'dbp'] as $key) {
            $previousKey = 'previous_' . $key;
            if (isset($data[$previousKey]) && $data[$previousKey] != 0 && abs($data[$key] - $data[$previousKey]) > 100) {
                $this->logIssue("Sensor anomaly detected for $key, replacing with median value", $data);
                $data[$key] = $this->calculateMedian([$data[$key], $data[$previousKey]]);
            }
        }
        return $data;
    }

    private function logIssue($message, $data)
    {
        Log::warning($message, $data);
    }

    private function logData($measurement, $watch, $device, $devicePublicWelfare, $gatewaySN)
    {
        $date = date("Y-m-d H:i:s", $measurement['timestamp']);
        ActivityService::insertAllActivityUser([
            'systolic' => $measurement['sbp'],
            'diastolic' => $measurement['dbp'],
            'recorded_at' => $date,
            'device_address' => $watch,
            'is_coming_from_gateway' => true,
            'measureId' => $measurement['measureId'],
            'patient_id' => $device ? $device->patient_id : null,
            'user_customer_id' => $devicePublicWelfare ? $devicePublicWelfare->user_customer_id : null,
            'temperature' => $measurement['bt'],
            'saturation' => $measurement['os'],
            'bpm' => $measurement['pr'],
            'serial_number_gateway' => $gatewaySN,
            'request_body' => json_encode($measurement)
        ]);
    }

    private function saveMeasurement($measurement, $watch, $device, $devicePublicWelfare, $gatewaySN)
    {
        $date = date("Y-m-d H:i:s", $measurement['timestamp']);

        $hr = [
            'bpm' => $measurement['pr'],
            'recorded_at' => $date,
            'device_address' => $watch,
            'is_coming_from_gateway' => true,
            'measureId' => $measurement['measureId'],
            'patient_id' => $device ? $device->patient_id : null,
            'user_customer_id' => $devicePublicWelfare ? $devicePublicWelfare->user_customer_id : null,
            'serial_number_gateway' => $gatewaySN,
        ];

        $check = $this->elasticsearch->getSingle('heart_rate_activity_index', 'recorded_at', 'desc', [
            ['term' => ['is_coming_from_gateway' => true]],
            ['match' => ['device_address' => $watch]],
            ['match' => ['bpm' => $measurement['pr']]],
            ['match' => ['recorded_at' => date("c", $measurement['timestamp'])]]
        ]);


        if (!$check) {
            $this->elasticsearch->save($hr, 'heart_rate_activity_index');
        }

        $bt = [
            'temperature' => $measurement['bt'],
            'recorded_at' => $date,
            'device_address' => $watch,
            'is_coming_from_gateway' => true,
            'measureId' => $measurement['measureId'],
            'patient_id' => $device ? $device->patient_id : null,
            'user_customer_id' => $devicePublicWelfare ? $devicePublicWelfare->user_customer_id : null,
            'serial_number_gateway' => $gatewaySN,
        ];

        $check = $this->elasticsearch->getSingle('body_temperature_activity_index', 'recorded_at', 'desc', [
            ['term' => ['is_coming_from_gateway' => true]],
            ['match' => ['device_address' => $watch]],
            ['match' => ['temperature' => $measurement['bt']]],
            ['match' => ['recorded_at' => date("c", $measurement['timestamp'])]]
        ]);

        if (!$check) {
            $this->elasticsearch->save($bt, 'body_temperature_activity_index');
        }

        $bp = [
            'systolic' => $measurement['sbp'],
            'diastolic' => $measurement['dbp'],
            'recorded_at' => $date,
            'device_address' => $watch,
            'is_coming_from_gateway' => true,
            'measureId' => $measurement['measureId'],
            'patient_id' => $device ? $device->patient_id : null,
            'user_customer_id' => $devicePublicWelfare ? $devicePublicWelfare->user_customer_id : null,
            'serial_number_gateway' => $gatewaySN,
        ];

        $check = $this->elasticsearch->getSingle('blood_pressure_activity_index', 'recorded_at', 'desc', [
            ['term' => ['is_coming_from_gateway' => true]],
            ['match' => ['device_address' => $watch]],
            ['match' => ['systolic' => $measurement['sbp']]],
            ['match' => ['diastolic' => $measurement['dbp']]],
            ['match' => ['recorded_at' => date("c", $measurement['timestamp'])]]
        ]);

        if (!$check) {
            $this->elasticsearch->save($bp, 'blood_pressure_activity_index');
        }




        $os = [
            'saturation' => $measurement['os'],
            'recorded_at' => $date,
            'device_address' => $watch,
            'is_coming_from_gateway' => true,
            'measureId' => $measurement['measureId'],
            'patient_id' => $device ? $device->patient_id : null,
            'user_customer_id' => $devicePublicWelfare ? $devicePublicWelfare->user_customer_id : null,
            'serial_number_gateway' => $gatewaySN,
        ];

        $check = $this->elasticsearch->getSingle('oxygen_saturation_activity_index', 'recorded_at', 'desc', [
            ['term' => ['is_coming_from_gateway' => true]],
            ['match' => ['device_address' => $watch]],
            ['match' => ['saturation' => $measurement['os']]],
            ['match' => ['recorded_at' => date("c", $measurement['timestamp'])]]
        ]);

        if (!$check) {
            $this->elasticsearch->save($os, 'oxygen_saturation_activity_index');
        }

    }


    // private function sendEmergencyOld($measurement, $device, $devicePublicWelfare)
    // {
    //     $date = date("Y-m-d H:i:s", $measurement['timestamp']);
    //     if ($device) {
    //         $activty = new \stdClass();
    //         $activty->bpm = $measurement['pr'];
    //         $activty->temperature = $measurement['bt'];
    //         $activty->systolic = $measurement['sbp'];
    //         $activty->diastolic = $measurement['dbp'];
    //         $activty->saturation = $measurement['os'];
    //         $activty->recorded_at = $date;
    //         if ($device->patient_id) {
    //             $getPatientsEmergency = ActivityService::getPatientsEmergencyByPatientIdHospital($device->patient_id, $activty, true);
    //             // Log::info('Sending Emergency Patient # ' . json_encode($getPatientsEmergency));
    //             if (count($getPatientsEmergency) > 0) {
    //                 AlarmEmergencyHospitalJob::dispatch($getPatientsEmergency);
    //             }
    //         }
    //     }

    //     if ($devicePublicWelfare) {
    //         $activty = new \stdClass();
    //         $activty->bpm = $measurement['pr'];
    //         $activty->temperature = $measurement['bt'];
    //         $activty->systolic = $measurement['sbp'];
    //         $activty->diastolic = $measurement['dbp'];
    //         $activty->saturation = $measurement['os'];
    //         $activty->recorded_at = $date;
    //         if ($devicePublicWelfare->user_customer_id) {
    //             $getCustomersEmergency = ActivityService::getCustomerEmergencyByCustomerIdPublicWelfare($devicePublicWelfare->user_customer_id, $activty, true);
    //             // Log::info('Sending Emergency Customer Public Welfare# ' . json_encode($getCustomersEmergency));
    //             if (count($getCustomersEmergency) > 0) {
    //                 AlarmEmergencyPublicWelfareJob::dispatch($getCustomersEmergency);
    //             }
    //         }
    //     }
    // }


    private function sendEmergencyNew($measurement, $device, $devicePublicWelfare)
    {
        $date = date("Y-m-d H:i:s", $measurement['timestamp']);
        $measurement = $this->detectEmergencyCondition($measurement);
        if ($measurement['alert_level'] !== 'none') {
            if ($device && $device->patient_id) {
                $activty = new \stdClass();
                $activty->bpm = $measurement['pr'];
                $activty->temperature = $measurement['bt'];
                $activty->systolic = $measurement['sbp'];
                $activty->diastolic = $measurement['dbp'];
                $activty->saturation = $measurement['os'];
                $activty->recorded_at = $date;
                $getPatientsEmergency = $this->getPatientsEmergencyByPatientIdHospital($device->patient_id, $activty, $measurement['alert_desc']);
                if (count($getPatientsEmergency) > 0) {
                    AlarmEmergencyHospitalJob::dispatch($getPatientsEmergency);
                }
            }
            if ($devicePublicWelfare && $devicePublicWelfare->user_customer_id) {
                $activty = new \stdClass();
                $activty->bpm = $measurement['pr'];
                $activty->temperature = $measurement['bt'];
                $activty->systolic = $measurement['sbp'];
                $activty->diastolic = $measurement['dbp'];
                $activty->saturation = $measurement['os'];
                $activty->recorded_at = $date;
                $getCustomersEmergency = $this->getCustomerEmergencyByCustomerIdPublicWelfare($devicePublicWelfare->user_customer_id, $activty, $measurement['alert_desc']);
                if (count($getCustomersEmergency) > 0) {
                    AlarmEmergencyPublicWelfareJob::dispatch($getCustomersEmergency);
                }
            }
        }
    }

    // private function detectEmergencyMessage(array $data)
    // {
    //     $message = '';
    // if ($data['bt'] > 39 && $data['pr'] > 120) {
    //     // Log::emergency("Critical Alert! High fever and rapid heart rate detected", $data);
    //     $message = '중요 경보! 고열과 빠른 심박수 감지';
    // } elseif ($data['pr'] < 40) {
    //     // Log::emergency("Emergency Alert! BPM dropped sharply below 40", $data);
    //     $message = '긴급 경보! BPM이 40 이하로 급격히 떨어졌습니다.';
    // } elseif ($data['bt'] > 37.5 && $data['pr'] > 100 && $data['os'] < 92) {
    //     // Log::emergency("Critical Condition Detected", $data);
    //     $message = '중대한 상태 감지됨';
    // }
    //     return $message;
    // }

    // Multi-Level Emergency Alert System
    private function detectEmergencyCondition(array $data): array
    {
        $alertLevel = 'none';
        $alertDescription = '';

        // Stage 1: Immediate Warning Conditions (single reading check)
        if ($data['pr'] < 45 && $data['os'] < 92) {
            Log::alert("Stage 1 Alert: Possible bradycardia!", $data);
            $alertLevel = 'warning';
            $alertDescription = '1단계 경고: 심박수 감소가 나타날 수 있습니다!';
        }
        // Stage 2: Immediate Critical Conditions (single reading check)
        if ($data['pr'] > 130 && $data['os'] < 94) {
            Log::alert("Stage 2 Alert: Possible tachycardia!", $data);
            $alertLevel = 'critical';
            $alertDescription = '2단계 경보: 빈맥이 나타날 수 있습니다!';
        }
        // Most severe: Stage 3 Emergency (immediate check)
        if ($data['pr'] < 40 && $data['os'] < 92 && $data['sbp'] < 90) {
            Log::alert("Stage 3 Emergency: Possible cardiac arrest!", $data);
            $alertLevel = 'emergency';
            $alertDescription = '3단계 응급상황: 심장마비 가능성이 있습니다!';
        }
        // Additional severe conditions using previous reading
        elseif ($data['pr'] < 45 && $data['previous_pr'] < 45 && $data['os'] < 92) {
            $alertLevel = 'emergency';
            $alertDescription = '긴급 경보! 서맥 (브래디카르디아) 발생: 심박수가 45 이하로 떨어졌습니다.';
        } elseif ($data['pr'] > 130 && $data['previous_pr'] > 130 && $data['os'] < 94) {
            $alertLevel = 'emergency';
            $alertDescription = '긴급 경보! 빠른 심박수 (빈맥) 발생: 심박수가 130 이상입니다.';
        } elseif (($data['previous_pr'] - $data['pr']) >= 30 && $data['os'] < 90) {
            $alertLevel = 'emergency';
            $alertDescription = '긴급 경보! 심박수 급락: 30 이상 하락하였으며, 산소포화도가 90% 이하입니다.';
        }
        // Stage 2: Critical Conditions
        elseif ($data['pr'] > 130 && $data['os'] < 94) {
            Log::alert("Stage 2 Alert: Possible tachycardia!", $data);
            $alertLevel = 'critical';
            $alertDescription = '2단계 경보: 빈맥이 나타날 수 있습니다!';
        } elseif (
            ($data['os'] < 91 && $data['previous_os'] < 91)
            || ($data['os'] < 88 && $data['previous_os'] < 88)
        ) {
            $alertLevel = 'critical';
            $alertDescription = '중대한 경고! 심각한 저산소증 발생: 산소포화도가 91% 이하로 떨어졌습니다.';
        } elseif (($data['sbp'] > 180 || $data['dbp'] > 120) && $data['pr'] > 110 && $data['os'] < 94) {
            $alertLevel = 'critical';
            $alertDescription = '중대한 경고! 심각한 고혈압 발생: 수축기 혈압이 180 이상이거나 이완기 혈압이 120 이상입니다.';
        } elseif ($data['bt'] > 39 && $data['pr'] > 120) {
            $alertLevel = 'critical';
            $alertDescription = '중요 경보! 고열과 빠른 심박수 감지';
        } elseif ($data['bt'] > 37.5 && $data['pr'] > 100 && $data['os'] < 92) {
            $alertLevel = 'critical';
            $alertDescription = '중대한 상태 감지됨';
        }
        // Stage 1: Warning Conditions
        elseif ($data['pr'] < 45 && $data['os'] < 92) {
            Log::alert("Stage 1 Alert: Possible bradycardia!", $data);
            $alertLevel = 'warning';
            $alertDescription = '1단계 경고: 심박수 감소가 나타날 수 있습니다!';
        } elseif ($data['os'] < 94 && $data['previous_os'] < 94 && $data['pr'] > 110) {
            $alertLevel = 'warning';
            $alertDescription = '경고! 경미한 저산소증 발생: 산소포화도가 94% 이하로 떨어졌고 심박수가 110 이상입니다.';
        } elseif (($data['sbp'] < 90 || $data['dbp'] < 60) && $data['pr'] < 50) {
            $alertLevel = 'warning';
            $alertDescription = '경고! 저혈압 발생: 수축기 혈압이 90 이하이거나 이완기 혈압이 60 이하입니다.';
        }
        // Additional blood pressure condition (Severe Hypotension)
        elseif ($data['sbp'] < 80 && $data['dbp'] < 50 && $data['pr'] < 50 && $data['os'] < 90) {
            $alertLevel = 'critical';
            $alertDescription = '중대한 경고! 심각한 저혈압 발생: 수축기 혈압이 80 이하이거나 이완기 혈압이 50 이하입니다.';
        }
        // If high blood pressure but not severe
        elseif ($data['sbp'] > 150 || $data['dbp'] > 100) {
            $alertLevel = 'warning';
            $alertDescription = '경고! 고혈압 발생: 수축기 혈압이 150 이상이거나 이완기 혈압이 100 이상입니다.';
        }
        // Multi-Vital Emergency Condition Detection
        elseif ($data['pr'] < 40 && $data['os'] < 92 && $data['sbp'] < 90 && $data['dbp'] < 60) {
            $alertLevel = 'emergency';
            $alertDescription = '경고! 심부전 가능성: 심박수가 40 이하, 산소포화도 92% 이하, 혈압이 낮습니다.';
        } elseif (
            $data['os'] < 88 && $data['pr'] > 130 && $data['sbp'] < 100 && $data['dbp'] < 70 &&
            $data['previous_os'] < 88 && $data['previous_pr'] > 130 && $data['previous_sb'] < 100 && $data['previous_dbp'] < 70
        ) {
            $alertLevel = 'emergency';
            $alertDescription = '긴급 경고! 심각한 호흡곤란 발생: 산소포화도가 88% 이하, 심박수가 130 이상, 혈압이 낮습니다.';
        } elseif ($data['pr'] < 40) {
            $alertLevel = 'emergency';
            $alertDescription = '긴급 경보! BPM이 40 이하로 급격히 떨어졌습니다.';
        }

        $data['alert_level'] = $alertLevel;
        $data['alert_desc'] = $alertDescription;
        return $data;
    }



    // private function detectEmergencyMessage(array $data)
    // {
    //     $message = '';

    // **Heart Rate (BPM) Criteria**
    // Bradycardia: BPM < 45 detected twice in a row with SpO₂ < 92%
    // if ($data['pr'] < 45 && $data['previous_pr'] < 45 && $data['os'] < 92) {
    //     $message = '긴급 경보! 서맥 (브래디카르디아) 발생: 심박수가 45 이하로 떨어졌습니다.';
    // }
    // // Tachycardia: BPM > 130 detected 3 times in a row with SpO₂ < 94%
    // elseif ($data['pr'] > 130 && $data['previous_pr'] > 130 && $data['previous_pr'] > 130 && $data['os'] < 94) {
    //     $message = '긴급 경보! 빠른 심박수 (빈맥) 발생: 심박수가 130 이상입니다.';
    // }
    // // Rapid Heart Rate Drop: BPM decreases by 30+ within 6 sec and SpO₂ < 90%
    // elseif (($data['previous_pr'] - $data['pr']) >= 30 && $data['os'] < 90) {
    //     $message = '긴급 경보! 심박수 급락: 30 이상 하락하였으며, 산소포화도가 90% 이하입니다.';
    // }

    // // **Oxygen Saturation (SpO₂) Criteria**
    // // Mild Hypoxia (1st Alert): SpO₂ < 94% detected 3 times in a row and BPM > 110
    // elseif ($data['os'] < 94 && $data['previous_os'] < 94 && $data['previous_os'] < 94 && $data['pr'] > 110) {
    //     $message = '경고! 경미한 저산소증 발생: 산소포화도가 94% 이하로 떨어졌고 심박수가 110 이상입니다.';
    // }
    // // Severe Hypoxia (2nd Alert): SpO₂ < 91% detected 3 times in a row or SpO₂ < 88% detected twice in a row
    // elseif (($data['os'] < 91 && $data['previous_os'] < 91 && $data['previous_os'] < 91) || ($data['os'] < 88 && $data['previous_os'] < 88)) {
    //     $message = '중대한 경고! 심각한 저산소증 발생: 산소포화도가 91% 이하로 떨어졌습니다.';
    // }

    // // **Blood Pressure (BP) & Temperature Criteria**
    // // Hypertension (1st Alert): Sys > 150 or Dia > 100 and BPM > 90
    // elseif (($data['sbp'] > 150 || $data['dbp'] > 100) && $data['pr'] > 90) {
    //     $message = '경고! 고혈압 발생: 수축기 혈압이 150 이상이거나 이완기 혈압이 100 이상입니다.';
    // }
    // // Severe Hypertension (2nd Alert): Sys > 180 or Dia > 120, BPM > 110, and SpO₂ < 94%
    // elseif (($data['sbp'] > 180 || $data['dbp'] > 120) && $data['pr'] > 110 && $data['os'] < 94) {
    //     $message = '중대한 경고! 심각한 고혈압 발생: 수축기 혈압이 180 이상이거나 이완기 혈압이 120 이상입니다.';
    // }
    // // Hypotension (1st Alert): Sys < 90 or Dia < 60, BPM < 50
    // elseif (($data['sbp'] < 90 || $data['dbp'] < 60) && $data['pr'] < 50) {
    //     $message = '경고! 저혈압 발생: 수축기 혈압이 90 이하이거나 이완기 혈압이 60 이하입니다.';
    // }
    // // Severe Hypotension (2nd Alert): Sys < 80 or Dia < 50, BPM < 50, SpO₂ < 90%
    // elseif ($data['sbp'] < 80 && $data['dbp'] < 50 && $data['pr'] < 50 && $data['os'] < 90) {
    //     $message = '중대한 경고! 심각한 저혈압 발생: 수축기 혈압이 80 이하이거나 이완기 혈압이 50 이하입니다.';
    // }

    // // **Multi-Vital Emergency Condition Detection**
    // // Severe Infection Possible: Temperature > 39°C, BPM > 120, SpO₂ < 94% (2 times in a row)
    // elseif ($data['bt'] > 39 && $data['pr'] > 120 && $data['os'] < 94 && $data['previous_bt'] > 39 && $data['previous_pr'] > 120 && $data['previous_os'] < 94) {
    //     $message = '중대한 경고! 감염 의심: 고열, 빠른 심박수, 산소포화도 저하가 지속되고 있습니다.';
    // }
    // // Heart Failure Possible: BPM < 40 & SpO₂ < 92% & BP < 90/60 (lasting more than 6 sec)
    // elseif ($data['pr'] < 40 && $data['os'] < 92 && $data['sbp'] < 90 && $data['dbp'] < 60) {
    //     $message = '경고! 심부전 가능성: 심박수가 40 이하, 산소포화도 92% 이하, 혈압이 낮습니다.';
    // }
    // // Severe Respiratory Distress: SpO₂ < 88% & BPM > 130 & BP < 100/70 (2 times in a row)
    // elseif ($data['os'] < 88 && $data['pr'] > 130 && $data['sbp'] < 100 && $data['dbp'] < 70 && $data['previous_os'] < 88 && $data['previous_pr'] > 130 && $data['previous_sb'] < 100 && $data['previous_dbp'] < 70) {
    //     $message = '긴급 경고! 심각한 호흡곤란 발생: 산소포화도가 88% 이하, 심박수가 130 이상, 혈압이 낮습니다.';
    // }

    //     return $message;
    // }

    private function getPatientsEmergencyByPatientIdHospital($patientId, $activity, $description)
    {
        $result = [];
        if ($activity->systolic > 0 && $activity->diastolic) {
            $stateSystolic = ActivityService::determineStateHospital($patientId, $activity->systolic, 'blood_pressure_systolic');
            $stateDiastolic = ActivityService::determineStateHospital($patientId, $activity->diastolic, 'blood_pressure_diastolic');
            $patient = PatientHospital::where('id', $patientId)->where('status', 'IN_PATIENT')->first();
            if ($patient) {
                if ($stateSystolic == 'danger' || $stateSystolic == 'caution') {
                    $alert_details = config('app.lang') != 'en' ? '혈압 수축기' : 'Blood pressure Systolic';
                    array_push($result, [
                        'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                        'name' => $patient->name,
                        'alert_details' => $alert_details . ' ' . $activity->systolic,
                        'ward' => $patient->hospital_room,
                        'alert_type' => $stateSystolic,
                        'doctor_in_charge' => $patient->doctor_in_charge,
                        'nurse_in_charge' => $patient->nurse_in_charge,
                        'user_general_purpose_id' => $patient->user_general_purpose_id,
                        'patient_id' => $patient->id,
                    ]);
                }
                if ($stateDiastolic == 'danger' || $stateDiastolic == 'caution') {
                    $alert_details = config('app.lang') != 'en' ? '혈압 이완기' : 'Blood pressure Diastolic';
                    if (count($result) > 0) {
                        $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->diastolic;
                        $result[0]['alert_type'] .= ',' . $stateDiastolic;
                    } else {
                        array_push($result, [
                            'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                            'name' => $patient->name,
                            'alert_details' => $alert_details . ' ' . $activity->diastolic,
                            'ward' => $patient->hospital_room,
                            'alert_type' => $stateDiastolic,
                            'doctor_in_charge' => $patient->doctor_in_charge,
                            'nurse_in_charge' => $patient->nurse_in_charge,
                            'user_general_purpose_id' => $patient->user_general_purpose_id,
                            'patient_id' => $patient->id,
                        ]);
                    }
                }
            }
        }
        if ($activity->bpm > 0) {
            $state = ActivityService::determineStateHospital($patientId, $activity->bpm, 'heart_rate');
            if ($state == 'danger' || $state == 'caution') {
                $patient = PatientHospital::where('id', $patientId)->where('status', 'IN_PATIENT')->first();
                if ($patient) {
                    $alert_details = config('app.lang') != 'en' ? '심박수' : 'Heart rate';
                    if (count($result) > 0) {
                        $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->bpm;
                        $result[0]['alert_type'] .= ',' . $state;
                    } else {
                        array_push($result, [
                            'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                            'name' => $patient->name,
                            'alert_details' => $alert_details . ' ' . $activity->bpm,
                            'ward' => $patient->hospital_room,
                            'alert_type' => $state,
                            'doctor_in_charge' => $patient->doctor_in_charge,
                            'nurse_in_charge' => $patient->nurse_in_charge,
                            'user_general_purpose_id' => $patient->user_general_purpose_id,
                            'patient_id' => $patient->id,
                        ]);
                    }
                }
            }
        }
        if ($activity->saturation > 0) {
            $state = ActivityService::determineStateHospital($patientId, $activity->saturation, 'oxygen_saturation');
            if ($state == 'danger' || $state == 'caution') {
                $patient = PatientHospital::where('id', $patientId)->where('status', 'IN_PATIENT')->first();
                if ($patient) {
                    $alert_details = config('app.lang') != 'en' ? '산소포화도' : 'Oxygen saturation';
                    if (count($result) > 0) {
                        $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->saturation;
                        $result[0]['alert_type'] .= ',' . $state;
                    } else {
                        array_push($result, [
                            'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                            'name' => $patient->name,
                            'alert_details' => $alert_details . ' ' . $activity->saturation,
                            'ward' => $patient->hospital_room,
                            'alert_type' => $state,
                            'doctor_in_charge' => $patient->doctor_in_charge,
                            'nurse_in_charge' => $patient->nurse_in_charge,
                            'user_general_purpose_id' => $patient->user_general_purpose_id,
                            'patient_id' => $patient->id,
                        ]);
                    }
                }
            }
        }
        if ($activity->temperature > 0) {
            $state = ActivityService::determineStateHospital($patientId, $activity->temperature, 'body_temperature');
            if ($state == 'danger' || $state == 'caution') {
                $patient = PatientHospital::where('id', $patientId)->where('status', 'IN_PATIENT')->first();
                if ($patient) {
                    $alert_details = config('app.lang') != 'en' ? '체온' : 'Body temperature';
                    if (count($result) > 0) {
                        $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->temperature;
                        $result[0]['alert_type'] .= ',' . $state;
                    } else {
                        array_push($result, [
                            'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                            'name' => $patient->name,
                            'alert_details' => $alert_details . ' ' . $activity->temperature,
                            'ward' => $patient->hospital_room,
                            'alert_type' => $state,
                            'doctor_in_charge' => $patient->doctor_in_charge,
                            'nurse_in_charge' => $patient->nurse_in_charge,
                            'user_general_purpose_id' => $patient->user_general_purpose_id,
                            'patient_id' => $patient->id,
                        ]);
                    }
                }
            }
        }
        if (count($result) > 0) {
            $result[0]['alert_details'] = $description . ' ' . $result[0]['alert_details'];
        }
        return $result;
    }

    public function getCustomerEmergencyByCustomerIdPublicWelfare($customerId, $activity, $description)
    {
        $result = [];
        $user = UserManagementPublicWelfare::where('user_customer_id', $customerId)->first();
        if (!$user) {
            return $result;
        }
        $customerId = $user->user_general_purpose_id;
        if (!$customerId) {
            return $result;
        }
        if (isset($activity->systolic) && isset($activity->diastolic)) {
            if ($activity->systolic > 0 && $activity->diastolic > 0) {
                $stateSystolic = ActivityService::determineStatePublicWelfare($customerId, $activity->systolic, 'blood_pressure_systolic');
                $stateDiastolic = ActivityService::determineStatePublicWelfare($customerId, $activity->diastolic, 'blood_pressure_diastolic');
                if ($stateSystolic == 'danger' || $stateSystolic == 'caution' || $stateDiastolic == 'danger' || $stateDiastolic == 'caution') {
                    if ($user) {
                        if ($stateSystolic == 'danger' || $stateSystolic == 'caution') {
                            $alert_details = config('app.lang') != 'en' ? '혈압 수축기' : 'Blood pressure Systolic';
                            array_push($result, [
                                'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                                'name' => $user->name,
                                'gender' => $user->gender,
                                'age' => $user->age,
                                'contact' => $user->contact_information,
                                'alert_details' => $alert_details . ' ' . $activity->systolic,
                                'ward' => '',
                                'alert_type' => $stateSystolic,
                                'doctor_in_charge' => '',
                                'nurse_in_charge' => '',
                                'user_general_purpose_id' => $user->user_general_purpose_id,
                                'user_customer_id' => $user->user_customer_id,
                            ]);
                        }
                        if ($stateDiastolic == 'danger' || $stateDiastolic == 'caution') {
                            $alert_details = config('app.lang') != 'en' ? '혈압 이완기' : 'Blood pressure Diastolic';
                            if (count($result) > 0) {
                                $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->diastolic;
                                $result[0]['alert_type'] .= ',' . $stateDiastolic;
                            } else {
                                array_push($result, [
                                    'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                                    'name' => $user->name,
                                    'gender' => $user->gender,
                                    'age' => $user->age,
                                    'contact' => $user->contact_information,
                                    'alert_details' => $alert_details . ' ' . $activity->diastolic,
                                    'ward' => '',
                                    'alert_type' => $stateDiastolic,
                                    'doctor_in_charge' => '',
                                    'nurse_in_charge' => '',
                                    'user_general_purpose_id' => $user->user_general_purpose_id,
                                    'user_customer_id' => $user->user_customer_id,
                                ]);
                            }
                        }
                    }
                }
            }
        }
        if (isset($activity->bpm)) {
            if ($activity->bpm > 0) {
                $state = ActivityService::determineStatePublicWelfare($customerId, $activity->bpm, 'heart_rate');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '심박수' : 'Heart rate';
                        if (count($result) > 0) {
                            $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->bpm;
                            $result[0]['alert_type'] .= ',' . $state;
                        } else {
                            array_push($result, [
                                'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                                'name' => $user->name,
                                'gender' => $user->gender,
                                'age' => $user->age,
                                'contact' => $user->contact_information,
                                'alert_details' => $alert_details . ' ' . $activity->bpm,
                                'ward' => '',
                                'alert_type' => $state,
                                'doctor_in_charge' => '',
                                'nurse_in_charge' => '',
                                'user_general_purpose_id' => $user->user_general_purpose_id,
                                'user_customer_id' => $user->user_customer_id,
                            ]);
                        }
                    }
                }
            }
        }
        if (isset($activity->saturation)) {
            if ($activity->saturation > 0) {
                $state = ActivityService::determineStatePublicWelfare($customerId, $activity->saturation, 'oxygen_saturation');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '산소포화도' : 'Oxygen saturation';
                        if (count($result) > 0) {
                            $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->saturation;
                            $result[0]['alert_type'] .= ',' . $state;
                        } else {
                            array_push($result, [
                                'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                                'name' => $user->name,
                                'gender' => $user->gender,
                                'age' => $user->age,
                                'contact' => $user->contact_information,
                                'alert_details' => $alert_details . ' ' . $activity->saturation,
                                'ward' => '',
                                'alert_type' => $state,
                                'doctor_in_charge' => '',
                                'nurse_in_charge' => '',
                                'user_general_purpose_id' => $user->user_general_purpose_id,
                                'user_customer_id' => $user->user_customer_id,
                            ]);
                        }
                    }
                }
            }
        }
        if (isset($activity->temperature)) {
            if ($activity->temperature > 0) {
                $state = ActivityService::determineStatePublicWelfare($customerId, $activity->temperature, 'body_temp');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '체온' : 'Body temperature';
                        if (count($result) > 0) {
                            $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->temperature;
                            $result[0]['alert_type'] .= ',' . $state;
                        } else {
                            array_push($result, [
                                'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                                'name' => $user->name,
                                'gender' => $user->gender,
                                'age' => $user->age,
                                'contact' => $user->contact_information,
                                'alert_details' => $alert_details . ' ' . $activity->temperature,
                                'ward' => '',
                                'alert_type' => $state,
                                'doctor_in_charge' => '',
                                'nurse_in_charge' => '',
                                'user_general_purpose_id' => $user->user_general_purpose_id,
                                'user_customer_id' => $user->user_customer_id,
                            ]);
                        }
                    }
                }
            }
        }
        if (isset($activity->rate)) {
            if ($activity->rate > 0) {
                $state = ActivityService::determineStatePublicWelfare($customerId, $activity->rate, 'respiratory_rate');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '호흡수' : 'Respiratory Rate';
                        if (count($result) > 0) {
                            $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->rate;
                            $result[0]['alert_type'] .= ',' . $state;
                        } else {
                            array_push($result, [
                                'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                                'name' => $user->name,
                                'gender' => $user->gender,
                                'age' => $user->age,
                                'contact' => $user->contact_information,
                                'alert_details' => $alert_details . ' ' . $activity->rate,
                                'ward' => '',
                                'alert_type' => $state,
                                'doctor_in_charge' => '',
                                'nurse_in_charge' => '',
                                'user_general_purpose_id' => $user->user_general_purpose_id,
                                'user_customer_id' => $user->user_customer_id,
                            ]);
                        }
                    }
                }
            }
        }
        if (isset($activity->bpmEcg)) {
            if ($activity->bpmEcg > 0) {
                $state = ActivityService::determineStatePublicWelfare($customerId, $activity->bpmEcg, 'ecg');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '심전도' : 'ECG';
                        if (count($result) > 0) {
                            $result[0]['alert_details'] .= ',' . $alert_details . ' ' . $activity->bpmEcg;
                            $result[0]['alert_type'] .= ',' . $state;
                        } else {
                            array_push($result, [
                                'recorded_at' => Carbon::parse($activity->recorded_at)->format('Y-m-d'),
                                'name' => $user->name,
                                'gender' => $user->gender,
                                'age' => $user->age,
                                'contact' => $user->contact_information,
                                'alert_details' => $alert_details . ' ' . $activity->bpmEcg,
                                'ward' => '',
                                'alert_type' => $state,
                                'doctor_in_charge' => '',
                                'nurse_in_charge' => '',
                                'user_general_purpose_id' => $user->user_general_purpose_id,
                                'user_customer_id' => $user->user_customer_id,
                            ]);
                        }
                    }
                }
            }
        }
        if (count($result) > 0) {
            $result[0]['alert_details'] = $description . ' ' . $result[0]['alert_details'];
        }
        return $result;
    }
}