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
            $table->decimal('koordinat_x', 5, 2)->nullable()->after('posisi_detail');
            $table->decimal('koordinat_y', 5, 2)->nullable()->after('koordinat_x');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apars', function (Blueprint $table) {
            $table->dropColumn(['koordinat_x', 'koordinat_y']);
        });
    }
};
