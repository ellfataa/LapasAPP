<x-guest-layout>
    <div
        x-data="{ showAlert: {{ session('success') || $errors->any() ? 'true' : 'false' }} }"
        class="grid w-full max-w-6xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10 lg:grid-cols-2"
    >
        <!-- Kolom Kiri: Logo BAPAS -->
        <section class="relative flex min-h-[280px] items-center justify-center overflow-hidden bg-blue-900 px-6 py-10 sm:px-10 lg:min-h-[650px] lg:px-12">
            <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/5"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-white/5"></div>
            <div class="pointer-events-none absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/5"></div>

            <div class="relative z-10 w-full max-w-md text-center">
                <a
                    href="{{ url('/') }}"
                    class="mx-auto flex h-48 w-48 max-w-full items-center justify-center overflow-hidden rounded-full bg-white p-2 shadow-2xl shadow-slate-950/25 ring-4 ring-white/15 transition duration-300 hover:scale-[1.03] focus:outline-none focus:ring-4 focus:ring-blue-200 sm:h-56 sm:w-56 sm:p-3 lg:h-64 lg:w-64"
                    aria-label="BAPAS Purwokerto"
                >
                    <img
                        src="{{ asset('images/bapaspwt.webp') }}"
                        alt="Logo BAPAS Purwokerto"
                        class="h-[92%] w-[92%] object-contain"
                    >
                </a>
            </div>
        </section>

        <!-- Kolom Kanan: Form Login -->
        <section class="flex items-center bg-white">
            <div class="w-full px-5 py-8 sm:px-8 sm:py-10 lg:px-12 lg:py-12">

                <!-- Header Login -->
                <div class="mb-8">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-900 ring-1 ring-inset ring-blue-100">
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"
                            />
                        </svg>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-4">
                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">
                            Login
                        </h2>

                        <a
                            href="{{ url('/') }}"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-100"
                            aria-label="Kembali ke halaman utama"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Kembali
                        </a>
                    </div>

                    <p class="mt-2 text-sm font-medium leading-relaxed text-slate-500">
                        Silakan masuk ke akun Anda untuk melanjutkan.
                    </p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Nomor Induk -->
                    <div>
                        <x-input-label
                            for="identitas"
                            value="Nomor Induk (NRP/NIP/NIK/Nomor Registrasi)"
                            class="mb-2 block text-sm font-bold text-slate-800"
                        />

                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M7 9h4m-4 4h7m3-4h.01m-.01 4h.01"
                                    />
                                </svg>
                            </span>

                            <x-text-input
                                id="identitas"
                                class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white py-3 pl-12 pr-4 text-base text-slate-900 shadow-sm transition placeholder:text-slate-400 hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700"
                                type="text"
                                name="identitas"
                                :value="old('identitas')"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Masukkan Nomor Induk Anda"
                                maxlength="50"
                            />
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <x-input-label
                                for="password"
                                :value="__('Password')"
                                class="block text-sm font-bold text-slate-800"
                            />

                            <!-- Informasi Lupa Password -->
                            <span class="text-xs font-medium italic text-slate-500 hover:text-blue-700 transition cursor-help" title="Silakan lapor kepada Admin Bapas untuk mereset password Anda">
                                Lupa password? Hubungi Admin
                            </span>
                        </div>

                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8 10V7a4 4 0 118 0v3"
                                    />
                                </svg>
                            </span>

                            <x-text-input
                                id="password"
                                class="block min-h-[48px] w-full rounded-xl border-slate-300 bg-white py-3 pl-12 pr-12 text-base text-slate-900 shadow-sm transition placeholder:text-slate-400 hover:border-slate-400 focus:border-blue-700 focus:ring-blue-700"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password Anda"
                            />

                            <button
                                type="button"
                                id="toggle-password"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 transition hover:text-slate-700"
                                aria-label="Tampilkan password"
                            >
                                <svg
                                    id="toggle-password-icon"
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        id="eye-icon"
                                        d="M1.5 12C1.5 12 5.5 5 12 5s10.5 7 10.5 7-4 7-10.5 7S1.5 12 1.5 12z"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        id="eye-circle"
                                        d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7z"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        id="eye-slash"
                                        class="hidden"
                                        d="M3 3l18 18M9.885 9.886A3.5 3.5 0 0112 7.5a3.5 3.5 0 013.5 3.5c0 .56-.15 1.08-.41 1.53"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Login Manual -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="inline-flex min-h-[50px] w-full items-center justify-center gap-2 rounded-xl bg-blue-900 px-4 py-3.5 font-bold text-white shadow-sm transition hover:bg-blue-800 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-blue-200"
                        >
                            <svg
                                class="h-5 w-5 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3H9m9.75 0l-3-3m3 3l-3 3"
                                />
                            </svg>

                            Masuk ke Sistem
                        </button>
                    </div>

                    {{-- FITUR LOGIN GOOGLE (DINONAKTIFKAN SEMENTARA)
                    <div class="relative py-1 mt-2">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white px-4 font-medium text-slate-500">
                                Atau lebih cepat dengan
                            </span>
                        </div>
                    </div>

                    <a
                        href="{{ route('google.login') }}"
                        class="flex min-h-[50px] w-full items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white px-4 py-3.5 font-bold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-slate-200"
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-inset ring-slate-200">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                        </span>
                        Masuk dengan Google
                    </a>
                    --}}

                    <!-- Tautan Registrasi -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-center mt-4">
                        <a
                            class="text-sm font-semibold text-blue-700 underline decoration-blue-300 underline-offset-4 transition hover:text-blue-900 hover:decoration-blue-700"
                            href="{{ route('register') }}"
                        >
                            Belum memiliki akun? Daftar Akun Baru
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <!-- Modal Notifikasi -->
        <div
            x-show="showAlert"
            x-cloak
            style="display: none;"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/65 px-4 py-6 backdrop-blur-sm transition-opacity"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                @click="showAlert = false"
                class="absolute inset-0 cursor-pointer"
            ></div>

            <div
                class="relative z-10 w-full max-w-md transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xl transition-all sm:p-8"
                x-transition:enter="ease-out duration-300 delay-100"
                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
            >
                @if(session('success'))
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 ring-1 ring-inset ring-emerald-100">
                        <svg
                            class="h-10 w-10 text-emerald-700"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </div>

                    <h3 class="mb-2 text-2xl font-bold text-slate-900">
                        Pemberitahuan
                    </h3>

                    <p class="mb-6 text-base leading-relaxed text-slate-600">
                        {{ session('success') }}
                    </p>
                @elseif($errors->any())
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-red-50 ring-1 ring-inset ring-red-100">
                        <svg
                            class="h-10 w-10 text-red-600"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </div>

                    <h3 class="mb-3 text-2xl font-bold text-slate-900">
                        Login Gagal!
                    </h3>

                    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-left text-sm leading-relaxed text-red-700">
                        <ul class="list-inside list-disc space-y-1.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button
                    @click="showAlert = false"
                    class="min-h-[48px] w-full rounded-xl bg-blue-900 px-4 py-3 font-bold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('toggle-password');
        const eyeSlashPath = document.getElementById('eye-slash');

        if (toggleButton && passwordInput) {
            toggleButton.addEventListener('click', function () {
                const isPasswordVisible = passwordInput.type === 'text';
                passwordInput.type = isPasswordVisible ? 'password' : 'text';
                eyeSlashPath.classList.toggle('hidden', !isPasswordVisible);
                toggleButton.setAttribute('aria-label', isPasswordVisible ? 'Tampilkan password' : 'Sembunyikan password');
            });
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-guest-layout>
