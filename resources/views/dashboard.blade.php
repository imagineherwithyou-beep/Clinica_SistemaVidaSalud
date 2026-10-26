@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <section class="dashboard-heading">
        <div>
            <p class="dashboard-kicker">{{ config('app.name') }} <span aria-hidden="true">/</span> Resumen</p>
            <h1>Buenos días, {{ $usuario->nombre_usuario }} <span aria-hidden="true">👋</span></h1>
            <p class="dashboard-intro">Aquí tienes el resumen de actividad de tu clínica.</p>
        </div>
        @if ($usuario->tieneRol(\App\Models\Role::RECEPCIONISTA))
            <button type="button" class="btn btn-primary appointment-button" data-feature="nueva-cita">
                <x-ui.icon name="plus" :size="18" /> Nueva cita
            </button>
        @endif
    </section>

    @if ($estadisticas)
        <div class="stats">
            @foreach ($estadisticas as $dato)
                <x-ui.stat :icon="$dato['icono']" :label="$dato['etiqueta']" :value="$dato['valor']" :tone="$dato['tono']" />
            @endforeach
        </div>
    @endif

    <div class="dashboard-section-heading">
        <div>
            <h2 class="section-title">Módulos</h2>
            <p class="dashboard-intro">Accede rápidamente a las herramientas de tu perfil.</p>
        </div>
    </div>

    <div class="module-grid">
        @forelse ($modulos as $modulo)
            @if ($modulo['ruta'])
                <a class="module" href="{{ route($modulo['ruta']) }}">
                    <span class="module-icon"><x-ui.icon :name="$modulo['icono']" :size="24" /></span>
                    <span class="module-title">{{ $modulo['titulo'] }}</span>
                    <span class="module-desc">{{ $modulo['descripcion'] }}</span>
                </a>
            @else
                <div class="module module-soon" aria-disabled="true">
                    <span class="module-icon"><x-ui.icon :name="$modulo['icono']" :size="24" /></span>
                    <span class="module-title">{{ $modulo['titulo'] }}</span>
                    <span class="module-desc">{{ $modulo['descripcion'] }}</span>
                    <span class="soon-tag">Pronto</span>
                </div>
            @endif
        @empty
            <p class="muted">Su rol todavía no tiene módulos asignados.</p>
        @endforelse
    </div>
@endsection
