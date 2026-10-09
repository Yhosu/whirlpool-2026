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
            $table->uuid('code_id')->nullable()->after('prize_id')->comment('Cupón asignado de la tabla codes');
            $table->foreign('code_id')->references('id')->on('codes')->onDelete('set null');
            $table->index('code_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropForeign(['code_id']);
            $table->dropIndex(['code_id']);
            $table->dropColumn('code_id');
        });
    }
};
