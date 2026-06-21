<x-layout.default>
    <!-- Script ApexCharts Engine -->
    <script defer src="{{ asset('assets/js/apexcharts.js') }}"></script>

    <div x-data="dashboard" class="space-y-5 pb-10 font-['Plus_Jakarta_Sans',sans-serif] antialiased text-slate-900 dark:text-white"
        x-init="initDashboard()">

        <!-- 1. HEADER & STATUS SISTEM -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center justify-between">
            <div class="space-y-1">
                <ul class="flex space-x-2 text-xs mb-2 text-white-dark tracking-widest font-black uppercase">
                    <li><a href="{{ route('index') }}" class="text-primary hover:underline font-black">Monitoring</a></li>
                    <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 font-black text-slate-400">Dashboard</li>
                </ul>
                <h1 class="text-2xl md:text-3xl font-black tracking-tighter leading-none italic">
                    Monitoring Panel <span class="text-primary underline decoration-primary/20">Presisi TBM III</span>
                </h1>
                <p class="text-xs font-bold italic text-slate-500 dark:text-slate-400 mt-2 border-l-2 border-primary pl-2 tracking-tight">
                    Sistem Integrasi Terpadu - PTPN IV Regional I
                </p>
            </div>

            <div
                class="flex items-center gap-4 px-4 py-2 bg-white/70 dark:bg-[#0e1726]/70 backdrop-blur-md border border-gray-100/80 dark:border-white-dark/10 rounded-xl shadow-sm hover:shadow-md transition-all font-plus-jakarta">
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 leading-none mb-1 tracking-widest uppercase">
                        Mesin Inferensi
                    </p>
                    <p class="text-xs font-black italic"
                        :class="{
                            'text-rose-500 dark:text-rose-400': !navigator.onLine,
                            'text-amber-500 dark:text-amber-400': isThinking,
                            'text-emerald-500 dark:text-emerald-400': navigator.onLine && !isThinking
                        }"
                        x-text="!navigator.onLine ? 'Terputus' : (isThinking ? 'Sedang Berpikir...' : ($store.app.activeModel || 'Gemini') + ' Terkoneksi')">
                    </p>
                </div>
                <div class="relative flex h-3 w-3 items-center justify-center">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                        :class="{
                            'bg-rose-500': !navigator.onLine,
                            'bg-amber-500': isThinking,
                            'bg-emerald-500': navigator.onLine && !isThinking
                        }"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5"
                        :class="{
                            'bg-rose-500': !navigator.onLine,
                            'bg-amber-500': isThinking,
                            'bg-emerald-500': navigator.onLine && !isThinking
                        }"></span>
                </div>
            </div>
        </div>

        <!-- 2. FILTER DIMENSI WAKTU -->
        <div class="panel border-none shadow-lg bg-white/80 dark:bg-[#0e1726]/80 backdrop-blur-md border border-white/20 dark:border-white-dark/10 rounded-2xl overflow-hidden relative group">
            <div
                class="absolute right-0 top-0 opacity-[0.03] dark:opacity-[0.07] pointer-events-none transform translate-x-10 -translate-y-10">
                <svg class="w-64 h-64 text-primary" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" />
                </svg>
            </div>
            <div class="relative z-10 p-6 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h4 class="text-xl font-black italic tracking-tighter text-slate-900 dark:text-white">
                        Filter Dimensi Temporal</h4>
                    <p class="text-slate-600 dark:text-white/60 text-[10px] font-bold tracking-widest mt-1">
                        Integrasi Data Terpadu & Analisis Kurva Pertumbuhan</p>
                </div>
                <div class="w-full md:w-80">
                    <select x-model="selectedPeriode" @change="changePeriode" class="form-select ...">
                        @foreach ($listPeriode as $slugKey => $info)
                            <option value="{{ $slugKey }}">
                                {{ strtoupper($info['label']) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if (isset($hasData) && $hasData == false)
            <div
                class="flex items-center p-5 mb-5 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-500">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="font-black italic tracking-widest text-xs">Informasi Sistem:</span>
                    <p class="text-sm font-bold">
                        {{ $message ?? 'Data untuk periode ini belum tersedia di database. Silakan pilih periode lain' }}
                    </p>
                </div>
            </div>
        @endif

        <!-- 3. EXECUTIVE KPI CARDS (Dinamis dari Backend) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <template x-for="kpi in kpiCards" :key="kpi.label">
                <div class="panel bg-white dark:bg-[#0e1726] border-none shadow-sm p-4 relative overflow-hidden group hover:shadow-md transition-all border-b-4"
                    :class="kpi.border">
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <div :class="`p-1.5 rounded-lg ${kpi.bg} ${kpi.color}`" x-html="kpi.icon"></div>
                                <span class="text-[10px] font-black text-gray-400 tracking-widest"
                                    x-text="kpi.label"></span>
                            </div>
                            <div class="flex items-baseline gap-1.5">
                                <h2 class="text-3xl font-black tracking-tighter leading-none" x-text="kpi.value"></h2>
                                <span class="text-[10px] font-bold text-gray-400" x-text="kpi.unit"></span>
                            </div>
                        </div>
                        <!-- Micro-Metrics: Perbandingan Terhadap Standar Agronomi -->
                        <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-gray-100 dark:border-white-dark/5">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 leading-none mb-1">Compliance
                                    Rate</p>
                                <p class="text-xs font-black italic"
                                    :class="parseFloat(kpi.compliance) >= 100 ? 'text-emerald-500' : 'text-rose-500'"
                                    x-text="kpi.compliance + '%'"></p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 leading-none mb-1">Status
                                    Standar</p>
                                <p class="text-[10px] font-black italic"
                                    :class="parseFloat(kpi.compliance) >= 100 && parseFloat(kpi.value) > 0 ?
                                        'text-emerald-500' : 'text-amber-500'"
                                    x-text="parseFloat(kpi.compliance) >= 100 && parseFloat(kpi.value) > 0 ? 'Optimal' : 'Deviation'">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- 4. AI EXECUTIVE HUB (Narasi Otomatis) -->
        <div
            class="panel border-none p-0 overflow-hidden shadow-xl bg-white/80 dark:bg-[#0e1726]/80 backdrop-blur-md rounded-2xl border border-white/20 dark:border-white-dark/10">
            <div
                class="p-5 bg-gray-50/50 dark:bg-black/20 border-b border-gray-100 dark:border-white-dark/5 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-primary text-white rounded-2xl flex items-center justify-center shadow-lg shadow-primary/20">
                        <svg class="w-7 h-7" :class="isThinking ? 'animate-spin' : 'animate-pulse'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h4
                            class="text-xl font-black text-slate-900 dark:text-white-light leading-none italic tracking-tighter">
                            Narasi Eksekutif AI</h4>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-gray-400 mt-2 tracking-widest">Inferensi: <span
                                class="text-primary" x-text="periodeLabels[selectedPeriode]"></span></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <select x-model="analysisMode"
                        class="form-select py-2 text-[10px] font-black rounded-xl border-gray-200 dark:bg-black/20 w-44 tracking-widest text-slate-900 dark:text-white">
                        <option value="multimodal">🧠 INTEGRASI DATA</option>
                        <option value="growth">📈 VIGOR TUMBUH</option>
                        <option value="survival">🛡️ MORTALITAS</option>
                    </select>
                    <button @click="runAIInference(true)"
                        class="btn btn-primary btn-sm rounded-xl px-6 font-black text-[10px] italic tracking-[0.2em]">
                        Refresh AI
                    </button>
                </div>
            </div>

            <!-- Body Panel AI -->
            <div class="p-10">
                <div x-show="isThinking" class="space-y-4 max-w-4xl mx-auto">
                    <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded-full w-full animate-pulse"></div>
                    <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded-full w-5/6 animate-pulse"></div>
                    <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded-full w-4/6 animate-pulse"></div>
                </div>

                <div x-show="!isThinking" class="max-w-none prose prose-slate dark:prose-invert">
                    <!-- Konten hasil AI yang sudah di-format -->
                    <div class="ai-content-wrapper text-slate-900 dark:text-gray-300 leading-[1.8] font-medium text-base md:text-lg italic tracking-tight"
                         x-html="formatAiOutput(aiInferenceText)">
                    </div>
                </div>

                <!-- Footer Reasoning Metrics -->
                <div class="mt-12 pt-8 border-t border-gray-100 dark:border-white-dark/5">
                    <h5 class="text-[10px] font-black text-slate-900 dark:text-white-light tracking-[0.3em] mb-6">Logika Pemrosesan AI</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <template x-for="param in reasoningParams" :key="param.label">
                            <div>
                                <div class="flex justify-between text-[10px] font-black mb-2 tracking-widest">
                                    <span class="text-slate-500 dark:text-gray-400" x-text="param.label"></span>
                                    <span x-text="param.value" :class="param.color" class="italic"></span>
                                </div>
                                <div class="h-1.5 bg-gray-100 dark:bg-black/40 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-[2000ms] ease-out"
                                        :style="`width: ${param.percent}%; background: ${param.hex}`"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. MATRIKS PERFORMA AGRONOMI -->
        <div
            class="panel border-none shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-2xl border-t-4 border-primary overflow-hidden">
            <div class="flex items-center justify-between mb-8 border-b border-gray-100 dark:border-white-dark/5 pb-4">
                <h3 class="text-lg font-black text-gray-800 dark:text-white-light italic tracking-tighter">
                    Matriks Performa Agronomi (Tolok Ukur PPKS)
                </h3>
                <div class="px-4 py-1 bg-primary/10 text-primary text-[10px] font-black rounded-lg animate-pulse">
                    Wawasan: <span x-text="agregat.correlation_insight"></span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="w-full text-sm font-bold">
                    <thead>
                        <tr
                            class="bg-gray-50 dark:bg-black/20 text-[#888ea8] text-[10px] font-black tracking-widest border-b border-gray-100 dark:border-white-dark/5">
                            <th class="py-4 px-4 text-left">Indikator Matriks Utama</th>
                            <th class="py-4 px-4 text-center">Rataan Realisasi</th>
                            <th class="py-4 px-4 text-center">Standar Agronomi</th>
                            <th class="py-4 px-4 text-center">Deviasi Komparatif</th>
                            <th class="py-4 px-4 text-center">Tingkat Performa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white-dark/5">
                        <!-- 1. INDEKS VIGOR -->
                        <tr>
                            <td class="py-5 px-4">
                                <span class="block text-xs font-semibold">Indeks Vigor (Batang &
                                    Pelepah)</span>
                                <span class="text-[9px] text-gray-400 font-bold">Sensor Pertumbuhan
                                    Biometrik</span>
                            </td>
                            <td class="py-5 px-4 text-center font-black text-gray-900 dark:text-white text-lg">
                                <span x-text="agregat.vigor_index"></span> <span
                                    class="text-[10px] text-gray-400 font-bold">Poin</span>
                            </td>
                            <td class="py-5 px-4 text-center opacity-50 text-[10px] font-black">Standar TBM
                                III</td>
                            <td class="py-5 px-4 text-center font-black text-lg"
                                :class="agregat.vigor_index >= 90 ? 'text-emerald-500' : 'text-rose-500'">
                                <span x-text="agregat.vigor_index >= 90 ? 'Optimal' : 'Stagnan'"></span>
                            </td>
                            <td class="py-5 px-4 text-center">
                                <span
                                    class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black tracking-widest shadow-sm"
                                    x-text="agregat.vigor_index + '%'"></span>
                            </td>
                        </tr>
                        <!-- 2. SKOR PEMELIHARAAN -->
                        <tr>
                            <td class="py-5 px-4">
                                <span class="block text-xs font-semibold">Skor Pemeliharaan (LCC &
                                    Piringan)</span>
                                <span class="text-[9px] text-gray-400 font-bold">Kualitas Perawatan
                                    Lapangan</span>
                            </td>
                            <td class="py-5 px-4 text-center font-black text-gray-900 dark:text-white text-lg">
                                <span x-text="agregat.maintenance_score"></span> <span
                                    class="text-[10px] text-gray-400 font-bold">Poin</span>
                            </td>
                            <td class="py-5 px-4 text-center opacity-50 text-[10px] font-black">Standar
                                Kultur Teknis</td>
                            <td class="py-5 px-4 text-center font-black text-lg"
                                :class="agregat.maintenance_score >= 90 ? 'text-emerald-500' : 'text-rose-500'">
                                <span x-text="agregat.maintenance_score >= 90 ? 'Standar' : 'Areal Bergulma'"></span>
                            </td>
                            <td class="py-5 px-4 text-center">
                                <span
                                    class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black tracking-widest shadow-sm"
                                    x-text="agregat.maintenance_score + '%'"></span>
                            </td>
                        </tr>
                        <!-- 3. INDEKS RISIKO LINGKUNGAN -->
                        <tr>
                            <td class="py-5 px-4">
                                <span class="block text-xs font-semibold">Risiko Lingkungan
                                    (Tergenang)</span>
                                <span class="text-[9px] text-gray-400 font-bold">Risiko Hipoksia & Pencucian
                                    Hara</span>
                            </td>
                            <td class="py-5 px-4 text-center font-black text-gray-900 dark:text-white text-lg">
                                <span x-text="agregat.risk_index"></span> <span
                                    class="text-[10px] text-gray-400 font-bold">% Luas</span>
                            </td>
                            <td class="py-5 px-4 text-center opacity-50 text-[10px] font-black">Ambang Batas
                                < 2.0%</td>
                            <td class="py-5 px-4 text-center font-black text-lg"
                                :class="agregat.risk_index < 2 ? 'text-emerald-500' : 'text-rose-500'">
                                <span x-text="agregat.risk_index < 2 ? 'Aman' : 'Kritis'"></span>
                            </td>
                            <td class="py-5 px-4 text-center">
                                <span
                                    class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-500 text-[10px] font-black tracking-widest shadow-sm">Audit
                                    Drainase</span>
                            </td>
                        </tr>
                        <!-- 4. PROYEKSI PRODUKTIVITAS -->
                        <tr>
                            <td class="py-5 px-4">
                                <span class="block text-xs font-semibold">Proyeksi Produktivitas (SPH)</span>
                                <span class="text-[9px] text-gray-400 font-bold">Kerapatan Pokok per
                                    Hektar</span>
                            </td>
                            <td class="py-5 px-4 text-center font-black text-gray-900 dark:text-white text-lg">
                                <span x-text="agregat.sph_actual"></span> <span
                                    class="text-[10px] text-gray-400 font-bold">Pkk/Ha</span>
                            </td>
                            <td class="py-5 px-4 text-center opacity-50 text-[10px] font-black">
                                {{ $benchmarks['std_sph'] }} Pkk/Ha</td>
                            <td class="py-5 px-4 text-center font-black text-lg"
                                :class="agregat.sph_actual >= 135 ? 'text-emerald-500' : 'text-rose-500'">
                                <span x-text="agregat.sph_actual - {{ $benchmarks['std_sph'] }}"></span>
                            </td>
                            <td class="py-5 px-4 text-center">
                                <span
                                    class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black tracking-widest shadow-sm">
                                    {{ round(($agregat['sph_actual'] / $benchmarks['std_sph']) * 100, 1) }}%
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. ANALYTICAL CHARTS GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Populasi Performance (Target 143 vs Real) -->
            <div
                class="lg:col-span-2 panel border-none shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-2xl border-l-4 border-primary">
                <h5
                    class="text-sm font-black text-gray-800 dark:text-white-light mb-6 italic tracking-widest border-b pb-2">
                    Analisis Densitas Populasi: Standar 143 Pkk/Ha vs Lapangan</h5>
                <div x-ref="populasiChart"></div>
            </div>

            <!-- Kesehatan (Sorted Exception) -->
            <div
                class="panel border-none shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-2xl border-l-4 border-emerald-500">
                <h5
                    class="text-sm font-black text-gray-800 dark:text-white-light mb-6 italic tracking-widest border-b pb-2">
                    Stratifikasi Kesehatan</h5>
                <div x-ref="kondisiPohonChart"></div>
            </div>

            <!-- Pemeliharaan -->
            <div
                class="panel border-none shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-2xl border-l-4 border-amber-500">
                <h5
                    class="text-sm font-black text-gray-800 dark:text-white-light mb-6 italic tracking-widest border-b pb-2">
                    Evaluasi Intensitas Pemeliharaan LCC & Gulma</h5>
                <div x-ref="pemeliharaanChart"></div>
            </div>

            <!-- Dinamika Luas (DIUBAH KE BAR SESUAI ANALISA ILMIAH) -->
            <div
                class="panel border-none shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-2xl border-l-4 border-indigo-500">
                <h5
                    class="text-sm font-black text-gray-800 dark:text-white-light mb-6 italic tracking-widest border-b pb-2">
                    Dinamika Luas Areal Per Tahun Tanam</h5>
                <div x-ref="trendLuasChart"></div>
            </div>

            <!-- Vegetatif -->
            <div
                class="panel border-none shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-2xl border-l-4 border-blue-500">
                <h5
                    class="text-sm font-black text-gray-800 dark:text-white-light mb-6 italic tracking-widest border-b pb-2">
                    Analisis Biometrik Vegetatif (Lingkar & Pelepah)</h5>
                <div x-ref="vegetatifChart"></div>
            </div>

            <!-- Distribusi Spasial -->
            <div
                class="lg:col-span-2 panel border-none shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-2xl border-l-4 border-rose-500">
                <h5
                    class="text-sm font-black text-gray-800 dark:text-white-light mb-6 italic tracking-widest border-b pb-2">
                    Distribusi Spasial Luas Areal Per Kebun</h5>
                <div x-ref="luasPerKebunChart"></div>
            </div>
        </div>

        <!-- 7. MONITORING TABLE (Exception: 5 Kebun Terburuk) -->
        <div class="panel border-none bg-white dark:bg-[#0e1726] rounded-2xl shadow-xl p-8 overflow-hidden">
            <div class="flex items-center justify-between mb-8 border-b border-gray-100 dark:border-white-dark/5 pb-5">
                <div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white-light tracking-tighter italic">
                        Unit Prioritas Intervensi (Exception Reporting)</h3>
                    <p class="text-[10px] text-gray-400 font-black tracking-widest mt-1">Daftar 5 Unit dengan
                        Deviasi Kesehatan Tertinggi Periode Saat Ini</p>
                </div>
                <a href="{{ route('monitoring.data-kebun') }}"
                    class="btn btn-outline-primary border-2 px-6 py-2.5 rounded-xl font-black text-[10px] italic tracking-widest shadow-sm">Review
                    Seluruh Areal</a>
            </div>
            <div class="table-responsive">
                <table class="w-full text-xs font-bold tracking-tight">
                    <thead>
                        <tr
                            class="text-gray-400 text-[10px] border-b border-gray-50 dark:border-white-dark/5 font-black tracking-[0.1em]">
                            <th class="text-left py-4 px-4">Unit</th>
                            <th class="text-left py-4 px-4">Nama Kebun</th>
                            <th class="text-center py-4 px-4">Luas (Ha)</th>
                            <th class="text-center py-4 px-4">Health Rate</th>
                            <th class="text-center py-4 px-4">Status Diagnosa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white-dark/5">
                        @foreach ($latestKebun as $kebun)
                            <tr class="hover:bg-gray-50 dark:hover:bg-black/10 transition-colors cursor-pointer group">
                                <td class="py-5 px-4 font-black tracking-widest text-sm">
                                    @if ($kebun->kebun_id)
                                        <a href="{{ route('monitoring.detail', ['id' => $kebun->kebun_id, 'periode' => $activeSlug]) }}"
                                            class="text-primary hover:text-primary-dark hover:underline transition-all duration-300">
                                            {{ $kebun->kebun }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 cursor-help"
                                            title="Data master lokasi belum tersedia">{{ $kebun->kebun }}</span>
                                    @endif
                                </td>
                                <td class="py-5 px-4 text-slate-700 dark:text-white-light">
                                    {{ $kebun->nama_kebun }}</td>
                                <td class="py-5 px-4 text-center font-black text-sm">
                                    {{ number_format($kebun->luas_ha ?? 0, 1) }} </td>
                                <td class="py-5 px-4 text-center">
                                    <span
                                        class="text-lg font-black {{ $kebun->persen_pkk_normal < 85 ? 'text-rose-500' : 'text-amber-500' }}">
                                        {{ $kebun->persen_pkk_normal }}%
                                    </span>
                                </td>
                                <td class="py-5 px-4 text-center">
                                    <span
                                        class="inline-block px-4 py-1.5 rounded-full text-[9px] font-black tracking-widest 
                                        {{ $kebun->status_kesehatan === 'Critical'
                                            ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400'
                                            : ($kebun->status_kesehatan === 'Warning'
                                                ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400'
                                                : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400') }}">
                                        {{ strtoupper($kebun->status_kesehatan) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            document.addEventListener("alpine:init", () => {
                Alpine.data("dashboard", () => ({
                    selectedPeriode: '{{ $activeSlug }}',
                    periodeLabels: @json(collect($listPeriode)->mapWithKeys(fn($v, $k) => [$k => $v['label']])),
                    hasData: {{ $hasData ? 'true' : 'false' }},
                    isThinking: false,
                    analysisMode: 'multimodal',
                    aiInferenceText: "{{ $hasData ? 'Sinkronisasi Integrasi Data Terpadu...' : 'Sistem Standby: Dataset untuk periode ini belum tersedia di database.' }}",
                    agregat: @json($agregat ?? []),

                    kpiCards: [{
                            label: 'Cakupan Areal Total',
                            value: '{{ number_format($total_luas ?? 0, 2) }}',
                            unit: 'Ha',
                            bg: 'bg-blue-500/10',
                            color: 'text-blue-500',
                            border: 'border-blue-500/30',
                            compliance: '100.0', // Luas selalu 100% terhadap dirinya sendiri
                            icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>'
                        },
                        {
                            label: 'Populasi Aktif',
                            value: '{{ number_format($total_pokok ?? 0) }}',
                            unit: 'Pkk',
                            bg: 'bg-indigo-500/10',
                            color: 'text-indigo-500',
                            border: 'border-indigo-500/30',
                            // AMBIL VARIABEL YANG SUDAH DIHITUNG DI CONTROLLER
                            compliance: '{{ $populasi_compliance }}',
                            icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>'
                        },
                        {
                            label: 'Indeks Kesehatan',
                            value: '{{ $avg_health }}',
                            unit: '%',
                            bg: 'bg-emerald-500/10',
                            color: 'text-emerald-500',
                            border: 'border-emerald-500/30',
                            // AMBIL VARIABEL YANG SUDAH DIHITUNG DI CONTROLLER
                            compliance: '{{ $health_compliance }}',
                            icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>'
                        },
                        {
                            label: 'Agronomy Compliance',
                            value: '{{ $agregat['compliance_rate'] }}',
                            unit: '%',
                            bg: 'bg-purple-500/10',
                            color: 'text-purple-500',
                            border: 'border-purple-500/30',
                            compliance: '{{ $agregat['compliance_rate'] }}',
                            icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                        }
                    ],

                    reasoningParams: [{
                            label: "Data Quality Engine",
                            value: "{{ $hasData ? 'Verified' : 'Wait' }}",
                            percent: {{ $hasData ? 96 : 0 }},
                            color: "{{ $hasData ? 'text-emerald-500' : 'text-gray-400' }}",
                            hex: "#10b981"
                        },
                        {
                            label: "Integrasi Data Terpadu",
                            value: "{{ $hasData ? 'Stable' : 'Wait' }}",
                            percent: {{ $hasData ? 88 : 0 }},
                            color: "{{ $hasData ? 'text-primary' : 'text-gray-400' }}",
                            hex: "#4361ee"
                        },
                        {
                            label: "Anomaly Detection",
                            value: "{{ $hasData ? 'Active' : 'Wait' }}",
                            percent: {{ $hasData ? 92 : 0 }},
                            color: "{{ $hasData ? 'text-amber-500' : 'text-gray-400' }}",
                            hex: "#f59e0b"
                        }
                    ],

                    changePeriode() {
                        this.isThinking = true;
                        window.location.href = window.location.pathname + "?periode=" + this
                            .selectedPeriode;
                    },

                    /**
                     * FUNGSI PARSER UNTUK MEMBERSIHKAN TEKS AI
                     */
                    formatAiOutput(text) {
                        if (!text) return "Memproses narasi...";

                        let cleanText = text.replace(/\*\*(.*?)\*\*/g,
                            '<strong class="text-slate-900 dark:text-white font-extrabold">$1</strong>');

                        let lines = cleanText.split('\n');
                        let html = '';

                        lines.forEach(line => {
                            let trimmed = line.trim();
                            if (!trimmed) return;

                            let isListItem = false;
                            let content = trimmed;

                            if (trimmed.startsWith('+') || trimmed.startsWith('-') || trimmed.startsWith('*')) {
                                isListItem = true;
                                content = trimmed.replace(/^[\+\-\*]\s*/, '').trim();
                            }

                            if (isListItem) {
                                html += `
                                    <div class="bg-slate-50/50 dark:bg-white/5 shadow-sm border-l-4 border-emerald-500 p-3 mb-2 rounded-r-xl flex items-start gap-3 transition-all hover:bg-slate-100/50 dark:hover:bg-white/10">
                                        <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="text-slate-700 dark:text-gray-300 text-xs md:text-sm font-medium leading-relaxed">${content}</div>
                                    </div>`;
                            } else if (trimmed.endsWith(':')) {
                                html += `<h5 class="text-xs md:text-sm font-extrabold text-slate-950 dark:text-white uppercase tracking-wider mt-4 mb-2 border-b border-gray-100 dark:border-white-dark/5 pb-1">${trimmed}</h5>`;
                            } else {
                                html += `<p class="text-xs md:text-sm text-slate-700 dark:text-gray-300 leading-relaxed mb-3">${trimmed}</p>`;
                            }
                        });

                        return html;
                    },

                    initDashboard() {
                        this.renderCharts();

                        // Jika data tidak ada, munculkan notifikasi mengambang (Toast)
                        @if (!$hasData)
                            const toast = window.Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true,
                            });
                            toast.fire({
                                icon: 'warning',
                                title: 'Data Kosong',
                                text: 'Periode {{ $activeSlug }} tidak ditemukan di database.',
                                padding: '10px 20px'
                            });
                        @else
                            setTimeout(() => this.runAIInference(), 1500);
                        @endif
                    },

                    async runAIInference(isManual = false) {
                        // Jika data tidak ada, jangan paksa kirim ke API
                        if (!this.hasData) {
                            this.aiInferenceText =
                                "Analisis dihentikan: Dataset periode ini kosong. Silakan pilih dimensi waktu lain.";
                            return;
                        }

                        this.isThinking = true;
                        try {
                            // 1. Ambil URL dasar dari route Laravel
                            let baseUrl = "{{ route('ai.analyze.dashboard') }}";

                            // 2. Susun Query Parameters (Mode + Periode)
                            let params = new URLSearchParams({
                                mode: this.analysisMode,
                                periode: this.selectedPeriode
                            });

                            // 3. Jika diklik manual lewat tombol Refresh, tambahkan flag refresh untuk hapus cache
                            if (isManual) {
                                params.append('refresh', '1');
                            }

                            // 4. Eksekusi Fetch
                            const res = await fetch(`${baseUrl}?${params.toString()}`);
                            const data = await res.json();

                            if (data.status === 'success') {
                                this.aiInferenceText = data.narration;
                            } else {
                                this.aiInferenceText = "Neural Engine gagal sinkronisasi: " + data
                                    .message;
                            }
                        } catch (e) {
                            console.error(e);
                            this.aiInferenceText = "Error memproses narasi biometrik.";
                        } finally {
                            this.isThinking = false;
                        }
                    },

                    renderCharts() {
                        const isDark = this.$store.app.theme === "dark" || this.$store.app.isDarkMode;
                        const premiumTooltip = {
                            theme: 'dark',
                            style: {
                                fontFamily: 'Plus Jakarta Sans, sans-serif'
                            },
                            x: {
                                show: true
                            },
                            marker: {
                                show: true
                            }
                        };
                        const commonAxis = {
                            labels: {
                                style: {
                                    colors: isDark ? '#888ea8' : '#475569',
                                    fontSize: '10px',
                                    fontWeight: 700
                                }
                            }
                        };

                        // 1. Populasi Chart (Target 143 vs Actual)
                        new ApexCharts(this.$refs.populasiChart, {
                            series: [{
                                name: 'Standar (143/Ha)',
                                data: @json($populasiTarget ?? [])
                            }, {
                                name: 'Realisasi Lapangan',
                                data: @json($populasiActual ?? [])
                            }],
                            chart: {
                                type: 'bar',
                                height: (@json($populasiLabels ?? []).length * 40) + 100,
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: [isDark ? '#334155' : '#e2e8f0', '#4361ee'],
                            plotOptions: {
                                bar: {
                                    horizontal: true,
                                    barHeight: '65%',
                                    borderRadius: 4,
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            xaxis: {
                                ...commonAxis,
                                categories: @json($populasiLabels ?? []),
                                title: {
                                    text: 'Jumlah Pokok (Pkk)',
                                    style: {
                                        color: isDark ? '#888ea8' : '#475569',
                                        fontWeight: 700
                                    }
                                }
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        fontSize: '10px',
                                        fontWeight: 800,
                                        colors: isDark ? '#cbd5e1' : '#1e293b'
                                    }
                                }
                            },
                            tooltip: {
                                ...premiumTooltip,
                                y: {
                                    formatter: (val) => new Intl.NumberFormat('id-ID').format(val) +
                                        ' Pkk'
                                }
                            },
                            legend: {
                                position: 'top',
                                horizontalAlign: 'right',
                                labels: {
                                    colors: isDark ? '#fff' : '#000'
                                }
                            }
                        }).render();

                        // 2. Kondisi Pohon 
                        const kData = @json($peringkatKondisiPohonChartData ?? []);
                        new ApexCharts(this.$refs.kondisiPohonChart, {
                            series: [{
                                    name: 'NORMAL',
                                    data: kData.map(i => i.normal)
                                },
                                {
                                    name: 'KERDIL (WARNING)',
                                    data: kData.map(i => i.non_valuer)
                                },
                                {
                                    name: 'MATI (CRITICAL)',
                                    data: kData.map(i => i.mati)
                                }
                            ],
                            chart: {
                                type: 'bar',
                                height: kData.length * 45 + 100,
                                stacked: true,
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#2ecc71', '#f39c12', '#ef4444'],
                            plotOptions: {
                                bar: {
                                    horizontal: true,
                                    barHeight: '75%'
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: v => v > 5 ? v.toFixed(1) + '%' : '',
                                style: {
                                    fontSize: '9px'
                                }
                            },
                            xaxis: {
                                ...commonAxis,
                                categories: kData.map(i => i.kebun),
                                max: 100
                            },
                            yaxis: commonAxis,
                            tooltip: {
                                ...premiumTooltip,
                                y: {
                                    formatter: v => v.toFixed(2) + '%'
                                }
                            }
                        }).render();

                        // 3. Pemeliharaan
                        const pData = @json($peringkatPemeliharaanChartData ?? []);
                        new ApexCharts(this.$refs.pemeliharaanChart, {
                            series: [{
                                    name: 'Kacangan (LCC)',
                                    data: pData.map(i => i.kacangan)
                                },
                                {
                                    name: 'Kurang Baik',
                                    data: pData.map(i => i.pemeliharaan)
                                },
                                {
                                    name: 'Tergenang',
                                    data: pData.map(i => i.tergenang)
                                },
                                {
                                    name: 'Anak Kayu',
                                    data: pData.map(i => i.anak_kayu)
                                }
                            ],
                            chart: {
                                type: 'bar',
                                height: pData.length * 45 + 100,
                                stacked: true,
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#2ecc71', '#f39c12', '#3498db', '#795548'],
                            plotOptions: {
                                bar: {
                                    horizontal: true,
                                    barHeight: '75%'
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: v => v > 5 ? v.toFixed(1) + '%' : '',
                                style: {
                                    fontSize: '9px'
                                }
                            },
                            xaxis: {
                                ...commonAxis,
                                categories: pData.map(i => i.kebun),
                                max: 100
                            },
                            yaxis: commonAxis,
                            tooltip: {
                                ...premiumTooltip,
                                y: {
                                    formatter: v => v.toFixed(2) + '%'
                                }
                            }
                        }).render();

                        // 4. Vegetatif 
                        new ApexCharts(this.$refs.vegetatifChart, {
                            series: [{
                                name: 'Rasio Lingkar Batang (LB/KC)',
                                data: @json($korelasiVegetatifLingkarBatang ?? [])
                            }, {
                                name: 'Indeks Jumlah Pelepah (JP/KC)',
                                data: @json($korelasiVegetatifJumlahPelepah ?? [])
                            }, {
                                name: 'Rasio Panjang Pelepah (PP/KC)',
                                data: @json($korelasiVegetatifPanjangPelepah ?? [])
                            }],
                            chart: {
                                type: 'bar',
                                height: 450,
                                toolbar: {
                                    show: true
                                }
                            },
                            colors: ['#3498db', '#2ecc71', '#e67e22'],
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '65%',
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            xaxis: {
                                categories: @json($korelasiVegetatifLabels ?? []),
                                labels: {
                                    rotate: -45,
                                    rotateAlways: true,
                                    minHeight: 100,
                                    style: {
                                        fontSize: '10px',
                                        fontWeight: 700,
                                        colors: isDark ? '#888ea8' : '#475569'
                                    }
                                }
                            },
                            yaxis: {
                                labels: {
                                    formatter: (val) => val.toFixed(3),
                                    style: {
                                        colors: isDark ? '#888ea8' : '#475569'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: false,
                            },
                            tooltip: {
                                ...premiumTooltip,
                                y: {
                                    formatter: (val) => val.toFixed(3)
                                }
                            },
                            legend: {
                                labels: {
                                    colors: isDark ? '#fff' : '#000'
                                }
                            }
                        }).render();

                        // 5. Trend Luas (DIUBAH KE BAR)
                        new ApexCharts(this.$refs.trendLuasChart, {
                            series: [{
                                name: 'Luas Areal (Ha)',
                                data: @json($totalLuas ?? [])
                            }],
                            chart: {
                                type: 'bar',
                                height: 350,
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#10b981'],
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '50%',
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            xaxis: {
                                ...commonAxis,
                                categories: @json($tahunTanam ?? [])
                            },
                            yaxis: commonAxis,
                            dataLabels: {
                                enabled: true,
                                offsetY: -25,
                                formatter: v => v.toFixed(0) + ' Ha',
                                style: {
                                    fontSize: '10px',
                                    colors: [isDark ? '#fff' : '#000']
                                }
                            },
                            tooltip: {
                                ...premiumTooltip,
                                y: {
                                    formatter: v => v.toFixed(2) + ' Ha'
                                }
                            }
                        }).render();

                        // 6. Luas Per Kebun
                        new ApexCharts(this.$refs.luasPerKebunChart, {
                            series: @json($series ?? []),
                            chart: {
                                type: 'bar',
                                height: 500,
                                stacked: true,
                                toolbar: {
                                    show: false
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: true,
                                    barHeight: '80%',
                                    borderRadius: 4
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val) {
                                    return val > 100 ? `${val.toFixed(2)} Ha` : '';
                                }
                            },
                            xaxis: {
                                ...commonAxis,
                                categories: @json($namaKebunTerluas ?? [])
                            },
                            yaxis: commonAxis,
                            tooltip: {
                                ...premiumTooltip,
                                y: {
                                    formatter: v => v.toFixed(2) + ' Ha'
                                }
                            },
                            legend: {
                                labels: {
                                    colors: isDark ? '#fff' : '#000'
                                }
                            }
                        }).render();
                    }
                }));
            });
        </script>

        <style>
            .panel {
                @apply shadow-[0_10px_30px_rgba(0, 0, 0, 0.04)] dark:shadow-[0_10px_30px_rgba(0, 0, 0, 0.2)];
            }

            .apexcharts-canvas {
                margin: 0 auto;
            }

            .apexcharts-tooltip.apexcharts-theme-dark {
                background: #000000 !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
                border-radius: 12px !important;
            }

            .apexcharts-tooltip-title {
                background: #111111 !important;
                border-bottom: 1px solid #333 !important;
                font-weight: 900 !important;
                text-transform: !important;
            }

            .table-responsive::-webkit-scrollbar {
                height: 6px;
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background: #e2e8f0;
                border-radius: 10px;
            }

            .dark .table-responsive::-webkit-scrollbar-thumb {
                background: #1b2e4b;
            }
        </style>
    </div>
</x-layout.default>
