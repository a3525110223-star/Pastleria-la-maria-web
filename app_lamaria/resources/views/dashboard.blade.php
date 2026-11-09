@extends('layouts.app')
@section('title', 'Panel principal | La María')
@section('header', 'Panel principal')
@section('content')
<h2 class="page-heading">Bienvenido, {{ auth()->user()->name }}</h2>
<p class="page-description">
    Administra y consulta la información de Pastelería La María.
</p>
<section class="page-card">
    <h3>Panel principal</h3>
    <p>
        Selecciona una opción del menú lateral para comenzar.
    </p>
</section>
@endsection
