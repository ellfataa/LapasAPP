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
    /**
     * Menampilkan Dashboard Utama Admin
     */
    public function index()
    {
        // 1. Ambil Statistik Global
        $totalPengawas = User::where('role', 'pengawas')->count();
        $totalNarapidana = User::where('role', 'narapidana')->count();
        $totalAbsensi = AbsensiKegiatan::count();

        // 2. Ambil Daftar Pengguna
        $daftarPengawas = User::where('role', 'pengawas')->orderBy('nama', 'asc')->get();
        $daftarNarapidana = User::where('role', 'narapidana')->orderBy('nama', 'asc')->get();

        // 3. Ambil Seluruh Data Kinerja & Absensi untuk Manajemen Admin
        $semuaKinerja = KinerjaPk::with('pengawas')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
        $semuaAbsensi = AbsensiKegiatan::with(['narapidana', 'pengawas'])->orderBy('tanggal_waktu', 'desc')->get();

        return view('dashboard.admin', compact(
            'totalPengawas',
            'totalNarapidana',
            'totalAbsensi',
            'daftarPengawas',
            'daftarNarapidana',
            'semuaKinerja',
            'semuaAbsensi'
        ));
    }

    /**
     * Memperbarui Data Pengguna (Edit Akun & Reset Password)
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:users,nomor_induk,' . $id],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $id],
            'role' => ['required', 'in:admin,pengawas,narapidana'],
            'password' => ['nullable', 'min:8'], // Password opsional, hanya diisi jika ingin direset
        ]);

        $dataToUpdate = [
            'nama' => $request->nama,
            'nomor_induk' => $request->nomor_induk,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Jika admin mengisi kolom password, maka update passwordnya
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        return redirect()->back()->with('success', "Data akun atas nama {$user->nama} berhasil diperbarui.");
    }

    /**
     * Menghapus Pengguna (Pengawas / Narapidana)
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $namaUser = $user->nama;
        $roleUser = ucfirst($user->role);
        $user->delete();

        return redirect()->back()->with('success', "Data $roleUser atas nama $namaUser berhasil dihapus dari sistem.");
    }

    /**
     * Menghapus Data Penilaian Kinerja PK
     */
    public function destroyKinerja($id)
    {
        $kinerja = KinerjaPk::findOrFail($id);
        $kinerja->delete();

        return redirect()->back()->with('success', 'Data Penilaian Kinerja PK berhasil dihapus.');
    }

    /**
     * Menghapus Data Laporan Absensi Klien
     */
    public function destroyAbsensi($id)
    {
        $absensi = AbsensiKegiatan::findOrFail($id);

        // Hapus file gambar dari storage
        if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
            Storage::disk('public')->delete($absensi->bukti_file);
        }
        $absensi->delete();

        return redirect()->back()->with('success', 'Data Laporan Absensi Klien berhasil dihapus.');
    }
}
