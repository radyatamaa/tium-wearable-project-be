<?php

namespace App\Jobs;

use App\Services\ActivityService;
use App\Services\ElasticSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DateTime;
use DateInterval;
class ActivitySyncHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $activities;
    protected $type;
    protected $request;
    protected $userCustomerId;
    public function __construct($activities, $type, $request, $userCustomerId)
    {
        $this->activities = $activities;
        $this->type = $type;
        $this->request = $request;
        $this->userCustomerId = $userCustomerId;
    }

    /**
     * Execute the job.
     */
    // public function handle(): void
    // {
    //     $elasticsearch = app()->make(\App\Services\ElasticSearchService::class);
    //     $activityModels = [
    //         'blood_pressure' => BloodPressureActivity::class,
    //         'heart_rate' => HeartRateActivity::class,
    //         'oxygen_saturation' => OxygenSaturationActivity::class,
    //         'body_temperature' => BodyTemperatureActivity::class,
    //         'respiratory_rate' => RespiratoryRateActivity::class,
    //     ];

    //     $data = $this->activities;
    //     $startTime = new DateTime('now'); // waktu awal diatur ke waktu saat ini
    //     $startTime->setTime(0, 10); // Set time to 00:10

    //     if ($this->type == 'blood_pressure') {
    //         $systolic = $data[0];
    //         $diastolic = $data[1];
    //         for ($index = 0; $index < count($systolic); $index++) {
    //             if ($systolic[$index] == 0) {
    //                 continue;
    //             }
    //             $time = clone $startTime;
    //             $time->add(new DateInterval('PT' . ($index * 10) . 'M'));

    //             $formattedTime = $time->format('Y-m-d H:i:s');

    //             $check = $activityModels[$this->type]::where('recorded_at', $formattedTime)->first();

    //             if ($check) {
    //                 continue;
    //             }

    //             $bp = [
    //                 'device_address' => $this->request['device_address'],
    //                 'user_customer_id' => $this->userCustomerId,
    //                 'systolic' => $systolic[$index],
    //                 'diastolic' => $diastolic[$index],
    //                 'pulse' => null,
    //                 'recorded_at' => $formattedTime,
    //                 'measureId' => null,
    //                 'is_history' => true,

    //                 'patient_id' => null,
    //                 'is_coming_from_gateway' => false,
    //             ];
    //             BloodPressureActivity::create($bp);
    //             $elasticsearch->save($bp, 'blood_pressure_activity_index');
    //             ActivityService::insertAllActivityUser([
    //                 'recorded_at' => $formattedTime,
    //                 'device_address' => $this->request['device_address'],
    //                 'is_coming_from_gateway' => false,
    //                 'measureId' => null,
    //                 'user_customer_id' => $this->userCustomerId,
    //                 'systolic' => $systolic[$index],
    //                 'diastolic' => $diastolic[$index],
    //                 'pulse' => null,
    //                 'is_history' => true
    //             ]);
    //         }
    //     } else {
    //         foreach ($data as $index => $value) {
    //             if ($value == 0) {
    //                 continue;
    //             }
    //             $time = clone $startTime;
    //             $time->add(new DateInterval('PT' . ($index * 10) . 'M'));

    //             $formattedTime = $time->format('Y-m-d H:i:s');

    //             $check = $activityModels[$this->type]::where('recorded_at', $formattedTime)->first();

    //             if ($check) {
    //                 continue;
    //             }

    //             switch ($this->type) {
    //                 case 'heart_rate':
    //                     $data = [
    //                         'user_customer_id' => $this->userCustomerId,
    //                         'bpm' => $value,
    //                         'device_address' => $this->request['device_address'],
    //                         'recorded_at' => $formattedTime,
    //                         'hrv' => null,
    //                         'activity_type' => null,
    //                         'cardiac_health_index' => null,
    //                         'measureId' => null,
    //                         'is_history' => true,

    //                         'patient_id' => null,
    //                         'is_coming_from_gateway' => false,
    //                     ];
    //                     HeartRateActivity::create($data);
    //                     $elasticsearch->save($data, 'heart_rate_activity_index');
    //                     ActivityService::insertAllActivityUser([
    //                         'recorded_at' => $formattedTime,
    //                         'device_address' => $this->request['device_address'],
    //                         'is_coming_from_gateway' => false,
    //                         'measureId' => null,
    //                         'user_customer_id' => $this->userCustomerId,
    //                         'bpm' => $value,
    //                         'hrv' => null,
    //                         'hr_activity_type' => null,
    //                         'cardiac_health_index' => null,
    //                         'is_history' => true
    //                     ]);
    //                     break;
    //                 case 'body_temperature':
    //                     $data = [
    //                         'device_address' => $this->request['device_address'],
    //                         'temperature' => $value,
    //                         'recorded_at' => $formattedTime,
    //                         'user_customer_id' => $this->userCustomerId,
    //                         'measureId' => null,
    //                         'is_history' => true,

    //                         'patient_id' => null,
    //                         'is_coming_from_gateway' => false,
    //                     ];
    //                     BodyTemperatureActivity::create($data);
    //                     $elasticsearch->save($data, 'body_temperature_activity_index');
    //                     ActivityService::insertAllActivityUser([
    //                         'recorded_at' => $formattedTime,
    //                         'device_address' => $this->request['device_address'],
    //                         'is_coming_from_gateway' => false,
    //                         'measureId' => null,
    //                         'user_customer_id' => $this->userCustomerId,
    //                         'temperature' => $value,
    //                         'is_history' => true
    //                     ]);
    //                     break;
    //                 case 'oxygen_saturation':
    //                     $data = [
    //                         'device_address' => $this->request['device_address'],
    //                         'user_customer_id' => $this->userCustomerId,
    //                         'saturation' => $value,
    //                         'activity_type' => null,
    //                         'recorded_at' => $formattedTime,
    //                         'measureId' => null,
    //                         'is_history' => true,

    //                         'patient_id' => null,
    //                         'is_coming_from_gateway' => false,
    //                     ];
    //                     OxygenSaturationActivity::create($data);
    //                     $elasticsearch->save($data, 'oxygen_saturation_activity_index');
    //                     ActivityService::insertAllActivityUser([
    //                         'recorded_at' => $formattedTime,
    //                         'device_address' => $this->request['device_address'],
    //                         'is_coming_from_gateway' => false,
    //                         'measureId' => null,
    //                         'user_customer_id' => $this->userCustomerId,
    //                         'saturation' => $value,
    //                         'os_activity_type' => null,
    //                         'is_history' => true
    //                     ]);
    //                     break;
    //                 case 'respiratory_rate':
    //                     $data = [
    //                         'device_address' => $this->request['device_address'],
    //                         'user_customer_id' => $this->userCustomerId,
    //                         'rate' => $value,
    //                         'activity_type' => null,
    //                         'recorded_at' => $formattedTime,
    //                         'measureId' => null,
    //                         'is_history' => true,

    //                         'patient_id' => null,
    //                         'is_coming_from_gateway' => false,
    //                     ];
    //                     RespiratoryRateActivity::create($data);
    //                     $elasticsearch->save($data, 'respiratory_rate_activity_index');
    //                     ActivityService::insertAllActivityUser([
    //                         'recorded_at' => $formattedTime,
    //                         'device_address' => $this->request['device_address'],
    //                         'is_coming_from_gateway' => false,
    //                         'measureId' => null,
    //                         'user_customer_id' => $this->userCustomerId,
    //                         'respiratory_rate' => $value,
    //                         'rr_activity_type' => null,
    //                         'is_history' => true
    //                     ]);
    //                     break;
    //             }
    //         }
    //     }

    // }

    public function handle(): void
    {
        $elasticsearch = app()->make(\App\Services\ElasticSearchService::class);

        $activityIndices = [
            'blood_pressure' => 'blood_pressure_activity_index',
            'heart_rate' => 'heart_rate_activity_index',
            'oxygen_saturation' => 'oxygen_saturation_activity_index',
            'body_temperature' => 'body_temperature_activity_index',
            'respiratory_rate' => 'respiratory_rate_activity_index',
        ];

        $data = $this->activities;
        $startTime = new DateTime('now');
        $startTime->setTime(0, 10);

        if ($this->type === 'blood_pressure') {
            $systolic = $data[0];
            $diastolic = $data[1];

            for ($index = 0; $index < count($systolic); $index++) {
                if ($systolic[$index] == 0) {
                    continue;
                }

                $time = clone $startTime;
                $time->add(new DateInterval('PT' . ($index * 10) . 'M'));
                $formattedTime = $time->format('Y-m-d H:i:s');

                $bpData = [
                    'device_address' => $this->request['device_address'],
                    'user_customer_id' => $this->userCustomerId,
                    'systolic' => $systolic[$index],
                    'diastolic' => $diastolic[$index],
                    'pulse' => null,
                    'recorded_at' => $formattedTime,
                    'measureId' => null,
                    'is_history' => true,
                    'patient_id' => null,
                    'is_coming_from_gateway' => false,
                ];

                $elasticsearch->save($bpData, $activityIndices['blood_pressure']);
                ActivityService::insertAllActivityUser($bpData);
            }
        } else {
            foreach ($data as $index => $value) {
                if ($value == 0) {
                    continue;
                }

                $time = clone $startTime;
                $time->add(new DateInterval('PT' . ($index * 10) . 'M'));
                $formattedTime = $time->format('Y-m-d H:i:s');

                $commonData = [
                    'device_address' => $this->request['device_address'],
                    'user_customer_id' => $this->userCustomerId,
                    'recorded_at' => $formattedTime,
                    'measureId' => null,
                    'is_history' => true,
                    'patient_id' => null,
                    'is_coming_from_gateway' => false,
                ];

                switch ($this->type) {
                    case 'heart_rate':
                        $hrData = array_merge($commonData, [
                            'bpm' => $value,
                            'hrv' => null,
                            'activity_type' => null,
                            'cardiac_health_index' => null,
                        ]);
                        $elasticsearch->save($hrData, $activityIndices['heart_rate']);
                        ActivityService::insertAllActivityUser($hrData);
                        break;

                    case 'body_temperature':
                        $btData = array_merge($commonData, [
                            'temperature' => $value,
                        ]);
                        $elasticsearch->save($btData, $activityIndices['body_temperature']);
                        ActivityService::insertAllActivityUser($btData);
                        break;

                    case 'oxygen_saturation':
                        $osData = array_merge($commonData, [
                            'saturation' => $value,
                            'activity_type' => null,
                        ]);
                        $elasticsearch->save($osData, $activityIndices['oxygen_saturation']);
                        ActivityService::insertAllActivityUser($osData);
                        break;

                    case 'respiratory_rate':
                        $rrData = array_merge($commonData, [
                            'rate' => $value,
                            'activity_type' => null,
                        ]);
                        $elasticsearch->save($rrData, $activityIndices['respiratory_rate']);
                        ActivityService::insertAllActivityUser($rrData);
                        break;
                }
            }
        }
    }

}