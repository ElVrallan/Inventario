<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_images');
    }

    public function down(): void
    {
        // En caso de rollback, recreamos las tablas antiguas (por si acaso)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('image_path');
            $table->timestamps();
        });
    }
};
