document.addEventListener('DOMContentLoaded', () => {
    const app = document.getElementById('catalogo-app');
    const catalogo = document.getElementById('catalogo');
    const filtros = document.getElementById('filtros');
    const estadoFiltros = document.getElementById('estado-filtros');
    const estado = document.getElementById('estado-catalogo');
    const recargar = document.getElementById('recargar-catalogo');

    if (!app || !catalogo || !filtros || !estadoFiltros || !estado) {
        return;
    }

    const API_PRODUCTOS = app.dataset.apiProductos;
    const API_CATEGORIAS = app.dataset.apiCategorias;
    const numeroWhatsapp = (app.dataset.whatsapp || '').replace(/\D/g, '');
    const moneda = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    });

    function normalizarCategoria(categoria) {
        return categoria
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim()
            .replace(/\s+/g, '-');
    }

    function crearAccionWhatsapp(producto, tipo) {
        const accion = document.createElement('a');
        const esCotizacion = tipo === 'cotizacion';
        const mensaje = esCotizacion
            ? `Hola, me gustaría solicitar una cotización para ${producto.nombre}.`
            : `Hola, me interesa el producto ${producto.nombre}.`;

        accion.className = esCotizacion
            ? 'product-action'
            : 'product-action product-action--whatsapp';
        accion.textContent = esCotizacion ? 'Solicitar cotización' : 'WhatsApp';
        accion.setAttribute('aria-label', `${accion.textContent}: ${producto.nombre}`);
        accion.target = '_blank';
        accion.rel = 'noopener noreferrer';

        const url = new URL(`https://wa.me/${numeroWhatsapp}`);
        url.searchParams.set('text', mensaje);
        accion.href = url.toString();

        return accion;
    }

    function mostrarProductos(productos) {
        catalogo.replaceChildren();

        if (!productos.length) {
            estado.textContent = 'No hay productos disponibles en este momento.';
            return;
        }

        estado.textContent = '';

        productos.forEach((producto) => {
            const tarjeta = document.createElement('article');
            tarjeta.className = 'product-card';
            tarjeta.dataset.categoria = normalizarCategoria(producto.categoria);

            if (producto.imagen) {
                const imagen = document.createElement('img');
                imagen.className = 'product-image';
                imagen.src = producto.imagen;
                imagen.alt = producto.nombre;
                imagen.loading = 'lazy';
                imagen.addEventListener('error', () => {
                    const reemplazo = document.createElement('div');
                    reemplazo.className = 'image-placeholder';
                    reemplazo.textContent = 'Pastelería La María';
                    imagen.replaceWith(reemplazo);
                }, { once: true });
                tarjeta.appendChild(imagen);
            } else {
                const imagen = document.createElement('div');
                imagen.className = 'image-placeholder';
                imagen.textContent = 'Pastelería La María';
                tarjeta.appendChild(imagen);
            }

            const contenido = document.createElement('div');
            contenido.className = 'product-content';

            const categoria = document.createElement('p');
            categoria.className = 'product-category';
            categoria.textContent = producto.categoria;

            const nombre = document.createElement('h3');
            nombre.className = 'product-name';
            nombre.textContent = producto.nombre;

            const descripcion = document.createElement('p');
            descripcion.className = 'product-description';
            descripcion.textContent = producto.descripcion || '';

            const precio = document.createElement('p');
            precio.className = 'product-price';
            const valor = producto.precio === null || producto.precio === ''
                ? Number.NaN
                : Number(producto.precio);
            precio.textContent = Number.isFinite(valor)
                ? moneda.format(valor)
                : 'Consultar precio';

            contenido.append(categoria, nombre, descripcion, precio);

            if (numeroWhatsapp) {
                const acciones = document.createElement('div');
                acciones.className = 'product-actions';
                acciones.append(
                    crearAccionWhatsapp(producto, 'whatsapp'),
                    crearAccionWhatsapp(producto, 'cotizacion'),
                );
                contenido.appendChild(acciones);
            }

            tarjeta.appendChild(contenido);
            catalogo.appendChild(tarjeta);
        });

        aplicarFiltro(document.querySelector('#filtros .is-active')?.dataset.filtro || 'todos');
    }

    function aplicarFiltro(seleccion) {
        catalogo.querySelectorAll('.product-card').forEach((producto) => {
            producto.hidden = seleccion !== 'todos' && producto.dataset.categoria !== seleccion;
        });
    }

    function renderizarFiltros(categorias) {
        filtros.replaceChildren();

        const todos = document.createElement('button');
        todos.className = 'filter-button is-active';
        todos.type = 'button';
        todos.dataset.filtro = 'todos';
        todos.setAttribute('aria-pressed', 'true');
        todos.textContent = 'Todos';
        filtros.appendChild(todos);

        categorias.forEach((categoria) => {
            const boton = document.createElement('button');
            boton.className = 'filter-button';
            boton.type = 'button';
            boton.dataset.filtro = normalizarCategoria(categoria.nombre);
            boton.setAttribute('aria-pressed', 'false');
            boton.textContent = categoria.nombre;
            filtros.appendChild(boton);
        });
    }

    async function cargarCategorias() {
        try {
            const respuesta = await fetch(API_CATEGORIAS, {
                headers: { Accept: 'application/json' },
            });

            if (!respuesta.ok) {
                throw new Error(`La consulta de categorías respondió HTTP ${respuesta.status}.`);
            }

            const resultado = await respuesta.json();
            if (!resultado.success || !Array.isArray(resultado.data)) {
                throw new Error('La respuesta de categorías tiene un formato inesperado.');
            }

            estadoFiltros.textContent = '';
            renderizarFiltros(resultado.data);
        } catch (error) {
            console.error('Error al cargar las categorías:', error);
            estadoFiltros.textContent = 'No fue posible cargar los filtros de categorías.';
        }
    }

    async function cargarProductos() {
        catalogo.replaceChildren();
        estado.textContent = 'Cargando productos...';

        try {
            const respuesta = await fetch(API_PRODUCTOS, {
                headers: { Accept: 'application/json' },
            });

            if (!respuesta.ok) {
                throw new Error(`La consulta de productos respondió HTTP ${respuesta.status}.`);
            }

            const resultado = await respuesta.json();
            if (!resultado.success || !Array.isArray(resultado.data)) {
                throw new Error('La respuesta de productos tiene un formato inesperado.');
            }

            mostrarProductos(resultado.data);
        } catch (error) {
            console.error('Error al cargar los productos:', error);
            estado.textContent = 'No fue posible cargar el catálogo. Intenta actualizarlo más tarde.';
        }
    }

    filtros.addEventListener('click', (evento) => {
        const boton = evento.target.closest('[data-filtro]');
        if (!boton || !filtros.contains(boton)) {
            return;
        }

        filtros.querySelectorAll('[data-filtro]').forEach((filtro) => {
            const activo = filtro === boton;
            filtro.classList.toggle('is-active', activo);
            filtro.setAttribute('aria-pressed', String(activo));
        });

        aplicarFiltro(boton.dataset.filtro);
    });

    recargar?.addEventListener('click', () => {
        cargarCategorias();
        cargarProductos();
    });

    cargarCategorias();
    cargarProductos();
});
