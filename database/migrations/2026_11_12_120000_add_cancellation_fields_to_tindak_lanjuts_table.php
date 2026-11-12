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
        Schema::table('tindak_lanjuts', function (Blueprint $table) {
            $table->text('alasan_pembatalan')->nullable()->after('catatan');
            $table->foreignId('dibatalkan_oleh_id')
                ->nullable()
                ->after('alasan_pembatalan')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('dibatalkan_at')->nullable()->after('dibatalkan_oleh_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tindak_lanjuts', function (Blueprint $table) {
            $table->dropForeign(['dibatalkan_oleh_id']);
            $table->dropColumn(['alasan_pembatalan', 'dibatalkan_oleh_id', 'dibatalkan_at']);
        });
    }
};
