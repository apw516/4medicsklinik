<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SatuSehatService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(SatuSehatService::class, function ($app) {
        $clientId = auth()->user()?->client_id;
        return new SatuSehatService($clientId);
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
