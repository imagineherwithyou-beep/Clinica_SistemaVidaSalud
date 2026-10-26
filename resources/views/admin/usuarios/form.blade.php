@extends('layouts.app')

@php
    $editando = $usuario->exists;
    $esMismo = $editando && $usuario->is(auth()->user());
    $ayudaClave = ($editando ? 'Déjela en blanco para conservar la actual. ' : '')
        .'Mínimo 8 caracteres, con mayúsculas, minúsculas y números.';
@endphp

@section('title', $editando ? 'Editar usuario' : 'Registrar usuario')

@section('content')
    <x-ui.page-header
        :title="$editando ? 'Editar usuario' : 'Registrar usuario'"
        subtitle="Los datos de acceso y el rol que define sus permisos." />

    <form method="POST" class="form-card"
          action="{{ $editando ? route('admin.usuarios.update', $usuario) : route('admin.usuarios.store') }}">
        @csrf
        @if ($editando)
            @method('PUT')
        @endif

        <div class="form-grid">
            <x-ui.field name="nombre_usuario" label="Nombre de usuario" :value="$usuario->nombre_usuario"
                        maxlength="100" autocomplete="off" required />

            <x-ui.select name="rol_id" label="Rol" :options="$roles" :value="$usuario->rol_id"
                         :disabled="$esMismo" :required="! $esMismo"
                         :hint="$esMismo ? 'No puede cambiar su propio rol.' : null" />

            <x-ui.field name="email" type="email" label="Correo electrónico" :value="$usuario->email"
                        maxlength="255" autocomplete="off" required wide />

            <x-ui.field name="password" type="password" :label="$editando ? 'Nueva contraseña' : 'Contraseña'"
                        :hint="$ayudaClave" autocomplete="new-password" :required="! $editando" />

            <x-ui.field name="password_confirmation" type="password" label="Confirmar contraseña"
                        autocomplete="new-password" :required="! $editando" />

            @if ($esMismo)
                {{-- Un campo deshabilitado no se envía; el controlador igual ignora cualquier cambio de rol propio --}}
                <input type="hidden" name="rol_id" value="{{ $usuario->rol_id }}">
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Registrar usuario' }}</button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@endsection
