<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class AbsensiController extends Controller
{
    // ==========================================
    // BAGIAN 1: FITUR KLIEN/NARAPIDANA
    public function indexNarapidana(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $pembimbingSaya = User::where('role', 'pengawas')->where('id', $user->pembimbing_id)->first();

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

        return view('dashboard.narapidana', compact('riwayat', 'availableYears', 'selectedYear', 'pembimbingSaya'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (empty($user->pembimbing_id)) {
            return redirect()->back()->withErrors(['pembimbing' => 'Anda belum dihubungkan dengan PK Pembimbing oleh Admin. Tidak dapat melakukan absensi.']);
        }

        $bulanInput = \Carbon\Carbon::parse($request->tanggal)->format('m');
        $tahunInput = \Carbon\Carbon::parse($request->tanggal)->format('Y');

        $sudahAbsenBulanIni = AbsensiKegiatan::where('narapidana_id', $user->id)
            ->whereMonth('tanggal_waktu', $bulanInput)
            ->whereYear('tanggal_waktu', $tahunInput)
            ->exists();

        if ($sudahAbsenBulanIni) {
            return redirect()->back()->withErrors(['tanggal' => 'Anda sudah mengirimkan laporan absen wajib untuk bulan ini. Laporan hanya dapat dilakukan 1 kali per bulan.']);
        }

        $request->validate([
            'tanggal'        => ['required', 'date', 'before_or_equal:today'],
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'bukti_file'     => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ]);

        $path = $request->file('bukti_file')->store('bukti_kegiatan', 'public');

        $absensiBaru = AbsensiKegiatan::create([
            'narapidana_id'  => $user->id,
            'pengawas_id'    => $user->pembimbing_id,
            'tanggal_waktu'  => $request->tanggal,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'bukti_file'     => $path,
        ]);

        try {
            $this->pushImageToGoogleSheets($absensiBaru, $user);
        } catch (\Exception $e) {
            Log::error('Google Sheets Sync Failed (Store): ' . $e->getMessage());
            return redirect()->back()->withErrors(['google_sync' => 'Absensi tersimpan di web, NAMUN GAGAL masuk ke Spreadsheet. Alasan: ' . $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Absensi dan bukti kegiatan berhasil dikirim ke Web dan Spreadsheet!');
    }

    public function edit(int $id)
    {
        $absensi = AbsensiKegiatan::where('narapidana_id', Auth::id())->findOrFail($id);
        $pembimbingSaya = User::where('role', 'pengawas')->where('id', Auth::user()->pembimbing_id)->first();
        return view('dashboard.edit_absensi_narapidana', compact('absensi', 'pembimbingSaya'));
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::user();
        $absensi = AbsensiKegiatan::where('narapidana_id', $user->id)->findOrFail($id);

        $bulanInput = \Carbon\Carbon::parse($request->tanggal)->format('m');
        $tahunInput = \Carbon\Carbon::parse($request->tanggal)->format('Y');
        $bulanLama = \Carbon\Carbon::parse($absensi->tanggal_waktu)->format('m');

        if ($bulanInput !== $bulanLama) {
            $bentrokBulanLain = AbsensiKegiatan::where('narapidana_id', $user->id)
                ->whereMonth('tanggal_waktu', $bulanInput)
                ->whereYear('tanggal_waktu', $tahunInput)
                ->where('id', '!=', $id)
                ->exists();

            if ($bentrokBulanLain) {
                return redirect()->back()->withErrors(['tanggal' => 'Data untuk bulan tersebut sudah ada.']);
            }
        }

        $request->validate([
            'tanggal'        => ['required', 'date', 'before_or_equal:today'],
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'bukti_file'     => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ]);

        $dataToUpdate = [
            'tanggal_waktu'  => $request->tanggal,
            'jenis_kegiatan' => $request->jenis_kegiatan,
        ];

        if(empty($absensi->pengawas_id) && !empty($user->pembimbing_id)) {
            $dataToUpdate['pengawas_id'] = $user->pembimbing_id;
        }

        $isImageChanged = false;
        if ($request->hasFile('bukti_file')) {
            if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
                Storage::disk('public')->delete($absensi->bukti_file);
            }
            $path = $request->file('bukti_file')->store('bukti_kegiatan', 'public');
            $dataToUpdate['bukti_file'] = $path;
            $isImageChanged = true;
        }

        $absensi->update($dataToUpdate);

        if ($isImageChanged) {
            try {
                $this->pushImageToGoogleSheets($absensi, $user);
            } catch (\Exception $e) {
                Log::error('Google Sheets Sync Failed (Update): ' . $e->getMessage());
                return redirect()->route('dashboard.narapidana')->withErrors(['google_sync' => 'Data terupdate di web, NAMUN GAGAL sinkron ke Spreadsheet. Alasan: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('dashboard.narapidana')->with('success', 'Data kegiatan berhasil diperbarui!');
    }


    // ==========================================
    // BAGIAN GOOGLE SHEETS API LOGIC (KATEGORI PENGAWASAN)
    private function pushImageToGoogleSheets(AbsensiKegiatan $absensi, User $klien): void
    {
        // 1. Ambil Nama Tab Sheet (Sesuai Nama PK)
        $pembimbing = User::find($absensi->pengawas_id);
        if (!$pembimbing) throw new \Exception("Pengawas tidak ditemukan di database.");
        $sheetName = trim($pembimbing->nama); // Pastikan nama di DB sama dengan Nama Tab

        // 2. Tentukan Kriteria Pencarian
        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanTarget = $bulanIndo[\Carbon\Carbon::parse($absensi->tanggal_waktu)->format('n') - 1];
        $tanggalApelTeks = \Carbon\Carbon::parse($absensi->tanggal_waktu)->translatedFormat('d F Y');

        // 3. Konfigurasi
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google-credentials.json'));
        $client->setScopes([Sheets::SPREADSHEETS]);
        $service = new Sheets($client);

        // TETAP SATU ID UNTUK SEMUA PK (KATEGORI PENGAWASAN)
        $spreadsheetId = '1dMWij_J6P_lvC0H2x19DFpqCSjAMDFsCdeT_H1S61HE';

        $response = $service->spreadsheets_values->get($spreadsheetId, "'{$sheetName}'!A1:ZZ200");
        $values = $response->getValues();

        if (empty($values)) throw new \Exception("Tab '{$sheetName}' kosong.");

        $targetRow = -1;
        $targetColIndexFoto = -1;
        $targetColIndexTanggal = -1;

        // --- A. CARI BARIS (NAMA KLIEN) ---
        $klienNameWeb = strtolower(trim($klien->nama));
        foreach ($values as $rowIndex => $row) {
            $sheetNameCell = strtolower(trim($row[1] ?? '')); // Kolom B adalah index 1
            if ($sheetNameCell === $klienNameWeb) {
                $targetRow = $rowIndex + 1;
                break;
            }
        }

        // --- B. CARI KOLOM (BULAN, FOTO, TANGGAL) ---
        // Penanganan Merge Cell (Bulan Juli akan muncul di index pertama, sisanya kosong)
        $rowBulan = $values[1] ?? [];
        $rowSubHeader = $values[2] ?? [];
        $currentMonth = '';

        foreach ($rowSubHeader as $colIndex => $subHeader) {
            // Update nama bulan hanya jika cell di baris 2 tidak kosong
            if (!empty($rowBulan[$colIndex])) {
                $currentMonth = trim($rowBulan[$colIndex]);
            }

            // Jika bulan saat ini sesuai target
            if (strtolower($currentMonth) === strtolower($bulanTarget)) {
                $subHeaderClean = strtolower(trim($subHeader));
                if ($subHeaderClean === 'foto') $targetColIndexFoto = $colIndex;
                if (str_contains($subHeaderClean, 'tanggal')) $targetColIndexTanggal = $colIndex;
            }
        }

        // --- C. EKSEKUSI ---
        if ($targetRow !== -1 && $targetColIndexFoto !== -1) {
            $colLetterFoto = $this->getColumnLetter($targetColIndexFoto);
            $colLetterTanggal = $this->getColumnLetter($targetColIndexTanggal);

            $urlFoto = asset('storage/' . $absensi->bukti_file);

            $dataToUpdate = [
                new ValueRange([
                    'range' => "'{$sheetName}'!{$colLetterFoto}{$targetRow}",
                    'values' => [['=IMAGE("' . $urlFoto . '")']]
                ]),
                new ValueRange([
                    'range' => "'{$sheetName}'!{$colLetterTanggal}{$targetRow}",
                    'values' => [[$tanggalApelTeks]]
                ])
            ];

            $body = new \Google\Service\Sheets\BatchUpdateValuesRequest();
            $body->setValueInputOption('USER_ENTERED');
            $body->setData($dataToUpdate);
            $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
        } else {
            throw new \Exception("Gagal: Nama baris ditemukan? " . ($targetRow !== -1 ? 'YA' : 'TIDAK') . ". Kolom 'Foto' di bulan '{$bulanTarget}' ditemukan? " . ($targetColIndexFoto !== -1 ? 'YA' : 'TIDAK'));
        }
    }

    private function getColumnLetter(int $num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return $this->getColumnLetter($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }


    // ==========================================
    // BAGIAN 2: FITUR PK/PENGAWAS
    public function indexPengawas(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $userName = $user->nama;

        // --- GOOGLE SHEETS SINKRONISASI LITMAS ---
        $spreadsheetId = '1yHJCmGakpsyLx16FmWeNIyKp6EzA9Y8VS3aI_D3eTEg';
        $gid = '385067762';
        $url = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid={$gid}";

        $dataLitmasRealtime = ['jumlah' => 0, 'selesai' => 0, 'belum_selesai' => 0, 'kinerja' => '0%', 'kinerja_angka' => 0];

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
            if ($response->successful()) {
                $barisCsv = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $response->body()));
                $loginName = preg_replace('/\s+/', ' ', strtolower(trim($userName)));

                foreach ($barisCsv as $rowData) {
                    if (count($rowData) > 39 && isset($rowData[23])) {
                        $sheetName = preg_replace('/\s+/', ' ', strtolower(trim($rowData[23])));
                        if ($sheetName === $loginName) {
                            $dataLitmasRealtime['jumlah'] = filter_var($rowData[36], FILTER_SANITIZE_NUMBER_INT) ?: 0;
                            $dataLitmasRealtime['selesai'] = filter_var($rowData[37], FILTER_SANITIZE_NUMBER_INT) ?: 0;
                            $dataLitmasRealtime['belum_selesai'] = filter_var($rowData[38], FILTER_SANITIZE_NUMBER_INT) ?: 0;
                            $kinerjaMentah = trim($rowData[39]);
                            $dataLitmasRealtime['kinerja'] = $kinerjaMentah ?: '0%';
                            $dataLitmasRealtime['kinerja_angka'] = floatval(str_replace(['%', ','], ['', '.'], $kinerjaMentah));
                            break;
                        }
                    }
                }
            }
        } catch (\Exception $e) { }


        // --- AMBIL DATA KLIEN & REKAP PENGAWASAN (SEMUA BULAN) ---
        $daftarKlienSaya = User::where('role', 'narapidana')->where('pembimbing_id', $userId)->orderBy('nama', 'asc')->get();
        $totalKlienSaatIni = $daftarKlienSaya->count();

        $tahunIni = date('Y');
        $rekapPengawasanTahunan = [];

        // Looping untuk mencari jumlah absen klien unik dari bulan 1 sampai 12
        for ($i = 1; $i <= 12; $i++) {
            $rekapPengawasanTahunan[$i] = AbsensiKegiatan::where('pengawas_id', $userId)
                ->whereMonth('tanggal_waktu', $i)
                ->whereYear('tanggal_waktu', $tahunIni)
                ->distinct('narapidana_id')
                ->count('narapidana_id');
        }


        // --- FILTER TABEL ABSENSI BAWAH ---
        $query = AbsensiKegiatan::with('narapidana')->where('pengawas_id', $userId);
        $availableYears = AbsensiKegiatan::where('pengawas_id', $userId)
            ->selectRaw('YEAR(tanggal_waktu) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();

        $currentSystemYear = date('Y');
        if (!in_array($currentSystemYear, $availableYears)) {
            $availableYears[] = $currentSystemYear; rsort($availableYears);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('narapidana', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month')) { $query->whereMonth('tanggal_waktu', $request->month); }
        if ($request->filled('year')) { $query->whereYear('tanggal_waktu', $request->year); }

        $semuaAbsensi = $query->orderBy('tanggal_waktu', 'desc')->get();

        // Riwayat Kinerja
        $riwayatKinerja = \App\Models\KinerjaPk::where('pengawas_id', $userId)
            ->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();

        return view('dashboard.pengawas', compact(
            'semuaAbsensi', 'availableYears', 'riwayatKinerja',
            'dataLitmasRealtime', 'daftarKlienSaya', 'rekapPengawasanTahunan', 'totalKlienSaatIni'
        ));
    }

    public function saveDraftPk(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->role === 'pengawas') {
            User::where('id', $user->id)->update(['kinerja_draft' => $request->draft]);
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error'], 403);
    }
}
