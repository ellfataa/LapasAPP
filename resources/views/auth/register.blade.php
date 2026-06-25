<x-guest-layout>
    <div x-data="{ showAlert: {{ $errors->any() ? 'true' : 'false' }} }">

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-input-label for="nama" value="Nama Lengkap" />
                <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="nama" />
            </div>

            <div class="mt-4">
                <x-input-label for="nomor_induk" value="Nomor Induk (NRP / NIP)" />
                <x-text-input id="nomor_induk" class="block mt-1 w-full" type="text" name="nomor_induk" :value="old('nomor_induk')" required autocomplete="nomor_induk" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-input-label for="role" value="Daftar Sebagai" />
                <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="" disabled selected>-- Pilih Peran --</option>
                    <option value="admin">Admin</option>
                    <option value="pengawas">Pengawas Lapas</option>
                    <option value="narapidana">Mantan Narapidana</option>
                </select>
            </div>

            <div class="mt-4">
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="underline text-sm text-blue-600 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium" href="{{ route('login') }}">
                    Sudah punya akun? Login
                </a>

                <x-primary-button class="ms-4 px-6 py-2">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>

        <div x-show="showAlert" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4 transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click="showAlert = false" class="absolute inset-0 cursor-pointer"></div>
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 md:p-8 text-center transform transition-all">

                @if($errors->any())
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-5">
                        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Registrasi Gagal!</h3>
                    <div class="text-sm text-red-700 text-left bg-red-50 p-4 rounded-lg mb-6 border border-red-100">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button @click="showAlert = false" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition-colors shadow-md">
                    Perbaiki Data
                </button>
            </div>
        </div>

    </div>
</x-guest-layout>
