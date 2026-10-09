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
            $table->smallInteger('periode_tahun')->nullable()->after('jadwal_id');
            $table->tinyInteger('periode_bulan')->nullable()->after('periode_tahun');

            $table->index(['apar_id', 'periode_tahun', 'periode_bulan'], 'pemeriksaan_apar_periode_index');
            $table->index(['periode_tahun', 'periode_bulan'], 'pemeriksaan_periode_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaans', function (Blueprint $table) {
            $table->dropIndex('pemeriksaan_apar_periode_index');
            $table->dropIndex('pemeriksaan_periode_index');
            $table->dropColumn(['periode_tahun', 'periode_bulan']);
        });
    }
};
