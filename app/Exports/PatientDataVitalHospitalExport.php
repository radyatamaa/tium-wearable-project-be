<?php

namespace App\Exports;

use App\Services\ActivityService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\PatientHospital;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class PatientDataVitalHospitalExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $patientId;
    protected $request;
    protected $elasticsearch;

    public function __construct($request, $patientId, $elasticsearch)
    {
        $this->patientId = $patientId;
        $this->request = $request;
        $this->elasticsearch = $elasticsearch;
    }

    public function collection()
    {
        $datas = ActivityService::getReportDataVitalTable($this->request, $this->patientId, $this->elasticsearch);

        $tes = json_encode($datas);

        // List of metrics keys to consider
        $metrics = ['blood_pressure', 'heart_rate', 'oxygen_saturation', 'body_temperature', 'respiratory', 'ecg'];

        // Gather all time keys from all metrics
        $times = [];
        foreach ($metrics as $metric) {
            if (isset($datas[$metric]) && is_array($datas[$metric])) {
                $times = array_merge($times, array_keys($datas[$metric]));
            }
        }
        // Get unique times and sort them (string sort, adjust if needed)
        $times = array_unique($times);
        sort($times);

        $formattedData = [];

        foreach ($times as $time) {
            // You can adjust the date format as needed.
            $dateTime = $this->request->start_date_range . ' ' . $time;

            // For each metric, check if there is data for this time, otherwise use an empty array.
            $bp = $datas['blood_pressure'][$time] ?? [];
            $hr = $datas['heart_rate'][$time] ?? [];
            $ox = $datas['oxygen_saturation'][$time] ?? [];
            $bt = $datas['body_temperature'][$time] ?? [];
            $resp = $datas['respiratory'][$time] ?? [];
            $ecg = $datas['ecg'][$time] ?? [];

            $formattedData[] = [
                'date' => $dateTime,

                // Blood Pressure
                'blood_pressure_systolic' => $bp['systolic'] ?? null,
                'blood_pressure_diastolic' => $bp['diastolic'] ?? null,
                'blood_pressure_avg' => $bp['avg'] ?? null,
                'blood_pressure_highest' => $bp['highest'] ?? null,
                'blood_pressure_lowest' => $bp['lowest'] ?? null,
                'blood_pressure_highest_systolic' => $bp['highest_systolic'] ?? null,
                'blood_pressure_lowest_systolic' => $bp['lowest_systolic'] ?? null,
                'blood_pressure_highest_diastolic' => $bp['highest_diastolic'] ?? null,
                'blood_pressure_lowest_diastolic' => $bp['lowest_diastolic'] ?? null,
                'blood_pressure_state' => $bp['state'] ?? null,
                'blood_pressure_state_systolic' => $bp['state_systolic'] ?? null,
                'blood_pressure_state_diastolic' => $bp['state_diastolic'] ?? null,

                // Heart Rate
                'heart_rate_avg' => $hr['avg'] ?? null,
                'heart_rate_highest' => $hr['highest'] ?? null,
                'heart_rate_lowest' => $hr['lowest'] ?? null,
                'heart_rate_state' => $hr['state'] ?? null,

                // Oxygen Saturation
                'oxygen_saturation_avg' => $ox['avg'] ?? null,
                'oxygen_saturation_highest' => $ox['highest'] ?? null,
                'oxygen_saturation_lowest' => $ox['lowest'] ?? null,
                'oxygen_saturation_state' => $ox['state'] ?? null,

                // Body Temperature
                'body_temperature_avg' => $bt['avg'] ?? null,
                'body_temperature_highest' => $bt['highest'] ?? null,
                'body_temperature_lowest' => $bt['lowest'] ?? null,
                'body_temperature_state' => $bt['state'] ?? null,

                // // Respiratory
                // 'respiratory_avg' => $resp['avg'] ?? null,
                // 'respiratory_highest' => $resp['highest'] ?? null,
                // 'respiratory_lowest' => $resp['lowest'] ?? null,
                // 'respiratory_state' => $resp['state'] ?? null,

                // // ECG
                // 'ecg_avg' => $ecg['avg'] ?? null,
                // 'ecg_highest' => $ecg['highest'] ?? null,
                // 'ecg_lowest' => $ecg['lowest'] ?? null,
                // 'ecg_state' => $ecg['state'] ?? null,
            ];
        }

        return new Collection($formattedData);

    }

    public function headings(): array
    {
        return [
            '날짜', // date

            // Blood Pressure (혈압)
            '혈압 수축기', // blood_pressure_systolic
            '혈압 이완기', // blood_pressure_diastolic
            '평균 혈압', // blood_pressure_avg
            '최고 혈압', // blood_pressure_highest
            '최저 혈압', // blood_pressure_lowest
            '최고 수축기 혈압', // blood_pressure_highest_systolic
            '최저 수축기 혈압', // blood_pressure_lowest_systolic
            '최고 이완기 혈압', // blood_pressure_highest_diastolic
            '최저 이완기 혈압', // blood_pressure_lowest_diastolic
            '혈압 상태', // blood_pressure_state
            '수축기 혈압 상태', // blood_pressure_state_systolic
            '이완기 혈압 상태', // blood_pressure_state_diastolic

            // Heart Rate (심박수)
            '평균 심박수', // heart_rate_avg
            '최고 심박수', // heart_rate_highest
            '최저 심박수', // heart_rate_lowest
            '심박수 상태', // heart_rate_state

            // Oxygen Saturation (산소포화도)
            '평균 산소포화도', // oxygen_saturation_avg
            '최고 산소포화도', // oxygen_saturation_highest
            '최저 산소포화도', // oxygen_saturation_lowest
            '산소포화도 상태', // oxygen_saturation_state

            // Body Temperature (체온)
            '평균 체온', // body_temperature_avg
            '최고 체온', // body_temperature_highest
            '최저 체온', // body_temperature_lowest
            '체온 상태', // body_temperature_state

            // // Respiratory (호흡수)
            // '평균 호흡수', // respiratory_avg
            // '최고 호흡수', // respiratory_highest
            // '최저 호흡수', // respiratory_lowest
            // '호흡 상태', // respiratory_state

            // // ECG (심전도)
            // '평균 심전도', // ecg_avg
            // '최고 심전도', // ecg_highest
            // '최저 심전도', // ecg_lowest
            // '심전도 상태', // ecg_state
        ];
    }

}