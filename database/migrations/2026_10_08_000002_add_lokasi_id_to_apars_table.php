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
        Schema::table('apars', function (Blueprint $table) {
            $table->foreignId('lokasi_id')->nullable()->after('qr_code')->constrained('lokasis')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apars', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lokasi_id');
        });
    }
};
