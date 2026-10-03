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
        Schema::create('apars', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nomor_register', 100)->nullable();
            $table->string('qr_code', 100)->unique();
            $table->foreignId('ruangan_id')->constrained('ruangans')->cascadeOnDelete();
            $table->string('posisi_detail', 255)->nullable();
            $table->string('jenis_media', 50);
            $table->string('kapasitas', 30);
            $table->string('merek', 100)->nullable();
            $table->string('tipe_model', 100)->nullable();
            $table->string('status', 30)->default('baik'); // baik, perhatian, rusak, tidak_aktif
            $table->date('tanggal_pemasangan')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->date('tanggal_pemeriksaan_terakhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apars');
    }
};
