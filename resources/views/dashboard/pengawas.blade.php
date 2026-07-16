<x-app-layout>
    <x-slot name="header">
        <div class="bapas-pengawas-header flex items-center gap-4 bg-slate-100">
            <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm sm:flex">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3.75 3.75 0 003.75-3.75V8.25A3.75 3.75 0 0016.5 4.5h-9a3.75 3.75 0 00-3.75 3.75V15a3.75 3.75 0 003.75 3.75m9 0V21m-9-2.25V21M9 9.75h6m-6 3h4.5"></path></svg>
            </div>
            <h2 class="text-xl leading-tight tracking-tight text-slate-900 sm:text-2xl md:text-3xl">
                Dashboard, Selamat Datang PK <span class="font-bold">{{ Auth::user()->nama }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-6 sm:py-10" x-data="{ showModal: false, imgSrc: '', showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:space-y-8 sm:px-6 lg:px-8">

            <!-- BAGIAN 1: FORM KINERJA PK (SINKRONISASI SPREADSHEET) -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-blue-800 bg-gradient-to-r from-slate-900 to-blue-900 px-5 py-5 sm:px-7 sm:py-6">
                    <div class="flex items-center gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white ring-1 ring-inset ring-white/20">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold leading-snug text-white sm:text-xl">Formulir Penilaian Kinerja PK</h3>
                            <p class="mt-1 text-sm leading-relaxed text-blue-100">Sistem otomatis menarik data dari Spreadsheet. Klik Simpan untuk memperbarui rekap.</p>
                        </div>
                    </div>

                    @php
                        $namaBulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $bulanTeks = $namaBulanIndo[date('n') - 1];
                    @endphp
                    <div class="bg-white/10 backdrop-blur-sm px-4 py-2.5 rounded-xl border border-white/20 shadow-inner">
                        <span class="block text-[11px] text-blue-200 font-bold uppercase tracking-widest mb-0.5">Periode Berjalan</span>
                        <span class="block text-white font-extrabold text-sm sm:text-base">{{ $bulanTeks }} {{ date('Y') }}</span>
                    </div>
                </div>

                <div class="p-5 sm:p-7 lg:p-8">

                    <script> window.rekapTahunanData = {!! json_encode($rekapPengawasanTahunan) !!}; </script>

                    <form action="{{ route('kinerja-pk.store') }}" method="POST" x-data="{
                        litmas_persen: {{ $dataLitmasRealtime['kinerja_angka'] ?? 0 }},
                        pembimbinganPersen: {{ $dataPembimbinganRealtime['kinerja_angka'] ?? 0 }},
                        totalKlien: {{ $totalKlienSaatIni }},

                        currentMonth: {{ date('n') }},
                        viewMonth: '{{ date('n') }}',
                        rekapTahunan: window.rekapTahunanData,

                        get pengawasanBerhasil() { return this.rekapTahunan[this.viewMonth] || 0; },
                        get pengawasanBelum() { return Math.max(0, this.totalKlien - this.pengawasanBerhasil); },

                        get pengawasanBerhasilCurrentMonth() { return this.rekapTahunan[this.currentMonth] || 0; },

                        get pengawasanPersenView() {
                            if(this.totalKlien > 0) return ((this.pengawasanBerhasil / this.totalKlien) * 100).toFixed(1);
                            return 0;
                        },
                        get pengawasanPersenReal() {
                            if(this.totalKlien > 0) return (this.pengawasanBerhasilCurrentMonth / this.totalKlien) * 100;
                            return 0;
                        },

                        get rataRata() {
                            let total = parseFloat(this.litmas_persen) || 0;
                            total += parseFloat(this.pembimbinganPersen) || 0;
                            total += parseFloat(this.pengawasanPersenReal) || 0;
                            return (total / 3).toFixed(1);
                        },

                        get predikat() {
                            let rata = parseFloat(this.rataRata);
                            if(rata >= 91) return 'Sangat Baik';
                            if(rata >= 81) return 'Baik';
                            if(rata >= 70) return 'Cukup';
                            if(rata >= 60) return 'Kurang';
                            return 'Sangat Kurang';
                        }
                    }">
                        @csrf

                        <!-- Hidden Input Mengunci Data ke Bulan Berjalan -->
                        <input type="hidden" name="pengawasan_kuota" :value="totalKlien">
                        <input type="hidden" name="pengawasan_berhasil" :value="pengawasanBerhasilCurrentMonth">

                        <input type="hidden" name="litmas_kuota" value="{{ $dataLitmasRealtime['jumlah'] ?? 0 }}">
                        <input type="hidden" name="litmas_berhasil" value="{{ $dataLitmasRealtime['selesai'] ?? 0 }}">

                        <input type="hidden" name="pembimbingan_kuota" value="{{ $dataPembimbinganRealtime['jumlah'] ?? 0 }}">
                        <input type="hidden" name="pembimbingan_berhasil" value="{{ $dataPembimbinganRealtime['bekerja'] ?? 0 }}">

                        <!-- KARTU 1: LITMAS -->
                        <article class="mb-5 overflow-hidden rounded-2xl border border-blue-200 bg-blue-50/50 transition duration-200 hover:border-blue-300 hover:shadow-sm">
                            <div class="border-b border-blue-200 bg-blue-100/70 px-5 py-3.5 sm:px-6">
                                <h4 class="flex items-center gap-3 text-base font-bold text-blue-950">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-blue-900 text-xs font-extrabold text-white shadow-sm">1</span>
                                    Penelitian Kemasyarakatan (Litmas)
                                </h4>
                            </div>

                            <div class="grid grid-cols-2 gap-3 p-4 sm:p-5 md:grid-cols-4">
                                <div class="rounded-xl border border-slate-200 bg-white p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-500">Jumlah Target</span>
                                    <span class="text-2xl font-extrabold text-slate-800">{{ $dataLitmasRealtime['jumlah'] ?? 0 }}</span>
                                </div>
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-emerald-700">Selesai</span>
                                    <span class="text-2xl font-extrabold text-emerald-600">{{ $dataLitmasRealtime['selesai'] ?? 0 }}</span>
                                </div>
                                <div class="rounded-xl border border-red-200 bg-red-50 p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-red-700">Belum Selesai</span>
                                    <span class="text-2xl font-extrabold text-red-600">{{ $dataLitmasRealtime['belum_selesai'] ?? 0 }}</span>
                                </div>
                                <div class="rounded-xl border border-blue-200 bg-blue-100 p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-blue-800">Skor Litmas</span>
                                    <span class="text-2xl font-extrabold text-blue-700">{{ $dataLitmasRealtime['kinerja'] ?? '0%' }}</span>
                                </div>
                            </div>
                        </article>

                        <!-- KARTU 2: PEMBIMBINGAN -->
                        <article class="mb-5 overflow-hidden rounded-2xl border border-indigo-200 bg-indigo-50/50 transition duration-200 hover:border-indigo-300 hover:shadow-sm">
                            <div class="border-b border-indigo-200 bg-indigo-100/70 px-5 py-3.5 sm:px-6">
                                <h4 class="flex items-center gap-3 text-base font-bold text-indigo-950">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-indigo-900 text-xs font-extrabold text-white shadow-sm">2</span>
                                    Pembimbingan
                                </h4>
                            </div>

                            <div class="grid grid-cols-2 gap-3 p-4 sm:p-5 md:grid-cols-4">
                                <div class="rounded-xl border border-slate-200 bg-white p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-500">Jumlah Klien</span>
                                    <span class="text-2xl font-extrabold text-slate-800">{{ $dataPembimbinganRealtime['jumlah'] ?? 0 }}</span>
                                </div>
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-emerald-700">Sudah Bekerja</span>
                                    <span class="text-2xl font-extrabold text-emerald-600">{{ $dataPembimbinganRealtime['bekerja'] ?? 0 }}</span>
                                </div>
                                <div class="rounded-xl border border-red-200 bg-red-50 p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-red-700">Belum Bekerja</span>
                                    <span class="text-2xl font-extrabold text-red-600">{{ $dataPembimbinganRealtime['belum_bekerja'] ?? 0 }}</span>
                                </div>
                                <div class="rounded-xl border border-indigo-200 bg-indigo-100 p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-indigo-800">Skor Pembimbingan</span>
                                    <span class="text-2xl font-extrabold text-indigo-700">{{ $dataPembimbinganRealtime['kinerja_angka'] ?? 0 }}%</span>
                                </div>
                            </div>
                        </article>

                        <!-- KARTU 3: PENGAWASAN -->
                        <article class="mb-8 overflow-hidden rounded-2xl border border-teal-200 bg-teal-50/50 transition duration-200 hover:border-teal-300 hover:shadow-sm">
                            <div class="border-b border-teal-200 bg-teal-100/70 px-5 py-3.5 sm:px-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <h4 class="flex items-center gap-3 text-base font-bold text-teal-950">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-teal-800 text-xs font-extrabold text-white shadow-sm">3</span>
                                    Pengawasan
                                </h4>
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-bold text-teal-800">Cek Bulan Lain:</label>
                                    <select x-model="viewMonth" class="text-sm font-bold text-teal-900 bg-white border-teal-300 rounded-lg py-1.5 pl-3 pr-8 shadow-sm cursor-pointer hover:border-teal-400 focus:ring-teal-500 focus:border-teal-500">
                                        @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $bln)
                                            <option value="{{ $index + 1 }}">{{ $bln }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 p-4 sm:p-5 md:grid-cols-4">
                                <div class="rounded-xl border border-slate-200 bg-white p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-500">Jumlah Klien</span>
                                    <span class="text-2xl font-extrabold text-slate-800" x-text="totalKlien"></span>
                                </div>
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-emerald-700">Berhasil Apel</span>
                                    <span class="text-2xl font-extrabold text-emerald-600" x-text="pengawasanBerhasil"></span>
                                </div>
                                <div class="rounded-xl border border-red-200 bg-red-50 p-3.5 text-center shadow-sm">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-red-700">Tidak Apel</span>
                                    <span class="text-2xl font-extrabold text-red-600" x-text="pengawasanBelum"></span>
                                </div>
                                <div class="relative rounded-xl border border-teal-200 bg-teal-100 p-3.5 text-center shadow-sm overflow-hidden" :class="{'ring-2 ring-red-400': viewMonth != currentMonth}">
                                    <span class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-teal-800" :class="{'text-red-700': viewMonth != currentMonth}">Skor Pengawasan <span x-show="viewMonth != currentMonth" class="text-red-600">*</span></span>
                                    <span class="text-2xl font-extrabold text-teal-700" :class="{'text-red-600': viewMonth != currentMonth}" x-text="pengawasanPersenView + '%'"></span>
                                    <div x-show="viewMonth != currentMonth" x-transition class="absolute bottom-0 left-0 w-full bg-red-500 py-0.5 text-center text-[9px] text-white font-bold tracking-widest uppercase">Bulan sudah lewat</div>
                                </div>
                            </div>
                        </article>


                        <!-- ESTIMASI KINERJA AKHIR & TOMBOL SUBMIT -->
                        <div class="mb-6 flex flex-col items-stretch justify-between gap-5 rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:p-6 md:flex-row md:items-center">
                            <div class="text-center md:text-left">
                                <h4 class="mb-1 text-xl font-extrabold text-slate-900">Estimasi Kinerja Akhir</h4>
                                <p class="text-sm font-medium leading-relaxed text-slate-600">Nilai dihitung otomatis berdasarkan 3 kategori di atas pada <b>Bulan Berjalan</b>.</p>
                            </div>

                            <div class="grid w-full grid-cols-2 gap-3 md:w-auto">
                                <div class="min-w-[140px] rounded-xl border border-slate-200 bg-white px-5 py-3.5 text-center shadow-sm">
                                    <span class="mb-1 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-500">Rata-Rata</span>
                                    <span class="text-2xl font-extrabold text-slate-800" x-text="rataRata + '%'"></span>
                                </div>
                                <div class="min-w-[140px] rounded-xl border border-slate-200 bg-white px-5 py-3.5 text-center shadow-sm">
                                    <span class="mb-1 block text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-500">Predikat</span>
                                    <span class="font-extrabold text-xl sm:text-2xl" :class="{'text-emerald-600': predikat === 'Sangat Baik', 'text-blue-600': predikat === 'Baik', 'text-yellow-500': predikat === 'Cukup', 'text-orange-500': predikat === 'Kurang', 'text-red-600': predikat === 'Sangat Kurang'}" x-text="predikat"></span>
                                </div>
                            </div>
                        </div>

                        <!-- FITUR PENCEGAHAN MANIPULASI -->
                        <div class="flex flex-col items-end gap-3 mt-6">
                            <div x-show="viewMonth != currentMonth" x-transition class="w-full rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700 shadow-sm text-center md:text-right">
                                <span class="font-bold text-red-800">Penyimpanan Terkunci:</span> Anda sedang dalam mode simulasi pengawasan bulan lain. Kembalikan <b>Cek Bulan</b> ke bulan berjalan untuk menyimpan laporan Anda.
                            </div>

                            <button type="submit" :disabled="viewMonth != currentMonth" :class="{'opacity-50 cursor-not-allowed grayscale': viewMonth != currentMonth, 'hover:bg-blue-800 hover:shadow-lg focus:ring-4 focus:ring-blue-200': viewMonth == currentMonth}" class="inline-flex min-h-[50px] w-full items-center justify-center rounded-xl bg-blue-700 px-8 py-3 text-base font-bold text-white shadow-sm transition md:w-auto focus:outline-none">
                                <svg class="mr-2.5 h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                                Simpan & Sinkronkan
                            </button>
                        </div>

                    </form>
                </div>
            </section>


            <!-- BAGIAN 2: RIWAYAT PENILAIAN -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center gap-4 border-b border-slate-200 bg-white px-5 py-5 sm:px-7 sm:py-6">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </span>
                    <h3 class="text-lg font-bold leading-snug text-slate-900 sm:text-xl">Riwayat Penilaian Kinerja Saya</h3>
                </div>

                <div class="custom-scrollbar overflow-x-auto">
                    <table class="w-full min-w-[900px] table-fixed text-left text-slate-700">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="w-40 border-r border-slate-200 px-6 py-4 text-center">Periode</th>
                                <th class="border-r border-slate-200 px-6 py-4">Rincian Kinerja 3 Kategori</th>
                                <th class="w-48 px-6 py-4 text-center">Skor Akhir & Predikat</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($riwayatKinerja as $kinerja)
                                @php $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                                <tr class="align-top hover:bg-slate-50 transition-colors">

                                    <td class="border-r border-slate-200 px-6 py-6 text-center align-middle">
                                        <div class="inline-flex min-w-[110px] flex-col rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                                            <span class="text-sm font-extrabold text-slate-800">{{ $namaBulan[$kinerja->bulan - 1] ?? $kinerja->bulan }}</span>
                                            <span class="mt-0.5 text-lg font-extrabold text-slate-900">{{ $kinerja->tahun }}</span>
                                        </div>
                                    </td>

                                    <td class="border-r border-slate-200 px-5 py-5">
                                        <div class="grid grid-cols-3 gap-3">
                                            @foreach(['litmas', 'pembimbingan', 'pengawasan'] as $kat)
                                                @php
                                                    $persenKat = $kinerja->{$kat.'_kuota'} > 0 ? ($kinerja->{$kat.'_berhasil'} / $kinerja->{$kat.'_kuota'}) * 100 : 0;
                                                    $lebarProgress = min(max($persenKat, 0), 100);
                                                @endphp
                                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3.5 shadow-sm flex flex-col justify-center min-w-0">
                                                    <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-2 truncate" title="{{ $kat }}">{{ $kat }}</p>
                                                    <div class="flex items-end justify-between gap-2 mb-2.5">
                                                        <div class="flex items-baseline gap-1 whitespace-nowrap">
                                                            <span class="text-lg font-extrabold text-slate-800 leading-none">{{ $kinerja->{$kat.'_berhasil'} }}</span>
                                                            <span class="text-[11px] font-semibold text-slate-400">/ {{ $kinerja->{$kat.'_kuota'} }}
                                                                @if($kat === 'litmas') Litmas
                                                                @elseif($kat === 'pembimbingan') Kerja
                                                                @elseif($kat === 'pengawasan') Apel
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <span class="text-xs font-bold text-blue-700 whitespace-nowrap">{{ number_format($persenKat, 1) }}%</span>
                                                    </div>
                                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                                                        <div class="h-full rounded-full bg-blue-600 transition-all" style="width: {{ $lebarProgress }}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td class="px-5 py-6 text-center align-middle flex flex-col items-center justify-center h-full min-h-[110px]">
                                        <span class="block text-2xl font-extrabold text-slate-800 tracking-tight leading-none mb-2">{{ $kinerja->rata_rata }}<span class="text-base text-slate-500">%</span></span>
                                        <span class="inline-flex min-w-[110px] items-center justify-center rounded-xl border px-3 py-1.5 text-xs font-extrabold shadow-sm {{ $kinerja->predikat == 'Sangat Baik' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($kinerja->predikat == 'Baik' ? 'border-blue-200 bg-blue-50 text-blue-700' : ($kinerja->predikat == 'Cukup' ? 'border-yellow-200 bg-yellow-50 text-yellow-700' : ($kinerja->predikat == 'Kurang' ? 'border-orange-200 bg-orange-50 text-orange-700' : 'border-red-200 bg-red-50 text-red-700'))) }}">
                                            {{ $kinerja->predikat }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-16 text-center">
                                        <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <span class="block text-sm font-medium text-slate-500">Belum ada riwayat kinerja yang tersimpan.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- BAGIAN 3: FILTER & DAFTAR LAPORAN KLIEN -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md">

                <div class="flex items-center gap-4 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:px-6">
                    <h3 class="flex items-center gap-3 text-base font-bold text-slate-800">
                        <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Pencarian Laporan Klien
                    </h3>
                </div>

                <div class="p-5 sm:p-6 border-b border-slate-200">
                    <form method="GET" action="{{ route('dashboard.pengawas') }}" class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">
                        <div class="md:col-span-5 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <!-- FITUR AUTO-SUBMIT: debounce.750ms -->
                            <input type="text" name="search" value="{{ request('search') }}" x-on:input.debounce.750ms="$el.closest('form').submit()" placeholder="Cari nama atau NIK/Nomor Registrasi klien..." class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white pl-11 pr-4 py-3 text-sm font-medium text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700">
                        </div>
                        <div class="md:col-span-3">
                            <!-- FITUR AUTO-SUBMIT: onchange -->
                            <select name="month" onchange="this.form.submit()" class="block min-h-[48px] w-full cursor-pointer rounded-xl border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700">
                                <option value="">-- Semua Bulan --</option>
                                @php $bulansFilter = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                                @foreach($bulansFilter as $index => $namaBulan) <option value="{{ $index + 1 }}" {{ request('month') == ($index + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option> @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <!-- FITUR AUTO-SUBMIT: onchange -->
                            <select name="year" onchange="this.form.submit()" class="block min-h-[48px] w-full cursor-pointer rounded-xl border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700">
                                <option value="">-- Semua Tahun --</option>
                                @if(isset($availableYears) && count($availableYears) > 0)
                                    @foreach($availableYears as $year) <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option> @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="md:col-span-2 flex gap-2">
                            <button type="submit" class="inline-flex min-h-[48px] w-full items-center justify-center rounded-xl bg-blue-900 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">Cari</button>
                            @if(request('search') || request('month') || request('year'))
                                <a href="{{ route('dashboard.pengawas') }}" class="inline-flex min-h-[48px] items-center justify-center rounded-xl border border-white bg-red-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-red-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-slate-200" title="Reset Pencarian">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="flex flex-col items-start justify-between gap-4 bg-white px-5 py-4 sm:flex-row sm:items-center sm:px-6">
                    <h3 class="font-bold text-lg text-slate-800">Daftar Absensi Wajib Klien Anda</h3>
                    <span class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-800 border border-blue-200 shadow-sm">
                        Total: {{ $semuaAbsensi->count() }} Laporan
                    </span>
                </div>

                <div class="custom-scrollbar overflow-x-auto">
                    <table class="w-full min-w-[880px] text-left text-slate-700">
                        <thead class="border-y border-slate-200 bg-slate-50/80">
                            <tr>
                                <th scope="col" class="w-40 px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-500">Tanggal</th>
                                <th scope="col" class="w-64 px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-500 border-l border-slate-200">Nama Klien</th>
                                <th scope="col" class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-500 border-l border-slate-200">Nama Kegiatan Sosial</th>
                                <th scope="col" class="w-36 px-6 py-4 text-center text-xs font-bold uppercase tracking-widest text-slate-500 border-l border-slate-200">Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-sm">
                            @forelse($semuaAbsensi as $item)
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4 align-top font-bold text-slate-800">{{ \Carbon\Carbon::parse($item->tanggal_waktu)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 align-top border-l border-slate-100">
                                        <span class="block font-bold text-slate-900 text-base">{{ $item->narapidana->nama }}</span>
                                        <span class="mt-1 block text-xs font-medium text-slate-500">NIK/No Reg: {{ $item->narapidana->nomor_induk }}</span>
                                    </td>
                                    <td class="px-6 py-4 leading-relaxed align-top border-l border-slate-100 font-medium">{{ $item->jenis_kegiatan }}</td>
                                    <td class="px-6 py-4 text-center align-middle border-l border-slate-100">
                                        @if($item->bukti_file)
                                            <button type="button" @click.prevent="showModal = true; imgSrc = '{{ asset('storage/' . $item->bukti_file) }}'" class="group inline-flex min-h-[36px] items-center justify-center rounded-lg border border-slate-200 bg-yellow-100 px-4 py-2 text-xs font-bold text-slate-900 shadow-sm transition hover:border-slate-300 hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-blue-100">
                                                <svg class="mr-1.5 h-4 w-4 text-slate-700 transition-colors group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Lihat Foto
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Tanpa Foto</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <span class="block text-sm font-medium text-slate-500">Belum ada laporan absensi klien ditemukan.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

        <!-- MODAL FOTO -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/90 px-4 py-6 backdrop-blur-md transition-opacity sm:px-6" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 flex w-full max-w-4xl flex-col items-center justify-center">

                <!-- FITUR DOWNLOAD FOTO -->
                <div class="flex flex-wrap items-center justify-center gap-3 mb-4">
                    <a :href="imgSrc" download="Bukti_Absensi_Klien.jpg" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border border-emerald-600 bg-emerald-600 px-5 py-2.5 font-bold text-white shadow-lg transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/50">
                        <svg class="mr-2 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download Foto
                    </a>
                    <button @click="showModal = false" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border border-red-300 bg-red-500 px-5 py-2.5 font-bold text-white shadow-lg transition hover:bg-red-300 focus:outline-none focus:ring-4 focus:ring-slate-500">
                        <svg class="mr-2 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Tutup Foto
                    </button>
                </div>

                <div class="flex max-h-[80vh] w-full items-center justify-center">
                    <img :src="imgSrc" class="max-h-[80vh] max-w-full rounded-2xl object-contain shadow-2xl ring-1 ring-white/10">
                </div>
            </div>
        </div>

        <!-- MODAL ALERT NOTIFIKASI -->
        <div x-show="showAlert" style="display: none;" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-950/65 px-4 py-6 backdrop-blur-sm transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 text-center shadow-2xl transition-all" x-transition:enter="ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                @if(session('success'))
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 ring-1 ring-inset ring-emerald-100">
                        <svg class="h-10 w-10 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="mb-2 text-2xl font-bold text-slate-900">Berhasil!</h3>
                    <p class="mb-6 text-base leading-relaxed text-slate-600">{{ session('success') }}</p>

                @elseif($errors->any())
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-red-50 ring-1 ring-inset ring-red-100">
                        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 class="mb-3 text-2xl font-bold text-slate-900">Pemberitahuan!</h3>
                    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-left text-sm leading-relaxed text-red-700">
                        <ul class="list-inside list-disc space-y-1.5">
                            @foreach(array_unique($errors->all()) as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <button @click="showAlert = false" class="min-h-[48px] w-full rounded-xl bg-blue-900 px-4 py-3 font-bold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    Tutup
                </button>
            </div>
        </div>

    </div>
</x-app-layout>

<style>
    header:has(.bapas-pengawas-header) { background-color: #f1f5f9 !important; border-bottom: 1px solid #e2e8f0; box-shadow: none !important; }
    .bapas-pengawas-header { background-color: #f1f5f9; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; border: 2px solid #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
