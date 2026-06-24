<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;

Route::get('/', function () {
    return view('auth.login'); // Langsung arahkan halaman utama ke form login
});

// Grup Route yang butuh Login
Route::middleware('auth')->group(function () {

    // Route khusus Admin dan Pengawas
    Route::middleware('role:admin,pengawas')->group(function () {
        // PERBAIKAN: Diarahkan ke controller agar membawa data laporan dari database
        Route::get('/petugas/dashboard', [AbsensiController::class, 'indexPetugas'])->name('dashboard.petugas');
    });

    // Route khusus Narapidana
    Route::middleware('role:narapidana')->group(function () {
        Route::get('/narapidana/dashboard', [AbsensiController::class, 'indexNarapidana'])->name('dashboard.narapidana');
        Route::post('/narapidana/absensi', [AbsensiController::class, 'store'])->name('absensi.store');

        // Route untuk fitur Edit
        Route::get('/narapidana/absensi/{id}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('/narapidana/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
    });

    // Route Profile bawaan Breeze (Biarkan saja, berguna untuk ganti password)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
