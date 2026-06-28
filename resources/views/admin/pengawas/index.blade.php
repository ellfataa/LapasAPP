<x-app-layout>

    <div x-data="{ sidebarOpen: false, showImportModal: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }"
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

                <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center shadow-sm rounded-xl">
                    <button @click="sidebarOpen = true" class="text-slate-600 hover:text-indigo-700 focus:outline-none"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                    <span class="ml-4 font-bold text-slate-800 text-lg">Navigasi Admin</span>
                </div>

                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-lg sm:text-xl text-slate-800 break-words">Tambah Data</h3>
                        <p class="text-sm text-slate-500 mt-1 leading-relaxed">Kelola data pengawas dengan tambah manual atau import via berkas Excel/CSV.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3 w-full sm:w-auto xl:justify-end">
                        <button @click="showImportModal = true" class="w-full sm:w-auto min-h-[44px] bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 sm:px-5 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm">
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
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama lengkap atau Nomor Induk (NIP/NRP)..." class="block min-h-[48px] w-full pl-12 pr-4 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm transition">
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
                                    <th class="px-6 py-4">Email Google (Opsional)</th>
                                    <th class="px-6 py-4 text-center">Aksi Manajemen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($daftarPengawas as $pk)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-center font-semibold text-slate-500">{{ $daftarPengawas->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $pk->nama }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-600">{{ $pk->nomor_induk }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $pk->email ?? '-' }}</td>
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
                                    <td colspan="5" class="text-center py-10 text-slate-500 font-medium">
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

        <div x-cloak x-show="showImportModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4">
            <div @click="showImportModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">
                <div class="bg-emerald-700 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Import Data via Excel/CSV</h3>
                    <button @click="showImportModal = false" class="text-emerald-200 hover:text-white"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form action="{{ route('admin.pengawas.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl text-sm border border-emerald-100">
                        <p class="font-bold mb-1">Petunjuk Import:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Mendukung file format <strong>.xlsx / .xls / .csv</strong></li>
                            <li>Harus Terdapat Kolom: <strong>Nama Lengkap</strong></li>
                            <li>Password default login awal: <span class="bg-emerald-200 px-1 font-mono font-bold">bapas123</span></li>
                        </ul>
                    </div>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center bg-slate-50 hover:bg-emerald-50 transition-colors cursor-pointer group relative">
                        <svg class="mx-auto h-12 w-12 text-emerald-500 mb-3 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <span class="block text-sm font-bold text-slate-800 mb-2">Pilih File Dokumen</span>
                        <input type="file" name="file_excel" accept=".csv, .xlsx, .xls" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <span class="text-xs text-slate-500 pointer-events-none group-hover:text-emerald-600 font-medium">Klik atau seret file ke area ini</span>
                    </div>
                    <div class="pt-2 flex justify-end gap-3 border-t border-slate-100 mt-2">
                        <button type="button" @click="showImportModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-bold hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-md">Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success') || $errors->any())
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 sm:p-8 text-center shadow-2xl">
                @if(session('success'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg></div>
                    <h3 class="font-bold text-xl mb-2 text-slate-900">Operasi Berhasil!</h3>
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
