<?php

namespace App\Providers;

use App\Services\ElasticSearchService;
use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(ElasticsearchService::class, function () {
            $host = config('app.elastic.host') . ':' . config('app.elastic.port');
            $username = config('app.elastic.username');
            $password = config('app.elastic.password');
            $verifySSL = filter_var(config('app.elastic.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);

            $client = ClientBuilder::create()
                ->setHosts([$host])
                ->setBasicAuthentication($username, $password) // Basic authentication
                ->setSSLVerification($verifySSL) // Enable or disable SSL verification
                ->build();

            return new ElasticsearchService($client);
        });
    }



    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}