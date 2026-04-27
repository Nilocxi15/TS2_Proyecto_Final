<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Recursos;
use App\Models\Subtemas;
use App\Services\CloudinaryService;
use App\Services\TutorRecursosService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Flujo Tutor para recursos: crear, editar, eliminar y enviar a revisión.
 */
class TutorRecursosController extends Controller
{
    public function __construct(
        private readonly TutorRecursosService $tutorRecursosService,
        private readonly CloudinaryService $cloudinaryService,
    ) {
    }

    /**
     * Crea un recurso del tutor y lo deja en BORRADOR o PENDIENTE.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($errorResponse = $this->validarArchivoCargado($request)) {
            return $errorResponse;
        }

        $tutorId = (int) auth()->id();
        $tiposPermitidos = implode(',', $this->tutorRecursosService->obtenerTiposRecursoTutor());

        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'modulo_id' => 'required|integer|exists:modulos,id',
            'subtema_id' => 'required|integer|exists:subtemas,id',
            'tipo' => "nullable|in:{$tiposPermitidos}",
            'url' => 'nullable|url|max:2000',
            'archivo' => 'nullable|file|mimetypes:image/*,video/*,application/pdf|max:204800',
            'accion_envio' => 'nullable|in:enviar,borrador',
        ], [
            'archivo.uploaded' => 'No se pudo subir el archivo. Revisa upload_max_filesize y post_max_size en php.ini.',
            'archivo.max' => 'El archivo supera el limite de 200 MB permitido por el sistema.',
            'archivo.mimetypes' => 'Solo se permiten imagenes, videos o PDF.',
        ]);

        $subtemaPertenece = Subtemas::query()
            ->where('id', (int) $validated['subtema_id'])
            ->where('modulo_id', (int) $validated['modulo_id'])
            ->exists();

        if (! $subtemaPertenece) {
            return back()->withErrors([
                'subtema_id' => 'El subtema seleccionado no pertenece al módulo indicado.',
            ]);
        }

        $fuenteEsUrl = filled($validated['url'] ?? null);
        $fuenteEsArchivo = $request->hasFile('archivo');

        if (! $fuenteEsUrl && ! $fuenteEsArchivo) {
            return back()->withErrors([
                'url' => 'Debes ingresar una URL o subir un archivo.',
            ])->withInput();
        }

        if ($fuenteEsUrl && $fuenteEsArchivo) {
            return back()->withErrors([
                'url' => 'Elige solo una fuente: URL o archivo.',
            ])->withInput();
        }

        $urlFinal = $validated['url'] ?? null;
        $tipoFinal = strtoupper((string) ($validated['tipo'] ?? ''));

        if ($fuenteEsArchivo) {
            $carga = $this->cloudinaryService->subirContenidoTutor($request->file('archivo'), [
                'folder' => 'ingeniamath/recursos/tutor_' . $tutorId,
            ]);

            $urlFinal = $carga['secure_url'] ?? $carga['url'] ?? null;

            $resourceType = $carga['resource_type'] ?? null;
            $format = strtolower((string) ($carga['format'] ?? ''));

            if ($resourceType === 'video') {
                $tipoFinal = 'VIDEO';
            } elseif ($format === 'pdf') {
                $tipoFinal = 'PDF';
            } else {
                $tipoFinal = 'SIMULADOR';
            }
        }

        if ($tipoFinal === '') {
            return back()->withErrors([
                'tipo' => 'Debes seleccionar un tipo cuando usas URL.',
            ])->withInput();
        }

        $estado = ($validated['accion_envio'] ?? 'enviar') === 'borrador'
            ? 'BORRADOR'
            : 'PENDIENTE';

        Recursos::query()->create([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'modulo_id' => (int) $validated['modulo_id'],
            'subtema_id' => (int) $validated['subtema_id'],
            'tipo' => $tipoFinal,
            'url' => $urlFinal,
            'estado' => $estado,
            'creado_por' => $tutorId,
        ]);

        return back()->with('success', $estado === 'BORRADOR'
            ? 'Recurso guardado en borrador.'
            : 'Recurso enviado y marcado como PENDIENTE.');
    }

    /**
     * Actualiza un recurso editable y lo reenvía a PENDIENTE.
     */
    public function update(Request $request, int $recursoId): RedirectResponse
    {
        if ($errorResponse = $this->validarArchivoCargado($request)) {
            return $errorResponse;
        }

        $tutorId = (int) auth()->id();
        $recurso = $this->tutorRecursosService->obtenerRecursoTutorPorId($tutorId, $recursoId);

        if (! $recurso) {
            abort(404);
        }

        if (! $this->tutorRecursosService->recursoEditable($recurso->estado)) {
            return back()->withErrors([
                'estado' => 'Este recurso no se puede editar hasta que vuelva a PUBLICADO.',
            ]);
        }

        $tiposPermitidos = implode(',', $this->tutorRecursosService->obtenerTiposRecursoTutor());

        $validated = $request->validate([
            'titulo' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
            'modulo_id' => 'nullable|integer|exists:modulos,id',
            'subtema_id' => 'nullable|integer|exists:subtemas,id',
            'tipo' => "nullable|in:{$tiposPermitidos}",
            'url' => 'nullable|string|max:2000',
            'archivo' => 'nullable|file|mimetypes:image/*,video/*,application/pdf|max:204800',
        ], [
            'archivo.uploaded' => 'No se pudo subir el archivo. Revisa upload_max_filesize y post_max_size en php.ini.',
            'archivo.max' => 'El archivo supera el limite de 200 MB permitido por el sistema.',
            'archivo.mimetypes' => 'Solo se permiten imagenes, videos o PDF.',
        ]);

        if (! empty($validated['subtema_id']) && ! empty($validated['modulo_id'])) {
            $subtemaPertenece = Subtemas::query()
                ->where('id', (int) $validated['subtema_id'])
                ->where('modulo_id', (int) $validated['modulo_id'])
                ->exists();

            if (! $subtemaPertenece) {
                return back()->withErrors([
                    'subtema_id' => 'El subtema seleccionado no pertenece al módulo indicado.',
                ]);
            }
        }

        if ($request->hasFile('archivo')) {
            $carga = $this->cloudinaryService->subirContenidoTutor($request->file('archivo'), [
                'folder' => 'ingeniamath/recursos/tutor_' . $tutorId,
            ]);

            $validated['url'] = $carga['secure_url'] ?? $carga['url'] ?? $validated['url'] ?? null;

            $resourceType = $carga['resource_type'] ?? null;
            $format = strtolower((string) ($carga['format'] ?? ''));

            if ($resourceType === 'video') {
                $validated['tipo'] = 'VIDEO';
            } elseif ($format === 'pdf') {
                $validated['tipo'] = 'PDF';
            } else {
                $validated['tipo'] = $validated['tipo'] ?? 'SIMULADOR';
            }
        }

        unset($validated['archivo']);
        $validated['estado'] = 'PENDIENTE';

        $recurso->fill($validated);
        $recurso->save();

        return back()->with('success', 'Recurso actualizado y enviado a estado PENDIENTE.');
    }

