<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $usuario = auth()->user();

        if (!$usuario) {
            abort(403);
        }

        foreach ($roles as $rol) {
            if ($usuario->tieneRol((int)$rol)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
