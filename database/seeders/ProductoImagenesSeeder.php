<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductoImagenesSeeder extends Seeder
{
    public function run(): void
    {
        // Ejemplo: generar 3 imágenes por producto para productos 1..5
        $now = Carbon::now();

        $inserts = [];
        for ($productoId = 1; $productoId <= 2; $productoId++) {
            for ($i = 1; $i <= 3; $i++) {
                $inserts[] = [
                    'producto_id'  => $productoId,
                    // patrón id_num.extension, por ejemplo: 1_1.jpg
                    'ruta'         => "productos/{$productoId}_{$i}.jpg",
                    // marcar la primera como principal
                    'es_principal' => ($i === 1) ? true : false,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
        }

        // Insert masivo
        DB::table('producto_imagenes')->insert($inserts);
    }
}
