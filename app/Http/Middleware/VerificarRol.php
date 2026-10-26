<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permisos por rol (RN12). Uso en rutas:
 *   ->middleware(VerificarRol::using(Rol::ADMINISTRADOR, Rol::RECEPCIONISTA))
 */
class VerificarRol
{
    public static function using(string ...$roles): string
    {
        return static::class.':'.implode(',', $roles);
    }

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        abort_unless(
            $usuario && $usuario->tieneRol(...$roles),
            403,
            'No tiene permiso para acceder a esta sección.'
        );

        return $next($request);
    }
}
