<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Empresa;
use Illuminate\Support\Facades\Route;

// Catálogo público
Route::get('/', function () {
    return view('catalogo', [
        'numeroWhatsapp' => Empresa::query()->value('numero_whatsapp'),
    ]);
})->name('catalogo');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Módulos existentes: se conservan sus controladores y vistas.
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('productos', ProductoController::class)->except(['show']);

    // Rutas exclusivas del Administrador
    Route::middleware(['admin'])->group(function () {
        Route::resource('usuarios', UserController::class)
            ->except(['show'])
            ->parameters(['usuarios' => 'user'])
            ->names('users');
        Route::resource('users', UserController::class)
            ->except(['show'])
            ->names('users.english');
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
    });
});
