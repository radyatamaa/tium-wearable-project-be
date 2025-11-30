<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (strpos($request->path(), 'general-purpose') !== false) {
            return $request->expectsJson() ? null : '/general-purpose/login';
        } else if (strpos($request->path(), 'hospital') !== false) {
            return $request->expectsJson() ? null : '/hospital/login';
        } else if (strpos($request->path(), 'public-welfare') !== false) {
            return $request->expectsJson() ? null : '/public-welfare/login';
        }        

        return $request->expectsJson() ? null : '/public-health-center/login';
    }
}