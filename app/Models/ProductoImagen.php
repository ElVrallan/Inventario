<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    use HasFactory;

    // Nombre de la tabla explícito
    protected $table = 'producto_imagenes';

    // Campos que se pueden asignar masivamente (usar la columna real 'ruta')
    protected $fillable = [
        'producto_id',
        'ruta',
        'es_principal',
    ];

    // Casts útiles
    protected $casts = [
        'es_principal' => 'boolean',
    ];

    /**
     * Relación inversa con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // --- Compatibilidad y utilidades ---

    /**
     * Alias para compatibilidad con código que usa 'ruta_imagen'
     */
    public function getRutaImagenAttribute()
    {
        return $this->attributes['ruta'] ?? null;
    }

    public function setRutaImagenAttribute($value)
    {
        $this->attributes['ruta'] = $value;
    }

    /**
     * Devuelve la URL pública completa (asset) y normaliza barras.
     * Uso en vistas: $imagen->url
     */
    public function getUrlAttribute()
    {
        $ruta = $this->attributes['ruta'] ?? null;
        if (!$ruta) {
            return null;
        }

        // Normalizar backslashes a slashes y remover prefijo 'storage/' si ya existe duplicado
        $ruta = str_replace('\\', '/', $ruta);
        $ruta = ltrim($ruta, '/');

        // Si la ruta ya incluye 'storage/', evitamos doble prefijo
        if (strpos($ruta, 'storage/') === 0) {
            return asset($ruta);
        }

        return asset('storage/' . $ruta);
    }
}
