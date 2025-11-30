<?php

namespace App\Services;

use WebSocket\Client;

class NotificationService
{
    protected $websocketUrl;

    public function __construct($websocketUrl)
    {
        $this->websocketUrl = $websocketUrl;
    }

    public function sendNotification(string $deviceToken, string $title, string $body, string $soundUrl)
    {
        // Create the payload
        $payload = [
            'notification' => [
                'title' => $title,
                'body' => $body,
                'icon' => '/firebase-logo.png',
                'sound' => $soundUrl
            ],
            'data' => [
                'custom_sound' => $soundUrl
            ]
        ];

        // Send notification via WebSocket
        try {
            $client = new Client($this->websocketUrl);
            $client->send(json_encode([
                'type' => 'notification',
                'data' => $payload
            ]));
            $client->close();

            return ['status' => 'success', 'message' => 'Notification sent successfully via WebSocket'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}