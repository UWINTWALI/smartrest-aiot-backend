<?php

use Illuminate\Support\Facades\Route;


Route::get('/', fn () => ['message' => 'Welcome to SmartRest IoT API']);
Route::get('/v1', fn () => ['message' => 'SmartRest IoT API v1 - Ready to serve your requests']);