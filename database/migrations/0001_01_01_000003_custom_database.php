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
        Schema::create('invalid_participants', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('ci_number')->nullable()->comment('Carnet de identidad|CI');
            $table->timestamps();
        });
        Schema::create('participants', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('name')->nullable()->comment('Nombre|Nombre');
            $table->string('cellphone')->nullable()->comment('Teléfono|Teléfono');
            $table->date('birth_date')->nullable()->comment('Fecha de nacimiento|Fecha de nacimiento');
            $table->string('ci_number')->nullable()->comment('CI|CI');
            $table->enum('document_type', ['CI', 'PE', 'NIT'])->nullable()->comment('Tipo de documento|Tipo de documento');
            $table->string('ci_number_complement')->nullable()->comment('Complemento|Complemento');
            $table->string('ci_number_expedition')->nullable()->comment('Expedición|Expedición');
            $table->string('city')->nullable()->comment('Ciudad|Ciudad');
            $table->enum('status', ['registered'])->default('registered')->comment('Estado|Estado');
            $table->string('email')->nullable()->comment('Email|Email');
            $table->date('date')->nullable()->comment('Fecha de registro en sistema|Fecha de registro en sistema');
            $table->time('time')->nullable()->comment('Hora de registro en sistema|Hora de registro en sistema');
            $table->timestamps();
        });
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('participant_id')->nullable()->comment('Participante|Participante');
            $table->string('city_agency')->nullable()->comment('Agencia|Agencia');
            $table->string('name')->nullable()->comment('Nombre|Nombre');
            $table->string('sale_date')->nullable()->comment('Fecha de venta|Fecha de venta');
            $table->string('control_code')->nullable()->comment('Código de control|Código de control');
            $table->enum('business_unit', ['ECO', 'RET', 'ACE'])->nullable()->default('ECO')->comment('Unidad de control|Unidad de control');
            $table->integer('quantity_items')->default(0)->comment('Cantidad de items|Cantidad de items');
            $table->decimal('total_amount', 10, 2)->nullable()->comment('Monto total|Monto total');
            $table->string('invoice_url')->nullable()->comment('Url de factura|Url de factura');
            $table->integer('invoice_number')->nullable()->comment('Nro Factua|Nro Factura');
            $table->enum('sale_type', ['CONTADO', 'MINICUOTAS'])->nullable()->default('CONTADO');
            $table->boolean('active')->nullable()->default(1)->comment('¿Activo?|¿Activo?');
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('restrict')->onUpdate('cascade');
            $table->index(['participant_id']);
            $table->timestamps();
        });
        Schema::create('participant_coupons', function (Blueprint $table) {
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
        });
        Schema::create('sale_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('sale_id')->nullable()->comment('Venta|Venta');
            $table->string('name')->nullable()->comment('Nombre|Nombre');
            $table->integer('code_brand')->nullable()->comment('Código|Código'); // LISTADO DE MARCAS A CONSIDERAR: 404/10
            $table->string('name_brand')->nullable()->comment('Marca|Marca');
            $table->decimal('amount', 10, 2)->nullable()->comment('Monto|Monto');
            $table->boolean('active')->nullable()->default(1)->comment('¿Activo?|¿Activo?');
            $table->timestamps();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('restrict')->onUpdate('cascade');
            $table->index(['sale_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
