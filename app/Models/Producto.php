<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'main_image',
        'created_by',
    ];

    // Relación con usuario que lo creó
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

        
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

}
