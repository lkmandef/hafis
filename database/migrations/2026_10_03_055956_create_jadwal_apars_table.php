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
        Schema::create('jadwal_apars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
            $table->foreignId('apar_id')->constrained('apars')->cascadeOnDelete();
            $table->string('status_pemeriksaan', 30)->default('belum_diperiksa'); // belum_diperiksa, sudah_diperiksa
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_id', 'apar_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_apars');
    }
};
