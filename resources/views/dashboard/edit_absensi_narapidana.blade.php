<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl md:text-3xl text-blue-900 leading-tight flex items-center">
            <!-- Icon SVG: Pencil/Edit -->
            <svg class="w-8 h-8 mr-3 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            {{ __('Edit Data Laporan') }}
        </h2>
    </x-slot>

    <!-- Menambahkan state 'showAlert' untuk Modal Pop-up Notifikasi -->
    <div class="py-6 sm:py-10 bg-white min-h-screen" x-data="{ showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100">

                <!-- Header Form -->
                <div class="bg-blue-50 px-6 py-5 border-b border-blue-100">
                    <p class="text-base text-blue-800 font-medium">
                        Silakan perbarui data atau ganti foto bukti kegiatan Anda di bawah ini.
                    </p>
                </div>

                <div class="p-6 md:p-8">
                    <form action="{{ route('absensi.update', $absensi->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Input Tanggal -->
                            <div>
                                <label for="tanggal" class="block text-base font-bold text-gray-800 mb-2">Tanggal Kegiatan</label>
                                <input id="tanggal" name="tanggal" type="date" class="block w-full px-4 py-3 text-base border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm" required max="{{ date('Y-m-d') }}" value="{{ \Carbon\Carbon::parse($absensi->tanggal_waktu)->format('Y-m-d') }}" />
                            </div>

                            <!-- Input Jenis Kegiatan -->
                            <div>
                                <label for="jenis_kegiatan" class="block text-base font-bold text-gray-800 mb-2">Nama Kegiatan</label>
                                <input id="jenis_kegiatan" name="jenis_kegiatan" type="text" class="block w-full px-4 py-3 text-base border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm" required value="{{ old('jenis_kegiatan', $absensi->jenis_kegiatan) }}" />
                            </div>

                            <!-- Area Ganti Foto -->
                            <div class="mt-6">
                                <label class="block text-base font-bold text-gray-800 mb-2">Bukti Foto Saat Ini</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 bg-gray-50 flex flex-col sm:flex-row items-center sm:items-start gap-6">

                                    <!-- Thumbnail Foto Lama -->
                                    <div class="shrink-0 w-32 h-32 sm:w-40 sm:h-40 overflow-hidden rounded-lg border-2 border-gray-200 shadow-sm bg-white">
                                        <img src="{{ asset('storage/' . $absensi->bukti_file) }}" alt="Foto Lama" class="object-cover w-full h-full">
                                    </div>

                                    <!-- Input File Baru -->
                                    <div class="flex-1 w-full text-center sm:text-left">
                                        <label for="bukti_file" class="block text-base font-bold text-gray-800 mb-1">Ganti Foto Baru (Opsional)</label>
                                        <!-- Update keterangan batas maksimal file menjadi 10MB -->
                                        <p class="text-sm text-gray-500 mb-4">Maksimal 10MB. Biarkan kosong jika tidak ingin mengubah foto yang sudah ada.</p>

                                        <input id="bukti_file" name="bukti_file" type="file" class="block w-full text-base text-gray-700 file:mr-4 file:py-2.5 file:px-5 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 cursor-pointer border border-gray-300 rounded-lg bg-white" accept="image/jpeg, image/png, image/jpg" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="mt-10 pt-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 sm:gap-4">
                            <!-- Tombol Batal -->
                            <a href="{{ route('dashboard.narapidana') }}" class="w-full sm:w-auto text-center px-6 py-3.5 bg-red-600 border-2 border-red-300 text-white font-bold text-lg rounded-lg hover:bg-red-700 hover:text-white focus:outline-none focus:ring-4 focus:ring-red-200 transition-all">
                                Batal
                            </a>

                            <!-- Tombol Simpan -->
                            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 rounded-lg font-bold text-white text-lg hover:bg-blue-700 focus:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all shadow-md flex items-center justify-center">
                                <!-- Icon SVG: Check/Save -->
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- MODAL POP-UP NOTIFIKASI (Berhasil / Gagal) -->
        <div x-show="showAlert" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4 transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 md:p-8 text-center transform transition-all" x-transition:enter="ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                @if(session('success'))
                    <!-- Tampilan Jika Sukses -->
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-5">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Berhasil!</h3>
                    <p class="text-base text-gray-600 mb-6">{{ session('success') }}</p>
                @elseif($errors->any())
                    <!-- Tampilan Jika Gagal/Error -->
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-5">
                        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Mohon Maaf, Gagal!</h3>
                    <div class="text-sm text-red-700 text-left bg-red-50 p-4 rounded-lg mb-6 border border-red-100">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button @click="showAlert = false" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors shadow-md">
                    Tutup Peringatan
                </button>
            </div>
        </div>

    </div>
</x-app-layout>
