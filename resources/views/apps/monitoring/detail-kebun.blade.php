<x-layout.default>
    <div x-data="detailKebun">
        <!-- Breadcrumbs & Header -->
        <div class="mb-6">
            <ul class="flex space-x-2 rtl:space-x-reverse text-xs mb-2">
                <li><a href="javascript:;" class="text-primary hover:underline">Monitoring</a></li>
                <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"><span>Detail Kebun</span></li>
            </ul>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-[#0e1726] dark:text-white-light">Kebun Sei Dadap</h1>
                <span
                    class="badge bg-emerald-100 text-emerald-700 text-[10px] px-2 py-0.5 font-bold rounded">Active</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Distrik Labuhan Batu Utara II • 24 Blok • 1450 Ha</p>
        </div>
        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-5">

            <!-- LEFT COLUMN: Stats & Controls -->
            <div class="space-y-5">
                <!-- Kebun Information -->
                <div class="panel p-5 border-0 shadow-sm">
                    <h5 class="font-bold text-sm mb-5">Kebun Information</h5>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[11px] text-gray-400 mb-1">Total Blok</p>
                            <p class="text-l font-bold">24</p>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 mb-1">Total Area</p>
                            <p class="text-l font-bold">1450 Ha</p>
                        </div>
                    </div>
                </div>

                <!-- Map Layers -->
                <div class="panel p-5 border-0 shadow-sm">
                    <h5 class="font-bold text-sm">Visualisasi Spasial</h5>
                    <div class="space-y-5">
                        <template x-for="layer in layers">
                            <div class="flex justify-between items-center group">
                                <span
                                    class="text-[12px] font-medium text-[#3b3f5c] dark:text-white-dark group-hover:text-primary transition-all"
                                    x-text="layer.label"></span>
                                <label class="w-9 h-5 relative mb-0">
                                    <input type="checkbox"
                                        class="custom_switch absolute w-full h-full opacity-0 z-10 cursor-pointer peer"
                                        x-model="layer.active" />
                                    <span
                                        class="bg-[#ebedf2] dark:bg-dark block h-full rounded-full before:absolute before:left-1 before:bg-white before:bottom-0.5 before:w-4 before:h-4 before:rounded-full peer-checked:before:left-4 peer-checked:bg-primary transition-all duration-300"></span>
                                </label>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Vegetation Status Panel -->
                <div class="panel border-[#ebedf2] dark:border-[#1b2e4b] shadow-sm">
                    <div class="mb-6">
                        <h5 class="font-bold text-sm">Vegetation Status</h5>
                    </div>

                    <div class="space-y-5">
                        <!-- Healthy -->
                        <div class="flex items-center justify-between group cursor-default">
                            <div class="flex items-center">
                                <!-- Dot Indicator -->
                                <div
                                    class="w-3.5 h-3.5 rounded-full bg-success shrink-0 shadow-[0_0_8px_rgba(16,185,129,0.4)]">
                                </div>
                                <!-- Teks digeser ke kanan dengan ml-3 -->
                                <span
                                    class="ml-3 text-[12px] font-medium text-[#3b3f5c] dark:text-white-dark group-hover:text-primary transition-all">Healthy</span>
                            </div>
                            <span class="text-[12px] font-bold text-[#0e1726] dark:text-white-light font-mono">18
                                blocks</span>
                        </div>

                        <!-- Moderate -->
                        <div class="flex items-center justify-between group cursor-default">
                            <div class="flex items-center">
                                <div
                                    class="w-3.5 h-3.5 rounded-full bg-warning shrink-0 shadow-[0_0_8px_rgba(226,160,63,0.4)]">
                                </div>
                                <!-- Teks digeser ke kanan dengan ml-3 -->
                                <span
                                    class="ml-3 text-[12px] font-medium text-[#3b3f5c] dark:text-white-dark group-hover:text-primary transition-all">Moderate</span>
                            </div>
                            <span class="text-[12px] font-bold text-[#0e1726] dark:text-white-light font-mono">5
                                blocks</span>
                        </div>

                        <!-- Attention -->
                        <div class="flex items-center justify-between group cursor-default">
                            <div class="flex items-center">
                                <div
                                    class="w-3.5 h-3.5 rounded-full bg-danger shrink-0 shadow-[0_0_8px_rgba(231,81,90,0.4)]">
                                </div>
                                <!-- Teks digeser ke kanan dengan ml-3 -->
                                <span
                                    class="ml-3 text-[12px] font-medium text-[#3b3f5c] dark:text-white-dark group-hover:text-primary transition-all">Attention</span>
                            </div>
                            <span class="text-[12px] font-bold text-[#0e1726] dark:text-white-light font-mono">1
                                block</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Interactive Map -->
            <div class="xl:col-span-3">
                <div class="panel p-0 border-0 shadow-sm h-full flex flex-col overflow-hidden min-h-[600px]">
                    <!-- Map Toolbar -->
                    <div class="p-4 flex justify-between items-center">
                        <h5 class="font-bold text-sm">Interactive Map Visualization</h5>
                        <div class="flex items-center gap-2">
                            <button @click="zoomOut"
                                class="p-1.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8" />
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                    <line x1="8" y1="11" x2="14" y2="11" />
                                </svg>
                            </button>
                            <button @click="zoomIn"
                                class="p-1.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8" />
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                    <line x1="11" y1="8" x2="11" y2="14" />
                                    <line x1="8" y1="11" x2="14" y2="11" />
                                </svg>
                            </button>
                            <button class="p-1.5 bg-indigo-50 text-indigo-600 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- SVG Container -->
                    <div class="flex-1 relative bg-white overflow-hidden cursor-move" @mousedown="startPan"
                        @mousemove="doPan" @mouseup="endPan" @mouseleave="endPan">

                        <div class="w-full h-full transition-transform duration-200 ease-out flex items-center justify-center"
                            :style="`transform: scale(${scale}) translate(${panX}px, ${panY}px)`">

                            <!-- Map SVG -->
                            <svg class="w-full h-full max-w-[800px] max-h-[500px]" viewBox="0 0 400 300"
                                preserveAspectRatio="xMidYMid meet">
                                <template x-for="(row, rIdx) in ['A','B','C']" :key="rIdx">
                                    <template x-for="(col, cIdx) in ['01','02','03','04']" :key="cIdx">
                                        <g :transform="`translate(${100 + (cIdx * 50)}, ${80 + (rIdx * 60)})`"
                                            class="group pointer-events-auto">
                                            <!-- Block Shape -->
                                            <rect x="-18" y="-22" width="36" height="44" rx="2"
                                                :fill="showNDVI ? getBlockColor(row + '-' + col) : '#cbd5e1'"
                                                :stroke="showBoundaries ? 'white' : 'none'" stroke-width="0.5"
                                                class="transition-all duration-200 group-hover:brightness-95" />
                                            <!-- Dot -->
                                            <circle cx="0" cy="-12" r="2.5" fill="white"
                                                opacity="0.9" />
                                            <!-- Label -->
                                            <text x="0" y="5" text-anchor="middle" fill="white" font-size="7"
                                                font-weight="bold" class="select-none">
                                                <tspan x="0" dy="0" x-text="row + '-' + col"></tspan>
                                            </text>
                                        </g>
                                    </template>
                                </template>
                            </svg>
                        </div>

                        <!-- Map Overlays -->
                        <!-- Compass -->
                        <div
                            class="absolute top-8 right-8 w-10 h-10 bg-white shadow-md rounded-full flex items-center justify-center border border-gray-100">
                            <span class="text-[10px] font-bold">N</span>
                        </div>

                        <!-- Scale Bar -->
                        <div
                            class="absolute bottom-10 left-10 bg-white/90 p-3 rounded shadow-sm border border-gray-50">
                            <p class="text-[9px] text-gray-400 mb-1">Scale</p>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-0.5 bg-gray-800 relative">
                                    <div class="absolute -top-1 left-0 w-[1px] h-2.5 bg-gray-800"></div>
                                    <div class="absolute -top-1 right-0 w-[1px] h-2.5 bg-gray-800"></div>
                                </div>
                                <span class="text-[10px] font-bold">1 km</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMART AI ANALYSIS SECTION (Scopus-Grade UI) -->
        <div
            class="panel border-0 shadow-lg p-0 overflow-hidden mt-5 bg-white dark:bg-gradient-to-r dark:from-indigo-900 dark:to-violet-950 border border-gray-200 dark:border-transparent">
            <div class="p-6">
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <!-- Icon with adaptive colors -->
                        <div
                            class="p-2.5 bg-indigo-100 dark:bg-white/20 rounded-xl shadow-sm border border-indigo-200 dark:border-white/30">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">
                                Analisis Cerdas per Blok</h4>
                            <p class="font text-sm">
                                Autonomous Prescriptive Engine Node</p>
                        </div>
                    </div>

                    <!-- Selection & Button Area -->
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative min-w-[105px]"> <!-- Container terkontrol -->
                            <select
                                class="form-select text-[11px] font-black py-2 pl-3 pr-8 bg-gray-50 dark:bg-white/10 border-gray-200 dark:border-white/20 text-gray-800 dark:text-white rounded-lg focus:ring-primary focus:border-primary w-full cursor-pointer appearance-none"
                                x-model="selectedBlock">
                                <option value="" class="text-gray-800 bg-white">Pilih Blok</option>
                                <template x-for="row in ['A','B','C']" :key="row">
                                    <template x-for="col in ['01','02','03','04']" :key="col">
                                        <option :value="row + '-' + col" class="text-gray-800 bg-white"
                                            x-text="'Blok ' + row + '-' + col"></option>
                                    </template>
                                </template>
                            </select>
                        </div>

                        <button
                            class="btn btn-primary shadow-indigo-200 dark:shadow-indigo-900/20 font-black text-[12px] py-2 px-6 tracking-widest whitespace-nowrap active:scale-95 transition-transform"
                            @click="runInference">
                            Jalankan Analisis AI
                        </button>
                    </div>
                </div>

                <!-- Inference Result Box (Adaptive Design) -->
                <div class="relative min-h-[140px] bg-gray-50 dark:bg-black/30 rounded-2xl border border-gray-100 dark:border-white/10 p-6 flex flex-col justify-center overflow-hidden transition-all duration-500"
                    :class="isAnalyzing ? 'animate-pulse' : ''">

                    <!-- State: Empty -->
                    <template x-if="!inferenceResult && !isAnalyzing">
                        <div class="text-center">
                            <div
                                class="inline-flex p-3 bg-white dark:bg-white/5 rounded-full mb-2 shadow-sm border border-gray-100 dark:border-white/5">
                                <svg class="w-5 h-5 text-gray-400 dark:text-white/40" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.989-2.386l-.548-.547z"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <p class="text-gray-400 dark:text-white/40 text-xs font-bold tracking-widest">
                                Pilih blok di peta untuk memulai inferensi AI</p>
                        </div>
                    </template>

                    <!-- State: Analyzing -->
                    <template x-if="isAnalyzing">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <span
                                class="animate-spin h-8 w-8 border-4 border-indigo-500 border-t-transparent rounded-full"></span>
                            <p class="text-indigo-600 dark:text-white text-[10px] font-black tracking-widest">
                                Neural node processing spatial parameters...</p>
                        </div>
                    </template>

                    <!-- State: Result (Professional Scopus Detail) -->
                    <template x-if="inferenceResult && !isAnalyzing">
                        <div class="animate__animated animate__fadeIn">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                <h6
                                    class="text-emerald-600 dark:text-emerald-400 text-[10px] font-black tracking-[0.3em]">
                                    Inference Hasil: <span x-text="selectedBlock"></span>
                                </h6>
                            </div>

                            <div
                                class="p-5 bg-white dark:bg-black/40 rounded-xl border border-gray-100 dark:border-transparent border-l-4 border-l-emerald-500 shadow-sm">
                                <p class="text-gray-700 dark:text-white text-sm leading-relaxed font-bold tracking-tight italic"
                                    x-text="inferenceResult.recommendation"></p>
                            </div>

                            <!-- Explainable AI Parameters -->
                            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div
                                    class="bg-white dark:bg-white/5 p-3 rounded-xl border border-gray-100 dark:border-white/5 shadow-sm">
                                    <p
                                        class="text-[9px] text-gray-400 dark:text-indigo-200 font-bold tracking-widest mb-1">
                                        Confidence Score</p>
                                    <p class="text-gray-900 dark:text-white text-xl font-black"
                                        x-text="inferenceResult.confidence + '%'"></p>
                                </div>
                                <div
                                    class="bg-white dark:bg-white/5 p-3 rounded-xl border border-gray-100 dark:border-white/5 shadow-sm">
                                    <p
                                        class="text-[9px] text-gray-400 dark:text-indigo-200 font-bold tracking-widest mb-1">
                                        Vigor Index</p>
                                    <p class="text-gray-900 dark:text-white text-xl font-black"
                                        x-text="inferenceResult.vigor"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Model Reference Tag -->
                <div class="mt-4 flex justify-center">
                    <span
                        class="text-[9px] font-bold text-gray-400 tracking-tighter bg-gray-100 dark:bg-black/20 px-3 py-1 rounded-full border border-gray-200 dark:border-white/5">
                        Model: DSS-SAWIT-XAI-2024 Node-01
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("detailKebun", () => ({
                scale: 1,
                panX: 0,
                panY: 0,
                isPanning: false,
                startX: 0,
                startY: 0,
                showNDVI: true,
                showBoundaries: true,

                // Block status dummy mapping
                statuses: {
                    'A-03': 'moderate',
                    'C-02': 'moderate',
                    'B-02': 'attention'
                },

                getBlockColor(id) {
                    const status = this.statuses[id] || 'healthy';
                    if (status === 'healthy') return '#10b981';
                    if (status === 'moderate') return '#f59e0b';
                    if (status === 'attention') return '#ef4444';
                },

                // Zoom Logic
                zoomIn() {
                    this.scale = Math.min(this.scale + 0.2, 3);
                },
                zoomOut() {
                    this.scale = Math.max(this.scale - 0.2, 0.5);
                },

                // Pan Logic (For moving map with mouse)
                startPan(e) {
                    this.isPanning = true;
                    this.startX = e.clientX - this.panX;
                    this.startY = e.clientY - this.panY;
                },
                doPan(e) {
                    if (!this.isPanning) return;
                    this.panX = e.clientX - this.startX;
                    this.panY = e.clientY - this.startY;
                },
                endPan() {
                    this.isPanning = false;
                },

                selectedBlock: '',
                isAnalyzing: false,
                inferenceResult: null,

                layers: [{
                        id: 'ndvi',
                        label: 'Layer Batas Blok',
                        active: true
                    },
                    {
                        id: 'point',
                        label: 'Layer Point (Pohon)',
                        active: true
                    },
                    {
                        id: 'maint',
                        label: 'Layer Pemeliharaan',
                        active: false
                    },
                    {
                        id: 'lcc',
                        label: 'Layer Kacangan',
                        active: false
                    },
                ],

                runInference() {
                    if (!this.selectedBlock) {
                        alert('Silakan pilih blok terlebih dahulu');
                        return;
                    }
                    this.isAnalyzing = true;
                    this.inferenceResult = null;

                    // Simulasi Neural Network Inference
                    setTimeout(() => {
                        this.isAnalyzing = false;
                        this.inferenceResult = {
                            recommendation: "Berdasarkan parameter vegetatif, Blok " + this
                                .selectedBlock +
                                " terdeteksi mengalami defisiensi hara. Rekomendasi Scopus: Aplikasi MOP 2.0kg/pokok dan evaluasi sistem drainase parit sekunder untuk mencegah leaching.",
                            confidence: (Math.random() * (99.8 - 94.5) + 94.5).toFixed(1),
                            vigor: (Math.random() * (0.88 - 0.72) + 0.72).toFixed(3)
                        };
                    }, 2000);
                },
            }));
        });
    </script>

    <style>
        /* Vristo Switch Styling */
        .custom_switch:checked~span:before {
            background-color: #fff !important;
        }

        .panel {
            background: #fff;
            border-radius: 8px;
        }
    </style>
</x-layout.default>
