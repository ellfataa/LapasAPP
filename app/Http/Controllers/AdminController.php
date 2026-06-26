<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AbsensiKegiatan;
use Illuminate\Http\Request;

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

        return view('dashboard.admin', compact(
            'totalPengawas',
            'totalNarapidana',
            'totalAbsensi',
            'daftarPengawas',
            'daftarNarapidana'
        ));
    }

    /**
     * Menghapus Pengguna (Pengawas / Narapidana)
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        // Simpan nama dan role untuk pesan notifikasi
        $namaUser = $user->nama;
        $roleUser = ucfirst($user->role);

        $user->delete();

        return redirect()->back()->with('success', "Data $roleUser atas nama $namaUser berhasil dihapus dari sistem.");
    }
}
