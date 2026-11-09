<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;

class EmpresaController extends Controller
{
    public function show(): JsonResponse
    {
        $empresa = Empresa::first();

        if (! $empresa) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron datos registrados de la empresa.',
            ], 404);
        }

        return response()->json([
            'nombre' => $empresa->nombre_comercial,
            'mision' => $empresa->mision,
            'vision' => $empresa->vision,
            'telefono' => $empresa->telefono,
            'direccion' => $empresa->ubicacion,
            'email' => $empresa->email,
            'numero_whatsapp' => $empresa->numero_whatsapp,
            'horario_normal' => $empresa->horario_normal,
            'horario_especial' => $empresa->horario_especial,
            'descripcion' => $empresa->descripcion,
        ], 200);
    }
}
