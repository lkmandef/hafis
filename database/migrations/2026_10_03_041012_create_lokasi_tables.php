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
        // 1. Tabel Gedung
        Schema::create('gedungs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 120)->unique();
            $table->text('keterangan')->nullable();
            $table->string('status', 20)->default('aktif');
            $table->timestamps();
        });

        // 2. Tabel Lantai
        Schema::create('lantais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gedung_id')->constrained('gedungs')->cascadeOnDelete();
            $table->integer('nomor_lantai');
            $table->string('nama', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['gedung_id', 'nomor_lantai']);
        });

        // 3. Tabel Ruangan / Area
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lantai_id')->constrained('lantais')->cascadeOnDelete();
            $table->string('kode', 50)->nullable();
            $table->string('nama', 150);
            $table->string('kategori', 60)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['lantai_id', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangans');
        Schema::dropIfExists('lantais');
        Schema::dropIfExists('gedungs');
    }
};
