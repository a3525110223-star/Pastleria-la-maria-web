<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;

class CategoriaApiController extends Controller
{
    public function index(): JsonResponse
    {
        $categorias = Categoria::where('activa', true)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $categorias->count(),
            'data' => $categorias->map(function ($categoria) {
                return [
                    'id_categoria' => $categoria->id_categoria,
                    'nombre' => $categoria->nombre,
                ];
            }),
        ], 200);
    }
}
