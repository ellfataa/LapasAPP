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

    public function pengawasIndex(Request $request)
    {
        $query = User::where('role', 'pengawas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        $daftarPengawas = $query->orderBy('nama', 'asc')->get();
        return view('admin.pengawas.index', compact('daftarPengawas'));
    }
    // TAMBAHAN: Halaman Form Tambah Manual PK
    public function pengawasCreate()
    {
        return view('admin.pengawas.create');
    }

    // TAMBAHAN: Halaman Form Edit PK
    public function pengawasEdit($id)
    {
        $pk = User::where('role', 'pengawas')->findOrFail($id);
        return view('admin.pengawas.edit', compact('pk'));
    }

    public function narapidanaIndex() { return "Halaman Manajemen Narapidana/Klien (Tahap Pengembangan)"; }
    public function kinerjaIndex() { return "Halaman Manajemen Penilaian Kinerja PK (Tahap Pengembangan)"; }
    public function absensiIndex() { return "Halaman Manajemen Laporan Absensi Klien (Tahap Pengembangan)"; }

    // =========================================================
    // FUNGSI CRUD SELESAI & VALID
    // =========================================================

    // 1. KREASI: Tambah PK Manual
    public function storePengawas(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:users,nomor_induk'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ], [
            'nama.regex' => 'Nama Lengkap hanya boleh berisi huruf dan spasi.',
            'nomor_induk.regex' => 'Nomor Induk (NIP) hanya boleh berisi angka.',
            'nomor_induk.max' => 'Nomor Induk (NIP) maksimal 18 digit.',
            'nomor_induk.unique' => 'Nomor Induk (NIP) ini sudah terdaftar di sistem.',
            'password.min' => 'Password minimal harus 8 karakter.'
        ]);

        User::create([
            'nama' => trim($request->nama),
            'nomor_induk' => $request->nomor_induk,
            'email' => $request->email,
            'role' => 'pengawas',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', "Akun PK / Pengawas atas nama {$request->nama} berhasil ditambahkan secara manual.");
    }

    // 2. PEMBARUAN: Update Data Akun PK & Reset Password
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:users,nomor_induk,' . $id],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'min:8'],
        ], [
            'nama.regex' => 'Nama Lengkap hanya boleh berisi huruf dan spasi.',
            'nomor_induk.regex' => 'Nomor Induk (NIP) hanya boleh berisi angka.',
            'nomor_induk.max' => 'Nomor Induk (NIP) maksimal 18 digit.',
            'nomor_induk.unique' => 'Nomor Induk (NIP) ini sudah digunakan oleh akun lain.',
            'password.min' => 'Password baru minimal harus 8 karakter.'
        ]);

        $user->nama = trim($request->nama);
        $user->nomor_induk = $request->nomor_induk;
        $user->email = $request->email;

        // Reset password hanya jika diisi oleh Admin
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', "Data akun PK atas nama {$user->nama} berhasil diperbarui.");
    }

    // 3. PENGHAPUSAN: Hapus Akun PK Permanen
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $namaUser = $user->nama;

        // Menghapus file laporan kinerja terkait sebelum menghapus user
        $kinerjas = KinerjaPk::where('pengawas_id', $id)->get();
        foreach ($kinerjas as $kinerja) {
            foreach (['litmas', 'pendampingan', 'pembimbingan', 'pengawasan'] as $kat) {
                $files = json_decode($kinerja->{$kat.'_file'}, true);
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
        }

        $user->delete();
        return redirect()->back()->with('success', "Akun PK atas nama {$namaUser} beserta seluruh data laporan kinerjanya berhasil dihapus permanen.");
    }

    public function importPengawas(Request $request)
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:csv,xlsx,xls,txt', 'max:8192']
        ]);

        try {
            $file = $request->file('file_excel');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $berhasil = 0;
            for ($i = 1; $i < count($rows); $i++) {
                $nama = $rows[$i][0] ?? null;
                if (!empty($nama) && trim($nama) != '') {
                    $nomorIndukSmt = '19' . date('ymd') . rand(1000, 9999);
                    User::create([
                        'nama' => trim($nama),
                        'nomor_induk' => $nomorIndukSmt,
                        'role' => 'pengawas',
                        'password' => Hash::make('bapas123'),
                    ]);
                    $berhasil++;
                }
            }
            return redirect()->back()->with('success', "Import Berhasil! Sebanyak {$berhasil} akun PK ditambahkan otomatis.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file_excel' => 'Gagal membaca file berkas Excel.']);
        }
    }

    public function destroyKinerja($id) { KinerjaPk::findOrFail($id)->delete(); return redirect()->back()->with('success', 'Data Penilaian Kinerja PK berhasil dihapus.'); }
    public function destroyAbsensi($id) { $absensi = AbsensiKegiatan::findOrFail($id); if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) { Storage::disk('public')->delete($absensi->bukti_file); } $absensi->delete(); return redirect()->back()->with('success', 'Data Laporan Absensi Klien berhasil dihapus.'); }
}
