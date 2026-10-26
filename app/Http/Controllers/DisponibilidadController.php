<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recepcion\DisponibilidadRequest;
use App\Models\Especialidad;
use App\Models\HorarioAtencion;
use App\Models\Medico;
use App\Services\DisponibilidadService;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class DisponibilidadController extends Controller
{
    public function index(DisponibilidadRequest $request, DisponibilidadService $disponibilidad): View
    {
        $filtros = $request->validated();
        $especialidades = Especialidad::where('estado', true)
            ->whereHas('medicos', fn ($query) => $query->where('medicos.estado', true))
            ->orderBy('nombre')->get();
        $medicos = Medico::where('estado', true)
            ->when(isset($filtros['especialidad_id']), fn ($query) => $query->whereHas('especialidades', fn ($especialidad) => $especialidad
                ->whereKey($filtros['especialidad_id'])->where('especialidades.estado', true)))
            ->with(['especialidades' => fn ($query) => $query->where('especialidades.estado', true)])
            ->orderBy('apellidos')->orderBy('nombres')->get();
        $medico = $medicos->firstWhere('id', (int) ($filtros['medico_id'] ?? 0)) ?? $medicos->first();
        $fecha = CarbonImmutable::parse($filtros['fecha'] ?? today()->toDateString());
        $slots = $medico ? $disponibilidad->intervalos($medico->id, $fecha->toDateString()) : [];
        $hayHorario = $medico && HorarioAtencion::where('medico_id', $medico->id)->where('dia_semana', $fecha->dayOfWeekIso)->exists();

        return view('recepcion.disponibilidad.index', [
            'fecha' => $fecha,
            'medico' => $medico,
            'medicos' => $medicos,
            'especialidades' => $especialidades,
            'especialidad' => isset($filtros['especialidad_id']) ? $especialidades->firstWhere('id', (int) $filtros['especialidad_id']) : null,
            'slots' => $slots,
            'hayHorario' => $hayHorario,
            'libres' => count(array_filter($slots, fn ($slot) => $slot['disponible'])),
        ]);
    }
}
