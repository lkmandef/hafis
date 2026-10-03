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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('tipe_target', 30)->default('semua'); // semua, gedung, custom
            $table->foreignId('gedung_id')->nullable()->constrained('gedungs')->nullOnDelete();
            $table->string('status', 30)->default('belum_dimulai'); // draft, belum_dimulai, berjalan, selesai, dibatalkan
            $table->text('deskripsi')->nullable();
            $table->string('dibuat_oleh', 100)->default('Admin K3RS');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
