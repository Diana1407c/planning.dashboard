<?php

use App\Http\Controllers\Api\AccountantReportController;
use App\Http\Controllers\Api\EngineerController;
use App\Http\Controllers\Api\EngineerHistoryController;
use App\Http\Controllers\Api\EngineerPerformanceController;
use App\Http\Controllers\Api\PlannedHourController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\StackController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamworkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['middleware' => array_merge(
    (array) config('backpack.base.web_middleware', 'web'),
    (array) config('backpack.base.middleware_key', 'admin')
),], function (){
    Route::group(['prefix' => 'projects'], function() {
        Route::get('', [ProjectController::class, 'index']);
        Route::get('all', [ProjectController::class, 'all']);
    });

    Route::group(['prefix' => 'teams'], function() {
        Route::get('', [TeamController::class, 'index']);
        Route::get('all', [TeamController::class, 'all']);
    });

    Route::group(['prefix' => 'stacks'], function() {
        Route::get('', [StackController::class, 'index']);
        Route::get('all', [StackController::class, 'all']);
    });


    Route::group(['prefix' => 'tl-planning', 'middleware' => ['permission:manage team_lead_planning']], function() {
        Route::get('weekly', [PlannedHourController::class, 'tlWeekly']);
        Route::get('monthly', [PlannedHourController::class, 'tlMonthly']);
        Route::post('', [PlannedHourController::class, 'tlStore']);
    });

    Route::group(['prefix' => 'pm-planning', 'middleware' => ['permission:manage project_manager_planning']], function() {
        Route::get('weekly', [PlannedHourController::class, 'pmWeekly']);
        Route::get('monthly', [PlannedHourController::class, 'pmMonthly']);
        Route::post('', [PlannedHourController::class, 'pmStore']);
    });

    Route::group(['prefix' => 'engineer/{engineer}', 'middleware' => ['permission:manage engineer']], function() {
        Route::get('performances', [EngineerPerformanceController::class, 'index']);
        Route::post('performances', [EngineerPerformanceController::class, 'store']);
        Route::patch('performances/{engineerPerformance}', [EngineerPerformanceController::class, 'update']);
        Route::delete('performances/{engineerPerformance}', [EngineerPerformanceController::class, 'delete']);

        Route::get('history', [EngineerHistoryController::class, 'index']);
    });

    Route::group(['prefix' => 'reports', 'middleware' => ['permission:manage reports']], function() {
        Route::get('comparison', [ReportController::class, 'comparison']);
        Route::get('comparison/export', [ReportController::class, 'export']);
        Route::get('comparison/detail/{project}', [ReportController::class, 'comparisonDetail']);

        Route::get('engineers', [EngineerController::class, 'index']);
        Route::get('engineers/export', [EngineerController::class, 'export']);
        Route::get('engineers/accountant', [EngineerController::class, 'accountant']);

        Route::get('teamwork-time', [TeamworkController::class, 'index']);
        Route::get('teamwork-time/export', [TeamworkController::class, 'export']);

        Route::get('accountant', [AccountantReportController::class, 'index']);
        Route::get('accountant/export', [AccountantReportController::class, 'export']);

        Route::get('history', [ProjectHistoryReportController::class,'index']);
    });
});
