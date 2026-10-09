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
        Schema::create('codes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('prize_id')->nullable()->comment('Premio asociado');
            $table->string('code', 50)->unique()->comment('Número de serie / Cupón');
            $table->string('product_code', 50)->nullable()->comment('Código de producto Dismac (ej. TC1000S)');
            $table->decimal('value', 10, 2)->nullable()->comment('Valor del cupón en Bs');
            $table->dateTime('expiration_date')->nullable()->comment('Fecha de vencimiento del cupón');
            $table->integer('days_valid')->default(90)->comment('Días de vigencia');
            $table->string('created_by')->nullable()->comment('Usuario creador en Dismac');
            $table->enum('status', ['available', 'used'])->default('available')->comment('Estado del código');
            $table->boolean('is_used')->default(false)->comment('¿Ya fue utilizado?');
            $table->dateTime('used_at')->nullable()->comment('Fecha y hora en que fue utilizado');
            $table->uuid('form_id')->nullable()->comment('Formulario ganador asociado');
            $table->timestamps();

            $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('set null');
            $table->foreign('form_id')->references('id')->on('forms')->onDelete('set null');

            $table->index('is_used');
            $table->index('status');
            $table->index('prize_id');
            $table->index('form_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes');
    }
};
