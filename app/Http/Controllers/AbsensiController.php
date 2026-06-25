<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    // ======================================================================
    // BAGIAN 1: FITUR MANTAN NAPI / NARAPIDANA
    // ======================================================================

    /**
     * Menampilkan halaman dashboard narapidana beserta riwayatnya.
     */
    public function indexNarapidana(Request $request)
    {
        $userId = Auth::id();

        // 1. Ambil daftar tahun yang tersedia dari database berdasarkan riwayat user
        $availableYears = AbsensiKegiatan::where('narapidana_id', $userId)
            ->selectRaw('YEAR(tanggal_waktu) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $currentSystemYear = date('Y');

        // Pastikan tahun ini selalu ada di dropdown meskipun belum absen
        if (!in_array($currentSystemYear, $availableYears)) {
            $availableYears[] = $currentSystemYear;
            rsort($availableYears); // Urutkan dari tahun terbaru ke terlama
        }

        // 2. Tentukan tahun yang sedang aktif dilihat (dari parameter URL, default: tahun ini)
        $selectedYear = $request->input('year', $currentSystemYear);

        // 3. Ambil riwayat HANYA untuk tahun yang dipilih
        $riwayat = AbsensiKegiatan::where('narapidana_id', $userId)
            ->whereYear('tanggal_waktu', $selectedYear)
            ->orderBy('tanggal_waktu', 'desc')
            ->get();

        return view('dashboard.narapidana', compact('riwayat', 'availableYears', 'selectedYear'));
    }

    /**
     * Memproses form input laporan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'        => ['required', 'date', 'before_or_equal:today'],
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'bukti_file'     => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:10240'], // Maksimal 10MB
        ], [
            'tanggal.before_or_equal' => 'Tanggal kegiatan tidak boleh melebihi hari ini.',
            'bukti_file.max'          => 'Ukuran file gambar maksimal adalah 10MB.',
            'bukti_file.image'        => 'File yang diunggah harus berupa gambar (JPEG, PNG, JPG).',
        ]);

        $path = $request->file('bukti_file')->store('bukti_kegiatan', 'public');

        AbsensiKegiatan::create([
            'narapidana_id'  => Auth::id(),
            'tanggal_waktu'  => $request->tanggal,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'bukti_file'     => $path,
        ]);

        return redirect()->back()->with('success', 'Absensi dan bukti kegiatan berhasil dikirim!');
    }

    /**
     * Menampilkan halaman form edit laporan.
     */
    public function edit($id)
    {
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
            'bukti_file'     => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
        ]);

        $dataToUpdate = [
            'tanggal_waktu'  => $request->tanggal,
            'jenis_kegiatan' => $request->jenis_kegiatan,
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


    // ======================================================================
    // BAGIAN 2: FITUR PENGAWAS / PK
    // ======================================================================

    /**
     * Menampilkan dashboard Pengawas/PK beserta data laporan dan filter pencarian.
     */
    public function indexPengawas(Request $request)
    {
        // Panggil relasi 'narapidana' agar tidak terjadi N+1 Query Problem saat menampilkan nama/nomor induk di tabel
        $query = AbsensiKegiatan::with('narapidana');

        // 1. Ambil ketersediaan Tahun untuk Dropdown Filter
        $availableYears = AbsensiKegiatan::selectRaw('YEAR(tanggal_waktu) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $currentSystemYear = date('Y');
        if (!in_array($currentSystemYear, $availableYears)) {
            $availableYears[] = $currentSystemYear;
            rsort($availableYears); // Urutkan dari tahun terbaru
        }

        // 2. Terapkan Filter Pencarian Nama / Nomor Induk
        if ($request->filled('search')) {
            $search = $request->search;
            // Pencarian dilakukan pada tabel 'users' yang berelasi
            $query->whereHas('narapidana', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        // 3. Terapkan Filter Bulan
        if ($request->filled('month')) {
            $query->whereMonth('tanggal_waktu', $request->month);
        }

        // 4. Terapkan Filter Tahun
        if ($request->filled('year')) {
            $query->whereYear('tanggal_waktu', $request->year);
        }

        // 5. Eksekusi Query (selalu urutkan dari yang terbaru)
        $semuaAbsensi = $query->orderBy('tanggal_waktu', 'desc')->get();

        // Return ke view 'pengawas.blade.php' yang akan kita buat
        return view('dashboard.pengawas', compact('semuaAbsensi', 'availableYears'));
    }
}
