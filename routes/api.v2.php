<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;

Route::middleware(['firebase.auth', 'throttle:api'])->group(function () {
    Route::prefix('user-management')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{$id}', [UserController::class, 'update']);
        Route::delete('/users/{$id}', [UserController::class, 'destroy']);
        Route::get('/users/{id}', [UserController::class, 'show']);
    });
});

Route::middleware(['throttle:api'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('verify', [AuthController::class, 'verifyToken'])->middleware(['firebase.auth']);
        Route::post('signin', [AuthController::class, 'login']);
    });
});
