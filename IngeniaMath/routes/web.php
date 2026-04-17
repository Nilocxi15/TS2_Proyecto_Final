<?php

use Illuminate\Support\Facades\Route;

// Rutas para login y autenticación
Route::get('/', function () {
    return view('welcome');
});


// Rutas para registro de usuarios nuevos
Route::get('/register', function () {
    return view('register');
})->name('register');