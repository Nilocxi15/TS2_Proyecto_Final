<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE recursos DROP CONSTRAINT IF EXISTS recursos_estado_check');
            DB::statement("ALTER TABLE recursos ADD CONSTRAINT recursos_estado_check CHECK ((estado)::text = ANY ((ARRAY['BORRADOR'::character varying, 'PENDIENTE'::character varying, 'REVISION'::character varying, 'APROBADO'::character varying, 'PUBLICADO'::character varying, 'DESHABILITADO'::character varying, 'RECHAZADO'::character varying])::text[]))");
            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recursos MODIFY COLUMN estado ENUM('BORRADOR','PENDIENTE','REVISION','APROBADO','PUBLICADO','DESHABILITADO','RECHAZADO') NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE recursos DROP CONSTRAINT IF EXISTS recursos_estado_check');
            DB::statement("ALTER TABLE recursos ADD CONSTRAINT recursos_estado_check CHECK ((estado)::text = ANY ((ARRAY['BORRADOR'::character varying, 'REVISION'::character varying, 'APROBADO'::character varying, 'PUBLICADO'::character varying, 'DESHABILITADO'::character varying])::text[]))");
            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE recursos MODIFY COLUMN estado ENUM('BORRADOR','REVISION','APROBADO','PUBLICADO','DESHABILITADO') NULL");
        }
    }
};
