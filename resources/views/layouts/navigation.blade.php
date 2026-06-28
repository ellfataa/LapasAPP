<nav x-data="{ open: false }" class="relative z-50 border-b border-blue-800/70 bg-blue-900 shadow-lg shadow-slate-900/10">
    @php
        $role = Auth::user()->role;
        if ($role === 'admin') {
            $urlDashboard = route('dashboard.admin');
            $isDashboardActive = request()->routeIs('dashboard.admin');
        } elseif ($role === 'pengawas') {
            $urlDashboard = route('dashboard.pengawas');
            $isDashboardActive = request()->routeIs('dashboard.pengawas');
        } else {
            $urlDashboard = route('dashboard.narapidana');
            $isDashboardActive = request()->routeIs('dashboard.narapidana');
        }
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-[72px] items-center justify-between">

            <div class="flex min-w-0 items-center">

                @if($role === 'admin')
                    <button @click="$dispatch('toggle-admin-sidebar')" class="md:hidden mr-4 inline-flex items-center justify-center p-2 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 focus:outline-none transition-colors">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h16" />
                        </svg>
                    </button>
                @endif

                <div class="flex shrink-0 items-center">
                    <a href="{{ $urlDashboard }}" class="group inline-flex min-h-12 items-center rounded-xl px-2 py-1.5 transition duration-200 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <img src="{{ asset('images/bapaspwt.png') }}" alt="Logo BAPAS" class="block h-11 w-auto max-w-[170px] object-contain transition duration-200 group-hover:scale-[1.02] sm:h-12 sm:max-w-[210px]">
                    </a>
                </div>

                <div class="hidden sm:ms-8 sm:flex sm:items-center">
                    <a href="{{ $urlDashboard }}" @class(['inline-flex min-h-11 items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-white/50', 'bg-white/15 text-white shadow-sm ring-1 ring-inset ring-white/20' => $isDashboardActive, 'text-blue-100 hover:bg-white/10 hover:text-white' => ! $isDashboardActive])>
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5M5.25 9.75V21h13.5V9.75M9 21v-6h6v6"/></svg>
                        {{ __('Dashboard') }}
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="group inline-flex min-h-11 max-w-[280px] items-center gap-3 rounded-xl border border-white/15 bg-white/10 px-3.5 py-2 text-left text-sm font-semibold text-white shadow-sm transition duration-200 hover:border-white/25 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/50">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15 text-white ring-1 ring-inset ring-white/20"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"/></svg></span>
                            <span class="min-w-0"><span class="block truncate">{{ Auth::user()->nama }}</span></span>
                            <svg class="h-4 w-4 shrink-0 text-blue-200 transition duration-200 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="py-1">
                            <x-dropdown-link :href="route('profile.edit')"><span class="flex items-center gap-3"><svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"/></svg>{{ __('Profile') }}</span></x-dropdown-link>
                            <div class="my-1 border-t border-slate-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"><span class="flex items-center gap-3 text-red-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3H9m9.75 0l-3-3m3 3l-3 3"/></svg>{{ __('Log Out') }}</span></x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center sm:hidden">
                <button type="button" @click="open = ! open" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/15 bg-white/10 text-blue-100 transition duration-200 hover:bg-white/15 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-cloak x-show="open" x-transition class="border-t border-white/10 bg-blue-950/95 backdrop-blur-sm sm:hidden z-50 relative">
        <div class="space-y-2 px-4 pb-4 pt-4">
            <a href="{{ $urlDashboard }}" @class(['flex min-h-12 w-full items-center gap-3 rounded-xl px-4 py-3 text-base font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-white/50', 'bg-white/15 text-white ring-1 ring-inset ring-white/20' => $isDashboardActive, 'text-blue-100 hover:bg-white/10 hover:text-white' => ! $isDashboardActive])>
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5M5.25 9.75V21h13.5V9.75M9 21v-6h6v6"/></svg>
                {{ __('Dashboard') }}
            </a>
        </div>
        <div class="border-t border-white/10 px-4 pb-5 pt-4">
            <div class="rounded-xl border border-white/10 bg-white/10 p-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white ring-1 ring-inset ring-white/20"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"/></svg></span>
                    <div class="min-w-0">
                        <div class="truncate text-base font-semibold text-white">{{ Auth::user()->nama }}</div>
                        <div class="truncate text-sm text-blue-200">{{ Auth::user()->email ?? Auth::user()->nomor_induk }}</div>
                    </div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="flex min-h-12 w-full items-center gap-3 rounded-xl px-4 py-3 text-base font-medium text-blue-100 transition duration-200 hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"/></svg>
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex min-h-12 w-full items-center gap-3 rounded-xl px-4 py-3 text-base font-medium text-red-200 transition duration-200 hover:bg-red-500/15 hover:text-red-100 focus:outline-none focus:ring-2 focus:ring-red-300/60">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3H9m9.75 0l-3-3m3 3l-3 3"/></svg>
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
