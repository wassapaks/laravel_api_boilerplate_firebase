<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::middleware(['firebase.auth', 'throttle:api'])->group(function () {
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);
});


