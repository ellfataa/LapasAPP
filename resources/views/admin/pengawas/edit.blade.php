<x-app-layout>

    <div x-data="{ sidebarOpen: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }" class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0 bg-slate-50 p-4 sm:p-6 lg:p-8">
            <h2 class="font-bold text-xl sm:text-2xl text-slate-900 leading-tight tracking-tight">Edit Data PK/Pengawas</h2>

            <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center shadow-sm mb-6 rounded-xl">
                <button @click="sidebarOpen = true" class="text-slate-600 hover:text-indigo-700 focus:outline-none"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                <span class="ml-4 font-bold text-slate-800 text-lg">Navigasi Admin</span>
            </div>

            <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mx-auto w-full">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Ubah Akun: {{ $pk->nama }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Kosongkan kolom password baru di bawah jika Anda tidak ingin mengubah password PK ini.</p>
                </div>

                <form action="{{ route('admin.user.update', $pk->id) }}" method="POST" class="space-y-5" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $pk->nama) }}" required pattern="[a-zA-Z\s]+" title="Nama hanya boleh berisi huruf dan spasi" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Induk (NRP/NIP)</label>
                        <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $pk->nomor_induk) }}" required inputmode="numeric" pattern="[0-9]+" maxlength="18" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Google <span class="text-slate-400 font-medium">(Opsional)</span></label>
                        <input type="email" name="email" value="{{ old('email', $pk->email) }}" placeholder="Belum mengisi email" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <input type="hidden" name="role" value="pengawas">

                    <div class="pt-6 mt-2 border-t border-slate-200">
                        <label class="text-sm font-bold text-slate-800 mb-2 flex items-center justify-between">
                            Ubah Password Akun PK
                            <span class="text-[11px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-bold uppercase tracking-wider">Berbahaya</span>
                        </label>
                        <input type="password" name="password" placeholder="Isi minimal 8 karakter hanya jika ingin mengubah password" class="block min-h-[48px] w-full rounded-xl border-slate-300 px-4 text-sm text-slate-900 shadow-sm transition focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div class="pt-6 mt-6 flex items-center justify-end gap-3 border-t border-slate-100">
                        <a href="{{ route('admin.pengawas.index') }}" class="inline-flex min-h-[48px] items-center justify-center px-6 py-2.5 rounded-xl bg-slate-100 font-bold text-sm text-slate-700 hover:bg-slate-200 transition-colors">Batal</a>
                        <button type="submit" class="inline-flex min-h-[48px] items-center justify-center px-8 py-2.5 rounded-xl bg-blue-700 text-white font-bold text-sm hover:bg-blue-800 transition-colors shadow-md">Simpan Perubahan</button>
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
                    <h3 class="font-bold text-xl mb-2 text-slate-900">Operasi Berhasil!</h3>
                    <p class="text-sm text-slate-600 mb-5 leading-relaxed">{{ session('success') }}</p>
                @elseif($errors->any())
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 mb-3">Gagal Memperbarui Akun</h3>
                    <ul class="text-sm text-red-700 text-left bg-red-50 p-4 rounded-xl mb-6 list-disc list-inside border border-red-100 space-y-1">
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
    [x-cloak] { display: none !important; }
</style>
