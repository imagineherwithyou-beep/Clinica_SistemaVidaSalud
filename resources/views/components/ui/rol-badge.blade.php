@props(['rol'])

<span {{ $attributes->class(['badge', $rol->clase]) }}>{{ $rol->etiqueta }}</span>
