@extends('layouts.app')

@section('title', 'Nuevo usuario | La María')
@section('header', 'Usuarios')
@section('content')
<h2 class="page-heading">Registrar usuario</h2>
<p class="page-description">Los usuarios nuevos solo pueden tener rol Administrador o Empleado.</p>

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
    <form action="{{ route('users.store') }}" method="POST" class="form">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">Nombre completo *</label>
            <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" maxlength="255" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Correo electrónico *</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" maxlength="255" required>
        </div>
        <div class="form-group">
            <label for="role_id" class="form-label">Rol *</label>
            <select id="role_id" name="role_id" class="form-control" required>
                <option value="">Selecciona un rol</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id_rol }}" @selected(old('role_id') == $role->id_rol)>{{ $role->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Contraseña *</label>
            <input id="password" type="password" name="password" class="form-control" autocomplete="new-password" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmar contraseña *</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Guardar usuario</button>
            <a href="{{ route('users.index') }}" class="btn btn--secondary">Cancelar</a>
        </div>
    </form>
</section>
@endsection
