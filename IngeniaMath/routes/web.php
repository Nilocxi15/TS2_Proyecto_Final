<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RecursosController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;

// Rutas para login y autenticación
Route::get('/', function () {
    return view('welcome');
});


// Rutas para registro de usuarios nuevos
Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', [UsuariosController::class, 'store'])->name('register.store');

Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

/* ==========================================================
    Rutas para el rol estudiante
   ========================================================== */

// Rutas para la página de inicio del estudiante
Route::get('/student/home', function() {
    return view('estudiante.home');
})->name('home-estudiante');

// Rutas para la página de diagnostico
Route::get('/student/diagnostic', function() {
    return view('estudiante.diagnostico');
})->name('diagnostic-estudiante');

// Rutas para la página de foro
Route::get('/student/forum', function() {
    return view('estudiante.foro');
})->name('forum-estudiante');

// Rutas para la página de practica de ejercicios
Route::get('/student/practice', function() {
    return view('estudiante.practica');
})->name('practice-estudiante');

// Rutas para la página de recursos educativos
Route::get('/student/resources', [RecursosController::class, 'index'])->name('resources-estudiante');

// Rutas para la página de ruta de aprendizaje
Route::get('/student/learning-route', function() {
    return view('estudiante.ruta-aprendizaje');
})->name('learning-route-estudiante');

// Rutas para la página de simulacros de ejercicios
Route::get('/student/mock-exams', function() {
    return view('estudiante.simulacros');
})->name('mock-exams-estudiante');

// Rutas para la página de perfil del estudiante
Route::get('/student/profile', function() {
    return view('estudiante.perfil');
})->name('profile-estudiante');