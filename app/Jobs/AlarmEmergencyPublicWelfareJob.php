<?php

namespace App\Jobs;

use App\Models\EmergencyReportPublicWelfare;
use App\Models\EmergencyReportHospital;
use App\Models\FCMToken;
use App\Models\userHospital;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AlarmEmergencyPublicWelfareJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * @param NotificationService $notificationService
     */
    protected $users;
    public function __construct($users)
    {
        $this->users = $users;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notificationService = app(NotificationService::class);
        foreach ($this->users as $user) {
            $now = date('Y-m-d H:i:s');
            $minInterval = 30 * 60; // 30 menit dalam detik

            $lastEmergency = EmergencyReportPublicWelfare::where("user_customer_id", $user['user_customer_id'])
                // ->where("alert_details", $patient['alert_details'])
                ->where("user_general_purpose_id", $user['user_general_purpose_id'])
                // ->where("assortment", $user['alert_type'])
                ->orderBy('notification_time', 'desc')
                ->first();

            // $check = EmergencyReportPublicWelfare::where("user_customer_id", $user['user_customer_id'])
            //     ->where("main_symptom", $user['alert_details'])
            //     ->where("user_general_purpose_id", $user['user_general_purpose_id'])
            //     ->where("assortment", $user['alert_type'])
            //     ->where("notification_time", $now)
            //     ->first();
            // if (!$check) {
            if (!$lastEmergency || (strtotime($now) - strtotime($lastEmergency->notification_time)) >= $minInterval) {
                $insertEmergency = EmergencyReportPublicWelfare::create([
                    'user_customer_id' => $user['user_customer_id'],
                    'assortment' => $user['alert_type'],
                    'notification_time' => now(),
                    'content' => '',
                    'confirmation_time' => null,
                    'action_time' => null,
                    'name' => $user['name'],
                    'main_symptom' => $user['alert_details'],
                    'gender' => $user['gender'],
                    'age' => $user['age'],
                    'contact' => $user['contact'],
                    'affiliations' => '',
                    'user_general_purpose_id' => $user['user_general_purpose_id'],
                ]);

                $title = "EMERGENCY_PUBLIC_WELFARE";
                $body = $insertEmergency->id;
                $soundUrl = asset('black/sounds/mixkit-bell-notification-933.wav');
                $sendNotif = $notificationService->sendNotification('', $title, $body, $soundUrl);
            }
        }
    }
}