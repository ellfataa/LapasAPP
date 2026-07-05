<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; // SPREADSHEET

class AbsensiController extends Controller
{
    // BAGIAN 1: FITUR NARAPIDANA
    public function indexNarapidana(Request $request)
    {
        $userId = Auth::id();
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
        $daftarPengawas = User::where('role', 'pengawas')->get();
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
        $user = Auth::user(); // Ambil seluruh objek user
        $userName = $user->nama;

        // --- GOOGLE SHEETS SINKRONISASI ---
        // $spreadsheetId = '1e_pp1RiwOtcg26SdeSRzspR3dmX52Rdf_39ZAFIPN_s';
        $spreadsheetId = '1yHJCmGakpsyLx16FmWeNIyKp6EzA9Y8VS3aI_D3eTEg';
        $gid = '385067762';

        $url = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid={$gid}";

        $dataLitmasRealtime = [
            'jumlah' => 0, 'selesai' => 0, 'belum_selesai' => 0, 'kinerja' => '0%', 'kinerja_angka' => 0
        ];

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
        // ----------------------------------

        // AMBIL DATA KLIEN BIMBINGAN (Untuk fitur baru kategori Pembimbingan)
        $daftarKlienSaya = User::where('role', 'narapidana')->where('pembimbing_id', $userId)->orderBy('nama', 'asc')->get();

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

        // Pass 'daftarKlienSaya' ke view
        return view('dashboard.pengawas', compact('semuaAbsensi', 'availableYears', 'riwayatKinerja', 'dataLitmasRealtime', 'daftarKlienSaya'));
    }
}
