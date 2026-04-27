<?php

namespace App\Http\Controllers\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\Usuarios;
use App\Services\UsuarioServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
    public function index(Request $request)
    {
        $query = Usuarios::with('roles');

        // Filtro por estado
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        // Filtro por rol
        if ($request->filled('rol_id')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('id', $request->rol_id);
            });
        }

        // Búsqueda
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'ILIKE', "%{$request->search}%")
                    ->orWhere('apellido', 'ILIKE', "%{$request->search}%")
                    ->orWhere('email', 'ILIKE', "%{$request->search}%");
            });
        }

        $usuarios = $query->orderBy('id')->paginate(10);
        $roles = Roles::all();

        return view('admin.users.index', compact('usuarios', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Roles::all();

        return view('admin.users.form', [
            'usuario' => new Usuarios(),
            'roles' => $roles,
            'isEdit' => false
        ]);
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
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmacion de contraseña no coincide.',
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

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:usuarios,email'],
            'password' => ['required', 'min:8'],
            'rol_id' => ['required', 'exists:roles,id'],
        ]);

        try {
            $this->usuarioServices->crearUsuario($validated, $validated['rol_id']);

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario creado correctamente');

        } catch (Throwable $e) {
            return back()->withInput()
                ->with('error', 'Error al crear usuario');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $usuario = Usuarios::with('roles')->findOrFail($id);

        return view('admin.users.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $usuario = Usuarios::with('roles')->findOrFail($id);
        $roles = Roles::all();

        return view('admin.users.form', [
            'usuario' => $usuario,
            'roles' => $roles,
            'isEdit' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuarios::findOrFail($id);

        $validated = $this->validateUsuario($request, $id);

        try {
            $this->usuarioServices->actualizarUsuario(
                $usuario,
                $validated,
                $validated['rol_id']
            );

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario actualizado');

        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar usuario');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = Usuarios::findOrFail($id);

        $usuario->update(['activo' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario deshabilitado');
    }

    private function validateUsuario(Request $request, $id = null)
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => "required|email|unique:usuarios,email,$id",
            'password' => $id ? 'nullable|min:8' : 'required|min:8',
            'rol_id' => 'required|exists:roles,id'
        ]);
    }
}
