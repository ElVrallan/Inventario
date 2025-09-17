<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Aquí llamamos a nuestro seeder de Admin
        $this->call(AdminUserSeeder::class);
        $this->call(ProductoSeeder::class);

    }
}
