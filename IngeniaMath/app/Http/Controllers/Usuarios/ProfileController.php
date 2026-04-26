<?php

namespace App\Http\Controllers\Usuarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => "required|email|unique:usuarios,email,{$user->id}",
            'password' => 'nullable|min:8',
        ]);

        DB::beginTransaction();

        try {
            $user->update([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email' => $validated['email'],
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password_hash' => Hash::make($request->password)
                ]);
            }

            DB::commit();

            return back()->with('success', 'Perfil actualizado');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar perfil');
        }
    }

}
