<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelperController extends Controller
{
    public function downloadApk(Request $request)
    {
        // Ambil User-Agent dari permintaan
        $userAgent = $request->header('User-Agent');

        $downloadUrl = config('app.apk.android_url');

        // Deteksi perangkat berdasarkan User-Agent
        if (stripos($userAgent, 'Android') !== false) {
            // URL unduhan untuk Android
            $downloadUrl = config('app.apk.android_url');
        } elseif (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            // URL untuk pengguna iOS (opsional)
            $downloadUrl = config('app.apk.ios_url');
        }

        // Log User-Agent dan URL yang dikembalikan (opsional untuk debugging)
        \Log::info("Device detected: $userAgent");
        \Log::info("Download URL: $downloadUrl");

        // Redirect ke URL APK
        return redirect($downloadUrl);
    }

}