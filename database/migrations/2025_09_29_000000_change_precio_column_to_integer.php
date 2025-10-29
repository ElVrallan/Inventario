<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // WARNING: make a backup before running. This converts precio -> INT.
        // Adjust the SQL if you prefer UNSIGNED or BIGINT.
        DB::statement("ALTER TABLE `productos` MODIFY `precio` INT NOT NULL DEFAULT 0");
    }

    public function down()
    {
        // Revert to decimal(10,2). Adjust precision/scale if your previous schema differed.
        DB::statement("ALTER TABLE `productos` MODIFY `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00");
    }
};
