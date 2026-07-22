<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sistem Informasi Manajemen Bapas') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-[Figtree] antialiased bg-slate-50 text-slate-800 selection:bg-blue-500 selection:text-white flex flex-col min-h-screen">

    @php
        $namaBulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanBerjalan = $namaBulanIndo[date('n') - 1];
    @endphp

    <!-- NAVBAR -->
    <nav id="main-navbar" class="fixed top-0 inset-x-0 z-50 bg-white/70 backdrop-blur-md border-b border-white/40 shadow-sm transition-transform duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Logo & Title -->
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl overflow-hidden shadow-sm">
                        <img src="{{ asset('images/bapaspwt.webp') }}" alt="Logo BAPAS Purwokerto" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight leading-none">SIBAPAS PWT</h1>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mt-0.5">Balai Pemasyarakatan Purwokerto</p>
                    </div>
                </div>

                <!-- Waktu WIB -->
                <div class="flex items-center" aria-label="Waktu Indonesia Barat">
                    <span class="mr-2 text-sm font-semibold text-slate-800">Pukul</span>
                    <time id="wib-clock" class="font-mono text-lg font-bold tracking-wider text-slate-800" datetime="">
                        --:--:--
                    </time>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <div class="relative overflow-hidden bg-slate-900 bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('images/kemenimipas.jpg') }}');">

        <div class="absolute inset-0 bg-black/55"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative pt-32 pb-24 lg:pt-36 lg:pb-32 text-center">

            <!-- STATISTIK: baris pertama (umum, 3 kartu) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-3">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl ring-1 ring-white/20 p-4 flex flex-col items-center text-center">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-indigo-500/20 text-indigo-300 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-widest mb-0.5">Total PK BAPAS</p>
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $totalPengawas }}</p>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl ring-1 ring-white/20 p-4 flex flex-col items-center text-center">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-orange-500/20 text-orange-300 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-widest mb-0.5">Total PK Ternilai</p>
                    <p class="text-2xl sm:text-3xl font-black text-white flex items-end gap-2 justify-center">
                        {{ $totalKinerja }}
                        <span class="text-[10px] font-bold text-blue-200/70 mb-1.5 uppercase leading-none">({{ $bulanBerjalan }} {{ date('Y') }})</span>
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl ring-1 ring-white/20 p-4 flex flex-col items-center text-center">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-widest mb-0.5">Total Klien BAPAS</p>
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $totalNarapidana }}</p>
                </div>
            </div>

            <!-- STATISTIK: baris kedua (klien, 4 kartu) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-2">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl ring-1 ring-white/20 border-t-2 border-emerald-400 p-4 flex flex-col items-center text-center">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-widest mb-0.5">Sudah Bekerja</p>
                    <div class="flex items-baseline gap-1.5 justify-center">
                        <p class="text-2xl sm:text-3xl font-black text-white">{{ $klienBekerja }}</p>
                        <span class="text-[10px] font-bold bg-emerald-400/20 text-emerald-200 px-1.5 py-0.5 rounded ring-1 ring-emerald-300/30">{{ $persenBekerja }}</span>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl ring-1 ring-white/20 border-t-2 border-red-400 p-4 flex flex-col items-center text-center">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-red-500/20 text-red-300 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-widest mb-0.5">Belum Bekerja</p>
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $klienBelumBekerja }}</p>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl ring-1 ring-white/20 border-t-2 border-blue-400 p-4 flex flex-col items-center text-center">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-500/20 text-blue-300 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-widest mb-0.5">Sudah Apel (Per Tahun)</p>
                    <div class="flex items-baseline gap-1.5 justify-center">
                        <p class="text-2xl sm:text-3xl font-black text-white">{{ $klienSudahApel }}</p>
                        <span class="text-[10px] font-bold bg-blue-400/20 text-blue-200 px-1.5 py-0.5 rounded ring-1 ring-blue-300/30">{{ $persenApel }}</span>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl ring-1 ring-white/20 border-t-2 border-orange-400 p-4 flex flex-col items-center text-center">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-orange-500/20 text-orange-300 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-200 uppercase tracking-widest mb-0.5">Belum Apel (Per Tahun)</p>
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $klienBelumApel }}</p>
                </div>
            </div>

            <!-- JUDUL -->
            <h2 class="text-3xl md:text-5xl lg:text-6xl font-black text-white tracking-tight mb-6 mt-6 leading-tight drop-shadow-sm">
                SiBapas Purwokerto
            </h2>
            <p class="max-w-2xl mx-auto text-base md:text-lg text-blue-100 mb-10 leading-relaxed font-medium">
                Pusat informasi dan pengelolaan laporan kegiatan klien secara terpadu. Memudahkan Pengawas Kemasyarakatan (PK) dalam memantau klien.
            </p>
            @if(!Auth::check())
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-10 py-4 rounded-xl font-extrabold text-base bg-blue-500 text-white hover:bg-blue-600 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus:ring-4 focus:ring-blue-300/50">
                    Mulai Akses Sistem
                </a>
            @endif
        </div>

    </div>
    </div>

    <main class="flex-1"></main>

    <!-- FOOTER -->
    @include('layouts.footer')

    <script>
        (function () {
            const clock = document.getElementById('wib-clock');
            const navbar = document.getElementById('main-navbar');
            let lastScrollY = window.scrollY;
            const hideThreshold = 80;

            function updateWibClock() {
                const now = new Date();
                clock.textContent = new Intl.DateTimeFormat('en-GB', {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hourCycle: 'h23'
                }).format(now);
                clock.dateTime = now.toISOString();
            }

            updateWibClock();
            window.setInterval(updateWibClock, 1000);

            window.addEventListener('scroll', function () {
                const currentScrollY = window.scrollY;

                if (currentScrollY <= hideThreshold) {
                    // Selalu tampil saat masih dekat atas halaman
                    navbar.classList.remove('-translate-y-full');
                } else if (currentScrollY > lastScrollY) {
                    // Scroll ke bawah -> sembunyikan navbar
                    navbar.classList.add('-translate-y-full');
                } else {
                    // Scroll ke atas -> tampilkan navbar
                    navbar.classList.remove('-translate-y-full');
                }

                lastScrollY = currentScrollY;
            }, { passive: true });
        })();
    </script>

</body>
</html>
