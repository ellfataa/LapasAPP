<?php

namespace App\Http\Controllers;

use App\Models\KinerjaPk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KinerjaPkController extends Controller
{
    public function store(Request $request)
    {
        $userId = Auth::id();

        // 1. Kunci Periode ke Bulan & Tahun Berjalan
        $bulanSekarang = date('m');
        $tahunSekarang = date('Y');

        // 2. Validasi Input Dasar
        $request->validate([
            'litmas_kuota' => ['required', 'numeric', 'min:0'],
            'litmas_berhasil' => ['required', 'numeric', 'min:0'],
            'pembimbingan_kuota' => ['required', 'numeric', 'min:0'],
            'pembimbingan_berhasil' => ['required', 'numeric', 'min:0'],
            'pengawasan_kuota' => ['required', 'numeric', 'min:0'],
            'pengawasan_berhasil' => ['required', 'numeric', 'min:0'],
        ], [
            'required' => 'Data kinerja dari Spreadsheet belum terisi/terdeteksi.'
        ]);

        $dataToSave = [
            'pengawas_id' => $userId,
            'bulan'       => $bulanSekarang,
            'tahun'       => $tahunSekarang,
        ];

        $totalPersen = 0;

        // --- A. KALKULASI LITMAS ---
        $litmasKuota = $request->input('litmas_kuota', 0);
        $litmasBerhasil = $request->input('litmas_berhasil', 0);
        $dataToSave['litmas_kuota'] = $litmasKuota;
        $dataToSave['litmas_berhasil'] = $litmasBerhasil;
        $dataToSave['litmas_file'] = null;
        $dataToSave['litmas_link'] = null;

        $litmasPersen = ($litmasKuota > 0) ? ($litmasBerhasil / $litmasKuota) * 100 : 0;
        $totalPersen += $litmasPersen;

        // --- B. KALKULASI PEMBIMBINGAN ---
        $pembimbinganKuota = $request->input('pembimbingan_kuota', 0);
        $pembimbinganBerhasil = $request->input('pembimbingan_berhasil', 0);
        $dataToSave['pembimbingan_kuota'] = $pembimbinganKuota;
        $dataToSave['pembimbingan_berhasil'] = $pembimbinganBerhasil;
        $dataToSave['pembimbingan_detail'] = null;
        $dataToSave['pembimbingan_file'] = null;
        $dataToSave['pembimbingan_link'] = null;

        $pembimbinganPersen = ($pembimbinganKuota > 0) ? ($pembimbinganBerhasil / $pembimbinganKuota) * 100 : 0;
        $totalPersen += $pembimbinganPersen;

        // --- C. KALKULASI PENGAWASAN ---
        $pengawasanKuota = $request->input('pengawasan_kuota', 0);
        $pengawasanBerhasil = $request->input('pengawasan_berhasil', 0);
        $dataToSave['pengawasan_kuota'] = $pengawasanKuota;
        $dataToSave['pengawasan_berhasil'] = $pengawasanBerhasil;
        $dataToSave['pengawasan_file'] = null;
        $dataToSave['pengawasan_link'] = null;

        $pengawasanPersen = ($pengawasanKuota > 0) ? ($pengawasanBerhasil / $pengawasanKuota) * 100 : 0;
        $totalPersen += $pengawasanPersen;


        // --- D. HITUNG RATA-RATA AKHIR & PREDIKAT ---
        $dataToSave['rata_rata'] = round($totalPersen / 3, 1);

        if ($dataToSave['rata_rata'] >= 91) {
            $dataToSave['predikat'] = 'Sangat Baik';
        } elseif ($dataToSave['rata_rata'] >= 81) {
            $dataToSave['predikat'] = 'Baik';
        } elseif ($dataToSave['rata_rata'] >= 70) {
            $dataToSave['predikat'] = 'Cukup';
        } elseif ($dataToSave['rata_rata'] >= 60) {
            $dataToSave['predikat'] = 'Kurang';
        } else {
            $dataToSave['predikat'] = 'Sangat Kurang';
        }

        // 4. Update Data Real-time ke Database
        KinerjaPk::updateOrCreate(
            ['pengawas_id' => $userId, 'bulan' => $bulanSekarang, 'tahun' => $tahunSekarang],
            $dataToSave
        );

        $namaBulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanTeks = $namaBulanIndo[(int)$bulanSekarang - 1];

        return redirect()->back()->with('success', "Laporan Kinerja bulan {$bulanTeks} {$tahunSekarang} berhasil disinkronkan.");
    }
}
