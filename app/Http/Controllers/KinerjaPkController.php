<?php

namespace App\Http\Controllers;

use App\Models\KinerjaPk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KinerjaPkController extends Controller
{
    public function store(Request $request)
    {
        $userId = Auth::id();

        // 1. Cek Data Existing
        $existingData = KinerjaPk::where('pengawas_id', $userId)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        // 2. Susun Aturan Validasi
        $rules = [
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer'],
            'pembimbingan_klien' => ['nullable', 'array'],
            'pengawasan_kuota' => ['required', 'numeric', 'min:0'],
            'pengawasan_berhasil' => ['required', 'numeric', 'min:0'],
        ];

        // PERUBAHAN: Hanya Pengawasan yang membutuhkan File & Link
        $kategoriList = ['pengawasan'];

        foreach ($kategoriList as $kategori) {
            if (!$existingData || empty(json_decode($existingData->{"{$kategori}_file"}, true))) {
                $rules["{$kategori}_file"] = ['required', 'array'];
            } else {
                $rules["{$kategori}_file"] = ['nullable', 'array'];
            }
            $rules["{$kategori}_file.*"] = ['file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'];
            $rules["{$kategori}_link"] = ['nullable', 'url', 'max:255'];
        }

        $request->validate($rules, [
            'required' => 'Kolom Kuota/Status, Berhasil, dan File Bukti wajib diisi.',
            'numeric' => 'Kolom harus berupa angka.',
            'mimes' => 'Format file bukti harus berupa JPG, JPEG, PNG, atau PDF.',
        ]);

        $dataToSave = [];
        $totalPersen = 0;

        // --- 1. KALKULASI LITMAS ---
        $litmasKuota = $request->input('litmas_kuota', 0);
        $litmasBerhasil = $request->input('litmas_berhasil', 0);
        $dataToSave['litmas_kuota'] = $litmasKuota;
        $dataToSave['litmas_berhasil'] = $litmasBerhasil;
        $dataToSave['litmas_file'] = null;
        $dataToSave['litmas_link'] = null;

        $litmasPersen = ($litmasKuota > 0) ? ($litmasBerhasil / $litmasKuota) * 100 : 0;
        $totalPersen += $litmasPersen;

        // --- 2. KALKULASI PEMBIMBINGAN ---
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

        // PERUBAHAN: Set null untuk file dan link pembimbingan karena sudah dihapus dari form
        $dataToSave['pembimbingan_file'] = null;
        $dataToSave['pembimbingan_link'] = null;

        $pembimbinganPersen = ($totalKlien > 0) ? ($jumlahBekerja / $totalKlien) * 100 : 0;
        $totalPersen += $pembimbinganPersen;

        // --- 3. KALKULASI PENGAWASAN ---
        $pengawasKuota = $request->input('pengawasan_kuota', 0);
        $pengawasBerhasil = $request->input('pengawasan_berhasil', 0);
        $dataToSave['pengawasan_kuota'] = $pengawasKuota;
        $dataToSave['pengawasan_berhasil'] = $pengawasBerhasil;
        $dataToSave['pengawasan_link'] = $request->input('pengawasan_link');

        $pengawasanPersen = ($pengawasKuota > 0) ? ($pengawasBerhasil / $pengawasKuota) * 100 : 0;
        $totalPersen += $pengawasanPersen;

        // --- PROSES UPLOAD FILE BUKTI (Hanya untuk Pengawasan) ---
        foreach ($kategoriList as $kategori) {
            if ($request->hasFile("{$kategori}_file")) {
                $files = $request->file("{$kategori}_file");
                $fileData = [];

                if ($existingData && $existingData->{"{$kategori}_file"}) {
                    $oldFiles = json_decode($existingData->{"{$kategori}_file"}, true);
                    if (is_array($oldFiles)) {
                        foreach ($oldFiles as $oldFile) {
                            $oldPath = is_array($oldFile) ? ($oldFile['path'] ?? '') : $oldFile;
                            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                                Storage::disk('public')->delete($oldPath);
                            }
                        }
                    }
                }

                foreach ($files as $file) {
                    $path = $file->store('bukti_kinerja_pk', 'public');
                    $fileData[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path
                    ];
                }
                $dataToSave["{$kategori}_file"] = json_encode($fileData);
            }
        }

        // --- HITUNG RATA-RATA AKHIR ---
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

        KinerjaPk::updateOrCreate(
            ['pengawas_id' => $userId, 'bulan' => $request->bulan, 'tahun' => $request->tahun],
            $dataToSave
        );

        // TAMBAHAN: Hapus draf dari database karena form sudah berhasil disubmit
        \App\Models\User::where('id', $userId)->update(['kinerja_draft' => null]);

        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanNama = $namaBulan[$request->bulan - 1] ?? $request->bulan;

        return redirect()->back()->with('success', 'Kinerja bulan ' . $bulanNama . ' ' . $request->tahun . ' berhasil disimpan. Predikat: ' . $dataToSave['predikat']);
    }
}
