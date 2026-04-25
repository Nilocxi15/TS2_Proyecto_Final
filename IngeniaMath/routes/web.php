<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RecursosController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;


//jrg
use App\Http\Controllers\BancoEjercicios\EjercicioController;

//donavin
use App\Enums\RolEnum;
use App\Http\Controllers\AuthController;

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


/*
|--------------------------------------------------------------------------
| Dashboards
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:' . RolEnum::ESTUDIANTE->value])->group(function () {
    Route::get('/estudiante/dashboard', function () {
        return view('dashboards.estudiante');
    })->name('dashboard.estudiante');
});

Route::middleware(['auth', 'role:' . RolEnum::TUTOR->value])->group(function () {
    Route::get('/tutor/dashboard', function () {
        return view('dashboards.tutor');
    })->name('dashboard.tutor');
});

Route::middleware(['auth', 'role:' . RolEnum::ADMINISTRADOR->value])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboards.admin');
    })->name('dashboard.admin');
});

Route::middleware(['auth', 'role:' . RolEnum::REVISOR->value])->group(function () {
    Route::get('/moderador/dashboard', function () {
        return view('dashboards.moderador');
    })->name('dashboard.moderador');
});


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
