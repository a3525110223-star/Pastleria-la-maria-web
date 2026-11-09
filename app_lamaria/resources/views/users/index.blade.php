@extends('layouts.app')

@section('title', 'Usuarios | La María')
@section('header', 'Usuarios')
@section('content')
<h2 class="page-heading">Administración de usuarios</h2>
<p class="page-description">Crea cuentas y administra el acceso del equipo.</p>

@if (session('success'))
    <div class="alert alert--success" role="status">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert--error" role="alert">{{ session('error') }}</div>
@endif

<section class="page-card">
    <div class="page-card__heading">
        <h3>Usuarios registrados</h3>
        <a href="{{ route('users.create') }}" class="btn btn--primary">Nuevo usuario</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo electrónico</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roles->pluck('nombre')->join(', ') ?: 'Sin rol' }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn--secondary">Editar</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                      onsubmit="return confirm('¿Confirma eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No hay usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container">{{ $users->links() }}</div>
</section>
@endsection
