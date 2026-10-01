<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mundial_coupons', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('sale_id')->nullable()->comment('Venta|Venta');
            $table->string('prize_id')->nullable()->comment('Premio|Premio');
            $table->string('participant_id')->nullable()->comment('Participante|Participante');
            $table->enum('business_unit', ['ECO', 'RET', 'ACE'])->nullable()->default('ECO')->comment('Unidad de negocio|Unidad de negocio');
            $table->string('code', 50)->nullable()->comment('Código cupón|Código cupón');
            $table->enum('status', ['exchanged', 'created'])->nullable()->default('created')->comment('Estado|Estado');
            $table->boolean('active')->nullable()->default(1)->comment('¿Activo?|¿Activo?');

            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('restrict')->onUpdate('cascade');

            $table->timestamps();

            $table->index(['participant_id', 'sale_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mundial_coupons');
    }
};
