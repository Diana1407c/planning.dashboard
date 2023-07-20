<?php

use Illuminate\Support\Facades\Route;
// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.
Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    Route::group(['middleware' => ['permission:manage team']], function () {
        Route::crud('team', 'TeamCrudController');
    });

    Route::group(['middleware' => ['permission:manage technology']], function () {
        Route::crud('technology', 'TechnologyCrudController');
    });

    Route::group(['middleware' => ['permission:manage engineer']], function () {
        Route::crud('engineer', 'EngineerCrudController');
        Route::get('engineer/sync', 'EngineerCrudController@sync');
    });

    Route::group(['middleware' => ['permission:manage project']], function () {
        Route::crud('project', 'ProjectCrudController');
        Route::get('project/sync', 'ProjectCrudController@sync');
    });

    Route::group(['middleware' => 'inertia:inertia'], function (){
        Route::group(['middleware' => ['permission:manage team_lead_planning']], function () {
            Route::get('weekly_team_lead_planning', 'PlannedHoursController@tlWeekly')->name('page.weekly_team_lead_planning.index');
            Route::get('monthly_team_lead_planning', 'PlannedHoursController@tlMonthly')->name('page.monthly_team_lead_planning.index');
        });
        Route::group(['middleware' => ['permission:manage project_manager_planning']], function () {
            Route::get('monthly_project_manager_planning', 'PlannedHoursController@pmMonthly')->name('page.monthly_project_manager.index');
            Route::get('weekly_project_manager_planning', 'PlannedHoursController@pmWeekly')->name('page.weekly_project_manager.index');
        });

        Route::group(['prefix' => 'reports', 'middleware' => ['permission:manage reports']], function () {
            Route::get('comparison', 'ComparisonReportController@index')->name('page.comparison_report.index');
            Route::get('engineers', 'EngineerReportController@index')->name('page.engineer_report.index');
            Route::get('teamwork-time', 'TeamworkTimeController@index')->name('page.teamwork_time.index');
        });
    });
    Route::group(['middleware' => ['permission:manage users']], function () {
        Route::crud('user', 'UserCrudController');
    });

    Route::group(['middleware' => ['permission:manage levels']], function () {
        Route::crud('level', 'LevelCrudController');
    });
});
