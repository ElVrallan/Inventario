<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->enum('tipo', ['entrada', 'salida']);
            $table->integer('cantidad');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('referencia_documento')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};