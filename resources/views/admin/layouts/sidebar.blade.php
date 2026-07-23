<div x-cloak x-show="sidebarOpen" class="fixed inset-0 bg-slate-900/50 z-40 md:hidden backdrop-blur-sm transition-opacity" @click="sidebarOpen = false"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-slate-900 to-blue-950 text-blue-50 transition-transform duration-300 md:sticky md:top-0 md:left-0 md:translate-x-0 shadow-2xl flex flex-col border-r border-blue-900">

    <div class="px-6 py-5 flex items-center justify-between bg-black/10 border-b border-white/10">
        <h2 class="text-xl font-extrabold text-white tracking-widest uppercase flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            Panel Admin
        </h2>
        <button @click="sidebarOpen = false" class="md:hidden text-blue-300 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">

        <a href="{{ route('dashboard.admin') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard.admin') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-900/50' : 'hover:bg-white/10 hover:text-white font-medium' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard Utama
        </a>

        <div class="pt-5 pb-2">
            <p class="px-4 text-xs font-bold text-blue-300/80 uppercase tracking-widest">Manajemen Akun</p>
        </div>

        <a href="{{ route('admin.pengawas.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.pengawas.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-900/50' : 'hover:bg-white/10 hover:text-white font-medium' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Akun PK/Pembimbing
        </a>

        <a href="{{ route('admin.narapidana.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.narapidana.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-900/50' : 'hover:bg-white/10 hover:text-white font-medium' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Akun Klien/Narapidana
        </a>

        <!-- MENU REKAP BARU -->
        <a href="{{ route('admin.rekap.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.rekap.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-900/50' : 'hover:bg-white/10 hover:text-white font-medium' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z"></path></svg>
            Rekap Data PK & Klien
        </a>

        <div class="pt-5 pb-2">
            <p class="px-4 text-xs font-bold text-blue-300/80 uppercase tracking-widest">Laporan</p>
        </div>

        <a href="{{ route('admin.kinerja.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.kinerja.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-900/50' : 'hover:bg-white/10 hover:text-white font-medium' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Penilaian Kinerja PK
        </a>

        <a href="{{ route('admin.absensi.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.absensi.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-900/50' : 'hover:bg-white/10 hover:text-white font-medium' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Absensi/Laporan Wajib Klien
        </a>
    </nav>
</aside>
