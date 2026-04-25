<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 50)->unique();
        });

        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100)->nullable();
            $table->string('apellido', 100)->nullable();
            $table->string('email', 150)->unique();
            $table->text('password_hash');
            $table->text('foto_perfil')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('usuario_roles', function (Blueprint $table) {
            $table->integer('usuario_id');
            $table->integer('rol_id');

            $table->primary(['usuario_id', 'rol_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('rol_id')->references('id')->on('roles');
        });

        Schema::create('modulos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
        });

        Schema::create('subtemas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('modulo_id');
            $table->string('nombre', 100);
            $table->integer('nivel_complejidad')->nullable();

            $table->foreign('modulo_id')->references('id')->on('modulos');
        });

        Schema::create('ejercicios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('modulo_id')->nullable();
            $table->integer('subtema_id')->nullable();
            $table->enum('dificultad', ['BASICO', 'INTERMEDIO', 'AVANZADO', 'EXAMEN'])->nullable();
            $table->enum('tipo', ['OPCION_MULTIPLE', 'VF', 'NUMERICO', 'COMPLETAR'])->nullable();
            $table->text('enunciado');
            $table->text('imagen')->nullable();
            $table->text('respuesta_correcta')->nullable();
            $table->text('solucion')->nullable();
            $table->text('explicacion')->nullable();
            $table->integer('tiempo_estimado')->nullable();
            $table->enum('estado', ['BORRADOR', 'REVISION', 'APROBADO', 'PUBLICADO', 'DESHABILITADO'])->default('BORRADOR');
            $table->integer('creado_por')->nullable();
            $table->integer('revisado_por')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('modulo_id')->references('id')->on('modulos');
            $table->foreign('subtema_id')->references('id')->on('subtemas');
            $table->foreign('creado_por')->references('id')->on('usuarios');
            $table->foreign('revisado_por')->references('id')->on('usuarios');
        });

        Schema::create('ejercicios_relacionados', function (Blueprint $table) {
            $table->integer('ejercicio_id');
            $table->integer('relacionado_id');

            $table->primary(['ejercicio_id', 'relacionado_id']);
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios');
            $table->foreign('relacionado_id')->references('id')->on('ejercicios');
        });

        Schema::create('sesiones_practica', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->nullable();
            $table->string('modo', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });

        Schema::create('respuestas_usuario', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->nullable();
            $table->integer('ejercicio_id')->nullable();
            $table->integer('sesion_id')->nullable();
            $table->text('respuesta')->nullable();
            $table->boolean('es_correcta')->nullable();
            $table->integer('tiempo_respuesta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios');
            $table->foreign('sesion_id')->references('id')->on('sesiones_practica');

            $table->index('usuario_id', 'idx_respuestas_usuario_usuario');
            $table->index('ejercicio_id', 'idx_respuestas_usuario_ejercicio');
        });

        Schema::create('ejercicios_guardados', function (Blueprint $table) {
            $table->integer('usuario_id');
            $table->integer('ejercicio_id');

            $table->primary(['usuario_id', 'ejercicio_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios');
        });

        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->nullable();
            $table->timestamp('fecha')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });

        Schema::create('resultados_diagnostico', function (Blueprint $table) {
            $table->integer('diagnostico_id');
            $table->integer('modulo_id');
            $table->decimal('puntaje', 5, 2)->nullable();
            $table->string('estado', 20)->nullable();

            $table->primary(['diagnostico_id', 'modulo_id']);
            $table->foreign('diagnostico_id')->references('id')->on('diagnosticos');
            $table->foreign('modulo_id')->references('id')->on('modulos');
        });

        Schema::create('rutas_aprendizaje', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });

        Schema::create('ruta_detalle', function (Blueprint $table) {
            $table->integer('ruta_id');
            $table->integer('subtema_id');
            $table->integer('prioridad')->nullable();
            $table->boolean('completado')->default(false);

            $table->primary(['ruta_id', 'subtema_id']);
            $table->foreign('ruta_id')->references('id')->on('rutas_aprendizaje');
            $table->foreign('subtema_id')->references('id')->on('subtemas');
        });

        Schema::create('simulacros', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->integer('duracion')->nullable();
            $table->decimal('puntaje', 5, 2)->nullable();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });

        Schema::create('simulacro_preguntas', function (Blueprint $table) {
            $table->integer('simulacro_id');
            $table->integer('ejercicio_id');
            $table->boolean('es_correcta')->nullable();

            $table->primary(['simulacro_id', 'ejercicio_id']);
            $table->foreign('simulacro_id')->references('id')->on('simulacros');
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios');
        });

        Schema::create('recursos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo', 150)->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('modulo_id')->nullable();
            $table->integer('subtema_id')->nullable();
            $table->enum('tipo', ['VIDEO', 'PDF', 'FLASHCARD', 'SIMULADOR'])->nullable();
            $table->text('url')->nullable();
            $table->enum('estado', ['BORRADOR', 'REVISION', 'APROBADO', 'PUBLICADO', 'DESHABILITADO'])->nullable();
            $table->integer('creado_por')->nullable();

            $table->foreign('modulo_id')->references('id')->on('modulos');
            $table->foreign('subtema_id')->references('id')->on('subtemas');
            $table->foreign('creado_por')->references('id')->on('usuarios');
        });

        Schema::create('flashcards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subtema_id')->nullable();
            $table->text('pregunta')->nullable();
            $table->text('respuesta')->nullable();

            $table->foreign('subtema_id')->references('id')->on('subtemas');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->nullable();
            $table->integer('modulo_id')->nullable();
            $table->integer('subtema_id')->nullable();
            $table->text('contenido')->nullable();
            $table->string('estado', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('modulo_id')->references('id')->on('modulos');
            $table->foreign('subtema_id')->references('id')->on('subtemas');
        });

        Schema::create('respuestas_post', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->nullable();
            $table->integer('usuario_id')->nullable();
            $table->text('contenido')->nullable();
            $table->boolean('es_solucion')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_post');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('recursos');
        Schema::dropIfExists('simulacro_preguntas');
        Schema::dropIfExists('simulacros');
        Schema::dropIfExists('ruta_detalle');
        Schema::dropIfExists('rutas_aprendizaje');
        Schema::dropIfExists('resultados_diagnostico');
        Schema::dropIfExists('diagnosticos');
        Schema::dropIfExists('ejercicios_guardados');
        Schema::dropIfExists('respuestas_usuario');
        Schema::dropIfExists('sesiones_practica');
        Schema::dropIfExists('ejercicios_relacionados');
        Schema::dropIfExists('ejercicios');
        Schema::dropIfExists('subtemas');
        Schema::dropIfExists('modulos');
        Schema::dropIfExists('usuario_roles');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('roles');
    }
};
