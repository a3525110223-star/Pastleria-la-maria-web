@extends('layouts.app')

@section('title', 'Roles | La María')
@section('header', 'Roles')
@section('content')
<h2 class="page-heading">Administración de roles</h2>
<p class="page-description">Consulta los perfiles de acceso del sistema.</p>

@if (session('success'))
    <div class="alert alert--success" role="status">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert--error" role="alert">{{ session('error') }}</div>
@endif

<section class="page-card">
    <div class="page-card__heading">
        <h3>Roles registrados</h3>
        <a href="{{ route('roles.create') }}" class="btn btn--primary">Nuevo rol</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>Nombre</th><th>Descripción</th><th>Usuarios</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td>{{ $role->nombre }}</td>
                        <td>{{ $role->descripcion ?: 'Sin descripción' }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn--secondary">Editar</a>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                      onsubmit="return confirm('¿Quieres eliminar este rol?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No hay roles registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container">{{ $roles->links() }}</div>
</section>
@endsection
