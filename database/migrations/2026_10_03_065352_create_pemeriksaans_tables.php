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
        Schema::create('pemeriksaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apar_id')->constrained('apars')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwals')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pemeriksa_nama', 100);
            $table->dateTime('tanggal_pemeriksaan');
            $table->string('hasil_keseluruhan', 30); // baik, perlu_tindakan
            $table->string('status', 20)->default('selesai'); // draft, selesai
            $table->text('catatan')->nullable();
            $table->string('foto_path')->nullable();
            $table->timestamps();
        });

        Schema::create('pemeriksaan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemeriksaan_id')->constrained('pemeriksaans')->cascadeOnDelete();
            $table->string('item_nama', 150);
            $table->string('hasil', 30); // baik, tidak_baik, tidak_diperiksa
            $table->string('catatan', 255)->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_details');
        Schema::dropIfExists('pemeriksaans');
    }
};
