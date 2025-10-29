<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'creado_por'];
    
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
