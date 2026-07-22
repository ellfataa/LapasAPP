<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penilaian Kinerja PK Bapas</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 12px; line-height: 1.4; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #2c3e50; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #7f8c8d; }
        .info-filter { margin-bottom: 20px; font-size: 12px; font-weight: bold; }
        table { w-full: 100%; width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #bdc3c7; padding: 10px 8px; text-align: center; vertical-align: middle; }
        th { background-color: #ecf0f1; color: #2c3e50; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        .text-left { text-align: left; }
        .score-good { color: #27ae60; font-weight: bold; }
        .score-bad { color: #c0392b; font-weight: bold; }
        .footer { text-align: right; margin-top: 50px; font-size: 12px; }
        .footer-ttd { margin-top: 60px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="header">
        <h1>REKAPITULASI PENILAIAN KINERJA PENGAWAS KEMASYARAKATAN (PK)</h1>
        <p>Balai Pemasyarakatan (BAPAS) Purwokerto</p>
    </div>

    <div class="info-filter">
        <p>Tanggal Cetak : {{ date('d/m/Y') }}</p>
        <p>Filter Pencarian : {{ $searchKeyword }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%">No</th>
                <th rowspan="2" style="width: 20%">Nama PK</th>
                <th rowspan="2" style="width: 15%">Periode</th>
                <th colspan="3">Rincian Capaian (%)</th>
                <th rowspan="2" style="width: 12%">Skor Rata-Rata</th>
                <th rowspan="2" style="width: 15%">Predikat</th>
            </tr>
            <tr>
                <th style="width: 11%">Litmas</th>
                <th style="width: 11%">Bimbingan</th>
                <th style="width: 11%">Pengawasan</th>
            </tr>
        </thead>
        <tbody>
            @php $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
            @forelse($semuaKinerja as $index => $kinerja)
                @php
                    $pLitmas = $kinerja->litmas_kuota > 0 ? ($kinerja->litmas_berhasil / $kinerja->litmas_kuota) * 100 : 0;
                    $pBimbingan = $kinerja->pembimbingan_kuota > 0 ? ($kinerja->pembimbingan_berhasil / $kinerja->pembimbingan_kuota) * 100 : 0;
                    $pPengawasan = $kinerja->pengawasan_kuota > 0 ? ($kinerja->pengawasan_berhasil / $kinerja->pengawasan_kuota) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $kinerja->pengawas->nama ?? '-' }}</strong><br>
                        <span style="font-size: 10px; color: #7f8c8d;">NRP/NIP: {{ $kinerja->pengawas->nomor_induk ?? '-' }}</span>
                    </td>
                    <td>{{ $namaBulan[$kinerja->bulan - 1] ?? $kinerja->bulan }} {{ $kinerja->tahun }}</td>
                    <td>{{ number_format($pLitmas, 1) }}%</td>
                    <td>{{ number_format($pBimbingan, 1) }}%</td>
                    <td>{{ number_format($pPengawasan, 1) }}%</td>
                    <td><strong>{{ $kinerja->rata_rata }}%</strong></td>
                    <td class="{{ in_array($kinerja->predikat, ['Sangat Baik', 'Baik']) ? 'score-good' : (in_array($kinerja->predikat, ['Kurang', 'Sangat Kurang']) ? 'score-bad' : '') }}">
                        {{ $kinerja->predikat }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data rekapitulasi kinerja ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Purwokerto, {{ date('d') }} {{ $namaBulan[date('n') - 1] }} {{ date('Y') }}</p>
        <p>Kepala/Administrator Bapas</p>
        <div class="footer-ttd">
            {{ Auth::user()->nama ?? 'Administrator' }}
        </div>
    </div>

</body>
</html>
