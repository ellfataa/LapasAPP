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

class AbsensiController extends Controller
{
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

        AbsensiKegiatan::create([
            'narapidana_id'  => $user->id,
            'pengawas_id'    => $user->pembimbing_id,
            'tanggal_waktu'  => $request->tanggal,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'bukti_file'     => $path,
        ]);

        return redirect()->back()->with('success', 'Absensi dan bukti kegiatan berhasil dikirim dan tersimpan di sistem!');
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

        if ($request->hasFile('bukti_file')) {
            if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
                Storage::disk('public')->delete($absensi->bukti_file);
            }
            $path = $request->file('bukti_file')->store('bukti_kegiatan', 'public');
            $dataToUpdate['bukti_file'] = $path;
        }

        $absensi->update($dataToUpdate);

        return redirect()->route('dashboard.narapidana')->with('success', 'Data laporan kegiatan berhasil diperbarui!');
    }


    // BAGIAN 2: FITUR PK/PENGAWAS
    public function indexPengawas(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $userName = $user->nama;

        // --- 1. GOOGLE SHEETS SINKRONISASI LITMAS ---
        $spreadsheetIdLitmas = '1yHJCmGakpsyLx16FmWeNIyKp6EzA9Y8VS3aI_D3eTEg';
        $gidLitmas = '385067762';
        $urlLitmas = "https://docs.google.com/spreadsheets/d/{$spreadsheetIdLitmas}/export?format=csv&gid={$gidLitmas}";

        $dataLitmasRealtime = ['jumlah' => 0, 'selesai' => 0, 'belum_selesai' => 0, 'kinerja' => '0%', 'kinerja_angka' => 0];

        try {
            $response = Http::timeout(10)->get($urlLitmas);
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
        } catch (\Exception $e) {
            Log::error('Gagal fetch Litmas: ' . $e->getMessage());
        }


        // --- 2. GOOGLE SHEETS SINKRONISASI PEMBIMBINGAN & PENGAWASAN ---
        $dataPembimbinganRealtime = ['jumlah' => 0, 'bekerja' => 0, 'belum_bekerja' => 0, 'kinerja_angka' => 0];

        $rekapPengawasanTahunan = [];
        for ($i = 1; $i <= 12; $i++) { $rekapPengawasanTahunan[$i] = 0; }

        try {
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google-credentials.json'));
            $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
            $service = new Sheets($client);

            $spreadsheetIdPengawasan = '1dMWij_J6P_lvC0H2x19DFpqCSjAMDFsCdeT_H1S61HE';

            // Mengambil range Data: Kolom B (Nama) sampai AF (Desember - Tidak Apel)
            $response = $service->spreadsheets_values->get($spreadsheetIdPengawasan, "'Rekap Data'!B2:AF100");
            $values = $response->getValues();

            if (!empty($values)) {
                $loginName = preg_replace('/\s+/', ' ', strtolower(trim($userName)));
                foreach ($values as $row) {
                    $sheetNama = preg_replace('/\s+/', ' ', strtolower(trim($row[0] ?? ''))); // Kolom B adalah index 0
                    if ($sheetNama === $loginName) {

                        // [A] Data Pembimbingan (Kolom E, F, G -> index 3, 4, 5)
                        $jmlKlien = floatval($row[3] ?? 0);
                        $jmlBekerja = floatval($row[4] ?? 0);
                        $jmlBelum = floatval($row[5] ?? 0);

                        $dataPembimbinganRealtime['jumlah'] = $jmlKlien;
                        $dataPembimbinganRealtime['bekerja'] = $jmlBekerja;
                        $dataPembimbinganRealtime['belum_bekerja'] = $jmlBelum;
                        $dataPembimbinganRealtime['kinerja_angka'] = ($jmlKlien > 0) ? round(($jmlBekerja / $jmlKlien) * 100, 1) : 0;

                        // [B] Data Pengawasan (Kolom I sampai AF -> index 7 dst)
                        for ($m = 1; $m <= 12; $m++) {
                            // Hitung pergeseran index (Bulan 1 = idx 7, Bulan 2 = idx 9, dst)
                            $idxApel = 7 + (($m - 1) * 2);
                            $rekapPengawasanTahunan[$m] = floatval($row[$idxApel] ?? 0);
                        }
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal fetch Pembimbingan & Pengawasan: ' . $e->getMessage());
        }


        // --- 3. AMBIL DATA KLIEN & ABSENSI WEB ---
        $daftarKlienSaya = User::where('role', 'narapidana')->where('pembimbing_id', $userId)->orderBy('nama', 'asc')->get();
        // Total klien prioritas dari spreadsheet, jika 0 pakai dari jumlah akun
        $totalKlienSaatIni = $dataPembimbinganRealtime['jumlah'] > 0 ? $dataPembimbinganRealtime['jumlah'] : $daftarKlienSaya->count();

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

        $riwayatKinerja = \App\Models\KinerjaPk::where('pengawas_id', $userId)
            ->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();

        return view('dashboard.pengawas', compact(
            'semuaAbsensi', 'availableYears', 'riwayatKinerja',
            'dataLitmasRealtime', 'dataPembimbinganRealtime', 'daftarKlienSaya', 'rekapPengawasanTahunan', 'totalKlienSaatIni'
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
