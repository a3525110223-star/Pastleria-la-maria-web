<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\CategoriaApiController;

Route::get('/productos', [ProductoApiController::class, 'index']);
Route::get('/categorias', [CategoriaApiController::class, 'index']);
