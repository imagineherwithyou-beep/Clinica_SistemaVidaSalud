@props(['tipo' => 'info'])

<div {{ $attributes->class(['alert', 'alert-'.$tipo]) }} role="{{ $tipo === 'error' ? 'alert' : 'status' }}">{{ $slot }}</div>
