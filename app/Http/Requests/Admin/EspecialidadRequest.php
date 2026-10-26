<?php

namespace App\Http\Requests\Admin;

use App\Models\Especialidad;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Str;

class EspecialidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tieneRol(Role::ADMINISTRADOR) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['nombre' => trim((string) $this->input('nombre', ''))]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'estado' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has('nombre') || ! $this->filled('nombre')) {
                return;
            }

            $especialidad = $this->route('especialidad');
            $duplicada = Especialidad::query()
                ->whereRaw('LOWER(TRIM(nombre)) = ?', [Str::lower($this->input('nombre'))])
                ->when($especialidad, fn ($query) => $query->where('id', '!=', $especialidad->id))
                ->exists();

            if ($duplicada) {
                $validator->errors()->add('nombre', 'Ya existe una especialidad con ese nombre.');
            }
        });
    }

    public function messages(): array
    {
        return ['nombre.required' => 'Ingrese el nombre de la especialidad.'];
    }
}
