@extends('layouts.app')

@section('title', 'Categorías | La María')
@section('header', 'Categorías')

@section('content')
@php $editando = isset($categoriaEditar); @endphp

<h2 class="page-heading">Administración de categorías</h2>
<p class="page-description">Registra, consulta y administra las categorías de los productos.</p>

{{-- Mensajes de estado de sesión --}}
@if (session('success'))
    <div class="alert alert--success" role="status">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert--error" role="alert">{{ session('error') }}</div>
@endif

<section class="page-card">
    <h3>{{ $editando ? 'Editar categoría' : 'Registrar categoría' }}</h3>

    <form action="{{ $editando ? route('categorias.update', $categoriaEditar->id_categoria) : route('categorias.store') }}"
          method="POST" class="form">
        @csrf
        @if ($editando)
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="nombre" class="form-label">Nombre de la categoría *</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   maxlength="50" value="{{ old('nombre', $categoriaEditar->nombre ?? '') }}" required>
            @error('nombre')
                <small class="form-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control form-control--textarea"
                      rows="3" maxlength="2000">{{ old('descripcion', $categoriaEditar->descripcion ?? '') }}</textarea>
            @error('descripcion')
                <small class="form-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="activa" class="form-label">Estado *</label>
            <select id="activa" name="activa" class="form-control" required>
                <option value="1" {{ (string) old('activa', $categoriaEditar->activa ?? 1) === '1' ? 'selected' : '' }}>Activa</option>
                <option value="0" {{ (string) old('activa', $categoriaEditar->activa ?? 1) === '0' ? 'selected' : '' }}>Inactiva</option>
            </select>
            @error('activa')
                <small class="form-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">
                {{ $editando ? 'Guardar cambios' : 'Registrar categoría' }}
            </button>
            @if ($editando)
                <a href="{{ route('categorias.index') }}" class="btn btn--secondary">Cancelar edición</a>
            @else
                <button type="reset" class="btn btn--secondary">Limpiar</button>
            @endif
        </div>
    </form>
</section>

<section class="page-card">
    <h3>Categorías registradas</h3>
    @if ($categorias->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorias as $categoria)
                        <tr>
                            <td>{{ $categoria->id_categoria }}</td>
                            <td>{{ $categoria->nombre }}</td>
                            <td>{{ $categoria->descripcion ?: 'Sin descripción' }}</td>
                            <td>
                                <span class="status-badge {{ $categoria->activa ? 'status-badge--active' : 'status-badge--inactive' }}">
                                    {{ $categoria->activa ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('categorias.edit', $categoria->id_categoria) }}" class="btn btn--secondary">Editar</a>
                                    <form action="{{ route('categorias.destroy', $categoria->id_categoria) }}" method="POST" onsubmit="return confirm('¿Quieres eliminar esta categoría?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn--danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-container">
            {{ $categorias->links() }}
        </div>
    @else
        <p>Todavía no hay categorías registradas.</p>
    @endif
</section>
@endsection
