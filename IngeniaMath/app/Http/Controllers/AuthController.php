<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\RolEnum;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credenciales = [
            'email' => $request->email,
            'password' => $request->password,
            'activo' => true
        ];

        if (Auth::guard('web')->attempt($credenciales)) {

            $request->session()->regenerate();

            $usuario = Auth::user();

            if ($usuario->tieneRol(RolEnum::ADMINISTRADOR->value)) {
                return redirect('/admin/dashboard');
            }

            if ($usuario->tieneRol(RolEnum::TUTOR->value)) {
                return redirect('/tutor/dashboard');
            }

            if ($usuario->tieneRol(RolEnum::REVISOR->value)) {
                return redirect('/revisor/dashboard');
            }

            return redirect('/student/dashboard');
        }

        return back()->with('error', 'Credenciales inválidas');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
