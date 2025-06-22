<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\ActivityLogController;
use App\Http\Controllers\Api\Admin\CenterController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public map API endpoints (no authentication required)
Route::prefix('public')->group(function () {
    Route::get('centers/map-data', [CenterController::class, 'mapData']);
    Route::get('centers/filters', [CenterController::class, 'getFilters']);
});

// Admin API routes
Route::middleware(['auth:sanctum'])->prefix('admin/private')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::post('users/{user}/active', [UserController::class, 'toggleActive']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
    
    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles/{role}/status', [RoleController::class, 'toggleStatus']);
    Route::delete('roles/{role}', [RoleController::class, 'destroy']);
    
    Route::get('logs', [ActivityLogController::class, 'index']);
    Route::delete('logs/{log}', [ActivityLogController::class, 'destroy']);
    
    // Medical center routes
    Route::apiResource('centers', CenterController::class);
    Route::get('centers-summary', [CenterController::class, 'summary']);
    Route::get('centers-map-data', [CenterController::class, 'mapData']);
    Route::get('centers-filters', [CenterController::class, 'getFilters']);
    Route::post('centers-import', [CenterController::class, 'import']);
});
