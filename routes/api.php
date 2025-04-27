<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Users\UserController;
use App\Http\Controllers\ProductController;

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

// API Welcome route
Route::get('/', [AuthController::class, 'welcome']);

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
     ->name('verification.verify')
     ->middleware('signed');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
     ->name('verification.send')
     ->middleware('throttle:6,1');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/reset-password-form/{token}', [AuthController::class, 'showResetPasswordForm'])
     ->name('password.reset.form');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
     // Auth routes
     Route::post('/logout', [AuthController::class, 'logout']);

     // User management routes
     Route::prefix('users')->group(function () {
          Route::get('/', [UserController::class, 'index']);
          Route::get('/{user}', [UserController::class, 'show']);
          Route::put('/{user}', [UserController::class, 'update']);
          Route::delete('/{user}', [UserController::class, 'destroy']);
          Route::post('/{user}/permissions', [UserController::class, 'addPermission']);
          Route::delete('/{user}/permissions', [UserController::class, 'removePermission']);
     });
});

Route::get('/v1', fn() => ['message' => 'SmartRest IoT API v1 - Ready to serve your requests']);

// Route::apiResource('products', ProductController::class);
// Route::middleware(['auth', 'permission:view_sensor_reading'])
//      ->get('/sensor-readings', [SensorReadingController::class, 'index']);