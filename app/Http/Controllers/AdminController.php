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

        $ringkasanPengawas = User::where('role', 'pengawas')->orderBy('created_at', 'desc')->take(5)->get();
        $ringkasanNarapidana = User::where('role', 'narapidana')->orderBy('created_at', 'desc')->take(5)->get();
        $ringkasanKinerja = KinerjaPk::with('pengawas')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->take(5)->get();
        $ringkasanAbsensi = AbsensiKegiatan::with(['narapidana', 'pengawas'])->orderBy('tanggal_waktu', 'desc')->take(5)->get();

        $currentSystemYear = date('Y');
        $availableYears = AbsensiKegiatan::selectRaw('YEAR(tanggal_waktu) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();

        if (!in_array($currentSystemYear, $availableYears)) {
            $availableYears[] = $currentSystemYear;
            rsort($availableYears);
        }

        $selectedYear = $request->input('year', $currentSystemYear);

        $absensiKalender = AbsensiKegiatan::with('narapidana')
            ->whereYear('tanggal_waktu', $selectedYear)
            ->get();

        return view('dashboard.admin', compact(
            'totalPengawas', 'totalNarapidana', 'totalAbsensi',
            'ringkasanPengawas', 'ringkasanNarapidana', 'ringkasanKinerja', 'ringkasanAbsensi',
            'availableYears', 'selectedYear', 'absensiKalender'
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
    public function pengawasEdit($id) {
        $pk = User::where('role', 'pengawas')->findOrFail($id);
        return view('admin.pengawas.edit', compact('pk'));
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

    public function narapidanaEdit($id) {
        $napi = User::where('role', 'narapidana')->findOrFail($id);
        return view('admin.narapidana.edit', compact('napi'));
    }

    public function storeNarapidana(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:users,nomor_induk'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ], [
            'nama.regex' => 'Nama Lengkap hanya boleh berisi huruf dan spasi.',
            'nomor_induk.regex' => 'Nomor Induk (NIK) hanya boleh berisi angka.',
            'nomor_induk.max' => 'Nomor Induk (NIK) maksimal 18 digit.',
            'nomor_induk.unique' => 'Nomor Induk (NIK) ini sudah terdaftar di sistem.',
            'password.min' => 'Password minimal harus 8 karakter.'
        ]);

        User::create([
            'nama' => trim($request->nama),
            'nomor_induk' => $request->nomor_induk,
            'email' => $request->email,
            'role' => 'narapidana',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.narapidana.index')->with('success', "Akun Klien / Narapidana atas nama {$request->nama} berhasil ditambahkan secara manual.");
    }


    // FUNGSI UMUM (CREATE PK, UPDATE SEMUA, DELETE SEMUA)
    public function storePengawas(Request $request) {
        $request->validate(['nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'], 'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:users,nomor_induk'], 'email' => ['nullable', 'email', 'max:255', 'unique:users,email'], 'password' => ['required', 'min:8']]);
        User::create(['nama' => trim($request->nama), 'nomor_induk' => $request->nomor_induk, 'email' => $request->email, 'role' => 'pengawas', 'password' => Hash::make($request->password)]);
        return redirect()->route('admin.pengawas.index')->with('success', "Akun PK atas nama {$request->nama} berhasil ditambahkan.");
    }

    public function importPengawas(Request $request) {
        $request->validate(['file_excel' => ['required', 'file', 'mimes:csv,xlsx,xls,txt', 'max:8192']]);
        try {
            $file = $request->file('file_excel'); $spreadsheet = IOFactory::load($file->getRealPath()); $worksheet = $spreadsheet->getActiveSheet(); $rows = $worksheet->toArray();
            $berhasil = 0;
            for ($i = 1; $i < count($rows); $i++) {
                $nama = $rows[$i][0] ?? null;
                if (!empty($nama) && trim($nama) != '') {
                    User::create(['nama' => trim($nama), 'nomor_induk' => '19' . date('ymd') . rand(1000, 9999), 'role' => 'pengawas', 'password' => Hash::make('bapas123')]);
                    $berhasil++;
                }
            }
            return redirect()->back()->with('success', "Import Berhasil! Sebanyak {$berhasil} akun PK ditambahkan otomatis.");
        } catch (\Exception $e) { return redirect()->back()->withErrors(['file_excel' => 'Gagal membaca file berkas Excel.']); }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate(['nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'], 'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:users,nomor_induk,' . $id], 'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $id], 'password' => ['nullable', 'min:8']]);
        $user->nama = trim($request->nama); $user->nomor_induk = $request->nomor_induk; $user->email = $request->email;
        if ($request->filled('password')) { $user->password = Hash::make($request->password); }
        $user->save();

        // Kembalikan ke halaman index masing-masing sesuai role user yang diubah
        $route = $user->role === 'pengawas' ? 'admin.pengawas.index' : 'admin.narapidana.index';
        return redirect()->route($route)->with('success', "Data akun atas nama {$user->nama} berhasil diperbarui.");
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $namaUser = $user->nama;

        // Pembersihan Otomatis Jika User = Pengawas(Hapus File Kinerja)
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

        // Pembersihan Otomatis Jika User = Narapidana(Hapus Foto Absensi)
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

    // MENU PENILAIAN KINERJA PK/PENGAWAS
    public function kinerjaIndex(Request $request)
    {
        // Ambil data kinerja beserta relasi user pengawas
        $query = KinerjaPk::with('pengawas')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        // Fitur Pencarian berdasarkan nama pengawas
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pengawas', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $semuaKinerja = $query->paginate(15);
        return view('admin.kinerja.index', compact('semuaKinerja'));
    }

    public function destroyKinerja($id)
    {
        $kinerja = KinerjaPk::findOrFail($id);

        foreach (['litmas', 'pendampingan', 'pembimbingan', 'pengawasan'] as $kat) {
            $files = json_decode($kinerja->{$kat . '_file'}, true);
            if (is_array($files)) {
                foreach ($files as $file) {
                    $path = is_array($file) ? ($file['path'] ?? '') : $file;
                    if ($path && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
        }

        $kinerja->delete();
        return redirect()->back()->with('success', 'Data penilaian kinerja PK berhasil dihapus beserta lampiran terkait.');
    }

    // MENU ABSENSI/LAPORAN WAJIB KLIEN
    public function absensiIndex(Request $request)
    {
        $query = AbsensiKegiatan::with(['narapidana', 'pengawas'])
            ->orderBy('tanggal_waktu', 'desc');

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

    public function destroyAbsensi($id)
    {
        $absensi = AbsensiKegiatan::findOrFail($id);

        if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
            Storage::disk('public')->delete($absensi->bukti_file);
        }

        $absensi->delete();
        return redirect()->back()->with('success', 'Data laporan absensi klien berhasil dihapus beserta bukti foto.');
    }
}
