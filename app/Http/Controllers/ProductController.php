<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        // Middleware de autenticación y roles válidos
        $this->middleware(['auth', 'role:admin,vendedor']);

        // Middleware adicional para bloquear usuarios 'pendiente'
        $this->middleware(function ($request, $next) {
            if(auth()->user()->rol === 'pendiente'){
                abort(403, 'Cuenta pendiente de aprobación.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'main_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'description', 'price', 'quantity');
        $data['created_by'] = auth()->id();

        // Imagen principal
        if($request->hasFile('main_image')){
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product = Product::create($data);

        // Galería de imágenes
        if($request->hasFile('gallery_images')){
            foreach($request->file('gallery_images') as $img){
                $path = $img->store('products', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'main_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'description', 'price', 'quantity');

        // Actualizar imagen principal
        if($request->hasFile('main_image')){
            // Borrar imagen antigua si existe
            if($product->main_image && \Storage::disk('public')->exists($product->main_image)){
                \Storage::disk('public')->delete($product->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($data);

        // Subir nuevas imágenes a la galería
        if($request->hasFile('gallery_images')){
            foreach($request->file('gallery_images') as $img){
                $path = $img->store('products', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if(auth()->user()->rol !== 'admin') {
            abort(403);
        }

        // Borrar imagen principal
        if($product->main_image && \Storage::disk('public')->exists($product->main_image)){
            \Storage::disk('public')->delete($product->main_image);
        }

        // Borrar imágenes de la galería
        foreach($product->images as $img){
            if(\Storage::disk('public')->exists($img->path)){
                \Storage::disk('public')->delete($img->path);
            }
        }

        $product->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }

}
