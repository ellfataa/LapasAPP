<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KinerjaPkController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AbsensiKegiatan;
use App\Models\KinerjaPk;

// 1. LANDING PAGE (HALAMAN DEPAN UTAMA)
Route::get('/', function () {
    $currentSystemYear  = date('Y');
    $currentSystemMonth = date('m');

    // STATISTIK UMUM (disamakan dengan AdminController@index)
    $totalPengawas   = User::where('role', 'pengawas')->count();
    $totalNarapidana = User::where('role', 'narapidana')->count();

    $totalKinerja = KinerjaPk::where('bulan', (int) $currentSystemMonth)
                              ->where('tahun', $currentSystemYear)
                              ->count();

    // --- FETCH DATA STATISTIK LANGSUNG DARI GOOGLE SHEETS (sama seperti AdminController) ---
    $klienBekerja      = 0;
    $klienBelumBekerja = 0;
    $persenBekerja     = '0%';

    $klienSudahApel = 0;
    $klienBelumApel = 0;
    $persenApel     = '0%';

    try {
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path('app/google-credentials.json'));
        $client->setScopes([\Google\Service\Sheets::SPREADSHEETS_READONLY]);
        $service = new \Google\Service\Sheets($client);

        $spreadsheetId = '1dMWij_J6P_lvC0H2x19DFpqCSjAMDFsCdeT_H1S61HE';

        // 1. Data Status Bekerja (F42:G43)
        $resBekerja = $service->spreadsheets_values->get($spreadsheetId, "'Rekap Data'!F42:G43");
        $valBekerja = $resBekerja->getValues();
        if (!empty($valBekerja)) {
            $klienBekerja      = $valBekerja[0][0] ?? 0;
            $klienBelumBekerja = $valBekerja[0][1] ?? 0;
            $persenBekerja     = $valBekerja[1][0] ?? '0%';
        }

        // 2. Data Apel Tahunan (J45:J47)
        $resApel = $service->spreadsheets_values->get($spreadsheetId, "'Rekap Data'!J45:J47");
        $valApel = $resApel->getValues();
        if (!empty($valApel)) {
            $klienSudahApel = $valApel[0][0] ?? 0;
            $klienBelumApel = $valApel[1][0] ?? 0;
            $persenApel     = $valApel[2][0] ?? '0%';
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Gagal fetch statistik welcome page dari Sheets: ' . $e->getMessage());
    }

    return view('welcome', compact(
        'totalPengawas', 'totalNarapidana', 'totalKinerja',
        'klienBekerja', 'klienBelumBekerja', 'persenBekerja',
        'klienSudahApel', 'klienBelumApel', 'persenApel'
    ));
});

// Grup Route yang butuh Login
Route::middleware('auth')->group(function () {

    // RUTE TERMINAL (Solusi agar sistem Laravel tidak bingung)
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;
        if ($role === 'admin') return redirect()->route('dashboard.admin');
        if ($role === 'pengawas') return redirect()->route('dashboard.pengawas');
        return redirect()->route('dashboard.narapidana');
    })->name('dashboard');

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
        // --- ROUTE CETAK PDF ---
        Route::get('/admin/kinerja/cetak-pdf', [AdminController::class, 'cetakKinerjaPdf'])->name('admin.kinerja.cetak_pdf');

        Route::get('/admin/absensi', [AdminController::class, 'absensiIndex'])->name('admin.absensi.index');

        // Manajemen Pengguna secara umum (Aksi Update & Delete)
        Route::put('/admin/user/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/admin/user/{id}', [AdminController::class, 'destroyUser'])->name('admin.user.destroy');

        Route::delete('/admin/kinerja/{id}', [AdminController::class, 'destroyKinerja'])->name('admin.kinerja.destroy');
        Route::delete('/admin/absensi/{id}', [AdminController::class, 'destroyAbsensi'])->name('admin.absensi.destroy');
    });

    // 2. ROUTE KHUSUS PK/PENGAWAS
    Route::middleware('role:pengawas')->group(function () {
        Route::get('/pengawas/dashboard', [AbsensiController::class, 'indexPengawas'])->name('dashboard.pengawas');
        Route::post('/pengawas/kinerja', [KinerjaPkController::class, 'store'])->name('kinerja-pk.store');

        // RUTE AUTO-SAVE DRAFT KINERJA
        Route::post('/pengawas/save-draft', [AbsensiController::class, 'saveDraftPk'])->name('pengawas.save_draft');
    });

    // 3. ROUTE KHUSUS KLIEN/NARAPIDANA
    Route::middleware('role:narapidana')->group(function () {
        Route::get('/narapidana/dashboard', [AbsensiController::class, 'indexNarapidana'])->name('dashboard.narapidana');
        Route::post('/narapidana/absensi', [AbsensiController::class, 'store'])->name('absensi.store');

        Route::get('/narapidana/absensi/{id}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('/narapidana/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
    });

    // ROUTE PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
