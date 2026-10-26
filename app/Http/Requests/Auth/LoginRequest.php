<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const MAX_INTENTOS = 5;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['login' => Str::lower(trim((string) $this->input('login')))]);
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'Ingrese su correo electrónico o nombre de usuario.',
            'password.required' => 'Ingrese su contraseña.',
        ];
    }

    /**
     * CU01: valida las credenciales (A1) y el estado de la cuenta (A2).
     * Si todo es correcto deja la sesión iniciada.
     */
    public function authenticate(): void
    {
        $this->verificarBloqueo();

        $login = $this->input('login');
        $campo = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nombre_usuario';

        // A1: credenciales inválidas. Mensaje genérico: no revela si el usuario existe.
        if (! Auth::once([$campo => $login, 'password' => $this->input('password')])) {
            RateLimiter::hit($this->claveBloqueo());

            throw ValidationException::withMessages([
                'login' => 'Las credenciales no son correctas. Revise el correo o usuario y la contraseña.',
            ]);
        }

        // A2: cuenta inactiva. Solo se informa cuando la contraseña fue correcta.
        if (! Auth::user()->estado) {
            throw ValidationException::withMessages([
                'login' => 'Su cuenta está suspendida. Comuníquese con el administrador.',
            ]);
        }

        Auth::login(Auth::user());
        RateLimiter::clear($this->claveBloqueo());
    }

    private function verificarBloqueo(): void
    {
        if (! RateLimiter::tooManyAttempts($this->claveBloqueo(), self::MAX_INTENTOS)) {
            return;
        }

        $segundos = RateLimiter::availableIn($this->claveBloqueo());

        throw ValidationException::withMessages([
            'login' => "Demasiados intentos. Vuelva a intentarlo en {$segundos} segundos.",
        ]);
    }

    private function claveBloqueo(): string
    {
        return Str::transliterate($this->input('login').'|'.$this->ip());
    }
}
