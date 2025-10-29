<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoImagenFactory extends Factory
{
    protected $model = ProductoImagen::class;

    public function definition(): array
    {
        return [
            'producto_id' => Producto::factory(),
            'ruta' => $this->faker->imageUrl(640, 480, 'products', true),
            'es_principal' => false,
        ];
    }
}
