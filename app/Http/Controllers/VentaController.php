<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function store(Request $request, Producto $producto)
    {
        // Basic validation first
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $cantidad = (int) $request->input('cantidad');

        // Check stock
        if ($cantidad > $producto->cantidad) {
            return back()->withInput()->withErrors(['cantidad' => 'La cantidad solicitada excede el stock disponible.']);
        }

        // Cast precio_unitario to integer (no decimals per app rules)
        $precioUnitario = is_numeric($producto->precio) ? (int) round($producto->precio) : (int) $producto->precio;

        // Application-wide cap (same cap used elsewhere)
        $MAX_TOTAL = 99999999;

        if ($precioUnitario <= 0) {
            return back()->withInput()->withErrors(['precio' => 'Precio unitario inválido.']);
        }

        $total = $precioUnitario * $cantidad;

        try {
            DB::transaction(function () use ($producto, $cantidad, $precioUnitario, $total, $MAX_TOTAL) {
                // Si cabe en un solo registro, crearla directamente
                if ($total <= $MAX_TOTAL) {
                    $venta = Venta::create([
                        'producto_id' => $producto->id,
                        'user_id' => auth()->id(),
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'total' => $total,
                    ]);

                    // Registrar la venta como una salida de inventario
                    MovimientoInventario::create([
                        'fecha' => now(),
                        'tipo' => 'salida',
                        'cantidad' => $cantidad,
                        'producto_id' => $producto->id,
                        'producto_nombre' => $producto->nombre,
                        'user_id' => auth()->id(),
                        'referencia_documento' => 'Venta #' . $venta->id
                    ]);
                } else {
                    // Calcular cuántas unidades máximo por registro para no superar MAX_TOTAL
                    $maxQtyPerSale = intdiv($MAX_TOTAL, $precioUnitario);

                    if ($maxQtyPerSale < 1) {
                        // No es posible registrar ni siquiera 1 unidad (precio unitario demasiado alto)
                        throw new \RuntimeException('Precio unitario demasiado alto para registrar la venta. Contacte al administrador.');
                    }

                    $remaining = $cantidad;
                    while ($remaining > 0) {
                        $chunkQty = ($remaining > $maxQtyPerSale) ? $maxQtyPerSale : $remaining;
                        $chunkTotal = $precioUnitario * $chunkQty;

                        Venta::create([
                            'producto_id' => $producto->id,
                            'user_id' => auth()->id(),
                            'cantidad' => $chunkQty,
                            'precio_unitario' => $precioUnitario,
                            'total' => $chunkTotal,
                        ]);

                        $remaining -= $chunkQty;
                    }
                }

                // Restar del inventario una sola vez por la cantidad total
                $producto->decrement('cantidad', $cantidad);
            }, 5); // retry up to 5 times on deadlock
        } catch (QueryException $e) {
            \Log::error('[VentaController@store] DB error inserting venta', [
                'error' => $e->getMessage(),
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'total' => $total,
            ]);

            return back()->withInput()->withErrors(['cantidad' => 'No se pudo registrar la venta por un error de la base de datos. Verifique los valores y vuelva a intentar.']);
        } catch (\RuntimeException $e) {
            \Log::warning('[VentaController@store] Venta no registrada: '.$e->getMessage(), [
                'producto_id' => $producto->id,
                'precio_unitario' => $precioUnitario,
            ]);
            return back()->withInput()->withErrors(['precio' => $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('[VentaController@store] Unexpected error', [
                'error' => $e->getMessage(),
            ]);
            return back()->withInput()->withErrors(['cantidad' => 'Error inesperado al registrar la venta.']);
        }

        return redirect()->back()->with('success', "Venta registrada correctamente.");
    }
}
