<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use App\Services\UsuarioServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class UsuariosController extends Controller
{
    public function __construct(
        private readonly UsuarioServices $usuarioServices,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'lastname.required' => 'Los apellidos son obligatorios.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresa un correo valido.',
            'email.unique' => 'Ese correo ya esta registrado.',
            'password.required' => 'La contrasena es obligatoria.',
            'password.min' => 'La contrasena debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmacion de contrasena no coincide.',
        ]);

        try {
            $this->usuarioServices->registrarEstudiante($validated);

            return redirect('/')
                ->with('status', 'Cuenta creada correctamente. Ahora puedes iniciar sesion.');
        } catch (Throwable $exception) {
            Log::error('Error registrando usuario', [
                'message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'register' => 'No fue posible completar el registro. Intenta nuevamente.',
                ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Usuarios $usuarios)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuarios $usuarios)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usuarios $usuarios)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuarios $usuarios)
    {
        //
    }
}
