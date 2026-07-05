<x-app-layout>
    <x-slot name="header">
        <div class="bapas-pengawas-header flex items-center gap-4 bg-slate-100">
            <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm sm:flex">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3.75 3.75 0 003.75-3.75V8.25A3.75 3.75 0 0016.5 4.5h-9a3.75 3.75 0 00-3.75 3.75V15a3.75 3.75 0 003.75 3.75m9 0V21m-9-2.25V21M9 9.75h6m-6 3h4.5"></path></svg>
            </div>
            <h2 class="text-xl leading-tight tracking-tight text-slate-900 sm:text-2xl md:text-3xl">
                Dashboard, Selamat Datang PK <span class="font-bold">{{ Auth::user()->nama }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-6 sm:py-10" x-data="{ showModal: false, imgSrc: '', showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:space-y-8 sm:px-6 lg:px-8">

            <!-- BAGIAN FORM KINERJA PK -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-4 border-b border-blue-800 bg-blue-900 px-5 py-5 sm:px-7 sm:py-6">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white ring-1 ring-inset ring-white/20">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold leading-snug text-white sm:text-xl">Formulir Penilaian Kinerja PK</h3>
                        <p class="mt-1 text-sm leading-relaxed text-blue-100">Lengkapi capaian kinerja berdasarkan klien yang Anda awasi.</p>
                    </div>
                </div>

                <div class="p-5 sm:p-7 lg:p-8">

                    <!-- PERBAIKAN: MEMISAHKAN DATA PHP KE SCRIPT GLOBAL AGAR TIDAK ERROR HTML -->
                    @php
                        $draftData = Auth::user()->kinerja_draft ? json_decode(Auth::user()->kinerja_draft, true) : null;
                    @endphp
                    <script>
                        window.serverKinerjaDraft = {!! json_encode($draftData) !!};
                        window.saveDraftUrl = "{{ route('pengawas.save_draft') }}";
                        window.csrfToken = "{{ csrf_token() }}";
                    </script>

                    <form action="{{ route('kinerja-pk.store') }}" method="POST" enctype="multipart/form-data" x-data="{
                        bulan_kinerja: '{{ date('n') }}',
                        tahun_kinerja: '{{ date('Y') }}',
                        litmas_persen: {{ $dataLitmasRealtime['kinerja_angka'] ?? 0 }},
                        pengawasan: { kuota: '', berhasil: '' },

                        klien: {
                            @foreach($daftarKlienSaya as $k)
                                '{{ $k->id }}': '',
                            @endforeach
                        },
                        totalKlien: {{ $daftarKlienSaya->count() }},

                        jmlBekerja: 0,
                        jmlBelumBekerja: 0,
                        pembimbinganPersen: 0,
                        skorDihitung: false,

                        draftTimeout: null,

                        init() {
                            // 1. Ambil data draf dari variable global yang sudah di-inject oleh PHP
                            const serverDraft = window.serverKinerjaDraft;

                            if (serverDraft) {
                                if (serverDraft.bulan_kinerja) this.bulan_kinerja = serverDraft.bulan_kinerja;
                                if (serverDraft.tahun_kinerja) this.tahun_kinerja = serverDraft.tahun_kinerja;
                                if (serverDraft.klien) this.klien = { ...this.klien, ...serverDraft.klien };
                                if (serverDraft.pengawasan) this.pengawasan = serverDraft.pengawasan;
                                if (serverDraft.skorDihitung !== undefined) this.skorDihitung = serverDraft.skorDihitung;

                                if (this.skorDihitung) {
                                    this.hitungSkor();
                                }
                            }

                            // 2. Pantau semua perubahan untuk disimpan otomatis (Auto-Save)
                            this.$watch('bulan_kinerja', () => this.saveDraft());
                            this.$watch('tahun_kinerja', () => this.saveDraft());
                            this.$watch('skorDihitung', () => this.saveDraft());
                            this.$watch('klien', () => {
                                this.skorDihitung = false;
                                this.saveDraft();
                            }, { deep: true });
                            this.$watch('pengawasan', () => this.saveDraft(), { deep: true });
                        },

                        saveDraft() {
                            // Menyimpan jeda 1 detik agar tidak mengirim data ke database tiap 1 huruf diketik
                            clearTimeout(this.draftTimeout);
                            this.draftTimeout = setTimeout(() => {
                                const draftJSON = JSON.stringify({
                                    bulan_kinerja: this.bulan_kinerja,
                                    tahun_kinerja: this.tahun_kinerja,
                                    klien: this.klien,
                                    pengawasan: this.pengawasan,
                                    skorDihitung: this.skorDihitung
                                });

                                fetch(window.saveDraftUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': window.csrfToken,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ draft: draftJSON })
                                }).catch(err => console.error('Auto-save gagal:', err));

                            }, 1000);
                        },

                        hitungSkor() {
                            let kerja = 0;
                            let belumKerja = 0;

                            for (const key in this.klien) {
                                if (this.klien[key] === 'bekerja') kerja++;
                                else if (this.klien[key] === 'belum') belumKerja++;
                            }

                            this.jmlBekerja = kerja;
                            this.jmlBelumBekerja = belumKerja;
                            this.pembimbinganPersen = (this.totalKlien > 0) ? (kerja / this.totalKlien) * 100 : 0;
                            this.skorDihitung = true;
                        },

                        calc(kuota, berhasil) {
                            let k = parseFloat(kuota); let b = parseFloat(berhasil);
                            if (k > 0 && !isNaN(b)) return ((b / k) * 100);
                            return 0;
                        },
                        get rataRata() {
                            let total = parseFloat(this.litmas_persen) || 0;
                            total += parseFloat(this.pembimbinganPersen) || 0;
                            total += parseFloat(this.calc(this.pengawasan.kuota, this.pengawasan.berhasil)) || 0;
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

                        <!-- PILIH PERIODE BULAN & TAHUN -->
                        <div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6">
                            <div>
                                <label for="bulan_kinerja" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Periode Bulan</label>
                                <select id="bulan_kinerja" name="bulan" x-model="bulan_kinerja" required class="block min-h-[48px] w-full cursor-pointer rounded-xl border-slate-300 bg-white px-4 py-3 text-base font-medium text-slate-800 shadow-sm transition focus:border-blue-700 focus:ring-blue-700">
                                    @php $bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                                    @foreach($bulans as $index => $namaBulan) <option value="{{ $index + 1 }}">{{ $namaBulan }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="tahun_kinerja" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Periode Tahun</label>
                                <input type="number" id="tahun_kinerja" name="tahun" x-model="tahun_kinerja" required class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-base text-slate-900 shadow-sm transition focus:border-blue-700 focus:ring-blue-700">
                            </div>
                        </div>

                        <!-- KARTU 1: LITMAS -->
                        <article class="mb-6 overflow-hidden rounded-2xl border border-blue-200 bg-blue-50 transition duration-200 hover:shadow-sm">
                            <div class="border-b border-blue-200 bg-blue-100 px-5 py-4 sm:px-6">
                                <h4 class="flex items-center gap-3 text-base font-bold text-blue-950 sm:text-lg">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-900 text-sm font-extrabold text-white shadow-sm">1</span>
                                    Penelitian Kemasyarakatan (Litmas) - Monitoring Real-Time
                                </h4>
                            </div>
                            <input type="hidden" name="litmas_kuota" value="{{ $dataLitmasRealtime['jumlah'] ?? 0 }}">
                            <input type="hidden" name="litmas_berhasil" value="{{ $dataLitmasRealtime['selesai'] ?? 0 }}">

                            <div class="grid grid-cols-2 gap-4 p-5 sm:p-6 md:grid-cols-4">
                                <div class="rounded-xl border border-slate-200 bg-white p-4 text-center shadow-sm">
                                    <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Jumlah Target</span>
                                    <span class="text-2xl font-extrabold text-slate-800">{{ $dataLitmasRealtime['jumlah'] ?? 0 }}</span>
                                </div>
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-center shadow-sm">
                                    <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-emerald-700">Selesai</span>
                                    <span class="text-2xl font-extrabold text-emerald-600">{{ $dataLitmasRealtime['selesai'] ?? 0 }}</span>
                                </div>
                                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-center shadow-sm">
                                    <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-red-700">Belum Selesai</span>
                                    <span class="text-2xl font-extrabold text-red-600">{{ $dataLitmasRealtime['belum_selesai'] ?? 0 }}</span>
                                </div>
                                <div class="rounded-xl border border-blue-200 bg-blue-100 p-4 text-center shadow-sm">
                                    <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-blue-800">% Kinerja</span>
                                    <span class="text-2xl font-extrabold text-blue-700">{{ $dataLitmasRealtime['kinerja'] ?? '0%' }}</span>
                                </div>
                            </div>
                        </article>

                        <!-- KARTU 2: PEMBIMBINGAN -->
                        <article class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 transition duration-200 hover:border-blue-200 hover:shadow-sm">
                            <div class="border-b border-slate-200 bg-white px-5 py-4 sm:px-6">
                                <h4 class="flex items-center gap-3 text-base font-bold text-slate-900 sm:text-lg">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-900 text-sm font-extrabold text-white shadow-sm">2</span>
                                    Pembimbingan - Status Kinerja Klien
                                </h4>
                            </div>

                            <div class="p-5 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                                <!-- List Klien Kiri -->
                                <div class="lg:col-span-8">
                                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                                        <div class="bg-slate-100 border-b border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 flex justify-between">
                                            <span>Nama Klien / Narapidana</span>
                                            <span>Status Pekerjaan</span>
                                        </div>
                                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                                            @forelse($daftarKlienSaya as $klien)
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 hover:bg-slate-50 transition">
                                                    <div class="text-sm font-bold text-slate-800">
                                                        {{ $loop->iteration }}. {{ $klien->nama }}
                                                        <span class="block text-xs font-medium text-slate-500 mt-0.5">NIK: {{ $klien->nomor_induk }}</span>
                                                    </div>
                                                    <select name="pembimbingan_klien[{{ $klien->id }}]"
                                                            x-model="klien['{{ $klien->id }}']"
                                                            required
                                                            class="block w-full sm:w-[190px] rounded-lg border-slate-300 py-2 px-3 text-sm shadow-sm cursor-pointer focus:ring-blue-500 focus:border-blue-500 transition-colors border"
                                                            :class="{
                                                                'bg-emerald-50 text-emerald-700 font-bold border-emerald-300': klien['{{ $klien->id }}'] === 'bekerja',
                                                                'bg-red-50 text-red-700 font-medium border-red-300': klien['{{ $klien->id }}'] === 'belum',
                                                                'bg-white text-slate-500 border-slate-300': klien['{{ $klien->id }}'] === '' || klien['{{ $klien->id }}'] === undefined
                                                            }">
                                                        <option value="" disabled>-- Pilih Status Bekerja --</option>
                                                        <option value="bekerja">Berhasil Bekerja</option>
                                                        <option value="belum">Belum Bekerja</option>
                                                    </select>
                                                </div>
                                            @empty
                                                <div class="p-6 text-center text-sm text-slate-500 italic">Belum ada klien ditugaskan.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Panel Ringkasan Kanan -->
                                <div class="lg:col-span-4 flex flex-col h-full gap-4">
                                    <div class="bg-indigo-900 rounded-xl p-6 text-white shadow-md flex-1 flex flex-col justify-center relative overflow-hidden">

                                        <!-- Ornamen desain blur di pojok kanan atas -->
                                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-5 rounded-full blur-xl pointer-events-none"></div>

                                        <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1 relative z-10">Skor Kinerja Pembimbingan</p>

                                        <div class="flex items-end gap-2 mb-5 relative z-10">
                                            <span class="text-5xl font-extrabold" x-text="pembimbinganPersen.toFixed(1)"></span>
                                            <span class="text-2xl font-bold text-indigo-300 mb-1.5">%</span>
                                        </div>

                                        <!-- Tombol Hitung -->
                                        <button type="button" @click="hitungSkor()" class="w-full bg-emerald-500 hover:bg-emerald-400 text-indigo-950 font-extrabold py-3 px-4 rounded-xl text-sm transition shadow-[0_0_15px_rgba(16,185,129,0.3)] hover:shadow-[0_0_20px_rgba(16,185,129,0.5)] mb-4 relative z-10 focus:outline-none focus:ring-4 focus:ring-emerald-300/50">
                                            Hitung Skor
                                        </button>

                                        <!-- Rincian Hasil (Muncul Setelah Dihitung) -->
                                        <div x-show="skorDihitung" x-transition class="grid grid-cols-3 gap-2 text-center text-xs border-t border-indigo-700/50 pt-4 relative z-10" style="display: none;">
                                            <div class="bg-indigo-800/50 rounded-lg p-2 flex flex-col items-center justify-center">
                                                <span class="block text-indigo-300 mb-0.5 leading-none">Total</span>
                                                <span class="font-bold text-sm leading-none">{{ $daftarKlienSaya->count() }}</span>
                                            </div>
                                            <div class="bg-emerald-800/30 rounded-lg p-2 border border-emerald-500/30 flex flex-col items-center justify-center">
                                                <span class="block text-emerald-300 mb-0.5 leading-none">Bekerja</span>
                                                <span class="font-bold text-sm text-emerald-400 leading-none" x-text="jmlBekerja"></span>
                                            </div>
                                            <div class="bg-red-800/30 rounded-lg p-2 border border-red-500/30 flex flex-col items-center justify-center">
                                                <span class="block text-red-300 mb-0.5 leading-none">Belum</span>
                                                <span class="font-bold text-sm text-red-400 leading-none" x-text="jmlBelumBekerja"></span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </article>

                        <!-- KARTU 3: PENGAWASAN (OTOMATIS) -->
                        <article class="mb-8 overflow-hidden rounded-2xl border border-blue-200 bg-blue-50 transition duration-200 hover:shadow-sm">
                            <div class="border-b border-blue-200 bg-blue-100 px-5 py-4 sm:px-6">
                                <h4 class="flex items-center gap-3 text-base font-bold text-blue-950 sm:text-lg">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-900 text-sm font-extrabold text-white shadow-sm">3</span>
                                    Pengawasan (Otomatis dari Absensi Klien)
                                </h4>
                            </div>

                            <div class="grid grid-cols-1 items-start gap-5 p-5 sm:p-6 lg:grid-cols-3">
                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-800">Total Klien Diampu</label>
                                    <input type="number" name="pengawasan_kuota" value="{{ $dataPengawasanOtomatis['kuota'] }}" readonly class="block min-h-[44px] w-full rounded-xl border-slate-200 bg-slate-100 px-4 text-base shadow-sm font-bold text-slate-600">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-800">Klien Berhasil Absen</label>
                                    <input type="number" name="pengawasan_berhasil" value="{{ $dataPengawasanOtomatis['berhasil'] }}" readonly class="block min-h-[44px] w-full rounded-xl border-slate-200 bg-slate-100 px-4 text-base shadow-sm font-bold text-emerald-700">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-800">Skor Pengawasan %</label>
                                    <div class="relative">
                                        <input type="text" value="{{ $dataPengawasanOtomatis['kuota'] > 0 ? number_format(($dataPengawasanOtomatis['berhasil'] / $dataPengawasanOtomatis['kuota']) * 100, 1) : 0 }}" readonly class="block min-h-[44px] w-full cursor-not-allowed rounded-xl border-slate-300 bg-slate-200 px-4 font-extrabold text-blue-800 shadow-sm">
                                        <span class="absolute right-3 top-2.5 font-bold text-slate-600">%</span>
                                    </div>
                                </div>
                            </div>
                        </article>


                        <!-- ESTIMASI KINERJA AKHIR & TOMBOL SUBMIT -->
                        <div class="mb-6 flex flex-col items-stretch justify-between gap-5 rounded-2xl border border-blue-200 bg-blue-50 p-5 sm:p-6 md:flex-row md:items-center">
                            <div class="text-center md:text-left">
                                <h4 class="mb-1 text-xl font-extrabold text-blue-950">Estimasi Kinerja Akhir</h4>
                                <p class="text-sm font-medium leading-relaxed text-blue-800">Nilai dihitung otomatis berdasarkan rasio keberhasilan 3 kategori di atas (Skor Pembimbingan harus dihitung terlebih dahulu).</p>
                            </div>

                            <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 md:w-auto">
                                <div class="min-w-[150px] rounded-xl border border-blue-200 bg-white px-6 py-3 text-center shadow-sm">
                                    <span class="mb-1 block text-xs font-bold uppercase tracking-widest text-slate-500">Rata-Rata</span>
                                    <span class="text-2xl font-extrabold text-blue-800" x-text="rataRata + '%'"></span>
                                </div>
                                <div class="min-w-[150px] rounded-xl border border-blue-200 bg-white px-6 py-3 text-center shadow-sm">
                                    <span class="mb-1 block text-xs font-bold uppercase tracking-widest text-slate-500">Predikat</span>
                                    <span class="font-extrabold text-2xl" :class="{'text-green-600': predikat === 'Sangat Baik', 'text-blue-600': predikat === 'Baik', 'text-yellow-500': predikat === 'Cukup', 'text-orange-500': predikat === 'Kurang', 'text-red-600': predikat === 'Sangat Kurang'}" x-text="predikat"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex min-h-[52px] w-full items-center justify-center rounded-xl bg-emerald-700 px-8 py-3.5 text-base font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200 md:w-auto">
                                <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Laporan Kinerja
                            </button>
                        </div>

                    </form>
                </div>
            </section>


            <!-- BAGIAN RIWAYAT PENILAIAN -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-4 border-b border-slate-200 bg-white px-5 py-5 sm:px-7 sm:py-6">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg></span>
                    <h3 class="text-lg font-bold leading-snug text-slate-900 sm:text-xl">Riwayat Penilaian Kinerja Saya</h3>
                </div>

                <div class="custom-scrollbar overflow-x-auto">
                    <table class="w-full min-w-[1120px] table-fixed text-left text-slate-700">
                        <thead class="border-b border-slate-200 bg-slate-100 text-sm font-semibold text-slate-700">
                            <tr>
                                <th rowspan="2" class="w-40 border-r border-slate-200 px-6 py-4 align-middle text-xs font-bold uppercase tracking-wide">Periode</th>
                                <th colspan="2" class="border-r border-slate-200 px-6 py-3 text-center text-xs font-bold uppercase tracking-wide">Kategori & Bukti</th>
                                <th rowspan="2" class="w-44 px-6 py-4 text-center align-middle text-xs font-bold uppercase tracking-wide">Predikat</th>
                            </tr>
                            <tr class="border-t border-slate-200 bg-slate-50">
                                <th class="w-[360px] border-r border-slate-200 px-6 py-3 text-xs font-bold uppercase tracking-wide text-slate-600">Kategori</th>
                                <th class="w-[420px] border-r border-slate-200 px-6 py-3 text-xs font-bold uppercase tracking-wide text-slate-600">Bukti File</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($riwayatKinerja as $kinerja)
                                @php $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                                <tr class="align-top hover:bg-blue-50/40 transition">
                                    <!-- Kolom Periode -->
                                    <td class="border-r border-slate-200 px-6 py-6 text-center">
                                        <div class="inline-flex min-w-[112px] flex-col rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 shadow-sm">
                                            <span class="text-sm font-extrabold text-blue-950">{{ $namaBulan[$kinerja->bulan - 1] ?? $kinerja->bulan }}</span>
                                            <span class="mt-0.5 text-lg font-extrabold text-blue-900">{{ $kinerja->tahun }}</span>
                                            <span class="mt-2 border-t border-blue-100 pt-2 text-xs font-medium text-blue-700">Rata: {{ $kinerja->rata_rata }}%</span>
                                        </div>
                                    </td>

                                    <!-- Kolom Persentase Per Kategori -->
                                    <td class="border-r border-slate-200 px-5 py-5">
                                        <div class="space-y-3">
                                            @foreach(['litmas', 'pembimbingan', 'pengawasan'] as $kat)
                                                @php
                                                    $persenKat = $kinerja->{$kat.'_kuota'} > 0 ? ($kinerja->{$kat.'_berhasil'} / $kinerja->{$kat.'_kuota'}) * 100 : 0;
                                                    $lebarProgress = min(max($persenKat, 0), 100);
                                                @endphp
                                                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-blue-200 hover:shadow-md transition">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <p class="text-xs font-extrabold uppercase text-slate-600">{{ $kat }}</p>
                                                        <span class="inline-flex rounded-lg bg-blue-50 px-2 py-1 text-xs font-bold text-blue-800">
                                                            {{ $kinerja->{$kat.'_berhasil'} }}/{{ $kinerja->{$kat.'_kuota'} }} ({{ number_format($persenKat, 1) }}%)
                                                        </span>
                                                    </div>
                                                    <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                                        <div class="h-full rounded-full bg-blue-800" style="width: {{ $lebarProgress }}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Kolom Bukti File -->
                                    <td class="border-r border-slate-200 px-5 py-5">
                                        <div class="space-y-3">
                                            @foreach(['litmas', 'pembimbingan', 'pengawasan'] as $kat)
                                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                                    <p class="mb-2 text-xs font-extrabold uppercase text-slate-600">{{ $kat }}</p>

                                                    @if($kat === 'pembimbingan')
                                                        <div class="text-[11px] text-slate-500 font-medium italic">
                                                            Status pekerjaan klien tersimpan secara otomatis (Tanpa File/Link).
                                                        </div>
                                                    @else
                                                        <div class="space-y-2">
                                                            @php
                                                                $rawFile = $kinerja->{$kat.'_file'};
                                                                $files = json_decode($rawFile, true);
                                                                if (!is_array($files)) $files = $rawFile ? [$rawFile] : [];
                                                            @endphp
                                                            @forelse($files as $file)
                                                                @php
                                                                    $filePath = is_array($file) ? ($file['path'] ?? '') : $file;
                                                                    $fileName = is_array($file) ? ($file['name'] ?? basename($filePath)) : basename($filePath);
                                                                @endphp
                                                                @if($filePath)
                                                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="flex min-h-[36px] w-full items-center gap-2 rounded-lg border bg-white px-2 py-1.5 text-[12px] text-blue-700 hover:border-blue-300 transition">
                                                                        📄 <span class="truncate">{{ $fileName }}</span>
                                                                    </a>
                                                                @endif
                                                            @empty
                                                                <span class="block text-[11px] italic text-slate-400">- File tidak diunggah -</span>
                                                            @endforelse

                                                            @if(!empty($kinerja->{$kat.'_link'}))
                                                                <a href="{{ $kinerja->{$kat.'_link'} }}" target="_blank" class="flex min-h-[36px] w-full items-center gap-2 rounded-lg border bg-emerald-50 px-2 py-1.5 text-[12px] text-emerald-800 hover:border-emerald-300 transition">
                                                                    🔗 <span>Buka Link G-Drive</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Kolom Predikat -->
                                    <td class="px-5 py-6 text-center">
                                        <span class="inline-flex min-w-[120px] items-center justify-center rounded-xl border px-4 py-2 text-sm font-extrabold shadow-sm {{ $kinerja->predikat == 'Sangat Baik' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($kinerja->predikat == 'Baik' ? 'border-blue-200 bg-blue-50 text-blue-700' : ($kinerja->predikat == 'Cukup' ? 'border-yellow-200 bg-yellow-50 text-yellow-700' : ($kinerja->predikat == 'Kurang' ? 'border-orange-200 bg-orange-50 text-orange-700' : 'border-red-200 bg-red-50 text-red-700'))) }}">
                                            {{ $kinerja->predikat }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-14 text-center text-slate-500">Belum ada riwayat kinerja yang tersimpan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- BAGIAN FILTER LAPORAN KLIEN -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-4 border-b border-slate-200 bg-white px-5 py-5 sm:px-7 sm:py-6">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold leading-snug text-slate-900 sm:text-xl">Filter Cari Laporan Klien Bimbingan Saya</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-500">Gunakan nama, nomor induk, bulan, atau tahun untuk mempersempit data.</p>
                    </div>
                </div>

                <div class="p-5 sm:p-7 lg:p-8">
                    <form method="GET" action="{{ route('dashboard.pengawas') }}" class="grid grid-cols-1 items-end gap-5 md:grid-cols-12">
                        <div class="md:col-span-5">
                            <label for="search" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Cari Nama/Nomor Induk</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Ketik nama/NIK klien..." class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-base text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700">
                        </div>
                        <div class="md:col-span-3">
                            <label for="month" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Bulan</label>
                            <select id="month" name="month" class="block min-h-[48px] w-full cursor-pointer rounded-xl border-slate-300 bg-white px-4 py-3 text-base font-medium text-slate-800 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700">
                                <option value="">-- Semua Bulan</option>
                                @php $bulansFilter = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                                @foreach($bulansFilter as $index => $namaBulan) <option value="{{ $index + 1 }}" {{ request('month') == ($index + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option> @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="year" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Tahun</label>
                            <select id="year" name="year" class="block min-h-[48px] w-full cursor-pointer rounded-xl border-slate-300 bg-white px-4 py-3 text-base font-medium text-slate-800 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700">
                                <option value="">-- Semua Tahun</option>
                                @if(isset($availableYears) && count($availableYears) > 0)
                                    @foreach($availableYears as $year) <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option> @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="md:col-span-2 flex flex-col gap-2">
                            <button type="submit" class="inline-flex min-h-[48px] w-full items-center justify-center rounded-xl bg-blue-900 px-4 py-3 font-bold text-white shadow-sm transition hover:bg-blue-800 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-blue-200">
                                <svg class="mr-2 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Cari
                            </button>
                            @if(request('search') || request('month') || request('year'))
                                <a href="{{ route('dashboard.pengawas') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-200">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </section>

            <!-- BAGIAN DAFTAR LAPORAN ABSENSI KLIEN -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col items-start justify-between gap-4 border-b border-slate-200 bg-white px-5 py-5 sm:flex-row sm:items-center sm:px-7 sm:py-6">
                    <h3 class="flex items-center gap-4 text-lg font-bold leading-snug text-slate-900 sm:text-xl">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg></span>
                        Daftar Laporan Klien Bimbingan Saya
                    </h3>
                    <span class="inline-flex min-h-[40px] items-center rounded-xl bg-blue-900 px-4 py-2 text-sm font-bold text-white shadow-sm">
                        Total: {{ $semuaAbsensi->count() }} Laporan
                    </span>
                </div>

                <div class="custom-scrollbar overflow-x-auto">
                    <table class="w-full min-w-[880px] text-left text-slate-700">
                        <thead class="border-b border-slate-200 bg-slate-100">
                            <tr>
                                <th scope="col" class="w-48 px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-700">Tanggal Kegiatan</th>
                                <th scope="col" class="w-64 px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-700">Data Klien Bimbingan</th>
                                <th scope="col" class="px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-700">Jenis Kegiatan Sosial</th>
                                <th scope="col" class="w-36 px-6 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-700">Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-base">
                            @forelse($semuaAbsensi as $item)
                                <tr class="transition-colors hover:bg-blue-50/60">
                                    <td class="whitespace-nowrap px-6 py-5 align-top font-bold text-slate-900">{{ \Carbon\Carbon::parse($item->tanggal_waktu)->format('d F Y') }}</td>
                                    <td class="px-6 py-5 align-top">
                                        <span class="block text-lg font-bold text-blue-950">{{ $item->narapidana->nama }}</span>
                                        <span class="mt-1 inline-block rounded-md bg-slate-100 px-2.5 py-1 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">NIK: {{ $item->narapidana->nomor_induk }}</span>
                                    </td>
                                    <td class="px-6 py-5 leading-relaxed align-top">{{ $item->jenis_kegiatan }}</td>
                                    <td class="px-6 py-5 text-center align-top">
                                        <button type="button" @click.prevent="showModal = true; imgSrc = '{{ asset('storage/' . $item->bukti_file) }}'" class="inline-flex min-h-[40px] w-full items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-800 transition hover:border-blue-300 hover:bg-blue-100 focus:outline-none focus:ring-4 focus:ring-blue-100">Lihat Foto</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-14 text-center text-slate-500">
                                        <svg class="mx-auto mb-3 h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <span class="block text-lg font-medium text-slate-600">Belum ada laporan absensi klien.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

        <!-- MODAL FOTO -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/95 px-4 py-6 backdrop-blur-md transition-opacity sm:px-6" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 flex w-full max-w-5xl flex-col items-center justify-center">
                <button @click="showModal = false" class="mb-4 inline-flex min-h-[44px] items-center justify-center rounded-xl border border-red-400/50 bg-red-600 px-5 py-2.5 font-bold text-white shadow-lg transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300/30">
                    <svg class="mr-2 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Tutup Foto
                </button>
                <div class="flex max-h-[82vh] w-full items-center justify-center">
                    <img :src="imgSrc" class="max-h-[82vh] max-w-full rounded-2xl object-contain shadow-[0_24px_80px_rgba(0,0,0,0.55)]">
                </div>
            </div>
        </div>

        <!-- MODAL ALERT -->
        <div x-show="showAlert" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/65 px-4 py-6 backdrop-blur-sm transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xl transition-all sm:p-8">

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
                    <h3 class="mb-3 text-2xl font-bold text-slate-900">Terjadi Kesalahan!</h3>
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

<script>
    function updateFileList(key, input) {
        const container = document.getElementById('file-list-' + key);
        container.innerHTML = '';
        if (input.files.length > 0) {
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const fileUrl = URL.createObjectURL(file);
                const fileDiv = document.createElement('div');
                fileDiv.className = 'flex min-w-0 items-center gap-1 rounded border border-slate-200 bg-slate-50 px-2 py-1.5 mb-1';

                fileDiv.innerHTML = `
                    <span class="shrink-0 text-[12px] text-slate-400">📄</span>
                    <a href="${fileUrl}" target="_blank" class="min-w-0 truncate text-[12px] font-medium text-blue-800 hover:text-blue-950 hover:underline">
                        ${file.name}
                    </a>
                `;
                container.appendChild(fileDiv);
            }
        }
    }
</script>

<style>
    header:has(.bapas-pengawas-header) { background-color: #f1f5f9 !important; border-bottom: 1px solid #e2e8f0; box-shadow: none !important; }
    .bapas-pengawas-header { background-color: #f1f5f9; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #94a3b8 #e2e8f0; }
    .custom-scrollbar::-webkit-scrollbar { width: 10px; height: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border: 2px solid #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
</style>
