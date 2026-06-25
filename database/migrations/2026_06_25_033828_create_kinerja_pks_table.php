<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kinerja_pks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pengawas_id')->constrained('users')->cascadeOnDelete();
        $table->integer('bulan');
        $table->integer('tahun');

        // Kategori Litmas
        $table->integer('litmas_kuota')->default(0);
        $table->integer('litmas_berhasil')->default(0);
        $table->string('litmas_file')->nullable();
        $table->string('litmas_link')->nullable();

        // Kategori Pendampingan
        $table->integer('pendampingan_kuota')->default(0);
        $table->integer('pendampingan_berhasil')->default(0);
        $table->string('pendampingan_file')->nullable();
        $table->string('pendampingan_link')->nullable();

        // Kategori Pembimbingan
        $table->integer('pembimbingan_kuota')->default(0);
        $table->integer('pembimbingan_berhasil')->default(0);
        $table->string('pembimbingan_file')->nullable();
        $table->string('pembimbingan_link')->nullable();

        // Kategori Pengawasan
        $table->integer('pengawasan_kuota')->default(0);
        $table->integer('pengawasan_berhasil')->default(0);
        $table->string('pengawasan_file')->nullable();
        $table->string('pengawasan_link')->nullable();

        // Hasil Akhir
        $table->decimal('rata_rata', 5, 2)->default(0);
        $table->string('predikat')->nullable(); // Baik Sekali, Baik, Cukup, Kurang

        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinerja_pks');
    }
};
