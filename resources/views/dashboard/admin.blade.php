<x-app-layout>

    <div x-data="{
            sidebarOpen: false,
            showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }}
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
                    <h2 class="text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                        Dashboard Administrator, Selamat Datang <span class="font-bold">{{ Auth::user()->nama }}</span>!
                    </h2>
                </div>

                @php
                    $bulansFilter = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $bulanBerjalan = $bulansFilter[date('n') - 1];
                @endphp

                <!-- STATISTIK ATAS -->
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-5 lg:gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total PK BAPAS</p>
                            <p class="text-3xl font-extrabold text-slate-900">{{ $totalPengawas }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-orange-100 text-orange-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total PK Ternilai</p>
                            <p class="text-3xl font-extrabold text-slate-900 flex items-end gap-2">{{ $totalKinerja }} <span class="text-[10px] font-bold text-slate-400 mb-1.5 uppercase leading-none">({{ $bulanBerjalan }} {{ date('Y') }})</span></p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Klien BAPAS</p>
                            <p class="text-3xl font-extrabold text-slate-900">{{ $totalNarapidana }}</p>
                        </div>
                    </div>

                </div>

                <!-- STATISTIK KLIEN -->
                <div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6">
                        <!-- Card: Sudah Bekerja -->
                        <div class="bg-white rounded-2xl shadow-sm border border-emerald-200 p-5 flex items-center gap-4 transition hover:shadow-md hover:border-emerald-300">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-0.5">Total Klien Sudah Bekerja</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-2xl font-extrabold text-slate-800">{{ $klienBekerja }} <span class="text-xs font-semibold text-slate-400">Klien</span></p>
                                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded shadow-sm border border-emerald-200" title="Persentase Klien Bekerja">{{ $persenBekerja }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Belum Bekerja -->
                        <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-5 flex items-center gap-4 transition hover:shadow-md hover:border-red-300">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600 ring-1 ring-red-200">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest mb-0.5">Total Klien Belum Bekerja</p>
                                <p class="text-2xl font-extrabold text-slate-800">{{ $klienBelumBekerja }} <span class="text-xs font-semibold text-slate-400">Klien</span></p>
                            </div>
                        </div>

                        <!-- Card: Sudah Apel Tahunan -->
                        <div class="bg-white rounded-2xl shadow-sm border border-blue-200 p-5 flex items-center gap-4 transition hover:shadow-md hover:border-blue-300">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 ring-1 ring-blue-200">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-0.5">Klien Sudah Apel (Per Tahun)</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-2xl font-extrabold text-slate-800">{{ $klienSudahApel }} <span class="text-xs font-semibold text-slate-400">Klien</span></p>
                                    <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded shadow-sm border border-blue-200" title="Persentase Disiplin Apel">{{ $persenApel }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Belum Apel Tahunan -->
                        <div class="bg-white rounded-2xl shadow-sm border border-orange-200 p-5 flex items-center gap-4 transition hover:shadow-md hover:border-orange-300">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-600 ring-1 ring-orange-200">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-orange-600 uppercase tracking-widest mb-0.5">Klien Belum Apel (Per Tahun)</p>
                                <p class="text-2xl font-extrabold text-slate-800">{{ $klienBelumApel }} <span class="text-xs font-semibold text-slate-400">Klien</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:gap-8 mt-2">

                    <!-- TABEL KINERJA PK TERBARU -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-5 py-4 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 text-white border-b border-amber-500">
                            <h3 class="font-bold text-base flex items-center gap-2">
                                <svg class="w-5 h-5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                5 Penilaian Kinerja PK Terbaru
                            </h3>

                            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                                <form method="GET" action="{{ route('dashboard.admin') }}" class="flex w-full sm:w-auto gap-2">
                                    <input type="hidden" name="absensi_month" value="{{ $absensiMonth }}">
                                    <input type="hidden" name="absensi_year" value="{{ $absensiYear }}">

                                    <select name="kinerja_month" onchange="this.form.submit()" class="block w-full sm:w-auto cursor-pointer appearance-none rounded-lg border-white bg-white/10 py-1.5 pl-3 pr-8 text-xs font-bold text-white shadow-sm focus:border-white focus:ring-white backdrop-blur-sm dashboard-filter-select [&>option]:text-slate-800">
                                        @foreach($bulansFilter as $index => $namaBulan)
                                            <option value="{{ $index + 1 }}" {{ (int)$kinerjaMonth == ($index + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option>
                                        @endforeach
                                    </select>

                                    <select name="kinerja_year" onchange="this.form.submit()" class="block w-full sm:w-auto cursor-pointer appearance-none rounded-lg border-white bg-white/10 py-1.5 pl-3 pr-8 text-xs font-bold text-white shadow-sm focus:border-white focus:ring-white backdrop-blur-sm dashboard-filter-select [&>option]:text-slate-800">
                                        @foreach($availableYears as $year)
                                            <option value="{{ $year }}" {{ $kinerjaYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                <a href="{{ route('admin.kinerja.index') }}" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors ring-1 ring-inset ring-white/20 whitespace-nowrap">Lihat Semua</a>
                            </div>
                        </div>
                        <div class="overflow-x-auto custom-scrollbar p-0 flex-1">
                            <table class="w-full text-left text-sm text-slate-700 min-w-[700px]">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs uppercase tracking-wider">
                                        <th class="px-5 py-3 font-bold w-32">Tgl Simpan</th>
                                        <th class="px-5 py-3 font-bold">Nama PK/Pengawas</th>
                                        <th class="px-5 py-3 font-bold w-40">Periode Laporan</th>
                                        <th class="px-5 py-3 text-center font-bold w-32">Skor Akhir</th>
                                        <th class="px-5 py-3 text-right font-bold w-32">Predikat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanKinerja as $kinerja)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-5 py-3.5 text-xs text-slate-500 font-medium">{{ $kinerja->created_at->format('d/m/Y') }}</td>
                                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $kinerja->pengawas->nama ?? '-' }}</td>
                                            <td class="px-5 py-3.5 font-medium">{{ $bulansFilter[$kinerja->bulan - 1] }} {{ $kinerja->tahun }}</td>
                                            <td class="px-5 py-3.5 text-center font-extrabold text-blue-700">{{ $kinerja->rata_rata }}%</td>
                                            <td class="px-5 py-3.5 text-right font-extrabold {{ $kinerja->predikat == 'Sangat Baik' ? 'text-emerald-600' : ($kinerja->predikat == 'Baik' ? 'text-blue-600' : ($kinerja->predikat == 'Cukup' ? 'text-yellow-600' : ($kinerja->predikat == 'Kurang' ? 'text-orange-600' : 'text-red-600'))) }}">{{ $kinerja->predikat }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-10 text-center italic text-slate-400 font-medium">Belum ada data laporan kinerja pada bulan <b>{{ $bulansFilter[(int)$kinerjaMonth - 1] }} {{ $kinerjaYear }}</b>.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABEL ABSENSI TERBARU -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-gradient-to-r from-teal-700 to-emerald-700 px-5 py-4 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 text-white border-b border-teal-600">
                            <h3 class="font-bold text-base flex items-center gap-2">
                                <svg class="w-5 h-5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                5 Laporan Absensi Klien Terbaru
                            </h3>

                            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                                <form method="GET" action="{{ route('dashboard.admin') }}" class="flex w-full sm:w-auto gap-2">
                                    <input type="hidden" name="kinerja_month" value="{{ $kinerjaMonth }}">
                                    <input type="hidden" name="kinerja_year" value="{{ $kinerjaYear }}">

                                    <select name="absensi_month" onchange="this.form.submit()" class="block w-full sm:w-auto cursor-pointer appearance-none rounded-lg border-white bg-white/10 py-1.5 pl-3 pr-8 text-xs font-bold text-white shadow-sm focus:border-white focus:ring-white backdrop-blur-sm dashboard-filter-select [&>option]:text-slate-800">
                                        @foreach($bulansFilter as $index => $namaBulan)
                                            <option value="{{ sprintf('%02d', $index + 1) }}" {{ $absensiMonth == sprintf('%02d', $index + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option>
                                        @endforeach
                                    </select>

                                    <select name="absensi_year" onchange="this.form.submit()" class="block w-full sm:w-auto cursor-pointer appearance-none rounded-lg border-white bg-white/10 py-1.5 pl-3 pr-8 text-xs font-bold text-white shadow-sm focus:border-white focus:ring-white backdrop-blur-sm dashboard-filter-select [&>option]:text-slate-800">
                                        @foreach($availableYears as $year)
                                            <option value="{{ $year }}" {{ $absensiYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                <a href="{{ route('admin.absensi.index') }}" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-colors ring-1 ring-inset ring-white/20 whitespace-nowrap">Lihat Semua</a>
                            </div>
                        </div>
                        <div class="overflow-x-auto custom-scrollbar p-0 flex-1">
                            <table class="w-full text-left text-sm text-slate-700 min-w-[700px]">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-xs uppercase tracking-wider">
                                        <th class="px-5 py-3 font-bold w-32">Tgl Kegiatan</th>
                                        <th class="px-5 py-3 font-bold w-48">Nama Klien</th>
                                        <th class="px-5 py-3 font-bold">Nama Kegiatan Sosial</th>
                                        <th class="px-5 py-3 text-right font-bold w-48">Dibimbing Oleh PK</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($ringkasanAbsensi as $absen)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-5 py-3.5 font-bold text-slate-800">{{ \Carbon\Carbon::parse($absen->tanggal_waktu)->format('d/m/Y') }}</td>
                                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $absen->narapidana->nama ?? '-' }}</td>
                                            <td class="px-5 py-3.5 text-slate-600">{{ $absen->jenis_kegiatan }}</td>
                                            <td class="px-5 py-3.5 text-right font-medium text-xs text-indigo-700">{{ $absen->pengawas->nama ?? 'Belum Dipilih' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="py-10 text-center italic text-slate-400 font-medium">Belum ada data laporan absensi pada bulan <b>{{ $bulansFilter[(int)$absensiMonth - 1] }} {{ $absensiYear }}</b>.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <!-- MODAL POP-UP NOTIFIKASI -->
        <div x-show="showAlert" style="display: none;" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-950/65 px-4 py-6 backdrop-blur-sm transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>

            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xl transition-all sm:p-8" x-transition:enter="ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

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
                    <h3 class="mb-3 text-2xl font-bold text-slate-900">Terdapat Kendala</h3>
                    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-left text-sm leading-relaxed text-red-700">
                        <ul class="list-inside list-disc space-y-1.5">
                            @foreach(array_unique($errors->all()) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
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
    header:has(.bapas-admin-header) { background-color: #f1f5f9 !important; border-bottom: 1px solid #e2e8f0; box-shadow: none !important; }
    .bapas-admin-header { background-color: #f1f5f9; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #94a3b8 #e2e8f0; }
    .custom-scrollbar::-webkit-scrollbar { width: 10px; height: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 9999px; border: 2px solid #e2e8f0; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
    .dashboard-filter-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23ffffff' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m19 9-7 7-7-7'/%3E%3C/svg%3E");
        background-position: right 0.7rem center;
        background-repeat: no-repeat;
        background-size: 1rem;
        padding-right: 2.25rem;
    }
</style>
