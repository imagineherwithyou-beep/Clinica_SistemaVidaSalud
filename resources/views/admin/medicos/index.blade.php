@extends('layouts.app')

@section('title', 'Médicos')

@section('content')
    <x-ui.page-header title="Médicos" subtitle="Consulte y busque médicos por nombre, registro o especialidad.">
        <x-slot:actions>
            @if($puedeGestionar)
                <a class="btn btn-primary" href="{{ route('admin.medicos.create') }}"><x-ui.icon name="plus" :size="18" /> Registrar médico</a>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <form method="GET" class="toolbar filter-card" role="search">
        <div class="field filter-search"><label for="buscar">Buscar</label><input id="buscar" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre, apellido o registro"></div>
        <div class="field"><label for="especialidad">Especialidad</label><select id="especialidad" name="especialidad"><option value="">Todas</option>@foreach($especialidades as $id => $nombre)<option value="{{ $id }}" @selected((string) request('especialidad') === (string) $id)>{{ $nombre }}</option>@endforeach</select></div>
        <div class="field"><label for="estado">Estado</label><select id="estado" name="estado"><option value="">Todos</option><option value="1" @selected(request('estado') === '1')>Activos</option><option value="0" @selected(request('estado') === '0')>Inactivos</option></select></div>
        <button class="btn btn-secondary" type="submit">Buscar</button>
    </form>

    <div class="table-wrap"><table class="table">
        <thead><tr><th>Médico</th><th>Especialidades</th><th>Teléfono</th><th>Estado</th><th><span class="sr-only">Acciones</span></th></tr></thead>
        <tbody>
        @forelse($medicos as $medico)
            <tr>
                <td><strong>{{ $medico->nombres }} {{ $medico->apellidos }}</strong><br><span class="muted">Registro {{ $medico->registro_profesional }}</span></td>
                <td><div class="tag-list">@forelse($medico->especialidades as $especialidad)<span class="tag">{{ $especialidad->nombre }}</span>@empty<span class="muted">Sin especialidades activas</span>@endforelse</div></td>
                <td>{{ $medico->telefono }}</td>
                <td><x-ui.estado :activo="$medico->estado" /></td>
                <td><div class="table-actions">
                    <a class="btn btn-secondary btn-sm" href="{{ route($puedeGestionar ? 'admin.medicos.show' : 'recepcion.medicos.show', $medico) }}">Ver</a>
                    @if($puedeGestionar)
                    <a class="btn btn-secondary btn-sm" href="{{ route('admin.medicos.edit', $medico) }}">Editar</a>
                    <form method="POST" action="{{ route('admin.medicos.estado', $medico) }}" onsubmit="return confirm('¿Está seguro de cambiar el estado de este médico?')">
                    @csrf 
                    @method('PATCH')
                    <button class="btn btn-sm {{ $medico->estado ? 'btn-danger' : 'btn-secondary' }}">{{ $medico->estado ? 'Desactivar' : 'Activar' }}</button>
                </form>
                    @endif
                </div></td>
            </tr>
        @empty
            <tr><td colspan="5">No se encontraron médicos. Ajuste los filtros o registre uno nuevo.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <x-ui.pager :paginator="$medicos" />
    <p class="muted listing-note">Un médico inactivo no recibe nuevas citas; sus citas históricas se conservan.</p>
@endsection
