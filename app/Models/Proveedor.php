<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    // permitir asignación masiva del campo creado_por
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'creado_por'
    ];

    // relación hacia el usuario que creó el proveedor
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    // Relación inversa con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'proveedor_id');
    }
}
