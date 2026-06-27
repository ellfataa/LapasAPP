<section>
    <header class="flex items-start gap-4 border-b border-slate-200 pb-5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10V7a4 4 0 118 0v3" />
            </svg>
        </div>

        <div class="min-w-0">
            <h2 class="text-lg font-bold text-slate-900 sm:text-xl">Perbarui Password</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-600">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Password Saat Ini" class="mb-2 block text-sm font-bold text-slate-800" />
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10V7a4 4 0 118 0v3" />
                    </svg>
                </span>

                <x-text-input id="update_password_current_password" name="current_password" type="password" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white py-3 pl-12 pr-4 text-base text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700" autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Password Baru" class="mb-2 block text-sm font-bold text-slate-800" />
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75v-1.5m3.75-3.75V7.875a3.75 3.75 0 10-7.5 0V10.5m-1.5 0h10.5A1.75 1.75 0 0119 12.25v6A1.75 1.75 0 0117.25 20H6.75A1.75 1.75 0 015 18.25v-6a1.75 1.75 0 011.75-1.75z" />
                    </svg>
                </span>

                <x-text-input id="update_password_password" name="password" type="password" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white py-3 pl-12 pr-4 text-base text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700" autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Password Baru" class="mb-2 block text-sm font-bold text-slate-800" />
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2 2 4-4m4.5-1.75V7a2 2 0 00-2-2h-1V4a4.5 4.5 0 00-9 0v1h-1a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-2" />
                    </svg>
                </span>

                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white py-3 pl-12 pr-4 text-base text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700" autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center">
            <x-primary-button class="inline-flex min-h-[48px] w-full items-center justify-center rounded-xl bg-blue-900 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800 focus:bg-blue-800 focus:ring-4 focus:ring-blue-200 sm:w-auto">
                Ubah Password
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="inline-flex min-h-[42px] items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 sm:justify-start">
                    Password diubah.
                </p>
            @endif
        </div>
    </form>
</section>
