<header
    class="h-16 bg-white dark:bg-[#0e1726] border-b border-gray-200 dark:border-[#191e3a] flex items-center justify-between px-6 sticky top-0 z-40 transition-all duration-300">

    <!-- IMPERSONATION INDICATOR (God Mode - Skripsi HCI Standard) -->
    <div class="flex-1 flex items-center pl-3 md:pl-4 min-w-0 mr-4">
        @if(session()->has('original_user_id'))
            <div class="flex items-center gap-2 px-3 py-1 rounded-full transition-all duration-300 shrink min-w-0"
                 style="background-color: rgba(225, 29, 72, 0.08) !important; border: 1px solid rgba(225, 29, 72, 0.25) !important;">
                <div class="flex items-center gap-1.5 text-[11px] font-semibold min-w-0" style="color: #e11d48 !important;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0 animate-pulse" style="color: #e11d48 !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="font-extrabold uppercase tracking-wider text-[9px] shrink-0" style="color: #e11d48 !important;">Penyamaran:</span>
                    <span class="truncate font-bold max-w-[120px] sm:max-w-[180px] md:max-w-[240px]" style="color: #be123c !important;">Menyamar sebagai <strong class="underline" style="color: #be123c !important;">{{ auth()->user()->name }}</strong></span>
                </div>
                <a href="{{ route('impersonate.leave') }}" 
                   class="px-2.5 py-0.5 font-extrabold text-[9px] uppercase tracking-wider rounded-full shadow transition-all active:scale-95 shrink-0 ml-1.5"
                   style="background-color: #e11d48 !important; color: #ffffff !important; display: inline-block !important;">
                    Kembali<span class="hidden sm:inline"> ke Superadmin</span>
                </a>
            </div>
        @endif
    </div>

    <!-- RIGHT SECTION -->
    <div class="flex items-center gap-4 shrink-0">

        <!-- DATE & TIME -->
        @if(!session()->has('original_user_id'))
        <div x-data="clock()"
            class="text-sm text-gray-600 dark:text-white-dark hidden md:block text-right leading-tight">
            <p class="font-medium text-gray-900 dark:text-white-light" x-text="date"></p>
            <p class="text-xs text-gray-500 dark:text-white-dark/70" x-text="time"></p>
        </div>
        @endif

        <!-- PROFILE DROPDOWN -->
        <div class="relative" x-data="dropdown">
            <button @click="toggle"
                class="flex items-center gap-3 p-2 hover:bg-gray-100 dark:hover:bg-white-light/10 rounded-lg transition-colors">
                <div
                    class="w-9 h-9 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="text-emerald-600 dark:text-emerald-500">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>
                <div class="text-left hidden md:block max-w-[150px]">
                    <p class="text-sm font-bold text-gray-900 dark:text-white-light leading-none truncate">
                        {{ auth()->user()->name ?? 'Admin PTPN' }}</p>
                    <p class="text-xs text-gray-500 dark:text-white-dark mt-1 capitalize truncate">
                        {{ auth()->user()->role->name ?? 'Administrator' }}</p>
                </div>
            </button>

            <!-- PROFILE MENU DROPDOWN -->
            <div x-cloak x-show="open" @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-[#1b2e4b] rounded-lg shadow-lg border border-gray-200 dark:border-[#191e3a] py-2 z-50">

                <div class="px-4 py-3 border-b border-gray-100 dark:border-[#191e3a]">
                    <p class="text-sm font-bold text-gray-900 dark:text-white-light">
                        {{ auth()->user()->name ?? 'Admin PTPN' }}</p>
                    <p class="text-xs text-gray-500 dark:text-white-dark truncate">
                        {{ auth()->user()->email ?? 'admin@ptpn4.co.id' }}</p>
                </div>

                <!-- UPDATE DISINI: Menggunakan route named 'settings.index' -->
                <a href="{{ route('settings.index') }}"
                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-white-dark hover:bg-gray-50 dark:hover:bg-white-light/5 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="text-gray-500 dark:text-white-dark">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg>
                    <span>Pengaturan</span>
                </a>

                <!-- Logout Form -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
