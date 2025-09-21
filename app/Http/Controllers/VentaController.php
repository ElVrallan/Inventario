<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;

class VentaController extends Controller
{
    public function store(Request $request, Producto $producto)
    {
        $request->validate([
            'cantidad' => "required|integer|min:1|max:{$producto->cantidad}"
        ]);

        // Registrar la venta
        Venta::create([
            'producto_id' => $producto->id,
            'user_id' => auth()->id(),
            'cantidad' => $request->cantidad,
        ]);

        // Restar del inventario
        $producto->decrement('cantidad', $request->cantidad);

        return redirect()->back()->with('success', "Venta registrada correctamente.");
    }
}
