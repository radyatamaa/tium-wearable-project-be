<?php

namespace Database\Seeders;

use App\Services\ElasticSearchService;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivityDummyPatientSeeder extends Seeder
{
    protected $elasticsearch;

    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticsearch = $elasticSearchService;
    }

    public function run(): void
    {
        // $deviceAddress = 'DEV001';
        // $userId = 4;
        $deviceAddress = 'test20250205';
        $serial_number_gateway = 'test1231';
        $userId = 19;

        $respiratoryRateValues = [16, 18, 20, 15, 0, 0, 0, 22, 14, 12, 16, 17, 16, 23, 21, 13, 18, 20, 19, 13, 19, 16, 14, 16];
        $bpmEcgValues = [72, 75, 78, 70, 0, 0, 0, 85, 60, 55, 72, 76, 72, 88, 82, 58, 74, 78, 76, 58, 76, 72, 60, 72];
        $bpmValues = [82, 81, 82, 86, 82, 81, 82, 86, 82, 81, 82, 86, 82, 81, 82, 86, 82, 81, 82, 86, 82, 81, 82, 86];
        $bodyTemperatureValues = [37.1, 37.2, 37.8, 37.1, 0, 0, 0, 38, 36.1, 35, 37.1, 37.5, 37.1, 38.1, 37.9, 35.1, 37.2, 37.8, 37.7, 35.1, 37.7, 37.1, 36.1, 37.1];
        $bloodPressureValues = [28, 24, 27, 28, 0, 0, 0, 21, 24, 24, 27, 30, 23, 21, 21, 24, 28, 29, 31, 22, 23, 25, 27, 31];
        $oxygenSaturationValues = [99, 79, 95, 85, 0, 0, 0, 70, 65, 83, 91, 65, 66, 87, 85, 81, 82, 84, 98, 65, 83, 91, 82, 84];

        $startDate = Carbon::now()->startOfDay();

        for ($day = 0; $day < 35; $day++) {
            $recordedAt = $startDate->copy()->subDays($day);

            for ($hour = 0; $hour < 24; $hour++) {
                $timestamp = $recordedAt->copy()->addHours($hour)->format('Y-m-d H:i:s'); // ✅ FIXED: Convert to MySQL format

                // Body Temperature
                if (isset($bodyTemperatureValues[$hour])) {
                    $bt = [
                        'temperature' => $bodyTemperatureValues[$hour],
                        'recorded_at' => $timestamp,
                        'device_address' => $deviceAddress,
                        'patient_id' => $userId,
                        'serial_number_gateway' => $serial_number_gateway,
                    ];
                    $this->elasticsearch->save($bt, 'body_temperature_activity_index');
                }

                // Blood Pressure
                if (isset($bloodPressureValues[$hour])) {
                    $bp = [
                        'systolic' => rand(100, 140),
                        'diastolic' => rand(60, 90),
                        'pulse' => $bloodPressureValues[$hour],
                        'recorded_at' => $timestamp,
                        'device_address' => $deviceAddress,
                        'patient_id' => $userId,
                        'serial_number_gateway' => $serial_number_gateway,
                    ];
                    $this->elasticsearch->save($bp, 'blood_pressure_activity_index');
                }

                // Oxygen Saturation
                if (isset($oxygenSaturationValues[$hour])) {
                    $os = [
                        'saturation' => $oxygenSaturationValues[$hour],
                        'recorded_at' => $timestamp,
                        'device_address' => $deviceAddress,
                        'patient_id' => $userId,
                        'serial_number_gateway' => $serial_number_gateway,
                    ];
                    $this->elasticsearch->save($os, 'oxygen_saturation_activity_index');
                }

                // Heart Rate
                if (isset($bpmValues[$hour])) {
                    $hr = [
                        'bpm' => $bpmValues[$hour],
                        'recorded_at' => $timestamp,
                        'device_address' => $deviceAddress,
                        'patient_id' => $userId,
                        'activity_type' => 'normal',
                        'serial_number_gateway' => $serial_number_gateway,
                    ];
                    $this->elasticsearch->save($hr, 'heart_rate_activity_index');
                }

                // Respiratory Rate
                if (isset($respiratoryRateValues[$hour])) {
                    $rr = [
                        'rate' => $respiratoryRateValues[$hour],
                        'recorded_at' => $timestamp,
                        'device_address' => $deviceAddress,
                        'patient_id' => $userId,
                        'activity_type' => 'normal',
                        'serial_number_gateway' => $serial_number_gateway,
                    ];
                    $this->elasticsearch->save($rr, 'respiratory_rate_activity_index');
                }

                // ECG Activity
                if (isset($bpmEcgValues[$hour])) {
                    $ecg = [
                        'ecg_data' => json_encode($this->generateEcgData()),
                        'bpm' => $bpmEcgValues[$hour],
                        'recorded_at' => $timestamp,
                        'device_address' => Str::random(12),
                        'patient_id' => $userId,
                        'activity_type' => 'normal',
                        'serial_number_gateway' => $serial_number_gateway,
                    ];
                    $this->elasticsearch->save($ecg, 'ecg_activity_index');
                }
            }
        }

        echo "Seeder completed: Data added to MySQL and Elasticsearch.\n";
    }

    private function generateEcgData()
    {
        return array_map(fn() => rand(500, 1500) / 1000, range(1, 50));
    }
}