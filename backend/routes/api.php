<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;


Route::prefix('api')->group(function () {
    Route::apiResource('events', EventController::class);
});