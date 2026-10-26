@extends('layouts.app')
@php($editando = $paciente->exists)
@section('title', $editando ? 'Editar paciente' : 'Registrar paciente')
@section('content')
    <x-ui.page-header :title="$editando ? 'Editar paciente' : 'Registrar paciente'" :subtitle="$editando ? 'Código '.$paciente->codigo_paciente.'. Editar usa este mismo formulario.' : 'El código se asigna al guardar. CI no puede repetirse.'" />

    <form method="POST" class="form-card patient-form" action="{{ $editando ? route('recepcion.pacientes.update', $paciente) : route('recepcion.pacientes.store') }}">
        @csrf @if($editando) @method('PUT') @endif
        <section class="form-section"><h2>Datos personales</h2><div class="form-grid">
            <x-ui.field name="ci" label="CI" :value="$paciente->ci" maxlength="30" required :hint="'No puede repetirse'" />
            <x-ui.field name="fecha_nacimiento" type="date" label="Fecha de nacimiento" :value="$paciente->fecha_nacimiento?->format('Y-m-d')" required />
            <x-ui.field name="nombres" label="Nombres" :value="$paciente->nombres" maxlength="100" required />
            <x-ui.field name="apellidos" label="Apellidos" :value="$paciente->apellidos" maxlength="100" required />
            <div class="field"><label for="sexo">Sexo</label><select name="sexo" id="sexo"><option value="">Seleccione</option><option value="F" @selected(old('sexo', $paciente->sexo) === 'F')>Femenino</option><option value="M" @selected(old('sexo', $paciente->sexo) === 'M')>Masculino</option><option value="O" @selected(old('sexo', $paciente->sexo) === 'O')>Otro</option></select>@error('sexo')<span class="field-error">{{ $message }}</span>@enderror</div>
        </div></section>

        <section class="form-section"><h2>Contacto</h2><div class="form-grid">
            <x-ui.field name="telefono" label="Teléfono" :value="$paciente->telefono" maxlength="20" required />
            <x-ui.field name="correo" type="email" label="Correo electrónico" :value="$paciente->correo" maxlength="100" />
            <div class="field field-full"><label for="direccion">Dirección</label><textarea id="direccion" name="direccion" rows="2" maxlength="2000">{{ old('direccion', $paciente->direccion) }}</textarea>@error('direccion')<span class="field-error">{{ $message }}</span>@enderror</div>
        </div></section>

        <section class="form-section"><h2>Información médica</h2><div class="form-grid">
            <div class="field field-full"><label for="tipo_sangre">Tipo de sangre</label><select id="tipo_sangre" name="tipo_sangre"><option value="">Seleccione</option>@foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)<option value="{{ $tipo }}" @selected(old('tipo_sangre', $paciente->tipo_sangre) === $tipo)>{{ $tipo }}</option>@endforeach</select>@error('tipo_sangre')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="field"><label for="alergias">Alergias</label><textarea id="alergias" name="alergias" rows="3" maxlength="5000">{{ old('alergias', $paciente->alergias) }}</textarea>@error('alergias')<span class="field-error">{{ $message }}</span>@enderror</div>
            <div class="field"><label for="enfermedades_cronicas">Enfermedades crónicas</label><textarea id="enfermedades_cronicas" name="enfermedades_cronicas" rows="3" maxlength="5000">{{ old('enfermedades_cronicas', $paciente->enfermedades_cronicas) }}</textarea>@error('enfermedades_cronicas')<span class="field-error">{{ $message }}</span>@enderror</div>
        </div></section>

        <section class="form-section"><h2>Contacto de emergencia</h2><div class="form-grid">
            <x-ui.field name="contacto_emergencia_nombre" label="Nombre" :value="$paciente->contacto_emergencia_nombre" maxlength="100" />
            <x-ui.field name="contacto_emergencia_telefono" label="Teléfono" :value="$paciente->contacto_emergencia_telefono" maxlength="20" />
        </div></section>

        <div class="form-actions"><button class="btn btn-primary" type="submit">{{ $editando ? 'Guardar cambios' : 'Registrar paciente' }}</button><a class="btn btn-secondary" href="{{ $editando ? route('recepcion.pacientes.show', $paciente) : route('recepcion.pacientes.index') }}">Cancelar</a></div>
    </form>
@endsection
