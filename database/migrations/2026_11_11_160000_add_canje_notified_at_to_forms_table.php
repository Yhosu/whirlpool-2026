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
            if (! Schema::hasColumn('forms', 'canje_notified_at')) {
                $table->timestamp('canje_notified_at')->nullable()->after('updated_at');
            }
        });

        // Marcar todos los canjes antiguos ya existentes como notificados para evitar spam inicial
        \Illuminate\Support\Facades\DB::table('forms')
            ->whereIn('status', ['used', 'delivered'])
            ->whereNull('canje_notified_at')
            ->update(['canje_notified_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (Schema::hasColumn('forms', 'canje_notified_at')) {
                $table->dropColumn('canje_notified_at');
            }
        });
    }
};

