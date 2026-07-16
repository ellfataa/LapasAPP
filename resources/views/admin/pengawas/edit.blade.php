<x-app-layout>

    <div x-data="{ sidebarOpen: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0 p-4 sm:p-6 lg:p-8">
            <div class="bapas-admin-header flex items-center gap-4 bg-slate-100 mb-8">
                <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                    Edit Data PK/Pengawas
                </h2>
            </div>

            <div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mx-auto w-full">
                <div class="border-b border-slate-100 pb-5 mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Ubah Akun: {{ $pk->nama }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Kosongkan kolom password baru di bawah jika Anda tidak ingin mengubah password PK ini.</p>
                </div>

                <form action="{{ route('admin.user.update', $pk->id) }}" method="POST" class="space-y-6" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $pk->nama) }}" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s.,'’()\/&-]+" title="Nama hanya boleh berisi huruf, spasi, dan tanda baca umum" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Induk (NRP/NIP)</label>
                        <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $pk->nomor_induk ?? '') }}" required maxlength="50" placeholder="Masukkan Nomor Induk (Huruf/Angka)" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{--
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Email <span class="text-slate-400 font-medium">(Opsional)</span></label>
                        <input type="email" name="email" value="{{ old('email', $pk->email) }}" placeholder="Belum mengisi email" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    --}}

                    <input type="hidden" name="role" value="pengawas">

                    <div class="pt-6 mt-2 border-t border-slate-200">
                        <label class="text-sm font-bold text-slate-800 mb-3 flex items-center justify-between">
                            Ubah Password Akun PK
                            <span class="text-[11px] bg-amber-100 text-amber-800 px-2.5 py-1 rounded-md font-bold uppercase tracking-wider">Aksi Berbahaya</span>
                        </label>
                        <input type="password" name="password" placeholder="Isi minimal 8 karakter hanya jika ingin mengubah password" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div class="pt-6 mt-6 flex items-center justify-end gap-4 border-t border-slate-100">
                        <a href="{{ route('admin.pengawas.index') }}" class="inline-flex min-h-[48px] items-center justify-center px-6 rounded-xl bg-red-500 font-bold text-sm text-white hover:bg-red-300 transition-colors">Batal</a>
                        <button type="submit" class="inline-flex min-h-[48px] items-center justify-center px-8 rounded-xl bg-green-700 text-white font-bold text-sm hover:bg-green-800 transition-colors shadow-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </main>

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
                    <h3 class="font-bold text-xl text-slate-900 mb-3">Gagal Memperbarui Akun</h3>
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
