<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    use HasFactory;

    // Nombre de la tabla explícito
    protected $table = 'producto_imagenes';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'producto_id',
        'ruta_imagen',
    ];

    /**
     * Relación inversa con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
