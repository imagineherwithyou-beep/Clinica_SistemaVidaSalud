@extends('layouts.app')
@section('title', 'Especialidades')
@section('content')
    <x-ui.page-header title="Especialidades" subtitle="Catálogo de especialidades que ofrece la clínica. El nombre no puede repetirse.">
        <x-slot:actions><a class="btn btn-primary" href="{{ route('admin.especialidades.create') }}"><x-ui.icon name="plus" :size="18" /> Nueva especialidad</a></x-slot:actions>
    </x-ui.page-header>
    <div class="stats user-stats">@foreach($estadisticas as $dato)<x-ui.stat :icon="$dato['icono']" :label="$dato['etiqueta']" :value="$dato['valor']" :tone="$dato['tono']" />@endforeach</div>
    <form method="GET" class="toolbar filter-card" role="search">
        <div class="field filter-search"><label for="buscar">Buscar</label><input id="buscar" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre de la especialidad"></div>
        <div class="field"><label for="estado">Estado</label><select id="estado" name="estado"><option value="">Todos</option><option value="1" @selected(request('estado') === '1')>Activas</option><option value="0" @selected(request('estado') === '0')>Inactivas</option></select></div>
        <button class="btn btn-secondary" type="submit">Buscar</button>
    </form>
    <div class="table-wrap"><table class="table"><thead><tr><th>Nombre</th><th>Descripción</th><th>Médicos</th><th>Estado</th><th><span class="sr-only">Acciones</span></th></tr></thead><tbody>
        @forelse($especialidades as $especialidad)
            <tr><td><strong>{{ $especialidad->nombre }}</strong></td><td>{{ $especialidad->descripcion ?: '—' }}</td><td>{{ $especialidad->medicos_count }}</td><td><x-ui.estado :activo="$especialidad->estado" /></td>
                <td><div class="table-actions"><a class="btn btn-secondary btn-sm" href="{{ route('admin.especialidades.edit', $especialidad) }}">Editar</a><form method="POST" action="{{ route('admin.especialidades.estado', $especialidad) }}" onsubmit="return confirm('{{ $especialidad->estado ? '¿Desactivar' : '¿Activar' }} esta especialidad?')">@csrf @method('PATCH')<button class="btn btn-sm {{ $especialidad->estado ? 'btn-danger' : 'btn-secondary' }}">{{ $especialidad->estado ? 'Desactivar' : 'Activar' }}</button></form></div></td>
            </tr>
        @empty <tr><td colspan="5">No se encontraron especialidades. Ajuste los filtros o cree una nueva.</td></tr> @endforelse
    </tbody></table></div>
    <x-ui.pager :paginator="$especialidades" />
    <p class="muted listing-note">Las especialidades vinculadas no se eliminan: al desactivarlas se conservan sus relaciones e historial.</p>
@endsection
