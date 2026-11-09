@extends('layouts.app')

@section('title', 'Empresa | La María')
@section('header', 'Empresa')
@section('content')
<h2 class="page-heading">Información de la pastelería</h2>
<p class="page-description">Mantén actualizados los datos institucionales de Pastelería La María.</p>

@if (session('success'))
    <div class="alert alert--success" role="status">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert--error" role="alert">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert--error" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section class="page-card">
    <form action="{{ route('empresa.update') }}" method="POST" class="form">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nombre_comercial" class="form-label">Nombre comercial *</label>
            <input id="nombre_comercial" name="nombre_comercial" class="form-control" maxlength="100"
                   value="{{ old('nombre_comercial', $empresa->nombre_comercial) }}" required>
        </div>
        <div class="form-group">
            <label for="razon_social" class="form-label">Razón social</label>
            <input id="razon_social" name="razon_social" class="form-control" maxlength="150"
                   value="{{ old('razon_social', $empresa->razon_social) }}">
        </div>
        <div class="form-group">
            <label for="rfc" class="form-label">RFC</label>
            <input id="rfc" name="rfc" class="form-control" maxlength="13" value="{{ old('rfc', $empresa->rfc) }}">
        </div>
        <div class="form-group">
            <label for="telefono" class="form-label">Teléfono</label>
            <input id="telefono" name="telefono" class="form-control" maxlength="20" value="{{ old('telefono', $empresa->telefono) }}">
        </div>
        <div class="form-group">
            <label for="numero_whatsapp" class="form-label">Número de WhatsApp *</label>
            <input id="numero_whatsapp" name="numero_whatsapp" class="form-control" maxlength="20"
                   value="{{ old('numero_whatsapp', $empresa->numero_whatsapp) }}" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Correo electrónico *</label>
            <input id="email" type="email" name="email" class="form-control" maxlength="100"
                   value="{{ old('email', $empresa->email) }}" required>
        </div>
        <div class="form-group">
            <label for="ubicacion" class="form-label">Ubicación / dirección *</label>
            <input id="ubicacion" name="ubicacion" class="form-control" maxlength="255"
                   value="{{ old('ubicacion', $empresa->ubicacion) }}" required>
        </div>
        <div class="form-group">
            <label for="horario_normal" class="form-label">Horario normal *</label>
            <input id="horario_normal" name="horario_normal" class="form-control" maxlength="255"
                   value="{{ old('horario_normal', $empresa->horario_normal) }}" required>
        </div>
        <div class="form-group">
            <label for="horario_especial" class="form-label">Horario especial</label>
            <input id="horario_especial" name="horario_especial" class="form-control" maxlength="255"
                   value="{{ old('horario_especial', $empresa->horario_especial) }}">
        </div>
        <div class="form-group">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control form-control--textarea" rows="4">{{ old('descripcion', $empresa->descripcion) }}</textarea>
        </div>
        <div class="form-group">
            <label for="logotipo" class="form-label">Ruta del logotipo</label>
            <input id="logotipo" name="logotipo" class="form-control" maxlength="255"
                   value="{{ old('logotipo', $empresa->logotipo) }}">
        </div>
        <div class="form-group">
            <label for="sitio_web" class="form-label">Sitio web</label>
            <input id="sitio_web" type="url" name="sitio_web" class="form-control" maxlength="255"
                   placeholder="https://ejemplo.com" value="{{ old('sitio_web', $empresa->sitio_web) }}">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Guardar cambios</button>
        </div>
    </form>
</section>
@endsection
