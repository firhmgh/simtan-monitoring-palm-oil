<!-- 
    SIMTAN (Sistem Informasi Monitoring Areal Tanaman) - PTPN IV Regional I
    Komponen Sidebar Navigasi Utama (HCI & Enterprise Grade)
    Rancangan Skripsi Bab 3 - Kepatuhan Interaksi Manusia & Komputer (HCI)
    Mengintegrasikan Vristo Template secara mulus dengan Glassmorphism & Transisi Dinamis
-->
<div :class="{ 'dark text-white-dark': $store.app.semidark }">
    
    <!-- Kontainer Navigasi Utama: Lebar dinamis 260px/80px dengan latar belakang transparan & backdrop-blur -->
    <nav x-data="sidebar"
        class="sidebar fixed min-h-screen h-full top-0 bottom-0 shadow-[5px_0_25px_0_rgba(0,0,0,0.05)] z-[1000] transition-all duration-300 bg-white/80 dark:bg-[#0e1726]/80 backdrop-blur-xl border-r border-gray-200/40 dark:border-white/5"
        :class="$store.app.sidebar ? 'w-[260px]' : 'w-[80px]'">

        <!-- Floating Interactive Toggle Button (HCI Compliance & Fitts's Law)
             Selalu terlihat di tepi kanan sidebar (-right-[14px]) tanpa x-show, mendukung rotasi transisi 180 derajat -->
        <button type="button" @click="$store.app.toggleSidebar()"
            class="absolute top-[26px] -right-[14px] z-[1001] w-[28px] h-[28px] bg-white dark:bg-[#0e1726] border border-gray-200/80 dark:border-white/10 rounded-full flex items-center justify-center text-slate-900 dark:text-white hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 shadow-[0_2px_8px_rgba(0,0,0,0.08)] hover:shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all duration-300 cursor-pointer"
            :class="{ 'rotate-180': !$store.app.sidebar }"
            :title="$store.app.sidebar ? 'Sembunyikan Navigasi' : 'Tampilkan Navigasi'">
            <svg class="w-3.5 h-3.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>

        <!-- Wrapper Konten Internal dengan Overflow Hidden untuk Mencegah Kebocoran Layout saat Mode Mini -->
        <div class="h-full flex flex-col overflow-hidden">

            <!-- Bagian Identitas Korporat & Brand Header (PTPN IV Regional I) -->
            <div class="h-20 flex items-center border-b border-gray-100 dark:border-slate-800/60 transition-all duration-300"
                :class="$store.app.sidebar ? 'px-4 justify-between' : 'px-[18px] justify-center'">
                <a href="{{ route('index') }}" class="flex items-center gap-3.5 group shrink-0">
                    <!-- Logo Utama PT Perkebunan Nusantara IV (PTPN 4) -->
                    <div class="w-[44px] h-[44px] bg-[#00a76f] rounded-xl flex items-center justify-center shrink-0 shadow-sm shadow-emerald-200/50 dark:shadow-none transition-transform group-hover:scale-105">
                        <img class="w-8 h-8 object-contain" src="{{ asset('assets/images/logo-ptpn4.png') }}" alt="Logo PTPN4" />
                    </div>

                    <!-- Judul & Subjudul: Disembunyikan pada Mini Mode dengan Transisi Opacity Halus -->
                    <div x-show="$store.app.sidebar" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100" 
                         class="flex flex-col justify-center h-[46px] font-['Plus_Jakarta_Sans',sans-serif]">
                        <span class="font-extrabold text-slate-900 dark:text-white text-[21px] leading-[24px] tracking-tighter">
                            PTPN IV
                        </span>
                        <span class="text-[12px] text-slate-500 dark:text-white-dark font-black tracking-[0.05em] leading-[18px]">
                            Regional I
                        </span>
                    </div>
                </a>
            </div>

            <!-- Daftar Menu Navigasi Utama Terintegrasi Standar Enterprise & Publikasi Akademik -->
            <nav class="flex-1 overflow-y-auto p-3 space-y-1 perfect-scrollbar font-['Plus_Jakarta_Sans',sans-serif]">
                @php
                    // Array Registrasi Menu Navigasi Utama Lengkap dengan Konfigurasi Role (RBAC)
                    $menus = [
                        [
                            'route' => 'index',
                            'label' => 'Dashboard',
                            'icon' => 'layout-dashboard',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                        [
                            'route' => 'monitoring.data-kebun',
                            'label' => 'Manajemen Areal',
                            'icon' => 'tabler-database',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                        [
                            'route' => 'monitoring.detail',
                            'label' => 'Detail Kebun',
                            'icon' => 'leaf',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                        [
                            'route' => 'monitoring.laporan',
                            'label' => 'Generate Laporan',
                            'icon' => 'file-bar-chart-2',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                        [
                            'route' => 'monitoring.import',
                            'label' => 'Proses Unggah Data',
                            'icon' => 'upload',
                            'roles' => ['superadmin', 'admin'],
                        ],
                        [
                            'route' => 'monitoring.riwayat-data',
                            'label' => 'Riwayat Data',
                            'icon' => 'activity',
                            'roles' => ['superadmin', 'admin'],
                        ],
                        [
                            'route' => 'monitoring.kelola-akun',
                            'label' => 'Kelola Akun',
                            'icon' => 'users',
                            'roles' => ['superadmin'],
                        ],
                        [
                            'route' => 'monitoring.settings',
                            'label' => 'Konfigurasi Sistem',
                            'icon' => 'settings',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                    ];
                @endphp

                <!-- Evaluasi RBAC Laravel (Role-Based Access Control) Terproteksi -->
                @foreach ($menus as $menu)
                    @php
                        $hasAccess = false;
                        foreach ($menu['roles'] as $role) {
                            if (auth()->user()->hasRole($role)) {
                                $hasAccess = true;
                                break;
                            }
                        }
                    @endphp

                    @if ($hasAccess)
                        @php
                            $url = isset($menu['route']) && $menu['route'] !== '#' && Route::has($menu['route'])
                                ? route($menu['route'])
                                : '#';

                            $isActive = $url !== '#' && request()->routeIs($menu['route']);
                        @endphp

                        <!-- Item Link Menu dengan Active State Dinamis, Hover Micro-animations, dan Responsive Alignment -->
                        <a href="{{ $url }}"
                            class="flex items-center rounded-lg transition-all duration-200 group
                            {{ $isActive
                                ? 'bg-gradient-to-r from-emerald-600 to-teal-500 text-slate-900 dark:text-white font-bold shadow-md shadow-emerald-200/20'
                                : 'text-slate-900 dark:text-white hover:bg-gradient-to-r hover:from-emerald-500/10 hover:to-teal-500/10 hover:text-emerald-600 dark:hover:text-emerald-400 font-bold' }}"
                            :class="$store.app.sidebar ? 'px-3 py-2.5 gap-3' : 'p-3 justify-center'"
                            :title="!$store.app.sidebar ? '{{ $menu['label'] }}' : ''">

                            <!-- Wrapper Icon Menu Navigasi dengan Warna Dinamis untuk Kontras Optimal -->
                            <div class="{{ $isActive ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-emerald-500' }} shrink-0">
                                @if (str_starts_with($menu['icon'], 'tabler-'))
                                    <x-dynamic-component :component="$menu['icon']" class="w-5 h-5" />
                                @elseif ($menu['icon'] == 'layout-dashboard')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7" />
                                        <rect x="14" y="3" width="7" height="7" />
                                        <rect x="14" y="14" width="7" height="7" />
                                        <rect x="3" y="14" width="7" height="7" />
                                    </svg>
                                @elseif($menu['icon'] == 'leaf')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" />
                                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" />
                                    </svg>
                                @elseif($menu['icon'] == 'file-bar-chart-2')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <path d="M12 18v-4" />
                                        <path d="M8 18v-2" />
                                        <path d="M16 18v-6" />
                                    </svg>
                                @elseif($menu['icon'] == 'upload')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="17 8 12 3 7 8" />
                                        <line x1="12" y1="3" x2="12" y2="15" />
                                    </svg>
                                @elseif($menu['icon'] == 'users')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                @elseif($menu['icon'] == 'activity')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                    </svg>
                                @elseif($menu['icon'] == 'settings')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3" />
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                    </svg>
                                @endif
                            </div>

                            <!-- Label Menu: Disembunyikan saat Mini Mode dengan Warna Dinamis Terjamin -->
                            <span x-show="$store.app.sidebar" 
                                  x-transition:enter="transition ease-out duration-200" 
                                  x-transition:enter-start="opacity-0 translate-x-2" 
                                  x-transition:enter-end="opacity-100 translate-x-0" 
                                  class="text-sm font-bold truncate text-slate-900 dark:text-white">
                                {{ $menu['label'] }}
                            </span>
                        </a>
                    @endif
                @endforeach

                <!-- SECTION: USER IMPERSONATION SWITCHER (Demo Mode: Switch User) -->
                @if(auth()->user()->role->name === 'superadmin' || session()->has('original_user_id'))
                    @php
                        $demoUsers = \App\Models\User::whereIn('email', [
                            'admin.regional1@ptpn4.co.id',
                            'user.regional1@ptpn4.co.id'
                        ])->get();
                    @endphp
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-800/60 font-jakarta" x-data="{ openSwitcher: false }">
                        <!-- Header Switcher Toggle -->
                        <button type="button" @click="openSwitcher = !openSwitcher"
                            class="flex items-center w-full text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-900/50 rounded-lg p-2.5 transition-all font-bold text-xs uppercase tracking-wider justify-between"
                            :title="!$store.app.sidebar ? 'Switch User' : ''">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <span x-show="$store.app.sidebar" class="truncate font-extrabold">Demo: Switch User</span>
                            </div>
                            <svg x-show="$store.app.sidebar" class="w-3.5 h-3.5 transition-transform duration-200" :class="{'rotate-90': openSwitcher}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>

                        <!-- Dropdown List of Demo Users -->
                        <div x-show="openSwitcher && $store.app.sidebar" x-collapse class="mt-1.5 space-y-1 pl-6">
                            @foreach($demoUsers as $demoUser)
                                @if(auth()->user()->id !== $demoUser->id)
                                    <a href="{{ route('impersonate', $demoUser->id) }}"
                                       class="flex flex-col p-2 bg-slate-50/50 dark:bg-slate-900/40 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 border border-slate-100 dark:border-slate-800/50 rounded-xl transition-all text-left">
                                        <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">{{ $demoUser->name }}</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium capitalize">{{ $demoUser->role->name }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Tombol Keluar Sistem (Logout Engine) dengan Konfirmasi CSS Terpadu -->
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-lg transition-all font-bold"
                        :class="$store.app.sidebar ? 'px-3 py-2.5 gap-3' : 'p-3 justify-center'"
                        :title="!$store.app.sidebar ? 'Keluar Sistem' : ''">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        <span x-show="$store.app.sidebar" 
                              x-transition:enter="transition ease-out duration-200" 
                              x-transition:enter-start="opacity-0 translate-x-2" 
                              x-transition:enter-end="opacity-100 translate-x-0" 
                              class="text-sm font-bold truncate">
                            Keluar Sistem
                        </span>
                    </button>
                </form>
            </nav>
        </div>
    </nav>
</div>
