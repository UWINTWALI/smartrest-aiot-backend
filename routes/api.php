<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SystemController;

// API Information Routes
Route::get('/', fn () => ['message' => 'Welcome to SmartRest IoT API']);
Route::get('/v1', fn () => ['message' => 'SmartRest IoT API v1 - Ready to serve your requests']);

// Category 1 • Authentication & Session Management
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::get('verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
    Route::post('social-login', [AuthController::class, 'socialLogin']);
});

// Category 2 • User Management (CRUD)
Route::middleware('auth:sanctum')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{userId}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{userId}', [UserController::class, 'update']);
    Route::delete('/{userId}', [UserController::class, 'destroy']);
});

// Category 3 • Product Catalog
Route::apiResource('products', ProductController::class);

// Category 4 • Sensor Data Collection & Query
Route::prefix('sensors')->group(function () {
    Route::post('data', [SensorController::class, 'storeData']);
    Route::get('latest', [SensorController::class, 'getLatest'])->middleware('auth:sanctum');
    Route::get('history', [SensorController::class, 'getHistory'])->middleware('auth:sanctum');
});

// Category 5 • Messaging & Notifications
Route::middleware('auth:sanctum')->group(function () {
    Route::post('messages', [MessageController::class, 'store']);
    Route::get('messages/{conversationId}', [MessageController::class, 'getThread']);
    Route::get('notifications', [MessageController::class, 'getNotifications']);
    Route::post('notifications/{id}/acknowledge', [MessageController::class, 'acknowledgeNotification']);
});

// Category 6 • Analytics & Reports
Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
    Route::get('sleep-report', [AnalyticsController::class, 'getSleepReport']);
    Route::get('health-summary', [AnalyticsController::class, 'getHealthSummary']);
});

// Category 7 • System & Device Management
Route::middleware('auth:sanctum')->prefix('system')->group(function () {
    Route::get('status', [SystemController::class, 'getStatus']);
    Route::post('reboot', [SystemController::class, 'reboot']);
});