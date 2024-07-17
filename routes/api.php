<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;

Route::prefix('v1')->group(function () {
    Route::middleware(['firebase.auth', 'throttle:api'])->group(function () {
        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/{id}', [BookController::class, 'show']);
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{id}', [BookController::class, 'update']);
        Route::delete('/books/{id}', [BookController::class, 'destroy']);
    });

    Route::prefix('user-management')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{$id}', [UserController::class, 'update']);
        Route::delete('/users/{$id}', [UserController::class, 'destroy']);
        Route::get('/users/{id}', [UserController::class, 'show']);
    });

    Route::middleware(['throttle:api'])->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('verify', [AuthController::class, 'verifyToken']);
            Route::post('signin', [AuthController::class, 'login']);
        });
    });
});
