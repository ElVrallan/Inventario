<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            // Add producto_nombre to keep audit trail even if product is deleted
            $table->string('producto_nombre')->nullable()->after('producto_id');

            // Change fecha from date to datetime
            // Use raw SQL because some DB drivers don't support changing column type via blueprint
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE movimientos_inventario MODIFY fecha DATETIME NOT NULL");
            } else {
                // Fallback: drop and add column (risky), but attempt Blueprint change
                $table->dateTime('fecha')->change();
            }
        });

        // Backfill producto_nombre from productos when possible
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("UPDATE movimientos_inventario m JOIN productos p ON p.id = m.producto_id SET m.producto_nombre = p.nombre");
        } else {
            // Generic SQL for other DBs (may need adjustments)
            DB::table('movimientos_inventario')
                ->leftJoin('productos', 'productos.id', '=', 'movimientos_inventario.producto_id')
                ->whereNotNull('productos.nombre')
                ->update(['movimientos_inventario.producto_nombre' => DB::raw('productos.nombre')]);
        }
    }

    public function down()
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            // revert fecha back to date
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE movimientos_inventario MODIFY fecha DATE NOT NULL");
            } else {
                $table->date('fecha')->change();
            }

            $table->dropColumn('producto_nombre');
        });
    }
};
