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

        .satellite-base {
            filter: brightness(0.9) contrast(1.1);
        }

        /* Vristo Popup & Tooltip Styling */
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
            width: 230px !important;
        }

        .vristo-popup .leaflet-popup-tip {
            background: #4361ee !important;
        }

        .custom-leaflet-popup .leaflet-popup-content-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 5px;
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

        <!-- 1. HEADER -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1 text-left">
                <nav class="flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-primary/60">
                    <a href="{{ route('monitoring.data-kebun') }}"
                        class="hover:text-primary transition-colors italic">Daftar Kebun</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-400 font-black">Analisis Bio-Spasial</span>
                </nav>
                <h1 class="text-3xl font-black tracking-tighter italic text-slate-900 dark:text-white leading-none">
                    {{ $kebun->nama_kebun }} <span class="text-primary not-italic">({{ $kebun->kebun }})</span>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] font-bold tracking-widest leading-none mt-2">
                    {{ $kebun->nama_distrik }} • {{ array_sum($statusCounts) }} Unit Blok •
                    {{ number_format($infoKebun['luas'] ?? 0, 2, ',', '.') }} Ha Luas Areal
                </p>
            </div>

            <!-- Pemilih Periode -->
            <div
                class="flex items-center gap-4 p-2 pl-5 bg-white dark:bg-[#0e1726] rounded-2xl border border-slate-100 dark:border-white-dark/10 shadow-sm transition-all hover:shadow-md">
                <div class="hidden lg:block text-right border-r border-slate-100 dark:border-slate-800 pr-4">
                    <p class="text-[9px] font-black text-slate-400 tracking-widest leading-none mb-1">Dimensi
                        Waktu</p>
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
            <!-- SIDEBAR KONTROL -->
            <div class="space-y-6" x-show="!isMapExpanded">
                <div class="panel p-6 border-none rounded-[2rem] bg-white dark:bg-[#0e1726] shadow-xl">
                    <h5 class="font-black text-[10px] tracking-[0.2em] mb-6 text-slate-400 italic">Kesehatan
                        Tanaman</h5>
                    <div class="space-y-5 text-[11px] font-black">
                        <div class="flex justify-between text-success"><span>Normal</span><span
                                x-text="statusCounts['healthy'] || 0"></span></div>
                        <div class="flex justify-between text-warning"><span>Waspada</span><span
                                x-text="statusCounts['moderate'] || 0"></span></div>
                        <div class="flex justify-between text-danger"><span>Kritis</span><span
                                x-text="statusCounts['critical'] || 0"></span></div>
                    </div>
                </div>

                <div class="panel p-6 border-none rounded-[2rem] bg-white dark:bg-[#0e1726] shadow-xl">
                    <h5 class="font-black text-[10px] mb-6 text-slate-400 italic tracking-widest border-b pb-2">
                        Visualization Layers</h5>
                    <div class="space-y-4">
                        <template x-for="layer in layers" :key="layer.id">
                            <div class="flex justify-between items-center group cursor-pointer"
                                @click="toggleLayer(layer.id)">
                                <div class="flex flex-col text-left">
                                    <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400"
                                        x-text="layer.label"></span>
                                    <span x-show="!layer.exists"
                                        class="text-[8px] text-danger font-black italic tracking-tighter leading-none mt-1">Data
                                        Not Integrated</span>
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
                        <div class="flex items-center gap-2 text-primary font-black italic tracking-widest text-[10px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m6-3l5.447 2.724a1 1 0 010.553 0.894v10.764a1 1 0 01-1.447 0.894L15 17m-6 3l6-3m-6 0V7m6 10V4" />
                            </svg>
                            Interactive Spatial Intelligence
                        </div>
                        <div class="flex gap-2">
                            <button @click="toggleMapHeight()"
                                class="p-2 px-4 rounded-xl transition-all shadow-sm flex items-center gap-2"
                                :class="isMapExpanded ? 'bg-danger text-white shadow-danger/20' :
                                    'bg-gray-100 dark:bg-black/20 text-primary'">
                                <span class="text-[10px] font-black"
                                    x-text="isMapExpanded ? 'Exit Theater' : 'Theater Mode'"></span>
                            </button>
                            <button @click="map.zoomIn()"
                                class="p-2 bg-primary text-white rounded-xl w-10 transition-all shadow-lg active:scale-90">+</button>
                            <button @click="map.zoomOut()"
                                class="p-2 bg-primary text-white rounded-xl w-10 transition-all shadow-lg active:scale-90">-</button>
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
                        <div class="flex items-center justify-between mb-10 text-left">
                            <h5
                                class="text-[11px] font-black text-slate-400 tracking-widest italic border-l-4 border-primary pl-4">
                                Condition Proportions</h5>
                            <span class="text-[9px] font-bold text-slate-300">Live Sync</span>
                        </div>
                        <div x-ref="pieChart" class="min-h-[320px]"></div>
                    </div>
                    <div
                        class="panel p-8 rounded-[2.5rem] bg-white dark:bg-[#0e1726] border-none shadow-xl text-center">
                        <div class="flex items-center justify-between mb-10 text-left">
                            <h5
                                class="text-[11px] font-black text-slate-400 tracking-widest italic border-l-4 border-success pl-4">
                                Cakupan Parameter</h5>
                            <span class="text-[9px] font-bold text-slate-300">Metric View</span>
                        </div>
                        <div x-ref="barChart" class="min-h-[320px]"></div>
                    </div>
                </div>

                <!-- AI PRESCRIPTIVE ENGINE (INTEGRATED) -->
                <div
                    class="panel p-0 rounded-[3rem] border-none shadow-2xl overflow-hidden bg-gradient-to-br from-slate-900 to-slate-800 dark:from-black dark:to-slate-900 relative">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 blur-[100px] -mr-32 -mt-32"></div>
                    <div class="p-10 relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center gap-8 mb-12 text-left">
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
                                    Node: DSS-XAI-STANDAR-SCOPUS</p>
                            </div>
                        </div>
                        <div
                            class="min-h-[160px] rounded-[2.2rem] bg-white/5 dark:bg-black/40 border border-white/10 p-10 flex flex-col justify-center relative overflow-hidden transition-all duration-700">
                            <!-- INTEGRASI LOGIKA THINKING -->
                            <template x-if="isThinkingBlok">
                                <div class="flex flex-col items-center gap-4 animate-pulse">
                                    <div
                                        class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <p class="text-[10px] font-black text-slate-400 tracking-[0.4em] italic">
                                        Neural Reasoning in Progress...</p>
                                </div>
                            </template>

                            <template x-if="inferenceResult && !isThinkingBlok">
                                <div class="animate__animated animate__fadeInUp space-y-4 text-left">
                                    <div
                                        class="inline-block px-4 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-black tracking-widest">
                                        High Confidence Recommendation</div>
                                    <p class="text-slate-100 text-xl font-medium leading-relaxed italic tracking-tight"
                                        x-text="inferenceResult.recommendation"></p>
                                </div>
                            </template>

                            <div x-show="!inferenceResult && !isThinkingBlok"
                                class="text-center space-y-4 py-6 opacity-40">
                                <div class="text-[11px] font-black text-slate-300 tracking-[0.6em] italic">
                                    System Standby</div>
                                <p class="text-slate-400 text-[10px] font-black tracking-widest">Pilih unit
                                    blok pada peta untuk analisis preskriptif</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BIOMETRIK VEGETATIF -->
                <template x-if="vegetatifData && vegetatifData.labels.length > 0">
                    <div class="panel p-10 rounded-[3.5rem] bg-white dark:bg-[#0e1726] border-none shadow-2xl space-y-8"
                        x-transition:enter="transition-premium" x-transition:enter-start="opacity-0 translate-y-10">
                        <div
                            class="flex flex-col md:flex-row md:items-center justify-between gap-6 text-left border-b border-slate-100 dark:border-white/5 pb-6">
                            <div class="space-y-1">
                                <h4
                                    class="text-2xl font-black italic text-slate-800 dark:text-white tracking-tighter leading-none">
                                    Analisis Biometrik Vegetatif</h4>
                                <div
                                    class="flex items-center gap-3 text-[10px] text-primary font-bold tracking-[0.3em] opacity-70">
                                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                                    Parameter: Lingkar Batang, Jumlah & Panjang Pelepah
                                </div>
                            </div>
                            <div class="flex gap-2 font-black italic">
                                <span
                                    class="px-5 py-2 rounded-2xl bg-slate-100 dark:bg-white/5 text-[10px] text-slate-500 tracking-widest">Multivariate
                                    Analysis</span>
                            </div>
                        </div>

                        <div class="relative w-full overflow-x-auto">
                            <div x-ref="vegetatifChart" class="min-h-[480px]"></div>
                        </div>

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
                                    <p class="text-[9px] text-slate-400 tracking-widest leading-none mb-1">Avg. Girth
                                    </p>
                                    <p class="text-lg dark:text-white tracking-tighter" x-text="avgGirth + ' m'"></p>
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
                                    <p class="text-[9px] text-slate-400 tracking-widest leading-none mb-1">Frond Prod.
                                    </p>
                                    <p class="text-lg dark:text-white tracking-tighter" x-text="avgFrondCount"></p>
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
                                    <p class="text-[9px] text-slate-400 tracking-widest leading-none mb-1">Avg. Length
                                    </p>
                                    <p class="text-lg dark:text-white tracking-tighter" x-text="avgFrondLen + ' m'">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- LOGIKA SCRIPT -->
    <script>
        document.addEventListener("alpine:init", () => {
            // LAYER KHUSUS: Mengolah Pixel secara Real-time agar putih hilang
            L.TileLayer.TransparentWhite = L.TileLayer.extend({
                createTile: function(coords, done) {
                    const tile = document.createElement('canvas');
                    const ctx = tile.getContext('2d');
                    tile.width = tile.height = 256;

                    const img = new Image();
                    img.crossOrigin = "Anonymous";
                    img.onload = function() {
                        ctx.drawImage(img, 0, 0);
                        const imageData = ctx.getImageData(0, 0, 256, 256);
                        const data = imageData.data;
                        for (let i = 0; i < data.length; i += 4) {
                            if (data[i] > 250 && data[i + 1] > 250 && data[i + 2] > 250) {
                                data[i + 3] = 0;
                            }
                        }
                        ctx.putImageData(imageData, 0, 0);
                        done(null, tile);
                    };
                    // FIX: Jika tile 404 dari GitHub, biarkan transparan agar peta dasar terlihat
                    img.onerror = () => {
                        ctx.clearRect(0, 0, 256, 256);
                        done(null, tile);
                    };
                    img.src = this.getTileUrl(coords);
                    return tile;
                }
            });

            Alpine.data("detailKebun", () => ({
                map: null,
                isUavMode: false,
                isMapExpanded: false,
                isThinkingBlok: false,
                geoLayers: {},
                masterBounds: null,
                inferenceResult: null,

                center: [{{ $kebun->latitude ?? 2.03394 }}, {{ $kebun->longitude ?? 99.9952 }}],
                tileUrl: '{{ $kebun->tile_url ?? '' }}',
                selectedPeriode: '{{ $activeSlug }}',
                lokasiPoints: @json($lokasiPoints ?? []),
                statusCounts: @json($statusCounts ?? []),
                kondisiPohon: @json($kondisiPohon ?? []),
                arealTanaman: @json($arealTanaman ?? []),

                vegetatifData: {
                    labels: @json($vegetatif['vegLabels'] ?? []),
                    lingkar: @json($vegetatif['vegLingkar'] ?? []),
                    jumlah: @json($vegetatif['vegJumlah'] ?? []),
                    panjang: @json($vegetatif['vegPanjang'] ?? []),
                },
                avgGirth: 0,
                avgFrondCount: 0,
                avgFrondLen: 0,

                layers: [{
                        id: 'basemap',
                        label: 'Citra Satelit/UAV',
                        active: true,
                        exists: true
                    },
                    {
                        id: 'batas',
                        label: 'Batas Blok',
                        active: false,
                        exists: false
                    },
                    {
                        id: 'pemel',
                        label: 'Anomaly Layer',
                        active: false,
                        exists: false
                    },
                    {
                        id: 'lcc',
                        label: 'Kacangan Layer',
                        active: false,
                        exists: false
                    }
                ],

                initComponent() {
                    this.isUavMode = this.tileUrl && this.tileUrl.includes('{z}');
                    this.masterBounds = L.latLngBounds();
                    this.masterBounds.extend(this.center);
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
                        this.avgGirth = (d.lingkar.reduce((a, b) => a + b, 0) / d.lingkar.length)
                            .toFixed(3);
                        this.avgFrondCount = (d.jumlah.reduce((a, b) => a + b, 0) / d.jumlah.length)
                            .toFixed(1);
                        this.avgFrondLen = (d.panjang.reduce((a, b) => a + b, 0) / d.panjang.length)
                            .toFixed(3);
                    }
                },

                async initMap() {
                    if (this.map) this.map.remove();
                    this.map = L.map('leafletMap', {
                        zoomControl: false,
                        attributionControl: false
                    }).setView(this.center, 15);

                    L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                        maxZoom: 22,
                        className: 'satellite-base'
                    }).addTo(this.map);

                    if (this.isUavMode) {
                        new L.TileLayer.TransparentWhite(this.tileUrl, {
                            maxZoom: 22,
                            maxNativeZoom: 18,
                            zIndex: 10
                        }).addTo(this.map);
                    }

                    this.map.createPane('topLayer').style.zIndex = 500;

                    if (this.lokasiPoints.length > 0) {
                        this.lokasiPoints.forEach(pt => {
                            if (pt.latitude && pt.latitude != 0) {
                                const pos = [parseFloat(pt.latitude), parseFloat(pt
                                    .longitude)];
                                this.masterBounds.extend(pos);
                                L.circleMarker(pos, {
                                    radius: 3.5,
                                    color: '#000',
                                    fillColor: '#fb923c',
                                    fillOpacity: 1,
                                    weight: 1.5,
                                    pane: 'topLayer'
                                }).addTo(this.map).bindTooltip(pt.nama_lokasi);
                            }
                        });
                    }

                    const kode = '{{ $kebun->kebun }}';
                    const fetchLayer = async (type, options) => {
                        try {
                            const response = await fetch(`/spatial-data/${kode}/${type}`);
                            const data = await response.json();
                            if (data.features && data.features.length > 0) {
                                options.pane = 'topLayer';
                                const layerInstance = L.geoJSON(data, options);
                                const idMap = {
                                    'batas': 'batas',
                                    'pemeliharaan': 'pemel',
                                    'kacangan': 'lcc'
                                };
                                this.geoLayers[idMap[type]] = layerInstance;
                                this.layers.find(l => l.id === idMap[type]).exists = true;
                                if (layerInstance.getBounds().isValid()) this.masterBounds
                                    .extend(layerInstance.getBounds());
                                if (type === 'batas') {
                                    this.layers.find(l => l.id === 'batas').active = true;
                                    layerInstance.addTo(this.map);
                                }
                            }
                        } catch (e) {}
                    };

                    await Promise.all([
                        fetchLayer('batas', {
                            style: {
                                color: 'white',
                                weight: 2,
                                fillOpacity: 0.1,
                                fillColor: 'transparent'
                            },
                            onEachFeature: (f, l) => {
                                // Tooltip bawaan Anda
                                l.bindTooltip(`Blok: ${f.properties.AFDELING}`);

                                // LOGIKA INTEGRASI AI SAAT KLIK BLOK
                                l.on('click', async (e) => {
                                    // 1. Reset hasil sebelumnya dan tampilkan loading
                                    this.inferenceResult = null;
                                    this.isThinkingBlok = true;

                                    // Scroll halus ke panel AI agar user tahu ada proses
                                    document.querySelector(
                                            '.animate-pulse-soft')
                                        .scrollIntoView({
                                            behavior: 'smooth'
                                        });

                                    try {
                                        // 2. Ambil data dari API AI Controller
                                        // f.properties.AFDELING mengambil "AFD03" dari GeoJSON Anda
                                        const res = await fetch(
                                            `/api/ai/block-insight?kebun=${kode}&blok_id=${f.properties.AFDELING}&periode=${this.selectedPeriode}`
                                        );
                                        const result = await res.json();

                                        if (result.status ===
                                            'success') {
                                            this.inferenceResult = {
                                                recommendation: result
                                                    .data
                                                    .rekomendasi_ai
                                            };
                                        }
                                    } catch (err) {
                                        this.inferenceResult = {
                                            recommendation: "Maaf, gagal menghubungi Neural Engine. Periksa API Key Anda."
                                        };
                                    } finally {
                                        this.isThinkingBlok = false;
                                    }
                                });
                            }
                        }),
                        fetchLayer('pemeliharaan', {
                            style: {
                                color: '#ef4444',
                                weight: 2,
                                fillOpacity: 0.2,
                                dashArray: '4,4'
                            },
                            onEachFeature: (f, l) => {
                                l.bindPopup(
                                    `<b class="text-danger text-xs">Anomaly: ${f.properties.KETERANGAN}</b>`
                                );
                            }
                        }),
                        fetchLayer('kacangan', {
                            style: {
                                color: '#84cc16',
                                weight: 1.5,
                                fillOpacity: 0.4,
                                fillColor: '#a3e635'
                            },
                            onEachFeature: (f, l) => {
                                l.bindPopup(
                                    `<div class="overflow-hidden vristo-popup"><div class="bg-primary px-4 py-2"><h5 class="text-white font-black text-[10px] m-0 italic">Kacangan Analysis</h5></div><div class="p-4 space-y-2 text-xs text-left"><div class="flex justify-between"><span>Unit ID</span><span class="font-black text-primary">${f.properties.afdeling_id || 'N/A'}</span></div><div class="flex justify-between"><span>Luas</span><span class="font-black">${f.properties.LUAS} Ha</span></div></div></div>`
                                );
                            }
                        })
                    ]);

                    if (this.masterBounds.isValid()) this.map.fitBounds(this.masterBounds, {
                        padding: [50, 50],
                        maxZoom: 16
                    });
                    setTimeout(() => this.map.invalidateSize(), 600);
                },

                renderCharts() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const textCol = isDark ? '#94a3b8' : '#64748b';

                    if (this.$refs.pieChart) {
                        new ApexCharts(this.$refs.pieChart, {
                            series: Object.values(this.kondisiPohon || {}),
                            labels: Object.keys(this.kondisiPohon || {}).map(l => l
                                .toUpperCase()),
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
                                                formatter: (w) => Math.round(w.globals
                                                    .seriesTotals.reduce((a, b) => a + b, 0)
                                                ) + "%"
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
                                name: 'Lingkar Batang (m)',
                                data: this.vegetatifData.lingkar
                            },
                            {
                                name: 'Jumlah Pelepah',
                                data: this.vegetatifData.jumlah
                            },
                            {
                                name: 'Panjang Pelepah (m)',
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
                    document.body.style.overflow = this.isMapExpanded ? 'hidden' : 'auto';
                    setTimeout(() => {
                        this.map.invalidateSize();
                        if (this.masterBounds.isValid()) this.map.fitBounds(this.masterBounds, {
                            padding: [40, 40],
                            maxZoom: 16
                        });
                    }, 400);
                },

                toggleLayer(id) {
                    const lyr = this.geoLayers[id === 'pemel' ? 'pemel' : id];
                    const layerDef = this.layers.find(l => l.id === id);
                    if (lyr && layerDef.exists) {
                        layerDef.active = !layerDef.active;
                        layerDef.active ? this.map.addLayer(lyr) : this.map.removeLayer(lyr);
                    }
                },

                changePeriode() {
                    window.location.href =
                        `/monitoring/detail-areal/{{ $kebun->id ?? $kebun->kebun }}?periode=${this.selectedPeriode}`;
                }
            }));
        });
    </script>
</x-layout.default>
