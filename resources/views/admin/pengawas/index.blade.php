<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl sm:text-2xl text-slate-900 leading-tight tracking-tight">Manajemen Akun PK / Pengawas</h2>
    </x-slot>

    <div x-data="{ sidebarOpen: false, showAddModal: false, showImportModal: false, showEditModal: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }}, editData: {}, editFormAction: '', openEdit(user) { this.editData = user; this.editFormAction = '/admin/user/' + user.id; this.showEditModal = true; } }" class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0">
            <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center shadow-sm">
                <button @click="sidebarOpen = true" class="text-slate-600 hover:text-indigo-700 focus:outline-none"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                <span class="ml-4 font-bold text-slate-800 text-lg">Navigasi Admin</span>
            </div>

            <div class="p-4 sm:p-6 lg:p-8 space-y-6 flex-1 overflow-y-auto">

                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div>
                        <h3 class="font-bold text-xl text-slate-800">Daftar Pengawas Lapas</h3>
                        <p class="text-sm text-slate-500 mt-1">Kelola data pembimbing, tambah manual, atau import via CSV.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                        <button @click="showImportModal = true" class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Import File .CSV
                        </button>
                        <button @click="showAddModal = true" class="flex-1 sm:flex-none bg-indigo-700 hover:bg-indigo-800 text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah PK Manual
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-slate-700 min-w-[700px]">
                            <thead class="bg-slate-50 border-b border-slate-200 text-sm font-bold uppercase tracking-wide">
                                <tr>
                                    <th class="px-6 py-4">Nama Lengkap</th>
                                    <th class="px-6 py-4">NIP / Identitas</th>
                                    <th class="px-6 py-4">Email</th>
                                    <th class="px-6 py-4 text-center">Aksi Manajemen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($daftarPengawas as $pk)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $pk->nama }}</td>
                                    <td class="px-6 py-4 font-medium">{{ $pk->nomor_induk }}</td>
                                    <td class="px-6 py-4">{{ $pk->email ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center space-x-2">
                                        <button @click="openEdit({{ $pk->toJson() }})" class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-4 py-2 rounded-lg text-sm font-bold">Edit Akun</button>
                                        <form action="{{ route('admin.user.destroy', $pk->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus permanen akun PK ini beserta data kinerjanya?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg text-sm font-bold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-8 text-slate-500">Belum ada akun Pengawas/PK yang terdaftar.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>

        <div x-cloak x-show="showAddModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4">
            <div @click="showAddModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-indigo-900 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Tambah Akun PK Baru</h3>
                    <button @click="showAddModal = false"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form action="{{ route('admin.pengawas.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div><label class="block text-sm font-bold mb-1">Nama Lengkap</label><input type="text" name="nama" required pattern="[a-zA-Z\s]+" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-bold mb-1">Nomor Induk Pegawai (NIP/NRP)</label><input type="text" name="nomor_induk" required inputmode="numeric" pattern="[0-9]+" maxlength="18" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-bold mb-1">Email Aktif (Opsional)</label><input type="email" name="email" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-bold mb-1">Password Pertama</label><input type="password" name="password" required minlength="8" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500"></div>
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-700 text-white font-bold">Simpan PK</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak x-show="showImportModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4">
            <div @click="showImportModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-emerald-700 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Import Data via CSV</h3>
                    <button @click="showImportModal = false"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form action="{{ route('admin.pengawas.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf

                    <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl text-sm border border-emerald-100 mb-4">
                        <p class="font-bold mb-1">Petunjuk Import Format Nama PK:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pastikan file di save as <strong>.CSV (Comma delimited)</strong>.</li>
                            <li>Sistem mendeteksi 1 Kolom berisi: <strong>NAMA LENGKAP</strong>.</li>
                            <li><strong>NIP</strong> akan dibuat secara acak. Admin dapat mengubahnya nanti.</li>
                            <li><strong>Password Default</strong> untuk semua PK yang diimport adalah: <span class="bg-emerald-200 px-1 font-mono">bapas123</span></li>
                        </ul>
                    </div>

                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center bg-white hover:bg-emerald-50 transition-colors">
                        <svg class="mx-auto h-12 w-12 text-emerald-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <label class="block text-sm font-bold text-slate-800 mb-2 cursor-pointer">
                            <span>Pilih File Spreadsheet (.CSV)</span>
                            <input type="file" name="file_excel" accept=".csv" required class="mt-2 w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200">
                        </label>
                    </div>
                    <div class="pt-2 flex justify-end gap-3">
                        <button type="button" @click="showImportModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold">Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak x-show="showEditModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4">
            <div @click="showEditModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-indigo-900 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg">Edit Data PK</h3>
                    <button @click="showEditModal = false"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form :action="editFormAction" method="POST" class="p-6 space-y-4">
                    @csrf @method('PUT')
                    <div><label class="block text-sm font-bold mb-1">Nama Lengkap</label><input type="text" name="nama" x-model="editData.nama" required pattern="[a-zA-Z\s]+" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-bold mb-1">Nomor Induk (NIP)</label><input type="text" name="nomor_induk" x-model="editData.nomor_induk" required inputmode="numeric" pattern="[0-9]+" maxlength="18" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-bold mb-1">Email</label><input type="email" name="email" x-model="editData.email" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500"></div>
                    <div class="hidden">
                        <input type="text" name="role" x-model="editData.role" required>
                    </div>
                    <div class="pt-2 border-t border-slate-200">
                        <label class="block text-sm font-bold mb-1">Reset Password Baru</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak direset" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500">
                    </div>
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-700 text-white font-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl">
                @if(session('success'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg></div>
                    <h3 class="font-bold text-xl mb-2">Berhasil!</h3><p class="text-slate-600 mb-5">{{ session('success') }}</p>
                @elseif($errors->any())
                    <h3 class="font-bold text-xl text-red-600 mb-3 mt-2">Error!</h3>
                    <ul class="text-sm text-red-600 text-left bg-red-50 p-3 rounded mb-5 list-disc list-inside">
                        @foreach(array_unique($errors->all()) as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                @endif
                <button @click="showAlert = false" class="w-full bg-indigo-900 text-white font-bold py-2.5 rounded-xl">Tutup</button>
            </div>
        </div>

    </div>
</x-app-layout>
