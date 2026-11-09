<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Pastelería La María</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <main class="login-container">
        <section class="login-card">
            <div class="brand">
                <h1>Pastelería La María</h1>
                <p>Administración del sistema</p>
            </div>
            <h2>Iniciar sesión</h2>
            <p class="description">Ingresa tus credenciales para continuar.</p>

            @if ($errors->any())
                <div class="error-message" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="usuario@ejemplo.com" autocomplete="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" autocomplete="current-password" required>
                </div>
                <label class="remember">
                    <input type="checkbox" name="remember" value="1"> Mantener mi sesión iniciada
                </label>
                <button type="submit">Ingresar</button>
            </form>
            <footer>Sistema de gestión Pastelería La María</footer>
        </section>
    </main>
    <script src="{{ asset('js/validaciones.js') }}" defer></script>
</body>
</html>
