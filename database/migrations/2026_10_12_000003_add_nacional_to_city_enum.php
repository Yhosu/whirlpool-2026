<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ampliar el enum de ciudad en branchs, forms y participants para incluir 'Nacional' (MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `branchs` MODIFY COLUMN `city` ENUM('Chuquisaca', 'Cochabamba', 'El Alto', 'La Paz', 'Montero', 'Santa Cruz', 'Tarija', 'Nacional') NULL COMMENT 'Ciudad|Ciudad'");
            DB::statement("ALTER TABLE `forms` MODIFY COLUMN `city` ENUM('Chuquisaca', 'Cochabamba', 'El Alto', 'La Paz', 'Montero', 'Santa Cruz', 'Tarija', 'Nacional') NULL COMMENT 'Ciudad|Ciudad'");
            DB::statement("ALTER TABLE `participants` MODIFY COLUMN `city` ENUM('Chuquisaca', 'Cochabamba', 'El Alto', 'La Paz', 'Montero', 'Santa Cruz', 'Tarija', 'Nacional') NULL COMMENT 'Ciudad|Ciudad'");
        }

        // 2. Asociar las sucursales que manejan el stock promocional a la ciudad 'Nacional'
        Branch::whereIn('name', ['CashBack Mayoristas', 'Central Dismac'])
            ->orWhere('channel', 'ECOMMERCE')
            ->orWhere('channel', 'MAYORISTA')
            ->update(['city' => 'Nacional']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Branch::where('city', 'Nacional')
            ->update(['city' => 'Santa Cruz']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `branchs` MODIFY COLUMN `city` ENUM('Chuquisaca', 'Cochabamba', 'El Alto', 'La Paz', 'Montero', 'Santa Cruz', 'Tarija') NULL COMMENT 'Ciudad|Ciudad'");
            DB::statement("ALTER TABLE `forms` MODIFY COLUMN `city` ENUM('Chuquisaca', 'Cochabamba', 'El Alto', 'La Paz', 'Montero', 'Santa Cruz', 'Tarija') NULL COMMENT 'Ciudad|Ciudad'");
            DB::statement("ALTER TABLE `participants` MODIFY COLUMN `city` ENUM('Chuquisaca', 'Cochabamba', 'El Alto', 'La Paz', 'Montero', 'Santa Cruz', 'Tarija') NULL COMMENT 'Ciudad|Ciudad'");
        }
    }
};
