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
        Schema::create('prizes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 20, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('branchs', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('name')->nullable();
            $table->text('address')->nullable();
            $table->text('schedule')->nullable();
            $table->enum('city', ['Chuquisaca', 'Cochabamba', 'El Alto', 'La Paz', 'Montero', 'Santa Cruz', 'Tarija'])->nullable()->comment('Ciudad|Ciudad');
            $table->enum('channel', ['RETAIL', 'MAYORISTA', 'ECOMMERCE'])->nullable();
            $table->timestamps();

            $table->index('city');
            $table->index('channel');
        });
        Schema::create('forms', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('code', 50)->nullable()->comment('Código cupón|Código cupón');
            $table->string('name')->nullable()->comment('Nombre|Nombre');
            $table->string('ci_number')->nullable()->comment('CI|CI');
            $table->string('cellphone')->nullable()->comment('Teléfono|Teléfono');
            $table->string('invoice_image')->nullable()->comment('Imagen|Imagen');
            $table->string('invoice_url')->nullable()->comment('URL Imagen|URL Imagen');
            $table->string('control_code')->nullable()->comment('Código de control|Código de control');
            $table->enum('city', ['Chuquisaca', 'Cochabamba', 'El Alto', 'La Paz', 'Montero', 'Santa Cruz', 'Tarija'])->nullable()->comment('Ciudad|Ciudad');
            $table->enum('status', ['registered', 'pending', 'invalidated', 'delivered', 'used'])->default('registered')->comment('Estado|Estado');
            $table->enum('type', ['dismac', 'informal-market'])->nullable()->default('informal-market');
            $table->uuid('sale_id')->nullable()->comment('Venta|Venta');
            $table->uuid('participant_id')->nullable()->comment('Participante|Participante');
            $table->uuid('branch_id')->nullable()->comment('Sucursal|Sucursal');
            $table->uuid('prize_id')->nullable()->comment('Premio|Premio');
            $table->foreignId('user_id')->nullable()->comment('Usuario que marcó')->constrained('users')->onDelete('set null');
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branchs')->onDelete('cascade');
            $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('cascade');
            $table->timestamps();

            $table->index('city');
            $table->index('status');
            $table->index('sale_id');
            $table->index('branch_id');
            $table->index('prize_id');
            $table->index('user_id');
        });

        Schema::create('prize_branch', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('prize_id')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branchs')->onDelete('cascade');

            $table->index('prize_id');
            $table->index('branch_id');
            $table->unique(['prize_id', 'branch_id']);
        });

        Schema::create('prize_stock_history', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('prize_id')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->integer('quantity');
            $table->enum('type', ['addition', 'subtraction', 'adjustment'])->default('addition');
            $table->uuid('form_id')->nullable()->comment('Formulario relacionado');
            $table->foreignId('user_id')->nullable()->comment('Usuario responsable')->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branchs')->onDelete('cascade');
            $table->foreign('form_id')->references('id')->on('forms')->onDelete('set null');

            $table->index('prize_id');
            $table->index('branch_id');
            $table->index('form_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_stock_history');
        Schema::dropIfExists('prize_branch');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('branchs');
        Schema::dropIfExists('prizes');
    }
};
