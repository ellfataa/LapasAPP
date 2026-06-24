<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Petugas (Admin & Pengawas)') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showModal: false, imgSrc: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Daftar Seluruh Laporan Kegiatan Narapidana</h3>
                    <div class="text-sm text-gray-500">
                        Total Laporan: {{ $semuaAbsensi->count() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 w-40">Tanggal Kegiatan</th>
                                <th class="px-4 py-3 w-64">Nama & NRP</th>
                                <th class="px-4 py-3">Jenis Kegiatan Sosial</th>
                                <th class="px-4 py-3 w-32 text-center">Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semuaAbsensi as $item)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($item->tanggal_waktu)->format('d F Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-semibold text-gray-900">{{ $item->narapidana->nama }}</span><br>
                                        <span class="text-xs">{{ $item->narapidana->nomor_induk }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $item->jenis_kegiatan }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click.prevent="showModal = true; imgSrc = '{{ asset('storage/' . $item->bukti_file) }}'" class="text-blue-600 hover:text-blue-800 underline font-medium text-xs focus:outline-none">Lihat Foto</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada laporan absensi yang masuk dari narapidana.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80 backdrop-blur-sm px-4 transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div @click="showModal = false" class="absolute inset-0 cursor-pointer"></div>

            <div class="relative z-10 w-full max-w-4xl flex justify-center">
                <button @click="showModal = false" class="absolute -top-12 right-0 text-white hover:text-red-400 focus:outline-none">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <img :src="imgSrc" class="max-w-full max-h-[85vh] rounded-lg shadow-2xl object-contain border border-gray-700">
            </div>
        </div>
    </div>
</x-app-layout>
