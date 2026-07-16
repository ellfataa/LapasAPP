<x-app-layout>

    <!-- Tambahkan variabel 'fileName' dan 'fileSelected' di x-data -->
    <div x-data="{ sidebarOpen: false, showImportModal: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }}, fileName: '', fileSelected: false }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0">
            <div class="p-4 sm:p-6 lg:p-8 space-y-8 flex-1 overflow-y-auto">
                <div class="bapas-admin-header flex items-center gap-4 bg-slate-100">
                    <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                        Manajemen Akun PK/Pengawas
                    </h2>
                </div>

                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-lg sm:text-xl text-slate-800 break-words">Tambah Data</h3>
                        <p class="text-sm text-slate-500 mt-1 leading-relaxed">Kelola data PK/Pengawas dengan tambah manual atau import via berkas Excel/CSV.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3 w-full sm:w-auto xl:justify-end">
                        <button @click="showImportModal = true; fileSelected = false; fileName = ''" class="w-full sm:w-auto min-h-[44px] bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 sm:px-5 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="whitespace-nowrap">Import Excel/CSV</span>
                        </button>
                        <a href="{{ route('admin.pengawas.create') }}" class="w-full sm:w-auto min-h-[44px] bg-indigo-700 hover:bg-indigo-800 text-white font-bold py-2.5 px-4 sm:px-5 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm text-center">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                            <span class="whitespace-nowrap">Tambah PK Manual</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <form method="GET" action="{{ route('admin.pengawas.index') }}" class="flex flex-col sm:flex-row gap-4">
                        <div class="relative flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan Nama Lengkap atau Nomor Induk (NRP/NIP)..." class="block min-h-[48px] w-full pl-12 pr-4 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm transition">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="min-h-[48px] bg-slate-800 hover:bg-slate-900 text-white font-bold px-8 rounded-xl text-sm transition-colors shadow-sm">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('admin.pengawas.index') }}" class="inline-flex items-center justify-center min-h-[48px] bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 rounded-xl text-sm border border-slate-300 transition-colors">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    <div class="overflow-x-auto custom-scrollbar flex-1">
                        <table class="w-full text-left text-slate-700 min-w-[750px]">
                            <thead class="bg-slate-50 border-b border-slate-200 text-sm font-bold uppercase tracking-wide">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center">No</th>
                                    <th class="px-6 py-4">Nama Lengkap</th>
                                    <th class="px-6 py-4">Nomor Induk (NRP/NIP)</th>
                                    <th class="px-6 py-4 text-center">Aksi Manajemen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($daftarPengawas as $pk)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-center font-semibold text-slate-500">{{ $daftarPengawas->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $pk->nama }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-600">{{ $pk->nomor_induk }}</td>
                                    <td class="px-6 py-2 text-center">
                                        <div class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                                            <a href="{{ route('admin.pengawas.edit', $pk->id) }}" class="inline-flex items-center justify-center bg-blue-100 text-blue-700 hover:bg-blue-200 px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-md text-[10px] sm:text-xs font-bold leading-none transition-colors">Edit Akun</a>
                                            <form action="{{ route('admin.user.destroy', $pk->id) }}" method="POST" class="inline-flex" onsubmit="return confirm('Hapus permanen akun PK ini beserta data kinerjanya?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-md text-[10px] sm:text-xs font-bold leading-none transition-colors">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-slate-500 font-medium">
                                        @if(request('search'))
                                            Tidak ditemukan data PK pembimbing dengan kata kunci "{{ request('search') }}".
                                        @else
                                            Belum ada akun Pengawas/PK yang terdaftar di dalam database.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($daftarPengawas->hasPages())
                        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4">
                            {{ $daftarPengawas->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </main>

        {{-- MODAL IMPORT EXCEL PENGAWAS --}}
        <div x-cloak x-show="showImportModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4">
            <div @click="showImportModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">

                <div class="bg-emerald-700 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Import Data via Excel/CSV</h3>
                    <button @click="showImportModal = false" class="text-emerald-200 hover:text-white transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form action="{{ route('admin.pengawas.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf

                    <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl text-sm border border-emerald-100">
                        <p class="font-bold mb-1">Petunjuk Import:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Mendukung format file <strong>.xlsx / .xls / .csv</strong></li>
                            <li>Kolom A (Kolom 1): <strong>Nama Lengkap</strong></li>
                            <li>Kolom B (Kolom 2): <strong>Nomor Induk(NRP/NIP) *Bisa dikosongkan</strong></li>
                            <li>Jika Nama sudah ada di sistem, maka yang terupdate hanya NRP/NIP-nya.</li>
                            <li>Password default: <span class="bg-emerald-200 text-emerald-900 px-1.5 py-0.5 rounded font-mono font-bold">bapas123</span></li>
                        </ul>
                    </div>

                    <div class="relative border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer group"
                         :class="fileSelected ? 'border-emerald-400 bg-emerald-50/50' : 'border-slate-300 bg-slate-50 hover:bg-emerald-50/30 hover:border-emerald-300'">

                        <!-- Tampilan Jika File Belum Dipilih -->
                        <div x-show="!fileSelected" class="flex flex-col items-center pointer-events-none">
                            <div class="h-14 w-14 mb-3 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center transition-transform group-hover:scale-110">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </div>
                            <span class="block text-sm font-bold text-slate-800 mb-1">Pilih File Dokumen</span>
                            <span class="text-xs text-slate-500 font-medium">Klik atau seret file ke area ini</span>
                        </div>

                        <!-- Tampilan Jika File SUDAH Dipilih -->
                        <div x-show="fileSelected" x-cloak class="flex flex-col items-center pointer-events-none">
                            <div class="h-14 w-14 mb-3 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="block text-sm font-bold text-emerald-700 mb-1">File Siap Di-import!</span>
                            <!-- Menampilkan Nama File -->
                            <span x-text="fileName" class="text-xs text-slate-600 font-semibold bg-white border border-slate-200 px-3 py-1.5 rounded-lg max-w-xs truncate shadow-sm"></span>
                            <span class="text-[10px] text-slate-400 mt-3 hover:underline cursor-pointer pointer-events-auto relative z-20">Klik area ini jika ingin mengganti file</span>
                        </div>

                        <!-- Input File (Transparan menutupi seluruh area) -->
                        <input type="file"
                               name="file_excel"
                               accept=".csv, .xlsx, .xls"
                               required
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @change="fileSelected = true; fileName = $event.target.files[0].name"
                        >
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row justify-end gap-3 border-t border-slate-100 mt-2">
                        <button type="button" @click="showImportModal = false" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">Batal</button>

                        <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-white font-bold transition-all shadow-md focus:ring-4 focus:ring-emerald-200"
                                :class="fileSelected ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-400 cursor-not-allowed hover:bg-slate-400 opacity-70'"
                                :disabled="!fileSelected">
                            Mulai Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Peringatan Sistem -->
        @if(session('success') || $errors->any())
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 sm:p-8 text-center shadow-2xl">
                @if(session('success'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg></div>
                    <h3 class="font-bold text-xl mb-2 text-slate-900">Berhasil!</h3>
                    <p class="text-sm text-slate-600 mb-5 leading-relaxed">{{ session('success') }}</p>
                @elseif($errors->any())
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 mb-3">Terjadi Kendala</h3>
                    <ul class="text-sm text-red-700 text-left bg-red-50 p-4 rounded-xl mb-6 list-disc list-inside font-medium border border-red-100 space-y-1">
                        @foreach(array_unique($errors->all()) as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                @endif
                <button @click="showAlert = false" class="w-full bg-indigo-900 text-white font-bold py-3 rounded-xl transition-colors hover:bg-indigo-950">Tutup</button>
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
</style>
