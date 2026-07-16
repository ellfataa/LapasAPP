<x-app-layout>

    <div x-data="{ sidebarOpen: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0 p-4 sm:p-6 lg:p-8 overflow-y-auto">

            <div class="bapas-admin-header flex items-center gap-4 bg-slate-100 mb-6 sm:mb-8">
                <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z"></path></svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                        Rekap Data & Distribusi Klien
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Pantau jumlah klien masing-masing PK dan hubungkan klien ke PK/Pengawas.</p>
                </div>
            </div>

            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-200 mb-6">
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Pencarian Tabel Rekap PK
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">Cari berdasarkan nama PK/Pengawas.</p>
                </div>
                <form method="GET" action="{{ route('admin.rekap.index') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <div class="relative flex-1 sm:min-w-[320px]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search_pk" value="{{ request('search_pk') }}" placeholder="Ketik nama PK/Pengawas..." class="block min-h-[48px] w-full rounded-xl border-slate-300 pl-11 pr-4 py-3 text-sm shadow-sm transition hover:border-slate-400 focus:border-blue-600 focus:ring-blue-600 text-slate-900 font-medium">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="min-h-[48px] bg-blue-800 hover:bg-blue-900 text-white font-bold px-6 rounded-xl text-sm transition-colors shadow-sm focus:ring-4 focus:ring-blue-200">Cari</button>
                        @if(request('search_pk'))
                            <a href="{{ route('admin.rekap.index') }}" class="inline-flex items-center justify-center min-h-[48px] bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 rounded-xl text-sm border border-slate-300 transition-colors focus:ring-4 focus:ring-slate-200" title="Reset Pencarian">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- BAGIAN 1: TABEL REKAPAN PK -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col mb-8">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-800">Tabel Rekap Jumlah Klien per PK</h3>
                </div>

                <div class="overflow-x-auto custom-scrollbar flex-1">
                    <table class="w-full text-left text-slate-700 min-w-[750px]">
                        <thead class="bg-slate-100 border-b border-slate-200 text-xs font-bold uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center text-slate-500">No</th>
                                <th class="px-6 py-4 text-slate-700">Nama PK/Pengawas</th>
                                <th class="px-6 py-4 text-slate-700">Nomor Induk (NRP/NIP)</th>
                                <th class="px-6 py-4 text-center text-slate-700">Jumlah Klien Diawasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($daftarPk as $pk)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-center font-semibold text-slate-500">{{ $daftarPk->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $pk->nama }}</td>
                                <td class="px-6 py-4 font-medium text-slate-600">{{ $pk->nomor_induk }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] h-[30px] rounded-lg {{ $pk->klien_bimbingan_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500' }} font-bold text-sm">
                                        {{ $pk->klien_bimbingan_count }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-10 text-slate-500 font-medium">
                                    Belum ada data PK terdaftar atau cocok dengan pencarian.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($daftarPk->hasPages())
                    <div class="border-t border-slate-200 bg-slate-50 px-6 py-4">
                        {{ $daftarPk->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            <!-- PREPARASI DATA JSON UNTUK JAVASCRIPT -->
            @php
                $klienJson = $semuaKlien->map(function($k) {
                    return [
                        'id' => (string)$k->id,
                        'nama' => $k->nama,
                        'has_pk' => !empty($k->pembimbing_id),
                        'pk_nama' => $k->pembimbing_id ? ($k->pembimbing->nama ?? 'PK Lain') : ''
                    ];
                })->values()->toJson();
            @endphp

            <!-- BAGIAN 2: FORM HUBUNGKAN PK DENGAN KLIEN -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="border-b border-slate-200 bg-indigo-900 px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <span class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white ring-1 ring-inset ring-white/20">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"></path></svg>
                    </span>
                    <div>
                        <h3 class="font-bold text-lg sm:text-xl text-white">Hubungkan Klien dengan PK/Pengawas</h3>
                        <p class="text-indigo-200 text-sm mt-1">Pilih PK pembimbing di kotak 1, lalu centang klien di kotak 2.</p>
                    </div>
                </div>

                <form action="{{ route('admin.rekap.hubungkan') }}" method="POST" class="p-5 sm:p-8">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <!-- KOTAK 1: Pilihan PK -->
                        <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 h-fit">
                            <label class="text-base font-extrabold text-slate-800 mb-4 flex items-center gap-3">
                                <span class="bg-indigo-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs shadow-sm">1</span>
                                Tentukan PK/Pengawas
                            </label>

                            <p class="text-sm text-slate-600 mb-4">Pilih PK yang akan menjadi pengawas untuk klien-klien yang akan Anda centang nanti.</p>

                            <select name="pk_id" required class="block w-full rounded-xl border-slate-300 bg-white py-3.5 px-4 text-base font-bold text-indigo-900 shadow-sm transition hover:border-indigo-400 focus:border-indigo-600 focus:ring-indigo-600 cursor-pointer">
                                <option value="" disabled selected>-- Klik di sini untuk memilih PK --</option>
                                @foreach($semuaPk as $pembimbing)
                                    <option value="{{ $pembimbing->id }}">{{ $pembimbing->nama }} (NIP: {{ $pembimbing->nomor_induk }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- KOTAK 2: Pilihan Klien -->
                        <div x-data="{
                                search: '',
                                selected: [],
                                clients: {{ $klienJson }},
                                currentPage: 1,
                                itemsPerPage: 20,

                                get filteredClients() {
                                    if (this.search.trim() === '') return this.clients;
                                    const q = this.search.toLowerCase();
                                    return this.clients.filter(c => c.nama.toLowerCase().includes(q));
                                },
                                get totalPages() {
                                    return Math.max(1, Math.ceil(this.filteredClients.length / this.itemsPerPage));
                                },
                                get paginatedClients() {
                                    const start = (this.currentPage - 1) * this.itemsPerPage;
                                    return this.filteredClients.slice(start, start + this.itemsPerPage);
                                }
                            }"
                            x-init="$watch('search', () => currentPage = 1)"
                            class="bg-emerald-50 p-5 sm:p-6 rounded-2xl border border-emerald-200 flex flex-col h-[700px]">

                            <!-- Input Hidden (Menjaga data centang tetap terkirim meski pindah halaman) -->
                            <template x-for="id in selected" :key="'hidden-'+id">
                                <input type="hidden" name="klien_ids[]" :value="id">
                            </template>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                                <label class="text-base font-extrabold text-emerald-900 flex items-center gap-3">
                                    <span class="bg-emerald-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs shadow-sm">2</span>
                                    Centang Klien
                                </label>
                                <div class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm font-bold shadow-sm whitespace-nowrap">
                                    <span x-text="selected.length"></span> Terpilih
                                </div>
                            </div>

                            <!-- Input Pencarian -->
                            <div class="relative mb-4 shrink-0">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" x-model="search" placeholder="Cari nama klien di sini..." class="block w-full pl-11 pr-4 py-3 border-emerald-300 rounded-xl leading-5 bg-white placeholder-emerald-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition shadow-sm font-medium text-emerald-900">

                                <button type="button" x-show="search.length > 0" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-emerald-400 hover:text-emerald-600 focus:outline-none" style="display: none;">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <!-- Daftar Klien -->
                            <div class="flex-1 overflow-y-auto custom-scrollbar bg-white rounded-xl border border-emerald-200 shadow-inner p-2">

                                <template x-for="(klien, index) in paginatedClients" :key="klien.id">
                                    <label class="flex items-center gap-4 p-3 hover:bg-emerald-50 rounded-lg cursor-pointer transition-colors border-b border-slate-100 last:border-0"
                                           :class="selected.includes(klien.id) ? 'bg-emerald-100 border-emerald-300 ring-1 ring-emerald-400' : ''">

                                        <!-- Checkbox (Tanpa "name" agar tidak ganda dengan Input Hidden) -->
                                        <div class="flex items-center h-5 shrink-0">
                                            <input type="checkbox" :value="klien.id" x-model="selected" class="w-6 h-6 text-emerald-600 border-slate-300 rounded focus:ring-emerald-600 cursor-pointer shadow-sm">
                                        </div>

                                        <div class="flex flex-col min-w-0 w-full">
                                            <span class="text-sm font-bold text-slate-800 truncate" x-text="( (currentPage - 1) * itemsPerPage + index + 1 ) + '. ' + klien.nama"></span>
                                            <span class="text-[11px] sm:text-xs mt-0.5 flex flex-wrap items-center gap-2">
                                                <template x-if="klien.has_pk">
                                                    <span class="flex items-center gap-2">
                                                        <span><span class="text-slate-500 font-medium">Dibimbing oleh: </span><span class="text-indigo-700 font-bold" x-text="klien.pk_nama"></span></span>
                                                        <!-- PENAMBAHAN: TOMBOL LEPAS PK -->
                                                        <form method="POST" :action="`/admin/rekap/lepas/${klien.id}`" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin melepas/membatalkan Klien ini dari PK-nya?');">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-200 px-2 py-0.5 rounded text-[10px] font-bold transition-colors shadow-sm">Lepas PK</button>
                                                        </form>
                                                    </span>
                                                </template>
                                                <template x-if="!klien.has_pk">
                                                    <span class="text-amber-600 font-bold bg-amber-100 px-1.5 py-0.5 rounded">Belum Memiliki PK</span>
                                                </template>
                                            </span>
                                        </div>
                                    </label>
                                </template>

                                <!-- Pesan Kosong -->
                                <div x-show="filteredClients.length === 0" class="p-6 text-center text-sm text-slate-500 italic" style="display: none;">
                                    Data klien tidak ditemukan.
                                </div>
                            </div>

                            <!-- Tombol Navigasi Pagination -->
                            <div class="flex items-center justify-between mt-4 bg-white p-2.5 sm:p-3 rounded-xl border border-emerald-200 shadow-sm shrink-0">
                                <button type="button" @click="if(currentPage > 1) currentPage--" :disabled="currentPage === 1"
                                        class="px-3 sm:px-4 py-2 sm:py-2.5 bg-emerald-100 text-emerald-800 text-sm font-bold rounded-lg hover:bg-emerald-200 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    &laquo; Previous
                                </button>

                                <div class="text-center px-2">
                                    <span class="text-sm font-bold text-slate-700 block">
                                        Hal. <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                                    </span>
                                    <span class="text-[10px] sm:text-[11px] font-medium text-slate-500 block mt-0.5">Total: <span x-text="filteredClients.length"></span> Klien</span>
                                </div>

                                <button type="button" @click="if(currentPage < totalPages) currentPage++" :disabled="currentPage === totalPages"
                                        class="px-3 sm:px-4 py-2 sm:py-2.5 bg-emerald-100 text-emerald-800 text-sm font-bold rounded-lg hover:bg-emerald-200 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    Next &raquo;
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end pt-5 border-t border-slate-100">
                        <button type="submit" class="inline-flex min-h-[50px] items-center justify-center gap-2 rounded-xl bg-indigo-700 px-8 py-3 font-bold text-white shadow-md transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"></path></svg>
                            Hubungkan PK dengan Klien
                        </button>
                    </div>
                </form>
            </div>

        </main>

        <!-- Pop-up Notifikasi -->
        @if(session('success') || $errors->any())
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 sm:p-8 text-center shadow-2xl transition-all" x-transition:enter="ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                @if(session('success'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 ring-4 ring-emerald-50"><svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg></div>
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
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
