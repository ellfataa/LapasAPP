<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KinerjaPkController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleAuthController;

// 1. RUTE UTAMA: Otomatis arahkan sesuai status login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard'); // Lempar ke terminal pengatur role
    }
    return redirect('/login');
});

// Route Google Auth (Tidak perlu login)
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// Grup Route yang butuh Login
Route::middleware('auth')->group(function () {

    // ====================================================================
    // RUTE TERMINAL (Solusi agar sistem Laravel tidak bingung)
    // ====================================================================
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;
        if ($role === 'admin') return redirect()->route('dashboard.admin');
        if ($role === 'pengawas') return redirect()->route('dashboard.pengawas');
        return redirect()->route('dashboard.narapidana');
    })->name('dashboard');


    // ====================================================================
    // 1. ROUTE KHUSUS ADMIN
    // ====================================================================
    Route::middleware('role:admin')->group(function () {
        // Halaman Utama Dashboard Admin
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('dashboard.admin');

        // Hapus Data Pengguna
        Route::delete('/admin/user/{id}', [AdminController::class, 'destroyUser'])->name('admin.user.destroy');
    });

    // ====================================================================
    // 2. ROUTE KHUSUS PENGAWAS / PK
    // ====================================================================
    Route::middleware('role:pengawas')->group(function () {
        Route::get('/pengawas/dashboard', [AbsensiController::class, 'indexPengawas'])->name('dashboard.pengawas');

        Route::post('/pengawas/kinerja', [KinerjaPkController::class, 'store'])->name('kinerja-pk.store');
    });

    // ====================================================================
    // 3. ROUTE KHUSUS MANTAN NAPI / NARAPIDANA
    // ====================================================================
    Route::middleware('role:narapidana')->group(function () {
        Route::get('/narapidana/dashboard', [AbsensiController::class, 'indexNarapidana'])->name('dashboard.narapidana');
        Route::post('/narapidana/absensi', [AbsensiController::class, 'store'])->name('absensi.store');

        Route::get('/narapidana/absensi/{id}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('/narapidana/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
    });

    // ====================================================================
    // ROUTE PROFILE
    // ====================================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
