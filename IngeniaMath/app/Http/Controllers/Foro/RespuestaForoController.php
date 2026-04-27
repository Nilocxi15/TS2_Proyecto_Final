<?php

namespace App\Http\Controllers\Foro;

use App\Http\Controllers\Controller;
use App\Models\Posts;
use App\Models\RespuestasPost;
use App\Services\ForoService;
use Illuminate\Http\Request;

class RespuestaForoController extends Controller
{

    public function __construct(
        private readonly ForoService $foroService
    ){}

    public function store(Request $request, $post)
    {
        $data = $request->validate([
            'contenido' => 'required|min:5'
        ]);

        $this->foroService->createResponse(
            $data,
            auth()->id(),
            $post
        );

        return redirect()
            ->route('forum.show', $post)
            ->with('success', 'Respuesta publicada');
    }

    public function approve($postId, $answerId)
    {
        $post = Posts::findOrFail($postId);

        // Solo dueño del post
        if ($post->usuario_id !== auth()->id()) {
            abort(403);
        }

        // Si ya existe solución aceptada no permitir más
        $alreadySolved = RespuestasPost::where('post_id', $postId)
            ->where('es_solucion', true)
            ->exists();

        if ($alreadySolved) {
            return back()->with('error', 'Ya existe una solución aceptada.');
        }

        $answer = RespuestasPost::where('post_id', $postId)
            ->findOrFail($answerId);

        $answer->update([
            'es_solucion' => true
        ]);

        return back()->with('success', 'Respuesta marcada como solución.');
    }
}
