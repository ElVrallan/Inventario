<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Producto;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario Admin principal
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
            'rol' => 'admin',
        ]);

        // Usuario Vendedor de prueba
        $vendedor = User::create([
            'name' => 'Vendedor',
            'email' => 'vendedor@test.com',
            'password' => bcrypt('12345678'),
            'rol' => 'vendedor',
        ]);

        // Categorías básicas
        $categorias = [
            ['nombre' => 'Computadoras'],
            ['nombre' => 'Periféricos'],
            ['nombre' => 'Componentes'],
            ['nombre' => 'Redes'],
        ];

        foreach ($categorias as $cat) {
            Categoria::create([
                'nombre' => $cat['nombre'],
                'creado_por' => $admin->id
            ]);
        }

        // Proveedores de ejemplo
        $proveedores = [
            [
                'nombre' => 'TechSupply SA',
                'email' => 'ventas@techsupply.com',
                'telefono' => '555-0101',
                'direccion' => 'Calle Principal 123'
            ],
            [
                'nombre' => 'Digital Import',
                'email' => 'contacto@digitalimport.com',
                'telefono' => '555-0102',
                'direccion' => 'Avenida Central 456'
            ],
        ];

        foreach ($proveedores as $prov) {
            Proveedor::create([
                'nombre' => $prov['nombre'],
                'email' => $prov['email'],
                'telefono' => $prov['telefono'],
                'direccion' => $prov['direccion'],
                'creado_por' => $admin->id
            ]);
        }

        // Productos de muestra
        $productos = [
            [
                'nombre' => 'Monitor 24" Full HD',
                'descripcion' => 'Monitor LED de 24 pulgadas con resolución 1920x1080.',
                'precio' => 700000,
                'cantidad' => 8,
                'categoria_id' => 2,
                'proveedor_id' => 1,
                'imagen_principal' => 'productos/monitor.jpg'
            ],
            [
                'nombre' => 'Teclado Mecánico RGB',
                'descripcion' => 'Teclado gaming con switches mecánicos y retroiluminación RGB.',
                'precio' => 250000,
                'cantidad' => 15,
                'categoria_id' => 2,
                'proveedor_id' => 2,
                'imagen_principal' => 'productos/teclado.jpg'
            ],
            [
                'nombre' => 'Mouse Gamer RGB',
                'descripcion' => 'Mouse gaming con sensor óptico de alta precisión y luces RGB personalizables.',
                'precio' => 150000,
                'cantidad' => 20,
                'categoria_id' => 2,
                'proveedor_id' => 2,
                'imagen_principal' => 'productos/mouse.jpg'
            ],
            [
                'nombre' => 'Laptop ProBook',
                'descripcion' => 'Laptop empresarial con Intel i5, 16GB RAM, 512GB SSD.',
                'precio' => 2500000,
                'cantidad' => 5,
                'categoria_id' => 1,
                'proveedor_id' => 1,
                'imagen_principal' => 'productos/laptop.jpg'
            ],
            [
                'nombre' => 'Router WiFi 6',
                'descripcion' => 'Router de última generación con soporte WiFi 6 y múltiples antenas.',
                'precio' => 180000,
                'cantidad' => 12,
                'categoria_id' => 4,
                'proveedor_id' => 2,
                'imagen_principal' => 'productos/router.jpg'
            ],
            [
                'nombre' => 'Procesador Intel i7',
                'descripcion' => 'Procesador de última generación para gaming y trabajo pesado.',
                'precio' => 1200000,
                'cantidad' => 6,
                'categoria_id' => 3,
                'proveedor_id' => 1,
                'imagen_principal' => 'productos/procesador.jpg'
            ],
            [
                'nombre' => 'Tarjeta Gráfica RTX 3070',
                'descripcion' => 'GPU gaming de alto rendimiento con Ray Tracing y DLSS.',
                'precio' => 2800000,
                'cantidad' => 4,
                'categoria_id' => 3,
                'proveedor_id' => 2,
                'imagen_principal' => 'productos/gpu.jpg'
            ],
            [
                'nombre' => 'SSD 1TB NVMe',
                'descripcion' => 'Unidad de estado sólido de alta velocidad con interfaz NVMe.',
                'precio' => 350000,
                'cantidad' => 10,
                'categoria_id' => 3,
                'proveedor_id' => 1,
                'imagen_principal' => 'productos/ssd.jpg'
            ],
            [
                'nombre' => 'Memoria RAM 16GB DDR4',
                'descripcion' => 'Módulo de memoria RAM de alta velocidad para gaming.',
                'precio' => 280000,
                'cantidad' => 15,
                'categoria_id' => 3,
                'proveedor_id' => 2,
                'imagen_principal' => 'productos/ram.jpg'
            ],
            [
                'nombre' => 'Switch 24 Puertos Gigabit',
                'descripcion' => 'Switch de red administrable con 24 puertos gigabit.',
                'precio' => 450000,
                'cantidad' => 8,
                'categoria_id' => 4,
                'proveedor_id' => 1,
                'imagen_principal' => 'productos/switch.jpg'
            ],
        ];

        foreach ($productos as $prod) {
            Producto::create([
                'nombre' => $prod['nombre'],
                'descripcion' => $prod['descripcion'],
                'precio' => $prod['precio'],
                'cantidad' => $prod['cantidad'],
                'categoria_id' => $prod['categoria_id'],
                'proveedor_id' => $prod['proveedor_id'],
                'imagen_principal' => $prod['imagen_principal'],
                'creado_por' => $admin->id
            ]);
        }

        // Llamar al seeder de imágenes de productos
        $this->call([
            ProductoImagenesSeeder::class,
        ]);
    }
}
