<section>
    <header class="flex items-start gap-4 border-b border-slate-200 pb-5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
            </svg>
        </div>

        <div class="min-w-0">
            <h2 class="text-lg font-bold text-slate-900 sm:text-xl">Informasi Profil</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-600">Perbarui data diri dan Nomor Induk (NRP/NIP/NIK) akun Anda.</p>
        </div>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="nama" value="Nama Lengkap" class="mb-2 block text-sm font-bold text-slate-800" />

            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
                    </svg>
                </span>

                <x-text-input id="nama" name="nama" type="text" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white py-3 pl-12 pr-4 text-base text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700" :value="old('nama', $user->nama)" required autofocus />
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
        </div>

        <div>
            <x-input-label for="nomor_induk" value="Nomor Induk (NRP/NIP/NIK)" class="mb-2 block text-sm font-bold text-slate-800" />

            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 9h4m-4 4h7m3-4h.01m-.01 4h.01" />
                    </svg>
                </span>

                <x-text-input id="nomor_induk" name="nomor_induk" type="text" inputmode="numeric" maxlength="18" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white py-3 pl-12 pr-4 text-base text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700" :value="old('nomor_induk', $user->nomor_induk)" required />
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('nomor_induk')" />
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center">
            <x-primary-button class="inline-flex min-h-[48px] w-full items-center justify-center rounded-xl bg-blue-900 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800 focus:bg-blue-800 focus:ring-4 focus:ring-blue-200 sm:w-auto">
                Simpan Perubahan
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="inline-flex min-h-[42px] items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 sm:justify-start">
                    Berhasil disimpan.
                </p>
            @endif
        </div>
    </form>
</section>
