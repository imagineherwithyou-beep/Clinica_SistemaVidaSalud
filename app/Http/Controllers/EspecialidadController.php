<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\EspecialidadRequest;
use App\Models\Especialidad;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EspecialidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $especialidades = Especialidad::query()->withCount('medicos')
            ->when($request->filled('buscar'), fn (Builder $q) => $q->where('nombre', 'like', '%'.trim($request->input('buscar')).'%'))
            ->when(in_array($request->input('estado'), ['0', '1'], true), fn (Builder $q) => $q->where('estado', (bool) $request->input('estado')))
            ->orderBy('nombre')->paginate(10)->withQueryString();
        $total = Especialidad::count();
        $activas = Especialidad::where('estado', true)->count();
        return view('admin.especialidades.index', [
            'especialidades' => $especialidades,
            'estadisticas' => [
                ['etiqueta' => 'Registradas', 'valor' => $total, 'icono' => 'tag', 'tono' => 'neutral'],
                ['etiqueta' => 'Activas', 'valor' => $activas, 'icono' => 'activity', 'tono' => 'neutral'],
                ['etiqueta' => 'Inactivas', 'valor' => $total - $activas, 'icono' => 'clock', 'tono' => 'neutral'],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.especialidades.form', ['especialidad' => new Especialidad()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EspecialidadRequest $request): RedirectResponse
    {
        Especialidad::create($request->validated());
        return redirect()->route('admin.especialidades.index')->with('status', 'Especialidad registrada.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Especialidad $especialidad): RedirectResponse
    {
        return redirect()->route('admin.especialidades.edit', $especialidad);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Especialidad $especialidad): View
    {
        return view('admin.especialidades.form', compact('especialidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EspecialidadRequest $request, Especialidad $especialidad): RedirectResponse
    {
        $especialidad->update($request->validated());
        return redirect()->route('admin.especialidades.index')->with('status', 'Cambios de especialidad guardados.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Especialidad $especialidad): RedirectResponse
    {
        return $this->cambiarEstado($especialidad);
    }

    public function cambiarEstado(Especialidad $especialidad): RedirectResponse
    {
        $especialidad->update(['estado' => ! $especialidad->estado]);
        return back()->with('status', $especialidad->estado ? 'Especialidad activada.' : 'Especialidad desactivada.');
    }

}
