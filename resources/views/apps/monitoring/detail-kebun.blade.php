<x-layout.default>
    <!-- 1. GIS Library Dependencies (Leaflet) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="/assets/js/apexcharts.js"></script>

    <div x-data="detailKebun()">
        <!-- 1. Breadcrumbs & Header -->
        <div class="mb-6">
            <ul class="flex space-x-2 rtl:space-x-reverse text-xs mb-2 tracking-widest font-bold text-gray-400">
                <li><a href="javascript:;" class="text-primary hover:underline">Monitoring</a></li>
                <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 text-gray-400 dark:text-gray-600">
                    <span>Detail Kebun Spasial</span>
                </li>
            </ul>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white-light">
                            {{-- Backend: {{ $infoKebun['nama'] ?? 'Kebun Sei Dadap' }} ({{ $infoKebun['kode_kebun'] ?? '1KDP' }}) --}}
                            Kebun Sei Dadap (1KDP)
                        </h1>
                        <span
                            class="badge bg-emerald-500 !text-white text-[10px] px-3 py-1 font-black rounded-full tracking-widest shadow-sm">CONNECTED</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium italic">
                        {{-- Backend: {{ $infoKebun['distrik'] ?? 'Distrik Labuhan Batu I' }} • 24 Blok Aktif • {{ $infoKebun['luas'] ?? '1.450' }} Ha --}}
                        Distrik Labuhan Batu I • 24 Blok Aktif • 1.450 Ha Total Areal
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-5">
            <!-- LEFT COLUMN: Controls & Legend -->
            <div class="space-y-5">
                <!-- Quick Stats -->
                <div class="panel p-6 border-0 shadow-sm rounded-3xl dark:bg-[#1b2e4b]">
                    <h5 class="font-black text-[10px] tracking-[0.2em] mb-5 text-gray-400 dark:text-gray-500 italic">
                        Kebun Summary</h5>
                    <div class="space-y-4">
                        <div class="flex justify-between items-end border-b border-gray-50 dark:border-gray-700 pb-2">
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold">Status Rawat</p>
                            <p class="text-sm font-black text-emerald-500">Optimal</p>
                        </div>
                        <div class="flex justify-between items-end border-b border-gray-50 dark:border-gray-700 pb-2">
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold">Terakhir Update
                            </p>
                            <p class="text-sm font-black italic text-gray-800 dark:text-gray-200">21 April 2026</p>
                        </div>
                    </div>
                </div>

                <!-- Layer Controls -->
                <div class="panel p-6 border-0 shadow-sm rounded-3xl dark:bg-[#1b2e4b]">
                    <h5 class="font-black text-[10px] tracking-[0.2em] mb-5 text-gray-400 dark:text-gray-500 italic">
                        Layer Visualisasi</h5>
                    <div class="space-y-4">
                        <template x-for="layer in layers" :key="layer.id">
                            <div class="flex justify-between items-center group">
                                <span
                                    class="text-[11px] font-bold text-gray-700 dark:text-gray-300 group-hover:text-primary transition-all"
                                    x-text="layer.label"></span>
                                <label class="w-8 h-4 relative mb-0">
                                    <input type="checkbox"
                                        class="custom_switch absolute w-full h-full opacity-0 z-10 cursor-pointer peer"
                                        x-model="layer.active" @change="toggleLayer(layer.id)" />
                                    <span
                                        class="bg-[#ebedf2] dark:bg-dark block h-full rounded-full before:absolute before:left-1 before:bg-white before:bottom-0.5 before:w-3 before:h-3 before:rounded-full peer-checked:before:left-4 peer-checked:bg-primary transition-all duration-300 shadow-inner"></span>
                                </label>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Legend status -->
                <div class="panel p-6 border-0 shadow-sm rounded-3xl dark:bg-[#1b2e4b]">
                    <h5 class="font-black text-[10px] tracking-[0.2em] mb-5 text-gray-400 dark:text-gray-500 italic">
                        Status Kesehatan Blok</h5>
                    <div class="space-y-4 text-[11px] font-bold">
                        <div class="flex items-center justify-between text-success">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-success shadow-[0_0_8px_rgba(16,185,129,0.4)]">
                                </div>
                                <span
                                    class=">Healthy</span>
                            </div>
                            <span class="font-black
                                    italic dark:text-gray-200 text-[12px]">18 Blok</span>
                            </div>
                            <div class="flex items-center justify-between text-warning">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full bg-warning shadow-[0_0_8px_rgba(245,158,11,0.4)]">
                                    </div>
                                    <span
                                        class=">Moderate</span>
                            </div>
                            <span class="font-black
                                        italic dark:text-gray-200 text-[12px]">5 Blok</span>
                                </div>
                                <div class="flex items-center justify-between text-danger">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-3 h-3 rounded-full bg-danger shadow-[0_0_8px_rgba(239,68,68,0.4)]">
                                        </div>
                                        <span
                                            class=">Critical</span>
                            </div>
                            <span class="font-black
                                            italic dark:text-gray-200 text-[12px]">1 Blok</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Interactive GIS Map & Charts -->
                        <div class="xl:col-span-3 space-y-5">
                            <!-- 1. INTEGRATED GIS MAP PANEL -->
                            <div
                                class="panel p-0 border-0 shadow-sm h-[500px] flex flex-col overflow-hidden rounded-3xl dark:bg-[#1b2e4b] relative border border-gray-100 dark:border-white/5">
                                <!-- Toolbar Map -->
                                <div
                                    class="p-4 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-white dark:bg-[#1b2e4b] z-20">
                                    <div class="flex items-center gap-2">
                                        <div class="p-2 bg-primary/10 text-primary rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-width="2"
                                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m6-3l5.447 2.724a1 1.01.553.894v10.764a1 1 0 01-1.447.894L15 17m-6 3l6-3m-6 0V7m6 10V4" />
                                            </svg>
                                        </div>
                                        <h5
                                            class="font-black text-[10px] tracking-widest text-gray-500 dark:text-gray-400">
                                            Interactive Spatial Grid</h5>
                                    </div>
                                    <div class="flex gap-2">
                                        <button @click="zoomOut"
                                            class="p-2 bg-gray-50 dark:bg-black/20 hover:bg-primary/10 rounded-xl border border-gray-100 dark:border-gray-700 transition-all text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-width="2.5" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <button @click="zoomIn"
                                            class="p-2 bg-gray-50 dark:bg-black/20 hover:bg-primary/10 rounded-xl border border-gray-100 dark:border-gray-700 transition-all text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- LEAFLET CONTAINER -->
                                <div class="flex-1 relative overflow-hidden">
                                    <div id="leafletMap" class="w-full h-full z-10 bg-[#f8f9fc] dark:bg-black/20"></div>

                                    <!-- Compass Overlay -->
                                    <div
                                        class="absolute top-6 right-6 w-12 h-12 bg-white/90 dark:bg-black/60 backdrop-blur rounded-full flex items-center justify-center border border-white/20 shadow-xl text-primary font-black z-[1000] pointer-events-none">
                                        N</div>
                                </div>
                            </div>

                            <!-- 2. Charts Row (Pie & Bar) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="panel border-0 shadow-sm rounded-3xl p-8 dark:bg-[#1b2e4b]">
                                    <h5
                                        class="font-black text-[10px] tracking-[0.2em] mb-8 text-gray-400 dark:text-gray-500 text-center italic">
                                        Proporsi Kondisi Pohon (%)</h5>
                                    <div x-ref="pieChartDummy" class="min-h-[300px]"></div>
                                </div>
                                <div class="panel border-0 shadow-sm rounded-3xl p-8 dark:bg-[#1b2e4b]">
                                    <h5
                                        class="font-black text-[10px] tracking-[0.2em] mb-8 text-gray-400 dark:text-gray-500 text-center italic">
                                        Analisis Parameter Areal (%)</h5>
                                    <div x-ref="barChartDummy" class="min-h-[300px]"></div>
                                </div>
                            </div>

                            <!-- 3. TREN BIOMETRIK VEGETATIF (FULL WIDTH) -->
                            <div class="panel border-0 shadow-sm rounded-3xl p-8 dark:bg-[#1b2e4b]">
                                <div
                                    class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 border-b border-gray-50 dark:border-gray-800 pb-5">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-warning/10 text-warning rounded-lg"><svg class="w-5 h-5"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                            </svg></div>
                                        <div>
                                            <h5
                                                class="font-black text-xs tracking-widest text-gray-900 dark:text-white-light">
                                                Tren Biometrik Vegetatif</h5>
                                            <p class="text-[10px] font-bold text-gray-400 tracking-tighter">Unit: <span
                                                    class="text-primary italic"
                                                    x-text="selectedBlock ? 'Unit Blok ' + selectedBlock : 'Rata-rata Seluruh Kebun'"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="selectedBlock = ''"
                                            class="btn btn-sm btn-outline-secondary rounded-lg text-[10px] font-black tracking-widest"
                                            x-show="selectedBlock">RESET DATA</button>
                                        <span
                                            class="text-[9px] font-black text-gray-400 tracking-[0.2em] bg-gray-50 dark:bg-black/20 px-3 py-1 rounded-full italic">Source:
                                            Ground-Check 2024</span>
                                    </div>
                                </div>
                                <div class="relative min-h-[350px]">
                                    <div x-ref="vegetativeTrendChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. AI ANALYSIS SECTION -->
                    <div
                        class="panel border-0 shadow-2xl p-0 overflow-hidden mt-8 bg-white dark:bg-gradient-to-br dark:from-[#1b2e4b] dark:to-[#060818] rounded-[2rem] border border-gray-100 dark:border-transparent transition-all duration-300">
                        <div class="p-10">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-8 mb-12">
                                <div class="flex items-center gap-5">
                                    <div
                                        class="w-16 h-16 bg-primary text-white rounded-3xl flex items-center justify-center shadow-xl shadow-primary/20 dark:shadow-none animate-pulse">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4
                                            class="font-black text-xl tracking-tight text-gray-900 dark:text-white tracking-tighter">
                                            Autonomous Prescriptive Engine</h4>
                                        <p
                                            class="text-xs text-indigo-500 dark:text-indigo-400 font-black tracking-[0.3em] mt-1 opacity-80">
                                            Diagnostic Node: DSS-SAWIT-V2.4</p>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center gap-4 bg-gray-50 dark:bg-black/30 p-3 rounded-[1.5rem] border border-gray-100 dark:border-white/10 w-full md:w-auto shadow-inner font-black">
                                    <select
                                        class="form-select border-none bg-transparent font-black text-xs tracking-widest min-w-[180px] cursor-pointer text-gray-700 dark:text-white focus:ring-0"
                                        x-model="selectedBlock">
                                        <option value="">Pilih Unit Blok</option>
                                        <template x-for="row in ['A','B','C']" :key="row">
                                            <template x-for="col in ['01','02','03','04']" :key="col">
                                                <option :value="row + '-' + col"
                                                    x-text="'ANALISIS BLOK ' + row + '-' + col"
                                                    class="dark:bg-[#1b2e4b]"></option>
                                            </template>
                                        </template>
                                    </select>
                                    <button
                                        class="btn btn-primary px-10 py-3 rounded-2xl font-black text-[11px] tracking-[0.2em] active:scale-95 shadow-xl shadow-primary/20 transition-all"
                                        @click="runInference" :disabled="isAnalyzing">
                                        <span x-show="!isAnalyzing">EXECUTE INFERENCE AI</span>
                                        <span x-show="isAnalyzing" class="animate-pulse">PROCESSING NEURAL...</span>
                                    </button>
                                </div>
                            </div>

                            <div
                                class="min-h-[200px] bg-gray-50/50 dark:bg-black/40 rounded-[2rem] border-2 border-dashed border-gray-200 dark:border-white/10 p-10 flex flex-col justify-center relative overflow-hidden transition-all duration-500">
                                <template x-if="!inferenceResult && !isAnalyzing">
                                    <div class="text-center py-4 opacity-50">
                                        <p
                                            class="text-gray-400 dark:text-white/20 text-[10px] font-black tracking-[0.5em] italic">
                                            System Idle - Standby for spatial input</p>
                                    </div>
                                </template>
                                <template x-if="isAnalyzing">
                                    <div class="flex flex-col items-center gap-6">
                                        <div
                                            class="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin">
                                        </div>
                                        <p
                                            class="text-xs font-black tracking-widest text-indigo-500 dark:text-indigo-400 animate-pulse">
                                            Running Monte Carlo Simulation...</p>
                                    </div>
                                </template>
                                <template x-if="inferenceResult && !isAnalyzing">
                                    <div class="animate__animated animate__fadeInUp relative z-10">
                                        <div class="flex items-center gap-3 mb-6">
                                            <span
                                                class="bg-emerald-500 !text-white text-[9px] font-black px-4 py-1.5 rounded-full tracking-[0.2em] shadow-lg shadow-emerald-500/20">Success
                                                Inference</span>
                                            <h6
                                                class="text-xs font-black text-gray-800 dark:text-white tracking-widest tracking-tighter">
                                                Diagnostic Report: Blok <span x-text="selectedBlock"></span></h6>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                                            <div
                                                class="lg:col-span-2 p-8 bg-white dark:bg-black/20 rounded-3xl border-l-[6px] border-l-emerald-500 shadow-sm border border-gray-100 dark:border-white/5 font-bold italic">
                                                <p
                                                    class="text-[10px] font-black text-emerald-500 tracking-widest mb-3 italic">
                                                    Rekomendasi Preskriptif:</p>
                                                <p class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed italic tracking-tight"
                                                    x-text="inferenceResult.recommendation"></p>
                                            </div>
                                            <div class="space-y-4">
                                                <div
                                                    class="bg-white dark:bg-black/20 p-5 rounded-2xl border border-gray-100 dark:border-white/5 shadow-sm flex flex-col justify-center">
                                                    <p
                                                        class="text-[9px] text-gray-500 dark:text-gray-400 font-black mb-1 tracking-widest italic">
                                                        Confidence Score</p>
                                                    <p class="text-2xl font-black text-emerald-500 font-mono"
                                                        x-text="inferenceResult.confidence + '%'"></p>
                                                </div>
                                                <div
                                                    class="bg-white dark:bg-black/20 p-5 rounded-2xl border border-gray-100 dark:border-white/5 shadow-sm flex flex-col justify-center">
                                                    <p
                                                        class="text-[9px] text-gray-500 dark:text-gray-400 font-black mb-1 tracking-widest italic">
                                                        Vigor Index (NDVI)</p>
                                                    <p class="text-2xl font-black text-indigo-500 dark:text-indigo-400 font-mono"
                                                        x-text="inferenceResult.vigor"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SCRIPTS -->
                <script>
                    document.addEventListener("alpine:init", () => {
                        Alpine.data("detailKebun", () => ({
                            // Map Properties
                            map: null,
                            blockLayer: null,
                            orthoLayer: null,
                            selectedBlock: '',
                            isAnalyzing: false,
                            inferenceResult: null,
                            trendChart: null,

                            // --- LOGIKA BACKEND (DITIDURKAN) ---
                            {{-- 
                infoKebun: @json($infoKebun ?? []),
                blockStatuses: @json($blockStatuses ?? []),
                kondisiPohon: @json($kondisiPohon ?? []),
                arealTanaman: @json($arealTanaman ?? []),
                vegetativeData: @json($vegetativeTrend ?? []),
                tileConfig: @json($tileConfig ?? null),
                --}}

                            // DUMMY DATA FOR UI (Fallbacks)
                            kondisiPohon: {
                                "PKK NORMAL": 92.5,
                                "PKK KERDIL": 5.2,
                                "PKK MATI": 2.3
                            },
                            arealTanaman: {
                                "KACANGAN": 78.5,
                                "KURANG BAIK": 12.0,
                                "TERGENANG": 6.5,
                                "ANAK KAYU": 3.0
                            },
                            dummyBlockStatuses: {
                                'A-03': '#f59e0b',
                                'C-02': '#f59e0b',
                                'B-02': '#ef4444'
                            },
                            vegetativeData: {
                                'default': {
                                    girth: [75, 76, 78, 79, 82, 85],
                                    fronds: [32, 33, 33, 34, 35, 36],
                                    length: [180, 182, 185, 188, 190, 192],
                                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']
                                },
                                'C-02': {
                                    girth: [60, 62, 63, 65, 66, 68],
                                    fronds: [28, 29, 29, 30, 30, 31],
                                    length: [150, 155, 158, 160, 162, 165],
                                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']
                                },
                                'B-02': {
                                    girth: [55, 56, 56, 57, 58, 59],
                                    fronds: [25, 26, 26, 26, 27, 27],
                                    length: [140, 142, 143, 144, 145, 148],
                                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']
                                }
                            },

                            layers: [{
                                    id: 'ortho',
                                    label: 'Layer Orthophoto (Drone)',
                                    active: true
                                },
                                {
                                    id: 'blocks',
                                    label: 'Layer Batas Blok Spasial',
                                    active: true
                                },
                                {
                                    id: 'trees',
                                    label: 'Point Individu Pohon',
                                    active: false
                                },
                            ],

                            init() {
                                this.$nextTick(() => {
                                    this.initMap();
                                    this.renderCharts();
                                    this.renderVegetativeTrend();
                                });
                                this.$watch('selectedBlock', (val) => {
                                    this.updateVegetativeChart(val);
                                });
                            },

                            initMap() {
                                // Start Map at Sei Dadap Sample
                                this.map = L.map('leafletMap').setView([-2.95, 104.7], 14);

                                // Tile Layer Standard Fallback
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);

                                // Logic Dummy Grid (Simulasi Grid A01-C04 di GIS)
                                // Nantinya diganti L.geoJSON(this.geoJSONData)
                                const gridColors = this.dummyBlockStatuses;
                                ['A', 'B', 'C'].forEach((row, rIdx) => {
                                    ['01', '02', '03', '04'].forEach((col, cIdx) => {
                                        const blockId = row + '-' + col;
                                        const lat = -2.95 - (rIdx * 0.005);
                                        const lng = 104.7 + (cIdx * 0.005);

                                        L.rectangle([
                                            [lat, lng],
                                            [lat - 0.004, lng + 0.004]
                                        ], {
                                            color: "#fff",
                                            weight: 1,
                                            fillColor: gridColors[blockId] || "#10b981",
                                            fillOpacity: 0.6
                                        }).on('click', () => {
                                            this.selectedBlock = blockId;
                                        }).addTo(this.map);
                                    });
                                });
                            },

                            getBlockColor(id) {
                                return this.dummyBlockStatuses[id] || '#10b981';
                            },
                            zoomIn() {
                                this.map.zoomIn();
                            },
                            zoomOut() {
                                this.map.zoomOut();
                            },
                            toggleLayer(id) {
                                /* Logic layer switch Leaflet */
                            },

                            renderCharts() {
                                const isDark = document.documentElement.classList.contains('dark');
                                const textCol = isDark ? '#cbd5e1' : '#334155';

                                new ApexCharts(this.$refs.pieChartDummy, {
                                    series: Object.values(this.kondisiPohon),
                                    labels: Object.keys(this.kondisiPohon),
                                    chart: {
                                        type: 'pie',
                                        height: 350
                                    },
                                    colors: ['#0ea5e9', '#f59e0b', '#ef4444'],
                                    stroke: {
                                        show: false
                                    },
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            colors: textCol
                                        }
                                    },
                                    dataLabels: {
                                        enabled: true,
                                        style: {
                                            fontWeight: 900
                                        },
                                        formatter: val => val.toFixed(1) + "%"
                                    }
                                }).render();

                                new ApexCharts(this.$refs.barChartDummy, {
                                    series: Object.entries(this.arealTanaman).map(([k, v]) => ({
                                        name: k,
                                        data: [v]
                                    })),
                                    chart: {
                                        type: 'bar',
                                        height: 300,
                                        toolbar: {
                                            show: false
                                        }
                                    },
                                    colors: ['#84cc16', '#f59e0b', '#ef4444', '#78350f'],
                                    plotOptions: {
                                        bar: {
                                            columnWidth: '60%',
                                            borderRadius: 10
                                        }
                                    },
                                    xaxis: {
                                        categories: [''],
                                        axisBorder: {
                                            show: false
                                        }
                                    },
                                    yaxis: {
                                        max: 100,
                                        labels: {
                                            style: {
                                                colors: textCol
                                            },
                                            formatter: v => v + "%"
                                        }
                                    },
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            colors: textCol
                                        }
                                    },
                                    grid: {
                                        borderColor: isDark ? '#334155' : '#e2e8f0'
                                    }
                                }).render();
                            },

                            renderVegetativeTrend() {
                                const isDark = document.documentElement.classList.contains('dark');
                                const textCol = isDark ? '#cbd5e1' : '#334155';
                                const data = this.vegetativeData['default'];
                                this.trendChart = new ApexCharts(this.$refs.vegetativeTrendChart, {
                                    series: [{
                                        name: 'Lingkar Batang (cm)',
                                        data: data.girth
                                    }, {
                                        name: 'Jumlah Pelepah',
                                        data: data.fronds
                                    }, {
                                        name: 'Panjang Pelepah (cm)',
                                        data: data.length
                                    }],
                                    chart: {
                                        type: 'bar',
                                        height: 350,
                                        toolbar: {
                                            show: true
                                        },
                                        animations: {
                                            enabled: true
                                        }
                                    },
                                    plotOptions: {
                                        bar: {
                                            columnWidth: '55%',
                                            borderRadius: 4
                                        }
                                    },
                                    colors: ['#0ea5e9', '#10b981', '#f59e0b'],
                                    xaxis: {
                                        categories: data.labels,
                                        labels: {
                                            style: {
                                                colors: textCol,
                                                fontWeight: 700
                                            }
                                        }
                                    },
                                    yaxis: {
                                        labels: {
                                            style: {
                                                colors: textCol
                                            }
                                        }
                                    },
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            colors: textCol
                                        }
                                    },
                                    grid: {
                                        borderColor: isDark ? '#334155' : '#e2e8f0'
                                    },
                                    tooltip: {
                                        theme: isDark ? 'dark' : 'light'
                                    }
                                });
                                this.trendChart.render();
                            },

                            updateVegetativeChart(blockId) {
                                const newData = this.vegetativeData[blockId] || this.vegetativeData['default'];
                                this.trendChart.updateSeries([{
                                    name: 'Lingkar Batang (cm)',
                                    data: newData.girth
                                }, {
                                    name: 'Jumlah Pelepah',
                                    data: newData.fronds
                                }, {
                                    name: 'Panjang Pelepah (cm)',
                                    data: newData.length
                                }]);
                            },

                            runInference() {
                                if (!this.selectedBlock) {
                                    alert('SILA PILIH BLOK TERLEBIH DAHULU');
                                    return;
                                }
                                this.isAnalyzing = true;
                                this.inferenceResult = null;
                                setTimeout(() => {
                                    this.isAnalyzing = false;
                                    this.inferenceResult = {
                                        recommendation: "Berdasarkan analisis citra spasial pada Blok " +
                                            this.selectedBlock +
                                            ", terdeteksi defisiensi hara Nitrogen signifikan. Rekomendasi: Aplikasi Urea 1.5kg/pokok dan evaluasi sistem drainase.",
                                        confidence: (Math.random() * (99.8 - 96.5) + 96.5).toFixed(1),
                                        vigor: (Math.random() * (0.85 - 0.75) + 0.75).toFixed(3)
                                    };
                                }, 2500);
                            }
                        }));
                    });
                </script>

                <style>
                    .custom_switch:checked~span:before {
                        background-color: #fff !important;
                    }

                    .panel {
                        background: #fff;
                        border-radius: 2rem;
                    }

                    .dark .panel {
                        background: #1b2e4b;
                    }

                    [x-cloak] {
                        display: none !important;
                    }

                    #leafletMap {
                        border-radius: 2rem;
                        min-height: 440px;
                    }
                </style>
</x-layout.default>
