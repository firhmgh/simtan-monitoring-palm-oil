<x-layout.default>
    <div x-data="reportGenerator()">

        <!-- 1. Header & Quick Actions -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4 pt-5">
            <div>
                <ul class="flex space-x-2 rtl:space-x-reverse mb-1">
                    <li>
                        <a href="javascript:;" class="text-primary hover:underline">Monitoring</a>
                    </li>
                    <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                        <span>Generate Laporan</span>
                    </li>
                </ul>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Generate Laporan</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Analisis cerdas dan ringkasan performa kebun TBM III
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="exportPDF()" :disabled="!showPreview"
                    class="btn btn-outline-danger flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold disabled:opacity-50">
                    <template x-if="!isGenerating">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 16V8M12 16L9 13M12 16L15 13" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3 15V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V15"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </template>
                    <span x-show="isGenerating"
                        class="animate-spin border-2 border-danger border-l-transparent rounded-full w-5 h-5"></span>
                    <span x-text="isGenerating ? 'Generating...' : 'Export PDF'"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- 2. Configuration Sidebar (Left) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Main Config -->
                <div class="panel rounded-2xl border-0 shadow-sm">
                    <h5 class="font-bold text-lg mb-5 flex items-center gap-2 text-gray-800 dark:text-white">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="text-primary">
                            <path d="M12 4V20M12 4L8 8M12 4L16 8" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Konfigurasi Laporan
                    </h5>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-semibold mb-2 block">Pilih Kebun</label>
                            <select x-model="selectedKebun"
                                class="form-select bg-gray-50 border-gray-200 dark:bg-gray-900 dark:border-gray-700 py-2.5">
                                <option value="">Pilih Kebun...</option>
                                <template x-for="item in listKebun" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-semibold mb-2 block">Pilih Periode</label>
                            <select x-model="selectedPeriode"
                                class="form-select bg-gray-50 border-gray-200 dark:bg-gray-900 dark:border-gray-700 py-2.5">
                                <option value="">Pilih Periode...</option>
                                <option value="2026-03">Maret 2026</option>
                                <option value="2026-02">Februari 2026</option>
                            </select>
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="p-2 bg-purple-100 dark:bg-purple-500/10 rounded-lg text-purple-600">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path
                                                d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-bold">Include AI Analysis</span>
                                </div>
                                <label class="w-12 h-6 relative shrink-0">
                                    <input type="checkbox"
                                        class="custom_switch absolute w-full h-full opacity-0 z-10 cursor-pointer peer"
                                        x-model="includeAI" />
                                    <span
                                        class="bg-[#ebedf2] dark:bg-dark block h-full rounded-full before:absolute before:left-1 before:bg-white before:bottom-1 before:w-4 before:h-4 before:rounded-full peer-checked:before:left-7 peer-checked:bg-primary before:transition-all duration-300"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button @click="generatePreview"
                        class="btn btn-primary w-full mt-6 py-3 rounded-xl shadow-lg shadow-primary/20 gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        Preview Report
                    </button>
                </div>

                <!-- Sections Switcher -->
                <div class="panel rounded-2xl border-0 shadow-sm">
                    <h5 class="font-bold text-base mb-4">Section Laporan</h5>
                    <div class="space-y-4">
                        <template x-for="section in sections" :key="section.id">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400" x-text="section.label"></span>
                                <label class="inline-flex">
                                    <input type="checkbox" class="form-checkbox outline-primary rounded" checked />
                                </label>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- 3. Report Preview Area (Right) -->
            <div class="lg:col-span-8">
                <!-- No Preview State -->
                <template x-if="!showPreview">
                    <div
                        class="panel h-[500px] flex flex-col items-center justify-center text-center border-2 border-dashed border-gray-200 dark:border-gray-800 bg-transparent rounded-2xl">
                        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-full mb-4">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                                class="text-gray-300 dark:text-gray-700" stroke="currentColor" stroke-width="1.5">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" x2="8" y1="13" y2="13" />
                                <line x1="16" x2="8" y1="17" y2="17" />
                                <line x1="10" x2="8" y1="9" y2="9" />
                            </svg>
                        </div>
                        <h5 class="text-xl font-bold text-gray-800 dark:text-white">Belum Ada Preview</h5>
                        <p class="text-gray-500 max-w-xs mx-auto text-sm">Pilih kebun dan periode untuk melihat
                            pratinjau laporan otomatis.</p>
                    </div>
                </template>

                <!-- Active Report State -->
                <template x-if="showPreview">
                    <div class="space-y-6 animate__animated animate__fadeIn">

                        <!-- Report Branding Header -->
                        <div class="panel p-0 overflow-hidden rounded-2xl border-0 shadow-sm">
                            <div class="bg-gradient-to-r from-emerald-600 to-teal-500 p-8 text-white">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm border border-white/30">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path
                                                d="m17 14 3 3.3a1 1 0 0 1-.7 1.7H4.7a1 1 0 0 1-.7-1.7L7 14h-.3a1 1 0 0 1-.7-1.7L9 9h-.2a1 1 0 0 1-.8-1.7L12 3l4 4.3a1 1 0 0 1-.8 1.7H15l3 3.3a1 1 0 0 1-.7 1.7H17Z" />
                                            <path d="M12 22v-3" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold">Laporan Monitoring TBM III</h2>
                                        <p class="opacity-80 font-medium" x-text="reportData.kebun"></p>
                                    </div>
                                </div>
                                <div class="flex gap-5 text-sm font-medium opacity-90">
                                    <span>Periode: <span x-text="reportData.periode"></span></span>
                                    <span>Generated: {{ date('d F Y') }}</span>
                                </div>
                            </div>

                            <!-- KPI Summary Cards (Styled like your example) -->
                            <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 bg-white dark:bg-[#0e1726]">
                                <div class="rounded-xl p-4 text-white" style="background-color: #00a76f;">
                                    <div class="text-[10px] uppercase font-bold opacity-80 mb-1">Total Blok</div>
                                    <div class="text-2xl font-bold" x-text="reportData.summary.totalBlok"></div>
                                </div>
                                <div class="rounded-xl p-4 text-white" style="background-color: #1c64f2;">
                                    <div class="text-[10px] uppercase font-bold opacity-80 mb-1">Total Areal</div>
                                    <div class="text-2xl font-bold"><span
                                            x-text="reportData.summary.totalAreal"></span> Ha</div>
                                </div>
                                <div class="rounded-xl p-4 text-white" style="background-color: #8b5cf6;">
                                    <div class="text-[10px] uppercase font-bold opacity-80 mb-1">Avg NDVI</div>
                                    <div class="text-2xl font-bold" x-text="reportData.summary.averageNDVI"></div>
                                </div>
                                <div class="rounded-xl p-4 text-white bg-orange-500">
                                    <div class="text-[10px] uppercase font-bold opacity-80 mb-1">Health Score</div>
                                    <div class="text-2xl font-bold"><span
                                            x-text="reportData.summary.healthScore"></span>%</div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="panel rounded-2xl border-0 shadow-sm">
                                <h5 class="font-bold text-base mb-5">Performance per Blok</h5>
                                <div id="blockChart"></div>
                            </div>
                            <div class="panel rounded-2xl border-0 shadow-sm">
                                <h5 class="font-bold text-base mb-5">Trend Vegetasi (6 Bulan)</h5>
                                <div id="trendChart"></div>
                            </div>
                        </div>

                        <!-- AI Recommendations (Styled Elegantly) -->
                        <div x-show="includeAI"
                            class="panel rounded-2xl border-0 shadow-sm border-l-4 border-purple-500 animate__animated animate__fadeInUp">
                            <div class="flex items-center gap-2 mb-6 text-purple-600">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path
                                        d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z" />
                                </svg>
                                <h5 class="font-bold text-lg">AI Smart Recommendations</h5>
                            </div>

                            <div class="space-y-4">
                                <div
                                    class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 flex gap-4">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></div>
                                    <div>
                                        <h6 class="font-bold text-emerald-900 dark:text-emerald-400">Overall
                                            Assessment: Excellent</h6>
                                        <p class="text-sm text-emerald-700 dark:text-emerald-500/80 mt-1">Kebun Sei
                                            Rampah menunjukkan stabilitas NDVI di atas 0.8. Tidak diperlukan tindakan
                                            korektif mendesak.</p>
                                    </div>
                                </div>

                                <div
                                    class="p-4 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 flex gap-4">
                                    <div class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0"></div>
                                    <div>
                                        <h6 class="font-bold text-amber-900 dark:text-amber-400">Peringatan: Blok B-02
                                        </h6>
                                        <p class="text-sm text-amber-700 dark:text-amber-500/80 mt-1">Terjadi penurunan
                                            skor kesehatan di Blok B-02 (70%). Disarankan pengecekan lapangan terkait
                                            ketersediaan air.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Script Section -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("reportGenerator", () => ({
                selectedKebun: '',
                selectedPeriode: '',
                includeAI: true,
                showPreview: false,
                isGenerating: false,

                listKebun: [{
                        id: 'sei-rampah',
                        name: 'Kebun Sei Rampah'
                    },
                    {
                        id: 'dolok-ilir',
                        name: 'Kebun Dolok Ilir'
                    },
                    {
                        id: 'bandar-pulau',
                        name: 'Kebun Bandar Pulau'
                    },
                ],

                sections: [{
                        id: 'exec',
                        label: 'Executive Summary'
                    },
                    {
                        id: 'veg',
                        label: 'Vegetation Analysis'
                    },
                    {
                        id: 'block',
                        label: 'Block Details'
                    },
                    {
                        id: 'trend',
                        label: 'Trend Charts'
                    },
                    {
                        id: 'recom',
                        label: 'AI Recommendations'
                    },
                ],

                reportData: {
                    kebun: 'Kebun Sei Rampah',
                    periode: 'Maret 2026',
                    summary: {
                        totalBlok: 24,
                        totalAreal: 1450,
                        averageNDVI: 0.83,
                        healthScore: 92,
                    },
                    blockPerformance: [{
                            block: 'A-01',
                            ndvi: 0.85,
                            health: 90
                        },
                        {
                            block: 'A-02',
                            ndvi: 0.82,
                            health: 87
                        },
                        {
                            block: 'A-03',
                            ndvi: 0.78,
                            health: 82
                        },
                        {
                            block: 'A-04',
                            ndvi: 0.88,
                            health: 93
                        },
                        {
                            block: 'B-01',
                            ndvi: 0.84,
                            health: 89
                        },
                        {
                            block: 'B-02',
                            ndvi: 0.65,
                            health: 70
                        },
                    ],
                    trends: [{
                            month: 'Oct',
                            ndvi: 0.72
                        }, {
                            month: 'Nov',
                            ndvi: 0.75
                        }, {
                            month: 'Dec',
                            ndvi: 0.78
                        },
                        {
                            month: 'Jan',
                            ndvi: 0.81
                        }, {
                            month: 'Feb',
                            ndvi: 0.84
                        }, {
                            month: 'Mar',
                            ndvi: 0.83
                        },
                    ]
                },

                generatePreview() {
                    if (!this.selectedKebun || !this.selectedPeriode) {
                        alert('Silahkan pilih kebun dan periode!');
                        return;
                    }
                    this.showPreview = true;
                    // Inisialisasi chart setelah elemen muncul di DOM
                    setTimeout(() => this.initCharts(), 200);
                },

                initCharts() {
                    // Blok Performance Chart (Bar)
                    const blockOpts = {
                        series: [{
                                name: 'NDVI Index',
                                data: this.reportData.blockPerformance.map(i => i.ndvi)
                            },
                            {
                                name: 'Health Score %',
                                data: this.reportData.blockPerformance.map(i => i.health)
                            }
                        ],
                        chart: {
                            height: 300,
                            type: 'bar',
                            toolbar: {
                                show: false
                            }
                        },
                        colors: ['#00a76f', '#1c64f2'],
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '55%'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: this.reportData.blockPerformance.map(i => i.block)
                        },
                    };
                    new ApexCharts(document.querySelector("#blockChart"), blockOpts).render();

                    // Trend Chart (Line)
                    const trendOpts = {
                        series: [{
                            name: 'NDVI',
                            data: this.reportData.trends.map(i => i.ndvi)
                        }],
                        chart: {
                            height: 300,
                            type: 'line',
                            toolbar: {
                                show: false
                            }
                        },
                        colors: ['#00a76f'],
                        stroke: {
                            curve: 'smooth',
                            width: 4
                        },
                        xaxis: {
                            categories: this.reportData.trends.map(i => i.month)
                        },
                        markers: {
                            size: 5,
                            strokeColors: '#fff',
                            strokeWidth: 2,
                            hover: {
                                size: 7
                            }
                        },
                    };
                    new ApexCharts(document.querySelector("#trendChart"), trendOpts).render();
                },

                exportPDF() {
                    this.isGenerating = true;
                    setTimeout(() => {
                        this.isGenerating = false;
                        alert('Laporan PDF berhasil di-generate!');
                    }, 2000);
                }
            }));
        });
    </script>
</x-layout.default>
