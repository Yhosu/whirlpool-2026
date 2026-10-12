<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->uuid('sale_item_id')->nullable()->after('sale_id')->comment('Ítem de venta asociado');
            $table->foreign('sale_item_id')->references('id')->on('sale_items')->onDelete('set null');
            $table->index('sale_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropForeign(['sale_item_id']);
            $table->dropIndex(['sale_item_id']);
            $table->dropColumn('sale_item_id');
        });
    }
};
