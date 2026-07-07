<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admin\DepartmentController;
use App\Http\Controllers\Api\V1\Admin\StatsController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::middleware('throttle:10,1')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'me']);

        Route::middleware('role:admin')->prefix('admin')->group(function (): void {
            Route::get('/stats', StatsController::class);
            Route::apiResource('users', UserController::class);
            Route::apiResource('departments', DepartmentController::class);
        });
    });
});
