<x-app-layout>

    <!-- PERBAIKAN: Tambahkan variabel showAlert di dalam x-data -->
    <div x-data="{
            sidebarOpen: false,
            showImageModal: false,
            images: [],
            currentIndex: 0,
            showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }},
            openImageModal(imgArray) {
                this.images = imgArray;
                this.currentIndex = 0;
                this.showImageModal = true;
            },
            nextImage() { if (this.currentIndex < this.images.length - 1) this.currentIndex++; },
            prevImage() { if (this.currentIndex > 0) this.currentIndex--; }
         }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0">
            <div class="p-4 sm:p-6 lg:p-8 space-y-8 flex-1 overflow-y-auto">
                <div class="bapas-admin-header flex items-center gap-4 bg-slate-100">
                    <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A9.004 9.004 0 0112 15c2.133 0 4.094.742 5.637 1.982M15 11a3 3 0 11-6 0 3 3 0 016 0zm6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                        Dashboard Administrator, Selamat Datang {{ Auth::user()->nama }}!
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 lg:gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Total PK/Pengawas</p>
                            <p class="text-3xl font-extrabold text-slate-900">{{ $totalPengawas }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Total Klien Bapas</p>
                            <p class="text-3xl font-extrabold text-slate-900">{{ $totalNarapidana }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Total Absensi/Laporan Wajib Klien</p>
                            <p class="text-3xl font-extrabold text-slate-900">{{ $totalAbsensi }}</p>
                        </div>
                    </div>
                </div>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-5 border-b border-slate-200 bg-white px-5 py-5 xl:flex-row xl:items-center xl:justify-between sm:px-7 sm:py-6">
                        <h3 class="flex items-center gap-4 text-base font-bold leading-snug text-slate-900 sm:text-base">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            Kalender Absensi/Laporan Wajib (Seluruh Klien)
                        </h3>

                        <div class="flex w-full flex-col sm:flex-row sm:items-center gap-3 xl:w-auto">
                            <form method="GET" action="{{ route('dashboard.admin') }}" class="m-0 flex min-h-[44px] w-full items-center overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm transition focus-within:border-blue-700 focus-within:ring-4 focus-within:ring-blue-100 sm:w-auto">
                                <div class="flex self-stretch items-center justify-center border-r border-slate-200 bg-slate-50 px-3 text-slate-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 00-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                </div>
                                <select name="year" onchange="this.form.submit()" class="block min-h-[44px] w-full cursor-pointer border-0 py-2.5 pl-3 pr-9 text-sm font-bold text-slate-800 focus:ring-0 sm:w-auto">
                                    @foreach($availableYears as $year) <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>Tahun {{ $year }}</option> @endforeach
                                </select>
                            </form>
                            <span class="inline-flex min-h-[44px] w-full items-center justify-center rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-center text-sm font-semibold text-amber-800 sm:w-auto">
                                Geser kalender ke kiri/kanan &rarr;
                            </span>
                        </div>
                    </div>

                    @php
                        $bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $absensiMap = [];
                        foreach($absensiKalender as $rekam) {
                            $tgl = \Carbon\Carbon::parse($rekam->tanggal_waktu);
                            $absensiMap[$tgl->month][$tgl->day][] = $rekam;
                        }
                    @endphp

                    <div class="p-4 sm:p-6 lg:p-8 bg-slate-50/50">
                        <div class="custom-scrollbar overflow-x-auto pb-5 snap-x snap-mandatory">
                            <div class="flex min-w-max gap-4 px-1 pb-1">
                                @for($m = 1; $m <= 12; $m++)
                                    <article class="w-64 shrink-0 snap-start overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md sm:w-72">
                                        <h4 class="border-b border-slate-200 bg-slate-50 px-4 py-3.5 text-center text-base font-bold text-slate-800">{{ $bulans[$m-1] }}</h4>
                                        <div class="grid grid-cols-5 gap-2 p-4">
                                            @php $daysInMonth = \Carbon\Carbon::create($selectedYear, $m)->daysInMonth; @endphp
                                            @for($d = 1; $d <= $daysInMonth; $d++)
                                                @if(isset($absensiMap[$m][$d]))
                                                    @php
                                                        $count = count($absensiMap[$m][$d]);
                                                        $first = $absensiMap[$m][$d][0];
                                                        $imageUrls = collect($absensiMap[$m][$d])->map(function($item) { return asset('storage/' . $item->bukti_file); })->toJson();
                                                    @endphp
                                                    <button type="button" @click.prevent="openImageModal({{ $imageUrls }})" class="group relative block aspect-square w-full overflow-hidden rounded-lg border-2 border-blue-500 bg-blue-50 shadow-sm transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-200">
                                                        <img src="{{ asset('storage/' . $first->bukti_file) }}" alt="Bukti" class="h-full w-full object-cover transition duration-300 group-hover:scale-110">
                                                        <span class="absolute inset-0 ring-1 ring-inset ring-black/10"></span>
                                                        @if($count > 1)
                                                            <span class="absolute bottom-0 right-0 bg-blue-900 text-white text-[11px] font-bold px-1.5 py-0.5 rounded-tl-lg shadow">+{{ $count - 1 }}</span>
                                                        @endif
                                                    </button>
                                                @else
                                                    <div class="flex aspect-square w-full items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-sm font-semibold text-slate-400">{{ $d }}</div>
                                                @endif
                                            @endfor
                                        </div>
                                    </article>
                                @endfor
                            </div>
                        </div>
                    </div>
                </section>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8">

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-gradient-to-r from-indigo-900 to-blue-900 px-5 py-4 flex justify-between items-center text-white border-b border-indigo-800">
                            <h3 class="font-bold text-base flex items-center gap-2">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                5 Akun PK/Pengawas Terbaru
                            </h3>
                            <a href="{{ route('admin.pengawas.index') }}" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors ring-1 ring-inset ring-white/20">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto p-0 flex-1">
                            <table class="w-full text-left text-sm text-slate-700">
                                <thead><tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs uppercase tracking-wider"><th class="px-5 py-3 font-bold">Nama PK/Pengawas</th><th class="px-5 py-3 text-right font-bold">Nomor Induk (NRP/NIP)</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanPengawas as $pk)
                                        <tr class="hover:bg-slate-50"><td class="px-5 py-3.5 font-bold text-slate-900">{{ $pk->nama }}</td><td class="px-5 py-3.5 text-right font-medium text-slate-600">{{ $pk->nomor_induk }}</td></tr>
                                    @empty
                                        <tr><td colspan="2" class="py-8 text-center italic text-slate-400">Belum ada data pendaftar</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-gradient-to-r from-emerald-800 to-teal-800 px-5 py-4 flex justify-between items-center text-white border-b border-emerald-700">
                            <h3 class="font-bold text-base flex items-center gap-2">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                5 Klien Bapas Terbaru
                            </h3>
                            <a href="{{ route('admin.narapidana.index') }}" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors ring-1 ring-inset ring-white/20">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto p-0 flex-1">
                            <table class="w-full text-left text-sm text-slate-700">
                                <thead><tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs uppercase tracking-wider"><th class="px-5 py-3 font-bold">Nama Klien</th><th class="px-5 py-3 text-right font-bold">Nomor Induk (NIK/Registrasi)</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanNarapidana as $napi)
                                        <tr class="hover:bg-slate-50"><td class="px-5 py-3.5 font-bold text-slate-900">{{ $napi->nama }}</td><td class="px-5 py-3.5 text-right font-medium text-slate-600">{{ $napi->nomor_induk }}</td></tr>
                                    @empty
                                        <tr><td colspan="2" class="py-8 text-center italic text-slate-400">Belum ada data pendaftar</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-5 py-4 flex justify-between items-center text-white border-b border-amber-500">
                            <h3 class="font-bold text-base flex items-center gap-2">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                5 Penilaian Kinerja PK Terbaru
                            </h3>
                            <a href="{{ route('admin.kinerja.index') }}" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors ring-1 ring-inset ring-white/20">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto p-0 flex-1">
                            <table class="w-full text-left text-sm text-slate-700">
                                <thead><tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs uppercase tracking-wider"><th class="px-5 py-3 font-bold">Nama PK/Pengawas</th><th class="px-5 py-3 font-bold">Periode</th><th class="px-5 py-3 text-right font-bold">Predikat</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanKinerja as $kinerja)
                                        <tr class="hover:bg-slate-50"><td class="px-5 py-3.5 font-bold text-slate-900">{{ $kinerja->pengawas->nama ?? '-' }}</td><td class="px-5 py-3.5 font-medium">{{ $kinerja->bulan }}/{{ $kinerja->tahun }}</td><td class="px-5 py-3.5 text-right font-extrabold text-amber-600">{{ $kinerja->predikat }}</td></tr>
                                    @empty
                                        <tr><td colspan="3" class="py-8 text-center italic text-slate-400">Belum ada data laporan kinerja</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-gradient-to-r from-teal-700 to-emerald-700 px-5 py-4 flex justify-between items-center text-white border-b border-teal-600">
                            <h3 class="font-bold text-base flex items-center gap-2">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                5 Laporan Absensi Terbaru
                            </h3>
                            <a href="{{ route('admin.absensi.index') }}" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors ring-1 ring-inset ring-white/20">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto p-0 flex-1">
                            <table class="w-full text-left text-sm text-slate-700">
                                <thead><tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs uppercase tracking-wider"><th class="px-5 py-3 font-bold">Tanggal</th><th class="px-5 py-3 font-bold">Nama Klien</th><th class="px-5 py-3 text-right font-bold">Dibimbing Oleh PK</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanAbsensi as $absen)
                                        <tr class="hover:bg-slate-50"><td class="px-5 py-3.5 font-bold">{{ \Carbon\Carbon::parse($absen->tanggal_waktu)->format('d/m/y') }}</td><td class="px-5 py-3.5 font-bold text-slate-900">{{ $absen->narapidana->nama ?? '-' }}</td><td class="px-5 py-3.5 text-right font-medium text-xs">{{ $absen->pengawas->nama ?? 'Belum Dipilih' }}</td></tr>
                                    @empty
                                        <tr><td colspan="3" class="py-8 text-center italic text-slate-400">Belum ada data laporan absensi</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <div x-cloak x-show="showImageModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/95 px-4 backdrop-blur-md">
            <div @click="showImageModal = false" class="absolute inset-0 cursor-pointer"></div>

            <div class="relative z-10 flex w-full max-w-5xl flex-col items-center">

                <button @click="showImageModal = false" class="mb-6 inline-flex min-h-[44px] items-center justify-center rounded-xl border border-red-400/50 bg-red-600 px-6 py-2.5 font-bold text-white shadow-lg transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300/30">
                    <svg class="mr-2 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Tutup Galeri
                </button>

                <div class="relative flex items-center justify-center w-full group">

                    <button x-show="images.length > 1 && currentIndex > 0" @click="prevImage()" class="absolute left-2 md:-left-12 z-20 bg-white/20 hover:bg-white/40 text-white p-3 sm:p-4 rounded-full backdrop-blur-md transition-all border border-white/30 focus:outline-none focus:ring-4 focus:ring-white/30 shadow-lg">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <img :src="images[currentIndex]" class="max-h-[75vh] max-w-full rounded-2xl object-contain shadow-[0_24px_80px_rgba(0,0,0,0.55)] border-2 border-white/10 transition-all duration-300">

                    <button x-show="images.length > 1 && currentIndex < images.length - 1" @click="nextImage()" class="absolute right-2 md:-right-12 z-20 bg-white/20 hover:bg-white/40 text-white p-3 sm:p-4 rounded-full backdrop-blur-md transition-all border border-white/30 focus:outline-none focus:ring-4 focus:ring-white/30 shadow-lg">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <div x-show="images.length > 1" class="mt-6 flex flex-col items-center gap-3">
                    <div class="bg-white/10 text-white px-5 py-1.5 rounded-full text-sm font-bold backdrop-blur-md border border-white/20 tracking-wider">
                        Foto <span x-text="currentIndex + 1"></span> dari <span x-text="images.length"></span>
                    </div>
                    <div class="flex gap-2">
                        <template x-for="(img, index) in images" :key="index">
                            <div class="h-2.5 rounded-full transition-all duration-300" :class="index === currentIndex ? 'w-8 bg-blue-500' : 'w-2.5 bg-white/40'"></div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL POP-UP NOTIFIKASI TAMBAHAN -->
        <div x-show="showAlert" style="display: none;" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-950/65 px-4 py-6 backdrop-blur-sm transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>

            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xl transition-all sm:p-8" x-transition:enter="ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                @if(session('success'))
                    <!-- Tampilan Jika Sukses -->
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 ring-1 ring-inset ring-emerald-100">
                        <svg class="h-10 w-10 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-2xl font-bold text-slate-900">Berhasil!</h3>
                    <p class="mb-6 text-base leading-relaxed text-slate-600">{{ session('success') }}</p>
                @elseif($errors->any())
                    <!-- Tampilan Jika Gagal/Error -->
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-red-50 ring-1 ring-inset ring-red-100">
                        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h3 class="mb-3 text-2xl font-bold text-slate-900">Mohon Maaf, Gagal!</h3>
                    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-left text-sm leading-relaxed text-red-700">
                        <ul class="list-inside list-disc space-y-1.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button @click="showAlert = false" class="min-h-[48px] w-full rounded-xl bg-blue-900 px-4 py-3 font-bold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    Tutup Peringatan
                </button>
            </div>
        </div>

    </div>
</x-app-layout>

<style>
    header:has(.bapas-admin-header) { background-color: #f1f5f9 !important; border-bottom: 1px solid #e2e8f0; box-shadow: none !important; }
    .bapas-admin-header { background-color: #f1f5f9; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #94a3b8 #e2e8f0; }
    .custom-scrollbar::-webkit-scrollbar { width: 10px; height: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border: 2px solid #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
</style>
