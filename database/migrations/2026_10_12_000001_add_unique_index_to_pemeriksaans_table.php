<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a partial unique index on (apar_id, periode_tahun, periode_bulan)
     * enforcing database-level uniqueness strictly for monthly inspections
     * while preserving legacy inspections where periode_tahun and periode_bulan are NULL.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS pemeriksaan_apar_periode_unique ON pemeriksaans (apar_id, periode_tahun, periode_bulan) WHERE periode_tahun IS NOT NULL AND periode_bulan IS NOT NULL');
        } else {
            DB::statement('CREATE UNIQUE INDEX pemeriksaan_apar_periode_unique ON pemeriksaans (apar_id, periode_tahun, periode_bulan)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS pemeriksaan_apar_periode_unique');
    }
};
