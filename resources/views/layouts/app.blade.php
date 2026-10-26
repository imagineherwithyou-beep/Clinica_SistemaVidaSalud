<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') – {{ config('app.name') }}</title>
    {{-- Restaura el estado del menú antes de pintar para evitar parpadeos --}}
    <script>try{if(localStorage.getItem('clinica.sidebar')==='1')document.documentElement.classList.add('sidebar-collapsed')}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/shell.js') }}" defer></script>
</head>
<body>
@php($actual = auth()->user())

<div class="app">
    @include('layouts.partials.sidebar')
    <div class="app-backdrop" data-sidebar-close></div>

    <div class="app-main">
        <header class="topbar">
            <button type="button" class="icon-btn" data-sidebar-toggle aria-controls="sidebar" aria-expanded="true" aria-label="Mostrar u ocultar el menú">
                <x-ui.icon name="menu" />
            </button>

            <div class="topbar-user">
                <span class="avatar {{ $actual->rol->clase }}" aria-hidden="true">{{ $actual->inicial() }}</span>
                <span class="topbar-user-name">{{ $actual->nombre_usuario }}</span>
                <x-ui.rol-badge :rol="$actual->rol" />
            </div>
        </header>

        <main class="content">
            @if (session('status'))
                <x-ui.alert tipo="ok">{{ session('status') }}</x-ui.alert>
            @endif
            @if (session('error'))
                <x-ui.alert tipo="error">{{ session('error') }}</x-ui.alert>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
