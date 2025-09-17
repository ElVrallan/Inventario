<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\ProductoImagen;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 3 productos de ejemplo
        $productos = [
            [
                'nombre' => 'Laptop Gamer',
                'descripcion' => 'Laptop de alto rendimiento con tarjeta gráfica dedicada.',
                'precio' => 4500000,
                'cantidad' => 10,
                'imagen_principal' => 'productos/laptop.jpg',
                'creado_por' => 1, // 👈 asegúrate de tener un usuario con id=1
                'categoria_id' => null,
                'proveedor_id' => null,
            ],
            [
                'nombre' => 'Mouse Inalámbrico',
                'descripcion' => 'Mouse ergonómico con conexión Bluetooth.',
                'precio' => 120000,
                'cantidad' => 50,
                'imagen_principal' => 'productos/mouse.jpg',
                'creado_por' => 1,
                'categoria_id' => null,
                'proveedor_id' => null,
            ],
            [
                'nombre' => 'Teclado Mecánico',
                'descripcion' => 'Teclado con switches rojos y retroiluminación RGB.',
                'precio' => 280000,
                'cantidad' => 30,
                'imagen_principal' => 'productos/teclado.jpg',
                'creado_por' => 1,
                'categoria_id' => null,
                'proveedor_id' => null,
            ],
        ];

        foreach ($productos as $productoData) {
            $producto = Producto::create($productoData);

            // Agregar imágenes adicionales
            for ($i = 1; $i <= 2; $i++) {
                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta_imagen' => "productos/galeria_{$producto->id}_{$i}.jpg",
                ]);
            }
        }
    }
}
