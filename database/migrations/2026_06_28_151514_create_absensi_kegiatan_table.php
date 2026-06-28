<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_kegiatan', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();

            // Relasi ke User (Klien)
            $table->foreignId('narapidana_id')->constrained('users')->cascadeOnDelete();

            // Relasi ke User (PK/Pengawas) - sudah digabung di sini agar tidak error
            $table->foreignId('pengawas_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('jenis_kegiatan');
            $table->date('tanggal_waktu'); // Sesuaikan dengan tipe data yang Anda pakai
            $table->string('bukti_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_kegiatan');
    }
};
