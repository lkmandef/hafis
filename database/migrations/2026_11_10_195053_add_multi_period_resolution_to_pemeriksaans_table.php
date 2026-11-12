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
        Schema::table('pemeriksaans', function (Blueprint $table) {
            $table->foreignId('pemeriksaan_induk_id')
                ->nullable()
                ->after('jadwal_id')
                ->constrained('pemeriksaans')
                ->nullOnDelete();

            $table->string('tipe_penyelesaian', 30)
                ->default('tepat_waktu')
                ->after('hasil_keseluruhan');

            $table->index('pemeriksaan_induk_id', 'pemeriksaan_induk_index');
            $table->index(['apar_id', 'tipe_penyelesaian'], 'pemeriksaan_apar_tipe_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaans', function (Blueprint $table) {
            $table->dropIndex('pemeriksaan_induk_index');
            $table->dropIndex('pemeriksaan_apar_tipe_index');
            $table->dropConstrainedForeignId('pemeriksaan_induk_id');
            $table->dropColumn('tipe_penyelesaian');
        });
    }
};
