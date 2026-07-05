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
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('nama');
            $table->string('nomor_induk')->unique(); // NIK/NIP/NRP
            $table->string('email')->nullable()->unique();
            $table->string('role'); // admin, pengawas, narapidana

            // TAMBAHAN: Kolom untuk menyimpan ID PK Pembimbing
            $table->unsignedBigInteger('pembimbing_id')->nullable();

            $table->string('password');
            $table->string('google_id')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // TAMBAHAN: Relasi Foreign Key
            // Jika PK (Pengawas) dihapus, maka klien tidak ikut terhapus, melainkan status pembimbingnya menjadi kosong (null)
            $table->foreign('pembimbing_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
