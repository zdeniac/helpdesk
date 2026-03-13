<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HelpdeskBotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::group(['middleware' => 'api'], function ($routes) {
        Route::post('/login', [AuthController::class, 'login']);

        Route::group(['auth'], function () {

            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);

            Route::apiResource('events', EventController::class);
            Route::get('/helpdesk', [HelpdeskBotController::class, 'show']);

        });
});