    /**
     * Elimina un recurso propiedad del tutor autenticado.
     */
    public function destroy(int $recursoId): RedirectResponse
    {
        $tutorId = (int) auth()->id();
        $recurso = $this->tutorRecursosService->obtenerRecursoTutorPorId($tutorId, $recursoId);

        if (! $recurso) {
            abort(404);
        }

        $recurso->delete();

        return back()->with('success', 'Recurso eliminado correctamente.');
    }

    /**
     * Cambia el estado según reglas permitidas para tutor.
     */
    public function cambiarEstado(Request $request, int $recursoId): RedirectResponse
    {
        $tutorId = (int) auth()->id();
        $recurso = $this->tutorRecursosService->obtenerRecursoTutorPorId($tutorId, $recursoId);

        if (! $recurso) {
            abort(404);
        }

        $estadoPermitido = implode(',', $this->tutorRecursosService->obtenerEstadosContenido());

        $validated = $request->validate([
            'estado' => "required|in:{$estadoPermitido}",
        ]);

        if (! $this->tutorRecursosService->tutorPuedeCambiarEstado($recurso->estado, $validated['estado'])) {
            return back()->withErrors([
                'estado' => 'No puedes cambiar este estado desde tu panel. Los recursos en PENDIENTE o REVISION se resuelven por revisión.',
            ]);
        }

        $recurso->estado = $validated['estado'];
        $recurso->save();

        return back()->with('success', 'Estado del recurso actualizado correctamente.');
    }

    /**
     * Valida fallos de subida para devolver un error entendible al tutor.
     */
    private function validarArchivoCargado(Request $request): ?RedirectResponse
    {
        if (! $request->hasFile('archivo')) {
            return null;
        }

        $archivo = $request->file('archivo');

        if (! $archivo instanceof UploadedFile) {
            return null;
        }

        if ($archivo->isValid()) {
            return null;
        }

        $errorCode = $archivo->getError();
        $detalle = $this->descripcionErrorSubida($errorCode);
        $uploadMax = (string) ini_get('upload_max_filesize');
        $postMax = (string) ini_get('post_max_size');
        $tmpDir = (string) (ini_get('upload_tmp_dir') ?: sys_get_temp_dir());

        return back()->withErrors([
            'archivo' => "No se pudo subir el archivo ({$detalle}). Codigo PHP: {$errorCode}. Limites actuales: upload_max_filesize={$uploadMax}, post_max_size={$postMax}. Carpeta temporal: {$tmpDir}.",
        ])->withInput();
    }

    /**
     * Traduce códigos nativos de subida de PHP a mensajes funcionales.
     */
    private function descripcionErrorSubida(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'excede upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'excede el limite del formulario',
            UPLOAD_ERR_PARTIAL => 'la subida llego incompleta',
            UPLOAD_ERR_NO_FILE => 'no se recibio archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'no hay carpeta temporal para subidas',
            UPLOAD_ERR_CANT_WRITE => 'no se pudo escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION => 'una extension de PHP detuvo la subida',
            default => 'error de subida no especificado',
        };
    }
}
