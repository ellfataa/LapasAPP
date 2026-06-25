<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl md:text-3xl text-blue-900 leading-tight">
            Dashboard {{ Auth::user()->nama }}
        </h2>
    </x-slot>

    <!-- Menambahkan state 'showAlert' yang otomatis true jika ada session success atau error validasi -->
    <div class="py-6 sm:py-10 bg-white min-h-screen" x-data="{ showModal: false, imgSrc: '', showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- BLOK 1: FORM INPUT ABSENSI -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100">
                <div class="bg-blue-50 px-6 py-5 border-b border-blue-100">
                    <h3 class="text-xl font-bold text-blue-900 flex items-center">
                        <!-- Icon SVG: Document/Edit -->
                        <svg class="w-7 h-7 mr-3 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Buat Absensi/Laporan Wajib Baru
                    </h3>
                </div>

                <div class="p-6 md:p-8">
                    <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tanggal" class="block text-base font-bold text-gray-800 mb-2">Tanggal Kegiatan</label>
                                <input id="tanggal" name="tanggal" type="date" class="block w-full px-4 py-3 text-base border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm" required max="{{ date('Y-m-d') }}" value="{{ old('tanggal', date('Y-m-d')) }}" />
                            </div>

                            <div>
                                <label for="jenis_kegiatan" class="block text-base font-bold text-gray-800 mb-2">Nama Kegiatan</label>
                                <input id="jenis_kegiatan" name="jenis_kegiatan" type="text" class="block w-full px-4 py-3 text-base border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm" required placeholder="Contoh: Membersihkan selokan" value="{{ old('jenis_kegiatan') }}" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="bukti_file" class="block text-base font-bold text-gray-800 mb-2">Unggah Bukti Foto (Maksimal 10MB)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50 hover:bg-blue-50 transition-colors">
                                <input id="bukti_file" name="bukti_file" type="file" class="block w-full text-base text-gray-700 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-base file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" accept="image/jpeg, image/png, image/jpg" required />
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full md:w-auto px-8 py-3.5 bg-green-600 rounded-lg font-bold text-white text-lg hover:bg-green-700 focus:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all shadow-md flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Kirim Laporan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- BLOK 2: PETA KALENDER -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100">
                <div class="bg-blue-50 px-6 py-5 border-b border-blue-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-xl font-bold text-blue-900 flex items-center">
                        <!-- Icon SVG: Calendar -->
                        <svg class="w-7 h-7 mr-3 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Kalender Kegiatan Absensi/Laporan Wajib
                    </h3>
                    <span class="text-sm text-yellow-800 font-semibold bg-yellow-100 px-3 py-1.5 rounded-md border border-yellow-200">
                        Geser tabel ke kiri/kanan &rarr;
                    </span>
                </div>

                @php
                    $bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $currentYear = date('Y');

                    $absensiMap = [];
                    foreach($riwayat as $rekam) {
                        $tgl = \Carbon\Carbon::parse($rekam->tanggal_waktu);
                        $absensiMap[$tgl->month][$tgl->day] = $rekam->bukti_file;
                    }
                @endphp

                <div class="p-5 md:p-8">
                    <div class="overflow-x-auto pb-6 snap-x custom-scrollbar">
                        <div class="flex space-x-4 min-w-max px-1">
                            @for($m = 1; $m <= 12; $m++)
                                <div class="border-2 border-gray-100 rounded-xl p-4 bg-white shadow-sm w-64 shrink-0 snap-start">
                                    <h4 class="text-center font-bold text-base text-gray-800 mb-4 border-b pb-2">
                                        {{ $bulans[$m-1] }} ({{ $currentYear }})
                                    </h4>
                                    <div class="grid grid-cols-5 gap-2">
                                        @php $daysInMonth = \Carbon\Carbon::create($currentYear, $m)->daysInMonth; @endphp
                                        @for($d = 1; $d <= $daysInMonth; $d++)
                                            @if(isset($absensiMap[$m][$d]))
                                                <!-- Kotak Ada Foto -->
                                                <button type="button" @click.prevent="showModal = true; imgSrc = '{{ asset('storage/' . $absensiMap[$m][$d]) }}'" title="Lihat foto tanggal {{ $d }} {{ $bulans[$m-1] }} {{ $currentYear }}" class="block w-full aspect-square border-2 border-green-500 rounded-md overflow-hidden hover:scale-105 hover:shadow-lg transition-all focus:outline-none focus:ring-4 focus:ring-green-300">
                                                    <img src="{{ asset('storage/' . $absensiMap[$m][$d]) }}" alt="Bukti" class="object-cover w-full h-full">
                                                </button>
                                            @else
                                                <!-- Kotak Kosong -->
                                                <div class="w-full aspect-square border border-gray-200 bg-gray-50 rounded-md flex items-center justify-center text-gray-500 font-semibold text-sm sm:text-base" title="Tanggal {{ $d }}">{{ $d }}</div>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOK 3: TABEL LOGBOOK -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100">
                <div class="bg-blue-50 px-6 py-5 border-b border-blue-100">
                    <h3 class="text-xl font-bold text-blue-900 flex items-center">
                        <!-- Icon SVG: Clipboard/List -->
                        <svg class="w-7 h-7 mr-3 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Riwayat Absensi/Laporan Wajib Saya
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-700">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 w-40 font-bold text-gray-800">Tanggal</th>
                                <th scope="col" class="px-6 py-4 font-bold text-gray-800">Nama Kegiatan</th>
                                <th scope="col" class="px-6 py-4 w-32 text-center font-bold text-gray-800">Bukti Foto</th>
                                <th scope="col" class="px-6 py-4 w-32 text-center font-bold text-gray-800">Pengaturan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-base">
                            @forelse($riwayat as $item)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-5 font-bold text-gray-900 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->tanggal_waktu)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-5 min-w-[200px] leading-relaxed">
                                        {{ $item->jenis_kegiatan }}
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <button type="button" @click.prevent="showModal = true; imgSrc = '{{ asset('storage/' . $item->bukti_file) }}'" class="inline-block bg-blue-100 text-blue-700 hover:bg-blue-200 font-bold px-4 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400 w-full text-sm">
                                            Lihat Foto
                                        </button>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <a href="{{ route('absensi.edit', $item->id) }}" class="inline-block bg-yellow-500 border-2 border-yellow-300 text-white hover:border-yellow-400 hover:bg-yellow-600 font-bold px-4 py-2 rounded-lg transition-colors w-full text-sm text-center">
                                            Edit Data
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 font-medium text-lg">
                                        Belum ada riwayat kegiatan. <br> Silakan buat laporan baru melalui formulir di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- MODAL POP-UP GAMBAR FOTO -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-90 backdrop-blur-sm px-4 transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-3xl flex justify-center flex-col items-center">
                <button @click="showModal = false" class="mb-4 bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-full font-bold shadow-lg flex items-center transition-colors focus:outline-none focus:ring-4 focus:ring-red-300">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Tutup Foto
                </button>
                <img :src="imgSrc" class="max-w-full max-h-[75vh] rounded-xl shadow-2xl object-contain border-4 border-white bg-white">
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

<style>
    .custom-scrollbar::-webkit-scrollbar {
        height: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 8px;
        border: 2px solid #f1f5f9;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>
