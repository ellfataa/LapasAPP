<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pastikan engine tabel menggunakan InnoDB untuk mendukung Foreign Key
        Schema::create('kinerja_pks', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();

            // 2. Gunakan unsignedBigInteger agar tipe data cocok dengan $table->id() milik users
            $table->unsignedBigInteger('pengawas_id');

            $table->unsignedTinyInteger('bulan'); // Gunakan tinyInt karena bulan hanya 1-12
            $table->year('tahun');                // Gunakan tipe data year agar lebih spesifik

            // Kategori Litmas
            $table->integer('litmas_kuota')->default(0);
            $table->integer('litmas_berhasil')->default(0);
            $table->json('litmas_file')->nullable(); // Disarankan JSON jika menyimpan banyak file
            $table->text('litmas_link')->nullable(); // Gunakan text untuk link agar lebih fleksibel

            // ==========================================
            // Kategori Pendampingan DIHAPUS
            // ==========================================

            // Kategori Pembimbingan
            $table->integer('pembimbingan_kuota')->default(0);
            $table->integer('pembimbingan_berhasil')->default(0);
            $table->json('pembimbingan_detail')->nullable(); // Menyimpan JSON status kerja klien
            $table->json('pembimbingan_file')->nullable();
            $table->text('pembimbingan_link')->nullable();

            // Kategori Pengawasan
            $table->integer('pengawasan_kuota')->default(0);
            $table->integer('pengawasan_berhasil')->default(0);
            $table->json('pengawasan_file')->nullable();
            $table->text('pengawasan_link')->nullable();

            // Hasil Akhir
            $table->decimal('rata_rata', 5, 2)->default(0.00);
            $table->string('predikat', 50)->nullable();

            $table->timestamps();

            // 3. Relasi Foreign Key dengan constraint yang tepat
            $table->foreign('pengawas_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinerja_pks');
    }
}; 
