<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FCMToken;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class NotificationController extends Controller
{
    public function saveToken(Request $request)
    {
        $agent = new Agent();

        // Set the user agent string
        $agent->setUserAgent($request->header('User-Agent'));

        // Get detailed information
        $device = $agent->device();
        $data = $request->all();
        $data['device'] = $device;

        $check = FCMToken::where('device', $data['device'])
            ->where('notification_type', $data['notification_type'])
            ->first();

        if ($check) {
            $check->fcm_token = $data['fcm_token'];
            $check->save();
            return response()->json(['success' => true]);
        }

        FCMToken::create($data);
        return response()->json(['success' => true]);
    }
}