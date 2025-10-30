<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovimientoInventarioController extends Controller
{
    public function index()
    {
        $movimientos = MovimientoInventario::with(['producto', 'user'])
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        return view('movimientos.index', compact('movimientos'));
    }

    public function create()
    {
        $productos = Producto::all();
        return view('movimientos.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'producto_id' => 'required|exists:productos,id',
            'referencia_documento' => 'nullable|string|max:255'
        ]);

        $movimiento = new MovimientoInventario($request->all());
    $movimiento->user_id = Auth::id();

    // Capture product name at time of movement for audit
    $producto = Producto::find($request->producto_id);
    $movimiento->producto_nombre = $producto ? $producto->nombre : null;

    $movimiento->save();

        $producto = Producto::find($request->producto_id);
        if ($request->tipo === 'entrada') {
            $producto->stock += $request->cantidad;
        } else {
            $producto->stock -= $request->cantidad;
        }
        $producto->save();

        return redirect()->route('movimientos.index')
            ->with('success', 'Movimiento registrado correctamente');
    }
}