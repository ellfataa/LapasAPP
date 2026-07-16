<x-app-layout>
    <x-slot name="header">
        <div class="bapas-narapidana-header flex items-center gap-4 bg-slate-100">
            <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm sm:flex">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>

            <h2 class="text-xl font-bold leading-tight tracking-tight text-slate-900 sm:text-2xl md:text-3xl">
                {{ __('Edit Data Absensi/Laporan Wajib') }}
            </h2>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-6 sm:py-10" x-data="{ showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Tombol Kembali -->
            <div class="mb-6 flex items-center">
                <a href="{{ route('dashboard.narapidana') }}" class="group inline-flex items-center gap-2.5 rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 transition-all hover:bg-blue-50 hover:text-blue-700 hover:ring-blue-200 focus:outline-none focus:ring-4 focus:ring-blue-100">
                    <svg class="h-5 w-5 text-slate-400 transition-colors group-hover:text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md">

                <!-- Header Form -->
                <div class="flex items-start gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-900 to-blue-900 px-5 py-5 sm:items-center sm:px-7 sm:py-6">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white ring-1 ring-inset ring-white/20">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>

                    <div class="pt-0.5 sm:pt-0">
                        <h3 class="text-lg font-bold text-white sm:text-xl">Perbarui Absensi/Laporan Wajib</h3>
                        <p class="mt-1 text-sm font-medium leading-relaxed text-blue-100">
                            Silakan perbarui data atau ganti foto bukti kegiatan Anda di bawah ini.
                        </p>
                    </div>
                </div>

                <form action="{{ route('absensi.update', $absensi->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="p-5 sm:p-7 lg:p-8">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6">

                            <!-- Input Tanggal -->
                            <div>
                                <label for="tanggal" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Tanggal Kegiatan</label>
                                <input id="tanggal" name="tanggal" type="date" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-base text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700" required max="{{ date('Y-m-d') }}" value="{{ \Carbon\Carbon::parse($absensi->tanggal_waktu)->format('Y-m-d') }}" />
                            </div>

                            <!-- Input Jenis Kegiatan -->
                            <div>
                                <label for="jenis_kegiatan" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Nama Kegiatan Sosial</label>
                                <input id="jenis_kegiatan" name="jenis_kegiatan" type="text" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-base text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700" required value="{{ old('jenis_kegiatan', $absensi->jenis_kegiatan) }}" />
                            </div>

                            <!-- Info PK/Pengawas -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Penanggung Jawab PK/Pengawas Anda</label>

                                <div class="flex items-center gap-3 min-h-[48px] w-full rounded-xl border border-blue-100 bg-blue-50/50 px-4 py-3 text-base shadow-sm">
                                    <svg class="h-6 w-6 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <div class="min-w-0 flex-1">
                                        @if($pembimbingSaya)
                                            <span class="block text-slate-900 font-bold truncate">{{ $pembimbingSaya->nama }}</span>
                                            <span class="block text-slate-500 text-xs font-medium truncate">NRP/NIP: {{ $pembimbingSaya->nomor_induk }}</span>
                                        @else
                                            <span class="block text-red-600 font-bold truncate">Belum Dipilih/Tidak Ada</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="mt-2 text-xs font-medium text-red-500 italic">*Pembaruan absensi ini akan otomatis sinkron di dashboard PK Anda.</p>
                            </div>
                        </div>

                        <!-- Area Ganti Foto -->
                        <div class="mt-8 sm:mt-10">
                            <label class="mb-3 block text-sm font-bold text-slate-800 sm:text-base">Dokumentasi/Bukti Foto</label>

                            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                <div class="grid grid-cols-1 lg:grid-cols-[300px_minmax(0,1fr)]">

                                    <!-- Thumbnail Foto Lama -->
                                    <div class="border-b border-slate-200 bg-slate-200/50 p-5 lg:border-b-0 lg:border-r flex flex-col items-center justify-center">
                                        <span class="mb-3 block text-xs font-bold uppercase tracking-wider text-slate-500">Foto Saat Ini</span>
                                        <div class="aspect-square w-full max-w-[220px] overflow-hidden rounded-xl bg-slate-200 shadow-sm ring-1 ring-inset ring-slate-300">
                                            <img src="{{ asset('storage/' . $absensi->bukti_file) }}" alt="Foto Lama" class="h-full w-full object-cover transition-transform duration-300 hover:scale-105">
                                        </div>
                                    </div>

                                    <!-- Input File Baru -->
                                    <div class="flex min-w-0 flex-col justify-center p-5 sm:p-7 lg:p-8">
                                        <h4 class="text-base font-bold text-slate-900">Ganti Foto Baru (Opsional)</h4>
                                        <p class="mt-1 text-sm leading-relaxed text-slate-500">Maksimal ukuran file 10MB. Biarkan bagian ini kosong jika Anda tidak ingin mengubah foto bukti yang lama.</p>

                                        <div class="mt-5 rounded-2xl border-2 border-dashed border-slate-300 bg-white p-2 transition hover:border-blue-400 hover:bg-blue-50/40 sm:p-3">
                                            <input id="bukti_file" name="bukti_file" type="file" class="block w-full cursor-pointer rounded-xl bg-slate-50 text-sm text-slate-600 shadow-sm ring-1 ring-inset ring-slate-200 file:mr-3 file:min-h-[48px] file:cursor-pointer file:border-0 file:bg-blue-900 file:px-5 file:py-3 file:text-sm file:font-bold file:text-white hover:file:bg-blue-800 sm:text-base sm:file:mr-4 sm:file:px-6" accept="image/jpeg, image/png, image/jpg" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex flex-col-reverse items-stretch justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-5 sm:flex-row sm:items-center sm:px-7 sm:py-6 lg:px-8">

                        <!-- Tombol Batal -->
                        <a href="{{ route('dashboard.narapidana') }}" class="inline-flex min-h-[48px] w-full items-center justify-center rounded-xl border border-slate-300 bg-red-500 px-6 py-3 text-center text-sm font-bold text-white shadow-sm transition hover:bg-red-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-red-200 sm:w-auto sm:text-base">
                            Batal
                        </a>

                        <!-- Tombol Simpan -->
                        <button type="submit" class="inline-flex min-h-[48px] w-full items-center justify-center rounded-xl bg-green-700 px-8 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-green-800 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-green-200 sm:w-auto sm:text-base">
                            <svg class="mr-2 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <!-- MODAL POP-UP NOTIFIKASI -->
        <div x-show="showAlert" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/65 px-4 py-6 backdrop-blur-sm transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>

            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xl transition-all sm:p-8" x-transition:enter="ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                @if(session('success'))
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 ring-1 ring-inset ring-emerald-100">
                        <svg class="h-10 w-10 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-2xl font-bold text-slate-900">Berhasil!</h3>
                    <p class="mb-6 text-base leading-relaxed text-slate-600">{{ session('success') }}</p>
                @elseif($errors->any())
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-red-50 ring-1 ring-inset ring-red-100">
                        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h3 class="mb-3 text-2xl font-bold text-slate-900">Mohon Maaf, Gagal!</h3>
                    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-left text-sm leading-relaxed text-red-700">
                        <ul class="list-inside list-disc space-y-1.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button @click="showAlert = false" class="min-h-[48px] w-full rounded-xl bg-blue-900 px-4 py-3 font-bold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    header:has(.bapas-narapidana-header) {
        background-color: #f1f5f9 !important;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: none !important;
    }
    .bapas-narapidana-header {
        background-color: #f1f5f9;
    }
    [x-cloak] {
        display: none !important;
    }
</style>
