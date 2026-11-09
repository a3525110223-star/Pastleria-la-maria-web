<?php

use App\Http\Controllers\Api\CategoriaApiController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\ProductoApiController;
use Illuminate\Support\Facades\Route;

Route::get('/empresa', [EmpresaController::class, 'show']);
Route::get('/productos', [ProductoApiController::class, 'index']);
Route::get('/categorias', [CategoriaApiController::class, 'index']);
