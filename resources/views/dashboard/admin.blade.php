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

    <div class="min-h-screen bg-slate-100 py-6 sm:py-10" x-data="{
        showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }},
        showEditModal: false,
        showImageModal: false,
        imgSrc: '',
        editData: { id: '', nama: '', nomor_induk: '', email: '', role: '' },
        editFormAction: '',
        openEditUser(user) {
            this.editData = user;
            this.editFormAction = '/admin/user/' + user.id;
            this.showEditModal = true;
        }
    }">
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
                            @forelse($daftarPengawas as $pengawas)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-semibold">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $pengawas->nama }}</td>
                                <td class="px-6 py-4">{{ $pengawas->nomor_induk }}</td>
                                <td class="px-6 py-4">{{ $pengawas->email ?? '-' }}</td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <button @click="openEditUser({{ $pengawas->toJson() }})" class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Edit</button>
                                    <form action="{{ route('admin.user.destroy', $pengawas->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus akun Pengawas ini? Semua data kinerjanya juga akan ikut terhapus.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-6 text-slate-500">Tidak ada data Pengawas.</td></tr>
                            @endforelse
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
                            @forelse($daftarNarapidana as $napi)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-semibold">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $napi->nama }}</td>
                                <td class="px-6 py-4">{{ $napi->nomor_induk }}</td>
                                <td class="px-6 py-4">{{ $napi->email ?? '-' }}</td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <button @click="openEditUser({{ $napi->toJson() }})" class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Edit</button>
                                    <form action="{{ route('admin.user.destroy', $napi->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus akun Klien ini? Semua data laporan absensinya juga akan ikut terhapus.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-6 text-slate-500">Tidak ada data Klien/Narapidana.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-indigo-800 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="font-bold text-lg">Kontrol Seluruh Penilaian Kinerja PK</h3>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-slate-700 min-w-[700px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-sm font-bold uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-4">Periode</th>
                                <th class="px-6 py-4">Nama PK/Pengawas</th>
                                <th class="px-6 py-4 text-center">Predikat</th>
                                <th class="px-6 py-4 text-center">Aksi Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($semuaKinerja as $kinerja)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold">{{ $kinerja->bulan }}/{{ $kinerja->tahun }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $kinerja->pengawas->nama ?? 'Akun Telah Dihapus' }}</td>
                                <td class="px-6 py-4 text-center font-bold text-blue-700">{{ $kinerja->predikat }} ({{ $kinerja->rata_rata }}%)</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.kinerja.destroy', $kinerja->id) }}" method="POST" onsubmit="return confirm('Yakin menghapus data kinerja ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">Hapus Kinerja</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-6 text-slate-500">Belum ada data Penilaian Kinerja masuk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-teal-800 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="font-bold text-lg">Kontrol Seluruh Laporan Absensi Klien</h3>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-slate-700 min-w-[900px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-sm font-bold uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Nama Klien</th>
                                <th class="px-6 py-4">Kegiatan</th>
                                <th class="px-6 py-4">Dibimbing Oleh (PK)</th>
                                <th class="px-6 py-4 text-center">Bukti & Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($semuaAbsensi as $absensi)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold">{{ \Carbon\Carbon::parse($absensi->tanggal_waktu)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $absensi->narapidana->nama ?? 'Akun Telah Dihapus' }}</td>
                                <td class="px-6 py-4">{{ $absensi->jenis_kegiatan }}</td>
                                <td class="px-6 py-4 text-sm">{{ $absensi->pengawas->nama ?? 'Belum Dipilih' }}</td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <button type="button" @click.prevent="showImageModal = true; imgSrc = '{{ asset('storage/' . $absensi->bukti_file) }}'" class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-2 rounded-lg text-sm font-bold transition-colors">Lihat</button>
                                    <form action="{{ route('admin.absensi.destroy', $absensi->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin menghapus laporan absensi ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-2 rounded-lg text-sm font-bold transition-colors">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-6 text-slate-500">Belum ada data Laporan Absensi masuk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

        <div x-cloak x-show="showEditModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4">
            <div @click="showEditModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">
                <div class="bg-indigo-900 px-6 py-4 flex justify-between items-center">
                    <h3 class="font-bold text-white text-lg">Edit Data Pengguna</h3>
                    <button @click="showEditModal = false" class="text-indigo-200 hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form :action="editFormAction" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" x-model="editData.nama" required pattern="[a-zA-Z\s]+" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Nomor Induk (NIK/NIP/NRP)</label>
                        <input type="text" name="nomor_induk" x-model="editData.nomor_induk" required inputmode="numeric" pattern="[0-9]*" maxlength="18" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Email Aktif (Opsional)</label>
                        <input type="email" name="email" x-model="editData.email" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Role / Peran</label>
                        <select name="role" x-model="editData.role" required class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                            <option value="admin">Administrator</option>
                            <option value="pengawas">PK / Pengawas Lapas</option>
                            <option value="narapidana">Klien / Narapidana</option>
                        </select>
                    </div>
                    <div class="pt-2 border-t border-slate-200">
                        <label class="block text-sm font-bold text-slate-800 mb-1">Reset Password Baru</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mereset password" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="text-[11px] text-slate-500 mt-1">Isi minimal 8 karakter jika klien lupa password.</p>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-700 text-white font-bold hover:bg-indigo-800 shadow-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak x-show="showImageModal" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/95 px-4 backdrop-blur-md transition-opacity">
            <div @click="showImageModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 flex w-full max-w-4xl flex-col items-center justify-center">
                <button @click="showImageModal = false" class="mb-4 bg-red-600 px-5 py-2.5 font-bold text-white rounded-xl hover:bg-red-700">Tutup Foto</button>
                <img :src="imgSrc" class="max-h-[82vh] max-w-full rounded-2xl object-contain shadow-2xl">
            </div>
        </div>

        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm transition-opacity">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-center shadow-2xl transition-all sm:p-8">
                @if(session('success'))
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 ring-1 ring-inset ring-emerald-100">
                        <svg class="h-10 w-10 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="mb-2 text-2xl font-bold text-slate-900">Berhasil!</h3>
                    <p class="mb-6 text-base leading-relaxed text-slate-600">{{ session('success') }}</p>
                @elseif($errors->any())
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-red-50 ring-1 ring-inset ring-red-100">
                        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 class="mb-3 text-2xl font-bold text-slate-900">Terjadi Kesalahan!</h3>
                    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-left text-sm leading-relaxed text-red-700">
                        <ul class="list-inside list-disc space-y-1.5">
                            @foreach(array_unique($errors->all()) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button @click="showAlert = false" class="w-full bg-indigo-900 hover:bg-indigo-800 text-white font-bold py-3 px-4 rounded-xl">Tutup Peringatan</button>
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
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #94a3b8 #e2e8f0; }
    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 9999px; }
</style>
