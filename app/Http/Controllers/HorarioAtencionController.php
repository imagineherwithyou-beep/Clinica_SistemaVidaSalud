<?php

namespace App\Http\Controllers;

use App\Models\HorarioAtencion;
use App\Models\Cita;
use App\Models\Medico;
use App\Http\Requests\Admin\HorarioAtencionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HorarioAtencionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $medicos = Medico::with('especialidades')->where('estado', true)->orderBy('apellidos')->orderBy('nombres')->get();
        $medico = $medicos->firstWhere('id', (int) $request->input('medico_id')) ?? $medicos->first();
        $horarios = $medico
            ? $medico->horarios()->orderBy('dia_semana')->orderBy('hora_inicio')->get()->groupBy('dia_semana')
            : collect();

        return view('admin.horarios.index', compact('medicos', 'medico', 'horarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.horarios.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HorarioAtencionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $inicio = $data['hora_inicio'].':00';
        $fin = $data['hora_fin'].':00';

        $resultado = DB::transaction(function () use ($data, $inicio, $fin) {
            $medico = Medico::whereKey($data['medico_id'])->lockForUpdate()->first();
            if (! $medico?->estado) {
                return 'inactivo';
            }
            $existentes = HorarioAtencion::where('medico_id', $data['medico_id'])
                ->where('dia_semana', $data['dia_semana'])->lockForUpdate()->get();
            if ($existentes->contains(fn (HorarioAtencion $horario) => $inicio < $horario->hora_fin && $fin > $horario->hora_inicio)) {
                return 'solapado';
            }
            HorarioAtencion::create($data);
            return null;
        });

        if ($resultado === 'inactivo') {
            return back()->withInput()->with('error', 'El médico fue desactivado. Solo se puede configurar disponibilidad para médicos activos.');
        }
        if ($resultado === 'solapado') {
            return back()->withInput()->with('error', 'El bloque se superpone con otro horario del mismo día.');
        }

        return redirect()->route('admin.horarios.index', ['medico_id' => $data['medico_id']])->with('status', 'Bloque de atención agregado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HorarioAtencion $horarioAtencion): RedirectResponse
    {
        return redirect()->route('admin.horarios.index', ['medico_id' => $horarioAtencion->medico_id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HorarioAtencion $horarioAtencion): RedirectResponse
    {
        return redirect()->route('admin.horarios.index', ['medico_id' => $horarioAtencion->medico_id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HorarioAtencion $horarioAtencion): RedirectResponse
    {
        return redirect()->route('admin.horarios.index', ['medico_id' => $horarioAtencion->medico_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HorarioAtencion $horarioAtencion): RedirectResponse
    {
        $citas = Cita::where('medico_id', $horarioAtencion->medico_id)
            ->whereDate('fecha_cita', '>=', today())->whereIn('estado', ['Programada', 'Confirmada'])->get();

        $conflicto = $citas->contains(function (Cita $cita) use ($horarioAtencion) {
            return (int) \Illuminate\Support\Carbon::parse($cita->fecha_cita)->dayOfWeekIso === (int) $horarioAtencion->dia_semana
                && $cita->hora_inicio < $horarioAtencion->hora_fin
                && $cita->hora_fin > $horarioAtencion->hora_inicio;
        });

        if ($conflicto) {
            return back()->with('error', 'No se puede quitar el bloque: hay citas futuras reservadas dentro de ese horario. Resuelva esas citas primero.');
        }

        $medicoId = $horarioAtencion->medico_id;
        $horarioAtencion->delete();
        return redirect()->route('admin.horarios.index', ['medico_id' => $medicoId])->with('status', 'Bloque eliminado.');
    }
}
