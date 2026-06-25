<footer class="mt-auto border-t border-blue-800 bg-blue-900 text-blue-100">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col items-center justify-between gap-4 text-center sm:flex-row sm:text-left">

            <!-- Identitas Sistem -->
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 ring-1 ring-inset ring-white/15">
                    <img src="{{ asset('images/bapaspwt.png') }}" alt="BAPAS Purwokerto" class="h-8 w-8 object-contain">
                </div>

                <div>
                    <p class="text-sm font-semibold text-white">
                        BAPAS Purwokerto
                    </p>
                    <p class="mt-0.5 text-xs leading-relaxed text-blue-200">
                        Sistem Informasi
                    </p>
                </div>
            </div>

            <!-- Hak Cipta -->
            <div class="text-sm leading-relaxed text-blue-200">
                <p>
                    &copy; {{ date('Y') }} BAPAS Purwokerto. Seluruh hak cipta dilindungi.
                </p>
            </div>

        </div>
    </div>
</footer>
