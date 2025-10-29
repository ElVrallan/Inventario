<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Laptop Gamer',
                'descripcion' => 'Laptop de alto rendimiento con tarjeta gráfica dedicada.',
                'precio' => 4500000,
                'cantidad' => 10,
                'imagen_principal' => 'productos/laptop.jpg',
                'creado_por' => 1,
            ],
            [
                'nombre' => 'Mouse Inalámbrico',
                'descripcion' => 'Mouse ergonómico con conexión Bluetooth.',
                'precio' => 120000,
                'cantidad' => 50,
                'imagen_principal' => 'productos/mouse.jpg',
                'creado_por' => 1,
            ],
            [
                'nombre' => 'Teclado Mecánico',
                'descripcion' => 'Teclado con switches rojos y retroiluminación RGB.',
                'precio' => 280000,
                'cantidad' => 30,
                'imagen_principal' => 'productos/teclado.jpg',
                'creado_por' => 1,
            ],
            [
                'nombre' => 'Tecladom Mecánico RGB',
                'descripcion' => 'Teclado mecánico retroiluminado con switches azules.',
                'precio' => 220000,
                'cantidad' => 15,
                'imagen_principal' => 'productos/tecladom.jpg',
                'creado_por' => 1,
            ],
            [
                'nombre' => 'Silla Gamer',
                'descripcion' => 'Silla ergonómica ajustable con soporte lumbar.',
                'precio' => 850000,
                'cantidad' => 5,
                'imagen_principal' => 'productos/silla.jpg',
                'creado_por' => 1,
            ],
            [
                'nombre' => 'Monitor 24" Full HD',
                'descripcion' => 'Monitor LED de 24 pulgadas con resolución 1920x1080.',
                'precio' => 700000,
                'cantidad' => 8,
                'imagen_principal' => 'productos/monitor.jpg',
                'creado_por' => 1,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
