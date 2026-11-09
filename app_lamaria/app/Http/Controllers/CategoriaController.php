<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->paginate(10);
        return view('categorias', compact('categorias'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->paginate(10);
        return view('categorias', [
            'categorias' => $categorias,
            'modo' => 'crear',
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:categorias,nombre'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'activa' => ['required', 'boolean'],
        ]);

        Categoria::create($datos);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categorias = Categoria::orderBy('nombre')->paginate(10);

        return view('categorias', [
            'categorias' => $categorias,
            'modo' => 'editar',
            'categoriaEditar' => $categoria,
        ]);
    }

    
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categorias', 'nombre')
                    ->ignore($categoria->id_categoria, 'id_categoria'),
            ],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'activa' => ['required', 'boolean'],
        ]);

        $categoria->update($datos);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        // Protección de relación: comprobar existencia en la tabla productos
        if (DB::table('productos')->where('id_categoria', $categoria->id_categoria)->exists()) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar: la categoría tiene productos asociados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
