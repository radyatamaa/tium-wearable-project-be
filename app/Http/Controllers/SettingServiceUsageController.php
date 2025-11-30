<?php

namespace App\Http\Controllers;

use App\Models\ServiceSubscriptionInformationGeneralPurpose;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\ServiceUsageSetting;
use Illuminate\Support\Facades\DB;

class SettingServiceUsageController extends Controller
{

    public function edit()
    {
        // return view('setting-service-usage.edit');

        $datas = ServiceUsageSetting::get();
        $service_usage_fee = [];
        $service_classification_desc = [];
        $scope_and_usage_fee = [];

        foreach ($datas as $data) {
            $usageFee = ServiceSubscriptionInformationGeneralPurpose::where('subscription_service', $data->service_classification)->sum('service_fee');
            array_push($service_usage_fee, [
                'service_classification_desc' => $data->service_classification_desc,
                'service_usage_fee' => $usageFee,
            ]);
            array_push($service_classification_desc, $data->service_classification_desc);
            array_push($scope_and_usage_fee, [
                'service_classification_desc' => $data->service_classification_desc,
                'number_of_user_from' => $data->number_of_user_from,
                'number_of_user_to' => $data->number_of_user_to,
                'usage_fee_monthly' => $data->usage_fee_monthly,
                'fees_used_year' => $data->fees_used_year,
            ]);
        }

        return view(
            'setting-service-usage.edit',
            [
                'service_usage_fee_list' => $service_usage_fee,
                'service_classification_desc_list' => $service_classification_desc,
                'scope_and_usage_fee_list' => $scope_and_usage_fee,
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

                $check = ServiceUsageSetting::where('service_classification', $service_classification)->first();

                if ($check) {
                    $check->delete();
                }
            }

            foreach ($service_classification_descs as $index => $service_classification_desc) {
                // Convert the string to lowercase
                $service_classification = strtolower($service_classification_desc);

                // Replace spaces with underscores
                $service_classification = str_replace(' ', '_', $service_classification);

                if ($service_classification != "+") {
                    $check = ServiceUsageSetting::where('service_classification', $service_classification)->first();
                    if ($check) {
                        $check->service_classification_desc = $service_classification_desc;
                        $check->service_usage_fee = $settings['service_usage_fee' . '_' . $index];
                        $check->number_of_user_from = $settings['number_of_user_from' . '_' . $index];
                        $check->number_of_user_to = $settings['number_of_user_to' . '_' . $index];
                        $check->usage_fee_monthly = $settings['usage_fee_monthly' . '_' . $index];
                        $check->fees_used_year = $settings['fees_used_year' . '_' . $index];
                        $check->save();
                        continue;
                    }
                    ServiceUsageSetting::create([
                        'service_classification' => $service_classification,
                        'service_classification_desc' => $service_classification_desc,
                        'service_usage_fee' => $settings['service_usage_fee' . '_' . $index],
                        'number_of_user_from' => $settings['number_of_user_from' . '_' . $index],
                        'number_of_user_to' => $settings['number_of_user_to' . '_' . $index],
                        'usage_fee_monthly' => $settings['usage_fee_monthly' . '_' . $index],
                        'fees_used_year' => $settings['fees_used_year' . '_' . $index],
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

            // Optionally, log the error or return a different response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $datas = ServiceUsageSetting::get();
        return response()->json([
            'data' => $datas,
            'success' => true,
        ]);
    }
}