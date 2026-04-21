<header
    class="h-16 bg-white dark:bg-[#0e1726] border-b border-gray-200 dark:border-[#191e3a] flex items-center justify-between px-6 sticky top-0 z-40 transition-all duration-300">

    <!-- SEARCH BAR -->
    <div class="flex-1 max-w-xl">
        <div class="relative group pl-3 md:pl-4">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
            </div>
            <input type="text" placeholder="Cari kebun, data monitoring, laporan..."
                class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#1b2e4b] border border-gray-200 dark:border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm text-gray-700 dark:text-white-dark placeholder-gray-500 dark:placeholder-white-dark/50 transition-all" />
        </div>
    </div>

    <!-- RIGHT SECTION -->
    <div class="flex items-center gap-4 ml-6">

        <!-- DATE & TIME -->
        <div x-data="clock()"
            class="text-sm text-gray-600 dark:text-white-dark hidden md:block text-right leading-tight">
            <p class="font-medium text-gray-900 dark:text-white-light" x-text="date"></p>
            <p class="text-xs text-gray-500 dark:text-white-dark/70" x-text="time"></p>
        </div>

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
                <div class="text-left hidden md:block">
                    <p class="text-sm font-bold text-gray-900 dark:text-white-light leading-none">
                        <?php echo e(auth()->user()->name ?? 'Admin PTPN'); ?></p>
                    <p class="text-xs text-gray-500 dark:text-white-dark mt-1 capitalize">
                        <?php echo e(auth()->user()->role->name ?? 'Administrator'); ?></p>
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
                        <?php echo e(auth()->user()->name ?? 'Admin PTPN'); ?></p>
                    <p class="text-xs text-gray-500 dark:text-white-dark truncate">
                        <?php echo e(auth()->user()->email ?? 'admin@ptpn4.co.id'); ?></p>
                </div>

                <!-- UPDATE DISINI: Menggunakan route named 'settings.index' -->
                <a href="<?php echo e(route('settings.index')); ?>"
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
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
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
<?php /**PATH C:\simtan-monitoring-palm-oil\resources\views/components/common/header.blade.php ENDPATH**/ ?>