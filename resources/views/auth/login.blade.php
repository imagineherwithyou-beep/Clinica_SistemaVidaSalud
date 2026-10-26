<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123b5b">
    <title>Iniciar sesión – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
<div class="login-screen">
    <aside class="login-art" aria-label="Información de la clínica">
        <a class="brand" href="{{ url('/') }}" aria-label="{{ config('app.name') }} - inicio">
            <span class="brand-mark"><x-ui.icon name="cross" :size="22" /></span>
            <span>{{ config('app.name') }}</span>
        </a>

        <div class="art-copy">
            <p class="eyebrow">Cuidado y gestión en un solo lugar</p>
            <h1>Tu tiempo importa.<br>Tu salud también.</h1>
            <p>Organiza la atención, acompaña a tus pacientes y coordina el trabajo de tu equipo desde un solo lugar.</p>
            <div class="mini-card">
                <span class="mini-icon" aria-hidden="true">✓</span>
                <span><strong>Todo bajo control</strong><small>Agenda y gestión clínica en un solo panel.</small></span>
            </div>
        </div>

        <footer class="art-foot">© {{ date('Y') }} {{ config('app.name') }} · Plataforma de gestión clínica</footer>
    </aside>

    <main class="login-panel">
        <section class="login-box" aria-labelledby="login-title">
            <a class="brand brand-login" href="{{ url('/') }}">
                <span class="brand-mark"><x-ui.icon name="cross" :size="22" /></span>
                <span>{{ config('app.name') }}</span>
            </a>

            <h2 id="login-title">Bienvenido de nuevo</h2>
            <p class="sub">Ingresa tus credenciales para acceder a tu espacio de trabajo.</p>

            @if (session('status'))
                <x-ui.alert tipo="ok">{{ session('status') }}</x-ui.alert>
            @endif

            @if ($errors->has('login'))
                <div class="login-error" role="alert">{{ $errors->first('login') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label for="login">Correo electrónico o usuario</label>
                    <input id="login" name="login" type="text" value="{{ old('login') }}" autocomplete="username" maxlength="255" autofocus required>
                </div>
                <div class="field">
                    <label for="password">Contraseña</label>
                    <div class="password-wrap">
                        <input id="password" name="password" type="password" autocomplete="current-password" required>
                        <button class="show-pass" type="button" aria-controls="password" aria-pressed="false">Mostrar</button>
                    </div>
                    @error('password')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn btn-primary btn-block login-submit">Iniciar sesión <span aria-hidden="true">→</span></button>
            </form>
            <p class="hint">Usa las credenciales asignadas por el administrador de tu clínica.</p>
        </section>
    </main>
</div>
<script>
    document.querySelector('.show-pass').addEventListener('click', function () {
        const input = document.getElementById(this.getAttribute('aria-controls'));
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        this.textContent = visible ? 'Ocultar' : 'Mostrar';
        this.setAttribute('aria-pressed', String(visible));
    });
</script>
</body>
</html>
