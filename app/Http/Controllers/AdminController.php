<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AbsensiKegiatan;
use App\Models\KinerjaPk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $totalPengawas = User::where('role', 'pengawas')->count();
        $totalNarapidana = User::where('role', 'narapidana')->count();
        $totalAbsensi = AbsensiKegiatan::count();
        $totalKinerja = KinerjaPk::count();

        // Variabel Default (Bulan & Tahun Saat Ini)
        $currentSystemYear = date('Y');
        $currentSystemMonth = date('m');

        // Tangkap Parameter Filter Kinerja (atau gunakan default)
        $kinerjaYear = $request->input('kinerja_year', $currentSystemYear);
        $kinerjaMonth = $request->input('kinerja_month', $currentSystemMonth);

        // Tangkap Parameter Filter Absensi (atau gunakan default)
        $absensiYear = $request->input('absensi_year', $currentSystemYear);
        $absensiMonth = $request->input('absensi_month', $currentSystemMonth);

        // Ambil Daftar Tahun yang Tersedia untuk Dropdown
        $availableYearsAbsensi = AbsensiKegiatan::selectRaw('YEAR(tanggal_waktu) as year')
            ->distinct()->pluck('year')->toArray();

        $availableYearsKinerja = KinerjaPk::select('tahun as year')
            ->distinct()->pluck('year')->toArray();

        $availableYears = array_unique(array_merge($availableYearsAbsensi, $availableYearsKinerja, [$currentSystemYear]));
        rsort($availableYears);

        // 1. Ambil 5 Laporan Kinerja PK Terbaru (Berdasarkan Filter Kinerja)
        $ringkasanKinerja = KinerjaPk::with('pengawas')
            ->where('tahun', $kinerjaYear)
            ->where('bulan', (int)$kinerjaMonth)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 2. Ambil 5 Laporan Absensi Terbaru (Berdasarkan Filter Absensi)
        $ringkasanAbsensi = AbsensiKegiatan::with(['narapidana', 'pengawas'])
            ->whereYear('tanggal_waktu', $absensiYear)
            ->whereMonth('tanggal_waktu', $absensiMonth)
            ->orderBy('tanggal_waktu', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalPengawas', 'totalNarapidana', 'totalAbsensi', 'totalKinerja',
            'ringkasanKinerja', 'ringkasanAbsensi',
            'availableYears', 'kinerjaYear', 'kinerjaMonth', 'absensiYear', 'absensiMonth'
        ));
    }

    // MANAJEMEN PK/PENGAWAS
    public function pengawasIndex(Request $request)
    {
        $query = User::where('role', 'pengawas');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }
        $daftarPengawas = $query->orderBy('nama', 'asc')->paginate(15);
        return view('admin.pengawas.index', compact('daftarPengawas'));
    }

    public function pengawasCreate() { return view('admin.pengawas.create'); }

    public function pengawasEdit(int $id) {
        $pk = User::where('role', 'pengawas')->findOrFail($id);
        return view('admin.pengawas.edit', compact('pk'));
    }

    public function storePengawas(Request $request) {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[\pL\s.,\'’()\/&-]+$/u'],
            'nomor_induk' => ['required', 'string', 'max:50', 'regex:/^[\pL\pN\s.,\'’()\/&-]+$/u', 'unique:users,nomor_induk'],
            'password' => ['required', 'min:8']
        ]);

        User::create([
            'nama' => trim($request->nama),
            'nomor_induk' => $request->nomor_induk,
            'role' => 'pengawas',
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('admin.pengawas.index')->with('success', "Akun PK atas nama {$request->nama} berhasil ditambahkan.");
    }

    public function importPengawas(Request $request) {
        $request->validate(['file_excel' => ['required', 'file', 'mimes:csv,xlsx,xls,txt', 'max:8192']]);
        try {
            $file = $request->file('file_excel');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $berhasilTambah = 0; $berhasilUpdate = 0;

            for ($i = 1; $i < count($rows); $i++) {
                $nama = trim($rows[$i][0] ?? '');
                $nipExcel = trim($rows[$i][1] ?? '');

                if (!empty($nama)) {
                    $existingUser = User::where('role', 'pengawas')->whereRaw('LOWER(nama) = ?', strtolower($nama))->first();

                    if ($existingUser) {
                        if (!empty($nipExcel) && $existingUser->nomor_induk !== $nipExcel) {
                            if (!User::where('nomor_induk', $nipExcel)->where('id', '!=', $existingUser->id)->exists()) {
                                $existingUser->update(['nomor_induk' => $nipExcel]);
                                $berhasilUpdate++;
                            }
                        }
                    } else {
                        $finalNip = !empty($nipExcel) ? $nipExcel : ('19' . date('ymd') . rand(1000, 9999));
                        if (!User::where('nomor_induk', $finalNip)->exists()) {
                            User::create(['nama' => $nama, 'nomor_induk' => $finalNip, 'role' => 'pengawas', 'password' => Hash::make('bapas123')]);
                            $berhasilTambah++;
                        }
                    }
                }
            }

            $pesan = "Import Selesai! Sebanyak {$berhasilTambah} akun PK ditambahkan";
            if ($berhasilUpdate > 0) $pesan .= " dan {$berhasilUpdate} diperbarui NIP-nya";
            return redirect()->back()->with('success', $pesan . ".");

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file_excel' => 'Gagal membaca file Excel. Pastikan Kolom A (Nama) dan B (NIP).']);
        }
    }


    // MANAJEMEN KLIEN/NARAPIDANA
    public function narapidanaIndex(Request $request)
    {
        $query = User::where('role', 'narapidana');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        $daftarNarapidana = $query->orderBy('nama', 'asc')->paginate(15);
        return view('admin.narapidana.index', compact('daftarNarapidana'));
    }

    public function narapidanaCreate() { return view('admin.narapidana.create'); }

    public function narapidanaEdit(int $id) {
        $napi = User::where('role', 'narapidana')->findOrFail($id);
        return view('admin.narapidana.edit', compact('napi'));
    }

    public function storeNarapidana(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[\pL\s.,\'’()\/&-]+$/u'],
            'nomor_induk' => ['required', 'string', 'max:50', 'regex:/^[\pL\pN\s.,\'’()\/&-]+$/u', 'unique:users,nomor_induk'],
            'password' => ['required', 'min:8'],
        ], [
            'nama.regex' => 'Nama Lengkap hanya boleh berisi huruf, spasi, dan tanda baca umum.',
            'nomor_induk.regex' => 'Nomor Induk hanya boleh berisi huruf, angka, spasi, dan tanda baca umum.',
            'nomor_induk.max' => 'Nomor Induk maksimal 50 karakter.',
            'nomor_induk.unique' => 'Nomor Induk ini sudah terdaftar di sistem.',
            'password.min' => 'Password minimal harus 8 karakter.'
        ]);

        User::create([
            'nama' => trim($request->nama),
            'nomor_induk' => $request->nomor_induk,
            'role' => 'narapidana',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.narapidana.index')->with('success', "Akun Klien/Narapidana atas nama {$request->nama} berhasil ditambahkan secara manual.");
    }

    public function importNarapidana(Request $request) {
        $request->validate(['file_excel' => ['required', 'file', 'mimes:csv,xlsx,xls,txt', 'max:8192']]);
        try {
            $file = $request->file('file_excel');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $berhasilTambah = 0; $berhasilUpdate = 0;

            $romawi = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'];
            $bulanRomawi = $romawi[date('n')];
            $tahun = date('Y');

            for ($i = 1; $i < count($rows); $i++) {
                $nama = trim($rows[$i][0] ?? '');
                $nikExcel = trim($rows[$i][1] ?? '');

                if (!empty($nama)) {
                    $existingUser = User::where('role', 'narapidana')->whereRaw('LOWER(nama) = ?', strtolower($nama))->first();

                    if ($existingUser) {
                        if (!empty($nikExcel) && $existingUser->nomor_induk !== $nikExcel) {
                            if (!User::where('nomor_induk', $nikExcel)->where('id', '!=', $existingUser->id)->exists()) {
                                $existingUser->update(['nomor_induk' => $nikExcel]);
                                $berhasilUpdate++;
                            }
                        }
                    } else {
                        $nomorAcak = rand(100, 999);
                        $finalNik = !empty($nikExcel) ? $nikExcel : ("{$nomorAcak}/PB/{$bulanRomawi}/{$tahun}");

                        if (!User::where('nomor_induk', $finalNik)->exists()) {
                            User::create([
                                'nama' => $nama,
                                'nomor_induk' => $finalNik,
                                'role' => 'narapidana',
                                'password' => Hash::make('bapas123')
                            ]);
                            $berhasilTambah++;
                        }
                    }
                }
            }

            $pesan = "Import Selesai! Sebanyak {$berhasilTambah} akun Klien ditambahkan";
            if ($berhasilUpdate > 0) $pesan .= " dan {$berhasilUpdate} diperbarui NIK/No.Registrasinya";
            return redirect()->back()->with('success', $pesan . ".");

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file_excel' => 'Gagal membaca file Excel. Pastikan Kolom A berisi Nama dan Kolom B berisi NIK/No.Registrasi.']);
        }
    }


    // FUNGSI UMUM UPDATE & DELETE (Berlaku untuk PK & Klien)
    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[\pL\s.,\'’()\/&-]+$/u'],
            'nomor_induk' => ['required', 'string', 'max:50', 'regex:/^[\pL\pN\s.,\'’()\/&-]+$/u', 'unique:users,nomor_induk,' . $id],
            'password' => ['nullable', 'min:8']
        ]);

        $user->nama = trim($request->nama);
        $user->nomor_induk = $request->nomor_induk;

        if ($request->filled('password')) { $user->password = Hash::make($request->password); }
        $user->save();

        $route = $user->role === 'pengawas' ? 'admin.pengawas.index' : 'admin.narapidana.index';
        return redirect()->route($route)->with('success', "Data akun atas nama {$user->nama} berhasil diperbarui.");
    }

    public function destroyUser(int $id)
    {
        $user = User::findOrFail($id);
        $namaUser = $user->nama;

        if ($user->role === 'pengawas') {
            $kinerjas = KinerjaPk::where('pengawas_id', $id)->get();
            foreach ($kinerjas as $kinerja) {
                foreach (['litmas', 'pendampingan', 'pembimbingan', 'pengawasan'] as $kat) {
                    $files = json_decode($kinerja->{$kat.'_file'}, true);
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            $path = is_array($file) ? ($file['path'] ?? '') : $file;
                            if ($path && Storage::disk('public')->exists($path)) { Storage::disk('public')->delete($path); }
                        }
                    }
                }
                $kinerja->delete();
            }
        }

        if ($user->role === 'narapidana') {
            $absensis = AbsensiKegiatan::where('narapidana_id', $id)->get();
            foreach ($absensis as $absensi) {
                if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
                    Storage::disk('public')->delete($absensi->bukti_file);
                }
                $absensi->delete();
            }
        }

        $user->delete();
        return redirect()->back()->with('success', "Akun atas nama {$namaUser} beserta seluruh berkas miliknya berhasil dihapus bersih.");
    }

    // MENU REKAP DATA & PEMETAAN
    public function rekapIndex(Request $request)
    {
        $queryPk = User::where('role', 'pengawas')->withCount('klienBimbingan');

        if ($request->filled('search_pk')) {
            $queryPk->where('nama', 'like', "%{$request->search_pk}%");
        }
        $daftarPk = $queryPk->orderBy('nama', 'asc')->paginate(15, ['*'], 'pk_page');

        $semuaPk = User::where('role', 'pengawas')->orderBy('nama', 'asc')->get();
        $semuaKlien = User::where('role', 'narapidana')->orderBy('nama', 'asc')->get();

        return view('admin.rekap.index', compact('daftarPk', 'semuaPk', 'semuaKlien'));
    }

    public function hubungkanPkKlien(Request $request)
    {
        $request->validate([
            'pk_id' => 'required|exists:users,id',
            'klien_ids' => 'required|array',
            'klien_ids.*' => 'exists:users,id'
        ]);

        $pk = User::where('role', 'pengawas')->findOrFail($request->pk_id);
        User::whereIn('id', $request->klien_ids)->update(['pembimbing_id' => $pk->id]);

        return redirect()->back()->with('success', count($request->klien_ids) . " Klien berhasil dihubungkan di bawah pengawasan PK: {$pk->nama}.");
    }

    public function lepasKlien(int $klien_id)
    {
        $klien = User::where('role', 'narapidana')->findOrFail($klien_id);
        $klien->update(['pembimbing_id' => null]);
        return redirect()->back()->with('success', "Klien {$klien->nama} berhasil dilepas dari pengawasan PK-nya.");
    }

    // MENU PENILAIAN KINERJA & ABSENSI
    public function kinerjaIndex(Request $request)
    {
        $query = KinerjaPk::with('pengawas')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pengawas', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }
        $semuaKinerja = $query->paginate(15);
        return view('admin.kinerja.index', compact('semuaKinerja'));
    }

    public function destroyKinerja(int $id)
    {
        $kinerja = KinerjaPk::findOrFail($id);
        foreach (['litmas', 'pendampingan', 'pembimbingan', 'pengawasan'] as $kat) {
            $files = json_decode($kinerja->{$kat . '_file'}, true);
            if (is_array($files)) {
                foreach ($files as $file) {
                    $path = is_array($file) ? ($file['path'] ?? '') : $file;
                    if ($path && Storage::disk('public')->exists($path)) { Storage::disk('public')->delete($path); }
                }
            }
        }
        $kinerja->delete();
        return redirect()->back()->with('success', 'Data penilaian kinerja PK berhasil dihapus beserta lampiran terkait.');
    }

    public function absensiIndex(Request $request)
    {
        $query = AbsensiKegiatan::with(['narapidana', 'pengawas'])->orderBy('tanggal_waktu', 'desc');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('narapidana', function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%");
                })->orWhere('jenis_kegiatan', 'like', "%{$search}%");
            });
        }
        $semuaAbsensi = $query->paginate(15);
        return view('admin.absensi.index', compact('semuaAbsensi'));
    }

    public function destroyAbsensi(int $id)
    {
        $absensi = AbsensiKegiatan::findOrFail($id);
        if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
            Storage::disk('public')->delete($absensi->bukti_file);
        }
        $absensi->delete();
        return redirect()->back()->with('success', 'Data laporan absensi klien berhasil dihapus beserta bukti foto.');
    }
}
