<?php

namespace App\Services;

use App\Models\MeasurementSettingCriteriaPatientHospital;
use App\Models\MeasurementSettingsGeneralPurpose;
use App\Models\MeasurementSettingsPublicWelfare;
use App\Models\PatientHospital;
use App\Models\Scopes\UserGeneralPurposeScope;
use App\Models\UserCustomer;
use App\Models\UserManagementGeneralPurpose;
use App\Models\UserManagementPublicWelfare;
use Carbon\Carbon;
use App\Models\NotificationRangeSetting;
use App\Models\GoalSetting;
use App\Models\SleepActivity;
use App\Models\MenstrualCycleActivity;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class ActivityService
{
    // hospital
    public static function defaultValuesMeasurementSetting()
    {
        return [
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
            'respiratory_good_health_value1' => 12,
            'respiratory_good_health_value2' => 20,
            'respiratory_caution_value1' => 21,
            'respiratory_caution_value2' => 24,
            'respiratory_dangers_value1' => 25,
            'respiratory_dangers_value2' => 30,
            'ecg_good_min' => 60,
            'ecg_good_max' => 100,
            'ecg_caution_min' => 101,
            'ecg_caution_max' => 110,
            'ecg_danger_min' => 111,
            'ecg_danger_max' => 130,
        ];

    }
    public static function measurementSettingByPatientIdHospital($patient_id)
    {
        // Fetch the measurement settings criteria for the patient
        $criteria = MeasurementSettingCriteriaPatientHospital::where('patient_id', $patient_id)->first();

        $defaultValues = self::defaultValuesMeasurementSetting();

        if (!$criteria) {
            $criteria = $defaultValues;
            $defaultValues['patient_id'] = $patient_id;
            MeasurementSettingCriteriaPatientHospital::create($defaultValues);
        } else {
            $criteria = array_merge($defaultValues, $criteria->toArray());
        }

        return $criteria;
    }
    public static function determineStateHospital($patient_id, $value1, $key, $value2 = 0)
    {
        $criteria = self::measurementSettingByPatientIdHospital($patient_id);
        $defaultValues = self::defaultValuesMeasurementSetting();

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
        } else if ($key == 'ecg') {
            // If criteria exists, override the default values
            $goodMin = $criteria ? $criteria['ecg_good_min'] : $defaultValues['ecg_good_min'];
            $goodMax = $criteria ? $criteria['ecg_good_max'] : $defaultValues['ecg_good_max'];
            $cautionMin = $criteria ? $criteria['ecg_caution_min'] : $defaultValues['ecg_caution_min'];
            $cautionMax = $criteria ? $criteria['ecg_caution_max'] : $defaultValues['ecg_caution_max'];
            $dangerMin = $criteria ? $criteria['ecg_danger_min'] : $defaultValues['ecg_danger_min'];
            $dangerMax = $criteria ? $criteria['ecg_danger_max'] : $defaultValues['ecg_danger_max'];
            if ($value1 >= $goodMin && $value1 <= $goodMax) {
                return 'good';
            } elseif ($value1 >= $cautionMin && $value1 <= $cautionMax) {
                return 'caution';
            } elseif ($value1 >= $dangerMin && $value1 <= $dangerMax) {
                return 'danger';
            } else {
                return '';
            }
        } else {
            // If criteria exists, override the default values
            $goodMin = $criteria ? $criteria[$key . '_good_health_value1'] : $defaultValues[$key . '_good_health_value1'];
            $goodMax = $criteria ? $criteria[$key . '_good_health_value2'] : $defaultValues[$key . '_good_health_value2'];
            $cautionMin = $criteria ? $criteria[$key . '_caution_value1'] : $defaultValues[$key . '_caution_value1'];
            $cautionMax = $criteria ? $criteria[$key . '_caution_value2'] : $defaultValues[$key . '_caution_value2'];
            $dangerMin = $criteria ? $criteria[$key . '_dangers_value1'] : $defaultValues[$key . '_dangers_value1'];
            $dangerMax = $criteria ? $criteria[$key . '_dangers_value2'] : $defaultValues[$key . '_dangers_value2'];
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
    public static function getPatientsEmergencyByPatientIdHospital($patientId, $activity, $isBulk = false)
    {
        $result = [];

        if ($activity->systolic > 0 && $activity->diastolic) {
            $stateSystolic = self::determineStateHospital($patientId, $activity->systolic, 'blood_pressure_systolic');
            $stateDiastolic = self::determineStateHospital($patientId, $activity->diastolic, 'blood_pressure_diastolic');

            if ($stateSystolic == 'danger' || $stateSystolic == 'caution' || $stateDiastolic == 'danger' || $stateDiastolic == 'caution') {
                $patient = PatientHospital::where('id', $patientId)->where('status', 'IN_PATIENT')->first();
                if ($patient) {
                    if ($isBulk) {
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
                    } else {
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
        }

        if ($activity->bpm > 0) {
            $state = self::determineStateHospital($patientId, $activity->bpm, 'heart_rate');
            if ($state == 'danger' || $state == 'caution') {
                $patient = PatientHospital::where('id', $patientId)->where('status', 'IN_PATIENT')->first();
                if ($patient) {
                    $alert_details = config('app.lang') != 'en' ? '심박수' : 'Heart rate';
                    if ($isBulk && count($result) > 0) {
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
            $state = self::determineStateHospital($patientId, $activity->saturation, 'oxygen_saturation');
            if ($state == 'danger' || $state == 'caution') {
                $patient = PatientHospital::where('id', $patientId)->where('status', 'IN_PATIENT')->first();
                if ($patient) {
                    $alert_details = config('app.lang') != 'en' ? '산소포화도' : 'Oxygen saturation';
                    if ($isBulk && count($result) > 0) {
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
            $state = self::determineStateHospital($patientId, $activity->temperature, 'body_temperature');
            if ($state == 'danger' || $state == 'caution') {
                $patient = PatientHospital::where('id', $patientId)->where('status', 'IN_PATIENT')->first();
                if ($patient) {
                    $alert_details = config('app.lang') != 'en' ? '체온' : 'Body temperature';
                    if ($isBulk && count($result) > 0) {
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

        return $result;
    }
    public static function detemineColorOfStateHospital($state)
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

    // general purpose
    public static function defaultValuesMeasurementSettingGeneralPurpose()
    {
        return [
            'body_temp_caution_min' => 34.5,
            'body_temp_caution_max' => 38.5,
            'body_temp_danger_min' => 33.5,
            'body_temp_danger_max' => 39.5,
            'heart_rate_caution_min' => 60,
            'heart_rate_caution_max' => 120,
            'heart_rate_danger_min' => 50,
            'heart_rate_danger_max' => 160,
            'respiratory_rate_caution_min' => 15,
            'respiratory_rate_caution_max' => 22,
            'respiratory_rate_danger_min' => 10,
            'respiratory_rate_danger_max' => 30,
            'blood_pressure_systolic_caution_min' => 60,
            'blood_pressure_systolic_caution_max' => 140,
            'blood_pressure_diastolic_caution_min' => 50,
            'blood_pressure_diastolic_caution_max' => 160,
            'blood_pressure_systolic_danger_min' => 50,
            'blood_pressure_systolic_danger_max' => 100,
            'blood_pressure_diastolic_danger_min' => 40,
            'blood_pressure_diastolic_danger_max' => 120,
            'oxygen_saturation_caution_min' => 95,
            'oxygen_saturation_caution_max' => 85,
            'oxygen_saturation_danger_min' => 90,
            'oxygen_saturation_danger_max' => 90,
            'external_temp_default_value' => 35,
            'ecg_caution_min' => 101,
            'ecg_caution_max' => 110,
            'ecg_danger_min' => 111,
            'ecg_danger_max' => 130,
        ];
    }
    public static function measurementSettingByPatientIdGeneralPurpose($customerId)
    {
        // Fetch the measurement settings criteria for the patient
        $criteria = MeasurementSettingsGeneralPurpose::withoutGlobalScope(UserGeneralPurposeScope::class)->where('user_general_purpose_id', $customerId)->first();

        $defaultValues = self::defaultValuesMeasurementSettingGeneralPurpose();

        if (!$criteria) {
            $criteria = $defaultValues;
            MeasurementSettingsGeneralPurpose::create($defaultValues);
        } else {
            $criteria = array_merge($defaultValues, $criteria->toArray());
        }

        return $criteria;
    }
    public static function determineStateGeneralPurpose($customerId, $value1, $key, $value2 = 0)
    {
        $criteria = self::measurementSettingByPatientIdGeneralPurpose($customerId);
        $defaultValues = self::defaultValuesMeasurementSettingGeneralPurpose();

        if ($key == 'blood_pressure' && $value2 !== null) {
            // Adjust for systolic and diastolic pressure
            $systolicCautionMin = $criteria['blood_pressure_systolic_caution_min'] ?? $defaultValues['blood_pressure_systolic_caution_min'];
            $systolicCautionMax = $criteria['blood_pressure_systolic_caution_max'] ?? $defaultValues['blood_pressure_systolic_caution_max'];
            $systolicDangerMin = $criteria['blood_pressure_systolic_danger_min'] ?? $defaultValues['blood_pressure_systolic_danger_min'];
            $systolicDangerMax = $criteria['blood_pressure_systolic_danger_max'] ?? $defaultValues['blood_pressure_systolic_danger_max'];

            $diastolicCautionMin = $criteria['blood_pressure_diastolic_caution_min'] ?? $defaultValues['blood_pressure_diastolic_caution_min'];
            $diastolicCautionMax = $criteria['blood_pressure_diastolic_caution_max'] ?? $defaultValues['blood_pressure_diastolic_caution_max'];
            $diastolicDangerMin = $criteria['blood_pressure_diastolic_danger_min'] ?? $defaultValues['blood_pressure_diastolic_danger_min'];
            $diastolicDangerMax = $criteria['blood_pressure_diastolic_danger_max'] ?? $defaultValues['blood_pressure_diastolic_danger_max'];

            if (($value1 >= $systolicCautionMin && $value1 <= $systolicCautionMax) && ($value2 >= $diastolicCautionMin && $value2 <= $diastolicCautionMax)) {
                return 'caution';
            } elseif (($value1 >= $systolicDangerMin && $value1 <= $systolicDangerMax) && ($value2 >= $diastolicDangerMin && $value2 <= $diastolicDangerMax)) {
                return 'danger';
            } else {
                return '';
            }
        } elseif ($key == 'blood_pressure_systolic') {
            // Adjust for systolic pressure
            $systolicCautionMin = $criteria['blood_pressure_systolic_caution_min'] ?? $defaultValues['blood_pressure_systolic_caution_min'];
            $systolicCautionMax = $criteria['blood_pressure_systolic_caution_max'] ?? $defaultValues['blood_pressure_systolic_caution_max'];
            $systolicDangerMin = $criteria['blood_pressure_systolic_danger_min'] ?? $defaultValues['blood_pressure_systolic_danger_min'];
            $systolicDangerMax = $criteria['blood_pressure_systolic_danger_max'] ?? $defaultValues['blood_pressure_systolic_danger_max'];

            if ($value1 >= $systolicCautionMin && $value1 <= $systolicCautionMax) {
                return 'caution';
            } elseif ($value1 >= $systolicDangerMin && $value1 <= $systolicDangerMax) {
                return 'danger';
            } else {
                return '';
            }
        } elseif ($key == 'blood_pressure_diastolic') {
            // Adjust for diastolic pressure
            $diastolicCautionMin = $criteria['blood_pressure_diastolic_caution_min'] ?? $defaultValues['blood_pressure_diastolic_caution_min'];
            $diastolicCautionMax = $criteria['blood_pressure_diastolic_caution_max'] ?? $defaultValues['blood_pressure_diastolic_caution_max'];
            $diastolicDangerMin = $criteria['blood_pressure_diastolic_danger_min'] ?? $defaultValues['blood_pressure_diastolic_danger_min'];
            $diastolicDangerMax = $criteria['blood_pressure_diastolic_danger_max'] ?? $defaultValues['blood_pressure_diastolic_danger_max'];

            if ($value1 >= $diastolicCautionMin && $value1 <= $diastolicCautionMax) {
                return 'caution';
            } elseif ($value1 >= $diastolicDangerMin && $value1 <= $diastolicDangerMax) {
                return 'danger';
            } else {
                return '';
            }
        } else {
            // If criteria exists, override the default values
            $cautionMin = $criteria[$key . '_caution_min'] ?? $defaultValues[$key . '_caution_min'];
            $cautionMax = $criteria[$key . '_caution_max'] ?? $defaultValues[$key . '_caution_max'];
            $dangerMin = $criteria[$key . '_danger_min'] ?? $defaultValues[$key . '_danger_min'];
            $dangerMax = $criteria[$key . '_danger_max'] ?? $defaultValues[$key . '_danger_max'];

            if ($value1 >= $cautionMin && $value1 <= $cautionMax) {
                return 'caution';
            } elseif ($value1 >= $dangerMin && $value1 <= $dangerMax) {
                return 'danger';
            } else {
                return '';
            }
        }
    }
    public static function getCustomerEmergencyByCustomerIdGeneralPurpose($customerId, $activity, $isBulk = false)
    {
        $result = [];
        $user = UserManagementGeneralPurpose::where('user_customer_id', $customerId)->first();
        if (!$user) {
            return $result;
        }

        $customerId = $user->user_general_purpose_id;
        if (!$customerId) {
            return $result;
        }

        if (isset($activity->systolic) && isset($activity->diastolic)) {
            if ($activity->systolic > 0 && $activity->diastolic > 0) {
                $stateSystolic = self::determineStateGeneralPurpose($customerId, $activity->systolic, 'blood_pressure_systolic');
                $stateDiastolic = self::determineStateGeneralPurpose($customerId, $activity->diastolic, 'blood_pressure_diastolic');

                if ($stateSystolic == 'danger' || $stateSystolic == 'caution' || $stateDiastolic == 'danger' || $stateDiastolic == 'caution') {

                    if ($user) {
                        if ($isBulk) {
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
                        } else {
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
                $state = self::determineStateGeneralPurpose($customerId, $activity->bpm, 'heart_rate');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '심박수' : 'Heart rate';
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

        if (isset($activity->saturation)) {
            if ($activity->saturation > 0) {
                $state = self::determineStateGeneralPurpose($customerId, $activity->saturation, 'oxygen_saturation');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '산소포화도' : 'Oxygen saturation';
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

        if (isset($activity->temperature)) {
            if ($activity->temperature > 0) {
                $state = self::determineStateGeneralPurpose($customerId, $activity->temperature, 'body_temp');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '체온' : 'Body temperature';
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

        if (isset($activity->rate)) {
            if ($activity->rate > 0) {
                $state = self::determineStateGeneralPurpose($customerId, $activity->rate, 'respiratory_rate');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '호흡수' : 'Respiratory Rate';
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

        if (isset($activity->bpmEcg)) {
            if ($activity->bpmEcg > 0) {
                $state = self::determineStateGeneralPurpose($customerId, $activity->bpmEcg, 'ecg');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '심전도' : 'ECG';
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
        return $result;
    }
    public static function detemineColorOfStateGeneralPurpose($state)
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

    // public welfare
    public static function defaultValuesMeasurementSettingPublicWelfare()
    {
        return [
            'body_temp_caution_min' => 34.5,
            'body_temp_caution_max' => 38.5,
            'body_temp_danger_min' => 33.5,
            'body_temp_danger_max' => 39.5,
            'heart_rate_caution_min' => 60,
            'heart_rate_caution_max' => 120,
            'heart_rate_danger_min' => 50,
            'heart_rate_danger_max' => 160,
            'respiratory_rate_caution_min' => 15,
            'respiratory_rate_caution_max' => 22,
            'respiratory_rate_danger_min' => 10,
            'respiratory_rate_danger_max' => 30,
            'blood_pressure_systolic_caution_min' => 60,
            'blood_pressure_systolic_caution_max' => 140,
            'blood_pressure_diastolic_caution_min' => 50,
            'blood_pressure_diastolic_caution_max' => 160,
            'blood_pressure_systolic_danger_min' => 50,
            'blood_pressure_systolic_danger_max' => 100,
            'blood_pressure_diastolic_danger_min' => 40,
            'blood_pressure_diastolic_danger_max' => 120,
            'oxygen_saturation_caution_min' => 95,
            'oxygen_saturation_caution_max' => 85,
            'oxygen_saturation_danger_min' => 90,
            'oxygen_saturation_danger_max' => 90,
            'external_temp_default_value' => 35,
            'ecg_caution_min' => 101,
            'ecg_caution_max' => 110,
            'ecg_danger_min' => 111,
            'ecg_danger_max' => 130,
        ];
    }
    public static function measurementSettingByPatientIdPublicWelfare($customerId)
    {
        // Fetch the measurement settings criteria for the patient
        $criteria = MeasurementSettingsPublicWelfare::withoutGlobalScope(UserGeneralPurposeScope::class)->where('user_general_purpose_id', $customerId)->first();

        $defaultValues = self::defaultValuesMeasurementSettingPublicWelfare();

        if (!$criteria) {
            $criteria = $defaultValues;
            MeasurementSettingsPublicWelfare::create($defaultValues);
        } else {
            $criteria = array_merge($defaultValues, $criteria->toArray());
        }

        return $criteria;
    }
    public static function determineStatePublicWelfare($customerId, $value1, $key, $value2 = 0)
    {
        $criteria = self::measurementSettingByPatientIdPublicWelfare($customerId);
        $defaultValues = self::defaultValuesMeasurementSettingPublicWelfare();

        if ($key == 'blood_pressure' && $value2 !== null) {
            // Adjust for systolic and diastolic pressure
            $systolicCautionMin = $criteria['blood_pressure_systolic_caution_min'] ?? $defaultValues['blood_pressure_systolic_caution_min'];
            $systolicCautionMax = $criteria['blood_pressure_systolic_caution_max'] ?? $defaultValues['blood_pressure_systolic_caution_max'];
            $systolicDangerMin = $criteria['blood_pressure_systolic_danger_min'] ?? $defaultValues['blood_pressure_systolic_danger_min'];
            $systolicDangerMax = $criteria['blood_pressure_systolic_danger_max'] ?? $defaultValues['blood_pressure_systolic_danger_max'];

            $diastolicCautionMin = $criteria['blood_pressure_diastolic_caution_min'] ?? $defaultValues['blood_pressure_diastolic_caution_min'];
            $diastolicCautionMax = $criteria['blood_pressure_diastolic_caution_max'] ?? $defaultValues['blood_pressure_diastolic_caution_max'];
            $diastolicDangerMin = $criteria['blood_pressure_diastolic_danger_min'] ?? $defaultValues['blood_pressure_diastolic_danger_min'];
            $diastolicDangerMax = $criteria['blood_pressure_diastolic_danger_max'] ?? $defaultValues['blood_pressure_diastolic_danger_max'];

            if (($value1 >= $systolicCautionMin && $value1 <= $systolicCautionMax) && ($value2 >= $diastolicCautionMin && $value2 <= $diastolicCautionMax)) {
                return 'caution';
            } elseif (($value1 >= $systolicDangerMin && $value1 <= $systolicDangerMax) && ($value2 >= $diastolicDangerMin && $value2 <= $diastolicDangerMax)) {
                return 'danger';
            } else {
                return '';
            }
        } elseif ($key == 'blood_pressure_systolic') {
            // Adjust for systolic pressure
            $systolicCautionMin = $criteria['blood_pressure_systolic_caution_min'] ?? $defaultValues['blood_pressure_systolic_caution_min'];
            $systolicCautionMax = $criteria['blood_pressure_systolic_caution_max'] ?? $defaultValues['blood_pressure_systolic_caution_max'];
            $systolicDangerMin = $criteria['blood_pressure_systolic_danger_min'] ?? $defaultValues['blood_pressure_systolic_danger_min'];
            $systolicDangerMax = $criteria['blood_pressure_systolic_danger_max'] ?? $defaultValues['blood_pressure_systolic_danger_max'];

            if ($value1 >= $systolicCautionMin && $value1 <= $systolicCautionMax) {
                return 'caution';
            } elseif ($value1 >= $systolicDangerMin && $value1 <= $systolicDangerMax) {
                return 'danger';
            } else {
                return '';
            }
        } elseif ($key == 'blood_pressure_diastolic') {
            // Adjust for diastolic pressure
            $diastolicCautionMin = $criteria['blood_pressure_diastolic_caution_min'] ?? $defaultValues['blood_pressure_diastolic_caution_min'];
            $diastolicCautionMax = $criteria['blood_pressure_diastolic_caution_max'] ?? $defaultValues['blood_pressure_diastolic_caution_max'];
            $diastolicDangerMin = $criteria['blood_pressure_diastolic_danger_min'] ?? $defaultValues['blood_pressure_diastolic_danger_min'];
            $diastolicDangerMax = $criteria['blood_pressure_diastolic_danger_max'] ?? $defaultValues['blood_pressure_diastolic_danger_max'];

            if ($value1 >= $diastolicCautionMin && $value1 <= $diastolicCautionMax) {
                return 'caution';
            } elseif ($value1 >= $diastolicDangerMin && $value1 <= $diastolicDangerMax) {
                return 'danger';
            } else {
                return '';
            }
        } else {
            // If criteria exists, override the default values
            $cautionMin = $criteria[$key . '_caution_min'] ?? $defaultValues[$key . '_caution_min'];
            $cautionMax = $criteria[$key . '_caution_max'] ?? $defaultValues[$key . '_caution_max'];
            $dangerMin = $criteria[$key . '_danger_min'] ?? $defaultValues[$key . '_danger_min'];
            $dangerMax = $criteria[$key . '_danger_max'] ?? $defaultValues[$key . '_danger_max'];

            if ($value1 >= $cautionMin && $value1 <= $cautionMax) {
                return 'caution';
            } elseif ($value1 >= $dangerMin && $value1 <= $dangerMax) {
                return 'danger';
            } else {
                return '';
            }
        }
    }
    public static function getCustomerEmergencyByCustomerIdPublicWelfare($customerId, $activity, $isBulk = false)
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
                $stateSystolic = self::determineStatePublicWelfare($customerId, $activity->systolic, 'blood_pressure_systolic');
                $stateDiastolic = self::determineStatePublicWelfare($customerId, $activity->diastolic, 'blood_pressure_diastolic');

                if ($stateSystolic == 'danger' || $stateSystolic == 'caution' || $stateDiastolic == 'danger' || $stateDiastolic == 'caution') {
                    if ($user) {
                        if ($isBulk) {
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
                        } else {
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
                $state = self::determineStatePublicWelfare($customerId, $activity->bpm, 'heart_rate');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '심박수' : 'Heart rate';
                        if ($isBulk && count($result) > 0) {
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
                $state = self::determineStatePublicWelfare($customerId, $activity->saturation, 'oxygen_saturation');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '산소포화도' : 'Oxygen saturation';
                        if ($isBulk && count($result) > 0) {
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
                $state = self::determineStatePublicWelfare($customerId, $activity->temperature, 'body_temp');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '체온' : 'Body temperature';
                        if ($isBulk && count($result) > 0) {
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
                $state = self::determineStatePublicWelfare($customerId, $activity->rate, 'respiratory_rate');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '호흡수' : 'Respiratory Rate';
                        if ($isBulk && count($result) > 0) {
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
                $state = self::determineStatePublicWelfare($customerId, $activity->bpmEcg, 'ecg');
                if ($state == 'danger' || $state == 'caution') {
                    if ($user) {
                        $alert_details = config('app.lang') != 'en' ? '심전도' : 'ECG';
                        if ($isBulk && count($result) > 0) {
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
        return $result;
    }
    public static function detemineColorOfStatePublicWelfare($state)
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

    public static function insertAllActivityUser($data)
    {
        $tableName = 'all_activity_users_' . date('Ymd'); // Dynamic Table Name

        // Ensure the table exists before inserting data
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_customer_id')->nullable();
                $table->unsignedTinyInteger('systolic')->nullable();
                $table->unsignedTinyInteger('diastolic')->nullable();
                $table->unsignedTinyInteger('pulse')->nullable();
                $table->timestamp('recorded_at');
                $table->string('device_address', 17)->nullable();
                $table->boolean('is_coming_from_gateway')->nullable();
                $table->unsignedTinyInteger('measureId')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->boolean('is_history')->nullable();

                $table->decimal('temperature', 5, 2)->nullable();
                $table->unsignedTinyInteger('saturation')->nullable();
                $table->string('os_activity_type', 20)->nullable();
                $table->unsignedTinyInteger('bpm')->nullable();

                $table->decimal('hrv', 4, 2)->nullable();
                $table->string('hr_activity_type', 20)->nullable(); // e.g., resting, walking, exercising
                $table->decimal('cardiac_health_index', 4, 2)->nullable();

                $table->decimal('respiratory_rate', 4, 2)->nullable();
                $table->string('rr_activity_type', 20)->nullable(); // e.g., resting, walking, exercising
                $table->string('serial_number_gateway', 30)->nullable();

                $table->json('request_body')->nullable();
                $table->timestamp('created_at')->useCurrent();

            });

            // Change table engine to ARCHIVE
            DB::statement("ALTER TABLE `$tableName` ENGINE = ARCHIVE");
        }

        // Insert Data Using DB::table()
        DB::table($tableName)->insert([
            'systolic' => $data['systolic'] ?? null,
            'diastolic' => $data['diastolic'] ?? null,
            'pulse' => $data['pulse'] ?? null,
            'recorded_at' => $data['recorded_at'] ?? now(),
            'device_address' => $data['device_address'] ?? null,
            'is_coming_from_gateway' => $data['is_coming_from_gateway'] ?? null,
            'measureId' => $data['measureId'] ?? null,
            'patient_id' => $data['patient_id'] ?? null,
            'is_history' => $data['is_history'] ?? null,
            'user_customer_id' => $data['user_customer_id'] ?? null,
            'temperature' => $data['temperature'] ?? null,
            'saturation' => $data['saturation'] ?? null,
            'os_activity_type' => $data['os_activity_type'] ?? null,
            'bpm' => $data['bpm'] ?? null,
            'hrv' => $data['hrv'] ?? null,
            'hr_activity_type' => $data['hr_activity_type'] ?? null,
            'cardiac_health_index' => $data['cardiac_health_index'] ?? null,
            'respiratory_rate' => $data['respiratory_rate'] ?? null,
            'rr_activity_type' => $data['rr_activity_type'] ?? null,
            'serial_number_gateway' => $data['serial_number_gateway'] ?? null,
            'request_body' => $data['request_body'] ?? null,
            'created_at' => now()
        ]);
    }


    public static function getReportDataVitalTable($request, $id, $elasticsearch)
    {
        try {
            $startDate = $request->query('start_date_range')
                ? Carbon::parse($request->query('start_date_range'))->startOfDay()->toIso8601String()
                : Carbon::now()->startOfDay()->toIso8601String();

            $endDate = $request->query('end_date_range')
                ? Carbon::parse($request->query('end_date_range'))->endOfDay()->toIso8601String()
                : Carbon::now()->endOfDay()->toIso8601String();
            if ($request->type == 'date_with_minute') {
                $startDate = $request->query('start_date_range')
                    ? Carbon::parse($request->query('start_date_range'))->toIso8601String()
                    : Carbon::now()->startOfDay()->toIso8601String();

                $endDate = $request->query('end_date_range')
                    ? Carbon::parse($request->query('end_date_range'))->toIso8601String()
                    : Carbon::now()->endOfDay()->toIso8601String();
            }
            // Remove pagination parameters:
            // $page = $request->query('page', 1);
            // $size = $request->query('size', 10);
            // $from = ($page - 1) * $size;

            $chartData = [];

            $activityIndices = [
                'blood_pressure' => 'blood_pressure_activity_index',
                'heart_rate' => 'heart_rate_activity_index',
                'oxygen_saturation' => 'oxygen_saturation_activity_index',
                'body_temperature' => 'body_temperature_activity_index',
                'respiratory_rate' => 'respiratory_rate_activity_index',
                'ecg' => 'ecg_activity_index',
            ];

            $activity_type = $request->input('activity_type', '');
            if ($activity_type) {
                $activityIndices = [$activity_type => $activityIndices[$activity_type]];
            }

            foreach ($activityIndices as $type => $index) {
                $mustFilters = [
                    ['term' => ['patient_id' => $id]],
                    ['range' => ['recorded_at' => ['gte' => $startDate, 'lte' => $endDate]]]
                ];

                switch ($type) {
                    case 'heart_rate':
                    case 'ecg':
                        $mustFilters[] = ['range' => ['bpm' => ['gte' => 40, 'lte' => 180]]];
                        break;
                    case 'body_temperature':
                        $mustFilters[] = ['range' => ['temperature' => ['gte' => 25, 'lte' => 42]]];
                        break;
                    case 'blood_pressure':
                        $mustFilters[] = ['range' => ['systolic' => ['gte' => 70, 'lte' => 200]]];
                        $mustFilters[] = ['range' => ['diastolic' => ['gte' => 40, 'lte' => 120]]];
                        break;
                    case 'oxygen_saturation':
                        $mustFilters[] = ['range' => ['saturation' => ['gte' => 75, 'lte' => 100]]];
                        break;
                    case 'respiratory_rate':
                        $mustFilters[] = ['range' => ['rate' => ['gte' => 6, 'lte' => 40]]];
                        break;
                }

                $query = [
                    'index' => $index,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $mustFilters,
                            ],
                        ],
                        'aggs' => [
                            'group_by_time' => [
                                'date_histogram' => [
                                    'field' => 'recorded_at',
                                    'calendar_interval' => 'minute',  // Default grouping by minute
                                    'format' => 'HH:mm',  // Default format
                                    'order' => ['_key' => 'desc'],
                                    'min_doc_count' => 1,
                                    'time_zone' => 'Asia/Seoul'
                                ],
                                'aggs' => [
                                    'avg_systolic' => ['avg' => ['field' => 'systolic']],
                                    'max_systolic' => ['max' => ['field' => 'systolic']],
                                    'min_systolic' => ['min' => ['field' => 'systolic']],
                                    'avg_diastolic' => ['avg' => ['field' => 'diastolic']],
                                    'max_diastolic' => ['max' => ['field' => 'diastolic']],
                                    'min_diastolic' => ['min' => ['field' => 'diastolic']],
                                    'avg_value' => [
                                        'avg' => [
                                            'field' => match ($type) {
                                                'heart_rate', 'ecg' => 'bpm',
                                                'oxygen_saturation' => 'saturation',
                                                'body_temperature' => 'temperature',
                                                'respiratory_rate' => 'rate',
                                                default => 'value'
                                            }
                                        ]
                                    ],
                                    'max_value' => [
                                        'max' => [
                                            'field' => match ($type) {
                                                'heart_rate', 'ecg' => 'bpm',
                                                'oxygen_saturation' => 'saturation',
                                                'body_temperature' => 'temperature',
                                                'respiratory_rate' => 'rate',
                                                default => 'value'
                                            }
                                        ]
                                    ],
                                    'min_value' => [
                                        'min' => [
                                            'field' => match ($type) {
                                                'heart_rate', 'ecg' => 'bpm',
                                                'oxygen_saturation' => 'saturation',
                                                'body_temperature' => 'temperature',
                                                'respiratory_rate' => 'rate',
                                                default => 'value'
                                            }
                                        ]
                                    ],
                                ]
                            ]
                        ],
                        'size' => 0 // No raw hits, only aggregations
                    ]
                ];

                if ($request->query('start_date_range') && $request->query('end_date_range') && $request->type == 'date') {
                    $query['body']['aggs']['group_by_time']['date_histogram']['calendar_interval'] = 'hour';
                    $query['body']['aggs']['group_by_time']['date_histogram']['format'] = 'yyyy-MM-dd HH:00'; // Hourly format
                } else if ($request->query('start_date_range') && $request->query('end_date_range') && $request->type == 'date_with_minute') {
                    $query['body']['aggs']['group_by_time']['date_histogram']['calendar_interval'] = 'minute';
                    $query['body']['aggs']['group_by_time']['date_histogram']['format'] = 'yyyy-MM-dd HH:mm'; // Hourly format
                } else {
                    $query['body']['aggs']['group_by_time']['date_histogram']['calendar_interval'] = 'minute';
                    $query['body']['aggs']['group_by_time']['date_histogram']['format'] = 'HH:mm'; // Minute-level format
                }

                try {
                    $response = $elasticsearch->client->search($query);
                    // Instead of slicing the buckets, retrieve them all
                    $buckets = $response['aggregations']['group_by_time']['buckets'] ?? [];
                } catch (\Exception $e) {
                    $buckets = [];
                }

                $aggregatedData = [];

                foreach ($buckets as $bucket) {
                    $interval = $bucket['key_as_string'];

                    if ($type === 'blood_pressure') {
                        $systolicAvg = round($bucket['avg_systolic']['value'] ?? 0);
                        $diastolicAvg = round($bucket['avg_diastolic']['value'] ?? 0);
                        $systolicMax = round($bucket['max_systolic']['value'] ?? 0);
                        $diastolicMax = round($bucket['max_diastolic']['value'] ?? 0);
                        $systolicMin = round($bucket['min_systolic']['value'] ?? 0);
                        $diastolicMin = round($bucket['min_diastolic']['value'] ?? 0);

                        $systolicState = ActivityService::determineStateHospital($id, $systolicAvg, 'blood_pressure_systolic');
                        $diastolicState = ActivityService::determineStateHospital($id, $diastolicAvg, 'blood_pressure_diastolic');
                        $state = ActivityService::determineStateHospital($id, $systolicAvg, 'blood_pressure', $diastolicAvg);

                        $aggregatedData[$interval] = [
                            'systolic' => $systolicAvg,
                            'diastolic' => $diastolicAvg,
                            'avg' => "$systolicAvg/$diastolicAvg",
                            'highest' => "$systolicMax/$diastolicMax",
                            'lowest' => "$systolicMin/$diastolicMin",
                            'highest_systolic' => $systolicMax,
                            'lowest_systolic' => $systolicMin,
                            'highest_diastolic' => $diastolicMax,
                            'lowest_diastolic' => $diastolicMin,
                            'state' => $state,
                            'state_systolic' => $systolicState,
                            'state_diastolic' => $diastolicState,
                            'color_state_systolic' => ActivityService::detemineColorOfStateHospital($systolicState),
                            'color_state_diastolic' => ActivityService::detemineColorOfStateHospital($diastolicState),
                        ];
                    } else {
                        $avgValue = round($bucket['avg_value']['value'] ?? 0);
                        $highestValue = round($bucket['max_value']['value'] ?? 0);
                        $lowestValue = round($bucket['min_value']['value'] ?? 0);
                        $originalType = $type;
                        if ($type == 'respiratory_rate') {
                            $type = 'respiratory';
                        }
                        $state = ActivityService::determineStateHospital($id, $avgValue, $type);
                        $colorState = ActivityService::detemineColorOfStateHospital($state);

                        $aggregatedData[$interval] = [
                            'avg' => $avgValue,
                            'highest' => $highestValue,
                            'lowest' => $lowestValue,
                            'state' => $state,
                            'color_state' => $colorState,
                        ];
                    }
                }

                $chartData[$type] = $aggregatedData;
            }

            return $chartData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

}