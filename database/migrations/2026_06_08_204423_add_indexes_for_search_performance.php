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
        Schema::table('participants', function (Blueprint $table) {
            $table->index('ci_number');
            $table->index('cellphone');
        });

        Schema::table('mundial_coupons', function (Blueprint $table) {
            $table->index('code');
            $table->index('status');
            $table->index('active');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropIndex(['ci_number']);
            $table->dropIndex(['cellphone']);
        });

        Schema::table('mundial_coupons', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropIndex(['status']);
            $table->dropIndex(['active']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['active']);
        });
    }
};
