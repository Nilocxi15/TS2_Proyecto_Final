<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Enums\RolEnum;

class LoginController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresa un correo valido.',
            'password.required' => 'La contrasena es obligatoria.',
        ]);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'activo' => true,
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        $usuario = Auth::user();

        if ($usuario->tieneRol(RolEnum::ADMINISTRADOR->value)) {
            return redirect()->route('admin.dashboard');
        }

        if ($usuario->tieneRol(RolEnum::TUTOR->value)) {
            return redirect()->route('tutor.dashboard');
        }

        if ($usuario->tieneRol(RolEnum::REVISOR->value)) {
            return redirect()->route('moderador.dashboard');
        }

        return redirect()->route('student.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
