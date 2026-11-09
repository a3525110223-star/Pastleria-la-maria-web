<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index()
    {
        return $this->mostrarProductos();
    }

    public function create()
    {
        return redirect()->route('productos.index')->withFragment('formulario');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_categoria' => [
                'required',
                'integer',
                Rule::exists('categorias', 'id_categoria')->where('activa', 1),
            ],
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('productos', 'nombre'),
            ],
            'descripcion' => ['required', 'string'],
            'precio' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'imagen' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'activa' => ['required', 'boolean'],
        ], [
            'id_categoria.required' => 'Selecciona una categoría.',
            'id_categoria.exists' => 'La categoría seleccionada no existe o está inactiva.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede superar 100 caracteres.',
            'nombre.unique' => 'Ya existe un producto registrado con este nombre.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'precio.numeric' => 'El precio debe ser numérico.',
            'precio.min' => 'El precio no puede ser negativo.',
            'imagen.required' => 'Selecciona una fotografía del producto.',
            'imagen.image' => 'El archivo seleccionado debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe estar en formato JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La imagen no puede superar los 2 MB.',
        ]);

        $rutaImagen = $request->file('imagen')->store('productos', 'public');
        $validated['image_url'] = $rutaImagen;
        $validated['activa'] = $request->boolean('activa');
        unset($validated['imagen']);

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function edit(string $id)
    {
        $productoEditar = Producto::findOrFail($id);
        return $this->mostrarProductos($productoEditar);
    }

    public function update(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'id_categoria' => [
                'required',
                'integer',
                Rule::exists('categorias', 'id_categoria')->where(function ($query) use ($producto) {
                    $query->where('activa', 1)
                        ->orWhere('id_categoria', $producto->id_categoria);
                }),
            ],
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('productos', 'nombre')->ignore($producto->id_producto, 'id_producto'),
            ],
            'descripcion' => ['required', 'string'],
            'precio' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'activa' => ['required', 'boolean'],
        ], [
            'id_categoria.required' => 'Selecciona una categoría.',
            'id_categoria.exists' => 'La categoría no existe o no puede seleccionarse.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede superar 100 caracteres.',
            'nombre.unique' => 'Ya existe otro producto registrado con este nombre.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'precio.numeric' => 'El precio debe ser numérico.',
            'precio.min' => 'El precio no puede ser negativo.',
            'imagen.image' => 'El archivo seleccionado debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe estar en formato JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La imagen no puede superar los 2 MB.',
        ]);

        $rutaAnterior = $producto->image_url;

        if ($request->hasFile('imagen')) {
            $validated['image_url'] = $request->file('imagen')->store('productos', 'public');
        }

        $validated['activa'] = $request->boolean('activa');
        unset($validated['imagen']);

        $producto->update($validated);

        if (
            $request->hasFile('imagen')
            && $rutaAnterior
            && !str_starts_with($rutaAnterior, 'http://')
            && !str_starts_with($rutaAnterior, 'https://')
        ) {
            Storage::disk('public')->delete($rutaAnterior);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        $rutaImagen = $producto->image_url;

        $producto->delete();

        if (
            $rutaImagen
            && !str_starts_with($rutaImagen, 'http://')
            && !str_starts_with($rutaImagen, 'https://')
        ) {
            Storage::disk('public')->delete($rutaImagen);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    private function mostrarProductos(?Producto $productoEditar = null)
    {
        $productos = Producto::with('categoria')
            ->orderBy('nombre')
            ->paginate(10);

        $categorias = Categoria::where('activa', true)
            ->when($productoEditar, fn ($query) => $query->orWhere('id_categoria', $productoEditar->id_categoria))
            ->orderBy('nombre')
            ->get();

        return view('productos', compact('productos', 'categorias', 'productoEditar'));
    }
}
