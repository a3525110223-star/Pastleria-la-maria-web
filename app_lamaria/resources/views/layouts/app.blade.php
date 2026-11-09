<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6A5492">
    <title>@yield('title', 'La María')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="app-shell">
        @include('layouts.navigation')
        <div class="app-main">
            <header class="topbar">
                <button
                    type="button"
                    class="sidebar_menu-toggle"
                    id="sidebar-menu-toggle"
                    aria-controls="sidebar"
                    aria-expanded="false"
                    aria-label="Abrir menú"
                >
                    ☰
                </button>
                <div>
                    <h1 class="topbar_title">
                        @yield('header', 'Panel administrativo')
                    </h1>
                    <p class="topbar_subtitle">
                        Pastelería La María
                    </p>
                </div>
            </header>
            <main class="app-content" id="contenido-principal">
                @yield('content')
            </main>
            <footer class="app-footer">
                Pastelería La María &copy; {{ date('Y') }}
            </footer>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/validaciones.js') }}" defer></script>
</body>
</html>
