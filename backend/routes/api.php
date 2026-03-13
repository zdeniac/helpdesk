<?php

use App\Http\Controllers\HelpdeskBotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;


Route::prefix('api')->group(function () {
    Route::apiResource('events', EventController::class);
    Route::get('/helpdesk', [HelpdeskBotController::class, 'show']);
});