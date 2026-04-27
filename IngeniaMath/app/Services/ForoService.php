<?php

namespace App\Services;

use App\Models\Modulos;
use App\Models\Posts;
use App\Models\RespuestasPost;
use App\Models\Subtemas;
use Illuminate\Support\Facades\DB;

class ForoService
{
    public function crearPost(array $data, int $usuarioId): Posts
    {
        return Posts::create([
            'usuario_id' => $usuarioId,
            'modulo_id' => $data['modulo_id'],
            'subtema_id' => $data['subtema_id'],
            'contenido' => $data['contenido'],
            'estado' => 'ABIERTO'
        ]);
    }
    public function getPosts()
    {
        return Posts::with([
            'usuario',
            'modulo',
            'subtema'
        ])
            ->latest()
            ->paginate(10);
    }

    public function getModules()
    {
        return Modulos::orderBy('nombre')->get();
    }

    public function getSubtopicsByModule(int $moduleId)
    {
        return Subtemas::where('modulo_id', $moduleId)
            ->orderBy('nombre')
            ->get();
    }

    public function createResponse(array $data, int $userId, int $postId): RespuestasPost
    {
        return RespuestasPost::create([
            'post_id' => $postId,
            'usuario_id' => $userId,
            'contenido' => $data['contenido'],
            'es_solucion' => false,
            'created_at' => now(),
        ]);
    }

    public function marcarSolucion(int $respuestaId, int $postId)
    {
        DB::transaction(function () use ($respuestaId, $postId) {

            RespuestasPost::where('post_id', $postId)
                ->update(['es_solucion' => false]);

            RespuestasPost::where('id', $respuestaId)
                ->update(['es_solucion' => true]);

            Posts::where('id', $postId)
                ->update(['estado' => 'RESUELTO']);
        });
    }

    public function resolvePost(int $postId)
    {
        Posts::where('id', $postId)
            ->update(['estado' => 'RESUELTO']);
    }
}
