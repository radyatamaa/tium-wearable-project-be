<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiLoggerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        // Proses request ke controller
        $response = $next($request);

        $duration = round((microtime(true) - $startTime) * 1000, 2); // Waktu eksekusi dalam ms
        $statusCode = $response->status();

        // Cek jika status code bukan 200 atau ada error
        if ($statusCode !== 200) {
            Log::error('⚠️ API Error Request', [
                'ip' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'body' => $request->except(['password', 'password_confirmation']),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            Log::error('⚠️ API Error Response', [
                'status' => $statusCode,
                'duration' => "{$duration}ms",
                'response_body' => method_exists($response, 'getContent') ? json_decode($response->getContent(), true) : 'binary data',
            ]);
        }

        return $response;
    }
}