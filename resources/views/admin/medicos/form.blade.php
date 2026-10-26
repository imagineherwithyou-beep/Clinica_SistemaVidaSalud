@extends('layouts.app')

@php($editando = $medico->exists)
@section('title', $editando ? 'Editar médico' : 'Registrar médico')

@section('content')
    <x-ui.page-header :title="$editando ? 'Editar médico' : 'Registrar médico'" subtitle="Los mismos campos sirven para editar y para activar o desactivar el perfil." />
    <form method="POST" class="form-card" action="{{ $editando ? route('admin.medicos.update', $medico) : route('admin.medicos.store') }}">
        @csrf @if($editando) @method('PUT') @endif
        <section class="form-section"><h2>Datos profesionales</h2><div class="form-grid">
            <x-ui.field name="registro_profesional" label="Registro profesional" :value="$medico->registro_profesional" maxlength="50" placeholder="Ej. 12345" required />
            <x-ui.field name="telefono" label="Teléfono" :value="$medico->telefono" maxlength="20" required />
            <x-ui.field name="nombres" label="Nombres" :value="$medico->nombres" maxlength="100" required />
            <x-ui.field name="apellidos" label="Apellidos" :value="$medico->apellidos" maxlength="100" required />
            <x-ui.field name="correo" type="email" label="Correo electrónico" :value="$medico->correo" maxlength="100" wide />
        </div></section>

        <section class="form-section"><fieldset class="specialty-fieldset"><legend>Especialidades <span aria-hidden="true">*</span></legend>
            <div class="specialty-options">
            @foreach($especialidades as $especialidad)
                <label class="specialty-option"><input type="checkbox" name="especialidades[]" value="{{ $especialidad->id }}" @checked(in_array($especialidad->id, old('especialidades', $medico->especialidades->pluck('id')->all())))><span>{{ $especialidad->nombre }}</span></label>
            @endforeach
            @if($editando)
                @foreach($medico->especialidades->where('estado', false) as $especialidad)
                    <span class="specialty-option specialty-legacy">{{ $especialidad->nombre }} <small>(inactiva; relación conservada)</small></span>
                @endforeach
            @endif
            </div>
            <p class="field-hint">Seleccione al menos una. Solo aparecen especialidades activas.</p>
            @error('especialidades')<span class="field-error">{{ $message }}</span>@enderror
            @error('especialidades.*')<span class="field-error">{{ $message }}</span>@enderror
        </fieldset></section>

        <section class="form-section"><h2>Cuenta de acceso (opcional)</h2>
            <x-ui.select name="user_id" label="Usuario con rol Médico" :options="$usuarios->mapWithKeys(fn($usuario) => [$usuario->id => $usuario->nombre_usuario.' ('.$usuario->email.')'])" :value="$medico->user_id" placeholder="Sin cuenta vinculada" hint="Las cuentas y sus roles se crean en Usuarios y roles." />
            @error('user_id')<span class="field-error">{{ $message }}</span>@enderror
        </section>

        @if($editando)
            <div class="field"><label for="estado">Estado del perfil</label><select name="estado" id="estado"><option value="1" @selected((string) old('estado', (int) $medico->estado) === '1')>Activo</option><option value="0" @selected((string) old('estado', (int) $medico->estado) === '0')>Inactivo</option></select><span class="field-hint">Al desactivar se bloquean nuevas citas. Primero resuelva cualquier cita futura.</span></div>
        @endif
        <div class="form-actions"><button class="btn btn-primary" type="submit">{{ $editando ? 'Guardar cambios' : 'Registrar médico' }}</button><a class="btn btn-secondary" href="{{ route('admin.medicos.index') }}">Cancelar</a></div>
    </form>
@endsection
