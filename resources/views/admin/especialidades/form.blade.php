@extends('layouts.app')
@php($editando = $especialidad->exists)
@section('title', $editando ? 'Editar especialidad' : 'Nueva especialidad')
@section('content')
    <x-ui.page-header :title="$editando ? 'Editar especialidad' : 'Nueva especialidad'" subtitle="Los nombres se comparan sin distinguir mayúsculas y minúsculas." />
    <form method="POST" class="form-card" action="{{ $editando ? route('admin.especialidades.update', $especialidad) : route('admin.especialidades.store') }}">
        @csrf @if($editando) @method('PUT') @endif
        <div class="form-grid">
            <x-ui.field name="nombre" label="Nombre" :value="$especialidad->nombre" maxlength="100" required wide />
            <div class="field field-full"><label for="descripcion">Descripción (opcional)</label><textarea id="descripcion" name="descripcion" rows="4" maxlength="2000">{{ old('descripcion', $especialidad->descripcion) }}</textarea>@error('descripcion')<span class="field-error">{{ $message }}</span>@enderror</div>
            @if($editando)<div class="field"><label for="estado">Estado</label><select id="estado" name="estado"><option value="1" @selected((string) old('estado', (int) $especialidad->estado) === '1')>Activa</option><option value="0" @selected((string) old('estado', (int) $especialidad->estado) === '0')>Inactiva</option></select></div>@else<input type="hidden" name="estado" value="1">@endif
        </div>
        <div class="form-actions"><button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Registrar especialidad' }}</button><a href="{{ route('admin.especialidades.index') }}" class="btn btn-secondary">Cancelar</a></div>
    </form>
@endsection
