<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientApiController;

Route::prefix('v1')->group(function () {
    Route::get('ping', fn() => ['ok' => true, 'time' => now()->toISOString()]);

    Route::post('auth/login',  [AuthController::class, 'login']);
    Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:api');

    Route::middleware('auth:api')->group(function () {
        Route::apiResource('clients', ClientApiController::class);
        Route::patch('clients/{client}/toggle', [ClientApiController::class, 'toggle']);
        Route::delete('clients',              [ClientApiController::class, 'destroyMany']);
    });
});
