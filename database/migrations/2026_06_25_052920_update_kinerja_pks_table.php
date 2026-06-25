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
        Schema::table('kinerja_pks', function (Blueprint $table) {
            // Ubah kolom file menjadi TEXT agar muat JSON banyak file
            $table->text('litmas_file')->change();
            $table->text('pendampingan_file')->change();
            $table->text('pembimbingan_file')->change();
            $table->text('pengawasan_file')->change();

            // Pastikan kolom-kolom ini ada (seperti di struktur tabel Anda)
            // Jika kolom sudah ada, abaikan saja baris di bawah ini
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
