<?php

namespace App\Http\Requests\Recepcion;

use App\Models\Medico;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class DisponibilidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tieneRol(Role::RECEPCIONISTA) ?? false;
    }

    public function rules(): array
    {
        return [
            'medico_id' => ['nullable', 'integer', Rule::exists('medicos', 'id')->where('estado', true)],
            'fecha' => ['nullable', 'date', 'after_or_equal:today'],
            'especialidad_id' => ['nullable', 'integer', Rule::exists('especialidades', 'id')->where('estado', true)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $medicoId = $this->input('medico_id');
            $especialidadId = $this->input('especialidad_id');
            if ($medicoId && $especialidadId && ! $validator->errors()->has('medico_id') && ! $validator->errors()->has('especialidad_id')) {
                $asignada = Medico::whereKey($medicoId)->whereHas('especialidades', fn ($query) => $query
                    ->whereKey($especialidadId)->where('especialidades.estado', true))->exists();
                if (! $asignada) {
                    $validator->errors()->add('especialidad_id', 'El médico no atiende la especialidad seleccionada.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'medico_id.exists' => 'Seleccione un médico activo.',
            'fecha.after_or_equal' => 'La fecha debe ser hoy o posterior.',
            'especialidad_id.exists' => 'Seleccione una especialidad activa.',
        ];
    }
}
