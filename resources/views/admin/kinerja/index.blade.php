<x-app-layout>
    <div x-data="{ sidebarOpen: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0 p-4 sm:p-6 lg:p-8 overflow-y-auto">

            <div class="bapas-admin-header flex items-center gap-4 bg-slate-100 mb-6 sm:mb-8">
                <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-600 text-white shadow-sm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">Manajemen Penilaian Kinerja PK</h2>
                    <p class="text-sm text-slate-500 mt-1">Pantau dan evaluasi hasil capaian kinerja bulanan secara terpusat.</p>
                </div>
            </div>

            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-200 mb-8">
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter Data Kinerja
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">Gunakan kotak di bawah untuk mencari berdasarkan nama PK.</p>
                </div>
                <form method="GET" action="{{ route('admin.kinerja.index') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <div class="relative flex-1 sm:min-w-[300px]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama PK/Pengawas..." class="block min-h-[48px] w-full rounded-xl border-slate-300 pl-11 pr-4 py-3 text-sm shadow-sm transition hover:border-slate-400 focus:border-amber-500 focus:ring-amber-500 text-slate-900 font-medium">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="min-h-[48px] bg-amber-600 hover:bg-amber-700 text-white font-bold px-6 rounded-xl text-sm transition-colors shadow-sm focus:ring-4 focus:ring-amber-200">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('admin.kinerja.index') }}" class="inline-flex items-center justify-center min-h-[48px] bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 rounded-xl text-sm border border-slate-300 transition-colors focus:ring-4 focus:ring-slate-200">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Data Utama -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col mb-8">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="font-bold text-lg text-slate-800">Daftar Seluruh Laporan Kinerja PK</h3>
                    <span class="inline-flex min-h-[44px] items-center justify-center rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-center text-sm font-semibold text-amber-800">
                        Geser tabel ke kiri/kanan &rarr;
                    </span>
                </div>

                <div class="overflow-x-auto custom-scrollbar flex-1">
                    <table class="w-full text-left text-slate-700 min-w-[1100px] table-fixed">
                        <thead class="bg-slate-100 border-b border-slate-200 text-xs font-bold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="w-16 px-6 py-4 text-center">No</th>
                                <th class="w-60 px-6 py-4 border-r border-slate-200">Informasi PK & Periode</th>
                                <th class="w-[400px] px-6 py-4 border-r border-slate-200">Rincian Capaian 3 Kategori</th>
                                <th class="w-44 px-6 py-4 text-center border-r border-slate-200">Skor Akhir & Predikat</th>
                                <th class="w-32 px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($semuaKinerja as $kinerja)
                                @php $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                                <tr class="hover:bg-amber-50/30 transition-colors align-top">

                                    <!-- Kolom No -->
                                    <td class="px-6 py-5 text-center font-semibold text-slate-500">
                                        {{ $semuaKinerja->firstItem() + $loop->index }}
                                    </td>

                                    <!-- Kolom Info PK -->
                                    <td class="px-6 py-5 border-r border-slate-200">
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-bold text-slate-900 text-base leading-tight">{{ $kinerja->pengawas->nama ?? 'Tidak Ditemukan' }}</span>
                                            <span class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">NIP: {{ $kinerja->pengawas->nomor_induk ?? '-' }}</span>
                                            <span class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-amber-800 bg-amber-100 px-2.5 py-1.5 rounded-lg w-fit border border-amber-200">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ $namaBulan[$kinerja->bulan - 1] ?? $kinerja->bulan }} {{ $kinerja->tahun }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Kolom Rincian 3 Kategori (Progress Bar Mini) -->
                                    <td class="px-4 py-5 border-r border-slate-200">
                                        <div class="grid grid-cols-3 gap-2 sm:gap-3">
                                            @foreach(['litmas', 'pembimbingan', 'pengawasan'] as $kat)
                                                @php
                                                    $persenKat = $kinerja->{$kat.'_kuota'} > 0 ? ($kinerja->{$kat.'_berhasil'} / $kinerja->{$kat.'_kuota'}) * 100 : 0;
                                                    $lebarProgress = min(max($persenKat, 0), 100);
                                                @endphp
                                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 shadow-sm min-w-0 flex flex-col justify-center">
                                                    <div class="font-extrabold text-[9px] xl:text-[10px] uppercase tracking-wider text-slate-500 mb-1.5">{{ $kat }}</div>

                                                    <div class="flex items-center justify-between gap-1 mb-2 text-xs">
                                                        <span class="font-bold text-slate-700 whitespace-nowrap">
                                                            {{ $kinerja->{$kat.'_berhasil'} }}<span class="text-slate-400 font-medium">/{{ $kinerja->{$kat.'_kuota'} }}</span>
                                                        </span>
                                                        <span class="font-bold text-blue-700 whitespace-nowrap">{{ number_format($persenKat, 1) }}%</span>
                                                    </div>
                                                    <div class="h-1.5 bg-slate-200 rounded-full overflow-hidden w-full">
                                                        <div class="h-full bg-blue-600 rounded-full transition-all" style="width: {{ $lebarProgress }}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Kolom Predikat Akhir -->
                                    <td class="px-6 py-5 border-r border-slate-200 text-center flex flex-col items-center justify-center h-full min-h-[90px]">
                                        <span class="block text-3xl font-extrabold text-slate-800 tracking-tight leading-none">{{ $kinerja->rata_rata }}<span class="text-lg text-slate-500">%</span></span>
                                        <span class="inline-flex mt-2 items-center justify-center min-w-[100px] rounded-lg border px-3 py-1.5 text-[11px] font-extrabold shadow-sm {{ $kinerja->predikat == 'Sangat Baik' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($kinerja->predikat == 'Baik' ? 'border-blue-200 bg-blue-50 text-blue-700' : ($kinerja->predikat == 'Cukup' ? 'border-yellow-200 bg-yellow-50 text-yellow-700' : ($kinerja->predikat == 'Kurang' ? 'border-orange-200 bg-orange-50 text-orange-700' : 'border-red-200 bg-red-50 text-red-700'))) }}">
                                            {{ $kinerja->predikat }}
                                        </span>
                                    </td>

                                    <!-- Kolom Aksi -->
                                    <td class="px-6 py-5 text-center align-middle">
                                        <form action="{{ route('admin.kinerja.destroy', $kinerja->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penilaian kinerja ini? Seluruh file lampiran pendukung (jika ada) juga akan terhapus secara permanen.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 px-3 py-2 rounded-xl text-sm font-bold transition-all shadow-sm focus:ring-4 focus:ring-red-100">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-16">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-slate-50 p-4 rounded-full mb-3 ring-1 ring-slate-100">
                                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <span class="text-base font-bold text-slate-600">Data Tidak Ditemukan</span>
                                            <span class="text-sm text-slate-500 mt-1 max-w-md">
                                                @if(request('search'))
                                                    Tidak ada data kinerja PK yang cocok dengan kata pencarian "{{ request('search') }}".
                                                @else
                                                    Belum ada satupun data penilaian kinerja bulanan PK yang tersimpan di dalam sistem.
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($semuaKinerja->hasPages())
                    <div class="border-t border-slate-200 bg-slate-50 px-6 py-4">
                        {{ $semuaKinerja->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </main>

        @if(session('success') || $errors->any())
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 sm:p-8 text-center shadow-2xl transition-all" x-transition:enter="ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                @if(session('success'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 ring-4 ring-emerald-50">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="font-bold text-xl mb-2 text-slate-900">Operasi Berhasil!</h3>
                    <p class="text-sm text-slate-600 mb-6 leading-relaxed">{{ session('success') }}</p>
                @elseif($errors->any())
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600 ring-4 ring-red-50">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 mb-3">Terjadi Kendala</h3>
                    <ul class="text-sm text-red-700 text-left bg-red-50 p-4 rounded-xl mb-6 list-disc list-inside font-medium border border-red-100 space-y-1">
                        @foreach(array_unique($errors->all()) as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                @endif
                <button @click="showAlert = false" class="w-full bg-slate-800 hover:bg-slate-900 focus:ring-4 focus:ring-slate-200 text-white font-bold py-3.5 text-base rounded-xl transition-colors shadow-sm">Tutup Notifikasi</button>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>

<style>
    header:has(.bapas-admin-header) { background-color: #f1f5f9 !important; border-bottom: 1px solid #e2e8f0; box-shadow: none !important; }
    .bapas-admin-header { background-color: #f1f5f9; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; border: 2px solid #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
