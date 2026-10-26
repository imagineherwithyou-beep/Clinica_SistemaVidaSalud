<?php

namespace App\Http\Requests\Recepcion;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tieneRol(Role::RECEPCIONISTA) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ci' => strtoupper(trim((string) $this->input('ci', ''))),
            'nombres' => trim((string) $this->input('nombres', '')),
            'apellidos' => trim((string) $this->input('apellidos', '')),
            'correo' => $this->filled('correo') ? strtolower(trim((string) $this->input('correo'))) : null,
            'contacto_emergencia_nombre' => $this->filled('contacto_emergencia_nombre') ? trim((string) $this->input('contacto_emergencia_nombre')) : null,
        ]);
    }

    public function rules(): array
    {
        $paciente = $this->route('paciente');

        return [
            'ci' => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9 .\/-]+$/', Rule::unique('pacientes', 'ci')->ignore($paciente)],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today', 'after:1900-01-01'],
            'sexo' => ['nullable', Rule::in(['M', 'F', 'O'])],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^[0-9+().\s-]+$/'],
            'correo' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:2000'],
            'tipo_sangre' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'alergias' => ['nullable', 'string', 'max:5000'],
            'enfermedades_cronicas' => ['nullable', 'string', 'max:5000'],
            'contacto_emergencia_nombre' => ['nullable', 'string', 'max:100'],
            'contacto_emergencia_telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+().\s-]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.required' => 'Ingrese el CI del paciente.',
            'ci.unique' => 'Ese CI ya está registrado. Busque la ficha existente antes de continuar.',
            'ci.regex' => 'El CI solo puede contener letras, números, espacios, punto, guion y barra.',
            'nombres.required' => 'Ingrese los nombres del paciente.',
            'apellidos.required' => 'Ingrese los apellidos del paciente.',
            'fecha_nacimiento.required' => 'Ingrese la fecha de nacimiento.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede estar en el futuro.',
            'fecha_nacimiento.after' => 'Verifique la fecha de nacimiento ingresada.',
            'telefono.required' => 'Ingrese un teléfono de contacto.',
            'telefono.regex' => 'El teléfono contiene caracteres no válidos.',
            'correo.email' => 'Ingrese un correo electrónico válido.',
            'contacto_emergencia_telefono.regex' => 'El teléfono de emergencia contiene caracteres no válidos.',
        ];
    }
}
