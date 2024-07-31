<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['firebase.auth', 'throttle:api'])->group(function () {
    Route::prefix('user-management')->group(function () {
        Route::post('/users', 'UserController@store');
        Route::put('/users/{$id}', 'UserController@update');
        Route::delete('/users/{$id}', 'UserController@destroy');
        Route::get('/users/{id}', 'UserController@show');
    });
});

Route::middleware(['throttle:api'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('verify', 'AuthController@verifyToken')->middleware(['firebase.auth']);
        Route::post('signin', 'AuthController@login');
    });
});
