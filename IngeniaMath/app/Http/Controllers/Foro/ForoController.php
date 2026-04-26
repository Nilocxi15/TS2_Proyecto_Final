<?php

namespace App\Http\Controllers\Foro;

use App\Http\Controllers\Controller;
use App\Models\Posts;
use App\Models\Modulos;
use App\Services\ForoService;
use Illuminate\Http\Request;

class ForoController extends Controller
{
    public function __construct(
        private readonly ForoService $foroService
    ){}

    public function index(Request $request)
    {
        $query = Posts::with([
            'usuario',
            'modulo',
            'subtema'
        ]);

        // Search text
        if ($request->filled('search')) {
            $query->where('contenido', 'ILIKE', '%' . $request->search . '%');
        }

        // Filter module
        if ($request->filled('module_id')) {
            $query->where('modulo_id', $request->module_id);
        }

        // Filter subtopic
        if ($request->filled('subtopic_id')) {
            $query->where('subtema_id', $request->subtopic_id);
        }

        // Filter author
        if ($request->filled('author')) {
            $query->whereHas('usuario', function ($q) use ($request) {
                $q->where('nombre', 'ILIKE', '%' . $request->author . '%');
            });
        }

        $posts = $query->latest()->paginate(10)->withQueryString();
        $modules = Modulos::orderBy('nombre')->get();

        return view(
            'estudiante.foro.index',
            compact('posts', 'modules')
        );
    }

    public function getSubtopics($moduleId)
    {
        return response()->json(
            $this->foroService->getSubtopicsByModule($moduleId)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'modulo_id' => 'required',
            'subtema_id' => 'required',
            'contenido' => 'required|min:10'
        ]);

        $this->foroService->crearPost($data, auth()->id());

        return redirect()->route('forum.index')
            ->with('success','Duda publicada');
    }

    public function show($id)
    {
        $post = Posts::with([
            'usuario',
            'respuestas' => function ($query) {
                $query->orderByDesc('es_solucion')
                    ->latest();
            },
            'respuestas.usuario'
        ])->findOrFail($id);

        return view('estudiante.foro.show', compact('post'));
    }
}
