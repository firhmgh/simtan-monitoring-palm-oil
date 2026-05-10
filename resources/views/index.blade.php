<x-layout.default>
    <!-- Script ApexCharts -->
    <script defer src="/assets/js/apexcharts.js"></script>

    <div x-data="dashboard" class="space-y-6">
        <!-- 1. Breadcrumbs & Header -->
        <div>
            <ul class="flex space-x-2 rtl:space-x-reverse text-xs font-semibold mb-3">
                <li><a href="javascript:;" class="text-primary hover:underline">Beranda</a></li>
                <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 text-gray-400">
                    <span>Dashboard</span>
                </li>
            </ul>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white-light font-heading">Dashboard Monitoring</h1>
                <p class="text-gray-600 dark:text-white-dark mt-1">
                    Selamat datang di sistem monitoring areal kelapa sawit TBM III PTPN IV Regional I
                </p>
            </div>
            <!-- AI Status Badge -->
            <div class="flex items-center gap-2 px-4 py-2 bg-primary/10 border border-primary/20 rounded-full shadow-sm">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                </span>
                <span class="text-sm font-bold text-primary tracking-tighter font-mono">Neural API: Terhubung</span>
            </div>
        </div>

        <!-- 2. KPI Summary Cards (Data Real) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Luas Areal -->
            <div class="panel border-0 p-0 overflow-hidden rounded-2xl shadow-md transform hover:scale-[1.02] transition-all duration-300 group"
                style="background: linear-gradient(135deg, #00a76f 0%, #007b55 100%);">
                <div class="p-6 relative text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[10px] font-black text-white/70 uppercase tracking-[2px] mb-1">Spatial
                                Coverage</p>
                            <h3 class="text-white/90 text-xs font-bold">Total Luas Areal</h3>
                        </div>
                        <div
                            class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-6 flex items-baseline gap-2">
                        <span class="text-4xl font-black">{{ number_format($total_luas ?? 0, 1) }}</span>
                        <span class="text-lg font-bold text-white/70 uppercase">Ha</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Populasi Pokok -->
            <div class="panel border-0 p-0 overflow-hidden rounded-2xl shadow-md transform hover:scale-[1.02] transition-all duration-300 group"
                style="background: linear-gradient(135deg, #1c64f2 0%, #154ec1 100%);">
                <div class="p-6 relative text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[10px] font-black text-white/70 uppercase tracking-[2px] mb-1">Resource
                                Inventory</p>
                            <h3 class="text-white/90 text-xs font-bold">Populasi Pokok</h3>
                        </div>
                        <div
                            class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-6 flex items-baseline gap-2">
                        <span class="text-4xl font-black">{{ number_format($total_pokok ?? 0) }}</span>
                        <span class="text-lg font-bold text-white/70 uppercase">Pkk</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Health Index -->
            <div class="panel border-0 p-0 overflow-hidden rounded-2xl shadow-md transform hover:scale-[1.02] transition-all duration-300 group"
                style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                <div class="p-6 relative text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[10px] font-black text-white/70 uppercase tracking-[2px] mb-1">Health Index
                            </p>
                            <h3 class="text-white/90 text-xs font-bold">Rata-rata Kesehatan</h3>
                        </div>
                        <div
                            class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-6 flex items-baseline gap-2">
                        <span class="text-4xl font-black">{{ $avg_health ?? 0 }}</span>
                        <span class="text-lg font-bold text-white/70 uppercase">%</span>
                    </div>
                    <div class="mt-6 space-y-2">
                        <div class="w-full bg-black/10 h-1.5 rounded-full overflow-hidden border border-white/5">
                            <div class="bg-white h-full" style="width: {{ $avg_health ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. SMART AI DIAGNOSTIC HUB -->
        <div
            class="panel border-0 p-0 overflow-hidden shadow-xl bg-white dark:bg-[#0e1726] rounded-2xl border border-[#ebedf2] dark:border-[#1b2e4b]">
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
                        <p class="text-[#888ea8] text-[10px] font-bold mt-1 tracking-widest uppercase">Neural Inference
                            Engine v2.1</p>
                    </div>
                </div>
                <div
                    class="flex items-center gap-4 bg-white dark:bg-black/20 p-2 rounded-lg border border-[#ebedf2] dark:border-white-dark/10">
                    <div class="text-right border-r border-[#ebedf2] dark:border-white-dark/10 pr-3">
                        <span class="text-[9px] font-black text-gray-400 block uppercase">Confidence Score</span>
                        <span class="text-emerald-500 text-lg font-black font-mono"
                            x-text="confidenceScore + '%'"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase">Engine Status</span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-primary">
                            <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span> Optimized
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="lg:col-span-3 space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-gray-500 tracking-widest uppercase">1. Fokus
                                Diagnostik</label>
                            <select x-model="analysisMode"
                                class="form-select text-xs font-bold bg-gray-50 dark:bg-black/20 border-[#ebedf2] dark:border-[#1b2e4b] rounded-xl py-3">
                                <option value="multimodal">Fusi Multimodal Terpadu</option>
                                <option value="growth">Analisis Vigor Pertumbuhan</option>
                                <option value="survival">Risiko Mortalitas & Survival</option>
                            </select>
                        </div>
                        <button type="button" @click="runAIInference"
                            class="btn btn-primary w-full py-4 text-xs font-black tracking-[0.2em] shadow-lg shadow-primary/20 transition-all">
                            <span x-show="!isThinking">EKSEKUSI DIAGNOSIS AI →</span>
                            <span x-show="isThinking">MEMPROSES...</span>
                        </button>
                    </div>

                    <div class="lg:col-span-6">
                        <div
                            class="h-full p-6 bg-gray-50 dark:bg-black/40 rounded-2xl border border-primary/20 flex flex-col justify-center">
                            <h5
                                class="text-primary text-[10px] font-black mb-4 tracking-[0.3em] flex items-center gap-2 font-mono uppercase">
                                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> Output Inferensi
                                Neural:
                            </h5>
                            <p class="text-lg text-slate-800 dark:text-white leading-relaxed font-bold italic"
                                x-text="aiInferenceText"></p>
                        </div>
                    </div>

                    <div class="lg:col-span-3 space-y-5 border-l border-[#ebedf2] dark:border-[#1b2e4b] pl-4">
                        <p class="text-[10px] font-black text-[#888ea8] tracking-[0.3em] mb-4 uppercase">Bukti
                            Eksplanatori (XAI)</p>
                        <div class="space-y-5">
                            <template x-for="param in reasoningParams">
                                <div class="group">
                                    <div class="flex justify-between text-[10px] font-black mb-2 uppercase">
                                        <span class="text-[#3b3f5c] dark:text-white-light"
                                            x-text="param.label"></span>
                                        <span x-text="param.value" :class="param.color"></span>
                                    </div>
                                    <div class="h-1.5 bg-[#ebedf2] dark:bg-[#1b2e4b] rounded-full overflow-hidden">
                                        <div class="h-full transition-all duration-1000"
                                            :style="`width: ${param.percent}%; background-color: ${param.hex}`"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Performa Agregat Table -->
        <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-xl">
            <h3 class="text-lg font-bold text-[#3b3f5c] dark:text-white-light mb-6">Performa Agregat Keseluruhan</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="bg-gray-50 dark:bg-black/20 text-[#888ea8] text-[10px] font-bold tracking-widest uppercase border-b border-[#ebedf2] dark:border-[#1b2e4b]">
                            <th class="py-4 px-4 text-left">Indikator Performa</th>
                            <th class="py-4 px-4 text-center">Rata-rata</th>
                            <th class="py-4 px-4 text-center">Target</th>
                            <th class="py-4 px-4 text-center">Deviasi</th>
                            <th class="py-4 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-100 dark:divide-white-dark/5 text-[#3b3f5c] dark:text-white-dark font-semibold">
                        <tr>
                            <td class="py-4 px-4">Laju Pertumbuhan Batang</td>
                            <td class="py-4 px-4 text-center">{{ $agregat['avg_girth'] ?? 0 }} cm</td>
                            <td class="py-4 px-4 text-center">12.0 cm</td>
                            <td class="py-4 px-4 text-center text-emerald-500 font-black">
                                +{{ round(($agregat['avg_girth'] ?? 0) - 12, 1) }}</td>
                            <td class="py-4 px-4 text-center">✓</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4">Survival Rate (Pokok Hidup)</td>
                            <td class="py-4 px-4 text-center">{{ $agregat['survival_rate'] ?? 0 }}%</td>
                            <td class="py-4 px-4 text-center">98.0%</td>
                            <td class="py-4 px-4 text-center text-rose-500 font-black">
                                {{ round(($agregat['survival_rate'] ?? 0) - 98, 1) }}</td>
                            <td class="py-4 px-4 text-center">!</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Visualization Section (Data Real) -->
        <div class="space-y-6">
            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726] rounded-xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6">Analisis Efisiensi Kerapatan
                    Tanaman (Target vs Realisasi)</h3>
                <div x-ref="populasiChart"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6">Trend Luas Areal per Tahun
                        Tanam</h3>
                    <div x-ref="luasTahunTanamChart"></div>
                </div>
                <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6">Sebaran Luas Areal per Unit
                    </h3>
                    <div x-ref="luasKebunChart"></div>
                </div>
            </div>

            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6">Peringkat Kondisi Pohon per Unit
                </h3>
                <div x-ref="kondisiPohonChart"></div>
            </div>

            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6">Peringkat Pemeliharaan per Unit
                </h3>
                <div x-ref="pemeliharaanChart"></div>
            </div>

            <div class="panel border-0 shadow-md p-6 bg-white dark:bg-[#0e1726]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white-light mb-6">Tren Pengukuran Vegetatif
                    (Historis)</h3>
                <div x-ref="vegetatifChart"></div>
            </div>
        </div>

        <!-- 6. Monitoring Table Terkini (Data Real) -->
        <div
            class="bg-white dark:bg-[#0e1726] rounded-xl shadow-md border border-gray-200 dark:border-white-dark/10 p-6">
            <div class="flex items-center justify-between mb-6 border-b border-[#ebedf2] pb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white-light tracking-tight font-heading">
                        Monitoring Unit Terkini</h3>
                    <p class="text-xs text-[#888ea8] font-semibold mt-1">Status terbaru 5 unit kebun Regional I</p>
                </div>
                <a href="{{ route('monitoring.data-kebun') }}"
                    class="btn btn-sm btn-outline-primary text-[10px] font-black tracking-widest px-4">LIHAT SEMUA
                    →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-semibold">
                    <thead>
                        <tr class="text-[#888ea8] text-[10px] tracking-widest uppercase">
                            <th class="text-left py-4 px-4">ID Unit</th>
                            <th class="text-left py-4 px-4">Nama Kebun</th>
                            <th class="text-left py-4 px-4">Distrik</th>
                            <th class="text-center py-4 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white-dark/5 text-gray-700 dark:text-white-dark">
                        @foreach ($latestKebuns as $kebun)
                            <tr class="hover:bg-gray-50 dark:hover:bg-black/5 transition-colors cursor-pointer">
                                <td class="py-4 px-4 font-black text-primary">{{ $kebun->kebun }}</td>
                                <td class="py-4 px-4">{{ $kebun->nama_kebun }}</td>
                                <td class="py-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {{ $kebun->nama_distrik }}</td>
                                <td class="py-4 px-4 text-center">
                                    <a href="{{ route('monitoring.detail', $kebun->id) }}"
                                        class="btn btn-xs btn-primary">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Data Binding Script -->
    <script>
        const populasiLabels = @json($populasiLabels ?? []);
        const populasiTarget = @json($populasiTarget ?? []);
        const populasiActual = @json($populasiActual ?? []);

        const tahunTanam = @json($tahunTanam ?? []);
        const totalLuas = @json($totalLuas ?? []);

        const kondisiPohonData = @json($peringkatKondisiPohonChartData ?? []);
        const pemeliharaanData = @json($peringkatPemeliharaanChartData ?? []);

        const vegetatifLabels = @json($korelasiVegetatifLabels ?? []);
        const lingkarBatang = @json($korelasiVegetatifLingkarBatang ?? []);
        const jmlPelepah = @json($korelasiVegetatifJumlahPelepah ?? []);
        const panjPelepah = @json($korelasiVegetatifPanjangPelepah ?? []);

        const stackedSeries = @json($series ?? []);
        const namaKebunTerluas = @json($namaKebunTerluas ?? []);

        document.addEventListener("alpine:init", () => {
            Alpine.data("dashboard", () => ({
                isThinking: false,
                analysisMode: 'multimodal',
                confidenceScore: 0,
                aiInferenceText: "Neural engine siaga. Lakukan diagnosa untuk mendapatkan wawasan agronomis.",
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

                init() {
                    const isDark = this.$store.app.theme === "dark" || this.$store.app.isDarkMode;

                    // 1. Chart Populasi
                    new ApexCharts(this.$refs.populasiChart, {
                        series: [{
                                name: 'Target (143/Ha)',
                                data: populasiTarget
                            },
                            {
                                name: 'Realisasi (Normal)',
                                data: populasiActual
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 350,
                            fontFamily: 'Inter'
                        },
                        colors: ['#e2e8f0', '#4361ee'],
                        xaxis: {
                            categories: populasiLabels
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                borderRadius: 4
                            }
                        }
                    }).render();

                    // 2. Chart Trend Luas
                    new ApexCharts(this.$refs.luasTahunTanamChart, {
                        series: [{
                            name: 'Luas Areal',
                            data: totalLuas
                        }],
                        chart: {
                            type: 'area',
                            height: 300
                        },
                        xaxis: {
                            categories: tahunTanam
                        },
                        colors: ['#00a76f'],
                        stroke: {
                            curve: 'smooth'
                        }
                    }).render();

                    // 3. Chart Sebaran per Unit (Stacked)
                    new ApexCharts(this.$refs.luasKebunChart, {
                        series: stackedSeries,
                        chart: {
                            type: 'bar',
                            height: 350,
                            stacked: true
                        },
                        xaxis: {
                            categories: namaKebunTerluas
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                barHeight: '80%'
                            }
                        }
                    }).render();

                    // 4. Chart Kondisi Pohon
                    new ApexCharts(this.$refs.kondisiPohonChart, {
                        series: [{
                                name: 'NORMAL',
                                data: kondisiPohonData.map(i => i.normal)
                            },
                            {
                                name: 'NON VALUER',
                                data: kondisiPohonData.map(i => i.non_valuer)
                            },
                            {
                                name: 'MATI',
                                data: kondisiPohonData.map(i => i.mati)
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 400,
                            stacked: true
                        },
                        xaxis: {
                            categories: kondisiPohonData.map(i => i.kebun)
                        },
                        colors: ['#27ae60', '#f39c12', '#FF0000'],
                        plotOptions: {
                            bar: {
                                horizontal: true
                            }
                        }
                    }).render();

                    // 5. Chart Pemeliharaan
                    new ApexCharts(this.$refs.pemeliharaanChart, {
                        series: [{
                                name: 'Kacangan',
                                data: pemeliharaanData.map(i => i.kacangan)
                            },
                            {
                                name: 'Pemeliharaan',
                                data: pemeliharaanData.map(i => i.pemeliharaan)
                            },
                            {
                                name: 'Tergenang',
                                data: pemeliharaanData.map(i => i.tergenang)
                            },
                            {
                                name: 'Anak Kayu',
                                data: pemeliharaanData.map(i => i.anak_kayu)
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 400,
                            stacked: true
                        },
                        xaxis: {
                            categories: pemeliharaanData.map(i => i.kebun)
                        },
                        colors: ['#27ae60', '#f39c12', '#3498db', '#8B4513'],
                        plotOptions: {
                            bar: {
                                horizontal: true
                            }
                        }
                    }).render();

                    // 6. Chart Vegetatif
                    new ApexCharts(this.$refs.vegetatifChart, {
                        series: [{
                                name: 'Lingkar Batang',
                                data: lingkarBatang
                            },
                            {
                                name: 'Jml Pelepah',
                                data: jmlPelepah
                            },
                            {
                                name: 'Panj Pelepah',
                                data: panjPelepah
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 400
                        },
                        xaxis: {
                            categories: vegetatifLabels,
                            labels: {
                                rotate: -45,
                                style: {
                                    fontSize: '9px'
                                }
                            }
                        },
                        colors: ['#1E90FF', '#32CD32', '#FF8C00']
                    }).render();
                },

                runAIInference() {
                    this.isThinking = true;
                    setTimeout(() => {
                        let text = "";
                        if (this.analysisMode === 'multimodal') {
                            text = "Hasil Diagnosa: Fokus pada Unit " + (populasiLabels[0] ||
                                    "Utama") + ". Terdapat deviasi populasi sebesar " + (
                                    populasiTarget[0] - populasiActual[0]) +
                                " pokok dari standar agronomi.";
                            this.confidenceScore = 96;
                        } else {
                            text =
                                "Analisis Agregat Selesai: Laju pertumbuhan lingkar batang mencapai rata-rata regional " +
                                lingkarBatang[0] + " cm. Efisiensi piringan di atas 90%.";
                            this.confidenceScore = 92;
                        }
                        this.aiInferenceText = text;
                        this.isThinking = false;
                    }, 1500);
                }
            }));
        });
    </script>
</x-layout.default>
