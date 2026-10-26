@props(['activo'])

<span @class(['status', 'status-on' => $activo])>{{ $activo ? 'Activo' : 'Inactivo' }}</span>
