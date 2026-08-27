<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset='utf-8' />
    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
    <title>{{ $title ?? 'SIMTAN - Sistem Informasi Monitoring Areal Tanaman' }}</title>

    <meta name='viewport' content='width=device-width, initial-scale=1' />
    <link rel="icon" type="image/svg" href="{{ asset('assets/images/logo-ptpn4.png') }}" />

    <!-- Font Preloading for Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" />
    </noscript>

    <script defer src="{{ asset('assets/js/perfect-scrollbar.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/tippy-bundle.umd.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
    @vite(['resources/css/app.css'])

    <!-- Inisialisasi Objek main & $store.app Terpadu -->
    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.store('app')) {
                Alpine.store('app', {
                    sidebar: window.innerWidth > 1024,
                    theme: localStorage.getItem('theme') || 'light',
                    isDarkMode: localStorage.getItem('theme') === 'dark',
                    menu: 'vertical',
                    layout: 'full',
                    rtlClass: 'ltr',
                    animation: '',
                    navbar: 'navbar-sticky',
                    semidark: false,
                    locale: 'en',
                    init() {
                        this.refreshTheme();
                    },
                    toggleSidebar() {
                        this.sidebar = !this.sidebar;
                    },
                    toggleTheme(val) {
                        this.theme = val || (this.theme === 'light' ? 'dark' : 'light');
                        localStorage.setItem('theme', this.theme);
                        this.isDarkMode = this.theme === 'dark';
                        this.refreshTheme();
                    },
                    refreshTheme() {
                        if (this.theme === 'dark') {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    },
                    setRTLLayout() {
                        document.querySelector('html').setAttribute('dir', this.rtlClass);
                    }
                });
            }

            if (!Alpine.data('main')) {
                Alpine.data('main', () => ({}));
            }
        });
    </script>
    <style>
        .custom-toast {
            background: rgba(255, 255, 255, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
        }
        html.dark .custom-toast {
            background: rgba(15, 23, 42, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
    </style>
</head>

<body x-data="main" class="antialiased relative font-['Plus_Jakarta_Sans',sans-serif] text-sm font-normal overflow-x-hidden"
    :class="[$store.app.sidebar ? 'toggle-sidebar' : '', $store.app.theme === 'dark' || $store.app.isDarkMode ? 'dark' : '',
        $store.app.menu, $store.app.layout, $store.app.rtlClass
    ]">



    <!-- screen loader -->
    <div
        class="screen_loader fixed inset-0 bg-[#fafafa] dark:bg-[#060818] z-[60] grid place-content-center animate__animated">
        <svg width="64" height="64" viewBox="0 0 135 135" xmlns="http://www.w3.org/2000/svg" fill="#4361ee">
            <path
                d="M67.447 58c5.523 0 10-4.477 10-10s-4.477-10-10-10-10 4.477-10 10 4.477 10 10 10zm9.448 9.447c0 5.523 4.477 10 10 10 5.522 0 10-4.477 10-10s-4.478-10-10-10c-5.523 0-10 4.477-10 10zm-9.448 9.448c-5.523 0-10 4.477-10 10 0 5.522 4.477 10 10 10s10-4.478 10-10c0-5.523-4.477-10-10-10zM58 67.447c0-5.523-4.477-10-10-10s-10 4.477-10 10 4.477 10 10 10 10-4.477 10-10z">
                <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="-360 67 67" dur="2.5s"
                    repeatCount="indefinite" />
            </path>
            <path
                d="M28.19 40.31c6.627 0 12-5.374 12-12 0-6.628-5.373-12-12-12-6.628 0-12 5.372-12 12 0 6.626 5.372 12 12 12zm30.72-19.825c4.686 4.687 12.284 4.687 16.97 0 4.686-4.686 4.686-12.284 0-16.97-4.686-4.687-12.284-4.687-16.97 0-4.687 4.686-4.687 12.284 0 16.97zm35.74 7.705c0 6.627 5.37 12 12 12 6.626 0 12-5.373 12-12 0-6.628-5.374-12-12-12-6.63 0-12 5.372-12 12zm19.822 30.72c-4.686 4.686-4.686 12.284 0 16.97 4.687 4.686 12.285 4.686 16.97 0 4.687-4.686 4.687-12.284 0-16.97-4.685-4.687-12.283-4.687-16.97 0zm-7.704 35.74c-6.627 0-12 5.37-12 12 0 6.626 5.373 12 12 12s12-5.374 12-12c0-6.63-5.373-12-12-12zm-30.72 19.822c-4.686-4.686-12.284-4.686-16.97 0-4.686 4.687-4.686 12.285 0 16.97 4.686 4.687 12.284 4.687 16.97 0 4.687-4.685 4.687-12.283 0-16.97zm-35.74-7.704c0-6.627-5.372-12-12-12-6.626 0-12 5.373-12 12s5.374 12 12 12c6.628 0 12-5.373 12-12zm-19.823-30.72c4.687-4.686 4.687-12.284 0-16.97-4.686-4.686-12.284-4.686-16.97 0-4.687 4.686-4.687 12.284 0 16.97 4.686 4.687 12.284 4.687 16.97 0z">
                <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="360 67 67" dur="8s"
                    repeatCount="indefinite" />
            </path>
        </svg>
    </div>

    <!-- GLOBAL NOTIFICATION SYSTEM (TOAST TOP-CENTER) -->
    <div x-data="{ 
        show: false, 
        message: '', 
        type: 'success',
        init() {
            @if (session('success'))
                this.trigger('{{ session('success') }}', 'success');
            @endif
            @if (session('error'))
                this.trigger('{{ session('error') }}', 'error');
            @endif
            @if (session('toast'))
                this.trigger('{{ session('toast')['message'] }}', '{{ session('toast')['type'] }}');
            @endif
        },
        trigger(message, type) {
            this.message = message;
            this.type = type || 'success';
            this.show = true;
            setTimeout(() => this.show = false, 5000);
        }
    }" x-cloak
        @toast.window="trigger($event.detail.message, $event.detail.type)"
        class="fixed top-8 left-1/2 -translate-x-1/2 pointer-events-none z-[999999] w-full max-w-md px-4"
        style="left: 50% !important; transform: translateX(-50%) !important;">

        <!-- Floating Premium Toast -->
        <div x-show="show" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="-translate-y-20 opacity-0 scale-90"
            x-transition:enter-end="translate-y-0 opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="-translate-y-20 opacity-0 scale-90" class="pointer-events-auto w-full max-w-md">

            <div :class="{
                'border-emerald-500/40': type === 'success',
                'border-rose-500/40': type === 'error'
            }"
                class="custom-toast border-l-[6px] rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] p-5 flex items-center gap-5 border backdrop-blur-xl">

                <!-- Icon (Vibrant Gradient Backgrounds) -->
                <div :class="type === 'success' ? 'bg-gradient-to-tr from-emerald-600 to-teal-400' : 'bg-gradient-to-tr from-rose-600 to-orange-500'"
                    :style="type === 'success' ? 'background: linear-gradient(135deg, #059669 0%, #2dd4bf 100%) !important;' : 'background: linear-gradient(135deg, #e11d48 0%, #f97316 100%) !important;'"
                    class="p-3 text-white rounded-2xl shadow-lg shrink-0">
                    <template x-if="type === 'success'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </template>
                    <template x-if="type === 'error'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </template>
                </div>

                <!-- Text Content -->
                <div class="flex-1 text-center">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-40 mb-1 text-center"
                        x-text="type === 'success' ? 'Integrasi Data Terpadu' : 'Security Alert'"></p>
                    <p class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-tight leading-tight text-center"
                        x-text="message"></p>
                </div>

                <!-- Close Button -->
                <button @click="show = false" class="opacity-30 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- sidebar menu overlay -->
    <div x-cloak class="fixed inset-0 bg-[black]/60 z-50 lg:hidden" :class="{ 'hidden': !$store.app.sidebar }"
        @click="$store.app.toggleSidebar()"></div>


    <div class="fixed bottom-6 ltr:right-6 rtl:left-6 z-50" x-data="scrollToTop">
        <template x-if="showTopButton">
            <button type="button"
                class="btn btn-outline-primary rounded-full p-2 animate-pulse bg-[#fafafa] dark:bg-[#060818] dark:hover:bg-primary shadow-xl"
                @click="goToTop">
                <svg width="24" height="24" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.5" d="M12 20.75V10.75" fill="currentColor" />
                    <path d="M6.00002 10.75L12 3.25L18 10.75H6.00002Z" fill="currentColor" />
                </svg>
            </button>
        </template>
    </div>

    <x-common.theme-customiser />

    <div class="main-container text-black dark:text-white-dark min-h-screen" :class="[$store.app.navbar]">
        <x-common.sidebar />
        <div class="main-content flex flex-col min-h-screen">
            <x-common.header />
            <div class="dvanimation p-6 animate__animated" :class="[$store.app.animation]">
                {{ $slot }}
            </div>
            <x-common.footer />
        </div>
    </div>

    <!-- Load Alpine.js Plugins & Custom Scripts BEFORE Alpine.js Core to prevent initialization race conditions -->
    <script defer src="{{ asset('assets/js/alpine-collaspe.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/alpine-persist.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/alpine-ui.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/alpine-focus.min.js') }}"></script>
    <script defer src="{{ asset('assets/js/custom.js') }}"></script>
    <script defer src="{{ asset('assets/js/alpine-colorthemes.js') }}"></script>
    <script defer src="{{ asset('assets/js/app.js') }}"></script>
    <script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
</body>

</html>
