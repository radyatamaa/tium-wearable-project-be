<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthCustomerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(15)); // Example of setting token expiration
        Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}