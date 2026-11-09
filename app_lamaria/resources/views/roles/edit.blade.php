@extends('layouts.app')

@section('title', 'Editar rol | La María')
@section('header', 'Roles')
@section('content')
<h2 class="page-heading">Editar rol</h2>
<p class="page-description">Actualiza el rol {{ $role->nombre }}.</p>

@if ($errors->any())
    <div class="alert alert--error" role="alert">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<section class="page-card">
    <form action="{{ route('roles.update', $role) }}" method="POST" class="form">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nombre" class="form-label">Nombre *</label>
            <input id="nombre" name="nombre" class="form-control" maxlength="50"
                   value="{{ old('nombre', $role->nombre) }}" required>
        </div>
        <div class="form-group">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control form-control--textarea"
                      rows="3" maxlength="255">{{ old('descripcion', $role->descripcion) }}</textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Guardar cambios</button>
            <a href="{{ route('roles.index') }}" class="btn btn--secondary">Cancelar</a>
        </div>
    </form>
</section>
@endsection
