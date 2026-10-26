<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class MedicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tieneRol(Role::ADMINISTRADOR) ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['registro_profesional', 'nombres', 'apellidos', 'telefono', 'correo'] as $field) {
            $value = trim((string) $this->input($field, ''));
            $this->merge([$field => $value === '' ? null : $value]);
        }
    }

    public function rules(): array
    {
        $medico = $this->route('medico');
        $medicoRoleId = Role::where('nombre_rol', Role::MEDICO)->value('id');

        return [
            'registro_profesional' => ['required', 'string', 'max:50', Rule::unique('medicos', 'registro_profesional')->ignore($medico)],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'telefono' => ['required', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:100'],
            'estado' => ['sometimes', 'boolean'],
            'especialidades' => $medico ? ['sometimes', 'array'] : ['required', 'array', 'min:1'],
            'especialidades.*' => ['required', 'integer', Rule::exists('especialidades', 'id')->where('estado', true)],
            'user_id' => [
                'nullable', 'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('estado', true)->where('rol_id', $medicoRoleId)),
                Rule::unique('medicos', 'user_id')->ignore($medico),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has('especialidades')) {
                return;
            }

            $medico = $this->route('medico');
            $seleccionadas = $this->input('especialidades', []);
            if (! $medico && count($seleccionadas) === 0) {
                return;
            }

            $conservaInactiva = $medico?->especialidades()->where('estado', false)->exists() ?? false;
            if (count($seleccionadas) === 0 && ! $conservaInactiva) {
                $validator->errors()->add('especialidades', 'Seleccione al menos una especialidad activa.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'registro_profesional.required' => 'Ingrese el registro profesional.',
            'registro_profesional.unique' => 'Ese registro profesional ya está asociado a otro médico.',
            'nombres.required' => 'Ingrese los nombres del médico.',
            'apellidos.required' => 'Ingrese los apellidos del médico.',
            'telefono.required' => 'Ingrese un teléfono de contacto.',
            'correo.email' => 'Ingrese un correo electrónico válido.',
            'especialidades.required' => 'Seleccione al menos una especialidad.',
            'especialidades.min' => 'Seleccione al menos una especialidad.',
            'especialidades.*.exists' => 'Una de las especialidades seleccionadas no existe o está inactiva.',
            'user_id.exists' => 'Seleccione una cuenta activa con rol Médico.',
            'user_id.unique' => 'Esa cuenta ya está asociada a otro médico.',
        ];
    }
}
