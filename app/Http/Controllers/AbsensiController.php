<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    // ======================================================================
    // BAGIAN 1: FITUR NARAPIDANA
    // ======================================================================

    /**
     * Menampilkan halaman dashboard narapidana beserta riwayatnya.
     */
    public function indexNarapidana()
    {
        $riwayat = AbsensiKegiatan::where('narapidana_id', Auth::id())
            ->orderBy('tanggal_waktu', 'desc')
            ->get();

        return view('dashboard.narapidana', compact('riwayat'));
    }

    /**
     * Memproses form input laporan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'        => ['required', 'date', 'before_or_equal:today'],
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'bukti_file'     => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], // Maksimal 2MB
        ], [
            'tanggal.before_or_equal' => 'Tanggal kegiatan tidak boleh melebihi hari ini.',
            'bukti_file.max'          => 'Ukuran file gambar maksimal adalah 2MB.',
            'bukti_file.image'        => 'File yang diunggah harus berupa gambar (JPEG, PNG, JPG).',
        ]);

        $path = $request->file('bukti_file')->store('bukti_kegiatan', 'public');

        AbsensiKegiatan::create([
            'narapidana_id'  => Auth::id(),
            'tanggal_waktu'  => $request->tanggal,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'bukti_file'     => $path,
            // 'status_validasi' dihapus karena otomatis diset 'menunggu' oleh default MySQL
        ]);

        return redirect()->back()->with('success', 'Absensi dan bukti kegiatan berhasil dikirim!');
    }

    /**
     * Menampilkan halaman form edit laporan.
     */
    public function edit($id)
    {
        // Pengecekan ID narapidana agar tidak bisa mengedit data orang lain
        $absensi = AbsensiKegiatan::where('narapidana_id', Auth::id())->findOrFail($id);

        return view('dashboard.edit_absensi_narapidana', compact('absensi'));
    }

    /**
     * Memproses update data dan file gambar.
     */
    public function update(Request $request, $id)
    {
        $absensi = AbsensiKegiatan::where('narapidana_id', Auth::id())->findOrFail($id);

        $request->validate([
            'tanggal'        => ['required', 'date', 'before_or_equal:today'],
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'bukti_file'     => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $dataToUpdate = [
            'tanggal_waktu'  => $request->tanggal,
            'jenis_kegiatan' => $request->jenis_kegiatan,
        ];

        // Jika narapidana mengunggah foto baru
        if ($request->hasFile('bukti_file')) {
            // Hapus foto lama dari penyimpanan public
            if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
                Storage::disk('public')->delete($absensi->bukti_file);
            }

            // Simpan foto yang baru
            $path = $request->file('bukti_file')->store('bukti_kegiatan', 'public');
            $dataToUpdate['bukti_file'] = $path;
        }

        $absensi->update($dataToUpdate);

        return redirect()->route('dashboard.narapidana')->with('success', 'Data kegiatan berhasil diperbarui!');
    }


    // ======================================================================
    // BAGIAN 2: FITUR ADMIN & PENGAWAS
    // ======================================================================

    /**
     * Menampilkan dashboard petugas beserta seluruh data absensi.
     */
    public function indexPetugas()
    {
        $semuaAbsensi = AbsensiKegiatan::with('narapidana')
            ->orderBy('tanggal_waktu', 'desc')
            ->get();

        return view('dashboard.petugas', compact('semuaAbsensi'));
    }
}
