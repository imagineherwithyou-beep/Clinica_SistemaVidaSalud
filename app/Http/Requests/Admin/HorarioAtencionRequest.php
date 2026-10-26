<?php

namespace App\Http\Requests\Admin;

use App\Models\Medico;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class HorarioAtencionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tieneRol(Role::ADMINISTRADOR) ?? false;
    }

    public function rules(): array
    {
        return [
            'medico_id' => ['required', 'integer', 'exists:medicos,id'],
            'dia_semana' => ['required', 'integer', 'between:1,7'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i'],
            'intervalo_minutos' => ['required', 'integer', 'min:1', 'max:240'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $medicoId = $this->input('medico_id');
            $medico = $medicoId ? Medico::find($medicoId) : null;
            if ($medico && ! $medico->estado) {
                $validator->errors()->add('medico_id', 'Solo se puede configurar disponibilidad para médicos activos.');
            }

            $inicio = $this->input('hora_inicio');
            $fin = $this->input('hora_fin');
            if ($inicio && $fin && strtotime($fin) <= strtotime($inicio)) {
                $validator->errors()->add('hora_fin', 'La hora final debe ser posterior a la hora inicial.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'medico_id.required' => 'Seleccione un médico.',
            'medico_id.exists' => 'El médico seleccionado no existe.',
            'dia_semana.between' => 'Seleccione un día válido de la semana.',
            'hora_inicio.date_format' => 'Ingrese una hora inicial válida.',
            'hora_fin.date_format' => 'Ingrese una hora final válida.',
            'intervalo_minutos.min' => 'El intervalo debe ser mayor que cero.',
            'intervalo_minutos.max' => 'El intervalo no puede superar 240 minutos.',
        ];
    }
}
