<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ClearSessionOnLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('get') && 
        ($request->is('public-health-center/login') || 
        $request->is('general-purpose/login') || 
        $request->is('hospital/login') || 
        $request->is('public-welfare/login') || 
        $request->is('/'))) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $next($request);
    }
}