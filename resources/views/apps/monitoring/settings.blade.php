<x-layout.default>
    <div x-data="settingsData()" class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan Sistem</h1>
                <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">Kelola informasi personal, keamanan akun, dan
                    konfigurasi kecerdasan artifisial</p>
            </div>

            <!-- Alert Notifikasi -->
            @if (session('success'))
                <div
                    class="flex items-center p-3.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-sm animate__animated animate__fadeInRight">
                    <span class="ltr:mr-2 rtl:ml-2">✨ {{ session('success') }}</span>
                </div>
            @endif
        </div>

        <!-- Tab Navigation -->
        <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-800">
            <button @click="activeTab = 'profile'"
                :class="activeTab === 'profile' ? 'border-emerald-600 text-emerald-600' :
                    'border-transparent text-gray-500 hover:text-gray-700'"
                class="flex items-center gap-2 px-6 py-3 border-b-2 font-bold text-sm transition-all tracking-wider">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                Profil Pengguna
            </button>
            <button @click="activeTab = 'security'"
                :class="activeTab === 'security' ? 'border-emerald-600 text-emerald-600' :
                    'border-transparent text-gray-500 hover:text-gray-700'"
                class="flex items-center gap-2 px-6 py-3 border-b-2 font-bold text-sm transition-all tracking-wider">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                Keamanan
            </button>

            <!-- Khusus Superadmin (Sesuai Bab 3.6.3.2 No 8) -->
            @if (auth()->user()->hasRole('superadmin'))
                <button @click="activeTab = 'ai'"
                    :class="activeTab === 'ai' ? 'border-emerald-600 text-emerald-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="flex items-center gap-2 px-6 py-3 border-b-2 font-bold text-sm transition-all tracking-wider">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m12 8-9.04 9.06a2.82 2.82 0 1 0 3.98 3.98L16 12" />
                        <circle cx="17" cy="7" r="5" />
                    </svg>
                    Konfigurasi AI & Sistem
                </button>
            @endif
        </div>

        <!-- Tab Content -->
        <div class="mt-4">
            <!-- Profile Tab -->
            <div x-show="activeTab === 'profile'" class="animate__animated animate__fadeIn">
                <div class="panel">
                    <h3 class="text-lg font-bold mb-5 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                        Informasi Personal
                    </h3>
                    <form action="{{ route('settings.update-profile') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-xs font-bold text-gray-400 mb-2 block">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ auth()->user()->name }}"
                                    class="form-input rounded-xl py-3" placeholder="Masukkan nama lengkap">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 mb-2 block">Email
                                    Perusahaan</label>
                                <input type="email" name="email" value="{{ auth()->user()->email }}"
                                    class="form-input rounded-xl py-3" placeholder="email@ptpn4.com">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 mb-2 block">Username</label>
                                <input type="text" value="{{ auth()->user()->username }}"
                                    class="form-input rounded-xl py-3 bg-gray-50 cursor-not-allowed" readonly>
                                <span class="text-[10px] text-gray-400 mt-1 italic italic">Username dikunci oleh sistem
                                    administrator.</span>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 mb-2 block">Otoritas
                                    Akses</label>
                                <div class="mt-2">
                                    <span
                                        class="badge badge-outline-primary py-2 px-4 rounded-full text-[10px] font-black">
                                        {{ auth()->user()->role->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end">
                            <button type="submit"
                                class="btn btn-primary px-10 rounded-xl shadow-lg shadow-emerald-200">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tab -->
            <div x-show="activeTab === 'security'" x-cloak class="animate__animated animate__fadeIn">
                <div class="panel">
                    <h3 class="text-lg font-bold mb-5 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                        Manajemen Kata Sandi
                    </h3>
                    <form action="{{ route('settings.update-password') }}" method="POST" class="max-w-xl space-y-5">
                        @csrf
                        <div>
                            <label class="text-xs font-bold text-gray-400 mb-2 block">Kata Sandi Saat
                                Ini</label>
                            <input type="password" name="current_password" class="form-input rounded-xl py-3"
                                placeholder="••••••••">
                        </div>
                        <hr class="border-gray-100 dark:border-gray-800">
                        <div>
                            <label class="text-xs font-bold text-gray-400 mb-2 block">Kata Sandi Baru</label>
                            <input type="password" name="password" class="form-input rounded-xl py-3"
                                placeholder="Minimal 8 karakter">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 mb-2 block">Konfirmasi Kata Sandi
                                Baru</label>
                            <input type="password" name="password_confirmation" class="form-input rounded-xl py-3"
                                placeholder="Ulangi kata sandi baru">
                        </div>
                        <div class="mt-8">
                            <button type="submit"
                                class="btn btn-primary px-10 rounded-xl shadow-lg shadow-emerald-200">Perbarui
                                Keamanan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- AI Configuration Tab (Superadmin Only) -->
            @if (auth()->user()->hasRole('superadmin'))
                <div x-show="activeTab === 'ai'" x-cloak class="space-y-6 animate__animated animate__fadeIn">
                    <form action="{{ route('ai.config.update') }}" method="POST">
                        @csrf
                        <!-- API Management -->
                        <div class="panel">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-3 bg-purple-100 text-purple-600 rounded-2xl shadow-sm">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
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
                                <h3 class="text-lg font-bold tracking-wider">AI Neural Engine Failsafe</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <label class="flex items-center justify-between">
                                        <span class="text-xs font-black text-gray-500">Layanan Utama
                                            (L1)</span>
                                        <span
                                            class="text-[9px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-black">Online</span>
                                    </label>
                                    <select name="api_primary" class="form-select rounded-xl py-3 font-bold text-xs">
                                        <option value="gemini">Gemini 1.5 Pro (Google DeepMind)</option>
                                        <option value="gpt4">GPT-4o (OpenAI)</option>
                                    </select>
                                    <input type="password" name="api_key_primary" class="form-input rounded-xl py-3"
                                        placeholder="API Key Primer" value="••••••••••••••••">
                                </div>

                                <div class="space-y-4 opacity-70">
                                    <label class="flex items-center justify-between">
                                        <span class="text-xs font-black text-gray-500">Layanan Cadangan
                                            (L2)</span>
                                        <span
                                            class="text-[9px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-black">Standby</span>
                                    </label>
                                    <select name="api_backup" class="form-select rounded-xl py-3 font-bold text-xs">
                                        <option value="groq">Llama 3 - Groq API (High Performance)</option>
                                        <option value="claude">Claude 3.5 Sonnet</option>
                                    </select>
                                    <input type="password" name="api_key_backup" class="form-input rounded-xl py-3"
                                        placeholder="API Key Cadangan" value="••••••••••••••••">
                                </div>
                            </div>
                        </div>

                        <!-- Threshold Agronomi -->
                        <div class="panel border-l-4 border-amber-500">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="p-3 bg-amber-100 text-amber-600 rounded-2xl">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M12 9v4" />
                                        <path d="M12 17h.01" />
                                        <path
                                            d="m12.8 2.8 7 12.3a2 2 0 0 1-1.7 3H5.9a2 2 0 0 1-1.7-3l7-12.3a2 2 0 0 1 3.4 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold tracking-wider">Parameter Kalibrasi Agronomi
                                </h3>
                            </div>

                            <p class="text-xs text-gray-500 mb-8 italic">Threshold ini digunakan sebagai basis logika
                                inferensi AI dalam menentukan status kesehatan blok tanaman TBM III.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                <!-- Batas Kuning -->
                                <div class="space-y-4">
                                    <label class="text-xs font-black text-amber-600 flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                                        Ambang Batas Peringatan (Yellow Zone)
                                    </label>
                                    <div class="flex items-center gap-6">
                                        <input type="range" name="threshold_yellow" min="0" max="100"
                                            x-model="threshold.yellow"
                                            class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-amber-500">
                                        <div class="w-20 text-center font-black bg-amber-50 text-amber-700 py-2 px-3 rounded-xl border border-amber-200 shadow-sm"
                                            x-text="threshold.yellow + '%'"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 leading-relaxed font-medium">Batas
                                        minimum kesehatan tanaman sebelum sistem memberikan diagnosis "PERLU PERHATIAN".
                                    </p>
                                </div>

                                <!-- Batas Merah -->
                                <div class="space-y-4">
                                    <label class="text-xs font-black text-red-600 flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                                        Ambang Batas Kritis (Red Zone)
                                    </label>
                                    <div class="flex items-center gap-6">
                                        <input type="range" name="threshold_red" min="0" max="100"
                                            x-model="threshold.red"
                                            class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-red-500">
                                        <div class="w-20 text-center font-black bg-red-50 text-red-700 py-2 px-3 rounded-xl border border-red-200 shadow-sm"
                                            x-text="threshold.red + '%'"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 leading-relaxed font-medium">Batas
                                        minimum kesehatan dimana blok dianggap "GAGAL/KRITIS" dan membutuhkan audit
                                        agronomi segera.</p>
                                </div>
                            </div>

                            <div class="mt-12 pt-6 border-t border-gray-100 flex justify-between items-center">
                                <button type="button" @click="resetThreshold()"
                                    class="text-[10px] font-black text-gray-400 hover:text-red-500 transition-colors underline underline-offset-4">Reset
                                    ke Default PTPN IV</button>
                                <button type="submit"
                                    class="btn btn-primary px-10 rounded-xl shadow-lg shadow-blue-200 flex items-center gap-2 text-xs font-black tracking-widest">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
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

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('settingsData', () => ({
                activeTab: 'profile',

                threshold: {
                    yellow: 85,
                    red: 75
                },

                resetThreshold() {
                    if (confirm(
                            'Apakah Anda yakin ingin mengembalikan kalibrasi ke standar PTPN IV?')) {
                        this.threshold.yellow = 85;
                        this.threshold.red = 70;
                    }
                }
            }));
        });
    </script>
</x-layout.default>
