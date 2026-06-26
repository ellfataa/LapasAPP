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
        Schema::table('absensi_kegiatan', function (Blueprint $table) {
            // Menambahkan kolom pengawas_id dan merelasikannya ke tabel users
            $table->foreignId('pengawas_id')->nullable()->after('narapidana_id')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('absensi_kegiatan', function (Blueprint $table) {
            $table->dropForeign(['pengawas_id']);
            $table->dropColumn('pengawas_id');
        });
    }
};
