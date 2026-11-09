<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#6a5492">
    <title>@yield('title') | Pastelería La María</title>
    <style>
        :root { color-scheme: light; font-family: Inter, system-ui, sans-serif; color: #342f38; background: #f8f5fa; }
        body { display: grid; min-height: 100vh; margin: 0; place-items: center; }
        main { width: min(440px, calc(100% - 40px)); padding: 40px 32px; border: 1px solid #eee8f0; border-radius: 18px; background: #fff; box-shadow: 0 14px 36px #342f3814; text-align: center; }
        .code { margin: 0; color: #6a5492; font-size: 14px; font-weight: 700; letter-spacing: .14em; }
        h1 { margin: 12px 0; color: #4d3b70; font-family: Georgia, serif; font-size: 30px; }
        .message { color: #77717d; line-height: 1.6; }
        a { display: inline-block; margin-top: 16px; padding: 11px 18px; border-radius: 9px; color: #fff; background: #6a5492; font-weight: 600; text-decoration: none; }
        a:hover { background: #4d3b70; }
    </style>
</head>
<body>
    <main>
        <p class="code">@yield('code')</p>
        <h1>@yield('title')</h1>
        <p class="message">@yield('message')</p>
        <a href="{{ route('catalogo') }}">Volver al catálogo</a>
    </main>
</body>
</html>
