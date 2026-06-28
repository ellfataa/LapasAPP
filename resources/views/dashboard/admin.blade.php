<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 bg-slate-100">
            <h2 class="font-bold text-xl sm:text-2xl text-slate-900 leading-tight tracking-tight">
                Dashboard Utama Administrator, Selamat Datang {{ Auth::user()->nama }}!
            </h2>
        </div>
    </x-slot>

    <div x-data="{
            sidebarOpen: false,
            showImageModal: false,
            images: [],
            currentIndex: 0,
            openImageModal(imgArray) {
                this.images = imgArray;
                this.currentIndex = 0;
                this.showImageModal = true;
            },
            nextImage() {
                if (this.currentIndex < this.images.length - 1) this.currentIndex++;
            },
            prevImage() {
                if (this.currentIndex > 0) this.currentIndex--;
            }
         }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0">
            <div class="p-4 sm:p-6 lg:p-8 space-y-8 flex-1 overflow-y-auto">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5">
                        <div class="bg-indigo-100 text-indigo-700 p-4 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total PK/Pengawas</p>
                            <p class="text-3xl font-extrabold text-slate-900">{{ $totalPengawas }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5">
                        <div class="bg-emerald-100 text-emerald-700 p-4 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Klien/Narapidana</p>
                            <p class="text-3xl font-extrabold text-slate-900">{{ $totalNarapidana }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5">
                        <div class="bg-amber-100 text-amber-700 p-4 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Absensi/Laporan Wajib</p>
                            <p class="text-3xl font-extrabold text-slate-900">{{ $totalAbsensi }}</p>
                        </div>
                    </div>
                </div>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-4 border-b border-slate-200 bg-white px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                        <h3 class="flex items-center gap-3 text-lg font-bold text-slate-900">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-800"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></span>
                            Kalender Absensi/Laporan Wajib (Seluruh Klien/Narapidana)
                        </h3>
                        <form method="GET" action="{{ route('dashboard.admin') }}" class="flex">
                            <select name="year" onchange="this.form.submit()" class="rounded-xl border-slate-300 text-sm font-bold text-slate-800 focus:ring-0">
                                @foreach($availableYears as $year) <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>Tahun {{ $year }}</option> @endforeach
                            </select>
                        </form>
                    </div>

                    @php
                        $bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $absensiMap = [];
                        foreach($absensiKalender as $rekam) {
                            $tgl = \Carbon\Carbon::parse($rekam->tanggal_waktu);
                            $absensiMap[$tgl->month][$tgl->day][] = $rekam;
                        }
                    @endphp

                    <div class="p-4 sm:p-6">
                        <div class="custom-scrollbar overflow-x-auto pb-4 snap-x snap-mandatory">
                            <div class="flex min-w-max gap-4 px-1 pb-1">
                                @for($m = 1; $m <= 12; $m++)
                                    <article class="w-64 shrink-0 snap-start overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:w-72">
                                        <h4 class="border-b border-slate-200 bg-slate-50 px-4 py-3 text-center text-sm font-bold text-slate-800">{{ $bulans[$m-1] }}</h4>
                                        <div class="grid grid-cols-5 gap-1.5 p-3">
                                            @php $daysInMonth = \Carbon\Carbon::create($selectedYear, $m)->daysInMonth; @endphp
                                            @for($d = 1; $d <= $daysInMonth; $d++)
                                                @if(isset($absensiMap[$m][$d]))
                                                    @php
                                                        $count = count($absensiMap[$m][$d]);
                                                        $first = $absensiMap[$m][$d][0];

                                                        // Buat array JSON berisi semua URL foto untuk tanggal ini
                                                        $imageUrls = collect($absensiMap[$m][$d])->map(function($item) {
                                                            return asset('storage/' . $item->bukti_file);
                                                        })->toJson();
                                                    @endphp
                                                    <button type="button" @click.prevent="openImageModal({{ $imageUrls }})" class="group relative block aspect-square w-full overflow-hidden rounded-lg border border-emerald-400 bg-emerald-50 shadow-sm transition hover:scale-105 hover:ring-2 hover:ring-emerald-300">
                                                        <img src="{{ asset('storage/' . $first->bukti_file) }}" alt="Bukti" class="h-full w-full object-cover">
                                                        @if($count > 1)
                                                            <span class="absolute bottom-0 right-0 bg-blue-900/90 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-tl-md">+{{ $count - 1 }}</span>
                                                        @endif
                                                    </button>
                                                @else
                                                    <div class="flex aspect-square w-full items-center justify-center rounded-lg bg-slate-50 text-xs font-semibold text-slate-400">{{ $d }}</div>
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
                        <div class="bg-indigo-900 px-5 py-4 flex justify-between items-center text-white">
                            <h3 class="font-bold text-base">5 Akun PK/Pengawas Terbaru</h3>
                            <a href="{{ route('admin.pengawas.index') }}" class="text-xs font-bold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition-colors">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto p-4 flex-1">
                            <table class="w-full text-left text-sm text-slate-700">
                                <thead><tr class="text-slate-500 border-b border-slate-200"><th class="pb-2">Nama PK/Pengawas</th><th class="pb-2 text-right">Nomor Induk</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanPengawas as $pk)
                                        <tr><td class="py-3 font-bold text-slate-900">{{ $pk->nama }}</td><td class="py-3 text-right">{{ $pk->nomor_induk }}</td></tr>
                                    @empty
                                        <tr><td colspan="2" class="py-4 text-center italic text-slate-400">Belum ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-emerald-800 px-5 py-4 flex justify-between items-center text-white">
                            <h3 class="font-bold text-base">5 Klien/Narapidana Terbaru</h3>
                            <a href="{{ route('admin.narapidana.index') }}" class="text-xs font-bold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition-colors">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto p-4 flex-1">
                            <table class="w-full text-left text-sm text-slate-700">
                                <thead><tr class="text-slate-500 border-b border-slate-200"><th class="pb-2">Nama Klien/Narapidana</th><th class="pb-2 text-right">Nomor Induk/Registrasi</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanNarapidana as $napi)
                                        <tr><td class="py-3 font-bold text-slate-900">{{ $napi->nama }}</td><td class="py-3 text-right">{{ $napi->nomor_induk }}</td></tr>
                                    @empty
                                        <tr><td colspan="2" class="py-4 text-center italic text-slate-400">Belum ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-amber-600 px-5 py-4 flex justify-between items-center text-white">
                            <h3 class="font-bold text-base">5 Laporan Kinerja PK Terbaru</h3>
                            <a href="{{ route('admin.kinerja.index') }}" class="text-xs font-bold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition-colors">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto p-4 flex-1">
                            <table class="w-full text-left text-sm text-slate-700">
                                <thead><tr class="text-slate-500 border-b border-slate-200"><th class="pb-2">PK Lapor</th><th class="pb-2">Bulan</th><th class="pb-2 text-right">Predikat</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanKinerja as $kinerja)
                                        <tr><td class="py-3 font-bold text-slate-900">{{ $kinerja->pengawas->nama ?? '-' }}</td><td class="py-3">{{ $kinerja->bulan }}/{{ $kinerja->tahun }}</td><td class="py-3 text-right font-bold text-amber-700">{{ $kinerja->predikat }}</td></tr>
                                    @empty
                                        <tr><td colspan="3" class="py-4 text-center italic text-slate-400">Belum ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-teal-700 px-5 py-4 flex justify-between items-center text-white">
                            <h3 class="font-bold text-base">5 Absensi/Laporan Wajib Terbaru</h3>
                            <a href="{{ route('admin.absensi.index') }}" class="text-xs font-bold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition-colors">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto p-4 flex-1">
                            <table class="w-full text-left text-sm text-slate-700">
                                <thead><tr class="text-slate-500 border-b border-slate-200"><th class="pb-2">Tanggal</th><th class="pb-2">Nama Klien</th><th class="pb-2 text-right">PK/Pengawas</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanAbsensi as $absen)
                                        <tr><td class="py-3 font-bold">{{ \Carbon\Carbon::parse($absen->tanggal_waktu)->format('d/m/y') }}</td><td class="py-3 font-bold text-slate-900">{{ $absen->narapidana->nama ?? '-' }}</td><td class="py-3 text-right text-xs">{{ $absen->pengawas->nama ?? 'Belum Dipilih' }}</td></tr>
                                    @empty
                                        <tr><td colspan="3" class="py-4 text-center italic text-slate-400">Belum ada data</td></tr>
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

            <div class="relative z-10 flex w-full max-w-4xl flex-col items-center">
                <button @click="showImageModal = false" class="mb-5 bg-red-600 hover:bg-red-700 px-6 py-2.5 font-bold text-white rounded-xl shadow-lg transition-colors">Tutup Foto</button>

                <div class="relative flex items-center justify-center w-full group">

                    <button x-show="images.length > 1 && currentIndex > 0" @click="prevImage()" class="absolute left-0 z-20 bg-black/40 hover:bg-black/70 text-white p-3 sm:p-4 rounded-full backdrop-blur-md transition-all sm:-ml-12 focus:outline-none focus:ring-4 focus:ring-white/30">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <img :src="images[currentIndex]" class="max-h-[75vh] max-w-full rounded-2xl object-contain shadow-2xl transition-all duration-300">

                    <button x-show="images.length > 1 && currentIndex < images.length - 1" @click="nextImage()" class="absolute right-0 z-20 bg-black/40 hover:bg-black/70 text-white p-3 sm:p-4 rounded-full backdrop-blur-md transition-all sm:-mr-12 focus:outline-none focus:ring-4 focus:ring-white/30">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <div x-show="images.length > 1" class="mt-5 bg-black/60 text-white px-5 py-2 rounded-full text-sm font-bold backdrop-blur-md tracking-wider">
                    Foto <span x-text="currentIndex + 1"></span> dari <span x-text="images.length"></span>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

<style>
    header:has(.font-bold) { background-color: #f1f5f9 !important; border-bottom: 1px solid #e2e8f0; box-shadow: none !important; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #94a3b8 #e2e8f0; }
    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 9999px; }
</style>
