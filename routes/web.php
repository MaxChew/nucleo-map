<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CenterController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Root path redirect to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Handle direct access to index.php
Route::get('/index.php', function () {
    return redirect()->route('login');
});

// Sanctum CSRF cookie route
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['status' => 'ok']);
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot password routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Development helper pages
Route::get('/color-palette', function () {
    return view('color-palette');
})->name('color-palette');

// FontAwesome test page
Route::get('/fontawesome-test', function () {
    return view('fontawesome-test');
})->name('fontawesome-test');

// Map routes (public pages, no login required)
Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/map/embed', [MapController::class, 'embed'])->name('map.embed');

// iframe example page (public page, no login required)  
Route::get('/map/iframe-example', function () {
    return view('map.iframe-example');
})->name('map.iframe-example');

// Default dashboard route (user role)
Route::middleware(['auth', 'register.global:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// User route group
Route::middleware(['auth', 'register.global:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Admin route group
Route::middleware(['auth', 'register.global:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::post('users/{user}/active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::resource('roles', RoleController::class);
    
    // Medical Centers
    Route::resource('centers', CenterController::class);
    
    // Activity Logs
    Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('logs/{log}', [ActivityLogController::class, 'show'])->name('logs.show');
    Route::delete('logs/{log}', [ActivityLogController::class, 'destroy'])->name('logs.destroy');
    Route::post('logs/clear-old', [ActivityLogController::class, 'clearOld'])->name('logs.clear-old');
    Route::post('logs/clear-all', [ActivityLogController::class, 'clearAll'])->name('logs.clear-all');
});

// Tutor route group
Route::middleware(['auth', 'register.global:tutor'])->prefix('tutor')->name('tutor.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
