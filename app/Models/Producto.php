<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    // Nombre de la tabla explícito (por si acaso)
    protected $table = 'productos';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'cantidad',
        'imagen_principal',
        'creado_por',
        'categoria_id',
        'proveedor_id',
    ];

    // Add or extend casts to ensure precio is treated as integer
    protected $casts = [
        'precio' => 'integer',
    ];

    /**
     * Relación con el usuario que creó el producto
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Relación con la categoría del producto
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Relación con el proveedor del producto
     */
    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    /**
     * Relación con las imágenes del producto
     */
    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'producto_id');
    }
}
