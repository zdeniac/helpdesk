<?php

use App\Enums\UserRole;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HelpdeskBotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::group(['middleware' => 'api'], function () {

    // Public routes
    Route::post('/login', [AuthController::class, 'login']);

    // Authenticated routes
    Route::group(['middleware' => 'auth:api'], function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::apiResource('events', EventController::class);

        Route::group(['middleware' => ['role:user']], function () {
            Route::get('/helpdesk', [HelpdeskBotController::class, 'show']);
            Route::post('/helpdesk', [HelpdeskBotController::class, 'store']);
        });
    });
});