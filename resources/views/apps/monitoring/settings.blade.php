<x-layout.default>
    {{-- Font Plus Jakarta Sans (standar tipografi SIMTAN) --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <div x-data="settingsData()" class="space-y-6 font-['Plus_Jakarta_Sans'] antialiased">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between gap-4">
            <div>
                <!-- Navigasi Breadcrumb Standar SIMTAN -->
                <ul class="flex space-x-2 text-xs mb-2 text-white-dark tracking-widest font-black uppercase">
                    <li><a href="{{ route('index') }}" class="text-primary hover:underline font-black">Monitoring</a></li>
                    <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 font-black text-slate-400">Settings</li>
                </ul>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white italic tracking-tighter">
                    Pengaturan Sistem
                </h1>
                <p class="text-xs font-bold italic text-slate-500 dark:text-slate-400 mt-2 border-l-2 border-primary pl-2 tracking-tight">
                    Sistem Integrasi Terpadu - PTPN IV Regional I
                </p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-800">
            {{-- Tab navigasi: Profil, Keamanan, dan Konfigurasi AI --}}
            <button @click="activeTab = 'profile'"
                :class="activeTab === 'profile' ? 'border-emerald-600 text-emerald-600' :
                    'border-transparent text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                class="flex items-center gap-2 px-8 py-4 border-b-2 font-black text-xs transition-all duration-200 tracking-[0.1em] italic">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                Profil Pengguna
            </button>
            <button @click="activeTab = 'security'"
                :class="activeTab === 'security' ? 'border-emerald-600 text-emerald-600' :
                    'border-transparent text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                class="flex items-center gap-2 px-8 py-4 border-b-2 font-black text-xs transition-all duration-200 tracking-[0.1em] italic">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                Keamanan
            </button>

            @if (auth()->user()->hasRole('superadmin'))
                <button @click="activeTab = 'ai'"
                    :class="activeTab === 'ai' ? 'border-emerald-600 text-emerald-600' :
                        'border-transparent text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="flex items-center gap-2 px-8 py-4 border-b-2 font-black text-xs transition-all duration-200 tracking-[0.1em] italic">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="m12 8-9.04 9.06a2.82 2.82 0 1 0 3.98 3.98L16 12" />
                        <circle cx="17" cy="7" r="5" />
                    </svg>
                    Konfigurasi AI & Sistem
                </button>
            @endif
        </div>

        <div class="mt-4">
            <!-- 1. Profile Tab -->
            <div x-show="activeTab === 'profile'" class="animate__animated animate__fadeIn">
                <div class="panel shadow-lg border-none rounded-2xl p-8 bg-white dark:bg-[#0e1726]">
                    <h3
                        class="text-base font-black mb-8 flex items-center gap-3 italic tracking-wider dark:text-white text-left">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span> Informasi Personal
                    </h3>
                    <form action="{{ route('settings.update-profile') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 tracking-widest pl-1">Nama
                                    Lengkap</label>
                                <input type="text" name="name" value="{{ auth()->user()->name }}"
                                    class="form-input rounded-xl py-3 font-bold dark:bg-[#1b2e4b] dark:border-none dark:text-white focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 tracking-widest pl-1">Email</label>
                                <input type="email" name="email" value="{{ auth()->user()->email }}"
                                    class="form-input rounded-xl py-3 font-bold dark:bg-[#1b2e4b] dark:border-none dark:text-white focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-gray-400 tracking-widest pl-1">Username</label>
                                <input type="text" value="{{ auth()->user()->username }}"
                                    class="form-input rounded-xl py-3 bg-gray-50 dark:bg-black/20 cursor-not-allowed font-bold opacity-60 dark:text-white"
                                    readonly>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 tracking-widest pl-1">Otoritas
                                    Akses</label>
                                <div class="mt-1">
                                    <span
                                        class="badge badge-outline-primary py-2 px-6 rounded-full text-[10px] font-black tracking-tighter italic">
                                        {{ auth()->user()->role->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 flex justify-end">
                            <button type="submit"
                                class="btn btn-primary px-12 rounded-xl shadow-lg font-black tracking-widest text-[10px] italic">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. Security Tab -->
            <div x-show="activeTab === 'security'" x-cloak class="animate__animated animate__fadeIn">
                <div class="panel shadow-lg border-none rounded-2xl p-8 bg-white dark:bg-[#0e1726]">
                    <h3
                        class="text-base font-black mb-8 flex items-center gap-3 italic tracking-wider dark:text-white text-left">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span> Manajemen Keamanan
                    </h3>
                    <form action="{{ route('settings.update-password') }}" method="POST"
                        class="max-w-2xl space-y-6 text-left">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 tracking-widest pl-1">Kata
                                Sandi Saat Ini</label>
                            <input type="password" name="current_password"
                                class="form-input rounded-xl py-3 dark:bg-[#1b2e4b] dark:border-none dark:text-white"
                                placeholder="••••••••">
                        </div>
                        <hr class="border-gray-100 dark:border-gray-800">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 tracking-widest pl-1">Kata
                                    Sandi Baru</label>
                                <input type="password" name="password"
                                    class="form-input rounded-xl py-3 dark:bg-[#1b2e4b] dark:border-none dark:text-white"
                                    placeholder="Minimal 8 karakter">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 tracking-widest pl-1">Konfirmasi
                                    Password</label>
                                <input type="password" name="password_confirmation"
                                    class="form-input rounded-xl py-3 dark:bg-[#1b2e4b] dark:border-none dark:text-white"
                                    placeholder="Ulangi kata sandi">
                            </div>
                        </div>
                        <div class="mt-10">
                            <button type="submit"
                                class="btn btn-primary px-12 rounded-xl shadow-lg font-black tracking-widest text-[10px] italic">Perbarui
                                Keamanan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 3. AI Tab -->
            @if (auth()->user()->hasRole('superadmin'))
                <div x-show="activeTab === 'ai'" x-cloak class="space-y-6 animate__animated animate__fadeIn">
                    <form action="{{ route('ai.config.update') }}" method="POST">
                        @csrf
                        <div class="panel shadow-lg border-none rounded-2xl p-8 bg-white dark:bg-[#0e1726]">
                            <div class="flex items-center gap-3 mb-10 text-left">
                                <div class="p-3 bg-purple-100 text-purple-600 rounded-2xl shadow-sm">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path d="M12 2v4" />
                                        <path d="m16.2 7.8 2.9-2.9" />
                                        <path d="M18 12h4" />
                                        <path d="m16.2 16.2 2.9 2.9" />
                                        <path d="M12 18v4" />
                                        <path d="m4.9 19.1 2.9-2.9" />
                                        <path d="M2 12h4" />
                                        <path d="m4.9 4.9 2.9 2.9" />
                                    </svg>
                                </div>
                                <div>
                                    {{-- Heading konfigurasi mesin AI dengan manajemen API keys multi-penyedia --}}
                                    <h3
                                        class="text-base font-black italic tracking-wider leading-none dark:text-white">
                                        Konfigurasi Mesin AI</h3>
                                    <p class="text-[10px] font-bold text-gray-400 mt-1 tracking-widest">
                                        Manajemen Kunci API Multi-Penyedia</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 text-left">
                                <div class="space-y-5">
                                    <label class="flex items-center justify-between tracking-widest">
                                        <span class="text-[10px] font-black text-gray-500">Layanan Utama
                                            (L1)</span>
                                        {{-- Badge status layanan: aktif digunakan sistem saat ini --}}
                                        <span
                                            class="text-[9px] bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full font-black italic">Aktif</span>
                                    </label>
                                    <select name="api_primary"
                                        class="form-select rounded-xl py-3 font-bold text-xs shadow-sm dark:bg-[#1b2e4b] dark:border-none dark:text-white">
                                        <option value="gemini"
                                            {{ ($aiConfig->provider_primary ?? '') == 'gemini' ? 'selected' : '' }}>
                                            Gemini 1.5 Flash (Google)</option>
                                        <option value="groq"
                                            {{ ($aiConfig->provider_primary ?? '') == 'groq' ? 'selected' : '' }}>Llama
                                            3 - Groq API</option>
                                    </select>
                                    <input type="password" name="api_key_primary"
                                        class="form-input rounded-xl py-3 shadow-inner font-mono text-xs dark:bg-[#1b2e4b] dark:border-none dark:text-white"
                                        placeholder="••••••••••••••••" value="{{ $aiConfig->key_primary ?? '' }}">
                                </div>

                                <div class="space-y-5">
                                    <label class="flex items-center justify-between tracking-widest">
                                        <span class="text-[10px] font-black text-gray-500">Layanan Cadangan
                                            (L2)</span>
                                        {{-- Badge status layanan: siaga sebagai failsafe --}}
                                        <span
                                            class="text-[9px] bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-black italic">Cadangan</span>
                                    </label>
                                    <select name="api_backup"
                                        class="form-select rounded-xl py-3 font-bold text-xs shadow-sm dark:bg-[#1b2e4b] dark:border-none dark:text-white">
                                        <option value="groq"
                                            {{ ($aiConfig->provider_backup ?? '') == 'groq' ? 'selected' : '' }}>Llama
                                            3 - Groq API</option>
                                        <option value="gemini"
                                            {{ ($aiConfig->provider_backup ?? '') == 'gemini' ? 'selected' : '' }}>
                                            Gemini 1.5 Flash (Google)</option>
                                    </select>
                                    <input type="password" name="api_key_backup"
                                        class="form-input rounded-xl py-3 shadow-inner font-mono text-xs dark:bg-[#1b2e4b] dark:border-none dark:text-white"
                                        placeholder="••••••••••••••••" value="{{ $aiConfig->key_backup ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Threshold Agronomi -->
                        <div
                            class="panel border-l-8 border-amber-500 shadow-lg rounded-2xl p-8 bg-white dark:bg-[#0e1726]">
                            <div class="flex items-center gap-3 mb-8 text-left">
                                <div class="p-3 bg-amber-100 text-amber-600 rounded-2xl shadow-sm">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path d="M12 9v4" />
                                        <path d="M12 17h.01" />
                                        <path
                                            d="m12.8 2.8 7 12.3a2 2 0 0 1-1.7 3H5.9a2 2 0 0 1-1.7-3l7-12.3a2 2 0 0 1 3.4 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-black italic tracking-wider leading-none dark:text-white">
                                    Parameter Kalibrasi Agronomi</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 text-left mb-10">
                                <div class="space-y-6">
                                    <label
                                        class="text-[10px] font-black text-amber-600 flex items-center gap-2 tracking-widest">
                                        <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                                        {{-- Zona Kuning: ambang batas peringatan sebelum status kritis --}}
                                        Ambang Peringatan (Zona Kuning)
                                    </label>
                                    <div class="flex items-center gap-8 px-2">
                                        <input type="range" name="threshold_yellow" min="0" max="100"
                                            x-model="threshold.yellow"
                                            class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-amber-500">
                                        <div class="w-24 text-center font-black bg-amber-50 text-amber-700 py-3 px-4 rounded-2xl border border-amber-200 shadow-sm text-sm"
                                            x-text="threshold.yellow + '%'"></div>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <label
                                        class="text-[10px] font-black text-red-600 flex items-center gap-2 tracking-widest">
                                        <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                                        {{-- Zona Merah: ambang batas kritis yang memicu notifikasi intervensi --}}
                                        Ambang Kritis (Zona Merah)
                                    </label>
                                    <div class="flex items-center gap-8 px-2">
                                        <input type="range" name="threshold_red" min="0" max="100"
                                            x-model="threshold.red"
                                            class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-red-500">
                                        <div class="w-24 text-center font-black bg-red-50 text-red-700 py-3 px-4 rounded-2xl border border-red-200 shadow-sm text-sm"
                                            x-text="threshold.red + '%'"></div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="pt-8 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center">
                                <button type="button" @click="resetThreshold()"
                                    class="text-[10px] font-black text-gray-400 hover:text-red-500 transition-colors underline underline-offset-8 tracking-widest italic">Reset
                                    ke Default</button>
                                <button type="submit"
                                    class="btn btn-primary px-12 rounded-xl shadow-lg shadow-blue-200 flex items-center gap-3 text-[10px] font-black tracking-widest italic">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                    Sinkronisasi Konfigurasi AI
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Script Notification Logic (Toast Only) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Konfigurasi SweetAlert agar hanya berbentuk TOAST di pojok kanan atas
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if (session('success'))
                toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if (session('error') || $errors->any())
                toast.fire({
                    icon: 'error',
                    title: "{{ session('error') ?? 'Input tidak valid. Periksa kembali form Anda.' }}"
                });
            @endif
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('settingsData', () => ({
                activeTab: 'profile', // Default tab selalu ke profil

                threshold: {
                    yellow: {{ $aiConfig->threshold_yellow ?? 85 }},
                    red: {{ $aiConfig->threshold_red ?? 75 }}
                },

                resetThreshold() {
                    if (confirm(
                            'Apakah Anda yakin ingin mengembalikan kalibrasi ke standar PTPN IV?')) {
                        this.threshold.yellow = 85;
                        this.threshold.red = 75;
                    }
                }
            }));
        });
    </script>
</x-layout.default>
