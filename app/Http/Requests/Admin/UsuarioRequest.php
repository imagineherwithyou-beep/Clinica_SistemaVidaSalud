<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    // Defensa adicional a la del middleware de la ruta.
    public function authorize(): bool
    {
        return $this->user()?->tieneRol(Role::ADMINISTRADOR) ?? false;
    }

    // Se normaliza para que "Ana" y "ana" no cuenten como dos usuarios distintos.
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre_usuario' => Str::lower(trim((string) $this->input('nombre_usuario'))),
            'email' => Str::lower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario'); // null al crear

        return [
            'nombre_usuario' => [
                'required', 'string', 'min:3', 'max:100',
                'regex:/^[a-z0-9._-]+$/',
                Rule::unique('users', 'nombre_usuario')->ignore($usuario),
            ],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($usuario), // A1: correo duplicado
            ],
            'rol_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            'password' => [
                $usuario ? 'nullable' : 'required', // al editar, vacía = no cambiar
                'string', 'min:8', 'max:72',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_usuario.required' => 'Ingrese el nombre de usuario.',
            'nombre_usuario.min' => 'El nombre de usuario debe tener al menos :min caracteres.',
            'nombre_usuario.regex' => 'Use solo letras, números, punto, guion y guion bajo.',
            'nombre_usuario.unique' => 'Ese nombre de usuario ya está en uso. Elija otro.',
            'email.required' => 'Ingrese el correo electrónico.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'email.unique' => 'Ese correo electrónico ya está registrado.',
            'rol_id.required' => 'Seleccione un rol.',
            'rol_id.exists' => 'El rol seleccionado no es válido.',
            'password.required' => 'Ingrese una contraseña.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.regex' => 'La contraseña debe combinar mayúsculas, minúsculas y números.',
            'password.confirmed' => 'La confirmación no coincide con la contraseña.',
        ];
    }
}
