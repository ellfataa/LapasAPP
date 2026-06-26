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

        // 1. Cek Data Existing (Apakah bulan & tahun ini sudah pernah diisi?)
        $existingData = KinerjaPk::where('pengawas_id', $userId)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        // 2. Susun Aturan Validasi
        $rules = [
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer'],
            'litmas_berhasil' => ['required', 'numeric', 'min:0'],
            'pendampingan_kuota' => ['required', 'numeric', 'min:0'],
            'pendampingan_berhasil' => ['required', 'numeric', 'min:0'],
            'pembimbingan_kuota' => ['required', 'numeric', 'min:0'],
            'pembimbingan_berhasil' => ['required', 'numeric', 'min:0'],
            'pengawasan_kuota' => ['required', 'numeric', 'min:0'],
            'pengawasan_berhasil' => ['required', 'numeric', 'min:0'],
        ];

        $kategoriList = ['litmas', 'pendampingan', 'pembimbingan', 'pengawasan'];

        foreach ($kategoriList as $kategori) {
            // Jika belum ada data lama, FILE WAJIB DIUNGGAH
            if (!$existingData || empty(json_decode($existingData->{"{$kategori}_file"}, true))) {
                $rules["{$kategori}_file"] = ['required', 'array'];
            } else {
                $rules["{$kategori}_file"] = ['nullable', 'array'];
            }
            // Validasi format dan ukuran file
            $rules["{$kategori}_file.*"] = ['file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'];

            // TAMBAHAN: Validasi untuk Link G-Drive (Opsional tapi harus berupa URL yang valid jika diisi)
            $rules["{$kategori}_link"] = ['nullable', 'url', 'max:255'];
        }

        // 3. Eksekusi Validasi Ketat
        $request->validate($rules, [
            'required' => 'Kolom Kuota, Berhasil, dan File Bukti wajib diisi (File wajib diunggah untuk form baru).',
            'numeric' => 'Kolom harus berupa angka.',
            'min' => 'Nilai tidak boleh kurang dari 0.',
            'mimes' => 'Format file bukti harus berupa JPG, JPEG, PNG, atau PDF.',
            'max' => 'Ukuran file maksimal adalah 10MB per file.',
            'url' => 'Format link G-Drive/Spreadsheet tidak valid (harus diawali http:// atau https://).'
        ]);

        $dataToSave = [];
        $totalPersen = 0;

        foreach ($kategoriList as $kategori) {
            // Kunci kuota Litmas wajib 12 dari sisi Server
            $kuota = ($kategori === 'litmas') ? 12 : $request->input("{$kategori}_kuota", 0);
            $berhasil = $request->input("{$kategori}_berhasil", 0);

            $dataToSave["{$kategori}_kuota"] = $kuota;
            $dataToSave["{$kategori}_berhasil"] = $berhasil;

            // TAMBAHAN: Simpan link G-Drive
            $dataToSave["{$kategori}_link"] = $request->input("{$kategori}_link");

            // Kalkulasi
            $persen = ($kuota > 0) ? ($berhasil / $kuota) * 100 : 0;
            $totalPersen += $persen;

            // Proses Upload File
            if ($request->hasFile("{$kategori}_file")) {
                $files = $request->file("{$kategori}_file");
                $fileData = [];

                // Hapus file lama untuk mencegah penumpukan data sampah di server
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

        // Hitung Rata-Rata Akhir & Predikat
        $dataToSave['rata_rata'] = round($totalPersen / 4, 1);

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

        // Simpan ke Database
        KinerjaPk::updateOrCreate(
            ['pengawas_id' => $userId, 'bulan' => $request->bulan, 'tahun' => $request->tahun],
            $dataToSave
        );

        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanNama = $namaBulan[$request->bulan - 1] ?? $request->bulan;

        return redirect()->back()->with('success', 'Kinerja bulan ' . $bulanNama . ' ' . $request->tahun . ' berhasil disimpan. Predikat: ' . $dataToSave['predikat']);
    }
}
