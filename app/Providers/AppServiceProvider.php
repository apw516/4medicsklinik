<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SatuSehatService;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
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
        if (config('app.env') === 'production' || request()->secure() || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
