<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function edit()
    {
        // Centralización: obtener o instanciar el registro único (ID = 1)[cite: 1]
        $empresa = Empresa::firstOrCreate(
            ['id' => 1],
            [
                'nombre_comercial' => 'Pastelería La María',
                'numero_whatsapp' => '5550000000',
                'email' => 'contacto@lamaria.com',
                'ubicacion' => 'Av. Principal #123',
                'mision' => null,
                'vision' => null,
                'horario_normal' => 'Lunes a Sábado de 8:00 AM a 8:00 PM',
            ]
        );

        return view('empresa.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nombre_comercial' => 'required|string|max:100',
            'razon_social' => 'nullable|string|max:150',
            'rfc' => 'nullable|string|max:13',
            'telefono' => 'nullable|string|max:20',
            'numero_whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'ubicacion' => 'required|string|max:255',
            'mision' => 'nullable|string|max:2000',
            'vision' => 'nullable|string|max:2000',
            'horario_normal' => 'required|string|max:255',
            'horario_especial' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'logotipo' => 'nullable|string|max:255',
            'sitio_web' => 'nullable|url|max:255',
        ]);

        $empresa = Empresa::firstOrCreate(
            ['id' => 1],
            [
                'nombre_comercial' => 'Pastelería La María',
                'numero_whatsapp' => '5550000000',
                'email' => 'contacto@lamaria.com',
                'ubicacion' => 'Av. Principal #123',
                'mision' => null,
                'vision' => null,
                'horario_normal' => 'Lunes a Sábado de 8:00 AM a 8:00 PM',
            ]
        );

        $empresa->update($validated);

        return redirect()->route('empresa.edit')->with('success', 'Información de la empresa actualizada correctamente.');
    }
}
