{{-- Menú lateral: los módulos salen de config/menu.php según el rol del usuario --}}
<aside id="sidebar" class="sidebar" aria-label="Menú principal">
    <a class="brand" href="{{ route('dashboard') }}">
        <span class="brand-mark"><x-ui.icon name="cross" :size="22" /></span>
        <span class="brand-text">{{ config('app.name') }}</span>
    </a>

    <nav class="nav">
        <a href="{{ route('dashboard') }}" title="Inicio" @class(['nav-item', 'is-active' => request()->routeIs('dashboard')])>
            <x-ui.icon name="home" />
            <span class="nav-text">Inicio</span>
        </a>

        @foreach ($actual->modulos()->groupBy('grupo') as $grupo => $items)
            <div class="nav-group">
                <p class="nav-group-title">{{ $grupo }}</p>

                @foreach ($items as $modulo)
                    @if ($modulo['ruta'])
                        <a href="{{ route($modulo['ruta']) }}" title="{{ $modulo['titulo'] }}"
                           @class(['nav-item', 'is-active' => request()->routeIs($modulo['patron'] ?? $modulo['ruta'])])>
                            <x-ui.icon :name="$modulo['icono']" />
                            <span class="nav-text">{{ $modulo['titulo'] }}</span>
                        </a>
                    @else
                        <span class="nav-item is-disabled" title="{{ $modulo['titulo'] }} (próximamente)" aria-disabled="true">
                            <x-ui.icon :name="$modulo['icono']" />
                            <span class="nav-text">{{ $modulo['titulo'] }}</span>
                            <span class="nav-soon">Pronto</span>
                        </span>
                    @endif
                @endforeach
            </div>
        @endforeach
    </nav>

    {{-- CU02: el cierre de sesión es POST con token CSRF --}}
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" title="Cerrar sesión">
                <x-ui.icon name="log-out" />
                <span class="nav-text">Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>
