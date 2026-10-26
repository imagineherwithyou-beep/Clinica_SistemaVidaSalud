<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Http\Requests\Recepcion\PacienteRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pacientes = Paciente::query()
            ->when($request->filled('buscar'), function (Builder $query) use ($request) {
                $termino = trim($request->string('buscar')->toString());
                $query->where(fn (Builder $q) => $q->where('ci', 'like', "%{$termino}%")
                    ->orWhere('nombres', 'like', "%{$termino}%")
                    ->orWhere('apellidos', 'like', "%{$termino}%")
                    ->orWhere('codigo_paciente', 'like', "%{$termino}%"));
            })
            ->when(in_array($request->input('estado', '1'), ['0', '1'], true), fn (Builder $q) => $q->where('estado', (bool) $request->input('estado', '1')))
            ->orderBy('apellidos')->orderBy('nombres')->paginate(10)->withQueryString();

        return view('recepcion.pacientes.index', compact('pacientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('recepcion.pacientes.form', ['paciente' => new Paciente()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PacienteRequest $request): RedirectResponse
    {
        $data = $request->validated();
        try {
            $paciente = DB::transaction(function () use ($data) {
                $paciente = Paciente::create($data + ['codigo_paciente' => 'P-'.Str::upper(Str::random(17)), 'estado' => true]);
                $base = sprintf('P-%06d', $paciente->id);
                $codigo = $base;
                $sufijo = 1;
                while (Paciente::where('codigo_paciente', $codigo)->where('id', '!=', $paciente->id)->exists()) {
                    $codigo = $base.'-'.$sufijo++;
                }
                $paciente->update(['codigo_paciente' => $codigo]);
                return $paciente;
            });
        } catch (Throwable $error) {
            report($error);
            return back()->withInput()->with('error', 'No se pudo completar el registro del paciente. Revise los datos e inténtelo nuevamente.');
        }

        return redirect()->route('recepcion.pacientes.show', $paciente)->with('status', 'Paciente registrado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paciente $paciente): View
    {
        $citas = $paciente->citas()->with(['medico', 'especialidad'])
            ->orderByDesc('fecha_cita')->orderByDesc('hora_inicio')->paginate(8);
        return view('recepcion.pacientes.show', compact('paciente', 'citas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paciente $paciente): View
    {
        return view('recepcion.pacientes.form', compact('paciente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PacienteRequest $request, Paciente $paciente): RedirectResponse
    {
        $paciente->update($request->validated());
        return redirect()->route('recepcion.pacientes.show', $paciente)->with('status', 'Datos del paciente actualizados.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paciente $paciente): RedirectResponse
    {
        return $this->cambiarEstado($paciente);
    }

    public function cambiarEstado(Request $request, Paciente $paciente): RedirectResponse
    {
        $request->validate(['estado' => ['required', 'boolean']]);
        $nuevoEstado = $request->boolean('estado');
        if ($nuevoEstado === $paciente->estado) {
            return back()->with('status', $paciente->estado ? 'La ficha ya estaba activa.' : 'La ficha ya estaba inactiva.');
        }

        if (! $nuevoEstado) {
            $tieneCitasFuturas = $paciente->citas()->whereDate('fecha_cita', '>=', today())
                ->whereIn('estado', ['Programada', 'Confirmada'])->exists();

            if ($tieneCitasFuturas) {
                return back()->with('error', 'No se puede desactivar: el paciente tiene citas futuras programadas. Resuelva esas citas primero.');
            }
        }

        $paciente->update(['estado' => $nuevoEstado]);
        return back()->with('status', $nuevoEstado ? 'Paciente activado.' : 'Paciente desactivado. Su historial se conservó.');
    }
}
