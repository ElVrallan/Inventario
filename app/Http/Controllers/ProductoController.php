<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,vendedor']);

        $this->middleware(function ($request, $next) {
            if (auth()->user()->rol === 'pendiente') {
                abort(403, 'Cuenta pendiente de aprobación.');
            }
            return $next($request);
        });
    }

    /**
     * Mostrar todos los productos
     */
public function index(Request $request)
{
    $query = Producto::with('imagenes');

    if ($request->has('q') && $request->q != '') {
        $search = $request->q;
        $query->where(function($q) use ($search) {
            $q->where('nombre', 'like', "%{$search}%")
              ->orWhere('descripcion', 'like', "%{$search}%")
              ->orWhere('precio', 'like', "%{$search}%")
              ->orWhere('cantidad', 'like', "%{$search}%");
        });
    }

    $productos = $query->orderBy('id', 'desc')->paginate(9);

    // Si es AJAX, solo devolvemos las tarjetas
    if ($request->ajax()) {
        return view('productos.partials.productos_grid', compact('productos'))->render();
    }

    return view('productos.index', compact('productos'));
}


    /**
     * Formulario de creación
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Guardar un nuevo producto
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            'imagen_principal' => 'nullable|image|max:2048',
            'galeria.*' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('nombre', 'descripcion', 'precio', 'cantidad');
        $data['creado_por'] = auth()->id();

        // Imagen principal
        if ($request->hasFile('imagen_principal')) {
            $data['imagen_principal'] = $request->file('imagen_principal')->store('productos', 'public');
        }

        $producto = Producto::create($data);

        // Galería
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $img) {
                $ruta = $img->store('productos', 'public');
                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta_imagen' => $ruta
                ]);
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    /**
     * Mostrar un producto
     */
    public function show(Producto $producto)
    {
        $producto->load('imagenes');
        return view('productos.show', compact('producto'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Producto $producto)
    {
        $producto->load('imagenes');
        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualizar un producto
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            'imagen_principal' => 'nullable|image|max:2048',
            'galeria.*' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('nombre', 'descripcion', 'precio', 'cantidad');

        // Imagen principal
        if ($request->hasFile('imagen_principal')) {
            if ($producto->imagen_principal && Storage::disk('public')->exists($producto->imagen_principal)) {
                Storage::disk('public')->delete($producto->imagen_principal);
            }
            $data['imagen_principal'] = $request->file('imagen_principal')->store('productos', 'public');
        }

        $producto->update($data);

        // Galería
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $img) {
                $ruta = $img->store('productos', 'public');
                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta_imagen' => $ruta
                ]);
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminar un producto
     */
    public function destroy(Producto $producto)
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403);
        }

        // Imagen principal
        if ($producto->imagen_principal && Storage::disk('public')->exists($producto->imagen_principal)) {
            Storage::disk('public')->delete($producto->imagen_principal);
        }

        // Galería
        foreach ($producto->imagenes as $img) {
            if (Storage::disk('public')->exists($img->ruta_imagen)) {
                Storage::disk('public')->delete($img->ruta_imagen);
            }
            $img->delete();
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}