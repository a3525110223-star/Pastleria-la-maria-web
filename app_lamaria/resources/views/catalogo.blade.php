<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#6a5492">
    <title>Catálogo | Pastelería La María</title>
    <link rel="stylesheet" href="{{ asset('css/catalogo.css') }}">
    <script src="{{ asset('js/empresa.js') }}" defer></script>
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
        data-api-empresa="{{ url('/api/empresa') }}"
        data-api-productos="{{ url('/api/productos') }}"
        data-api-categorias="{{ url('/api/categorias') }}"
        data-whatsapp="{{ $numeroWhatsapp ?? '' }}"
    >
        <section class="hero" aria-labelledby="catalog-title">
            <p class="eyebrow">Pastelería artesanal</p>
            <h1 id="catalog-title">Un detalle dulce para cada ocasión</h1>
            <p>Explora nuestro catálogo y encuentra tu próximo favorito.</p>
        </section>

        <section class="company-section" id="seccion-empresa" aria-labelledby="empresa-nombre">
            <p class="eyebrow">Conócenos</p>
            <h2 id="empresa-nombre">Cargando información...</h2>
            <p class="company-description" id="empresa-descripcion"></p>
            <div class="company-values">
                <article class="company-card">
                    <h3>Misión</h3>
                    <p id="empresa-mision">Cargando información...</p>
                </article>
                <article class="company-card">
                    <h3>Visión</h3>
                    <p id="empresa-vision">Cargando información...</p>
                </article>
            </div>
            <div class="company-contact">
                <p><strong>Teléfono:</strong> <span id="empresa-telefono">Cargando...</span></p>
                <p><strong>Dirección:</strong> <span id="empresa-direccion">Cargando...</span></p>
                <p><strong>Correo:</strong> <span id="empresa-email">Cargando...</span></p>
                <p><strong>Horario:</strong> <span id="empresa-horario">Cargando...</span></p>
            </div>
            <p class="catalog-status" id="estado-empresa" role="status" aria-live="polite"></p>
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
