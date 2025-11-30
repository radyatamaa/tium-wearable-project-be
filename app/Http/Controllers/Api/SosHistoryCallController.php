<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientCallReportGeneralPurpose;
use App\Models\PatientCallReportHospital;
use App\Models\PatientCallReportPublicWelfare;
use App\Models\PatientHospital;
use App\Models\UserManagementGeneralPurpose;
use App\Models\UserManagementPublicWelfare;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SosHistoryCall;
use App\Models\EmergencyReportGeneralPurpose;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Log;
use Exception;

class SosHistoryCallController extends Controller
{
    protected $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function getSOSCallHistory(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $result = SosHistoryCall::where('user_customer_id', $auth->id)->get();

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

    public function createCallHistory(Request $request)
    {
        try {
            $auth = Auth::guard('api')->user();

            $user = SosHistoryCall::create([
                'user_customer_id' => $auth->id,
                'duration' => $request->duration,
                'description' => $request->description,
                'name_contact_sos' => $request->name_contact_sos,
                'phone_number_contact_sos' => $request->phone_number_contact_sos,
            ]);

            $patient = PatientHospital::with(['detailedLocation', 'doctorInCharge', 'nurseInCharge'])
                ->where('status', 'IN_PATIENT')
                ->where('user_customer_id', $auth->id)->first();
            if ($patient) {
                $defaultDesc = config('app.lang') != 'en' ? __('환자에게 전화하다') : __('Call a patient');

                $patientCall = [
                    'patient_name' => $patient->name,
                    'call_occurrence_time' => now(),
                    'call_details' => $request->description != '' && $request->description != '' ? $request->description : $defaultDesc,
                    'call_confirmation_time' => null,
                    'ward' => $patient->detailedLocation ? $patient->detailedLocation->location_name .
                        ' ' . $patient->hospital_room : $patient->hospital_room,
                    'call_type' => '',
                    'doctor_in_charge' => $patient->doctorInCharge ? $patient->doctorInCharge->name : '-',
                    'nurse_in_charge' => $patient->nurseInCharge ? $patient->nurseInCharge->name : '-',
                    'medical_staff' => '',
                    'cause_and_actions' => '',
                    'user_general_purpose_id' => $patient->user_general_purpose_id,
                    'patient_id' => $patient->id,
                ];

                $insertPatientCall = PatientCallReportHospital::create($patientCall);

                // $fcmTokens = FCMToken::where('notification_type', 'HOSPITAL')->get();
                // Log::info('Sending Call Patient # ' . json_encode($patientCall));
                // foreach ($fcmTokens as $key => $fcmToken) {
                $deviceToken = '$fcmToken->fcm_token';
                $title = "CALL_PATIENT_HOSPITAL";
                $body = $insertPatientCall->id;
                $soundUrl = asset('black/sounds/mixkit-bell-notification-933.wav');
                $sendNotif = $this->notificationService->sendNotification($deviceToken, $title, $body, $soundUrl);
                // }

            }

            $userGeneralPurpose = UserManagementGeneralPurpose::where('user_customer_id', '=', $auth->id)->first();
            if ($userGeneralPurpose) {
                $patientCall = [
                    'user_customer_id' => $auth->id,
                    'assortment' => '',
                    'notification_time' => now(),
                    'content' => '',
                    'confirmation_time' => null,
                    'action_time' => null,
                    'name' => $auth->name,
                    'main_symptom' => config('app.lang') != 'en' ? __('환자에게 전화하다') : __('Call a patient'),
                    'gender' => $auth->gender ?? '',
                    'age' => $auth->getAge(),
                    'contact' => $auth->phone_number ?? '',
                    'affiliations' => '',
                    'user_general_purpose_id' => $userGeneralPurpose->user_general_purpose_id,
                ];

                $insertPatientCall = PatientCallReportGeneralPurpose::create($patientCall);

                // Log::info('Sending Call Patient General Purpose# ' . json_encode($patientCall));
                $title = "CALL_PATIENT_GENERAL_PURPOSE";
                $body = $insertPatientCall->id;
                $soundUrl = asset('black/sounds/mixkit-bell-notification-933.wav');
                $sendNotif = $this->notificationService->sendNotification('', $title, $body, $soundUrl);
            }

            $userPublicWelfare = UserManagementPublicWelfare::where('user_customer_id', '=', $auth->id)->first();
            if ($userPublicWelfare) {
                $patientCall = [
                    'user_customer_id' => $auth->id,
                    'assortment' => '',
                    'notification_time' => now(),
                    'content' => '',
                    'confirmation_time' => null,
                    'action_time' => null,
                    'name' => $auth->name,
                    'main_symptom' => config('app.lang') != 'en' ? __('환자에게 전화하다') : __('Call a patient'),
                    'gender' => $auth->gender ?? '',
                    'age' => $auth->getAge(),
                    'contact' => $auth->phone_number ?? '',
                    'affiliations' => '',
                    'user_general_purpose_id' => $userPublicWelfare->user_general_purpose_id,
                ];

                $insertPatientCall = PatientCallReportPublicWelfare::create($patientCall);

                // Log::info('Sending Call Patient Public Welfare# ' . json_encode($patientCall));
                $title = "CALL_PATIENT_PUBLIC_WELFARE";
                $body = $insertPatientCall->id;
                $soundUrl = asset('black/sounds/mixkit-bell-notification-933.wav');
                $sendNotif = $this->notificationService->sendNotification('', $title, $body, $soundUrl);
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
}