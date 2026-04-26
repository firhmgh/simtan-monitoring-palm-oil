<x-layout.default>
    <!-- Script ApexCharts -->
    <script defer src="/assets/js/apexcharts.js"></script>

    <div x-data="dashboard" class="space-y-6">
        <div>
            <!-- Breadcrumbs -->
            <ul class="flex space-x-2 rtl:space-x-reverse text-xs font-semibold mb-3">
                <li><a href="javascript:;" class="text-primary hover:underline">Beranda</a></li>
                <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 text-gray-400">
                    <span>Dashboard</span>
                </li>
            </ul>
        </div>

        <!-- 1. Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white-light font-heading">Dashboard Monitoring
                </h1>
                <p class="text-gray-600 dark:text-white-dark mt-1">
                    Selamat datang di sistem monitoring areal kelapa sawit TBM III PTPN IV Regional I
                </p>
            </div>
            <!-- AI Status Badge (Professional Touch) -->
            <div class="flex items-center gap-2 px-4 py-2 bg-primary/10 border border-primary/20 rounded-full shadow-sm">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                </span>
                <span class="text-sm font-bold text-primary tracking-tighter font-mono">Neural API:
                    Terhubung</span>
            </div>
        </div>

        <!-- 2. KPI Summary Cards (Solid Style) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Green Card -->
            <div class="rounded-xl shadow-sm p-6 text-white transform hover:scale-[1.02] transition-transform duration-300"
                style="background: linear-gradient(135deg, #00a76f 0%, #007b55 100%);">
                <div class="text-sm font-medium text-white/80 mb-3 tracking-wider">Total Luas Areal</div>
                <div class="text-[32px] font-bold leading-none mb-4">
                    {{-- Backend: {{ number_format($total_luas ?? 15200) }} --}}
                    15.200 <span class="text-lg">Ha</span></div>
                <div class="text-sm text-white/90 font-medium opacity-75 italic">Seluruh Regional I</div>
            </div>

            <!-- Blue Card -->
            <div class="rounded-xl shadow-sm p-6 text-white transform hover:scale-[1.02] transition-transform duration-300"
                style="background: linear-gradient(135deg, #1c64f2 0%, #154ec1 100%);">
                <div class="text-sm font-medium text-white/80 mb-3 tracking-wider">Total Jumlah Pokok</div>
                <div class="text-[32px] font-bold leading-none mb-4">
                    {{-- Backend: {{ $total_pokok ?? '2.1' }} --}}
                    2.1 <span class="text-lg">Juta</span></div>
                <div class="text-sm text-white/90 font-medium opacity-75 italic">Tanaman kelapa sawit</div>
            </div>

            <!-- Orange Card -->
            <div class="rounded-xl shadow-sm p-6 text-white transform hover:scale-[1.02] transition-transform duration-300"
                style="background: linear-gradient(135deg, #f97316 0%, #d95d0d 100%);">
                <div class="text-sm font-medium text-white/80 mb-3 tracking-wider">Areal Produksi</div>
                <div class="text-[32px] font-bold leading-none mb-4">
                    {{-- Backend: {{ number_format($areal_produksi ?? 1120) }} --}}
                    1.120 <span class="text-lg">Ha</span></div>
                <div class="text-sm text-white/90 font-medium opacity-75 italic">Dalam masa TBM III</div>
            </div>

            <!-- Purple Card -->
            <div class="rounded-xl shadow-sm p-6 text-white transform hover:scale-[1.02] transition-transform duration-300"
                style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                <div class="text-sm font-medium text-white/80 mb-3 tracking-wider">Rata-rata Kesehatan</div>
                <div class="text-[32px] font-bold leading-none mb-4">
                    {{-- Backend: {{ $avg_health ?? '82.8' }}% --}}
                    82.8%</div>
                <div class="text-sm text-white/90 font-medium opacity-75 italic">Tingkat kesehatan tanaman</div>
            </div>
        </div>

        <!-- 3. SMART AI DIAGNOSTIC HUB (Scopus-Ready: Interactive DSS) -->
        <div
            class="panel border-0 p-0 overflow-hidden shadow-xl bg-white dark:bg-[#0e1726] rounded-2xl border border-[#ebedf2] dark:border-[#1b2e4b]">
            <!-- Header -->
            <div
                class="p-5 bg-gradient-to-r from-primary/10 to-purple-500/10 border-b border-[#ebedf2] dark:border-[#1b2e4b] flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white animate-pulse"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </div>
                    <div>
                        <h4
                            class="text-[#3b3f5c] dark:text-white-light font-black text-lg leading-none tracking-tighter">
                            Pusat Kendali Diagnostik AI</h4>
                        <p class="text-[#888ea8] text-[10px] font-bold mt-1 tracking-widest">Mesin Inferensi
                            Logika Neural v2.1</p>
                    </div>
                </div>
                <div
                    class="flex items-center gap-4 bg-white dark:bg-black/20 p-2 rounded-lg border border-[#ebedf2] dark:border-white-dark/10">
                    <div class="text-right border-r border-[#ebedf2] dark:border-white-dark/10 pr-3">
                        <span class="text-[9px] font-black text-gray-400 block">Tingkat Keyakinan
                            Inferensi</span>
                        <span class="text-emerald-500 text-lg font-black font-mono"
                            x-text="confidenceScore + '%'"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400">Status Mesin</span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-primary">
                            <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span> Teroptimalisasi
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <!-- LEFT: USER SELECTION (Parameter Input) -->
                    <div class="lg:col-span-3 space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-gray-500 tracking-widest">1. Fokus
                                Diagnostik</label>
                            <select x-model="analysisMode"
                                class="form-select text-xs font-bold bg-gray-50 dark:bg-black/20 border-[#ebedf2] dark:border-[#1b2e4b] rounded-xl py-3 text-gray-800 dark:text-white-light">
                                <option value="multimodal">Fusi Multimodal Terpadu (Seluruh Data)</option>
                                <option value="growth">Analisis Vigor Pertumbuhan (Vegetatif)</option>
                                <option value="survival">Tingkat Kelangsungan Hidup & Risiko Mortalitas</option>
                                <option value="maintenance">Audit Efisiensi Pemeliharaan</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-gray-500 tracking-widest">2. Cakupan
                                Regional</label>
                            <select
                                class="form-select text-xs font-bold bg-gray-50 dark:bg-black/20 border-[#ebedf2] dark:border-[#1b2e4b] rounded-xl py-3 text-gray-800 dark:text-white-light">
                                <option>Seluruh Unit (Regional I)</option>
                                <option>5 Unit Anomali Tertinggi</option>
                                <option>Sektor Pemeliharaan Prioritas</option>
                            </select>
                        </div>
                        <button type="button" @click="runAIInference"
                            class="btn btn-primary w-full py-4 text-xs font-black tracking-[0.2em] shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-3 hover:scale-[1.02]"
                            :class="isThinking ? 'opacity-50 pointer-events-none' : ''">
                            <span x-show="!isThinking">Eksekusi Diagnosis AI →</span>
                            <span x-show="isThinking" class="flex items-center gap-2">Memproses Bobot Neural...</span>
                        </button>
                    </div>

                    <!-- CENTER: AI INFERENCE OUTPUT -->
                    <div class="lg:col-span-6">
                        <div
                            class="h-full p-6 bg-gray-50 dark:bg-black/40 rounded-2xl relative overflow-hidden group border border-primary/20 dark:border-primary/30 flex flex-col justify-center shadow-inner">
                            <div class="absolute inset-0 opacity-[0.1] dark:opacity-[0.03] pointer-events-none"
                                style="background-image: radial-gradient(circle, #4361ee 1px, transparent 1px); background-size: 20px 20px;">
                            </div>

                            <h5
                                class="text-primary text-[10px] font-black mb-4 tracking-[0.3em] flex items-center gap-2 font-mono relative z-10">
                                <span
                                    class="w-2 h-2 rounded-full bg-primary animate-pulse shadow-[0_0_10px_#4361ee]"></span>
                                Output Inferensi Neural:
                            </h5>
                            <div class="relative z-10">
                                <div x-show="!isThinking" class="animate__animated animate__fadeIn">
                                    <p class="text-lg text-slate-800 dark:text-white leading-relaxed font-bold italic tracking-tight"
                                        x-text="aiInferenceText"></p>
                                    <div
                                        class="mt-6 pt-4 border-t border-gray-200 dark:border-white/10 flex items-center justify-between">
                                        <span
                                            class="text-[9px] text-gray-500 dark:text-gray-400 font-bold tracking-widest">ID
                                            Diagnostik: DSS-R1-2026-X99</span>
                                        <span class="text-[9px] bg-primary/20 text-primary px-2 py-1 rounded font-black"
                                            x-text="analysisMode"></span>
                                    </div>
                                </div>
                                <div x-show="isThinking" class="space-y-4">
                                    <div class="h-4 bg-gray-200 dark:bg-white/10 rounded animate-pulse w-full"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-white/10 rounded animate-pulse w-5/6"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-white/10 rounded animate-pulse w-4/6"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: EXPLAINABLE AI (XAI) -->
                    <div class="lg:col-span-3 space-y-5 border-l border-[#ebedf2] dark:border-[#1b2e4b] pl-4">
                        <p class="text-[10px] font-black text-[#888ea8] tracking-[0.3em] mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            Bukti Eksplanatori (XAI)
                        </p>
                        <div class="space-y-5">
                            <template x-for="param in reasoningParams">
                                <div class="group">
                                    <div class="flex justify-between text-[10px] font-black tracking-tighter mb-2">
                                        <span class="text-[#3b3f5c] dark:text-white-light"
                                            x-text="param.label"></span>
                                        <span x-text="param.value" :class="param.color"></span>
                                    </div>
                                    <div
                                        class="h-1.5 bg-[#ebedf2] dark:bg-[#1b2e4b] rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-1000"
                                            :style="`width: ${param.percent}%; background-color: ${param.hex}`">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performa Agregat Table -->
        <div
            class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-xl border border-[#ebedf2] dark:border-[#1b2e4b]">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-[#3b3f5c] dark:text-white-light tracking-tight leading-none">
                    Performa Agregat Keseluruhan</h3>
                <span
                    class="badge bg-primary px-3 py-1 rounded text-[10px] font-black tracking-widest shadow-sm">Periode:
                    Maret 2026</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="bg-gray-50 dark:bg-black/20 text-[#888ea8] text-[10px] font-bold tracking-widest border-b border-[#ebedf2] dark:border-[#1b2e4b]">
                            <th class="py-4 px-4 text-left font-black">Indikator Performa</th>
                            <th class="py-4 px-4 text-center font-black">Rata-rata</th>
                            <th class="py-4 px-4 text-center font-black">Target</th>
                            <th class="py-4 px-4 text-center font-black">Deviasi</th>
                            <th class="py-4 px-4 text-center font-black">Status</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-100 dark:divide-white-dark/5 text-[#3b3f5c] dark:text-white-dark font-semibold">
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-4 font-bold">Laju Pertumbuhan Batang</td>
                            <td class="py-4 px-4 text-center">
                                {{-- Backend: {{ $agregat['avg_girth'] ?? '12.5' }} cm --}}
                                12.5 cm</td>
                            <td class="py-4 px-4 text-center">12.0 cm</td>
                            <td class="py-4 px-4 text-center text-emerald-500 font-black">+0.5</td>
                            <td class="py-4 px-4 text-center">✓</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors text-rose-500">
                            <td class="py-4 px-4 font-bold">Tingkat Kelangsungan Hidup (Pokok Hidup)</td>
                            <td class="py-4 px-4 text-center">
                                {{-- Backend: {{ $agregat['survival_rate'] ?? '97.8' }}% --}}
                                97.8%</td>
                            <td class="py-4 px-4 text-center">98.0%</td>
                            <td class="py-4 px-4 text-center font-black">-0.2</td>
                            <td class="py-4 px-4 text-center">!</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors text-rose-500">
                            <td class="py-4 px-4 font-bold">Efisiensi Pemeliharaan</td>
                            <td class="py-4 px-4 text-center">88.5%</td>
                            <td class="py-4 px-4 text-center">90.0%</td>
                            <td class="py-4 px-4 text-center font-black">-1.5</td>
                            <td class="py-4 px-4 text-center">!</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-4 font-bold">Kesehatan Tajuk (Crown)</td>
                            <td class="py-4 px-4 text-center">4.2 m</td>
                            <td class="py-4 px-4 text-center">4.0 m</td>
                            <td class="py-4 px-4 text-center text-emerald-500 font-black">+0.2</td>
                            <td class="py-4 px-4 text-center">✓</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- 4. SMART VISUALIZATION SECTION -->
        <div class="space-y-6">
            <!-- TARGET VS REALISASI (POPULASI) -->
            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6 tracking-tight">
                    Analisis Efisiensi Kerapatan Tanaman (Target vs Realisasi)</h3>
                <div x-ref="populasiChart"></div>
                <p class="text-[10px] text-gray-400 mt-4 italic">*Target standard agronomi dihitung berdasarkan 143
                    pokok per hektar.</p>
            </div>

            <!-- Trend Luas Areal Tahun Tanam -->
            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6 tracking-tight">
                    Trend Luas Areal per Tahun Tanam</h3>
                <div x-ref="luasTahunTanamChart"></div>
            </div>

            <!-- Trend Luas Areal Tahun Tanam Per Kebun -->
            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6 tracking-tight">
                    Sebaran Luas Areal Tahun Tanam per Unit Kebun</h3>
                <div x-ref="luasKebunChart"></div>
            </div>

            <!-- Peringkat Kondisi Pohon -->
            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6 tracking-tight">
                    Perbandingan TBM per Unit Kebun (Berdasarkan Kondisi Pohon)</h3>
                <div x-ref="kondisiPohonChart"></div>
            </div>

            <!-- Peringkat Pemeliharaan -->
            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6 tracking-tight">
                    Perbandingan TBM per Unit Kebun (Berdasarkan Pemeliharaan)</h3>
                <div x-ref="pemeliharaanChart"></div>
            </div>

            <!-- Korelasi Vegetatif -->
            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6 tracking-tight font-heading">
                    Tren Pengukuran Vegetatif (Analisis Pertumbuhan Historis)</h3>
                <div x-ref="vegetatifChart"></div>
            </div>
        </div>

        <!-- 5. MONITORING TABLE (TERKINI) -->
        <div
            class="bg-white dark:bg-[#0e1726] rounded-xl shadow-md border border-gray-200 dark:border-white-dark/10 p-6">
            <div class="flex items-center justify-between mb-6 border-b border-[#ebedf2] pb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white-light tracking-tight font-heading">
                        Monitoring Areal Terkini</h3>
                    <p class="text-xs text-[#888ea8] font-semibold mt-1">Status terbaru 5 unit kebun Regional I</p>
                </div>
                <a href="/monitoring-areal"
                    class="btn btn-sm btn-outline-primary text-[10px] font-black tracking-widest px-4">Lihat
                    Semua Data →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-semibold">
                    <thead>
                        <tr class="text-[#888ea8] text-[10px] tracking-widest">
                            <th class="text-left py-4 px-4">ID</th>
                            <th class="text-left py-4 px-4">Nama Kebun</th>
                            <th class="text-left py-4 px-4 text-center">Luas (Ha)</th>
                            <th class="text-left py-4 px-4 text-center">Status Kesehatan</th>
                            <th class="text-left py-4 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white-dark/5 text-gray-700 dark:text-white-dark">
                        {{-- 
                        @foreach ($latestKebuns as $kebun)
                        <tr>
                            <td class="py-5 px-4 font-black text-primary italic">{{ $kebun->kebun }}</td>
                            <td class="py-5 px-4 text-center font-black">{{ $kebun->luas_ha }} Ha</td>
                            <td class="py-5 px-4 text-center font-black">Valid</td>
                        </tr>
                        @endforeach 
                        --}}
                        <!-- Baris 1: Baik -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-black/5 transition-colors cursor-pointer">
                            <td class="py-4 px-4 font-black">KBN-001</td>
                            <td class="py-4 px-4">Kebun Sei Dadap</td>
                            <td class="py-4 px-4 text-center font-black">250 Ha</td>
                            <td class="py-4 px-4 text-center">
                                <div class="w-48 mx-auto">
                                    <div
                                        class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-emerald-500 h-full" style="width: 92%"></div>
                                    </div>
                                    <span class="text-[10px] mt-1 block text-emerald-600 dark:text-emerald-400">92%
                                        Optimal</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-[10px] font-black tracking-wider
                            bg-emerald-100 text-emerald-800 
                            dark:bg-emerald-500/20 dark:text-emerald-400">
                                    Baik
                                </span>
                            </td>
                        </tr>

                        <!-- Baris 2: Perhatian -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-black/5 transition-colors cursor-pointer">
                            <td class="py-4 px-4 font-black">KBN-002</td>
                            <td class="py-4 px-4">Kebun Bandar Pulau</td>
                            <td class="py-4 px-4 text-center font-black">180 Ha</td>
                            <td class="py-4 px-4 text-center">
                                <div class="w-48 mx-auto">
                                    <div
                                        class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-orange-500 h-full" style="width: 75%"></div>
                                    </div>
                                    <span class="text-[10px] mt-1 block text-orange-600 dark:text-orange-400">75%
                                        Sub-Optimal</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-[10px] font-black tracking-wider
                            bg-orange-100 text-orange-800 
                            dark:bg-orange-500/20 dark:text-orange-400">
                                    Perhatian
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("alpine:init", () => {

            const vegetatifData = {
                categories: [
                    '2024 | BERGELOMBANG | 190A',
                    '2024 | BERGELOMBANG | 200A',
                    '2024 | BERBUKIT | 210A',
                    '2024 | BERBUKIT | 220A',
                    '2024 | RENDAHAN | 230A',
                    '2024 | RENDAHAN | 240B'
                ],
                series: [{
                        name: 'Lingkar Batang',
                        data: [0.075, 0.082, 0.077, 0.085, 0.090, 0.088]
                    },
                    {
                        name: 'Jumlah Pelepah',
                        data: [0.044, 0.042, 0.043, 0.045, 0.048, 0.047]
                    },
                    {
                        name: 'Panjang Pelepah',
                        data: [0.192, 0.183, 0.190, 0.195, 0.198, 0.194]
                    }
                ]
            };

            const allVegetatifData = vegetatifData.categories.map((cat, idx) => ({
                kebun: cat,
                lingkar_batang: vegetatifData.series[0].data[idx],
                jumlah_pelepah: vegetatifData.series[1].data[idx],
                panjang_pelepah: vegetatifData.series[2].data[idx],
            }));

            function getSortedVegetatifSeries(byField) {
                const sorted = [...allVegetatifData].sort((a, b) => b[byField] - a[byField]);
                return {
                    categories: sorted.map(d => d.kebun),
                    series: [{
                            name: 'Lingkar Batang',
                            data: sorted.map(d => d.lingkar_batang)
                        },
                        {
                            name: 'Jumlah Pelepah',
                            data: sorted.map(d => d.jumlah_pelepah)
                        },
                        {
                            name: 'Panjang Pelepah',
                            data: sorted.map(d => d.panjang_pelepah)
                        }
                    ]
                };
            }
            Alpine.data("dashboard", () => ({
                isThinking: false,
                analysisMode: 'multimodal',
                confidenceScore: 0,
                aiInferenceText: "Mesin neural siaga. Pilih fokus diagnostik dan cakupan regional untuk memulai.",
                reasoningParams: [{
                        label: "Konsistensi Data",
                        value: "Siap",
                        percent: 100,
                        color: "text-primary",
                        hex: "#4361ee"
                    },
                    {
                        label: "Bobot Logika",
                        value: "Siap",
                        percent: 100,
                        color: "text-primary",
                        hex: "#4361ee"
                    },
                    {
                        label: "Mesin Validasi",
                        value: "Aktif",
                        percent: 100,
                        color: "text-primary",
                        hex: "#4361ee"
                    }
                ],

                get korelasiVegetatifChartOptions() {
                    const isDark = this.$store.app.theme === "dark" || this.$store.app.isDarkMode;
                    const textCol = isDark ? '#bfc9d4' : '#3b3f5c';
                    return {
                        series: vegetatifData.series,
                        chart: {
                            type: 'bar',
                            height: 600,
                            width: '100%',
                            stacked: false,
                            events: {
                                legendClick: function(chartContext, seriesIndex) {
                                    const fieldMap = ['lingkar_batang', 'jumlah_pelepah',
                                        'panjang_pelepah'
                                    ];
                                    const selectedField = fieldMap[seriesIndex];
                                    const {
                                        categories,
                                        series
                                    } = getSortedVegetatifSeries(selectedField);
                                    chartContext.updateOptions({
                                        xaxis: {
                                            categories
                                        },
                                        series
                                    });
                                    return false;
                                }
                            },
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    zoom: false,
                                    pan: false,
                                    reset: true
                                }
                            },
                            fontFamily: 'Inter, sans-serif',
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 700
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                borderRadius: 4
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: vegetatifData.categories,
                            labels: {
                                rotate: -45,
                                style: {
                                    fontSize: '10px',
                                    fontWeight: 600,
                                    colors: textCol
                                },
                                formatter: val => val.split("|").map(p => p.trim()).join(" → ")
                            },
                            title: {
                                text: 'Tahun - Kebun - Topografi - Blok',
                                style: {
                                    fontWeight: 600,
                                    color: textCol
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Nilai Pengukuran',
                                style: {
                                    fontWeight: 600,
                                    color: textCol
                                }
                            },
                            labels: {
                                formatter: val => val.toFixed(3),
                                style: {
                                    colors: textCol
                                }
                            }
                        },
                        grid: {
                            borderColor: isDark ? '#191e3a' : '#e0e0e0',
                            strokeDashArray: 4
                        },
                        colors: ['#1E90FF', '#32CD32', '#FF8C00'],
                        legend: {
                            position: 'top',
                            fontSize: '13px',
                            labels: {
                                colors: textCol
                            }
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            theme: isDark ? 'dark' : 'light',
                            y: {
                                formatter: val => `${parseFloat(val).toFixed(3)}`
                            }
                        }
                    };
                },

                //    async runAIInference() {
                //         this.isThinking = true;
                //         try {
                //             // Mengambil data asli dari Controller Laravel
                //             const response = await fetch(`/api/ai/analyze?mode=${this.analysisMode}`);
                //             const data = await response.json();

                //             if(data.status === 'success') {
                //                 this.aiInferenceText = data.inference;
                //                 this.confidenceScore = data.confidence;
                //                 this.reasoningParams = data.params;
                //             } else {
                //                 this.aiInferenceText = "Terjadi gangguan pada mesin inferensi.";
                //             }
                //         } catch (error) {
                //             this.aiInferenceText = "Koneksi ke API terputus. Silakan coba lagi.";
                //         } finally {
                //             this.isThinking = false;
                //         }
                //     },

                runAIInference() {
                    this.isThinking = true;
                    this.confidenceScore = 0;

                    setTimeout(() => {
                        let result = "";
                        let params = [];

                        if (this.analysisMode === 'multimodal') {
                            result =
                                "Analisis Agregat: Kebun Sei Dadap membutuhkan perhatian khusus. Meskipun data vegetatif tinggi, efisiensi perawatan di Sektor 4 rendah akibat anomali curah hujan lokal.";
                            params = [{
                                    label: "Korelasi Silang",
                                    value: "0.88",
                                    percent: 88,
                                    color: "text-emerald-500",
                                    hex: "#10b981"
                                },
                                {
                                    label: "Densitas Anomali",
                                    value: "Tinggi",
                                    percent: 92,
                                    color: "text-rose-500",
                                    hex: "#ef4444"
                                },
                                {
                                    label: "Reliabilitas",
                                    value: "94.2%",
                                    percent: 94,
                                    color: "text-primary",
                                    hex: "#4361ee"
                                }
                            ];
                            this.confidenceScore = 94.2;
                        } else if (this.analysisMode === 'growth') {
                            result =
                                "Wawasan Vigor: Pertumbuhan lingkar batang di Kebun Tanah Itam Ulu mencatatkan rekor regional (+15% YoY). Parameter hara tanah di unit ini layak dijadikan benchmark regional.";
                            params = [{
                                    label: "Momentum Pertumbuhan",
                                    value: "Tinggi",
                                    percent: 95,
                                    color: "text-emerald-500",
                                    hex: "#10b981"
                                },
                                {
                                    label: "Akurasi Vigor",
                                    value: "0.91",
                                    percent: 91,
                                    color: "text-blue-500",
                                    hex: "#3b82f6"
                                },
                                {
                                    label: "Reliabilitas",
                                    value: "91.8%",
                                    percent: 91,
                                    color: "text-primary",
                                    hex: "#4361ee"
                                }
                            ];
                            this.confidenceScore = 91.8;
                        } else if (this.analysisMode === 'survival') {
                            result =
                                "Peringatan Risiko: Bandar Pulau mendeteksi risiko mortalitas bibit sebesar 8% di Blok C. Faktor utama: Akumulasi genangan air (88% korelasi) pada grafik pemeliharaan.";
                            params = [{
                                    label: "Risiko Mortalitas",
                                    value: "Tinggi",
                                    percent: 85,
                                    color: "text-rose-500",
                                    hex: "#ef4444"
                                },
                                {
                                    label: "Hambatan Cuaca",
                                    value: "Ekstrem",
                                    percent: 88,
                                    color: "text-amber-500",
                                    hex: "#f39c12"
                                },
                                {
                                    label: "Reliabilitas",
                                    value: "89.5%",
                                    percent: 89,
                                    color: "text-primary",
                                    hex: "#4361ee"
                                }
                            ];
                            this.confidenceScore = 89.5;
                        }

                        this.aiInferenceText = result;
                        this.reasoningParams = params;
                        this.isThinking = false;
                    }, 1500);
                },

                init() {
                    const isDark = this.$store.app.theme === "dark" || this.$store.app.isDarkMode;

                    // --- VERSI 1: DATA DUMMY (AKTIF) ---
                    const populasiLabels = ['Sei Dadap', 'Bandar Pulau', 'Tanah Itam Ulu', 'Kebun A',
                        'Kebun B'
                    ];
                    const populasiTarget = [35750, 25740, 28600, 21450, 17160]; // Hasil Luas * 143
                    const populasiActual = [32000, 24000, 26500, 21000, 15000];

                    // --- VERSI 2: DATA DARI CHARTDATASERVICE ---
                    /*
                    const populasiLabels = @json($populasiLabels ?? []);
                    const populasiTarget = @json($populasiTarget ?? []);
                    const populasiActual = @json($populasiActual ?? []);
                    */

                    const namaKebunTerbaik = ['Sei Dadap', 'Bandar Pulau', 'Tanah Itam Ulu', 'Kebun A',
                        'Kebun B'
                    ];
                    const persen_pkk_normal = [85, 82, 78, 75, 70];
                    const persen_pkk_non_valuer = [10, 12, 15, 18, 20];
                    const persen_pkk_mati = [5, 6, 7, 7, 10];

                    // 1. Chart Populasi

                    new ApexCharts(this.$refs.populasiChart, {
                        series: [{
                                name: 'Target (Standard 143/Ha)',
                                data: populasiTarget
                            },
                            {
                                name: 'Realisasi (Pokok Normal)',
                                data: populasiActual
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: true
                            },
                            fontFamily: 'Inter, sans-serif'
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                columnWidth: '55%',
                                borderRadius: 4
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: populasiLabels,
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Jumlah Pokok',
                                style: {
                                    color: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            },
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        colors: ['#e2e8f0',
                            '#4361ee'
                        ], // Abu-abu untuk target, Biru untuk realisasi
                        legend: {
                            labels: {
                                colors: isDark ? '#bfc9d4' : '#3b3f5c'
                            }
                        },
                        grid: {
                            borderColor: isDark ? '#191e3a' : '#f0f0f0'
                        }
                    }).render();

                    // 2. Chart: Trend Luas Areal Tahun Tanam
                    new ApexCharts(this.$refs.luasTahunTanamChart, {
                        series: [{
                            name: 'Luas Areal',
                            data: [1200, 1500, 1800, 2100, 2500]
                        }],
                        chart: {
                            type: 'area',
                            height: 360,
                            toolbar: {
                                show: true
                            },
                            fontFamily: 'Inter, sans-serif'
                        },
                        xaxis: {
                            categories: ['2020', '2021', '2022', '2023', '2024'],
                            title: {
                                text: 'Tahun Tanam',
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    color: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            },
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Luas (Ha)',
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    color: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            },
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                },
                                formatter: val => val + ' Ha'
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        colors: ['#4CAF50'],
                        grid: {
                            borderColor: isDark ? '#191e3a' : '#f0f0f0'
                        }
                    }).render();

                    // 3. Chart: Trend Luas Areal Tahun Tanam Per Kebun
                    new ApexCharts(this.$refs.luasKebunChart, {
                        series: [{
                                name: '2022',
                                data: [250, 180, 200, 150, 120]
                            },
                            {
                                name: '2023',
                                data: [300, 220, 240, 190, 160]
                            },
                            {
                                name: '2024',
                                data: [350, 280, 300, 230, 200]
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 400,
                            stacked: true,
                            toolbar: {
                                show: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                borderRadius: 6,
                                barHeight: '90%'
                            }
                        },
                        xaxis: {
                            title: {
                                text: 'Luas (Ha)',
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    color: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            },
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Nama Unit Kebun',
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    color: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            },
                            categories: namaKebunTerbaik,
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            labels: {
                                colors: isDark ? '#bfc9d4' : '#3b3f5c'
                            }
                        },
                        colors: ['#4F81BD', '#A9D18E', '#FFD966'],
                        grid: {
                            borderColor: isDark ? '#191e3a' : '#f0f0f0'
                        }
                    }).render();

                    // 4. Peringkat Kondisi Pohon
                    new ApexCharts(this.$refs.kondisiPohonChart, {
                        series: [{
                                name: 'PKK NORMAL',
                                data: persen_pkk_normal
                            },
                            {
                                name: 'PKK NON VALUER',
                                data: persen_pkk_non_valuer
                            },
                            {
                                name: 'PKK MATI',
                                data: persen_pkk_mati
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 400,
                            stacked: true,
                            toolbar: {
                                show: true
                            },
                            fontFamily: 'Inter, sans-serif'
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                borderRadius: 6,
                                barHeight: '70%',
                                dataLabels: {
                                    position: 'center'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: (val) => val + '%',
                            style: {
                                fontSize: '10px'
                            }
                        },
                        xaxis: {
                            categories: namaKebunTerbaik,
                            min: 0,
                            max: 100,
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            }
                        },
                        colors: ['#27ae60', '#f39c12', '#FF0000'],
                        legend: {
                            position: 'top',
                            labels: {
                                colors: isDark ? '#bfc9d4' : '#3b3f5c'
                            }
                        },
                        grid: {
                            borderColor: isDark ? '#191e3a' : '#f0f0f0'
                        }
                    }).render();

                    // 5. Peringkat Pemeliharaan
                    new ApexCharts(this.$refs.pemeliharaanChart, {
                        series: [{
                                name: 'Kacangan',
                                data: [70, 65, 60, 55, 50]
                            },
                            {
                                name: 'Pemeliharaan Kurang',
                                data: [10, 15, 20, 25, 30]
                            },
                            {
                                name: 'Areal Tergenang',
                                data: [5, 5, 10, 10, 10]
                            },
                            {
                                name: 'Anak Kayu',
                                data: [15, 15, 10, 10, 10]
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 400,
                            stacked: true,
                            toolbar: {
                                show: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                borderRadius: 6,
                                barHeight: '70%'
                            }
                        },
                        xaxis: {
                            categories: namaKebunTerbaik,
                            min: 0,
                            max: 100,
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: isDark ? '#bfc9d4' : '#3b3f5c'
                                }
                            }
                        },
                        colors: ['#27ae60', '#f39c12', '#3498db', '#8B4513'],
                        legend: {
                            position: 'top',
                            labels: {
                                colors: isDark ? '#bfc9d4' : '#3b3f5c'
                            }
                        },
                        grid: {
                            borderColor: isDark ? '#191e3a' : '#f0f0f0'
                        }
                    }).render();

                    // 6. Korelasi Vegetatif
                    new ApexCharts(this.$refs.vegetatifChart, this
                        .korelasiVegetatifChartOptions).render();
                }
            }));
        });
    </script>
</x-layout.default>
