<x-app-layout>

    <div x-data="{ sidebarOpen: false, showImportModal: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }" class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0">
            <h2 class="font-bold text-xl sm:text-2xl text-slate-900 leading-tight tracking-tight">Manajemen Akun PK/Pengawas</h2>

            <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center shadow-sm">
                <button @click="sidebarOpen = true" class="text-slate-600 hover:text-indigo-700 focus:outline-none"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                <span class="ml-4 font-bold text-slate-800 text-lg">Navigasi Admin</span>
            </div>

            <div class="p-4 sm:p-6 lg:p-8 space-y-6 flex-1 overflow-y-auto">

                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div>
                        <h3 class="font-bold text-xl text-slate-800">Tambah Data</h3>
                        <p class="text-sm text-slate-500 mt-1">Kelola data PK dengan tambah manual atau import via berkas Excel/CSV.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                        <button @click="showImportModal = true" class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Import Excel/CSV
                        </button>
                        <a href="{{ route('admin.pengawas.create') }}" class="flex-1 sm:flex-none bg-indigo-700 hover:bg-indigo-800 text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm text-center text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                            Tambah PK Manual
                        </a>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
                    <form method="GET" action="{{ route('admin.pengawas.index') }}" class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama lengkap atau Nomor Induk (NRP/NIP)..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-colors">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('admin.pengawas.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-sm border border-slate-300 text-center transition-colors">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-slate-700 min-w-[700px]">
                            <thead class="bg-slate-50 border-b border-slate-200 text-sm font-bold uppercase tracking-wide">
                                <tr>
                                    <th class="px-6 py-4">Nama Lengkap</th>
                                    <th class="px-6 py-4">Nomor Induk (NRP/NIP)</th>
                                    <th class="px-6 py-4">Email Google (Opsional)</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($daftarPengawas as $pk)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $pk->nama }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-600">{{ $pk->nomor_induk }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $pk->email ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center space-x-2">
                                        <a href="{{ route('admin.pengawas.edit', $pk->id) }}" class="inline-block bg-blue-100 text-blue-700 hover:bg-blue-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Edit Akun</a>
                                        <form action="{{ route('admin.user.destroy', $pk->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus permanen akun PK ini beserta data kinerjanya?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-10 text-slate-500 font-medium">Tidak ada data PK pembimbing.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>

        <div x-cloak x-show="showImportModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4">
            <div @click="showImportModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all" x-transition>
                <div class="bg-emerald-700 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Import Data via Excel / CSV</h3>
                    <button @click="showImportModal = false" class="text-emerald-200 hover:text-white"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form action="{{ route('admin.pengawas.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl text-sm border border-emerald-100">
                        <p class="font-bold mb-1">Petunjuk Import:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Mendukung file format <strong>.xlsx / .xls / .csv</strong></li>
                            <li>Kolom utama (A): <strong>Nama Lengkap</strong>.</li>
                            <li>Password default login awal: <span class="bg-emerald-200 px-1 font-mono font-bold">bapas123</span></li>
                        </ul>
                    </div>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center bg-slate-50 hover:bg-emerald-50 transition-colors">
                        <svg class="mx-auto h-12 w-12 text-emerald-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <label class="block text-sm font-bold text-slate-800 mb-2 cursor-pointer">
                            <span>Pilih File Dokumen</span>
                            <input type="file" name="file_excel" accept=".csv, .xlsx, .xls" required class="mt-2 w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200">
                        </label>
                    </div>
                    <div class="pt-2 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="showImportModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-bold hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-md">Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success') || $errors->any())
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl">
                @if(session('success'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg></div>
                    <h3 class="font-bold text-xl mb-2">Berhasil!</h3><p class="text-slate-600 mb-5 text-sm">{{ session('success') }}</p>
                @elseif($errors->any())
                    <h3 class="font-bold text-xl text-red-600 mb-3 mt-2">Error!</h3>
                    <ul class="text-sm text-red-600 text-left bg-red-50 p-3 rounded mb-5 list-disc list-inside">
                        @foreach(array_unique($errors->all()) as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                @endif
                <button @click="showAlert = false" class="w-full bg-indigo-900 text-white font-bold py-2.5 rounded-xl">Tutup</button>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #94a3b8 #e2e8f0; }
    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 9999px; }
</style>
