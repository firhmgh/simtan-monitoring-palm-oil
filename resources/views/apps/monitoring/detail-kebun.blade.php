<x-layout.default>
    <!-- 1. GIS & Chart Dependencies -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="/assets/js/apexcharts.js"></script>

    <div x-data="detailKebun()">
        <!-- 1. Breadcrumbs & Header -->
        <div class="mb-6">
            <ul
                class="flex space-x-2 rtl:space-x-reverse text-xs mb-2 tracking-widest font-bold text-gray-400 uppercase">
                <li><a href="javascript:;" class="text-primary hover:underline">Monitoring</a></li>
                <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 text-gray-400 dark:text-gray-600">
                    <span>Detail Kebun Spasial</span>
                </li>
            </ul>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 text-gray-900 dark:text-white">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold dark:text-white-light">
                            Kebun Aek Nabara Selatan (1KAS)
                        </h1>
                        <span
                            class="badge bg-emerald-500 !text-white text-[10px] px-3 py-1 font-black rounded-full tracking-widest shadow-sm uppercase">LIVE
                            GIS</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium italic">
                        Distrik Labuhan Batu I • 24 Blok Aktif • 1.450 Ha Total Areal
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-5">
            <!-- LEFT COLUMN: Sidebar Controls -->
            <div class="space-y-5">
                <!-- Summary -->
                <div class="panel p-6 border-0 shadow-sm rounded-3xl dark:bg-[#1b2e4b]">
                    <h5
                        class="font-black text-[10px] tracking-[0.2em] mb-5 text-gray-400 dark:text-gray-500 italic uppercase">
                        Kebun Summary</h5>
                    <div class="space-y-4 text-[11px]">
                        <div class="flex justify-between items-end border-b border-gray-50 dark:border-gray-700 pb-2">
                            <p class="text-gray-500 dark:text-gray-400 font-bold uppercase">Status Rawat</p>
                            <p class="font-black text-emerald-500 uppercase font-black">Optimal</p>
                        </div>
                        <div class="flex justify-between items-end border-b border-gray-50 dark:border-gray-700 pb-2">
                            <p class="text-gray-500 dark:text-gray-400 font-bold uppercase">Update</p>
                            <p class="font-black italic dark:text-gray-200">25 April 2026</p>
                        </div>
                    </div>
                </div>

                <!-- Layer Visualisasi -->
                <div class="panel p-6 border-0 shadow-sm rounded-3xl dark:bg-[#1b2e4b]">
                    <h5
                        class="font-black text-[10px] tracking-[0.2em] mb-5 text-gray-400 dark:text-gray-500 italic uppercase">
                        Layer Visualisasi</h5>
                    <div class="space-y-4">
                        <template x-for="layer in layers" :key="layer.id">
                            <div class="flex justify-between items-center group">
                                <span
                                    class="text-[11px] font-bold text-gray-700 dark:text-gray-300 group-hover:text-primary transition-all uppercase"
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
                    <h5
                        class="font-black text-[10px] tracking-[0.2em] mb-5 text-gray-400 dark:text-gray-500 italic uppercase">
                        Status Kesehatan Blok</h5>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 text-success">
                                <div class="w-2.5 h-2.5 rounded-full bg-success shadow-[0_0_8px_rgba(16,185,129,0.4)]">
                                </div>
                                <span class="text-[11px] font-bold uppercase tracking-tight">Healthy</span>
                            </div>
                            <span class="text-[11px] font-black italic dark:text-gray-200">18 Blok</span>
                        </div>
                        <div class="flex items-center justify-between text-warning">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-warning shadow-[0_0_8px_rgba(245,158,11,0.4)]">
                                </div>
                                <span class="text-[11px] font-bold uppercase tracking-tight">Moderate</span>
                            </div>
                            <span class="text-[11px] font-black italic dark:text-gray-200">5 Blok</span>
                        </div>
                        <div class="flex items-center justify-between text-danger">
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-danger shadow-[0_0_8px_rgba(239,68,68,0.4)]">
                                </div>
                                <span class="text-[11px] font-bold uppercase tracking-tight">Critical</span>
                            </div>
                            <span class="text-[11px] font-black italic dark:text-gray-200">1 Blok</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="xl:col-span-3 space-y-5">
                <!-- 1. INTEGRATED GIS MAP PANEL -->
                <div class="panel p-0 border-0 shadow-sm transition-all duration-500 flex flex-col overflow-hidden rounded-3xl dark:bg-[#1b2e4b] relative border border-gray-100 dark:border-white/5"
                    :class="isMapExpanded ? 'h-[750px] shadow-2xl ring-4 ring-primary/10' : 'h-[500px]'">

                    <div
                        class="p-4 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-white dark:bg-[#1b2e4b] z-20">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-primary/10 text-primary rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m6-3l5.447 2.724a1 1.01.553.894v10.764a1 1 0 01-1.447.894L15 17m-6 3l6-3m-6 0V7m6 10V4" />
                                </svg>
                            </div>
                            <h5
                                class="font-black text-[10px] tracking-widest text-gray-500 dark:text-gray-400 uppercase italic">
                                Interactive Spatial Intelligence</h5>
                        </div>
                        <div class="flex gap-2 text-gray-600 dark:text-gray-400">
                            <!-- Theater Mode Button -->
                            <button @click="toggleMapHeight()"
                                class="p-2 bg-gray-50 dark:bg-black/20 hover:bg-primary/10 rounded-xl border border-gray-100 dark:border-gray-700 transition-all shadow-sm">
                                <svg x-show="!isMapExpanded" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-width="2.5"
                                        d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3" />
                                </svg>
                                <svg x-show="isMapExpanded" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-width="2.5" d="M4 14h6m0 0v6m0-6L3 21m17-7h-6m0 0v6m0-6l7 7" />
                                </svg>
                            </button>

                            <div class="w-[1px] h-8 bg-gray-100 dark:bg-gray-700 mx-1"></div>
                            <!-- Pro-Zoom Controls -->
                            <button @click="zoomOut()"
                                class="p-2.5 bg-gray-50 dark:bg-black/20 hover:bg-primary hover:text-white rounded-xl border border-gray-100 dark:border-gray-700 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="3" d="M20 12H4" />
                                </svg>
                            </button>
                            <button @click="zoomIn()"
                                class="p-2.5 bg-gray-50 dark:bg-black/20 hover:bg-primary hover:text-white rounded-xl border border-gray-100 dark:border-gray-700 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="3" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div id="leafletMap" class="w-full h-full z-10 bg-[#1e1e2f]"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="panel border-0 shadow-sm rounded-3xl p-8 dark:bg-[#1b2e4b]">
                            <h5
                                class="font-black text-[10px] tracking-[0.2em] mb-8 text-gray-400 dark:text-gray-500 text-center italic uppercase">
                                Proporsi Kondisi Pohon (%) - <span class="text-primary"
                                    x-text="selectedBlock || 'Global'"></span></h5>
                            <div x-ref="pieChart" class="min-h-[300px]"></div>
                        </div>
                        <div class="panel border-0 shadow-sm rounded-3xl p-8 dark:bg-[#1b2e4b]">
                            <h5
                                class="font-black text-[10px] tracking-[0.2em] mb-8 text-gray-400 dark:text-gray-500 text-center italic uppercase">
                                Analisis Parameter Areal (%) - <span class="text-primary"
                                    x-text="selectedBlock || 'Global'"></span></h5>
                            <div x-ref="barChart" class="min-h-[300px]"></div>
                        </div>
                    </div>

                    <div class="panel border-0 shadow-sm rounded-3xl p-8 dark:bg-[#1b2e4b]">
                        <div
                            class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 border-b border-gray-50 dark:border-gray-800 pb-5 uppercase tracking-tighter">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-warning/10 text-warning rounded-lg"><svg class="w-5 h-5"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg></div>
                                <div>
                                    <h5
                                        class="font-black text-xs uppercase tracking-widest text-gray-900 dark:text-white-light italic">
                                        Tren Biometrik Vegetatif</h5>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter italic">
                                        Unit:
                                        <span class="text-primary italic"
                                            x-text="selectedBlock ? 'Unit Blok ' + selectedBlock : 'Rata-rata Seluruh Kebun'"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="resetToGlobal"
                                    class="btn btn-sm btn-outline-secondary rounded-lg text-[10px] font-black uppercase tracking-widest"
                                    x-show="selectedBlock">RESET DATA</button>
                                <span
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] bg-gray-50 dark:bg-black/20 px-3 py-1 rounded-full italic">Source:
                                    Ground-Check 2024</span>
                            </div>
                        </div>
                        <div class="relative min-h-[500px]">
                            <div x-ref="vegetativeTrendChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. AI ANALYSIS SECTION (Asli Desainmu) -->
            <div
                class="panel border-0 shadow-2xl p-0 overflow-hidden mt-8 bg-white dark:bg-gradient-to-br dark:from-[#1b2e4b] dark:to-[#060818] rounded-[2rem] border border-gray-100 dark:border-transparent transition-all duration-300 uppercase font-black">
                <div class="p-10">
                    <div
                        class="flex flex-col md:flex-row items-center justify-between gap-8 mb-12 text-gray-900 dark:text-white uppercase tracking-tighter">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-16 h-16 bg-primary text-white rounded-3xl flex items-center justify-center shadow-xl shadow-primary/20 animate-pulse">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl">Autonomous Prescriptive Engine</h4>
                                <p
                                    class="text-[10px] text-indigo-500 dark:text-indigo-300 tracking-[0.3em] mt-1 font-mono">
                                    Node: DSS-SAWIT-XAI-2025</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-4 bg-gray-50 dark:bg-black/30 p-3 rounded-[1.5rem] border border-gray-100 dark:border-white/10 w-full md:w-auto shadow-inner">
                            <div
                                class="px-5 py-2 bg-white dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/10 text-center">
                                <span
                                    class="text-[10px] text-gray-400 block mb-0.5 tracking-tighter uppercase font-bold">Selected
                                    Node</span>
                                <span class="text-sm text-primary font-black font-mono"
                                    x-text="selectedBlock || 'GLOBAL'"></span>
                            </div>
                            <button
                                class="btn btn-primary px-10 py-4 rounded-xl font-black text-[11px] tracking-[0.2em] active:scale-95 shadow-lg shadow-primary/20 transition-all"
                                @click="runInference" :disabled="!selectedBlock || isAnalyzing">
                                <span x-show="!isAnalyzing">EXECUTE AI ANALYSIS</span>
                                <span x-show="isAnalyzing" class="animate-pulse">PROCESSING NEURAL...</span>
                            </button>
                        </div>
                    </div>

                    <div
                        class="min-h-[200px] bg-gray-50/50 dark:bg-black/40 rounded-[2rem] border-2 border-dashed border-gray-200 dark:border-white/10 p-10 flex flex-col justify-center relative overflow-hidden transition-all duration-500">
                        <template x-if="inferenceResult && !isAnalyzing">
                            <div class="animate__animated animate__fadeInUp relative z-10">
                                <div class="flex items-center gap-3 mb-6 font-black uppercase">
                                    <span
                                        class="bg-emerald-500 !text-white text-[9px] px-4 py-1.5 rounded-full tracking-[0.2em] uppercase shadow-lg shadow-emerald-500/20">Success
                                        Inference</span>
                                    <h6 class="text-xs text-gray-800 dark:text-white tracking-widest">Diagnostic
                                        Report:
                                        Blok <span x-text="selectedBlock"></span></h6>
                                </div>
                                <!-- Grid Hasil AI Aslimu -->
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                                    <div
                                        class="lg:col-span-8 p-8 bg-white dark:bg-black/20 rounded-3xl border-l-[6px] border-l-emerald-500 shadow-sm border border-gray-100 dark:border-white/5 font-bold italic">
                                        <p
                                            class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-3 italic">
                                            Rekomendasi Preskriptif:</p>
                                        <p class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed italic tracking-tight"
                                            x-text="inferenceResult.recommendation"></p>
                                    </div>
                                    <div class="lg:col-span-4 space-y-4">
                                        <div
                                            class="bg-white dark:bg-black/20 p-5 rounded-2xl border border-gray-100 dark:border-white/5 shadow-sm flex flex-col justify-center text-center">
                                            <p
                                                class="text-[9px] text-gray-500 dark:text-gray-400 font-black mb-1 tracking-widest uppercase italic font-bold">
                                                Confidence Score</p>
                                            <p class="text-2xl font-black text-emerald-500 font-mono"
                                                x-text="inferenceResult.confidence + '%'"></p>
                                        </div>
                                        <div
                                            class="bg-white dark:bg-black/20 p-5 rounded-2xl border border-gray-100 dark:border-white/5 shadow-sm flex flex-col justify-center text-center">
                                            <p
                                                class="text-[9px] text-gray-500 dark:text-gray-400 font-black mb-1 tracking-widest uppercase italic font-bold">
                                                Vigor Index (NDVI)</p>
                                            <p class="text-2xl font-black text-indigo-500 dark:text-indigo-400 font-mono"
                                                x-text="inferenceResult.vigor"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <p x-show="!inferenceResult && !isAnalyzing"
                            class="text-center text-gray-400 dark:text-white/20 text-[10px] font-black uppercase tracking-[0.5em] italic">
                            System Idle - Standby for spatial input</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SCRIPTS (LOGIKA DIBERSIHKAN DARI DUPLIKASI) -->
        <script>
            document.addEventListener("alpine:init", () => {
                Alpine.data("detailKebun", () => ({
                    map: null,
                    selectedBlock: '',
                    isAnalyzing: false,
                    inferenceResult: null,
                    geoLayers: {},
                    isMapExpanded: false,
                    pieChart: null,
                    barChart: null,
                    trendChart: null,
                    kodeKebun: "1KAS",

                    layers: [{
                            id: 'satelit',
                            label: 'Basemap Satellite',
                            active: true
                        },
                        {
                            id: 'blocks',
                            label: 'Boundary (Afdeling)',
                            active: true
                        },
                        {
                            id: 'pemel',
                            label: 'Maintenance Layer',
                            active: true
                        },
                        {
                            id: 'lcc',
                            label: 'Layer Kacangan (LCC)',
                            active: true
                        },
                    ],

                    // Data BarChart sesuai gambar permintaan
                    arealTanamanGlobal: {
                        "Kacangan": 78.59,
                        "Pemeliharaan yang Kurang Baik": 8.24,
                        "Areal Tergenang": 0.15,
                        "Anak Kayu": 0.05
                    },

                    vegetativeData: {
                        'default': {
                            girth: [75, 76, 78, 79, 82, 85],
                            fronds: [32, 33, 33, 34, 35, 36],
                            length: [180, 182, 185, 188, 190, 192],
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']
                        },
                        '190A': {
                            girth: [60, 62, 63, 65, 66, 68],
                            fronds: [28, 29, 29, 30, 30, 31],
                            length: [150, 155, 158, 160, 162, 165],
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']
                        }
                    },

                    init() {
                        setTimeout(() => {
                            this.initMap();
                            this.renderCharts();
                            this.renderVegetativeTrend();
                        }, 500);
                        this.$watch('selectedBlock', (val) => {
                            this.updateCharts(val);
                        });
                    },

                    initMap() {
                        if (this.map) this.map.remove();
                        this.map = L.map('leafletMap', {
                            zoomControl: false,
                            attributionControl: false
                        }).setView([2.03394, 99.9952], 14);

                        this.geoLayers.satelit = L.tileLayer(
                            'https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                                maxZoom: 22
                            }).addTo(this.map);

                        fetch(`/api/spatial/blocks/${this.kodeKebun}`).then(r => r.json()).then(data => {
                            this.geoLayers.blocks = L.geoJSON(data, {
                                style: {
                                    color: 'white',
                                    weight: 2.5,
                                    fillOpacity: 0.1
                                },
                                onEachFeature: (f, l) => {
                                    l.bindTooltip(
                                        `<b>Wilayah:</b> ${f.properties.display_name}`, {
                                            sticky: true
                                        });
                                }
                            }).addTo(this.map);
                            this.map.fitBounds(this.geoLayers.blocks.getBounds(), {
                                padding: [50, 50]
                            });
                        });

                        fetch(`/api/spatial/maintenance/${this.kodeKebun}`).then(r => r.json()).then(
                            data => {
                                this.geoLayers.pemel = L.geoJSON(data, {
                                    style: (f) => ({
                                        color: f.properties.KETERANGAN ===
                                            'GENANGAN AIR/BANJIRAN' ? '#3b82f6' :
                                            '#ef4444',
                                        weight: 2,
                                        fillOpacity: 0.8
                                    }),
                                    onEachFeature: (f, l) => {
                                        l.bindTooltip(
                                            `<b>Blok:</b> ${f.properties.display_name}<br><small>${f.properties.KETERANGAN}</small>`, {
                                                sticky: true
                                            });
                                        l.on('click', (e) => {
                                            L.DomEvent.stopPropagation(e);
                                            this.selectedBlock = f.properties
                                                .display_name;
                                            l.setStyle({
                                                weight: 6,
                                                color: '#fbbf24',
                                                fillOpacity: 1
                                            });
                                            this.map.panTo(l.getBounds()
                                                .getCenter());
                                        });
                                    }
                                }).addTo(this.map);
                            });
                    },

                    toggleLayer(id) {
                        const layer = this.geoLayers[id];
                        if (layer) this.map.hasLayer(layer) ? this.map.removeLayer(layer) : this.map
                            .addLayer(layer);
                    },

                    toggleMapHeight() {
                        this.isMapExpanded = !this.isMapExpanded;
                        setTimeout(() => {
                            if (this.map) {
                                this.map.invalidateSize({
                                    animate: true
                                });
                            }
                        }, 500);
                    },

                    renderCharts() {
                        const isDark = document.documentElement.classList.contains('dark');
                        const textCol = isDark ? '#cbd5e1' : '#334155';

                        // PIE CHART ASLIMU
                        this.pieChart = new ApexCharts(this.$refs.pieChart, {
                            series: [92.5, 5.2, 2.3],
                            labels: ['Normal', 'Kerdil', 'Mati'],
                            chart: {
                                type: 'pie',
                                height: 350
                            },
                            colors: ['#0ea5e9', '#f59e0b', '#ef4444'],
                            legend: {
                                position: 'bottom',
                                labels: {
                                    colors: textCol
                                }
                            },
                            stroke: {
                                show: false
                            }
                        });
                        this.pieChart.render();

                        // BAR CHART (INTEGRASI MULTI-SERIES SESUAI GAMBAR)
                        const warnaAreal = ['#8BC34A', '#FF9800', '#F44336', '#795548'];
                        const seriesAreal = Object.entries(this.arealTanamanGlobal).map(([label, value],
                            index) => ({
                            name: label,
                            data: [value],
                            color: warnaAreal[index]
                        }));

                        this.barChart = new ApexCharts(this.$refs.barChart, {
                            chart: {
                                type: 'bar',
                                height: 350,
                                toolbar: {
                                    show: false
                                }
                            },
                            series: seriesAreal,
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '40%',
                                    dataLabels: {
                                        position: 'center'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: v => v + "%",
                                style: {
                                    fontWeight: 900,
                                    colors: ['#fff']
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
                                fontSize: '11px',
                                labels: {
                                    colors: textCol
                                },
                                markers: {
                                    radius: 4
                                }
                            },
                            grid: {
                                borderColor: isDark ? '#1b2e4b' : '#f1f2f3',
                                strokeDashArray: 4
                            },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: v => v + "%"
                                }
                            }
                        });
                        this.barChart.render();

                        // TREND CHART ASLIMU
                        const dataTrend = this.vegetativeData['default'];
                        this.trendChart = new ApexCharts(this.$refs.vegetativeTrendChart, {
                            series: [{
                                name: 'Lingkar Batang (cm)',
                                data: dataTrend.girth
                            }, {
                                name: 'Jumlah Pelepah',
                                data: dataTrend.fronds
                            }, {
                                name: 'Panjang Pelepah (cm)',
                                data: dataTrend.length
                            }],
                            chart: {
                                type: 'bar',
                                height: 450,
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
                            xaxis: {
                                categories: dataTrend.labels,
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
                            colors: ['#0ea5e9', '#10b981', '#f59e0b'],
                            grid: {
                                borderColor: isDark ? '#334155' : '#e2e8f0'
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    colors: textCol
                                }
                            }
                        });
                        this.trendChart.render();
                    },

                    updateCharts(blockId) {
                        const randomSet = () => [Math.floor(Math.random() * 100), Math.floor(Math.random() *
                            20), Math.floor(Math.random() * 10)];
                        this.pieChart.updateSeries(randomSet());

                        const newSeriesBar = Object.entries(this.arealTanamanGlobal).map(([label, value]) =>
                            ({
                                name: label,
                                data: [(Math.random() * 100).toFixed(2)]
                            }));
                        this.barChart.updateSeries(newSeriesBar);

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

                    resetToGlobal() {
                        this.selectedBlock = '';
                        this.map.fitBounds(this.geoLayers.blocks.getBounds());
                        this.inferenceResult = null;
                    },

                    runInference() {
                        if (!this.selectedBlock) return;
                        this.isAnalyzing = true;
                        this.inferenceResult = null;
                        setTimeout(() => {
                            this.isAnalyzing = false;
                            this.inferenceResult = {
                                recommendation: "Berdasarkan analisis spasial blok " + this
                                    .selectedBlock +
                                    ", terdeteksi defisiensi Nitrogen signifikan. Rekomendasi: Aplikasi segera Urea 1.5kg/pokok.",
                                confidence: 99.5,
                                vigor: 0.776
                            };
                        }, 2500);
                    },

                    zoomIn() {
                        this.map.zoomIn();
                    },
                    zoomOut() {
                        this.map.zoomOut();
                    }
                }));
            });
        </script>

        <style>
            #leafletMap {
                width: 100%;
                height: 100%;
                min-height: 440px;
                border-radius: 2rem;
                z-index: 10;
                background: #1e1e2f;
                cursor: crosshair;
            }

            .custom_switch:checked~span:before {
                background-color: #fff !important;
            }

            .panel {
                background: #fff;
                border-radius: 2rem;
                transition: all 0.3s;
            }

            .dark .panel {
                background: #1b2e4b;
            }

            .leaflet-tooltip {
                background: rgba(255, 255, 255, 0.9);
                border: none;
                border-radius: 8px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                font-weight: bold;
                font-family: inherit;
                font-size: 10px;
            }

            .dark .leaflet-tooltip {
                background: rgba(0, 0, 0, 0.8);
                color: white;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            [x-cloak] {
                display: none !important;
            }
        </style>
</x-layout.default>
