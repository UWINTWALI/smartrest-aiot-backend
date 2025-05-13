<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;


Route::get('/', fn () => ['message' => 'Welcome to SmartRest IoT API']);
Route::get('/v1', fn () => ['message' => 'SmartRest IoT API v1 - Ready to serve your requests']);

// Auth routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    
    // Protected routes with JWT middleware
    Route::middleware('jwt.auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::apiResource('products', ProductController::class);

Route::apiResource('users', UserController::class);