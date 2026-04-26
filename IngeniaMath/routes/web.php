<?php

use App\Http\Controllers\BancoEjercicios\EjercicioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RecursosController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;
use App\Enums\RolEnum;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', fn() => view('welcome'));
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', fn() => view('register'))->name('register');
    Route::post('/register', [UsuariosController::class, 'store'])->name('register.store');
});


/*
|--------------------------------------------------------------------------
| Rutas protegidas
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ESTUDIANTE
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:' . RolEnum::ESTUDIANTE->value)
        ->prefix('student')
        ->name('student.')
        ->group(function () {

            Route::get('/dashboard', fn() => view('dashboards.estudiante'))
                ->name('dashboard');

            // Rutas para la página de diagnostico
            Route::get('/diagnostic', fn() => view('estudiante.diagnostico'))->name('diagnostic');
            // Rutas para la página de foro
            Route::get('/forum', fn() => view('estudiante.foro'))->name('forum');
            // Rutas para la página de practica de ejercicios
            Route::get('/practice', fn() => view('estudiante.practica'))->name('practice');
            // Rutas para la página de recursos educativos
            Route::get('/resources', [RecursosController::class, 'index'])->name('resources');
            // Rutas para la página de ruta de aprendizaje
            Route::get('/learning-route', fn() => view('estudiante.ruta-aprendizaje'))->name('learning-route');
            // Rutas para la página de simulacros de ejercicios
            Route::get('/mock-exams', fn() => view('estudiante.simulacros'))->name('mock-exams');
            // Rutas para la página de perfil del estudiante
            Route::get('/profile', fn() => view('estudiante.perfil'))->name('profile');

            // RUTAS PARA LA PÁGINA DE FLASHCARDS
            // Galería de flashcards
            Route::get('/flashcards', fn() => view('estudiante.flashcards-galery'))->name('flashcards');
            // Detalle de flashcard
            Route::get('/flashcards/{id}', fn($id) => view('estudiante.flashcards', ['id' => $id]))->name('flashcards.detail');
        });


    /*
    |--------------------------------------------------------------------------
    | TUTOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:' . RolEnum::TUTOR->value)
        ->prefix('tutor')
        ->name('tutor.')
        ->group(function () {

            Route::get('/dashboard', fn() => view('dashboards.tutor'))
                ->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Banco de Ejercicios - Módulo 2
            |--------------------------------------------------------------------------
            */
            Route::prefix('ejercicios')
                ->name('ejercicios.')
                ->group(function () {

                    Route::get('/', [EjercicioController::class, 'index'])->name('index');
                    Route::get('/create', [EjercicioController::class, 'create'])->name('create');
                    Route::post('/', [EjercicioController::class, 'store'])->name('store');
                    Route::get('/{id}', [EjercicioController::class, 'show'])->name('show');
                    Route::get('/{id}/edit', [EjercicioController::class, 'edit'])->name('edit');
                    Route::put('/{id}', [EjercicioController::class, 'update'])->name('update');
                    Route::patch('/{id}/estado', [EjercicioController::class, 'cambiarEstado'])->name('cambiar-estado');
                    Route::delete('/{id}', [EjercicioController::class, 'destroy'])->name('destroy');

                });
        });


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:' . RolEnum::ADMINISTRADOR->value)
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', fn() => view('dashboards.admin'))
                ->name('dashboard');

        });


    /*
    |--------------------------------------------------------------------------
    | REVISOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:' . RolEnum::REVISOR->value)
        ->prefix('moderador')
        ->name('moderador.')
        ->group(function () {

            Route::get('/dashboard', fn() => view('dashboards.moderador'))
                ->name('dashboard');

        });

    /*
    |--------------------------------------------------------------------------
    | Privada pero no depende de un Rol
    |--------------------------------------------------------------------------
    */
    // logout
    Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

    // Rutas AJAX para selects dinámicos (sin API)
    Route::get('/subtemas/{moduloId}', [EjercicioController::class, 'getSubtemas']);
    Route::get('/ejercicios-publicados', [EjercicioController::class, 'getEjerciciosPublicados']);

});
