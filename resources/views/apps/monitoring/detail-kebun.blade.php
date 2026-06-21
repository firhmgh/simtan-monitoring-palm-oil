<x-layout.default>
    <!-- Dependensi GIS & Grafik Analitik -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="/assets/js/apexcharts.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        #leafletMap {
            height: 100% !important;
            width: 100% !important;
            min-height: 400px;
            background: #04131a !important;
            cursor: crosshair;
            border-radius: 1.5rem;
        }

        .map-fullscreen {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999 !important;
            border-radius: 0 !important;
        }

        .leaflet-control-zoom,
        .leaflet-control-attribution {
            display: none !important;
        }

        /* 1. Matikan pointer-events hanya pada container pane utama agar klik bisa tembus ke bawah */
        .leaflet-pane {
            pointer-events: none !important;
        }

        /* 2. HIDUPKAN KEMBALI pointer-events khusus untuk objek yang memang harus bisa diklik */
        .leaflet-interactive,
        .leaflet-popup-pane,
        .leaflet-tooltip-pane,
        .leaflet-control {
            pointer-events: auto !important;
        }

        .satellite-base {
            filter: brightness(0.8) contrast(1.2) saturate(0.8);
        }

        /* Critical Hotspot Animation */
        path.leaflet-interactive[fill="#ef4444"] {
            animation: critical-pulse 2s infinite;
        }

        @keyframes critical-pulse {
            0% {
                fill-opacity: 0.3;
                stroke-width: 1;
            }

            50% {
                fill-opacity: 0.7;
                stroke-width: 3;
            }

            100% {
                fill-opacity: 0.3;
                stroke-width: 1;
            }
        }

        .vristo-popup .leaflet-popup-content-wrapper {
            padding: 0 !important;
            overflow: hidden;
            border-radius: 12px !important;
            background: #fff !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        .dark .vristo-popup .leaflet-popup-content-wrapper {
            background: #1b2e4b !important;
            border: 1px solid #191e3a;
        }

        .vristo-popup .leaflet-popup-content {
            margin: 0 !important;
            width: 250px !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        @keyframes pulse-soft {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .animate-pulse-soft {
            animation: pulse-soft 3s infinite;
        }
    </style>

    <div x-data="detailKebun()" x-init="initComponent()" x-cloak
        class="space-y-6 pb-10 font-['Plus_Jakarta_Sans'] antialiased text-slate-800 dark:text-white-light text-left">

        <!-- HEADER & SELECTOR -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1 text-left">
                <!-- Navigasi Breadcrumb Standar SIMTAN -->
                <ul class="flex space-x-2 text-xs mb-2 text-white-dark tracking-widest font-black uppercase">
                    <li><a href="{{ route('index') }}" class="text-primary hover:underline font-black">Monitoring</a></li>
                    <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 font-black text-slate-400">Detail Kebun</li>
                </ul>
                <h1 class="text-3xl font-black tracking-tighter italic text-slate-900 dark:text-white leading-none">
                    {{ $kebun->nama_kebun }} <span class="text-primary not-italic">({{ $kebun->kebun }})</span>
                </h1>
                <p class="text-xs font-bold italic text-slate-500 dark:text-slate-400 mt-2 border-l-2 border-primary pl-2 tracking-tight">
                    Sistem Integrasi Terpadu - PTPN IV Regional I
                </p>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] font-bold tracking-widest leading-none mt-2">
                    {{ $kebun->nama_distrik }} • <span x-text="totalBlocks"></span> Unit Blok •
                    {{ number_format($infoKebun['luas'] ?? 0, 2, ',', '.') }} Ha Luas Areal
                </p>
            </div>

            <div
                class="flex items-center gap-4 p-2 pl-5 bg-white dark:bg-[#0e1726] rounded-2xl border border-slate-100 shadow-sm">
                <div class="hidden lg:block text-right border-r border-slate-100 dark:border-slate-800 pr-4">
                    <p class="text-[9px] font-black text-slate-400 tracking-widest mb-1">Dimensi Waktu</p>
                    <p class="text-[10px] font-black text-primary italic">Tersinkronisasi</p>
                </div>
                <select x-model="selectedPeriode" @change="changePeriode()"
                    class="form-select py-2.5 text-xs font-black rounded-xl border-none bg-slate-50 dark:bg-black/20 focus:ring-2 focus:ring-primary/20 cursor-pointer w-[240px] text-slate-700 dark:text-white">
                    @foreach ($listPeriode as $slug => $info)
                        <option value="{{ $slug }}">{{ strtoupper($info['label']) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <div class="space-y-6" x-show="!isMapExpanded">
                <!-- STATUS PERFORMA BLOK -->
                <div class="panel p-6 border-none rounded-[2rem] bg-white dark:bg-[#0e1726] shadow-xl overflow-hidden">
                    <h5 class="font-black text-[10px] tracking-[0.2em] mb-6 text-slate-400 italic border-b pb-2">
                        Identifikasi Performa</h5>
                    <div class="space-y-4 text-[11px] font-black">
                        <template x-for="(list, key) in categorizedBlocks" :key="key">
                            <div
                                class="group pt-2 border-t border-slate-50 dark:border-white/5"
                                :class="{'!border-t-0 !pt-0': key === 'healthy'}">
                                <div class="flex justify-between mb-2"
                                    :class="key === 'healthy' ? 'text-success' : (key === 'moderate' ? 'text-warning' :
                                        'text-danger')">
                                    <span
                                        x-text="key === 'healthy' ? 'Optimal' : (key === 'moderate' ? 'Peringatan' : 'Kritis')"></span>
                                    <span x-text="list.length"></span>
                                </div>
                                <div class="max-h-24 overflow-y-auto custom-scrollbar flex flex-wrap gap-1">
                                    <template x-for="bid in list" :key="bid">
                                        <span @click="zoomToBlock(bid)"
                                            class="px-2 py-0.5 rounded text-[9px] cursor-pointer transition-all"
                                            :class="key === 'healthy' ? 'bg-success/10 hover:bg-success hover:text-white' :
                                                (key === 'moderate' ?
                                                    'bg-warning/10 hover:bg-warning hover:text-white' :
                                                    'bg-danger/10 hover:bg-danger hover:text-white')"
                                            x-text="bid"></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- LAYER TOGGLE -->
                <div class="panel p-6 border-none rounded-[2rem] bg-white dark:bg-[#0e1726] shadow-xl">
                    {{-- Panel kontrol lapisan GIS yang dapat diaktifkan/nonaktifkan --}}
                    <h5 class="font-black text-[10px] mb-6 text-slate-400 italic border-b pb-2">Lapisan
                        Peta</h5>
                    <div class="space-y-4">
                        <template x-for="layer in layers" :key="layer.id">
                            <div class="flex justify-between items-center group cursor-pointer"
                                @click="toggleLayer(layer.id)">
                                <div class="flex flex-col text-left">
                                    <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400"
                                        x-text="layer.label"></span>
                                    <span x-show="!layer.exists"
                                        class="text-[8px] text-danger font-black mt-1">Offline</span>
                                </div>
                                <label class="w-8 h-4 relative mb-0">
                                    <input type="checkbox"
                                        class="custom_switch absolute w-full h-full opacity-0 z-10 cursor-pointer"
                                        x-model="layer.active" :disabled="!layer.exists" />
                                    <span class="bg-[#ebedf2] dark:bg-dark block h-full rounded-full transition-all"
                                        :class="layer.active ? 'bg-primary' : 'bg-slate-200'"></span>
                                </label>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- PETA PANEL -->
            <div :class="isMapExpanded ? 'xl:col-span-4' : 'xl:col-span-3'"
                class="space-y-6 transition-all duration-500">
                <div class="panel p-0 border-0 shadow-sm flex flex-col overflow-hidden bg-white dark:bg-[#1b2e4b] relative border border-gray-100 dark:border-white/5"
                    :class="isMapExpanded ? 'map-fullscreen' : 'h-[550px] rounded-3xl'">
                    <div
                        class="p-4 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-white dark:bg-[#1b2e4b] z-[1001]">
                        {{-- Label panel peta interaktif --}}
                        <div class="flex items-center gap-2 text-primary font-black italic tracking-widest text-[10px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m6-3l5.447 2.724a1 1 0 010.553 0.894v10.764a1 1 0 01-1.447 0.894L15 17m-6 3l6-3m-6 0V7m6 10V4" />
                            </svg>
                            Sistem Informasi Spasial
                        </div>
                        <div class="flex gap-2">
                            <button @click="toggleMapHeight()"
                                class="p-2 px-4 rounded-xl transition-all shadow-sm flex items-center gap-2"
                                :class="isMapExpanded ? 'bg-danger text-white' : 'bg-gray-100 dark:bg-black/20 text-primary'">
                                {{-- Tombol mode layar penuh untuk peta --}}
                                <span class="text-[10px] font-black"
                                    x-text="isMapExpanded ? 'Tutup Layar Penuh' : 'Mode Layar Penuh'"></span>
                            </button>
                            <button @click="map.zoomIn()"
                                class="p-2 bg-primary text-white rounded-xl w-10 shadow-lg active:scale-90">+</button>
                            <button @click="map.zoomOut()"
                                class="p-2 bg-primary text-white rounded-xl w-10 shadow-lg active:scale-90">-</button>
                        </div>
                    </div>
                    <div class="flex-1 relative overflow-hidden bg-[#04131a]">
                        <div id="leafletMap" class="w-full h-full z-10"></div>
                    </div>
                </div>

                <!-- ANALYTICS CHARTS -->
                <div x-show="!isMapExpanded" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div
                        class="panel p-8 rounded-[2.5rem] bg-white dark:bg-[#0e1726] border-none shadow-xl text-center">
                        {{-- Diagram proporsi kondisi kesehatan pohon per blok --}}
                        <h5
                            class="text-[11px] font-black text-slate-400 text-left border-l-4 border-primary pl-4 mb-10">
                            Proporsi Kondisi Blok</h5>
                        <div x-ref="pieChart" class="min-h-[320px]"></div>
                    </div>
                    <div
                        class="panel p-8 rounded-[2.5rem] bg-white dark:bg-[#0e1726] border-none shadow-xl text-center">
                        <h5
                            class="text-[11px] font-black text-slate-400 text-left border-l-4 border-success pl-4 mb-10">
                            Cakupan Parameter</h5>
                        <div x-ref="barChart" class="min-h-[320px]"></div>
                    </div>
                </div>

                <!-- BIOMETRIK VEGETATIF -->
                <template x-if="vegetatifData && vegetatifData.labels.length > 0">
                    <div
                        class="panel p-10 rounded-[3.5rem] bg-white dark:bg-[#0e1726] border-none shadow-2xl space-y-8">
                        <h4
                            class="text-2xl font-black italic text-slate-800 dark:text-white tracking-tighter text-left leading-none">
                            Analisis Biometrik Vegetatif</h4>
                        <div x-ref="vegetatifChart" class="min-h-[480px]"></div>
                        <div
                            class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8 border-t border-slate-100 dark:border-white/5 text-left font-black">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2.5" d="M12 2v20m0-20l-4 4m4-4l4 4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[9px] text-slate-400 mb-1">Rasio Keliling Batang</p>
                                    <p class="text-lg dark:text-white" x-text="avgGirth"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-success/10 flex items-center justify-center text-success">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[9px] text-slate-400 mb-1"> Indeks Produksi Pelepah</p>
                                    <p class="text-lg dark:text-white" x-text="avgFrondCount"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[9px] text-slate-400 mb-1">Rasio Ekspansi Pelepah</p>
                                    <p class="text-lg dark:text-white" x-text="avgFrondLen"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- AI ENGINE -->
                <div
                    class="panel p-0 rounded-[3rem] border-none shadow-2xl overflow-hidden bg-gradient-to-br from-slate-900 to-slate-800 dark:from-black dark:to-slate-900 relative text-left">
                    <div class="p-6 relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center gap-8 mb-6">
                            <div
                                class="w-20 h-20 bg-primary rounded-[2rem] flex items-center justify-center shadow-2xl shadow-primary/40 animate-pulse-soft">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h4
                                    class="text-xl font-black italic text-slate-100 dark:text-white tracking-tighter leading-none">
                                    Autonomous Prescriptive Engine</h4>
                                <p
                                    class="text-[10px] text-indigo-500 font-bold tracking-[0.3em] opacity-70 mt-2 font-mono">
                                    Node: DSS-SistemPakar-StandarEnterprise</p>
                            </div>
                        </div>
                        <div
                            class="min-h-[160px] rounded-[2.2rem] bg-white/5 dark:bg-black/40 border border-white/10 p-10 flex flex-col justify-center">
                            <template x-if="isThinkingBlok">
                                <div class="flex flex-col items-center gap-4 animate-pulse">
                                    <div
                                        class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <p class="text-[10px] font-black text-slate-400 tracking-[0.4em] italic">
                                        Logika Pemrosesan AI...</p>
                                </div>
                            </template>
                            <template x-if="inferenceResult && !isThinkingBlok">
                                <div class="animate__animated animate__fadeInUp space-y-4">
                                    <!-- Label Status -->
                                    <div
                                        class="inline-block px-4 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-black tracking-widest">
                                        High Confidence Recommendation
                                    </div>

                                    <!-- HASIL AI (Gunakan x-html di sini) -->
                                    <div class="prose prose-invert max-w-none"
                                        x-html="formatAiOutput(inferenceResult.recommendation)">
                                    </div>
                                </div>
                            </template>
                            <div x-show="!inferenceResult && !isThinkingBlok" class="text-center opacity-40">
                                <p class="text-slate-400 text-[10px] font-black tracking-widest">Pilih unit
                                    blok pada peta untuk analisis preskriptif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LOGIKA GIS CORE -->
    <script>
        document.addEventListener("alpine:init", () => {
            // ADVANCED: Orthophoto Canvas Sharpener
            L.TileLayer.TransparentWhite = L.TileLayer.extend({
                createTile: function(coords, done) {
                    const tile = document.createElement('canvas');
                    const ctx = tile.getContext('2d');
                    tile.width = tile.height = 256;
                    const img = new Image();
                    img.crossOrigin = "Anonymous";
                    img.onload = function() {
                        ctx.imageSmoothingEnabled = false;
                        ctx.drawImage(img, 0, 0);
                        const imageData = ctx.getImageData(0, 0, 256, 256);
                        const data = imageData.data;
                        for (let i = 0; i < data.length; i += 4) {
                            if (data[i] > 248 && data[i + 1] > 248 && data[i + 2] > 248) data[i +
                                3] = 0;
                        }
                        ctx.putImageData(imageData, 0, 0);
                        done(null, tile);
                    };
                    img.onerror = () => {
                        ctx.clearRect(0, 0, 256, 256);
                        done(null, tile);
                    };
                    img.src = this.getTileUrl(coords);
                    return tile;
                }
            });

            window.detailKebun = () => ({
                map: null,
                isMapExpanded: false,
                isThinkingBlok: false,
                geoLayers: {},
                masterBounds: null,
                inferenceResult: null,
                totalBlocks: 0,
                selectedPeriode: '{{ $activeSlug }}',
                categorizedBlocks: {
                    healthy: [],
                    moderate: [],
                    critical: []
                },

                avgGirth: 0,
                avgFrondCount: 0,
                avgFrondLen: 0,

                kondisiPohon: @json($kondisiPohon ?? []),
                arealTanaman: @json($arealTanaman ?? []),
                vegetatifData: {
                    labels: @json($vegetatif['vegLabels'] ?? []),
                    lingkar: @json($vegetatif['vegLingkar'] ?? []),
                    jumlah: @json($vegetatif['vegJumlah'] ?? []),
                    panjang: @json($vegetatif['vegPanjang'] ?? []),
                },

                layers: [{
                        id: 'basemap',
                        label: 'Citra Satelit',
                        active: true,
                        exists: true
                    },
                    {
                        id: 'batas',
                        label: 'Batas Afdeling',
                        active: true,
                        exists: false,
                        apiType: 'batas'
                    },
                    {
                        id: 'blok',
                        label: 'Unit Blok (Detail)',
                        active: true,
                        exists: false,
                        apiType: 'blok'
                    },
                    {
                        id: 'konpokok',
                        label: 'Titik Kondisi Pohon',
                        active: false,
                        exists: false,
                        apiType: 'konpokok'
                    },
                    {
                        id: 'pemeliharaan',
                        label: 'Anomali Lapangan',
                        active: false,
                        exists: false,
                        apiType: 'pemeliharaan'
                    },
                    {
                        id: 'kacangan',
                        label: 'Tanaman Kacangan',
                        active: false,
                        exists: false,
                        apiType: 'kacangan'
                    }
                ].filter(l => l.id === 'basemap' || @json($availableLayers).includes(l.apiType)),

                initComponent() {
                    this.masterBounds = L.latLngBounds();
                    this.calculateVegStats();
                    setTimeout(() => {
                        this.initMap();
                        this.renderCharts();
                        if (this.vegetatifData.labels.length > 0) this.renderVegetatifChart();
                    }, 400);
                },

                calculateVegStats() {
                    const d = this.vegetatifData;
                    if (d.labels.length > 0) {
                        const getAvg = (arr) => (arr.reduce((a, b) => a + b, 0) / arr.length).toFixed(3);

                        this.avgGirth = getAvg(d.lingkar); // Hasil: 0.137
                        this.avgFrondCount = getAvg(d.jumlah); // Hasil: 0.044 
                        this.avgFrondLen = getAvg(d.panjang); // Hasil: 0.208
                    }
                },

                async initMap() {
                    if (this.map) this.map.remove();
                    this.map = L.map('leafletMap', {
                        zoomControl: false,
                        attributionControl: false,
                        preferCanvas: false
                    }).setView([{{ $kebun->latitude ?? 2.03394 }},
                        {{ $kebun->longitude ?? 99.9952 }}
                    ], 15);

                    // PANE MANAGEMENT: Pastikan urutan Z-Index benar
                    this.map.createPane('blokPane').style.zIndex = 400; // Paling bawah
                    this.map.createPane('batasPane').style.zIndex = 450;
                    this.map.createPane('overlayPane').style.zIndex = 500;
                    this.map.createPane('pointPane').style.zIndex = 600; // Paling atas (Pohon)

                    this.geoLayers.basemap = L.tileLayer(
                        'https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                            maxZoom: 22,
                            className: 'satellite-base'
                        }).addTo(this.map);

                    if ('{{ $kebun->tile_url ?? '' }}') {
                        this.geoLayers.ortho = new L.TileLayer.TransparentWhite(
                            '{{ $kebun->tile_url }}', {
                                maxZoom: 22,
                                maxNativeZoom: 20,
                                zIndex: 10
                            }).addTo(this.map);
                    }

                    const kode = '{{ $kebun->kebun }}';

                    await Promise.all([
                        this.fetchLayer(kode, 'batas', 'batas', {
                            pane: 'batasPane',
                            style: {
                                color: 'yellow',
                                weight: 4,
                                fillOpacity: 0.1,
                                fillColor: 'transparent'
                            }
                        }),
                        this.fetchLayer(kode, 'blok', 'blok', {
                            pane: 'blokPane',
                            style: (f) => ({
                                color: 'white',
                                weight: 1,
                                fillOpacity: 0.4,
                                fillColor: f.properties.fill_color
                            })
                        }),
                        this.fetchLayer(kode, 'pemeliharaan', 'pemeliharaan', {
                            pane: 'overlayPane',
                            style: {
                                color: '#ef4444',
                                weight: 2,
                                fillOpacity: 0.5,
                                dashArray: '4,4'
                            }
                        }),
                        this.fetchLayer(kode, 'kacangan', 'kacangan', {
                            pane: 'overlayPane',
                            style: {
                                color: '#84cc16',
                                weight: 1.5,
                                fillOpacity: 0.6,
                                fillColor: '#a3e635'
                            }
                        }),
                        this.fetchLayer(kode, 'konpokok', 'konpokok', {
                            pane: 'pointPane',
                            pointToLayer: (f, latlng) => {
                                // LOGIKA WARNA POHON
                                const status = f.properties.KONPOKOK ? f.properties
                                    .KONPOKOK.toUpperCase() : '';
                                let color = '#94a3b8'; // Default
                                if (status.includes('NORMAL')) color = '#10b981';
                                else if (status.includes('KERDIL')) color = '#f59e0b';
                                else if (status.includes('MATI')) color = '#ef4444';
                                else if (f.properties.fill_color) color = f.properties
                                    .fill_color;

                                return L.circleMarker(latlng, {
                                    radius: 2.5,
                                    fillColor: color,
                                    color: "#fff",
                                    weight: 0.5,
                                    fillOpacity: 1
                                });
                            }
                        })
                    ]);

                    if (this.masterBounds.isValid()) this.map.fitBounds(this.masterBounds, {
                        padding: [50, 50]
                    });
                },

                async fetchLayer(kode, type, id, options) {
                    try {
                        const response = await fetch(
                            `/spatial-data/${kode}/${type}?periode=${this.selectedPeriode}`);
                        const data = await response.json();
                        if (!data.features || data.features.length === 0) return;

                        const instance = L.geoJSON(data, {
                            ...options,
                            onEachFeature: (f, l) => {
                                const p = f.properties;
                                l.bindTooltip(`Unit: ${p.BLOK || p.AFDELING || 'N/A'}`, {
                                    sticky: true
                                });

                                // Injeksi logika IPHI (Health Analysis) dari Backend
                                if (id === 'blok' && p.analysis) {
                                    const status = p.analysis.status;
                                    this.categorizedBlocks[status].push(p.BLOK);

                                    let fillColor = '#10b981';
                                    if (status === 'moderate') fillColor = '#f59e0b';
                                    if (status === 'critical') fillColor = '#ef4444';
                                    l.setStyle({
                                        fillColor: fillColor
                                    });
                                }

                                // DYNAMIC POPUP METADATA
                                let bodyHtml = '';
                                switch (id) {
                                    case 'batas':
                                        bodyHtml =
                                            `<div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">KEBUN</span><span class="font-bold text-slate-800 dark:text-white-light">${p.KEBUN || '-'}</span></div><div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">AFD</span><span class="font-bold text-slate-800 dark:text-white-light">${p.AFDELING || '-'}</span></div><div class="flex justify-between border-t border-slate-100 dark:border-slate-800 mt-1 pt-1"><span class="text-slate-500 dark:text-slate-400">LUAS ADM</span><span class="font-black text-primary">${p.LUAS_ADM || 0} HA</span></div><div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">LUAS SHP</span><span class="font-black text-success">${p.LUAS_SHP || 0} HA</span></div><div class="mt-1 border-t border-slate-100 dark:border-slate-800 pt-1 flex justify-between text-primary"><span class="text-slate-500 dark:text-slate-400">SURVIVAL</span><span class="font-black">${p.survival_rate || 0}%</span></div>`;
                                        break;
                                    case 'blok':
                                        bodyHtml =
                                            `<div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">KEBUN</span><span class="font-bold text-slate-800 dark:text-white-light">${p.KEBUN || '-'}</span></div><div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">AFD</span><span class="font-bold text-slate-800 dark:text-white-light">${p.AFDELING || '-'}</span></div><div class="flex justify-between border-t border-slate-100 dark:border-slate-800 mt-1 pt-1 text-primary"><span class="text-slate-500 dark:text-slate-400">BLOK ID</span><span class="font-black">${p.BLOK || '-'}</span></div>`;
                                        break;
                                    default:
                                        Object.keys(p).forEach(k => {
                                            const blacklist = ['fill_color',
                                                'layer_type', 'path', 'layer',
                                                'std_afdeling', 'std_blok',
                                                'db_found', 'LUAS_ADM',
                                                'survival_rate',
                                                'SURVIVAL_RATE', 'SURVIVAL'
                                            ];
                                            if (!blacklist.includes(k) && p[k] !==
                                                null) {
                                                bodyHtml +=
                                                    `<div class="flex justify-between gap-4"><span class="text-[8px] font-bold text-slate-400 dark:text-slate-500">${k}</span><span class="text-[9px] font-black text-slate-800 dark:text-white-light truncate">${p[k]}</span></div>`;
                                            }
                                        });
                                }

                                l.bindPopup(
                                    `<div class="vristo-popup p-4 min-w-[220px] font-['Plus_Jakarta_Sans']"><div class="flex justify-between items-start mb-2 border-b-2 border-primary/10 pb-2"><span class="text-[9px] font-black text-primary tracking-tighter">${id}</span><span class="text-[10px] font-black text-slate-800 dark:text-white">#${p.BLOK || p.AFDELING || 'N/A'}</span></div><div class="space-y-1 text-[9px] font-bold text-slate-600 dark:text-slate-300">${bodyHtml}</div></div>`, {
                                        autoPan: true,
                                        autoPanPadding: L.point(50,
                                            50
                                        ), // Jarak aman 50px dari tepi peta agar tidak kepotong
                                        offset: L.point(0, -
                                            5)
                                    }
                                );

                                l.on('click', (e) => {
                                    L.DomEvent.stopPropagation(e);
                                    if (id === 'blok' || id === 'batas') this
                                        .analyzeBlock(p.BLOK || p.AFDELING);
                                });

                                if (id === 'blok') this.totalBlocks++;
                            }
                        });

                        this.geoLayers[id] = instance;
                        const lyrDef = this.layers.find(ly => ly.id === id);
                        if (lyrDef) {
                            lyrDef.exists = true;
                            if (lyrDef.active) instance.addTo(this.map);
                        }
                        if (instance.getBounds().isValid()) this.masterBounds.extend(instance
                            .getBounds());
                    } catch (e) {
                        console.error(`Layer ${id} Error:`, e);
                    }
                },

                async analyzeBlock(bid) {
                    if (!bid || this.isThinkingBlok) return;
                    this.inferenceResult = null;
                    this.isThinkingBlok = true;

                    try {
                        const res = await fetch(`/api/ai/block-insight`, {
                            method: 'POST', // Pastikan ini POST
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Wajib ada untuk POST
                            },
                            body: JSON.stringify({
                                kebun: '{{ $kebun->kebun }}',
                                blok_id: bid,
                                periode: this.selectedPeriode
                            })
                        });

                        // Cek jika response error (misal 405, 500, 422)
                        if (!res.ok) {
                            const errorText = await res.text();
                            console.error("Server Error:", errorText);
                            this.inferenceResult = {
                                recommendation: "Gagal terhubung ke AI (Error " + res.status +
                                    "). Cek koneksi atau konfigurasi AI."
                            };
                            return;
                        }

                        const result = await res.json();
                        if (result.status === 'success') {
                            this.inferenceResult = {
                                recommendation: result.data.rekomendasi_ai
                            };
                        } else {
                            this.inferenceResult = {
                                recommendation: "AI tidak memberikan respon valid."
                            };
                        }
                    } catch (e) {
                        console.error("JS Error:", e);
                        this.inferenceResult = {
                            recommendation: "Terjadi kesalahan sistem: " + e.message
                        };
                    } finally {
                        this.isThinkingBlok = false;
                    }
                },

                formatAiOutput(text) {
                    if (!text) return "Memproses narasi...";

                    let cleanText = text.trim();

                    // 1. Markdown Bold ke Class Native Vristo
                    cleanText = cleanText.replace(/\*\*(.*?)\*\*/g,
                        '<b class="text-slate-800 dark:text-white-light font-bold">$1</b>');

                    // 2. Deteksi baris "Label: Value" (seperti Unit: AFD04) agar lebih rapi
                    cleanText = cleanText.replace(/^([^:\n]+):/gm,
                        '<span class="text-primary font-bold text-[10px] tracking-widest block mb-1">$1</span>'
                    );

                    let lines = cleanText.split('\n');
                    let html = '';
                    let inList = false;

                    lines.forEach(line => {
                        let trimmed = line.trim();
                        if (!trimmed) return;

                        // Jika baris adalah list bullet
                        if (trimmed.startsWith('*') || trimmed.startsWith('-')) {
                            if (!inList) {
                                html += '<ul class="space-y-2 my-3 list-disc list-inside text-sm">';
                                inList = true;
                            }
                            html +=
                                `<li class="text-white-dark dark:text-white-light/80">${trimmed.substring(1).trim()}</li>`;
                        }
                        // Jika baris adalah penomoran (1. 2. 3.)
                        else if (/^\d+\./.test(trimmed)) {
                            if (inList) {
                                html += '</ul>';
                                inList = false;
                            }
                            let num = trimmed.split('.')[0];
                            let content = trimmed.split('.').slice(1).join('.').trim();
                            // Desain gelembung nomor yang lebih kecil dan nyambung dengan tema
                            html += `
                <div class="flex gap-3 mb-4 mt-4">
                    <span class="flex-shrink-0 w-6 h-6 bg-primary text-white rounded-full flex items-center justify-center text-[10px] font-black">${num}</span>
                    <div class="text-white-dark dark:text-white-light/90 text-sm leading-relaxed">${content}</div>
                </div>`;
                        }
                        // Paragraf biasa atau Judul Laporan
                        else {
                            if (inList) {
                                html += '</ul>';
                                inList = false;
                            }
                            // Jika teks diawali "Laporan Audit", buat jadi Heading kecil
                            if (trimmed.includes('Laporan Audit')) {
                                html +=
                                    `<h5 class="text-lg font-black text-slate-800 dark:text-white mb-4 border-b border-white/10 pb-2 italic">${trimmed}</h5>`;
                            } else {
                                html +=
                                    `<p class="mb-3 text-sm text-white-dark dark:text-white-light/70">${trimmed}</p>`;
                            }
                        }
                    });

                    if (inList) html += '</ul>';
                    return html;
                },

                zoomToBlock(bid) {
                    if (!this.geoLayers.blok) return;
                    this.geoLayers.blok.eachLayer(l => {
                        if ((l.feature.properties.BLOK || l.feature.properties.std_blok) === bid) {
                            this.map.fitBounds(l.getBounds(), {
                                padding: [100, 100],
                                maxZoom: 18
                            });
                            l.openPopup();
                            this.analyzeBlock(bid);
                        }
                    });
                },

                toggleLayer(id) {
                    const def = this.layers.find(l => l.id === id);
                    if (!def || !def.exists) return;
                    def.active = !def.active;
                    if (id === 'basemap') {
                        def.active ? this.map.addLayer(this.geoLayers.basemap) : this.map.removeLayer(this
                            .geoLayers.basemap);
                        if (this.geoLayers.ortho) def.active ? this.map.addLayer(this.geoLayers.ortho) :
                            this.map.removeLayer(this.geoLayers.ortho);
                    } else if (this.geoLayers[id]) {
                        def.active ? this.map.addLayer(this.geoLayers[id]) : this.map.removeLayer(this
                            .geoLayers[id]);
                    }
                },

                renderCharts() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const textCol = isDark ? '#94a3b8' : '#64748b';
                    if (this.$refs.pieChart) {
                        new ApexCharts(this.$refs.pieChart, {
                            series: Object.values(this.kondisiPohon || {}),
                            labels: Object.keys(this.kondisiPohon || {}).map(l => l.toUpperCase()),
                            chart: {
                                type: 'donut',
                                height: 350,
                                fontFamily: 'Plus Jakarta Sans'
                            },
                            colors: ['#10b981', '#f59e0b', '#f43f5e'],
                            dataLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 900
                                }
                            },
                            stroke: {
                                width: 0
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    colors: textCol
                                },
                                fontWeight: 800
                            },
                            tooltip: {
                                theme: isDark ? 'dark' : 'light',
                                y: {
                                    formatter: val => `${val}%`
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '75%',
                                        labels: {
                                            show: true,
                                            name: {
                                                fontWeight: 900
                                            },
                                            value: {
                                                fontWeight: 900,
                                                color: textCol,
                                                formatter: (v) => v + "%"
                                            },
                                            total: {
                                                show: true,
                                                label: 'TOTAL',
                                                color: '#4361ee',
                                                fontWeight: 900,
                                                formatter: (w) => Math.round(w.globals.seriesTotals
                                                    .reduce((a, b) => a + b, 0)) + "%"
                                            }
                                        }
                                    }
                                }
                            }
                        }).render();
                    }
                    if (this.$refs.barChart) {
                        new ApexCharts(this.$refs.barChart, {
                            series: [{
                                name: 'Cakupan',
                                data: Object.values(this.arealTanaman || {})
                            }],
                            chart: {
                                type: 'bar',
                                height: 350,
                                toolbar: {
                                    show: false
                                },
                                fontFamily: 'Plus Jakarta Sans'
                            },
                            colors: ['#8BC34A', '#FF9800', '#F44336', '#795548'],
                            plotOptions: {
                                bar: {
                                    borderRadius: 12,
                                    columnWidth: '40%',
                                    distributed: true,
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                offsetY: -25,
                                style: {
                                    fontWeight: 900,
                                    colors: [textCol]
                                },
                                formatter: (v) => v.toFixed(2) + "%"
                            },
                            xaxis: {
                                categories: Object.keys(this.arealTanaman || {}),
                                labels: {
                                    style: {
                                        colors: textCol,
                                        fontWeight: 800,
                                        fontSize: '10px'
                                    }
                                },
                                axisBorder: {
                                    show: false
                                }
                            },
                            yaxis: {
                                max: 100,
                                labels: {
                                    style: {
                                        colors: textCol,
                                        fontWeight: 700
                                    },
                                    formatter: (v) => v.toFixed(0) + "%"
                                }
                            },
                            tooltip: {
                                theme: isDark ? 'dark' : 'light',
                                y: {
                                    title: {
                                        formatter: () => ''
                                    },
                                    formatter: (v) => `<b>${v.toFixed(2)}%</b>`
                                }
                            },
                            grid: {
                                borderColor: isDark ? '#1e293b' : '#f1f5f9',
                                strokeDashArray: 4
                            },
                            legend: {
                                show: false
                            }
                        }).render();
                    }
                },

                renderVegetatifChart() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const textCol = isDark ? '#94a3b8' : '#64748b';

                    new ApexCharts(this.$refs.vegetatifChart, {
                        series: [{
                                name: 'Rasio Lingkar Batang (LB/KC)',
                                data: this.vegetatifData.lingkar
                            },
                            {
                                name: 'Indeks Jumlah Pelepah (JP/KC)',
                                data: this.vegetatifData.jumlah
                            },
                            {
                                name: 'Rasio Panjang Pelepah (PP/KC)',
                                data: this.vegetatifData.panjang
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 480,
                            fontFamily: 'Plus Jakarta Sans',
                            toolbar: {
                                show: true
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 1000
                            }
                        },
                        colors: ['#2196F3', '#4CAF50', '#FF9800'],
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '60%',
                                borderRadius: 8,
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -20,
                            style: {
                                fontSize: '9px',
                                fontWeight: 900,
                                colors: [textCol]
                            },
                            formatter: (val) => val.toFixed(3)
                        },
                        xaxis: {
                            categories: this.vegetatifData.labels,
                            labels: {
                                rotate: -45,
                                rotateAlways: true,
                                style: {
                                    colors: textCol,
                                    fontWeight: 700,
                                    fontSize: '10px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: textCol,
                                    fontWeight: 700
                                },
                                formatter: (v) => v.toFixed(3)
                            }
                        },
                        grid: {
                            borderColor: isDark ? '#1e293b' : '#f1f5f9',
                            strokeDashArray: 5
                        },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: {
                                formatter: (v) => v.toFixed(3)
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                colors: textCol
                            },
                            fontWeight: 800
                        }
                    }).render();
                },

                toggleMapHeight() {
                    this.isMapExpanded = !this.isMapExpanded;
                    setTimeout(() => this.map.invalidateSize(), 400);
                },

                changePeriode() {
                    window.location.href =
                        `/monitoring/detail-areal/{{ $kebun->id ?? $kebun->kebun }}?periode=${this.selectedPeriode}`;
                }
            });
        });
    </script>
</x-layout.default>
