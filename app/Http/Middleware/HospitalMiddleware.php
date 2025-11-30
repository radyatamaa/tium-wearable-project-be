<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HospitalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Logic to ensure user is accessing Hospital
        if (!$request->user('hospital') && !$request->user('hospitalStaff')) {
            return redirect('/hospital/login');
        }
        return $next($request);
    }
}