<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KinerjaPkController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\Auth\GoogleAuthController; // Nonaktifkan sementara

// 1. RUTE UTAMA: Otomatis arahkan sesuai status login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect('/login');
});

// Route Google Auth (SEMENTARA DINONAKTIFKAN)
// Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
// Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// Grup Route yang butuh Login
Route::middleware('auth')->group(function () {
    // RUTE TERMINAL (Solusi agar sistem Laravel tidak bingung)
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;
        if ($role === 'admin') return redirect()->route('dashboard.admin');
        if ($role === 'pengawas') return redirect()->route('dashboard.pengawas');
        return redirect()->route('dashboard.narapidana');
    })->name('dashboard');

    // ====================================================================
    // 1. ROUTE KHUSUS ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('dashboard.admin');

        // Manajemen PK/Pengawas
        Route::get('/admin/pengawas', [AdminController::class, 'pengawasIndex'])->name('admin.pengawas.index');
        Route::get('/admin/pengawas/create', [AdminController::class, 'pengawasCreate'])->name('admin.pengawas.create');
        Route::get('/admin/pengawas/{id}/edit', [AdminController::class, 'pengawasEdit'])->name('admin.pengawas.edit');
        Route::post('/admin/pengawas', [AdminController::class, 'storePengawas'])->name('admin.pengawas.store');
        Route::post('/admin/pengawas/import', [AdminController::class, 'importPengawas'])->name('admin.pengawas.import');

        // Manajemen Klien/Narapidana
        Route::get('/admin/narapidana', [AdminController::class, 'narapidanaIndex'])->name('admin.narapidana.index');
        Route::get('/admin/narapidana/create', [AdminController::class, 'narapidanaCreate'])->name('admin.narapidana.create');
        Route::get('/admin/narapidana/{id}/edit', [AdminController::class, 'narapidanaEdit'])->name('admin.narapidana.edit');
        Route::post('/admin/narapidana', [AdminController::class, 'storeNarapidana'])->name('admin.narapidana.store');
        Route::post('/admin/narapidana/import', [AdminController::class, 'importNarapidana'])->name('admin.narapidana.import');

        // Manajemen Rekap dan Pemetaan Data PK-Klien
        Route::get('/admin/rekap', [AdminController::class, 'rekapIndex'])->name('admin.rekap.index');
        Route::post('/admin/rekap/hubungkan', [AdminController::class, 'hubungkanPkKlien'])->name('admin.rekap.hubungkan');
        Route::post('/admin/rekap/lepas/{id}', [AdminController::class, 'lepasKlien'])->name('admin.rekap.lepas');

        // Manajemen Halaman Lain (Kinerja, Absensi)
        Route::get('/admin/kinerja', [AdminController::class, 'kinerjaIndex'])->name('admin.kinerja.index');
        Route::get('/admin/absensi', [AdminController::class, 'absensiIndex'])->name('admin.absensi.index');

        // Manajemen Pengguna secara umum (Aksi Update & Delete)
        Route::put('/admin/user/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/admin/user/{id}', [AdminController::class, 'destroyUser'])->name('admin.user.destroy');

        Route::delete('/admin/kinerja/{id}', [AdminController::class, 'destroyKinerja'])->name('admin.kinerja.destroy');
        Route::delete('/admin/absensi/{id}', [AdminController::class, 'destroyAbsensi'])->name('admin.absensi.destroy');
    });

    // ====================================================================
    // 2. ROUTE KHUSUS PK/PENGAWAS
    Route::middleware('role:pengawas')->group(function () {
        Route::get('/pengawas/dashboard', [AbsensiController::class, 'indexPengawas'])->name('dashboard.pengawas');
        Route::post('/pengawas/kinerja', [KinerjaPkController::class, 'store'])->name('kinerja-pk.store');

        // RUTE BARU AUTO-SAVE DRAFT KINERJA
        Route::post('/pengawas/save-draft', [AbsensiController::class, 'saveDraftPk'])->name('pengawas.save_draft');
    });

    // ====================================================================
    // 3. ROUTE KHUSUS KLIEN/NARAPIDANA
    Route::middleware('role:narapidana')->group(function () {
        Route::get('/narapidana/dashboard', [AbsensiController::class, 'indexNarapidana'])->name('dashboard.narapidana');
        Route::post('/narapidana/absensi', [AbsensiController::class, 'store'])->name('absensi.store');

        Route::get('/narapidana/absensi/{id}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('/narapidana/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
    });

    // ====================================================================
    // ROUTE PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
