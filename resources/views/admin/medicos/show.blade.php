@extends('layouts.app')
@section('title', 'Detalle del médico')
@section('content')
    <x-ui.page-header title="{{ $medico->nombres }} {{ $medico->apellidos }}" subtitle="Ficha profesional del médico.">
        <x-slot:actions>
            @if($puedeGestionar)
                <a class="btn btn-primary" href="{{ route('admin.medicos.edit', $medico) }}">Editar</a>
            @endif
        </x-slot:actions>
    </x-ui.page-header>
    <div class="form-card detail-card"><dl class="detail-grid">
        <div><dt>Registro profesional</dt><dd>{{ $medico->registro_profesional }}</dd></div><div><dt>Estado</dt><dd><x-ui.estado :activo="$medico->estado" /></dd></div>
        <div><dt>Teléfono</dt><dd>{{ $medico->telefono }}</dd></div><div><dt>Correo</dt><dd>{{ $medico->correo ?: 'No registrado' }}</dd></div>
        <div class="field-full"><dt>Especialidades</dt><dd><div class="tag-list">@forelse($medico->especialidades as $especialidad)<span class="tag">{{ $especialidad->nombre }}</span>@empty<span class="muted">Sin especialidades</span>@endforelse</div></dd></div>
        @if($puedeGestionar)<div class="field-full"><dt>Cuenta vinculada</dt><dd>{{ $medico->usuario?->nombre_usuario ?? 'Sin cuenta vinculada' }}</dd></div>@endif
    </dl><a class="btn btn-secondary" href="{{ route($rutaListado) }}">Volver al listado</a></div>
@endsection
