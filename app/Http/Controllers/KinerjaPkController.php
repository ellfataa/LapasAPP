<?php

namespace App\Http\Controllers;

use App\Models\KinerjaPk;
use App\Models\User;
use App\Models\AbsensiKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KinerjaPkController extends Controller
{
    public function store(Request $request)
    {
        $userId = Auth::id();

        // 1. Kunci Periode ke Bulan & Tahun Berjalan Secara Real-Time
        $bulanSekarang = date('m');
        $tahunSekarang = date('Y');

        // 2. Validasi Strict: 1 Kali Submit Per Bulan
        $sudahSubmit = KinerjaPk::where('pengawas_id', $userId)
            ->where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->exists();

        if ($sudahSubmit) {
            return redirect()->back()->withErrors(['kinerja' => 'Anda sudah mengirimkan formulir penilaian kinerja untuk bulan ini. Laporan kinerja hanya dapat dilakukan 1 kali per bulan.']);
        }

        // 3. Validasi Input Dasar
        $request->validate([
            'litmas_kuota' => ['required', 'numeric', 'min:0'],
            'litmas_berhasil' => ['required', 'numeric', 'min:0'],
            'pembimbingan_klien' => ['nullable', 'array'],
        ], [
            'required' => 'Data target dan keberhasilan wajib diisi.'
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
        $dataToSave['litmas_file'] = null; // Dikosongkan
        $dataToSave['litmas_link'] = null; // Dikosongkan

        $litmasPersen = ($litmasKuota > 0) ? ($litmasBerhasil / $litmasKuota) * 100 : 0;
        $totalPersen += $litmasPersen;

        // --- B. KALKULASI PEMBIMBINGAN ---
        $klienData = $request->input('pembimbingan_klien', []);
        $totalKlien = count($klienData);
        $jumlahBekerja = 0;
        $detailKlien = [];

        foreach ($klienData as $idKlien => $status) {
            if ($status === 'bekerja') {
                $jumlahBekerja++;
            }
            $detailKlien[] = [
                'klien_id' => $idKlien,
                'status' => $status
            ];
        }

        $dataToSave['pembimbingan_kuota'] = $totalKlien;
        $dataToSave['pembimbingan_berhasil'] = $jumlahBekerja;
        $dataToSave['pembimbingan_detail'] = json_encode($detailKlien);
        $dataToSave['pembimbingan_file'] = null; // Dikosongkan
        $dataToSave['pembimbingan_link'] = null; // Dikosongkan

        $pembimbinganPersen = ($totalKlien > 0) ? ($jumlahBekerja / $totalKlien) * 100 : 0;
        $totalPersen += $pembimbinganPersen;

        // --- C. KALKULASI PENGAWASAN (OTOMATIS SERVER-SIDE) ---
        // Mengambil data murni dari database agar tidak bisa diakali via Inspect Element
        $jumlahKlienDiampu = User::where('role', 'narapidana')->where('pembimbing_id', $userId)->count();
        $jumlahKlienAbsen = AbsensiKegiatan::where('pengawas_id', $userId)
            ->whereMonth('tanggal_waktu', $bulanSekarang)
            ->whereYear('tanggal_waktu', $tahunSekarang)
            ->distinct('narapidana_id')
            ->count('narapidana_id');

        $dataToSave['pengawasan_kuota'] = $jumlahKlienDiampu;
        $dataToSave['pengawasan_berhasil'] = $jumlahKlienAbsen;
        $dataToSave['pengawasan_file'] = null; // Dikosongkan
        $dataToSave['pengawasan_link'] = null; // Dikosongkan

        $pengawasanPersen = ($jumlahKlienDiampu > 0) ? ($jumlahKlienAbsen / $jumlahKlienDiampu) * 100 : 0;
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

        // 4. Simpan ke Database
        KinerjaPk::create($dataToSave);

        // 5. Bersihkan Draft Auto-Save
        User::where('id', $userId)->update(['kinerja_draft' => null]);

        $namaBulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanTeks = $namaBulanIndo[(int)$bulanSekarang - 1];

        return redirect()->back()->with('success', "Kinerja bulan {$bulanTeks} {$tahunSekarang} berhasil disimpan secara permanen. Predikat Anda: " . $dataToSave['predikat']);
    }
}
