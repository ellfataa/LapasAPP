<x-app-layout>
    <div x-data="{ sidebarOpen: false, showAlert: {{ session('success') ? 'true' : 'false' }} }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0">
            <div class="p-4 sm:p-6 lg:p-8 space-y-6 flex-1 overflow-y-auto">
                <div class="bapas-admin-header flex items-center gap-4 bg-slate-100">
                    <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h10m-5-14v4m0 0h-2m2 0h2"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">Manajemen Penilaian Kinerja PK</h2>
                        <p class="text-sm text-slate-500 mt-1">Pantau penilaian bulanan PK/Pengawas secara terpusat dan konsisten.</p>
                    </div>
                </div>

                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-lg sm:text-xl text-slate-800">Daftar Kinerja PK/Pengawas</h3>
                        <p class="text-sm text-slate-500 mt-1">Cari berdasarkan nama PK atau periode penilaian untuk melihat hasilnya dengan cepat.</p>
                    </div>
                    <form method="GET" action="{{ route('admin.kinerja.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <div class="relative flex-1 sm:min-w-[260px]">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama PK atau periode..." class="block min-h-[48px] w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm transition pl-4 pr-4">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="min-h-[48px] bg-slate-800 hover:bg-slate-900 text-white font-bold px-6 rounded-xl text-sm transition-colors shadow-sm">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('admin.kinerja.index') }}" class="inline-flex items-center justify-center min-h-[48px] bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 rounded-xl text-sm border border-slate-300 transition-colors">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    <div class="overflow-x-auto custom-scrollbar flex-1">
                        <table class="w-full text-left text-slate-700 min-w-[760px]">
                            <thead class="bg-slate-50 border-b border-slate-200 text-sm font-bold uppercase tracking-wide">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center">No</th>
                                    <th class="px-6 py-4">Nama PK</th>
                                    <th class="px-6 py-4">Periode</th>
                                    <th class="px-6 py-4">Predikat</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($semuaKinerja as $kinerja)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-center font-semibold text-slate-500">{{ $semuaKinerja->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $kinerja->pengawas->nama ?? 'Tidak Ditemukan' }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-600">{{ $kinerja->bulan }}/{{ $kinerja->tahun }}</td>
                                    <td class="px-6 py-4 font-bold text-indigo-700">{{ $kinerja->predikat }} ({{ $kinerja->rata_rata }}%)</td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.kinerja.destroy', $kinerja->id) }}" method="POST" class="inline-flex" onsubmit="return confirm('Hapus data penilaian ini beserta lampiran pendukung?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-2 rounded-lg text-sm font-bold transition-colors">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 text-slate-500 font-medium">
                                        @if(request('search'))
                                            Tidak ditemukan data kinerja dengan kata kunci "{{ request('search') }}".
                                        @else
                                            Belum ada data penilaian kinerja PK yang tersimpan.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($semuaKinerja->hasPages())
                        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4">{{ $semuaKinerja->withQueryString()->links() }}</div>
                    @endif
                </div>

            </div>
        </main>

        @if(session('success'))
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="font-bold text-xl mb-2 text-slate-900">Berhasil</h3>
                <p class="text-sm text-slate-600 mb-5 leading-relaxed">{{ session('success') }}</p>
                <button @click="showAlert = false" class="w-full bg-indigo-900 text-white font-bold py-3 rounded-xl transition-colors hover:bg-indigo-950">Tutup</button>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
