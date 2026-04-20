<?php

namespace Database\Seeders;

use DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recursos = [
            ['titulo' => 'Números Reales', 'descripcion' => 'PDF con explicación de los números reales', 'modulo_id' => 1, 'subtema_id' => 1,'tipo' => 'PDF', 'url' => 'https://www.ipn.mx/assets/files/cecyt19/docs/2024/Estudiantes/Tramites%20y%20Servicios%20/manual-algebra.pdf', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Números Reales', 'descripcion' => 'Video explicativo de los números reales', 'modulo_id' => 1, 'subtema_id' => 1,'tipo' => 'VIDEO', 'url' => 'https://www.youtube.com/watch?v=fzWCqlM_Hx0', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Fundamentos de Álgebra', 'descripcion' => 'PDF con fundamentos de álgebra', 'modulo_id' => 2, 'subtema_id' => 7,'tipo' => 'PDF', 'url' => 'https://www.ocw.mit.edu/courses/res-8-001-applied-geometric-algebra-spring-2009/resources/lecture-notes/', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Fundamentos de Álgebra', 'descripcion' => 'Video explicativo de fundamentos de álgebra', 'modulo_id' => 2, 'subtema_id' => 7,'tipo' => 'VIDEO', 'url' => 'https://www.youtube.com/watch?v=4LouAWcajJs&list=PLeySRPnY35dGSJYCRn7tD1mVatw3MrdIm', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Ecuaciones e Inecuaciones', 'descripcion' => 'PDF con explicación de ecuaciones e inecuaciones', 'modulo_id' => 3, 'subtema_id' => 11,'tipo' => 'PDF', 'url' => 'https://diposit.ub.edu/server/api/core/bitstreams/809be9d9-b9e2-4863-b9dc-4fb2eb9c6e9c/content', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Ecuaciones e Inecuaciones', 'descripcion' => 'Video explicativo de ecuaciones e inecuaciones', 'modulo_id' => 3, 'subtema_id' => 11,'tipo' => 'VIDEO', 'url' => 'https://www.youtube.com/watch?v=gMDAtLLW5lM', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Funciones y Gráficas', 'descripcion' => 'PDF con explicación de funciones y gráficas', 'modulo_id' => 4, 'subtema_id' => 15,'tipo' => 'PDF', 'url' => 'https://www.cimat.mx/ciencia_para_jovenes/bachillerato/libros/algebra_angel_cap3.pdf', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Funciones y Gráficas', 'descripcion' => 'Video explicativo de funciones y gráficas', 'modulo_id' => 4, 'subtema_id' => 15,'tipo' => 'VIDEO', 'url' => 'https://www.youtube.com/watch?v=kvGsIo1TmsM', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Geometría Analítica', 'descripcion' => 'PDF con explicación de geometría analítica', 'modulo_id' => 5, 'subtema_id' => 20,'tipo' => 'PDF', 'url' => 'https://www.ocw.mit.edu/courses/res-8-001-applied-geometric-algebra-spring-2009/resources/lecture-notes/', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Geometría Analítica', 'descripcion' => 'Video explicativo de geometría analítica', 'modulo_id' => 5, 'subtema_id' => 20,'tipo' => 'VIDEO', 'url' => 'https://www.youtube.com/watch?v=u25vkjeCrF4', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Geometría', 'descripcion' => 'PDF con explicación de geometría', 'modulo_id' => 6, 'subtema_id' => 23,'tipo' => 'PDF', 'url' => 'https://diposit.ub.edu/server/api/core/bitstreams/809be9d9-b9e2-4863-b9dc-4fb2eb9c6e9c/content', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Geometría', 'descripcion' => 'Video explicativo de geometría', 'modulo_id' => 6, 'subtema_id' => 23,'tipo' => 'VIDEO', 'url' => 'https://www.youtube.com/watch?v=CAkMUdeB06o', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Trigonometría', 'descripcion' => 'PDF con explicación de trigonometría', 'modulo_id' => 7, 'subtema_id' => 28,'tipo' => 'PDF', 'url' => 'https://www.ocw.mit.edu/courses/18-03-differential-equations-spring-2010/resources/lecture-notes/', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['titulo' => 'Trigonometría', 'descripcion' => 'Video explicativo de trigonometría', 'modulo_id' => 7, 'subtema_id' => 28,'tipo' => 'VIDEO', 'url' => 'https://www.youtube.com/watch?v=PUB0TaZ7bhA', 'estado' => 'PUBLICADO', 'creado_por' => 4],
        ];

        foreach ($recursos as $recurso) {
            DB::table('recursos')->updateOrInsert(
                [
                    'titulo' => $recurso['titulo'],
                    'descripcion' => $recurso['descripcion'],
                    'modulo_id' => $recurso['modulo_id'],
                    'subtema_id' => $recurso['subtema_id'],
                    'tipo' => $recurso['tipo'],
                    'url' => $recurso['url'],
                    'estado' => $recurso['estado'],
                    'creado_por' => $recurso['creado_por'],
                ],
            );
        }
    }
}
