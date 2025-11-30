<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;
use Carbon\Carbon;

class EnsureUserIsAuthenticatedUserCustomerGatewayClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, $guard = 'api_gateway')
    {
        // Check if the user is authenticated
        if (!Auth::guard($guard)->check()) {
            return response()->json([
                'status_code' => 401,
                'status' => 'failed',
                'error' => 'Invalid credentials',
            ], 401);
        }

        // Get the current user's token from the database
        $user = Auth::guard($guard)->user();
        $token = $user->token();

        if (!$token) {
            return response()->json([
                'status_code' => 401,
                // 'status' => 'failed',
                // 'error' => 'Token not found',
                'status' => 'failed',
                'error' => 'Invalid credentials',
            ], 401);
        }

        // Check if the token is expired
        $now = Carbon::now()->format('Y-m-d H:i');
        $exp = Carbon::parse($token->expires_at)->setTimezone(config('app.timezone'))->format('Y-m-d H:i');
        if ($now >= $exp) {
            return response()->json([
                'status_code' => 401,
                // 'status' => 'failed',
                // 'error' => 'Token has expired',
                'status' => 'failed',
                'error' => 'Invalid credentials',
            ], 401);
        }

        // Proceed with the request
        return $next($request);
    }
}