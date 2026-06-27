<section class="space-y-6">
    <header class="flex items-start gap-4 border-b border-red-100 pb-5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-inset ring-red-100">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79L18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-1.327L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-10.978.397a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
        </div>

        <div class="min-w-0">
            <h2 class="text-lg font-bold text-red-600 sm:text-xl">Hapus Akun Permanen</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-600">Setelah akun Anda dihapus, semua sumber daya dan data di dalamnya akan dihapus secara permanen. Jika Anda adalah Narapidana/Klien, pastikan Anda telah berkonsultasi dengan PK sebelum menghapus akun.</p>
        </div>
    </header>

    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 sm:p-5">
        <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="inline-flex min-h-[48px] w-full items-center justify-center rounded-xl bg-red-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:bg-red-700 focus:ring-4 focus:ring-red-200 sm:w-auto">
            Hapus Akun Saya
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5 sm:p-6 lg:p-8">
            @csrf
            @method('delete')

            <div class="flex items-start gap-4 border-b border-slate-200 pb-5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 ring-1 ring-inset ring-red-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374L10.052 3.38c.865-1.5 3.03-1.5 3.896 0l7.355 12.746zM12 16.5h.008v.008H12V16.5z" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-slate-900 sm:text-xl">Apakah Anda yakin ingin menghapus akun ini?</h2>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Tindakan ini tidak dapat dibatalkan. Masukkan password Anda untuk mengonfirmasi bahwa Anda benar-benar ingin menghapus akun ini beserta seluruh riwayat laporannya.</p>
                </div>
            </div>

            <div class="mt-6">
                <x-input-label for="password" value="Password" class="sr-only" />

                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10V7a4 4 0 118 0v3" />
                        </svg>
                    </span>

                    <x-text-input id="password" name="password" type="password" class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white py-3 pl-12 pr-4 text-base text-slate-900 shadow-sm transition placeholder:text-slate-400 hover:border-slate-400 focus:border-red-500 focus:ring-red-500" placeholder="Masukkan Password Anda" />
                </div>

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
                <x-secondary-button x-on:click="$dispatch('close')" class="inline-flex min-h-[46px] w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 focus:ring-4 focus:ring-slate-200 sm:w-auto">
                    Batal
                </x-secondary-button>

                <x-danger-button class="inline-flex min-h-[46px] w-full items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:bg-red-700 focus:ring-4 focus:ring-red-200 sm:w-auto">
                    Ya, Hapus Akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
