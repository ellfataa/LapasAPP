<x-app-layout>

    <div x-data="{ sidebarOpen: false, showAlert: {{ $errors->any() ? 'true' : 'false' }} }" class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0 bg-slate-50 p-4 sm:p-6 lg:p-8">
            <div class="bapas-admin-header flex items-center gap-4 bg-slate-50 mb-8">
                <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                    Edit Data Klien/Narapidana
                </h2>
            </div>

            <div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mx-auto w-full">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Ubah Akun Klien: {{ $napi->nama }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Kosongkan kolom password baru di bawah jika Anda tidak ingin mengubah password klien ini.</p>
                </div>

                <form action="{{ route('admin.user.update', $napi->id) }}" method="POST" class="space-y-5" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap Klien/Narapidana</label>
                        <input type="text" name="nama" value="{{ old('nama', $napi->nama) }}" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s.,'’()\/&-]+" title="Nama hanya boleh berisi huruf, spasi, dan tanda baca umum" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Induk (NIK/Registrasi)</label>
                        <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $napi->nomor_induk) }}" required maxlength="50" placeholder="Masukkan Nomor Induk (Huruf/Angka)" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- DIKIRIMKAN KOMENTAR:
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Google <span class="text-slate-400 font-medium">(Opsional)</span></label>
                        <input type="email" name="email" value="{{ old('email', $napi->email) }}" placeholder="Belum mengisi email" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    --}}

                    <input type="hidden" name="role" value="narapidana">

                    <div class="pt-6 mt-2 border-t border-slate-200">
                        <label class="text-sm font-bold text-slate-800 mb-2 flex items-center justify-between">
                            Ubah Password Akun Klien
                            <span class="text-[11px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-bold uppercase tracking-wider">Aksi Berbahaya</span>
                        </label>
                        <input type="password" name="password" placeholder="Isi minimal 8 karakter hanya jika klien lupa password" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div class="pt-6 mt-6 flex items-center justify-end gap-3 border-t border-slate-100">
                        <a href="{{ route('admin.narapidana.index') }}" class="inline-flex min-h-[48px] items-center justify-center px-6 py-2.5 rounded-xl bg-red-500 font-bold text-sm text-white hover:bg-red-300 transition-colors">Batal</a>
                        <button type="submit" class="inline-flex min-h-[48px] items-center justify-center px-8 py-2.5 rounded-xl bg-green-700 text-white font-bold text-sm hover:bg-green-800 transition-colors shadow-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </main>

        @if($errors->any())
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 sm:p-8 text-center shadow-2xl">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-slate-900 mb-3">Gagal Memperbarui Akun</h3>
                <ul class="text-sm text-red-700 text-left bg-red-50 p-4 rounded-xl mb-6 list-disc list-inside font-medium border border-red-100 space-y-1">
                    @foreach(array_unique($errors->all()) as $error) <li>{{ $error }}</li> @endforeach
                </ul>
                <button @click="showAlert = false" class="w-full bg-indigo-900 text-white font-bold py-3 rounded-xl transition-colors hover:bg-indigo-950">Tutup</button>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>

<style>
    [x-cloak] { display: none !important; }
</style>
