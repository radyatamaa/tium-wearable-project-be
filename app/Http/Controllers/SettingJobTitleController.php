<?php

namespace App\Http\Controllers;

use App\Models\ServiceDetailedFieldSetting;
use App\Models\UserGeneralPurpose;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\ServiceJobSetting;
use Illuminate\Support\Facades\DB;

class SettingJobTitleController extends Controller
{

    public function edit()
    {
        $datas = ServiceJobSetting::get();
        $status_by_service = [];
        $service_classification_desc = [];
        $job_title = [];

        foreach ($datas as $data) {
            $userCount = UserGeneralPurpose::where('user_type', $data->service_classification)->count();
            array_push($status_by_service, [
                'service_classification_desc' => $data->service_classification_desc,
                'status_by_service' => $userCount,
            ]);
            array_push($service_classification_desc, $data->service_classification_desc);
            array_push($job_title, [
                'service_classification_desc' => $data->service_classification_desc,
                'job_title' => $data->job_title,
                'position' => $data->position,
            ]);
        }

        return view(
            'setting-job-title.edit',
            [
                'status_by_service_list' => $status_by_service,
                'service_classification_desc_list' => $service_classification_desc,
                'job_title_list' => $job_title,
            ]
        );
    }

    public function store(Request $request)
    {
        // Collect the settings from the request
        $settings = $request->all();

        $service_classification_descs = json_decode($settings['service_classification_desc']);
        $service_classification_deleteds = $settings['service_classification_deleted'] ? json_decode($settings['service_classification_deleted']) : [];

        // Start DB Transaction
        DB::beginTransaction();

        try {
            foreach ($service_classification_deleteds as $index => $service_classification_deleted) {
                // Convert the string to lowercase
                $service_classification = strtolower($service_classification_deleted);

                // Replace spaces with underscores
                $service_classification = str_replace(' ', '_', $service_classification);

                // Delete from ServiceJobSetting if exists
                $check = ServiceJobSetting::where('service_classification', $service_classification)->first();
                if ($check) {
                    $check->delete();
                }

                // Delete from ServiceDetailedFieldSetting if exists
                $checkDetailed = ServiceDetailedFieldSetting::where('service_classification', $service_classification)->first();
                if ($checkDetailed) {
                    $checkDetailed->delete();
                }
            }

            foreach ($service_classification_descs as $index => $service_classification_desc) {


                // Convert the string to lowercase
                $service_classification = strtolower($service_classification_desc);

                // Replace spaces with underscores
                $service_classification = str_replace(' ', '_', $service_classification);
                $colors = ['custom-bg-blue', ' custom-bg-purple', 'custom-bg-light-blue', 'custom-bg-light-purple', 'custom-bg-green'];
                $randomKey = rand(0, count($colors) - 1);
                $color = $colors[$randomKey];
                $icon = '/icons/dashboard/industrial-scene.svg';
                switch ($service_classification) {
                    case '의료기관':
                        $service_classification = 'medical_institution';
                        $icon = '/icons/dashboard/medical-institution.svg';
                        $color = 'custom-bg-blue';
                        break;
                    case '산업현장':
                        $service_classification = 'industrial_scene';
                        $icon = '/icons/dashboard/industrial-scene.svg';
                        $color = 'custom-bg-purple';
                        break;
                    case '공공복지':
                        $service_classification = 'public_welfare';
                        $icon = '/icons/dashboard/public-welfare.svg';
                        $color = 'custom-bg-light-blue';
                        break;
                }

                if ($service_classification != "+") {
                    // Update or create in ServiceJobSetting
                    $check = ServiceJobSetting::where('service_classification', $service_classification)->first();
                    if ($check) {
                        $check->service_classification_desc = $service_classification_desc;
                        $check->job_title = $settings['job_title' . '_' . $index];
                        $check->position = $settings['position' . '_' . $index];
                        $check->status_by_service = $settings['status_by_service' . '_' . $index];
                        $check->save();
                    } else {
                        ServiceJobSetting::create([
                            'service_classification' => $service_classification,
                            'service_classification_desc' => $service_classification_desc,
                            'job_title' => $settings['job_title' . '_' . $index],
                            'position' => $settings['position' . '_' . $index],
                            'status_by_service' => $settings['status_by_service' . '_' . $index],
                            'color_dashboard' => $color,
                            'icon' => $icon
                        ]);
                    }

                    // Update or create in ServiceDetailedFieldSetting
                    $checkDetailed = ServiceDetailedFieldSetting::where('service_classification', $service_classification)->first();
                    if (!$checkDetailed) {
                        ServiceDetailedFieldSetting::create([
                            'service_classification' => $service_classification,
                            'medical_subject' => '',
                            'detailed_field_by_service' => 0,
                            'service_classification_desc' => $service_classification_desc,
                        ]);
                    }
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

            // Optionally, log the error or return a different response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $jobSetting = ServiceJobSetting::get();
        return response()->json([
            'data' => $jobSetting,
            'success' => true,
        ]);
    }
}