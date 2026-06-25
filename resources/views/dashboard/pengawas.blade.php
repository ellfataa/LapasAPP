<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl md:text-3xl text-black leading-tight flex items-center">
            Dashboard, Selamat Datang PK {{ Auth::user()->nama }}!
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10 bg-white min-h-screen" x-data="{ showModal: false, imgSrc: '', showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100">
                <div class="bg-blue-50 px-6 py-5 border-b border-blue-100 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-blue-900 flex items-center">
                        <svg class="w-7 h-7 mr-3 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Formulir Penilaian Kinerja PK
                    </h3>
                </div>

                <div class="p-6 md:p-8">
                    <form action="{{ route('kinerja-pk.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="bulan_kinerja" class="block text-base font-bold text-gray-800 mb-2">Periode Bulan</label>
                                <select id="bulan_kinerja" name="bulan" required class="block w-full px-4 py-3 text-base font-medium text-gray-700 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm cursor-pointer bg-white">
                                    @php $bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                                    @foreach($bulans as $index => $namaBulan)
                                        <option value="{{ $index + 1 }}" {{ date('n') == ($index + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="tahun_kinerja" class="block text-base font-bold text-gray-800 mb-2">Periode Tahun</label>
                                <input type="number" id="tahun_kinerja" name="tahun" value="{{ date('Y') }}" required class="block w-full px-4 py-3 text-base border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                            </div>
                        </div>

                        <div x-data="{
                            data: {
                                litmas: { kuota: 12, berhasil: '' },
                                pendampingan: { kuota: '', berhasil: '' },
                                pembimbingan: { kuota: '', berhasil: '' },
                                pengawasan: { kuota: '', berhasil: '' }
                            },
                            calc(kuota, berhasil) {
                                let k = parseFloat(kuota);
                                let b = parseFloat(berhasil);
                                if (k > 0 && !isNaN(b)) return ((b / k) * 100);
                                return 0;
                            },
                            get rataRata() {
                                let total = 0;
                                total += this.calc(this.data.litmas.kuota, this.data.litmas.berhasil);
                                total += this.calc(this.data.pendampingan.kuota, this.data.pendampingan.berhasil);
                                total += this.calc(this.data.pembimbingan.kuota, this.data.pembimbingan.berhasil);
                                total += this.calc(this.data.pengawasan.kuota, this.data.pengawasan.berhasil);
                                return (total / 4).toFixed(1);
                            },
                            get predikat() {
                                let rata = parseFloat(this.rataRata);
                                if(rata >= 91) return 'Sangat Baik';
                                if(rata >= 81) return 'Baik';
                                if(rata >= 70) return 'Cukup';
                                if(rata >= 60) return 'Kurang';
                                return 'Sangat Kurang';
                            }
                        }">

                            @php
                                $kategoriPenilaian = [
                                    'litmas' => 'Penelitian Kemasyarakatan (Litmas)',
                                    'pendampingan' => 'Pendampingan',
                                    'pembimbingan' => 'Pembimbingan',
                                    'pengawasan' => 'Pengawasan'
                                ];
                            @endphp

                            <div class="space-y-6 mb-8">
                                @foreach($kategoriPenilaian as $key => $label)
                                    <div class="p-6 bg-gray-50 border border-gray-200 rounded-xl hover:border-blue-300 transition-colors">
                                        <h4 class="font-bold text-lg text-blue-900 mb-4 border-b border-gray-200 pb-2">
                                            {{ $loop->iteration }}. {{ $label }}
                                        </h4>
                                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 items-start">

                                            <div>
                                                <label for="{{ $key }}_kuota" class="block text-sm font-bold text-gray-800 mb-2">Kuota Beban per Bulan</label>
                                                @if($key === 'litmas')
                                                    <input type="number" id="{{ $key }}_kuota" name="{{ $key }}_kuota" x-model.number="data.{{ $key }}.kuota" readonly class="block w-full px-4 py-2 text-base border-gray-300 bg-gray-200 text-gray-700 cursor-not-allowed rounded-lg shadow-sm">
                                                    <p class="text-[11px] text-gray-500 mt-1 font-medium">*Kuota tetap (Fixed)</p>
                                                @else
                                                    <input type="number" id="{{ $key }}_kuota" name="{{ $key }}_kuota" x-model.number="data.{{ $key }}.kuota" min="0" required class="block w-full px-4 py-2 text-base border-gray-300 focus:border-blue-500 rounded-lg shadow-sm">
                                                @endif
                                            </div>

                                            <div>
                                                <label for="{{ $key }}_berhasil" class="block text-sm font-bold text-gray-800 mb-2">Berhasil Dilaksanakan</label>
                                                <input type="number" id="{{ $key }}_berhasil" name="{{ $key }}_berhasil" x-model.number="data.{{ $key }}.berhasil" min="0" required class="block w-full px-4 py-2 text-base border-gray-300 focus:border-blue-500 rounded-lg shadow-sm">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-bold text-gray-800 mb-2">Kalkulasi</label>
                                                <div class="relative">
                                                    <input type="text" :value="calc(data.{{ $key }}.kuota, data.{{ $key }}.berhasil).toFixed(1)" readonly class="block w-full px-4 py-2 text-base border-gray-200 bg-gray-200 text-gray-700 font-bold rounded-lg shadow-sm cursor-not-allowed">
                                                    <span class="absolute right-3 top-2 text-gray-600 font-bold">%</span>
                                                </div>
                                            </div>

                                            <div>
                                                <label for="{{ $key }}_file" class="block text-sm font-bold text-gray-800 mb-2">Upload Bukti (Foto/File PDF)</label>
                                                <input type="file"
                                                    id="{{ $key }}_file"
                                                    name="{{ $key }}_file[]"
                                                    multiple
                                                    required
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    onchange="updateFileList('{{ $key }}', this)"
                                                    class="block w-full text-xs text-gray-700 file:mr-2 file:py-2.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-gray-300 rounded-lg bg-white">

                                                <div id="file-list-{{ $key }}" class="mt-3 text-xs text-blue-700 space-y-2"></div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-6 bg-blue-100 border border-blue-300 rounded-xl mb-6 flex flex-col md:flex-row justify-between items-center gap-5">
                                <div class="text-center md:text-left">
                                    <h4 class="text-blue-900 font-extrabold text-xl mb-1">Estimasi Kinerja Akhir</h4>
                                    <p class="text-sm text-blue-700 font-medium">Nilai dihitung secara otomatis berdasarkan rasio keberhasilan 4 kategori di atas.</p>
                                </div>
                                <div class="flex items-center gap-4 w-full md:w-auto">
                                    <div class="flex-1 md:flex-none text-center bg-white px-6 py-3 rounded-xl border border-blue-200 shadow-sm">
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Rata-Rata</span>
                                        <span class="font-extrabold text-2xl text-blue-700" x-text="rataRata + '%'"></span>
                                    </div>
                                    <div class="flex-1 md:flex-none text-center bg-white px-6 py-3 rounded-xl border border-blue-200 shadow-sm">
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Predikat</span>
                                        <span class="font-extrabold text-2xl"
                                              :class="{
                                                  'text-green-600': predikat === 'Sangat Baik',
                                                  'text-blue-600': predikat === 'Baik',
                                                  'text-yellow-500': predikat === 'Cukup',
                                                  'text-orange-500': predikat === 'Kurang',
                                                  'text-red-600': predikat === 'Sangat Kurang'
                                              }" x-text="predikat"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="w-full md:w-auto px-10 py-4 bg-green-600 rounded-xl font-bold text-white text-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all shadow-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    Simpan Laporan Kinerja
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 mt-8">
                <div class="bg-blue-50 px-6 py-5 border-b border-blue-100">
                    <h3 class="text-xl font-bold text-blue-900">Riwayat Penilaian Kinerja Saya</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-700">
                        <thead class="bg-gray-50 text-sm font-semibold text-gray-700 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 w-32">Periode</th>
                                <th class="px-6 py-4">Kategori & Bukti</th>
                                <th class="px-6 py-4 text-right w-32">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($riwayatKinerja as $kinerja)
                            @php
                                $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            @endphp
                            <tr class="hover:bg-gray-50 align-top transition-colors">
                                <td class="px-6 py-6 font-bold text-gray-900 text-base">
                                    {{ $namaBulan[$kinerja->bulan - 1] ?? $kinerja->bulan }} {{ $kinerja->tahun }}
                                    <div class="text-xs text-gray-500 font-normal mt-1">
                                        {{ $kinerja->bulan }}/{{ $kinerja->tahun }}
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="space-y-5">
                                        @foreach(['litmas', 'pendampingan', 'pembimbingan', 'pengawasan'] as $kat)
                                            <div>
                                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 flex items-center gap-2">
                                                    {{ $kat }}:
                                                    <span class="text-blue-600 normal-case text-sm font-semibold tracking-normal">
                                                        {{ $kinerja->{$kat.'_berhasil'} }}/{{ $kinerja->{$kat.'_kuota'} }}
                                                        ({{ number_format(($kinerja->{$kat.'_kuota'} > 0 ? ($kinerja->{$kat.'_berhasil'}/$kinerja->{$kat.'_kuota'})*100 : 0), 1) }}%)
                                                    </span>
                                                </p>
                                                <div class="flex flex-col gap-1 mb-1">
                                                    @php
                                                        $rawFile = $kinerja->{$kat.'_file'};
                                                        $files = json_decode($rawFile, true);
                                                        if (!is_array($files)) {
                                                            $files = $rawFile ? [$rawFile] : [];
                                                        }
                                                    @endphp

                                                    @forelse($files as $file)
                                                        @php
                                                            $filePath = is_array($file) ? ($file['path'] ?? '') : $file;
                                                            $fileName = is_array($file) ? ($file['name'] ?? basename($filePath)) : basename($filePath);
                                                        @endphp
                                                        @if($filePath)
                                                            <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                                               class="text-[13px] text-blue-600 hover:text-blue-800 hover:underline">
                                                                {{ $fileName }}
                                                            </a>
                                                        @endif
                                                    @empty
                                                        <span class="text-[12px] text-gray-400 italic">- Tidak ada file -</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-right font-bold text-lg">
                                    <span class="
                                        {{ $kinerja->predikat == 'Sangat Baik' ? 'text-green-600' : '' }}
                                        {{ $kinerja->predikat == 'Baik' ? 'text-blue-600' : '' }}
                                        {{ $kinerja->predikat == 'Cukup' ? 'text-yellow-500' : '' }}
                                        {{ $kinerja->predikat == 'Kurang' ? 'text-orange-500' : '' }}
                                        {{ $kinerja->predikat == 'Sangat Kurang' ? 'text-red-600' : '' }}
                                    ">
                                        {{ $kinerja->predikat }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-gray-500 font-medium text-base">
                                    Belum ada riwayat penilaian kinerja yang disimpan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100">
                <div class="bg-blue-50 px-6 py-5 border-b border-blue-100 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-blue-900 flex items-center">
                        <svg class="w-7 h-7 mr-3 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter Cari Absensi/Laporan Wajib Klien
                    </h3>
                </div>

                <div class="p-6 md:p-8">
                    <form method="GET" action="{{ route('dashboard.pengawas') }}" class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
                        <div class="md:col-span-5">
                            <label for="search" class="block text-base font-bold text-gray-800 mb-2">Cari Nama/Nomor Induk</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Ketik nama klien..." class="block w-full px-4 py-3 text-base border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                        </div>
                        <div class="md:col-span-3">
                            <label for="month" class="block text-base font-bold text-gray-800 mb-2">Bulan</label>
                            <select id="month" name="month" class="block w-full px-4 py-3 text-base font-medium text-gray-700 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm cursor-pointer bg-white">
                                <option value="">-- Semua Bulan</option>
                                @php $bulansFilter = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                                @foreach($bulansFilter as $index => $namaBulan)
                                    <option value="{{ $index + 1 }}" {{ request('month') == ($index + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="year" class="block text-base font-bold text-gray-800 mb-2">Tahun</label>
                            <select id="year" name="year" class="block w-full px-4 py-3 text-base font-medium text-gray-700 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm cursor-pointer bg-white">
                                <option value="">-- Semua Tahun</option>
                                @if(isset($availableYears) && count($availableYears) > 0)
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="md:col-span-2 flex flex-col gap-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow transition-colors flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-blue-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Cari
                            </button>
                            @if(request('search') || request('month') || request('year'))
                                <a href="{{ route('dashboard.pengawas') }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 font-bold py-2 px-4 rounded-lg transition-colors text-center text-sm">
                                    Reset Filter
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100">
                <div class="bg-blue-50 px-6 py-5 border-b border-blue-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="text-xl font-bold text-blue-900 flex items-center">
                        <svg class="w-7 h-7 mr-3 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Daftar Absensi/Laporan Wajib Klien
                    </h3>
                    <span class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-sm flex items-center">
                        Total: {{ $semuaAbsensi->count() }} Laporan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-700">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 w-48 font-bold text-gray-800">Tanggal Kegiatan</th>
                                <th scope="col" class="px-6 py-4 w-64 font-bold text-gray-800">Data Mantan Napi</th>
                                <th scope="col" class="px-6 py-4 font-bold text-gray-800">Jenis Kegiatan Sosial</th>
                                <th scope="col" class="px-6 py-4 w-32 text-center font-bold text-gray-800">Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-base">
                            @forelse($semuaAbsensi as $item)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-5 font-bold text-gray-900 whitespace-nowrap align-top">
                                        {{ \Carbon\Carbon::parse($item->tanggal_waktu)->format('d F Y') }}
                                    </td>
                                    <td class="px-6 py-5 align-top">
                                        <span class="font-bold text-blue-900 block text-lg">{{ $item->narapidana->nama }}</span>
                                        <span class="text-sm font-semibold text-gray-500 bg-gray-200 px-2 py-0.5 rounded mt-1 inline-block">NIK: {{ $item->narapidana->nomor_induk }}</span>
                                    </td>
                                    <td class="px-6 py-5 leading-relaxed align-top">
                                        {{ $item->jenis_kegiatan }}
                                    </td>
                                    <td class="px-6 py-5 text-center align-top">
                                        <button type="button" @click.prevent="showModal = true; imgSrc = '{{ asset('storage/' . $item->bukti_file) }}'" class="inline-block bg-blue-100 text-blue-700 hover:bg-blue-200 font-bold px-4 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400 w-full text-sm">
                                            Lihat Foto
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="font-medium text-lg block">Tidak ada laporan ditemukan</span>
                                        <span class="text-sm">Coba ubah filter pencarian Anda.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-90 backdrop-blur-sm px-4 transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showModal = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-4xl flex justify-center flex-col items-center">
                <button @click="showModal = false" class="mb-4 bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-full font-bold shadow-lg flex items-center transition-colors focus:outline-none focus:ring-4 focus:ring-red-300">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Tutup Foto
                </button>
                <img :src="imgSrc" class="max-w-full max-h-[80vh] rounded-xl shadow-2xl object-contain border-4 border-white bg-white">
            </div>
        </div>

        <div x-show="showAlert" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4 transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 md:p-8 text-center transform transition-all">
                @if(session('success'))
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-5">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Berhasil!</h3>
                    <p class="text-base text-gray-600 mb-6">{{ session('success') }}</p>
                @elseif($errors->any())
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-5">
                        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Mohon Maaf, Gagal!</h3>
                    <div class="text-sm text-red-700 text-left bg-red-50 p-4 rounded-lg mb-6 border border-red-100">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach(array_unique($errors->all()) as $error)
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

<script>
    function updateFileList(key, input) {
        const container = document.getElementById('file-list-' + key);
        container.innerHTML = '';

        if (input.files.length > 0) {
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const fileUrl = URL.createObjectURL(file);
                const fileDiv = document.createElement('div');
                fileDiv.className = 'flex items-center gap-1 mt-1';

                fileDiv.innerHTML = `
                    <span class="text-[12px] text-gray-400">📄</span>
                    <a href="${fileUrl}" target="_blank" class="text-[13px] text-blue-600 font-medium truncate hover:text-blue-800 hover:underline">
                        ${file.name}
                    </a>
                `;
                container.appendChild(fileDiv);
            }
        }
    }
</script>
