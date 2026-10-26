<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MedicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $esAdministrador = $request->user()->tieneRol(Role::ADMINISTRADOR);
        $medicos = Medico::query()->with('especialidades')->withCount('citas')
            ->when(! $esAdministrador, fn (Builder $query) => $query->where('estado', true))
            ->when($request->filled('buscar'), function (Builder $query) use ($request) {
                $termino = trim($request->string('buscar')->toString());
                $query->where(fn (Builder $q) => $q->where('nombres', 'like', "%{$termino}%")
                    ->orWhere('apellidos', 'like', "%{$termino}%")
                    ->orWhere('registro_profesional', 'like', "%{$termino}%"));
            })
            ->when($request->filled('especialidad'), fn (Builder $q) => $q->whereHas('especialidades', fn (Builder $e) => $e->whereKey($request->input('especialidad'))))
            ->when(in_array($request->input('estado'), ['0', '1'], true), fn (Builder $q) => $q->where('estado', (bool) $request->input('estado')))
            ->orderBy('apellidos')->orderBy('nombres')->paginate(10)->withQueryString();

        return view('admin.medicos.index', [
            'medicos' => $medicos,
            'puedeGestionar' => $esAdministrador,
            'especialidades' => Especialidad::where('estado', true)->orderBy('nombre')->pluck('nombre', 'id'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.medicos.form', [
            'medico' => new Medico(),
            'especialidades' => Especialidad::where('estado', true)->orderBy('nombre')->get(),
            'usuarios' => $this->usuariosDisponibles(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\Admin\MedicoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $ids = $data['especialidades'] ?? [];
        unset($data['especialidades']);

        DB::transaction(function () use ($data, $ids) {
            $medico = Medico::create($data + ['estado' => true]);
            $medico->especialidades()->sync($ids);
        });

        return redirect()->route('admin.medicos.index')->with('status', 'Médico registrado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Medico $medico): View
    {
        $esAdministrador = $request->user()->tieneRol(Role::ADMINISTRADOR);
        abort_unless($esAdministrador || $medico->estado, 404);
        return view('admin.medicos.show', [
            'medico' => $medico->load('especialidades', 'usuario'),
            'puedeGestionar' => $esAdministrador,
            'rutaListado' => $esAdministrador ? 'admin.medicos.index' : 'recepcion.medicos.index',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medico $medico): View
    {
        return view('admin.medicos.form', [
            'medico' => $medico->load('especialidades'),
            'especialidades' => Especialidad::where('estado', true)->orderBy('nombre')->get(),
            'usuarios' => $this->usuariosDisponibles($medico),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\Admin\MedicoRequest $request, Medico $medico): RedirectResponse
    {
        $data = $request->validated();
        $ids = $data['especialidades'] ?? [];
        unset($data['especialidades']);

        if ($medico->estado && array_key_exists('estado', $data) && ! $data['estado'] && $medico->citas()
            ->whereDate('fecha_cita', '>=', today())->whereIn('estado', ['Programada', 'Confirmada'])->exists()) {
            return back()->withInput()->with('error', 'No se puede desactivar: el médico tiene citas futuras programadas. Resuelva esas citas primero.');
        }

        $especialidadesInactivas = $medico->especialidades()->where('estado', false)->pluck('especialidades.id')->all();
        DB::transaction(function () use ($medico, $data, $ids, $especialidadesInactivas) {
            $medico->update($data);
            $medico->especialidades()->sync(array_unique([...$ids, ...$especialidadesInactivas]));
        });

        return redirect()->route('admin.medicos.index')->with('status', 'Cambios del médico guardados.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medico $medico): RedirectResponse
    {
        return $this->cambiarEstado($medico);
    }

    public function cambiarEstado(Medico $medico): RedirectResponse
    {
        if ($medico->estado && $medico->citas()->whereDate('fecha_cita', '>=', today())
            ->whereIn('estado', ['Programada', 'Confirmada'])->exists()) {
            return back()->with('error', 'No se puede desactivar: el médico tiene citas futuras programadas. Resuelva esas citas primero.');
        }

        $medico->update(['estado' => ! $medico->estado]);
        return back()->with('status', $medico->estado ? 'Médico activado.' : 'Médico desactivado.');
    }

    private function usuariosDisponibles(?Medico $medico = null)
    {
        $roleId = Role::where('nombre_rol', Role::MEDICO)->value('id');

        return User::where('estado', true)->where('rol_id', $roleId)
            ->where(fn ($q) => $q->whereDoesntHave('medico')->orWhere('id', $medico?->user_id ?? 0))
            ->orderBy('nombre_usuario')->get();
    }
}
