<x-app-layout>
    <x-slot name="header">
        <div class="bapas-narapidana-header flex items-center gap-4 bg-slate-100">
            <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A9.004 9.004 0 0112 15c2.133 0 4.094.742 5.637 1.982M15 11a3 3 0 11-6 0 3 3 0 016 0zm6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                Dashboard, Selamat Datang <span class="font-bold">{{ Auth::user()->nama }}</span>!
            </h2>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-6 sm:py-10" x-data="{ showModal: false, imgSrc: '', showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:space-y-8 sm:px-6 lg:px-8">

            <!-- BAGIAN FORM LAPORAN ABSENSI -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-900 to-blue-900 px-5 py-5 sm:px-7 sm:py-6">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white ring-1 ring-inset ring-white/20">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold leading-snug text-white sm:text-xl">
                        Buat Absensi/Laporan Wajib Baru
                    </h3>
                </div>

                <div class="p-5 sm:p-7 lg:p-8">
                    @if(empty($pembimbingSaya))
                        <!-- Peringatan Jika Klien Belum Di-assign PK -->
                        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm">
                            <div class="flex items-start gap-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </span>
                                <div>
                                    <h4 class="text-base font-bold text-red-800">Anda Belum Memiliki PK Pembimbing!</h4>
                                    <p class="mt-1 text-sm text-red-700 leading-relaxed">Admin belum menetapkan Pengawas Kemasyarakatan (PK) untuk Anda. Anda tidak dapat melakukan laporan absen wajib sebelum Admin menghubungkan akun Anda dengan PK. Silakan hubungi Admin Bapas.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Form Laporan Absensi Normal -->
                        <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6">
                                <div>
                                    <label for="tanggal" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Tanggal Kegiatan</label>
                                    <input id="tanggal" name="tanggal" type="date" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-base text-slate-900 shadow-sm transition placeholder:text-slate-400 hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700" required max="{{ date('Y-m-d') }}" value="{{ old('tanggal', date('Y-m-d')) }}" />
                                </div>

                                <div>
                                    <label for="jenis_kegiatan" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Nama Kegiatan</label>
                                    <input id="jenis_kegiatan" name="jenis_kegiatan" type="text" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-base text-slate-900 shadow-sm transition placeholder:text-slate-400 hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700" required placeholder="Contoh: Membersihkan selokan" value="{{ old('jenis_kegiatan') }}" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Penanggung Jawab PK/Pengawas Anda</label>

                                    <!-- Field Read-Only karena PK sudah diatur dari Admin -->
                                    <div class="flex items-center gap-3 min-h-[48px] w-full rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-base shadow-sm">
                                        <svg class="h-6 w-6 text-blue-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <div class="min-w-0">
                                            <span class="block text-blue-950 font-bold truncate">{{ $pembimbingSaya->nama }}</span>
                                            <span class="block text-blue-700 text-xs font-medium truncate">NRP/NIP: {{ $pembimbingSaya->nomor_induk }}</span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-red-500 italic">*Laporan ini akan secara otomatis dikirimkan ke PK/Pengawas Anda.</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="bukti_file" class="mb-2 block text-sm font-bold text-slate-800 sm:text-base">Unggah Bukti Foto (Maksimal 10MB)</label>
                                <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-3 transition hover:border-blue-400 hover:bg-blue-50/60 sm:p-4">
                                    <input id="bukti_file" name="bukti_file" type="file" class="block w-full cursor-pointer rounded-xl bg-white text-sm text-slate-600 shadow-sm ring-1 ring-inset ring-slate-200 file:mr-3 file:h-12 file:cursor-pointer file:border-0 file:bg-blue-900 file:px-5 file:py-3 file:text-sm file:font-bold file:text-white hover:file:bg-blue-800 sm:text-base sm:file:mr-4 sm:file:px-6 sm:file:text-base" accept="image/jpeg, image/png, image/jpg" required />
                                </div>
                            </div>

                            <div class="mt-7 flex justify-end sm:mt-8">
                                <button type="submit" class="inline-flex min-h-[48px] w-full items-center justify-center rounded-xl bg-emerald-700 px-7 py-3.5 text-base font-bold text-white shadow-sm transition hover:bg-emerald-800 hover:shadow-md focus:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto sm:text-lg">
                                    <svg class="mr-2 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Kirim
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-5 border-b border-slate-200 bg-white px-5 py-5 sm:px-7 sm:py-6 xl:flex-row xl:items-center xl:justify-between">
                    <h3 class="flex items-center gap-4 text-lg font-bold leading-snug text-slate-900 sm:text-xl">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </span>
                        Kalender Kegiatan Absensi/Laporan Wajib
                    </h3>

                    <div class="flex w-full flex-col items-stretch gap-3 sm:flex-row sm:items-center xl:w-auto">
                        <form method="GET" action="{{ route('dashboard.narapidana') }}" class="m-0 flex min-h-[44px] w-full items-center overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm transition focus-within:border-blue-700 focus-within:ring-4 focus-within:ring-blue-100 sm:w-auto">
                            <label for="yearFilter" class="sr-only">Pilih Tahun</label>
                            <div class="flex self-stretch items-center justify-center border-r border-slate-200 bg-slate-50 px-3 text-slate-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                            </div>
                            <select id="yearFilter" name="year" onchange="this.form.submit()" class="block min-h-[44px] w-full cursor-pointer border-0 py-2.5 pl-3 pr-9 text-sm font-bold text-slate-800 focus:ring-0 sm:w-auto">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                                @endforeach
                            </select>
                        </form>

                        <span class="inline-flex min-h-[44px] w-full items-center justify-center rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-center text-sm font-semibold text-amber-800 sm:w-auto">
                            Geser tabel ke kiri/kanan &rarr;
                        </span>
                    </div>
                </div>

                @php
                    $bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $currentYear = $selectedYear;

                    $absensiMap = [];
                    foreach($riwayat as $rekam) {
                        $tgl = \Carbon\Carbon::parse($rekam->tanggal_waktu);
                        $absensiMap[$tgl->month][$tgl->day] = $rekam->bukti_file;
                    }
                @endphp

                <div class="p-4 sm:p-6 lg:p-8">
                    <div class="custom-scrollbar overflow-x-auto pb-5 snap-x snap-mandatory">
                        <div class="flex min-w-max gap-4 px-1 pb-1">
                            @for($m = 1; $m <= 12; $m++)
                                <article class="w-64 shrink-0 snap-start overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md sm:w-72">
                                    <h4 class="border-b border-slate-200 bg-slate-50 px-4 py-3.5 text-center text-base font-bold text-slate-800">
                                        {{ $bulans[$m-1] }} ({{ $currentYear }})
                                    </h4>

                                    <div class="grid grid-cols-5 gap-2 p-4">
                                        @php $daysInMonth = \Carbon\Carbon::create($currentYear, $m)->daysInMonth; @endphp
                                        @for($d = 1; $d <= $daysInMonth; $d++)
                                            @if(isset($absensiMap[$m][$d]))
                                                <button type="button" @click.prevent="showModal = true; imgSrc = '{{ asset('storage/' . $absensiMap[$m][$d]) }}'" title="Lihat foto tanggal {{ $d }} {{ $bulans[$m-1] }} {{ $currentYear }}" class="group relative block aspect-square w-full overflow-hidden rounded-lg border-2 border-emerald-500 bg-emerald-50 shadow-sm transition hover:scale-105 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-200">
                                                    <img src="{{ asset('storage/' . $absensiMap[$m][$d]) }}" alt="Bukti" class="h-full w-full object-cover transition duration-300 group-hover:scale-110">
                                                    <span class="absolute inset-0 ring-1 ring-inset ring-black/5"></span>
                                                </button>
                                            @else
                                                <div class="flex aspect-square w-full items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-sm font-semibold text-slate-500 sm:text-base" title="Tanggal {{ $d }}">{{ $d }}</div>
                                            @endif
                                        @endfor
                                    </div>
                                </article>
                            @endfor
                        </div>
                    </div>
                </div>
            </section>

            <section id="riwayat-absensi" class="scroll-mt-24 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-5 border-b border-slate-200 bg-white px-5 py-5 sm:px-7 sm:py-6 lg:flex-row lg:items-center lg:justify-between">
                    <h3 class="flex items-center gap-4 text-lg font-bold leading-snug text-slate-900 sm:text-xl">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </span>

                        <span>
                            Riwayat Absensi/Laporan Wajib
                            <span class="mt-1 block text-sm font-semibold text-slate-500">
                                Tahun {{ $selectedYear }}
                            </span>
                        </span>
                    </h3>

                    <form method="GET" action="{{ route('dashboard.narapidana') }}#riwayat-absensi" class="m-0 flex min-h-[44px] w-full items-center overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm transition focus-within:border-blue-700 focus-within:ring-4 focus-within:ring-blue-100 lg:w-auto">
                        <label for="yearFilterRiwayat" class="sr-only">Pilih Tahun Riwayat</label>

                        <div class="flex self-stretch items-center justify-center border-r border-slate-200 bg-slate-50 px-3 text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                        </div>

                        <select id="yearFilterRiwayat" name="year" onchange="this.form.submit()" class="block min-h-[44px] w-full cursor-pointer border-0 bg-white py-2.5 pl-3 pr-9 text-sm font-bold text-slate-800 focus:ring-0 lg:w-auto">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    Tahun {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="custom-scrollbar overflow-x-auto">
                    <table class="w-full min-w-[850px] text-left text-slate-700">
                        <thead class="border-b border-slate-200 bg-slate-100">
                            <tr>
                                <th scope="col" class="w-40 px-6 py-4 text-sm font-bold uppercase tracking-wide text-slate-700">Tanggal</th>
                                <th scope="col" class="px-6 py-4 text-sm font-bold uppercase tracking-wide text-slate-700">Nama Kegiatan</th>
                                <th scope="col" class="w-56 px-6 py-4 text-sm font-bold uppercase tracking-wide text-slate-700">PK/Pengawas</th>
                                <th scope="col" class="w-36 px-6 py-4 text-center text-sm font-bold uppercase tracking-wide text-slate-700">Bukti Foto</th>
                                <th scope="col" class="w-36 px-6 py-4 text-center text-sm font-bold uppercase tracking-wide text-slate-700">Pengaturan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-base">
                            @forelse($riwayat as $item)
                                <tr class="transition hover:bg-blue-50/60">
                                    <td class="whitespace-nowrap px-6 py-5 font-bold text-slate-900">
                                        {{ \Carbon\Carbon::parse($item->tanggal_waktu)->format('d M Y') }}
                                    </td>
                                    <td class="min-w-[200px] px-6 py-5 leading-relaxed text-slate-700">
                                        {{ $item->jenis_kegiatan }}
                                    </td>
                                    <td class="px-6 py-5 text-slate-700 font-medium">
                                        @if($item->pengawas)
                                            <span class="text-blue-900 font-bold block">{{ $item->pengawas->nama }}</span>
                                            <span class="text-xs text-slate-500 bg-slate-200 px-2 py-0.5 rounded mt-1 inline-block">NIP: {{ $item->pengawas->nomor_induk }}</span>
                                        @else
                                            <span class="text-amber-600 italic text-sm font-semibold">Belum Dipilih</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <button type="button" @click.prevent="showModal = true; imgSrc = '{{ asset('storage/' . $item->bukti_file) }}'" class="inline-flex min-h-[40px] w-full items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-800 transition hover:border-blue-300 hover:bg-blue-100 focus:outline-none focus:ring-4 focus:ring-blue-100">
                                            Lihat Foto
                                        </button>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <a href="{{ route('absensi.edit', $item->id) }}" class="inline-flex min-h-[40px] w-full items-center justify-center rounded-lg border border-amber-300 bg-amber-500 px-4 py-2 text-center text-sm font-bold text-white shadow-sm transition hover:border-amber-500 hover:bg-amber-600 focus:outline-none focus:ring-4 focus:ring-amber-100">
                                            Edit Data
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center text-lg font-medium leading-relaxed text-slate-500">
                                        Belum ada riwayat kegiatan di tahun {{ $selectedYear }}. <br> Silakan buat laporan baru melalui formulir di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/95 px-4 py-6 backdrop-blur-md transition-opacity sm:px-6" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showModal = false" class="absolute inset-0 cursor-pointer"></div>

            <div class="relative z-10 flex w-full max-w-5xl flex-col items-center justify-center">
                <button @click="showModal = false" class="mb-4 inline-flex min-h-[44px] items-center justify-center rounded-xl border border-red-300 bg-red-500 px-5 py-2.5 font-bold text-white shadow-lg backdrop-blur-md transition hover:border-red-400 hover:bg-red-300 focus:outline-none focus:ring-4 focus:ring-red/20">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Tutup Foto
                </button>

                <div class="flex max-h-[82vh] w-full items-center justify-center">
                    <img :src="imgSrc" class="max-h-[82vh] max-w-full rounded-2xl object-contain shadow-[0_24px_80px_rgba(0,0,0,0.55)]">
                </div>
            </div>
        </div>

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
                    Tutup Peringatan
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

    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #94a3b8 #e2e8f0;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #e2e8f0;
        border-radius: 9999px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border: 2px solid #e2e8f0;
        border-radius: 9999px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>
