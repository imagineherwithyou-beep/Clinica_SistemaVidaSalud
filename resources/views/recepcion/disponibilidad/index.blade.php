@extends('layouts.app')
@section('title', 'Disponibilidad')
@section('content')
    <x-ui.page-header title="Disponibilidad" subtitle="Horarios libres por médico y fecha. Las citas canceladas liberan el horario." />

    <form method="GET" action="{{ route('recepcion.disponibilidad.index') }}" class="toolbar filter-card availability-filters">
        <div class="field"><label for="especialidad_id">Especialidad</label><select id="especialidad_id" name="especialidad_id"><option value="">Todas</option>@foreach($especialidades as $opcion)<option value="{{ $opcion->id }}" @selected((string) request('especialidad_id') === (string) $opcion->id)>{{ $opcion->nombre }}</option>@endforeach</select></div>
        <div class="field"><label for="medico_id">Médico</label><select id="medico_id" name="medico_id"><option value="">Seleccione</option>@foreach($medicos as $opcion)<option value="{{ $opcion->id }}" @selected($medico?->id === $opcion->id)>Dr. {{ $opcion->nombres }} {{ $opcion->apellidos }}</option>@endforeach</select></div>
        <div class="field"><label for="fecha">Fecha</label><input type="date" id="fecha" name="fecha" value="{{ $fecha->format('Y-m-d') }}" min="{{ today()->format('Y-m-d') }}" required></div>
        <button type="submit" class="btn btn-secondary">Consultar</button>
    </form>

    @if($medico)
        @php($dia = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'][$fecha->dayOfWeekIso])
        @php($especialidadResumen = $especialidad?->nombre ?? $medico->especialidades->first()?->nombre ?? 'Sin especialidad activa')
        <div class="availability-layout">
            <section class="availability-slots-card">
                <h2>{{ $fecha->locale('es')->translatedFormat('F \d\e Y') }}</h2>
                <div class="selected-day"><span>{{ substr($dia, 0, 3) }}</span><strong>{{ $fecha->format('d') }}</strong></div>
                @if($slots)
                    <div class="availability-slot-grid">
                        @foreach($slots as $slot)
                            <span @class(['availability-slot', 'is-free' => $slot['disponible'], 'is-busy' => ! $slot['disponible']]) aria-label="{{ $slot['inicio'] }} {{ $slot['disponible'] ? 'libre' : 'ocupado' }}">{{ $slot['inicio'] }}</span>
                        @endforeach
                    </div>
                    <div class="availability-legend"><span><i class="legend-free"></i> Libre</span><span><i class="legend-busy"></i> Ocupado</span></div>
                    @if($libres === 0)<p class="availability-message">Todos los intervalos están ocupados o ya pasaron.</p>@endif
                @elseif($hayHorario)
                    <p class="availability-message">No quedan intervalos libres para esta fecha; los horarios pasados u ocupados no se ofrecen.</p>
                @else
                    <p class="availability-message">No hay horario de atención configurado para {{ strtolower($dia) }}.</p>
                @endif
            </section>

            <aside class="availability-summary"><h2>Resumen</h2><dl>
                <dt>Médico</dt><dd>Dr. {{ $medico->nombres }} {{ $medico->apellidos }}</dd>
                <dt>Especialidad</dt><dd>{{ $especialidadResumen }}</dd>
                <dt>Día</dt><dd>{{ $dia }} {{ $fecha->format('d') }} de {{ $fecha->locale('es')->translatedFormat('F') }}</dd>
                @if($slots)<dt>Atención</dt><dd>{{ $slots[0]['inicio'] }} a {{ $slots[count($slots) - 1]['fin'] }}</dd><dt>Libres</dt><dd>{{ $libres }} de {{ count($slots) }}</dd>@endif
            </dl><p class="schedule-help">La consulta no reserva el horario. Al crear una cita, el sistema debe comprobar nuevamente que siga libre.</p></aside>
        </div>
    @elseif($medicos->isEmpty())
        <div class="empty-state">No hay médicos activos que coincidan con los filtros.</div>
    @elseif($especialidades->isEmpty())
        <div class="empty-state">No hay médicos activos con especialidades disponibles.</div>
    @else
        <div class="empty-state">Seleccione un médico y consulte una fecha.</div>
    @endif
@endsection
