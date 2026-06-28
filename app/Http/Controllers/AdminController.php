<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AbsensiKegiatan;
use App\Models\KinerjaPk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // =========================================================
    // 1. DASHBOARD UTAMA
    // =========================================================
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

    // =========================================================
    // 2. HALAMAN MANAJEMEN SPESIFIK
    // =========================================================
    public function pengawasIndex()
    {
        $daftarPengawas = User::where('role', 'pengawas')->orderBy('nama', 'asc')->get();
        return view('admin.pengawas.index', compact('daftarPengawas'));
    }

    public function narapidanaIndex() { return "Halaman Manajemen Narapidana/Klien (Tahap Pengembangan)"; }
    public function kinerjaIndex() { return "Halaman Manajemen Penilaian Kinerja PK (Tahap Pengembangan)"; }
    public function absensiIndex() { return "Halaman Manajemen Laporan Absensi Klien (Tahap Pengembangan)"; }


    // =========================================================
    // 3. FUNGSI CRUD & IMPORT EXCEL / CSV
    // =========================================================

    // FUNGSI: Tambah PK Manual
    public function storePengawas(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:users,nomor_induk'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ]);

        User::create([
            'nama' => $request->nama,
            'nomor_induk' => $request->nomor_induk,
            'email' => $request->email,
            'role' => 'pengawas',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Akun PK / Pengawas baru berhasil ditambahkan secara manual.');
    }

    // FUNGSI: Import PK via CSV (Bawaan PHP)
    public function importPengawas(Request $request)
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:csv,txt', 'max:5120']
        ], [
            'file_excel.mimes' => 'Mohon ubah file Excel Anda menjadi format .CSV terlebih dahulu sebelum diupload.'
        ]);

        $file = $request->file('file_excel');
        $fileHandle = fopen($file->getRealPath(), 'r');

        // Lewati baris pertama (Header: "NAMA LENGKAP")
        fgetcsv($fileHandle);

        $berhasil = 0;
        while (($row = fgetcsv($fileHandle)) !== false) {
            $nama = $row[0] ?? null;

            if (!empty($nama)) {
                // Generate NIP Sementara (Misal: PK + TahunBulanHari + 3 Angka Random)
                $nomorIndukSmt = '19' . date('ymd') . rand(100, 999);

                User::create([
                    'nama' => trim($nama),
                    'nomor_induk' => $nomorIndukSmt,
                    'role' => 'pengawas',
                    'password' => Hash::make('bapas123'), // Set Password Default
                ]);
                $berhasil++;
            }
        }
        fclose($fileHandle);

        return redirect()->back()->with('success', "Import CSV Berhasil! Sebanyak $berhasil akun PK ditambahkan otomatis. NIP digenerate acak & Password Default: bapas123");
    }

    // FUNGSI: Edit Data PK (dan User lain)
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:users,nomor_induk,' . $id],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $id],
            'role' => ['required', 'in:admin,pengawas,narapidana'],
            'password' => ['nullable', 'min:8'],
        ]);

        $dataToUpdate = $request->only(['nama', 'nomor_induk', 'email', 'role']);
        if ($request->filled('password')) $dataToUpdate['password'] = Hash::make($request->password);
        $user->update($dataToUpdate);

        return redirect()->back()->with('success', "Data akun atas nama {$user->nama} berhasil diperbarui.");
    }

    // FUNGSI: Hapus PK
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $namaUser = $user->nama;
        $user->delete();
        return redirect()->back()->with('success', "Akun atas nama $namaUser berhasil dihapus beserta datanya.");
    }

    public function destroyKinerja($id)
    {
        KinerjaPk::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Penilaian Kinerja PK berhasil dihapus.');
    }

    public function destroyAbsensi($id)
    {
        $absensi = AbsensiKegiatan::findOrFail($id);
        if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
            Storage::disk('public')->delete($absensi->bukti_file);
        }
        $absensi->delete();
        return redirect()->back()->with('success', 'Data Laporan Absensi Klien berhasil dihapus.');
    }
}
