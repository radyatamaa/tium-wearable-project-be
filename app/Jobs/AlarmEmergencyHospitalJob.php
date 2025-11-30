<?php

namespace App\Jobs;

use App\Models\EmergencyReportHospital;
use App\Models\FCMToken;
use App\Models\PatientHospital;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AlarmEmergencyHospitalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * @param NotificationService $notificationService
     */
    protected $patients;
    public function __construct($patients)
    {
        $this->patients = $patients;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notificationService = app(NotificationService::class);
        foreach ($this->patients as $patient) {
            $now = date('Y-m-d H:i:s');
            $minInterval = 30 * 60; // 30 menit dalam detik

            $lastEmergency = EmergencyReportHospital::where("patient_id", $patient['patient_id'])
                // ->where("alert_details", $patient['alert_details'])
                ->where("user_general_purpose_id", $patient['user_general_purpose_id'])
                ->where("alert_type", $patient['alert_type'])
                ->orderBy('alert_occurrence_time', 'desc')
                ->first();

            // Cek jika tidak ada record sebelumnya atau sudah lebih dari 30 menit
            if (!$lastEmergency || (strtotime($now) - strtotime($lastEmergency->alert_occurrence_time)) >= $minInterval) {
                $insertEmergency = EmergencyReportHospital::create([
                    'patient_name' => $patient['name'],
                    'alert_occurrence_time' => $now,
                    'alert_details' => $patient['alert_details'],
                    'alert_confirmation_time' => null,
                    'ward' => $patient['ward'],
                    'alert_type' => $patient['alert_type'],
                    'doctor_in_charge' => $patient['doctor_in_charge'],
                    'nurse_in_charge' => $patient['nurse_in_charge'],
                    'medical_staff' => '',
                    'cause_and_actions' => '',
                    'user_general_purpose_id' => $patient['user_general_purpose_id'],
                    'patient_id' => $patient['patient_id'],
                ]);

                $title = "EMERGENCY_HOSPITAL";
                $body = $insertEmergency->id;
                $soundUrl = asset('black/sounds/mixkit-bell-notification-933.wav');
                $sendNotif = $notificationService->sendNotification('', $title, $body, $soundUrl);
            }
        }
    }

}