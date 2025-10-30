<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'fecha',
        'tipo',
        'cantidad',
        'producto_id',
        'producto_nombre',
        'user_id',
        'referencia_documento'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor para obtener el nombre del producto incluso si fue eliminado
    public function getProductoNombreAttribute(): string
    {
        // Prefer stored producto_nombre (audit), fallback to related producto name if present
        if (!empty($this->attributes['producto_nombre'])) {
            return $this->attributes['producto_nombre'];
        }

        return $this->producto ? $this->producto->nombre : 'Producto eliminado';
    }

    // Accessor para obtener el nombre del usuario incluso si fue eliminado
    public function getUserNombreAttribute(): string
    {
        return $this->user ? $this->user->name : 'Usuario eliminado';
    }
}