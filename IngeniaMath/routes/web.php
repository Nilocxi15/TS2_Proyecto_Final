<?php

use App\Enums\RolEnum;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BancoEjercicios\EjercicioController;
use App\Http\Controllers\Foro\ForoController;
use App\Http\Controllers\Foro\ModeracionForoController;
use App\Http\Controllers\Foro\RespuestaForoController;
use App\Http\Controllers\FlashcardsController;
use App\Http\Controllers\Moderador\ModeradorFlashcardsController;
use App\Http\Controllers\Moderador\ModeradorRecursosController;
use App\Http\Controllers\RecursosController;
use App\Http\Controllers\Tutor\TutorFlashcardsController;
use App\Http\Controllers\Tutor\TutorRecursosController;
use App\Http\Controllers\Tutor\TutorRecursosEducativosController;
use App\Http\Controllers\Usuarios\ProfileController;
use App\Http\Controllers\Usuarios\UsuariosController;
use Illuminate\Support\Facades\Route;

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
            // Rutas para la página de practica de ejercicios
            Route::get('/practice', [\App\Http\Controllers\Estudiante\PracticeController::class, 'index'])->name('practice');
            Route::post('/practice/start-free', [\App\Http\Controllers\Estudiante\PracticeController::class, 'startFree'])->name('practice.start-free');
            Route::post('/practice/start-guided', [\App\Http\Controllers\Estudiante\PracticeController::class, 'startGuided'])->name('practice.start-guided');
            Route::get('/practice/session/{id}', [\App\Http\Controllers\Estudiante\PracticeController::class, 'session'])->name('practice.session');
            Route::post('/practice/session/{id}/answer', [\App\Http\Controllers\Estudiante\PracticeController::class, 'saveAnswer'])->name('practice.answer');
            Route::get('/practice/session/{id}/summary', [\App\Http\Controllers\Estudiante\PracticeController::class, 'summary'])->name('practice.summary');
            // Rutas para la página de recursos educativos
            // Flujo Estudiante: solo consumo de recursos y flashcards publicados.
            Route::get('/resources', [RecursosController::class, 'index'])->name('resources');
            // Rutas para la página de ruta de aprendizaje
            Route::get('/learning-route', fn() => view('estudiante.ruta-aprendizaje'))->name('learning-route');
            // Rutas para la página de simulacros de ejercicios
            Route::get('/mock-exams', [\App\Http\Controllers\Estudiante\SimulacroController::class, 'index'])->name('mock-exams');
            Route::post('/mock-exams/start', [\App\Http\Controllers\Estudiante\SimulacroController::class, 'start'])->name('mock-exams.start');
            Route::get('/mock-exams/{id}/session', [\App\Http\Controllers\Estudiante\SimulacroController::class, 'session'])->name('mock-exams.session');
            Route::post('/mock-exams/{id}/answer', [\App\Http\Controllers\Estudiante\SimulacroController::class, 'saveAnswer'])->name('mock-exams.answer');
            Route::post('/mock-exams/{id}/finish', [\App\Http\Controllers\Estudiante\SimulacroController::class, 'finish'])->name('mock-exams.finish');
            Route::get('/mock-exams/{id}/results', [\App\Http\Controllers\Estudiante\SimulacroController::class, 'results'])->name('mock-exams.results');
            // Rutas para la página de flashcards
            Route::get('/flashcards', [FlashcardsController::class, 'index'])->name('flashcards');
            Route::get('/flashcards/subtema/{subtema}', [FlashcardsController::class, 'subtema'])->name('flashcards.subtema');
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
            Route::prefix('exercises')
                ->name('exercises.')
                ->group(function () {

                    Route::get('/', [EjercicioController::class, 'index'])->name('index');
                    Route::get('/create', [EjercicioController::class, 'create'])->name('create');
                    Route::post('/', [EjercicioController::class, 'store'])->name('store');
                    Route::get('/{id}/edit', [EjercicioController::class, 'edit'])->name('edit');
                    Route::put('/{id}', [EjercicioController::class, 'update'])->name('update');
                    Route::patch('/{id}/estado', [EjercicioController::class, 'cambiarEstado'])->name('cambiar-estado');
                    Route::delete('/{id}', [EjercicioController::class, 'destroy'])->name('destroy');

                });


            /*
            |--------------------------------------------------------------------------
            | Recursos Educativos - Módulo 5
            |--------------------------------------------------------------------------        
            */
            Route::prefix('resources')
                ->name('resources.')
                ->group(function () {
                    // Flujo Tutor: crea/edita contenido y lo envía a revisión.
                    Route::get('/flashcards', [TutorRecursosEducativosController::class, 'show'])->name('flashcards.show');
                    Route::post('/recursos', [TutorRecursosController::class, 'store'])->name('recursos.store');
                    Route::patch('/recursos/{recursoId}', [TutorRecursosController::class, 'update'])->name('recursos.update');
                    Route::delete('/recursos/{recursoId}', [TutorRecursosController::class, 'destroy'])->name('recursos.destroy');
                    Route::patch('/recursos/{recursoId}/estado', [TutorRecursosController::class, 'cambiarEstado'])->name('recursos.cambiar-estado');

                    Route::post('/flashcards', [TutorFlashcardsController::class, 'store'])->name('flashcards.store');
                    Route::patch('/flashcards/{flashcardId}', [TutorFlashcardsController::class, 'update'])->name('flashcards.update');
                    Route::delete('/flashcards/{flashcardId}', [TutorFlashcardsController::class, 'destroy'])->name('flashcards.destroy');
                    Route::patch('/flashcards/{flashcardId}/estado', [TutorFlashcardsController::class, 'cambiarEstado'])->name('flashcards.cambiar-estado');
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

            // Usuarios
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UsuariosController::class, 'index'])->name('index');
                Route::get('/create', [UsuariosController::class, 'create'])->name('create');
                Route::post('/', [UsuariosController::class, 'storeAdmin'])->name('store');
                Route::get('/{id}', [UsuariosController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [UsuariosController::class, 'edit'])->name('edit');
                Route::put('/{id}', [UsuariosController::class, 'update'])->name('update');
                Route::delete('/{id}', [UsuariosController::class, 'destroy'])->name('destroy');
            });

            // Configuraciones Globales
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'index'])->name('index');
                Route::put('/', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'update'])->name('update');
            });
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

            Route::prefix('exercises')->name('exercises.')->group(function () {
                Route::get('/revisions', [EjercicioController::class, 'revisionsIndex'])->name('revisions');
                Route::get('/{id}/review', [EjercicioController::class, 'review'])->name('review');
                Route::patch('/{id}/approve', [EjercicioController::class, 'approve'])->name('approve');
                Route::patch('/{id}/reject', [EjercicioController::class, 'reject'])->name('reject');
            });

            Route::prefix('resources')->name('resources.')->group(function () {
                // Flujo Moderador: lista global y resolución (aprobar/rechazar/eliminar).
                Route::get('/revisions', [ModeradorRecursosController::class, 'show'])->name('revisions');

                Route::patch('/recursos/{recursoId}/approve', [ModeradorRecursosController::class, 'approve'])
                    ->name('recursos.approve');
                Route::patch('/recursos/{recursoId}/reject', [ModeradorRecursosController::class, 'reject'])
                    ->name('recursos.reject');
                Route::delete('/recursos/{recursoId}', [ModeradorRecursosController::class, 'destroy'])
                    ->name('recursos.destroy');

                Route::patch('/flashcards/{flashcardId}/approve', [ModeradorFlashcardsController::class, 'approve'])
                    ->name('flashcards.approve');
                Route::patch('/flashcards/{flashcardId}/reject', [ModeradorFlashcardsController::class, 'reject'])
                    ->name('flashcards.reject');
                Route::delete('/flashcards/{flashcardId}', [ModeradorFlashcardsController::class, 'destroy'])
                    ->name('flashcards.destroy');
            });

        });

    /*
    |--------------------------------------------------------------------------
    | RUTAS COMPARTIDAS (Tutor y Revisor)
    |--------------------------------------------------------------------------
    */
    Route::get('/exercises/{id}', [EjercicioController::class, 'show'])
        ->middleware('role:' . RolEnum::TUTOR->value . ',' . RolEnum::REVISOR->value)
        ->name('tutor.exercises.show');

    /*
    |--------------------------------------------------------------------------
    | Privada pero no depende de un Rol
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | FORUM (shared module)
    |--------------------------------------------------------------------------
    */
    Route::prefix('forum')->name('forum.')->group(function () {

        // Todos autenticados
        Route::get('/', [ForoController::class,'index'])->name('index');
        Route::get('/{id}', [ForoController::class,'show'])->name('show');
        Route::get('/subtopics/{moduleId}', [ForoController::class,'getSubtopics'])->name('subtopics');

        // Estudiante
        Route::post('/', [ForoController::class,'store'])
            ->middleware('role:' . RolEnum::ESTUDIANTE->value)
            ->name('store');

        Route::post('/{post}/answer/{answer}/approve',
            [RespuestaForoController::class, 'approve'])
            ->middleware('role:' . RolEnum::ESTUDIANTE->value)
            ->name('approve');

        // Tutor
        Route::post('/{post}/reply',
            [RespuestaForoController::class, 'store'])
            ->middleware('role:' . RolEnum::TUTOR->value)
            ->name('reply');

        // Revisor
        Route::patch('/{id}/close',
            [ModeracionForoController::class,'resolve'])
            ->middleware('role:' . RolEnum::REVISOR->value)
            ->name('close');

        Route::delete('/{id}',
            [ModeracionForoController::class,'deletePost'])
            ->middleware('role:' . RolEnum::REVISOR->value)
            ->name('delete-post');

        Route::delete('/answer/{id}',
            [ModeracionForoController::class,'deleteResponse'])
            ->middleware('role:' . RolEnum::REVISOR->value)
            ->name('delete-answer');
    });
    
    // logout
    // Rutas para la página de perfil
    Route::get('/profile', fn() => view('dashboards.perfil'))->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

    // Rutas AJAX para selects dinámicos (sin API)
    Route::get('/subtemas/{moduloId}', [EjercicioController::class, 'getSubtemas']);
    Route::get('/ejercicios-publicados', [EjercicioController::class, 'getEjerciciosPublicados']);
    Route::get('/ejercicios/{id}/duplicados', [EjercicioController::class, 'findDuplicates']);
});