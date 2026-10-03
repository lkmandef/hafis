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
        // 1. Tambahkan kolom referensi penggantian pada tabel apars
        Schema::table('apars', function (Blueprint $table) {
            $table->foreignId('replaced_by_apar_id')->nullable()->constrained('apars')->nullOnDelete();
            $table->foreignId('replaces_apar_id')->nullable()->constrained('apars')->nullOnDelete();
            $table->string('alasan_nonaktif', 100)->nullable();
            $table->date('tanggal_nonaktif')->nullable();
        });

        // 2. Buat tabel pencatatan historis penggantian unit APAR
        Schema::create('penggantian_apars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apar_lama_id')->constrained('apars')->cascadeOnDelete();
            $table->foreignId('apar_baru_id')->constrained('apars')->cascadeOnDelete();
            $table->date('tanggal_penggantian');
            $table->string('alasan', 50); // rusak, tidak_layak, kadaluarsa, hasil_pemeriksaan, penggantian_unit, lainnya
            $table->string('alasan_keterangan', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->string('petugas_nama', 100);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggantian_apars');

        Schema::table('apars', function (Blueprint $table) {
            $table->dropForeign(['replaced_by_apar_id']);
            $table->dropForeign(['replaces_apar_id']);
            $table->dropColumn([
                'replaced_by_apar_id',
                'replaces_apar_id',
                'alasan_nonaktif',
                'tanggal_nonaktif',
            ]);
        });
    }
};
