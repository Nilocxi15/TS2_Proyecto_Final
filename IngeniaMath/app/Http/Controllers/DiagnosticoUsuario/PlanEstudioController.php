<?php

namespace App\Http\Controllers\DiagnosticoUsuario;

use App\Http\Controllers\Controller;
use App\Services\DiagnosticoUsuario\PlanEstudioService;
use Illuminate\Http\Request;

class PlanEstudioController extends Controller
{
    public function __construct(
        private PlanEstudioService $service
    ) {}

    public function index()
    {
        $usuarioId = auth()->id();

        $horas = $this->service->obtenerHoras($usuarioId);
        $plan = $this->service->obtenerPlanActivo($usuarioId);

        return view('estudiante.study-plan.index', compact(
            'horas',
            'plan'
        ));
    }

    public function saveHours(Request $request)
    {
        $request->validate([
            'horas_semana' => 'required|integer|min:1|max:80'
        ]);

        $usuarioId = auth()->id();

        $this->service->guardarHoras(
            $usuarioId,
            $request->horas_semana
        );

        $this->service->generarPlan($usuarioId);

        return redirect()->route('student.study-plan.index')
            ->with('success', 'Plan semanal generado.');
    }
}
