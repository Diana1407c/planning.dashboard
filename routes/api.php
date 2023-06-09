<?php

use App\Http\Controllers\Api\PMPricesController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectManagerPlanningController;
use App\Http\Controllers\Api\StackController;
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


Route::group(['prefix' => 'tl-planning'], function() {
    Route::get('', [TeamLeadPlanningController::class, 'index']);
    Route::post('', [TeamLeadPlanningController::class, 'storeOrUpdate']);
});

Route::group(['prefix' => 'pm-planning'], function() {
    Route::get('', [ProjectManagerPlanningController::class, 'index']);
    Route::post('', [ProjectManagerPlanningController::class, 'storeOrUpdate']);
});

Route::group(['prefix' => 'pm-prices'], function() {
    Route::get('', [PMPricesController::class, 'index']);
    Route::post('', [PMPricesController::class, 'storeOrUpdate']);
});


