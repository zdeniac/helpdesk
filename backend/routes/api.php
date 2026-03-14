<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HelpdeskBotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HelpdeskAgentController;
use App\Http\Controllers\PasswordResetController;

Route::group(['middleware' => 'api'], function () {

    Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])
        ->name('password.email');

    Route::post('/password/reset', [PasswordResetController::class, 'reset'])
        ->name('password.reset');

    // Public routes
    Route::post('/login', [AuthController::class, 'login']);

    // Authenticated routes
    Route::group(['middleware' => 'auth:api'], function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::apiResource('events', EventController::class);

        // User routes (normal helpdesk)
        Route::group(['middleware' => ['role:user']], function () {
            Route::get('/helpdesk', [HelpdeskBotController::class, 'show']);
            Route::post('/helpdesk', [HelpdeskBotController::class, 'store']);
        });

        // Agent routes (agent controller)
        Route::group(['prefix' => 'agent', 'middleware' => ['role:agent']], function () {
            Route::get('/conversations', [HelpdeskAgentController::class, 'index']);
            Route::get('/conversations/{id}', [HelpdeskAgentController::class, 'show']);
            Route::post('/conversations/{id}', [HelpdeskAgentController::class, 'store']);
            Route::post('/conversations/{id}/close', [HelpdeskAgentController::class, 'close']);
        });
    });
});