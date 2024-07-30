<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::middleware(['firebase.auth', 'throttle:api'])->group(function () {
    Route::get('/books', 'BookController@index');
    Route::get('/books/{id}', 'BookController@show');
    Route::post('/books', 'BookController@store');
    Route::put('/books/{id}', 'BookController@update');
    Route::delete('/books/{id}', 'BookController@destroy');
});


