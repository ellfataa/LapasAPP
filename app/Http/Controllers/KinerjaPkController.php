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
        // Validasi dasar
        $request->validate([
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer'],
        ]);

        $userId = Auth::id();
        $kategoriList = ['litmas', 'pendampingan', 'pembimbingan', 'pengawasan'];
        $dataToSave = [];
        $totalPersen = 0;

        // Cek apakah data bulan dan tahun ini sudah ada (untuk hapus file lama jika diupdate)
        $existingData = KinerjaPk::where('pengawas_id', $userId)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        foreach ($kategoriList as $kategori) {
            $kuota = $request->input("{$kategori}_kuota", 0);
            $berhasil = $request->input("{$kategori}_berhasil", 0);

            // Sesuaikan nama kolom dengan struktur database Anda
            $dataToSave["{$kategori}_kuota"] = $kuota;
            $dataToSave["{$kategori}_berhasil"] = $berhasil;
            // Link G-Drive dihapus sesuai permintaan

            // Proses Upload File
            if ($request->hasFile("{$kategori}_file")) {
                $files = $request->file("{$kategori}_file");
                $filePaths = [];

                foreach ($files as $file) {
                    $filePaths[] = $file->store('bukti_kinerja_pk', 'public');
                }
                $dataToSave["{$kategori}_file"] = json_encode($filePaths);
            }
        }

        // Hitung Rata-Rata Akhir & Predikat
        $dataToSave['rata_rata'] = $totalPersen / 4;

        if ($dataToSave['rata_rata'] >= 90) {
            $dataToSave['predikat'] = 'Baik Sekali';
        } elseif ($dataToSave['rata_rata'] >= 75) {
            $dataToSave['predikat'] = 'Baik';
        } elseif ($dataToSave['rata_rata'] >= 60) {
            $dataToSave['predikat'] = 'Cukup';
        } else {
            $dataToSave['predikat'] = 'Kurang';
        }

        // Simpan ke Database
        KinerjaPk::updateOrCreate(
            ['pengawas_id' => $userId, 'bulan' => $request->bulan, 'tahun' => $request->tahun],
            $dataToSave
        );

        return redirect()->back()->with('success', 'Formulir Kinerja PK berhasil disimpan dengan Predikat: ' . $dataToSave['predikat']);
    }
}
