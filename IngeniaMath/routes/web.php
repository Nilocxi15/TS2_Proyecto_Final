<?php

use Illuminate\Support\Facades\Route;


//jrg
use App\Http\Controllers\BancoEjercicios\EjercicioController;


// Rutas para login y autenticación
Route::get('/', function () {
    return view('welcome');
});


// Rutas para registro de usuarios nuevos
Route::get('/register', function () {
    return view('register');
})->name('register');






/*
|--------------------------------------------------------------------------
| Banco de Ejercicios - Módulo 2
|--------------------------------------------------------------------------
*/


// Banco de Ejercicios - Módulo 2
Route::prefix('ejercicios')->name('ejercicios.')->group(function () {
    Route::get('/', [EjercicioController::class, 'index'])->name('index');
    Route::get('/create', [EjercicioController::class, 'create'])->name('create');
    Route::post('/', [EjercicioController::class, 'store'])->name('store');
    Route::get('/{id}', [EjercicioController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [EjercicioController::class, 'edit'])->name('edit');
    Route::put('/{id}', [EjercicioController::class, 'update'])->name('update');
    Route::patch('/{id}/estado', [EjercicioController::class, 'cambiarEstado'])->name('cambiar-estado');
    Route::delete('/{id}', [EjercicioController::class, 'destroy'])->name('destroy');
});

// Rutas AJAX para selects dinámicos (sin API)
Route::get('/subtemas/{moduloId}', [EjercicioController::class, 'getSubtemas']);
Route::get('/ejercicios-publicados', [EjercicioController::class, 'getEjerciciosPublicados']);