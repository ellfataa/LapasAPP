<x-app-layout>
    <x-slot name="header">
        <div class="bapas-profile-header flex items-center gap-4 bg-slate-100">
            <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-900 text-white shadow-sm sm:flex">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
                </svg>
            </div>

            <h2 class="text-xl font-bold leading-tight tracking-tight text-slate-900 sm:text-2xl md:text-3xl">
                {{ __('Profile') }}
            </h2>
        </div>
    </x-slot>

        <!-- Toast / Popup for update result -->
        <div id="bapas-toast" class="hidden fixed top-5 right-5 z-50 max-w-sm rounded-lg px-4 py-3 text-white shadow-lg transition-all duration-300 transform opacity-0" role="alert" aria-live="polite">
            <div class="flex items-start gap-3">
                <div class="toast-icon flex-shrink-0">
                    <!-- icon injected by JS -->
                </div>
                <div class="toast-message flex-1 text-sm"></div>
                <button id="bapas-toast-close" class="ml-3 text-white opacity-90 hover:opacity-100">×</button>
            </div>
        </div>

        <div class="min-h-screen bg-slate-100 py-6 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @php
                $backRoute = route('dashboard.narapidana');
                $backLabel = 'Kembali ke Dashboard';

                if (auth()->check()) {
                    $role = strtolower((string) auth()->user()->role);

                    if ($role === 'admin') {
                        $backRoute = route('dashboard.admin');
                        $backLabel = 'Kembali';
                    } elseif ($role === 'pengawas' || $role === 'pk') {
                        $backRoute = route('dashboard.pengawas');
                        $backLabel = 'Kembali';
                    } elseif ($role === 'narapidana' || $role === 'klien' || $role === 'client') {
                        $backLabel = 'Kembali';
                    }
                }
            @endphp

            <!-- Tombol Kembali -->
            <div class="mb-6 flex items-center">
                <a href="{{ $backRoute }}" class="group inline-flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-blue-100">
                    <svg class="h-5 w-5 text-slate-400 transition-colors group-hover:text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>{{ $backLabel }}</span>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 xl:gap-8">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="h-1.5 bg-blue-900"></div>
                    <div class="p-5 sm:p-7 lg:p-8">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="h-1.5 bg-blue-900"></div>
                    <div class="p-5 sm:p-7 lg:p-8">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-red-200 bg-white shadow-sm xl:col-span-2">
                    <div class="h-1.5 bg-red-600"></div>
                    <div class="p-5 sm:p-7 lg:p-8">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $toastStatus = session('status');
        $toastErrors = $errors->all();
        $toastRole = isset($user) && isset($user->role) ? $user->role : (auth()->check() ? auth()->user()->role : null);
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const status = @json($toastStatus);
            const errors = @json($toastErrors);
            const role = @json($toastRole);

            let type = null;
            let message = null;

            if (status === 'profile-updated') {
                type = 'success';
                message = 'Profil berhasil diperbarui';
            } else if (status) {
                type = 'success';
                message = status;
            } else if (errors && errors.length) {
                type = 'error';
                message = errors[0];
            }

            if (message) {
                let roleLabel = '';
                if (role) {
                    const r = String(role).toLowerCase();
                    if (r.includes('admin')) roleLabel = ' (Admin)';
                    else if (r.includes('pk') || r.includes('pengawas')) roleLabel = ' (PK/Pengawas)';
                    else if (r.includes('nara') || r.includes('narapidana') || r.includes('napi')) roleLabel = ' (Narapidana)';
                }

                const container = document.getElementById('bapas-toast');
                const messageNode = container.querySelector('.toast-message');
                const iconNode = container.querySelector('.toast-icon');

                messageNode.textContent = message + roleLabel;
                iconNode.innerHTML = type === 'success'
                    ? '<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
                    : '<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';

                container.classList.remove('hidden');
                container.classList.remove('opacity-0');
                container.classList.add(type === 'success' ? 'bg-green-500' : 'bg-red-500');

                // Auto hide
                const hide = () => {
                    container.classList.add('opacity-0');
                    setTimeout(() => container.classList.add('hidden'), 300);
                };

                setTimeout(hide, 3500);

                // Close button
                const closeBtn = document.getElementById('bapas-toast-close');
                if (closeBtn) closeBtn.addEventListener('click', hide);
            }
        });
    </script>

</x-app-layout>

<style>
    header:has(.bapas-profile-header) {
        background-color: #f1f5f9 !important;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: none !important;
    }

    .bapas-profile-header {
        background-color: #f1f5f9;
    }
</style>
