<?php

use App\Http\Controllers\Admin\TeamLeadPlanningController;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('team', 'TeamCrudController');

    Route::crud('technology', 'TechnologyCrudController');

    Route::crud('engineer', 'EngineerCrudController');
    Route::get('engineer/sync', 'EngineerCrudController@sync');

    Route::crud('project', 'ProjectCrudController');
    Route::get('project/sync', 'ProjectCrudController@sync');

    Route::group(['middleware' => 'inertia:inertia'], function (){
        Route::get('team_lead_planning', 'TeamLeadPlanningController@index')->name('page.team_lead_planning.index');
        Route::get('project_manager_planning', 'ProjectManagerPlanningController@index')->name('page.project_manager.index');

        Route::group(['prefix' => 'reports'], function (){
            Route::get('comparison', 'ComparisonReportController@index')->name('page.comparison_report.index');
            Route::get('engineers', 'EngineerReportController@index')->name('page.engineer_report.index');
        });
    });
});
