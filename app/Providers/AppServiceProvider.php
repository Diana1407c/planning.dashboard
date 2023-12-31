<?php

namespace App\Providers;

use App\Models\Engineer;
use App\Models\EngineerPerformance;
use App\Models\Level;
use App\Models\Team;
use App\Models\Technology;
use App\Observers\EngineerObserver;
use App\Observers\EngineerPerformanceObserver;
use App\Observers\TeamObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
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

        Relation::morphMap([
            'technology' => Technology::class,
            'engineer' => Engineer::class,
            'team' => Team::class,
            'level' => Level::class,
//            PlannedHour::TECHNOLOGY_TYPE => Technology::class,
//            PlannedHour::ENGINEER_TYPE => Engineer::class,
        ]);

        Engineer::observe(EngineerObserver::class);
        Team::observe(TeamObserver::class);
        EngineerPerformance::observe(EngineerPerformanceObserver::class);
    }
}
