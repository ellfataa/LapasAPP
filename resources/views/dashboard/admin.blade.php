<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 bg-slate-100">
            <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-900 text-white shadow-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </div>
            <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                Dashboard Administrator
            </h2>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-6 sm:py-10" x-data="{ showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:space-y-8 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5">
                    <div class="bg-blue-100 text-blue-700 p-4 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total PK / Pengawas</p>
                        <p class="text-3xl font-extrabold text-slate-900">{{ $totalPengawas }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5">
                    <div class="bg-emerald-100 text-emerald-700 p-4 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Narapidana</p>
                        <p class="text-3xl font-extrabold text-slate-900">{{ $totalNarapidana }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-5">
                    <div class="bg-amber-100 text-amber-700 p-4 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Laporan Absensi</p>
                        <p class="text-3xl font-extrabold text-slate-900">{{ $totalAbsensi }}</p>
                    </div>
                </div>
            </div>

            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-blue-900 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="font-bold text-lg">Manajemen Akun Pengawas / PK</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-sm font-bold uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-4">No</th>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">NIP / Identitas</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($daftarPengawas as $pengawas)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-semibold">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $pengawas->nama }}</td>
                                <td class="px-6 py-4">{{ $pengawas->nomor_induk }}</td>
                                <td class="px-6 py-4">{{ $pengawas->email }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.user.destroy', $pengawas->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Pengawas ini? Semua data kinerjanya juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Hapus Akun</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-emerald-800 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="font-bold text-lg">Manajemen Akun Klien / Narapidana</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-sm font-bold uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-4">No</th>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">NIK / Identitas</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($daftarNarapidana as $napi)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-semibold">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $napi->nama }}</td>
                                <td class="px-6 py-4">{{ $napi->nomor_induk }}</td>
                                <td class="px-6 py-4">{{ $napi->email }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.user.destroy', $napi->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Klien ini? Semua data laporan absensinya juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Hapus Akun</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

        <div x-show="showAlert" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/65 px-4 py-6 backdrop-blur-sm transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xl transition-all sm:p-8">
                @if(session('success'))
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 ring-1 ring-inset ring-emerald-100">
                        <svg class="h-10 w-10 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="mb-2 text-2xl font-bold text-slate-900">Berhasil!</h3>
                    <p class="mb-6 text-base leading-relaxed text-slate-600">{{ session('success') }}</p>
                @endif
                <button @click="showAlert = false" class="min-h-[48px] w-full rounded-xl bg-indigo-900 px-4 py-3 font-bold text-white shadow-sm transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                    Tutup Peringatan
                </button>
            </div>
        </div>

    </div>
</x-app-layout>

<style>
    header:has(.bapas-admin-header) {
        background-color: #f1f5f9 !important;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: none !important;
    }
    [x-cloak] { display: none !important; }
</style>
