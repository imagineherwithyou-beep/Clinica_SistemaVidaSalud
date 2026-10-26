<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    // CU01 – pasos 1 y 2: vista de login
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    // CU01 – pasos 3 a 5: valida, inicia sesión y entra al panel de su rol
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Nuevo ID de sesión tras autenticarse (evita fijación de sesión)
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // CU02 – cerrar sesión
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Cerró sesión correctamente.');
    }
}
