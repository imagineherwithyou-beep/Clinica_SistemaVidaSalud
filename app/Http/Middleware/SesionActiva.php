<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Se aplica a todas las rutas internas:
 *  1. Si el administrador desactivó la cuenta mientras el usuario tenía sesión abierta, la cierra.
 *  2. Evita que el navegador guarde páginas protegidas (botón "Atrás" después de cerrar sesión).
 */
class SesionActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && ! $usuario->estado) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'Su cuenta está suspendida. Comuníquese con el administrador.',
            ]);
        }

        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        return $response;
    }
}
