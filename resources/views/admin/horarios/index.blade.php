@extends('layouts.app')
@section('title', 'Horarios de atención')
@section('content')
    <x-ui.page-header title="Horarios de atención" subtitle="Defina los días, horas y la duración de cada cita por médico." />

    <form method="GET" action="{{ route('admin.horarios.index') }}" class="filter-card schedule-doctor-select">
        <div class="field"><label for="medico_id">Médico</label><select id="medico_id" name="medico_id" required>
            @forelse($medicos as $opcion)<option value="{{ $opcion->id }}" @selected($medico?->id === $opcion->id)>Dr. {{ $opcion->nombres }} {{ $opcion->apellidos }} ({{ $opcion->especialidades->pluck('nombre')->join(', ') }})</option>@empty<option value="">No hay médicos activos</option>@endforelse
        </select></div><button class="btn btn-secondary" type="submit" @disabled($medicos->isEmpty())>Consultar</button>
    </form>

    @if($medico)
        @php($dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'])
        <div class="schedule-layout">
            <section class="schedule-week-card"><h2>Semana tipo</h2>
                @foreach($dias as $numero => $dia)
                    <div class="schedule-day-row"><h3>{{ $dia }}</h3><div class="schedule-blocks">
                        @forelse($horarios->get($numero, collect()) as $horario)
                            @php($duracion = $horario->intervalo_minutos > 0 ? intdiv((int) ((strtotime($horario->hora_fin) - strtotime($horario->hora_inicio)) / 60), $horario->intervalo_minutos) : 0)
                            <div class="schedule-block"><span>{{ substr($horario->hora_inicio, 0, 5) }} a {{ substr($horario->hora_fin, 0, 5) }}, cada {{ $horario->intervalo_minutos }} min <small>({{ $duracion }} intervalos)</small></span>
                                <form method="POST" action="{{ route('admin.horarios.destroy', $horario) }}" onsubmit="return confirm('¿Quitar este bloque? Se conservará si contiene citas futuras.')">@csrf @method('DELETE')<button class="schedule-remove" aria-label="Quitar bloque del {{ strtolower($dia) }}">×</button></form>
                            </div>
                        @empty <span class="muted">Sin atención</span> @endforelse
                    </div><button class="btn btn-secondary btn-sm" type="button" onclick="document.getElementById('dia_semana').value='{{ $numero }}'; document.getElementById('hora_inicio').focus()">＋ Agregar</button></div>
                @endforeach
            </section>

            <section class="schedule-add-card"><h2>Agregar bloque</h2>
                <form method="POST" action="{{ route('admin.horarios.store') }}">
                    @csrf<input type="hidden" name="medico_id" value="{{ $medico->id }}">
                    <div class="field"><label for="dia_semana">Día</label><select id="dia_semana" name="dia_semana" required><option value="">Seleccione</option>@foreach($dias as $numero => $dia)<option value="{{ $numero }}" @selected(old('dia_semana') == $numero)>{{ $dia }}</option>@endforeach</select>@error('dia_semana')<span class="field-error">{{ $message }}</span>@enderror</div>
                    <div class="schedule-time-grid"><x-ui.field name="hora_inicio" type="time" label="Hora inicial" :value="old('hora_inicio', '08:00')" required /><x-ui.field name="hora_fin" type="time" label="Hora final" :value="old('hora_fin', '12:00')" required /></div>
                    <div class="field"><label for="intervalo_minutos">Duración de cada cita</label><select id="intervalo_minutos" name="intervalo_minutos" required>@foreach([15, 20, 30, 45, 60] as $minutos)<option value="{{ $minutos }}" @selected(old('intervalo_minutos', 30) == $minutos)>{{ $minutos }} minutos</option>@endforeach</select>@error('intervalo_minutos')<span class="field-error">{{ $message }}</span>@enderror</div>
                    <p class="schedule-help">La hora final debe ser posterior al inicio. No se permiten bloques solapados del mismo día. Los minutos sobrantes al final no generan una cita parcial.</p>
                    <button class="btn btn-primary" type="submit">Agregar bloque</button>
                </form>
            </section>
        </div>
    @else
        <div class="empty-state">Registre y active un médico antes de configurar horarios.</div>
    @endif
@endsection
