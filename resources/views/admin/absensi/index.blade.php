<x-app-layout>
    <div x-data="{ sidebarOpen: false, showImageModal: false, imgSrc: '', showAlert: {{ session('success') ? 'true' : 'false' }} }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0">
            <div class="p-4 sm:p-6 lg:p-8 space-y-6 flex-1 overflow-y-auto">
                <div class="bapas-admin-header flex items-center gap-4 bg-slate-100">
                    <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-700 text-white shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">Manajemen Laporan Absensi/Wajib Klien</h2>
                        <p class="text-sm text-slate-500 mt-1">Pantau bukti absensi harian klien secara terpusat dan mudah dicari.</p>
                    </div>
                </div>

                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-lg sm:text-xl text-slate-800">Daftar Laporan Absensi Klien</h3>
                        <p class="text-sm text-slate-500 mt-1">Cari berdasarkan nama klien atau jenis kegiatan untuk melihat bukti absensi dengan cepat.</p>
                    </div>
                    <form method="GET" action="{{ route('admin.absensi.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <div class="relative flex-1 sm:min-w-[280px]">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama klien atau kegiatan..." class="block min-h-[48px] w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 text-sm shadow-sm transition pl-4 pr-4">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="min-h-[48px] bg-slate-800 hover:bg-slate-900 text-white font-bold px-6 rounded-xl text-sm transition-colors shadow-sm">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('admin.absensi.index') }}" class="inline-flex items-center justify-center min-h-[48px] bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 rounded-xl text-sm border border-slate-300 transition-colors">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    <div class="overflow-x-auto custom-scrollbar flex-1">
                        <table class="w-full text-left text-slate-700 min-w-[820px]">
                            <thead class="bg-slate-50 border-b border-slate-200 text-sm font-bold uppercase tracking-wide">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center">No</th>
                                    <th class="px-6 py-4">Nama Klien</th>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Kegiatan</th>
                                    <th class="px-6 py-4 text-center">Bukti</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($semuaAbsensi as $absensi)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-center font-semibold text-slate-500">{{ $semuaAbsensi->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $absensi->narapidana->nama ?? '-' }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-600">{{ \Carbon\Carbon::parse($absensi->tanggal_waktu)->format('d M Y') }}</td>
                                    <td class="px-6 py-4">{{ $absensi->jenis_kegiatan }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <button @click="showImageModal = true; imgSrc = '{{ asset('storage/' . $absensi->bukti_file) }}'" class="text-teal-700 font-bold hover:underline transition-colors">Lihat Foto</button>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.absensi.destroy', $absensi->id) }}" method="POST" class="inline-flex" onsubmit="return confirm('Hapus laporan absensi ini beserta bukti foto?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-2 rounded-lg text-sm font-bold transition-colors">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-slate-500 font-medium">
                                        @if(request('search'))
                                            Tidak ditemukan laporan absensi dengan kata kunci "{{ request('search') }}".
                                        @else
                                            Belum ada laporan absensi klien yang tersimpan.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($semuaAbsensi->hasPages())
                        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4">{{ $semuaAbsensi->withQueryString()->links() }}</div>
                    @endif
                </div>

            </div>
        </main>

        <div x-cloak x-show="showImageModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/90 p-4 backdrop-blur-md">
            <div @click="showImageModal = false" class="absolute inset-0 cursor-pointer"></div>
            <img :src="imgSrc" class="relative max-h-[80vh] max-w-full rounded-2xl shadow-2xl">
            <button @click="showImageModal = false" class="absolute top-5 right-5 rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">Tutup</button>
        </div>

        @if(session('success'))
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="font-bold text-xl mb-2 text-slate-900">Berhasil</h3>
                <p class="text-sm text-slate-600 mb-5 leading-relaxed">{{ session('success') }}</p>
                <button @click="showAlert = false" class="w-full bg-teal-700 text-white font-bold py-3 rounded-xl transition-colors hover:bg-teal-800">Tutup</button>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>

<style>
    header:has(.bapas-admin-header) { background-color: #f1f5f9 !important; border-bottom: 1px solid #e2e8f0; box-shadow: none !important; }
    .bapas-admin-header { background-color: #f1f5f9; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #94a3b8 #e2e8f0; }
    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 9999px; }
</style>
