<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAuthenticatedUserCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = 'api')
    {
        if (!Auth::guard($guard)->check()) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized',
                'data' => null,
                'time_stamp' => now(),
            ], 401);
        }

        return $next($request);
    }
}