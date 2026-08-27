<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Monitoring PTPN IV</title>

    <!-- Preload & Load Font Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS (Deferred for LCP) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        jakarta: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4361ee',
                    }
                }
            }
        }
    </script>

    <!-- Vristo Stylesheets & Scripts -->
    <script src="/assets/js/perfect-scrollbar.min.js"></script>
    <script defer src="/assets/js/popper.min.js"></script>
    <script defer src="/assets/js/tippy-bundle.umd.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
    </style>
</head>

<body x-data="main" class="min-h-screen relative flex items-center justify-center p-4 overflow-hidden transition-colors duration-300 bg-[#f8fafc] text-slate-800 dark:bg-[#060818] dark:text-slate-100"
    :class="[$store.app.theme === 'dark' || $store.app.isDarkMode ? 'dark' : '']">

    <!-- Vibrant Gradient Blur Blobs (Glassmorphism Enhancement) -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-600/10 dark:bg-blue-600/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 my-auto">

        <!-- Logo & Identitas Perusahaan -->
        <div class="text-center mb-4">
            <div class="flex items-center justify-center mb-2">
                <div class="relative p-1.5 bg-white dark:bg-slate-900 rounded-2xl shadow-md border border-slate-100 dark:border-slate-800/80 transition-all duration-300">
                    <img src="{{ asset('assets/images/logo-ptpn4.png') }}"
                         alt="Logo PT. Perkebunan Nusantara IV"
                         width="72"
                         height="72"
                         class="w-16 h-16 object-contain">
                </div>
            </div>

            <p class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 tracking-[0.2em] uppercase mb-0.5">
                PT. Perkebunan Nusantara IV — Regional I
            </p>

            <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                Sistem Informasi Monitoring
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">
                Areal Tanaman <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">(SIMTAN)</span>
            </p>
        </div>

        <!-- Premium Glassmorphic Login Card (Compacted to prevent scroll) -->
        <div class="bg-white/80 dark:bg-[#0e1726]/70 backdrop-blur-xl rounded-2xl p-6 border border-white/20 dark:border-slate-800/60 shadow-[0_15px_40px_rgba(0,0,0,0.03)] dark:shadow-[0_15px_40px_rgba(0,0,0,0.25)] transition-all duration-300">

            <div class="mb-6 text-center">
    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
        Login 
    </h2>
    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">
        Masukkan kredensial Anda untuk masuk ke sistem.
    </p>
</div>

            <!-- Menampilkan pesan sukses / info -->
            @if (session('success'))
                <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-xl text-[11px] font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Notifikasi Error Login dengan Desain Elegan -->
            @if ($errors->any())
                <div class="mb-4 p-3 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 rounded-xl text-[11px] font-semibold space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-3.5">
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 transition-colors duration-200">
                            <!-- Sharp Email Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            placeholder="nama@ptpn4.co.id"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 focus:bg-white dark:bg-slate-900/50 dark:focus:bg-slate-900/90 border border-slate-200 dark:border-slate-800/80 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 @error('email') border-rose-500 dark:border-rose-500 @enderror"
                            required autocomplete="email" autofocus>
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 transition-colors duration-200">
                            <!-- Sharp Lock Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 focus:bg-white dark:bg-slate-900/50 dark:focus:bg-slate-900/90 border border-slate-200 dark:border-slate-800/80 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 @error('password') border-rose-500 dark:border-rose-500 @enderror"
                            required autocomplete="current-password">
                    </div>
                </div>

                <!-- Ingat Saya -->
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-3.5 h-3.5 text-emerald-600 border-slate-300 dark:border-slate-800 rounded focus:ring-emerald-500 bg-white dark:bg-slate-950"
                            {{ old('remember') ? 'checked' : '' }}>
                        <span class="text-xs text-slate-600 dark:text-slate-400 font-semibold select-none">Ingat Sesi Saya</span>
                    </label>
                </div>

                <!-- Tombol Submit Gradient Mewah -->
                <button type="submit"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-xs tracking-wide transition-all duration-200 shadow-md shadow-emerald-500/10 active:scale-[0.99] disabled:opacity-60">
                    Masuk ke Sistem
                </button>
            </form>

        </div>

        <!-- Footer -->
        <div class="text-center mt-4">
            <p class="text-[10px] text-slate-400 dark:text-slate-600 font-semibold tracking-wide">
                &copy; {{ date('Y') }} SIMTAN PTPN IV. Hak Cipta Dilindungi.
            </p>
        </div>

    </div>

    <!-- Alpine.js & Plugins for Vristo App State -->
    <script src="/assets/js/alpine-collaspe.min.js"></script>
    <script src="/assets/js/alpine-persist.min.js"></script>
    <script defer src="/assets/js/alpine-ui.min.js"></script>
    <script defer src="/assets/js/alpine-focus.min.js"></script>
    <script defer src="/assets/js/alpine.min.js"></script>
    <script src="/assets/js/custom.js"></script>
</body>

</html>
