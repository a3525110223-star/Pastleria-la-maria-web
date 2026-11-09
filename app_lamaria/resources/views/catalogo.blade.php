<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#6a5492">
    <title>Catálogo | Pastelería La María</title>
    <link rel="stylesheet" href="{{ asset('css/catalogo.css') }}">
    <script src="{{ asset('js/catalogo.js') }}" defer></script>
</head>
<body>
    <header class="site-header">
        <a class="brand" href="{{ route('catalogo') }}" aria-label="Pastelería La María, inicio">
            <span class="brand-mark" aria-hidden="true">LM</span>
            <span>
                <strong>Pastelería La María</strong>
                <small>Hecho con cariño</small>
            </span>
        </a>
        <a class="admin-link" href="{{ route('login') }}">Acceso administrativo</a>
    </header>

    <main
        class="catalog-page"
        id="catalogo-app"
        data-api-productos="{{ url('/api/productos') }}"
        data-api-categorias="{{ url('/api/categorias') }}"
        data-whatsapp="{{ $numeroWhatsapp ?? '' }}"
    >
        <section class="hero" aria-labelledby="catalog-title">
            <p class="eyebrow">Pastelería artesanal</p>
            <h1 id="catalog-title">Un detalle dulce para cada ocasión</h1>
            <p>Explora nuestro catálogo y encuentra tu próximo favorito.</p>
        </section>

        <section class="catalog-section" aria-labelledby="products-title">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Preparado para ti</p>
                    <h2 id="products-title">Nuestro catálogo</h2>
                </div>
                <button class="refresh-button" id="recargar-catalogo" type="button">
                    Actualizar catálogo
                </button>
            </div>

            <nav class="filters" id="filtros" aria-label="Filtrar por categoría">
                <button class="filter-button is-active" type="button" data-filtro="todos" aria-pressed="true">
                    Todos
                </button>
            </nav>

            <p class="catalog-status" id="estado-filtros" role="status" aria-live="polite"></p>
            <p class="catalog-status" id="estado-catalogo" role="status" aria-live="polite">
                Cargando productos...
            </p>
            <div class="product-grid" id="catalogo" aria-live="polite"></div>
        </section>
    </main>

    <footer class="site-footer">
        <p>Pastelería La María · Elaborado con cariño para compartir.</p>
    </footer>
</body>
</html>
