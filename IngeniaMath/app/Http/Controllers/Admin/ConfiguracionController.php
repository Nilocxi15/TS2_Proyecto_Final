<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuraciones;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configuraciones = Configuraciones::all();
        return view('admin.configuraciones.index', compact('configuraciones'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $clave => $valor) {
            Configuraciones::where('clave', $clave)->update(['valor' => $valor]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuraciones actualizadas correctamente.');
    }
}
