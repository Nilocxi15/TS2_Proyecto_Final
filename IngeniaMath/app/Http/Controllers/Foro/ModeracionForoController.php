<?php

namespace App\Http\Controllers\Foro;

use App\Http\Controllers\Controller;
use App\Models\Posts;
use App\Models\RespuestasPost;
use App\Services\ForoService;

class ModeracionForoController extends Controller
{

    public function __construct(
        private readonly ForoService $foroService
    ){}

    public function resolve($id)
    {
        $this->foroService->resolvePost($id);

        return back()->with(
            'success',
            'El hilo fue marcado como resuelto.'
        );
    }

    public function deletePost($id)
    {
        Posts::findOrFail($id)->delete();

        return redirect()
            ->route('forum.index')
            ->with('success', 'Publicación eliminada.');
    }

    public function deleteResponse($id)
    {
        RespuestasPost::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Respuesta eliminada.'
        );
    }
}
