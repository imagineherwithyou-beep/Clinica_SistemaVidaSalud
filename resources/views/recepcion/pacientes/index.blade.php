@extends('layouts.app')
@section('title', 'Pacientes')
@section('content')
    <x-ui.page-header title="Pacientes" subtitle="Busque por CI, nombre o apellido.">
        <x-slot:actions><a class="btn btn-primary" href="{{ route('recepcion.pacientes.create') }}"><x-ui.icon name="plus" :size="18" /> Registrar paciente</a></x-slot:actions>
    </x-ui.page-header>

    <form method="GET" class="toolbar filter-card" role="search">
        <div class="field filter-search"><label for="buscar">Buscar paciente</label><input id="buscar" name="buscar" value="{{ request('buscar') }}" placeholder="CI, nombre o apellido"></div>
        <div class="field"><label for="estado">Estado</label><select id="estado" name="estado"><option value="1" @selected(request('estado', '1') === '1')>Activos</option><option value="0" @selected(request('estado') === '0')>Inactivos</option><option value="todos" @selected(request('estado') === 'todos')>Todos</option></select></div>
        <button class="btn btn-secondary" type="submit">Buscar</button>
    </form>

    <div class="table-wrap"><table class="table patient-table">
        <thead><tr><th>Código</th><th>CI</th><th>Paciente</th><th>Teléfono</th><th>Estado</th><th><span class="sr-only">Acciones</span></th></tr></thead>
        <tbody>
        @forelse($pacientes as $paciente)
            <tr>
                <td><a href="{{ route('recepcion.pacientes.show', $paciente) }}">{{ $paciente->codigo_paciente }}</a></td>
                <td>{{ $paciente->ci }}</td>
                <td><strong>{{ $paciente->nombre_completo }}</strong><br><span class="muted">{{ $paciente->fecha_nacimiento->age }} años{{ $paciente->sexo ? ', '.['M' => 'M', 'F' => 'F', 'O' => 'Otro'][$paciente->sexo] : '' }}</span></td>
                <td>{{ $paciente->telefono }}</td>
                <td><x-ui.estado :activo="$paciente->estado" /></td>
                <td><div class="table-actions">
                    <a class="btn btn-secondary btn-sm" href="{{ route('recepcion.pacientes.show', $paciente) }}">Ver</a>
                    <a class="btn btn-secondary btn-sm" href="{{ route('recepcion.pacientes.edit', $paciente) }}">Editar</a>
                    <form method="POST" action="{{ route('recepcion.pacientes.estado', $paciente) }}" onsubmit="return confirm('{{ $paciente->estado ? '¿Desactivar' : '¿Reactivar' }} esta ficha?')">@csrf @method('PATCH')<input type="hidden" name="estado" value="{{ $paciente->estado ? 0 : 1 }}"><button class="btn btn-sm {{ $paciente->estado ? 'btn-danger' : 'btn-secondary' }}">{{ $paciente->estado ? 'Desactivar' : 'Activar' }}</button></form>
                </div></td>
            </tr>
        @empty <tr><td colspan="6">No se encontraron pacientes. Ajuste los criterios o registre una ficha nueva.</td></tr> @endforelse
        </tbody>
    </table></div>
    <x-ui.pager :paginator="$pacientes" />
@endsection
