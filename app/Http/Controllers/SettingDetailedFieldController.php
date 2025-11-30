<?php

namespace App\Http\Controllers;

use App\Models\UserGeneralPurpose;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\ServiceDetailedFieldSetting;
use Illuminate\Support\Facades\DB;

class SettingDetailedFieldController extends Controller
{

    public function edit()
    {
        $datas = ServiceDetailedFieldSetting::get();
        $detailed_field_by_services = [];
        $setting_detailed_items = [];
        foreach ($datas as $data) {
            $userCount = UserGeneralPurpose::where('user_type', $data->service_classification)->count();
            array_push($detailed_field_by_services, [
                'service_classification_desc' => $data->service_classification_desc,
                'detailed_field_by_service' => $userCount,
            ]);
            array_push($setting_detailed_items, [
                'service_classification_desc' => $data->service_classification_desc,
                'medical_subject' => $data->medical_subject,
            ]);
        }

        return view(
            'setting-detailed-field.edit',
            [
                'detailed_field_by_services' => $detailed_field_by_services,
                'setting_detailed_items' => $setting_detailed_items,
            ]
        );
    }

    public function store(Request $request)
    {
        // Collect the settings from the request
        $settings = $request->all();

        // Start DB Transaction
        DB::beginTransaction();

        try {
            for ($index = 0; $index < $settings['count_of_form']; $index++) {
                // Convert the string to lowercase
                $service_classification = strtolower($settings['service_classification_desc' . '_' . $index]);

                // Replace spaces with underscores
                $service_classification = str_replace(' ', '_', $service_classification);
                switch ($service_classification) {
                    case '의료기관':
                        $service_classification = 'medical_institution';
                        break;
                    case '산업현장':
                        $service_classification = 'industrial_scene';
                        break;
                    case '공공복지':
                        $service_classification = 'public_welfare';
                        break;
                }

                $check = ServiceDetailedFieldSetting::where('service_classification', $service_classification)->first();
                if ($check) {
                    $check->medical_subject = $settings['medical_subject' . '_' . $index];
                    $check->detailed_field_by_service = $settings['detailed_field_by_service' . '_' . $index];
                    $check->service_classification_desc = $settings['service_classification_desc' . '_' . $index];
                    $check->save();
                } else {
                    ServiceDetailedFieldSetting::create([
                        'service_classification' => $service_classification,
                        'medical_subject' => $settings['medical_subject' . '_' . $index],
                        'detailed_field_by_service' => $settings['detailed_field_by_service' . '_' . $index],
                        'service_classification_desc' => $settings['service_classification_desc' . '_' . $index],
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
            ]);

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}