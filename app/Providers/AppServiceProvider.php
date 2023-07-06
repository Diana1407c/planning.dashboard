<?php

namespace App\Providers;

use App\Models\Team;
use App\Observers\TeamObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') !== 'dev') {
            $this->app['request']->server->set('HTTPS', 'on');
            URL::forceScheme('https'); // Force HTTPS
        }
    }
}
