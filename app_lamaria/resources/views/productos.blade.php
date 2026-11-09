@extends('layouts.app')
@section('title', 'Productos | La María')
@section('header', 'Productos')

@section('content')
@php
    $editando = isset($productoEditar);
@endphp

<h2 class="page-heading">Administración de productos</h2>
<p class="page-description">
    Registra, consulta y administra los productos de Pastelería La María.
</p>

@if (session('success'))
    <div class="alert alert--success" role="status">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert--error" role="alert">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert--error" role="alert">
        <p>Revisa los siguientes errores:</p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section class="page-card" id="formulario">
    <h3>{{ $editando ? 'Editar producto' : 'Registrar producto' }}</h3>

    <form
        action="{{ $editando ? route('productos.update', $productoEditar->id_producto) : route('productos.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="form"
    >
        @csrf
        @if ($editando)
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="nombre" class="form-label">Nombre del producto *</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                class="form-control"
                value="{{ old('nombre', $productoEditar->nombre ?? '') }}"
                maxlength="100"
                required
            >
        </div>

        <div class="form-group">
            <label for="id_categoria" class="form-label">Categoría *</label>
            <select id="id_categoria" name="id_categoria" class="form-control" required>
                <option value="">Selecciona una categoría</option>
                @foreach ($categorias as $categoria)
                    <option
                        value="{{ $categoria->id_categoria }}"
                        @selected(old('id_categoria', $productoEditar->id_categoria ?? '') == $categoria->id_categoria)
                    >
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="descripcion" class="form-label">Descripción *</label>
            <textarea
                id="descripcion"
                name="descripcion"
                class="form-control form-control--textarea"
                rows="3"
                required
            >{{ old('descripcion', $productoEditar->descripcion ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label for="precio" class="form-label">Precio</label>
            <input
                type="number"
                id="precio"
                name="precio"
                class="form-control"
                value="{{ old('precio', $productoEditar->precio ?? '') }}"
                min="0"
                max="99999999.99"
                step="0.01"
            >
        </div>

        <div class="form-group">
            <label for="imagen" class="form-label">
                Fotografía del producto *
                @if ($editando)
                    <span class="form-help">Selecciona una nueva solamente si deseas cambiarla.</span>
                @endif
            </label>
            <input
                type="file"
                id="imagen"
                name="imagen"
                class="form-control"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                @required(!$editando)
                aria-describedby="ayuda-imagen"
            >
            <p id="ayuda-imagen" class="form-help">
                Formatos permitidos: JPG, JPEG, PNG y WEBP. Tamaño máximo: 2 MB.
            </p>

            <div id="vista-previa-imagen" class="image-preview-container">
                @if ($editando)
                    <p class="form-help">Fotografía actual:</p>
                    @if ($productoEditar->image_url)
                        <img
                            src="{{ asset('storage/' . $productoEditar->image_url) }}"
                            alt="Fotografía de {{ $productoEditar->nombre }}"
                            class="product-image-preview"
                            onerror="this.style.display='none'; document.getElementById('error-imagen-actual').style.display='block';"
                        >
                        <p id="error-imagen-actual" class="form-help" style="display:none;">
                            No se pudo cargar la imagen. Verifica la ruta almacenada en image_url.
                        </p>
                    @else
                        <p class="form-help">Este producto todavía no tiene una fotografía registrada.</p>
                    @endif
                @endif
            </div>
        </div>

        <div class="form-group">
            <label for="activa" class="form-label">Estado *</label>
            <select id="activa" name="activa" class="form-control" required>
                <option value="1" @selected(old('activa', $productoEditar->activa ?? 1) == 1)>Activo</option>
                <option value="0" @selected(old('activa', $productoEditar->activa ?? 1) == 0)>Inactivo</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">
                {{ $editando ? 'Guardar cambios' : 'Registrar producto' }}
            </button>
            @if ($editando)
                <a href="{{ route('productos.index') }}" class="btn btn--secondary">Cancelar edición</a>
            @else
                <button type="reset" class="btn btn--secondary">Limpiar formulario</button>
            @endif
        </div>
    </form>
</section>

<section class="page-card">
    <h3>Productos registrados</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Fotografía</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>
                            @if (!empty($producto->image_url))
                                <img
                                    src="{{ asset('storage/' . $producto->image_url) }}"
                                    alt="{{ $producto->nombre }}"
                                    class="product-thumbnail"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="product-image-placeholder" style="display: none;">
                                    <span>Sin imagen</span>
                                </div>
                            @else
                                <div class="product-image-placeholder">
                                    <span>Sin imagen</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $producto->nombre }}</strong>
                            <p class="product-description-cell">
                                {{ \Illuminate\Support\Str::limit($producto->descripcion, 70) }}
                            </p>
                        </td>
                        <td>{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</td>
                        <td>
                            {{ $producto->precio !== null ? '$' . number_format((float) $producto->precio, 2) : 'No especificado' }}
                        </td>
                        <td>
                            @if ($producto->activa)
                                <span class="status-badge status-badge--active">Activo</span>
                            @else
                                <span class="status-badge status-badge--inactive">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('productos.edit', $producto->id_producto) }}#formulario" class="btn btn--secondary">
                                    Editar
                                </a>
                                <form
                                    action="{{ route('productos.destroy', $producto->id_producto) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este producto?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay productos registrados todavía.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $productos->links() }}
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputImagen = document.getElementById('imagen');
    const vistaPrevia = document.getElementById('vista-previa-imagen');

    if (!inputImagen || !vistaPrevia) return;

    let urlTemporalAnterior = null;

    inputImagen.addEventListener('change', function () {
        const archivo = inputImagen.files && inputImagen.files[0];
        if (!archivo || !archivo.type.startsWith('image/')) return;

        if (urlTemporalAnterior) {
            URL.revokeObjectURL(urlTemporalAnterior);
        }

        urlTemporalAnterior = URL.createObjectURL(archivo);
        vistaPrevia.replaceChildren();

        const texto = document.createElement('p');
        texto.className = 'form-help';
        texto.textContent = 'Vista previa de la nueva fotografía:';

        const imagen = document.createElement('img');
        imagen.src = urlTemporalAnterior;
        imagen.alt = 'Vista previa de la fotografía seleccionada';
        imagen.className = 'product-image-preview';

        vistaPrevia.append(texto, imagen);
    });
});
</script>
@endsection
