<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'creado_por'
    ];

    // Relación inversa con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'proveedor_id');
    }
}
