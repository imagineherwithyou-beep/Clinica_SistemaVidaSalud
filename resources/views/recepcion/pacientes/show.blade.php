@extends('layouts.app')
@section('title', 'Ficha del paciente')
@section('content')
    <x-ui.page-header :title="$paciente->nombre_completo" subtitle="Ficha del paciente. Desactivar conserva todo su historial.">
        <x-slot:actions><a class="btn btn-secondary" href="{{ route('recepcion.pacientes.edit', $paciente) }}">Editar</a>
            <form method="POST" action="{{ route('recepcion.pacientes.estado', $paciente) }}" onsubmit="return confirm('{{ $paciente->estado ? '¿Desactivar' : '¿Reactivar' }} esta ficha?')">@csrf @method('PATCH')<input type="hidden" name="estado" value="{{ $paciente->estado ? 0 : 1 }}"><button class="btn {{ $paciente->estado ? 'btn-danger' : 'btn-secondary' }}">{{ $paciente->estado ? 'Desactivar' : 'Activar' }}</button></form>
        </x-slot:actions>
    </x-ui.page-header>

    <section class="patient-profile">
        <div class="patient-identity"><span class="patient-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($paciente->nombres, 0, 1).mb_substr($paciente->apellidos, 0, 1)) }}</span><div><h2>{{ $paciente->nombre_completo }}</h2><p>Código {{ $paciente->codigo_paciente }} <span aria-hidden="true">·</span> CI {{ $paciente->ci }}</p></div><x-ui.estado :activo="$paciente->estado" /></div>
        <div class="patient-detail-grid">
            <section class="patient-info-card"><h2>Datos del paciente</h2><dl>
                <dt>Nacimiento</dt><dd>{{ $paciente->fecha_nacimiento->format('d/m/Y') }} ({{ $paciente->fecha_nacimiento->age }} años)</dd>
                <dt>Sexo</dt><dd>{{ ['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'][$paciente->sexo] ?? 'No especificado' }}</dd>
                <dt>Teléfono</dt><dd>{{ $paciente->telefono }}</dd>
                <dt>Correo</dt><dd>{{ $paciente->correo ?: 'No registrado' }}</dd>
                <dt>Dirección</dt><dd>{{ $paciente->direccion ?: 'No registrada' }}</dd>
                <dt>Tipo de sangre</dt><dd>{{ $paciente->tipo_sangre ?: 'No registrado' }}</dd>
                <dt>Alergias</dt><dd>{{ $paciente->alergias ?: 'No registradas' }}</dd>
                <dt>Enfermedades crónicas</dt><dd>{{ $paciente->enfermedades_cronicas ?: 'No registradas' }}</dd>
                <dt>Emergencia</dt><dd>{{ $paciente->contacto_emergencia_nombre ?: 'No registrado' }}@if($paciente->contacto_emergencia_telefono) <br>{{ $paciente->contacto_emergencia_telefono }}@endif</dd>
            </dl></section>

            <section class="patient-history-card"><h2>Historial de citas</h2><div class="table-wrap"><table class="table patient-history-table"><thead><tr><th>Fecha</th><th>Horario</th><th>Médico</th><th>Especialidad</th><th>Estado</th></tr></thead><tbody>
                @forelse($citas as $cita)
                    <tr><td>{{ \Illuminate\Support\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</td><td>{{ substr($cita->hora_inicio, 0, 5) }} a {{ substr($cita->hora_fin, 0, 5) }}</td><td>{{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}</td><td>{{ $cita->especialidad?->nombre }}</td><td><span class="appointment-status status-{{ \Illuminate\Support\Str::slug($cita->estado) }}">{{ $cita->estado }}</span></td></tr>
                @empty <tr><td colspan="5">Este paciente aún no tiene citas registradas.</td></tr> @endforelse
            </tbody></table></div><x-ui.pager :paginator="$citas" /></section>
        </div>
    </section>
    <a class="btn btn-secondary" href="{{ route('recepcion.pacientes.index') }}">Volver al listado</a>
@endsection
