<x-app-layout>

    <div x-data="{ sidebarOpen: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0">

            <div class="p-4 sm:p-6 lg:p-8 space-y-6 flex-1 overflow-y-auto">
                <div class="bapas-admin-header flex items-center gap-4 bg-slate-100">
                    <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                        Manajemen Akun Klien/Narapidana
                    </h2>
                </div>

                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div>
                        <h3 class="font-bold text-xl text-slate-800">Tambah Data</h3>
                        <p class="text-sm text-slate-500 mt-1">Kelola data klien, modifikasi akun, dan hapus data riwayat secara permanen.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                        <a href="{{ route('admin.narapidana.create') }}" class="flex-1 sm:flex-none bg-indigo-700 hover:bg-indigo-800 text-white font-bold py-2.5 px-5 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm text-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Klien/Narapidana
                        </a>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <form method="GET" action="{{ route('admin.narapidana.index') }}" class="flex flex-col sm:flex-row gap-4">
                        <div class="relative flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama lengkap atau Nomor Induk (NIK/Registrasi)..." class="block min-h-[48px] w-full pl-12 pr-4 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm transition">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="min-h-[48px] bg-slate-800 hover:bg-slate-900 text-white font-bold px-8 rounded-xl text-sm transition-colors shadow-sm">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('admin.narapidana.index') }}" class="inline-flex items-center justify-center min-h-[48px] bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 rounded-xl text-sm border border-slate-300 transition-colors">Reset</a>
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
                                    <th class="px-6 py-4">Nomor Induk (NIK/Registrasi)</th>
                                    <th class="px-6 py-4">Email Google (Opsional)</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($daftarNarapidana as $napi)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-center font-semibold text-slate-500">{{ $daftarNarapidana->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $napi->nama }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-600">{{ $napi->nomor_induk }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $napi->email ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                                            <a href="{{ route('admin.narapidana.edit', $napi->id) }}" class="inline-flex items-center justify-center bg-emerald-100 text-emerald-700 hover:bg-emerald-200 px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-md text-[10px] sm:text-xs font-bold leading-none transition-colors">Edit Akun</a>
                                            <form action="{{ route('admin.user.destroy', $napi->id) }}" method="POST" class="inline-flex" onsubmit="return confirm('Hapus permanen akun Klien ini? Seluruh riwayat absen dan foto-fotonya juga akan terhapus!');">
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
                                            Tidak ditemukan data Klien dengan kata kunci "{{ request('search') }}".
                                        @else
                                            Belum ada akun Klien/Narapidana yang terdaftar di dalam database.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($daftarNarapidana->hasPages())
                        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4">
                            {{ $daftarNarapidana->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </main>

        @if(session('success') || $errors->any())
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl">
                @if(session('success'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg></div>
                    <h3 class="font-bold text-xl mb-2">Berhasil!</h3><p class="text-slate-600 mb-5 text-sm">{{ session('success') }}</p>
                @elseif($errors->any())
                    <h3 class="font-bold text-xl text-red-600 mb-3 mt-2">Error!</h3>
                    <ul class="text-sm text-red-600 text-left bg-red-50 p-3 rounded mb-5 list-disc list-inside border border-red-100 space-y-1">
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
    header:has(.bapas-admin-header) { background-color: #f1f5f9 !important; border-bottom: 1px solid #e2e8f0; box-shadow: none !important; }
    .bapas-admin-header { background-color: #f1f5f9; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #94a3b8 #e2e8f0; }
    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 9999px; }
</style>
