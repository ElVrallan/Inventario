<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovimientoInventarioController extends Controller
{
    public function index(Request $request)
    {
        // Eager load producto + categoria + proveedor and user
        $query = MovimientoInventario::with(['producto.categoria', 'producto.proveedor', 'user']);

        // Filtros mutuamente excluyentes (solo se aplica uno a la vez)
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        } elseif ($request->has('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        } elseif ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        } elseif ($request->has('categoria_id')) {
            // filter via producto relation
            $query->whereHas('producto', function ($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            });
        } elseif ($request->has('proveedor_id')) {
            $query->whereHas('producto', function ($q) use ($request) {
                $q->where('proveedor_id', $request->proveedor_id);
            });
        }

        // Ordenamiento: permitir 'fecha' o 'cantidad'
        $allowedSorts = ['fecha', 'cantidad'];
        $sort_field = $request->input('sort', 'fecha');
        if (!in_array($sort_field, $allowedSorts)) {
            $sort_field = 'fecha';
        }
        $sort_direction = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort_field, $sort_direction);

        $perPage = (int) $request->input('per_page', 10);
        $movimientos = $query->paginate($perPage)->withQueryString();

        // Extraer información para mostrar filtros activos
        $filtroActivo = null;
        if ($request->has('tipo')) {
            $filtroActivo = 'Tipo: ' . ucfirst($request->tipo);
        } elseif ($request->has('producto_id')) {
            // obtener un movimiento representativo para leer categoría y nombre
            $movimiento = MovimientoInventario::with('producto.categoria')->where('producto_id', $request->producto_id)->first();
            if ($movimiento && $movimiento->producto && $movimiento->producto->categoria) {
                $filtroActivo = 'Categoría: ' . $movimiento->producto->categoria->nombre . ' — ' . ($movimiento->producto_nombre ?? $movimiento->producto->nombre);
            } else {
                $filtroActivo = 'Producto: ' . ($movimiento->producto_nombre ?? 'Desconocido');
            }
        } elseif ($request->has('user_id')) {
            $movimiento = MovimientoInventario::with('user')->where('user_id', $request->user_id)->first();
            $filtroActivo = 'Usuario: ' . ($movimiento && $movimiento->user ? $movimiento->user->name : 'Desconocido');
        } elseif ($request->has('categoria_id')) {
            $categoriaName = null;
            $m = MovimientoInventario::with('producto.categoria')->whereHas('producto', function ($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            })->first();
            if ($m && $m->producto && $m->producto->categoria) {
                $categoriaName = $m->producto->categoria->nombre;
            }
            $filtroActivo = 'Categoría: ' . ($categoriaName ?? $request->categoria_id);
        } elseif ($request->has('proveedor_id')) {
            $provName = null;
            $m = MovimientoInventario::with('producto.proveedor')->whereHas('producto', function ($q) use ($request) {
                $q->where('proveedor_id', $request->proveedor_id);
            })->first();
            if ($m && $m->producto && $m->producto->proveedor) {
                $provName = $m->producto->proveedor->nombre ?? ($m->producto->proveedor->razon_social ?? null);
            }
            $filtroActivo = 'Proveedor: ' . ($provName ?? $request->proveedor_id);
        }

        return view('movimientos.index', compact('movimientos', 'sort_field', 'sort_direction', 'filtroActivo'));
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