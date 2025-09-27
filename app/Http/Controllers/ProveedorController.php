<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::orderBy('nombre')->paginate(10);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        // forzar que creado_por sea el usuario autenticado (evita FK null/incorrecto)
        $request->merge(['creado_por' => auth()->id()]);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:proveedores,email',
            'direccion' => 'nullable|string|max:255',
            'creado_por' => 'required|exists:users,id',
        ]);

        Proveedor::create($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedore)
    {
        return view('proveedores.edit', compact('proveedore'));
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'required|email|unique:proveedores,email,' . $proveedore->id,
            'direccion' => 'nullable|string|max:255',
        ]);

        // Actualizar sólo los campos permitidos (incluyendo direccion)
        $proveedore->update($request->only(['nombre', 'email', 'telefono', 'direccion']));

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedore)
    {
        $proveedore->delete();
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }
}
