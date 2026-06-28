<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    // BAGIAN 1: FITUR MANTAN NAPI / NARAPIDANA
    public function indexNarapidana(Request $request)
    {
        $userId = Auth::id();

        // Ambil daftar user yang memiliki role 'pengawas' untuk dilempar ke dropdown form
        $daftarPengawas = User::where('role', 'pengawas')->get();

        $availableYears = AbsensiKegiatan::where('narapidana_id', $userId)
            ->selectRaw('YEAR(tanggal_waktu) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $currentSystemYear = date('Y');

        if (!in_array($currentSystemYear, $availableYears)) {
            $availableYears[] = $currentSystemYear;
            rsort($availableYears);
        }

        $selectedYear = $request->input('year', $currentSystemYear);

        $riwayat = AbsensiKegiatan::with('pengawas')
            ->where('narapidana_id', $userId)
            ->whereYear('tanggal_waktu', $selectedYear)
            ->orderBy('tanggal_waktu', 'desc')
            ->get();

        return view('dashboard.narapidana', compact('riwayat', 'availableYears', 'selectedYear', 'daftarPengawas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'        => ['required', 'date', 'before_or_equal:today'],
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'pengawas_id'    => ['required', 'exists:users,id'],
            'bukti_file'     => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ], [
            'tanggal.before_or_equal' => 'Tanggal kegiatan tidak boleh melebihi hari ini.',
            'pengawas_id.required'    => 'Anda wajib memilih Pengawas Pembimbing.',
            'bukti_file.max'          => 'Ukuran file gambar maksimal adalah 10MB.',
            'bukti_file.image'        => 'File yang diunggah harus berupa gambar (JPEG, PNG, JPG).',
        ]);

        $path = $request->file('bukti_file')->store('bukti_kegiatan', 'public');

        AbsensiKegiatan::create([
            'narapidana_id'  => Auth::id(),
            'pengawas_id'    => $request->pengawas_id,
            'tanggal_waktu'  => $request->tanggal,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'bukti_file'     => $path,
        ]);

        return redirect()->back()->with('success', 'Absensi dan bukti kegiatan berhasil dikirim!');
    }

    public function edit($id)
    {
        $absensi = AbsensiKegiatan::where('narapidana_id', Auth::id())->findOrFail($id);
        $daftarPengawas = User::where('role', 'pengawas')->get(); // Ambil list pengawas untuk form edit

        return view('dashboard.edit_absensi_narapidana', compact('absensi', 'daftarPengawas'));
    }

    public function update(Request $request, $id)
    {
        $absensi = AbsensiKegiatan::where('narapidana_id', Auth::id())->findOrFail($id);

        $request->validate([
            'tanggal'        => ['required', 'date', 'before_or_equal:today'],
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'pengawas_id'    => ['required', 'exists:users,id'],
            'bukti_file'     => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ]);

        $dataToUpdate = [
            'tanggal_waktu'  => $request->tanggal,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'pengawas_id'    => $request->pengawas_id,
        ];

        if ($request->hasFile('bukti_file')) {
            if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
                Storage::disk('public')->delete($absensi->bukti_file);
            }

            $path = $request->file('bukti_file')->store('bukti_kegiatan', 'public');
            $dataToUpdate['bukti_file'] = $path;
        }

        $absensi->update($dataToUpdate);

        return redirect()->route('dashboard.narapidana')->with('success', 'Data kegiatan berhasil diperbarui!');
    }

    // BAGIAN 2: FITUR PK/PENGAWAS
    public function indexPengawas(Request $request)
    {
        $userId = Auth::id();

        $query = AbsensiKegiatan::with('narapidana')->where('pengawas_id', $userId);

        // 1. Ambil ketersediaan Tahun (Juga harus difilter berdasarkan pengawas ini)
        $availableYears = AbsensiKegiatan::where('pengawas_id', $userId)
            ->selectRaw('YEAR(tanggal_waktu) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $currentSystemYear = date('Y');
        if (!in_array($currentSystemYear, $availableYears)) {
            $availableYears[] = $currentSystemYear;
            rsort($availableYears);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('narapidana', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal_waktu', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal_waktu', $request->year);
        }

        $semuaAbsensi = $query->orderBy('tanggal_waktu', 'desc')->get();

        $riwayatKinerja = \App\Models\KinerjaPk::where('pengawas_id', $userId)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('dashboard.pengawas', compact('semuaAbsensi', 'availableYears', 'riwayatKinerja'));
    }
}
