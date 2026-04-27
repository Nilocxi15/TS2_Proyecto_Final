<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Mejoras:
     * 1. Actualiza claves foráneas existentes con ON DELETE / ON UPDATE apropiados.
     * 2. Agrega tablas: configuracion_estudio, planes_estudio, plan_detalle.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // 1. usuario_roles
        // ---------------------------------------------------------------
        Schema::table('usuario_roles', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['rol_id']);

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 2. subtemas
        // ---------------------------------------------------------------
        Schema::table('subtemas', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);

            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 3. ejercicios
        // ---------------------------------------------------------------
        Schema::table('ejercicios', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);
            $table->dropForeign(['subtema_id']);
            $table->dropForeign(['creado_por']);
            $table->dropForeign(['revisado_por']);

            // Si se elimina el módulo/subtema, se deja en NULL (el ejercicio puede existir sin clasificación)
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('subtema_id')->references('id')->on('subtemas')->onDelete('set null')->onUpdate('cascade');
            // Si el usuario que lo creó/revisó se elimina, se deja en NULL
            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('revisado_por')->references('id')->on('usuarios')->onDelete('set null')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 4. ejercicios_relacionados
        // ---------------------------------------------------------------
        Schema::table('ejercicios_relacionados', function (Blueprint $table) {
            $table->dropForeign(['ejercicio_id']);
            $table->dropForeign(['relacionado_id']);

            $table->foreign('ejercicio_id')->references('id')->on('ejercicios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('relacionado_id')->references('id')->on('ejercicios')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 5. sesiones_practica
        // ---------------------------------------------------------------
        Schema::table('sesiones_practica', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);

            // Si el usuario se elimina, sus sesiones se eliminan también
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 6. respuestas_usuario
        // ---------------------------------------------------------------
        Schema::table('respuestas_usuario', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['ejercicio_id']);
            $table->dropForeign(['sesion_id']);

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
            // Si el ejercicio se elimina, la respuesta pierde referencia pero se conserva (historial)
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('sesion_id')->references('id')->on('sesiones_practica')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 7. ejercicios_guardados
        // ---------------------------------------------------------------
        Schema::table('ejercicios_guardados', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['ejercicio_id']);

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 8. diagnosticos
        // ---------------------------------------------------------------
        Schema::table('diagnosticos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 9. resultados_diagnostico
        // ---------------------------------------------------------------
        Schema::table('resultados_diagnostico', function (Blueprint $table) {
            $table->dropForeign(['diagnostico_id']);
            $table->dropForeign(['modulo_id']);

            $table->foreign('diagnostico_id')->references('id')->on('diagnosticos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 10. rutas_aprendizaje
        // ---------------------------------------------------------------
        Schema::table('rutas_aprendizaje', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 11. ruta_detalle
        // ---------------------------------------------------------------
        Schema::table('ruta_detalle', function (Blueprint $table) {
            $table->dropForeign(['ruta_id']);
            $table->dropForeign(['subtema_id']);

            $table->foreign('ruta_id')->references('id')->on('rutas_aprendizaje')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('subtema_id')->references('id')->on('subtemas')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 12. simulacros
        // ---------------------------------------------------------------
        Schema::table('simulacros', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 13. simulacro_preguntas
        // ---------------------------------------------------------------
        Schema::table('simulacro_preguntas', function (Blueprint $table) {
            $table->dropForeign(['simulacro_id']);
            $table->dropForeign(['ejercicio_id']);

            $table->foreign('simulacro_id')->references('id')->on('simulacros')->onDelete('cascade')->onUpdate('cascade');
            // Si el ejercicio se elimina, se marca la pregunta como sin referencia (historial del simulacro)
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios')->onDelete('set null')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 14. recursos
        // ---------------------------------------------------------------
        Schema::table('recursos', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);
            $table->dropForeign(['subtema_id']);
            $table->dropForeign(['creado_por']);

            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('subtema_id')->references('id')->on('subtemas')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 15. flashcards
        // ---------------------------------------------------------------
        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropForeign(['subtema_id']);

            $table->foreign('subtema_id')->references('id')->on('subtemas')->onDelete('set null')->onUpdate('cascade');
        });

        // flashcards.creado_por (agregada en migración 000006)
        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropForeign(['creado_por']);

            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 16. posts
        // ---------------------------------------------------------------
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['modulo_id']);
            $table->dropForeign(['subtema_id']);

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('subtema_id')->references('id')->on('subtemas')->onDelete('set null')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 17. respuestas_post
        // ---------------------------------------------------------------
        Schema::table('respuestas_post', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['usuario_id']);

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
        });

        // ---------------------------------------------------------------
        // 18. NUEVAS TABLAS
        // ---------------------------------------------------------------

        // Configuración de estudio por usuario (1-a-1)
        Schema::create('configuracion_estudio', function (Blueprint $table) {
            $table->integer('usuario_id')->primary();
            $table->integer('horas_semana');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
        });

        // Planes de estudio
        Schema::create('planes_estudio', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->integer('horas_semana');
            $table->boolean('activo')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade')->onUpdate('cascade');
        });

        // Detalle diario de cada plan
        Schema::create('plan_detalle', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('plan_id')->nullable();
            $table->date('fecha');
            $table->integer('subtema_id')->nullable();
            $table->integer('ejercicios_recomendados')->nullable();
            $table->integer('tiempo_estimado')->nullable()->comment('En minutos');
            $table->boolean('completado')->default(false);

            $table->foreign('plan_id')->references('id')->on('planes_estudio')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('subtema_id')->references('id')->on('subtemas')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar nuevas tablas (en orden por dependencias)
        Schema::dropIfExists('plan_detalle');
        Schema::dropIfExists('planes_estudio');
        Schema::dropIfExists('configuracion_estudio');

        // Revertir constraints a sin ON DELETE / ON UPDATE
        // (se reestablecen como estaban originalmente, sin acción explícita)

        Schema::table('respuestas_post', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['usuario_id']);
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['modulo_id']);
            $table->dropForeign(['subtema_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('modulo_id')->references('id')->on('modulos');
            $table->foreign('subtema_id')->references('id')->on('subtemas');
        });

        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropForeign(['creado_por']);
            $table->dropForeign(['subtema_id']);
            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('subtema_id')->references('id')->on('subtemas');
        });

        Schema::table('recursos', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);
            $table->dropForeign(['subtema_id']);
            $table->dropForeign(['creado_por']);
            $table->foreign('modulo_id')->references('id')->on('modulos');
            $table->foreign('subtema_id')->references('id')->on('subtemas');
            $table->foreign('creado_por')->references('id')->on('usuarios');
        });

        Schema::table('simulacro_preguntas', function (Blueprint $table) {
            $table->dropForeign(['simulacro_id']);
            $table->dropForeign(['ejercicio_id']);
            $table->foreign('simulacro_id')->references('id')->on('simulacros');
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios');
        });

        Schema::table('simulacros', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });

        Schema::table('ruta_detalle', function (Blueprint $table) {
            $table->dropForeign(['ruta_id']);
            $table->dropForeign(['subtema_id']);
            $table->foreign('ruta_id')->references('id')->on('rutas_aprendizaje');
            $table->foreign('subtema_id')->references('id')->on('subtemas');
        });

        Schema::table('rutas_aprendizaje', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });

        Schema::table('resultados_diagnostico', function (Blueprint $table) {
            $table->dropForeign(['diagnostico_id']);
            $table->dropForeign(['modulo_id']);
            $table->foreign('diagnostico_id')->references('id')->on('diagnosticos');
            $table->foreign('modulo_id')->references('id')->on('modulos');
        });

        Schema::table('diagnosticos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });

        Schema::table('ejercicios_guardados', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['ejercicio_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios');
        });

        Schema::table('respuestas_usuario', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['ejercicio_id']);
            $table->dropForeign(['sesion_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios');
            $table->foreign('sesion_id')->references('id')->on('sesiones_practica');
        });

        Schema::table('sesiones_practica', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });

        Schema::table('ejercicios_relacionados', function (Blueprint $table) {
            $table->dropForeign(['ejercicio_id']);
            $table->dropForeign(['relacionado_id']);
            $table->foreign('ejercicio_id')->references('id')->on('ejercicios');
            $table->foreign('relacionado_id')->references('id')->on('ejercicios');
        });

        Schema::table('ejercicios', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);
            $table->dropForeign(['subtema_id']);
            $table->dropForeign(['creado_por']);
            $table->dropForeign(['revisado_por']);
            $table->foreign('modulo_id')->references('id')->on('modulos');
            $table->foreign('subtema_id')->references('id')->on('subtemas');
            $table->foreign('creado_por')->references('id')->on('usuarios');
            $table->foreign('revisado_por')->references('id')->on('usuarios');
        });

        Schema::table('subtemas', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);
            $table->foreign('modulo_id')->references('id')->on('modulos');
        });

        Schema::table('usuario_roles', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['rol_id']);
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('rol_id')->references('id')->on('roles');
        });
    }
};