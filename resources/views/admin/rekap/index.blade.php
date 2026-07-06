<x-app-layout>

    <div x-data="{ sidebarOpen: false, showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }"
         @toggle-admin-sidebar.window="sidebarOpen = !sidebarOpen"
         class="flex min-h-[calc(100vh-73px)] items-stretch bg-slate-100 relative">

        @include('admin.layouts.sidebar')

        <main class="flex-1 flex flex-col min-w-0 p-4 sm:p-6 lg:p-8 overflow-y-auto">

            <div class="bapas-admin-header flex items-center gap-4 bg-slate-100 mb-6 sm:mb-8">
                <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z"></path></svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-slate-900 leading-tight tracking-tight">
                        Rekap Data & Distribusi Klien
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Pantau jumlah klien masing-masing PK dan hubungkan klien ke PK/Pengawas.</p>
                </div>
            </div>

            <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center shadow-sm rounded-xl mb-6">
                <button @click="sidebarOpen = true" class="text-slate-600 hover:text-indigo-700 focus:outline-none"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                <span class="ml-4 font-bold text-slate-800 text-lg">Menu Admin</span>
            </div>

            <!-- BAGIAN 1: TABEL REKAPAN PK -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col mb-8">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-800">Tabel Rekap Jumlah Klien per PK</h3>
                </div>

                <div class="overflow-x-auto custom-scrollbar flex-1">
                    <table class="w-full text-left text-slate-700 min-w-[750px]">
                        <thead class="bg-slate-100 border-b border-slate-200 text-xs font-bold uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center text-slate-500">No</th>
                                <th class="px-6 py-4 text-slate-700">Nama PK/Pengawas</th>
                                <th class="px-6 py-4 text-slate-700">Nomor Induk (NRP/NIP)</th>
                                <th class="px-6 py-4 text-center text-slate-700">Jumlah Klien Diawasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($daftarPk as $pk)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-center font-semibold text-slate-500">{{ $daftarPk->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $pk->nama }}</td>
                                <td class="px-6 py-4 font-medium text-slate-600">{{ $pk->nomor_induk }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] h-[30px] rounded-lg {{ $pk->klien_bimbingan_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500' }} font-bold text-sm">
                                        {{ $pk->klien_bimbingan_count }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-10 text-slate-500 font-medium">
                                    Belum ada data PK terdaftar.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($daftarPk->hasPages())
                    <div class="border-t border-slate-200 bg-slate-50 px-6 py-4">
                        {{ $daftarPk->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            <!-- BAGIAN 2: FORM HUBUNGKAN PK DENGAN KLIEN -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="border-b border-slate-200 bg-indigo-900 px-6 py-5 flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white ring-1 ring-inset ring-white/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"></path></svg>
                    </span>
                    <div>
                        <h3 class="font-bold text-lg text-white">Hubungkan Klien dengan PK/Pengawas</h3>
                        <p class="text-indigo-200 text-xs mt-0.5">Pilih satu PK, lalu pilih banyak Klien sekaligus untuk didistribusikan.</p>
                    </div>
                </div>

                <form action="{{ route('admin.rekap.hubungkan') }}" method="POST" class="p-6 sm:p-8">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <!-- Pilihan PK -->
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                            <label class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="bg-indigo-200 text-indigo-800 w-6 h-6 rounded-full flex items-center justify-center text-xs">1</span>
                                Pilih PK/Pengawas
                            </label>
                            <select name="pk_id" required class="block w-full rounded-xl border-slate-300 bg-white py-3 px-4 text-sm text-slate-900 shadow-sm transition hover:border-indigo-400 focus:border-indigo-600 focus:ring-indigo-600 cursor-pointer">
                                <option value="" disabled selected>-- Klik untuk memilih 1 PK Pembimbing --</option>
                                @foreach($semuaPk as $pembimbing)
                                    <option value="{{ $pembimbing->id }}">{{ $pembimbing->nama }} (NIP: {{ $pembimbing->nomor_induk }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-3 italic">*PK yang dipilih akan menjadi pembimbing bagi klien-klien di sebelah kanan.</p>
                        </div>

                        <!-- Pilihan Klien (Multiple) -->
                        <div class="bg-emerald-50 p-5 rounded-2xl border border-emerald-200">
                            <label class="text-sm font-bold text-emerald-900 mb-3 flex items-center gap-2">
                                <span class="bg-emerald-200 text-emerald-800 w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span>
                                Pilih Klien/Narapidana (Bisa lebih dari satu)
                            </label>

                            <!-- Bantuan Instruksi Windows/Mac -->
                            <p class="text-[11px] text-yellow-700 font-semibold mb-2 bg-yellow-100 p-2 rounded-lg">
                                Tips: Tahan tombol <kbd class="bg-white border px-1 rounded shadow-sm">Ctrl</kbd> (Windows) atau <kbd class="bg-white border px-1 rounded shadow-sm">Cmd ⌘</kbd> (Apple MacBook) sambil mengklik nama klien untuk memilih banyak sekaligus.
                            </p>

                            <select name="klien_ids[]" multiple required size="8" class="block w-full rounded-xl border-emerald-300 bg-white py-2 px-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-600 focus:ring-emerald-600 custom-scrollbar outline-none">
                                @foreach($semuaKlien as $klien)
                                    <option value="{{ $klien->id }}" class="py-2 px-3 rounded-lg mb-1 border border-transparent hover:bg-emerald-50 focus:bg-emerald-100 cursor-pointer">
                                        {{ $klien->nama }}
                                        @if($klien->pembimbing_id)
                                            (Saat ini di bawah: {{ $klien->pembimbing->nama ?? 'PK Lain' }})
                                        @else
                                            (Belum ada PK)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end pt-5 border-t border-slate-100">
                        <button type="submit" class="inline-flex min-h-[50px] items-center justify-center gap-2 rounded-xl bg-indigo-700 px-8 py-3 font-bold text-white shadow-md transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"></path></svg>
                            Hubungkan PK dengan Klien
                        </button>
                    </div>
                </form>
            </div>

        </main>

        <!-- Pop-up Notifikasi -->
        @if(session('success') || $errors->any())
        <div x-cloak x-show="showAlert" class="fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/65 px-4 backdrop-blur-sm">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 sm:p-8 text-center shadow-2xl">
                @if(session('success'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg></div>
                    <h3 class="font-bold text-xl mb-2 text-slate-900">Operasi Berhasil!</h3>
                    <p class="text-sm text-slate-600 mb-5 leading-relaxed">{{ session('success') }}</p>
                @elseif($errors->any())
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 mb-3">Terjadi Kendala</h3>
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
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
