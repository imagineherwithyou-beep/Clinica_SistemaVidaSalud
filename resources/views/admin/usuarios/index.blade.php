@extends('layouts.app')

@section('title', 'Usuarios y roles')

@section('content')
    <x-ui.page-header title="Usuarios y roles" subtitle="El rol de cada cuenta define a qué módulos puede entrar.">
        <x-slot:actions>
            <a class="btn btn-primary" href="{{ route('admin.usuarios.create') }}">
                <x-ui.icon name="plus" :size="18" /> Registrar usuario
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="stats user-stats">
        @foreach ($estadisticas as $dato)
            <x-ui.stat :icon="$dato['icono']" :label="$dato['etiqueta']" :value="$dato['valor']" :tone="$dato['tono']" />
        @endforeach
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Usuario</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Estado</th>
                    <th scope="col"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($usuarios as $fila)
                @php($esMismo = $fila->is(auth()->user()))
                <tr>
                    <td>
                        {{ $fila->nombre_usuario }}
                        @if ($esMismo)
                            <span class="muted">(usted)</span>
                        @endif
                    </td>
                    <td>{{ $fila->email }}</td>
                    <td><x-ui.rol-badge :rol="$fila->rol" /></td>
                    <td><x-ui.estado :activo="$fila->estado" /></td>
                    <td>
                        <div class="table-actions">
                            <a class="btn btn-secondary btn-sm" href="{{ route('admin.usuarios.edit', $fila) }}">Editar</a>

                            {{-- Nadie puede desactivar su propia cuenta --}}
                            @unless ($esMismo)
                                <form method="POST" action="{{ route('admin.usuarios.estado', $fila) }}"
                                      onsubmit="return confirm('{{ $fila->estado ? '¿Desactivar' : '¿Activar' }} esta cuenta?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" @class(['btn', 'btn-sm', 'btn-danger' => $fila->estado, 'btn-secondary' => ! $fila->estado])>
                                        {{ $fila->estado ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            @endunless
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Aún no hay usuarios registrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <x-ui.pager :paginator="$usuarios" />
@endsection
