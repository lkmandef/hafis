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
        Schema::create('tindak_lanjuts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apar_id')->constrained('apars')->cascadeOnDelete();
            $table->foreignId('pemeriksaan_id')->nullable()->constrained('pemeriksaans')->nullOnDelete();
            $table->foreignId('pemeriksaan_detail_id')->nullable()->constrained('pemeriksaan_details')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('deskripsi_temuan', 255)->nullable();
            $table->string('jenis_tindakan', 50); // perbaikan, pengisian_ulang, pembersihan, penggantian_komponen, pemeriksaan_ulang, lainnya
            $table->text('uraian_tindakan');
            $table->string('status', 30)->default('belum_ditindaklanjuti'); // belum_ditindaklanjuti, dalam_proses, selesai
            $table->string('prioritas', 20)->default('sedang'); // rendah, sedang, tinggi
            $table->string('petugas_pelaksana', 100);
            $table->date('tanggal_tindakan');
            $table->date('tanggal_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->string('foto_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjuts');
    }
};
