<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamLeadPlanningController;
use Illuminate\Http\Request;
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
Route::group(['prefix' => 'projects'], function() {
    Route::get('', [ProjectController::class, 'index']);
    Route::get('all', [ProjectController::class, 'shortIndex']);
});

Route::group(['prefix' => 'teams'], function() {
    Route::get('', [TeamController::class, 'index']);
    Route::get('all', [TeamController::class, 'shortIndex']);
});

Route::group(['prefix' => 'tl-planning'], function() {
    Route::get('', [TeamLeadPlanningController::class, 'index']);
    Route::post('', [TeamLeadPlanningController::class, 'storeOrUpdate']);
    Route::get('teams', [TeamLeadPlanningController::class, 'teamIndex']);
    Route::get('teams/{team}', [TeamLeadPlanningController::class, 'teamShow']);
    Route::get('engineers', [TeamLeadPlanningController::class, 'engineerIndex']);
    Route::get('engineers/{engineer}', [TeamLeadPlanningController::class, 'engineerShow']);
});


