<?php if (isset($component)) { $__componentOriginal9d5893b966d42bc9a39e2bb81c9df0c6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d5893b966d42bc9a39e2bb81c9df0c6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.default','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('layout.default'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    

    <div x-data="detailKebun()">
        <!-- 1. Breadcrumbs & Header -->
        <div class="mb-6">
            <ul class="flex space-x-2 rtl:space-x-reverse text-xs mb-2 tracking-widest font-bold text-gray-400">
                <li><a href="javascript:;" class="text-primary hover:underline">Monitoring</a></li>
                <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"><span>Detail Kebun Spasial</span></li>
            </ul>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white-light">
                            
                            Kebun Sei Dadap (1KDP)
                        </h1>
                        <span
                            class="badge bg-emerald-500 !text-white text-[10px] px-3 py-1 font-black rounded-full tracking-widest shadow-sm">CONNECTED</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium italic">
                        
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
                                        x-model="layer.active" />
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
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-[#10b981] shadow-[0_0_8px_rgba(16,185,129,0.4)]">
                                </div>
                                <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">Healthy</span>
                            </div>
                            <span class="text-[11px] font-black italic text-gray-800 dark:text-gray-200">
                                
                                18 Blok
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-[#f59e0b] shadow-[0_0_8px_rgba(245,158,11,0.4)]">
                                </div>
                                <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">Moderate</span>
                            </div>
                            <span class="text-[11px] font-black italic text-gray-800 dark:text-gray-200">
                                
                                5 Blok
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-[#ef4444] shadow-[0_0_8px_rgba(239,68,68,0.4)]">
                                </div>
                                <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">Critical</span>
                            </div>
                            <span class="text-[11px] font-black italic text-gray-800 dark:text-gray-200">
                                
                                1 Blok
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Interactive Map & Charts -->
            <div class="xl:col-span-3 space-y-5">
                <!-- Interactive Map Panel -->
                <div
                    class="panel p-0 border-0 shadow-sm h-[500px] flex flex-col overflow-hidden rounded-3xl dark:bg-[#1b2e4b]">
                    <!-- Toolbar Map -->
                    <div
                        class="p-4 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-white dark:bg-[#1b2e4b] z-10">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-primary/10 text-primary rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m6-3l5.447 2.724a1 1.01.553.894v10.764a1 1 0 01-1.447.894L15 17m-6 3l6-3m-6 0V7m6 10V4" />
                                </svg>
                            </div>
                            <h5 class="font-black text-[10px] tracking-widest text-gray-500 dark:text-gray-400">
                                Visualisasi Spasial Blok</h5>
                        </div>
                        <div class="flex gap-2">
                            <button @click="zoomOut"
                                class="p-2 bg-gray-50 dark:bg-black/20 hover:bg-primary/10 rounded-xl transition-all border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2.5" d="M20 12H4" />
                                </svg>
                            </button>
                            <button @click="zoomIn"
                                class="p-2 bg-gray-50 dark:bg-black/20 hover:bg-primary/10 rounded-xl transition-all border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- SVG Workspace -->
                    <div class="flex-1 relative bg-gray-50 dark:bg-black/20 overflow-hidden cursor-move"
                        @mousedown="startPan" @mousemove="doPan" @mouseup="endPan" @mouseleave="endPan">
                        <div class="w-full h-full transition-transform duration-200 ease-out flex items-center justify-center"
                            :style="`transform: scale(${scale}) translate(${panX}px, ${panY}px)`">
                            <svg class="w-full h-full max-w-[800px]" viewBox="0 0 400 300"
                                preserveAspectRatio="xMidYMid meet">
                                <template x-for="(row, rIdx) in ['A','B','C']" :key="rIdx">
                                    <template x-for="(col, cIdx) in ['01','02','03','04']" :key="cIdx">
                                        <g :transform="`translate(${100 + (cIdx * 60)}, ${80 + (rIdx * 70)})`"
                                            class="group cursor-pointer" @click="selectedBlock = row + '-' + col">
                                            <rect x="-25" y="-30" width="50" height="60" rx="6"
                                                :fill="getBlockColor(row + '-' + col)"
                                                :stroke="selectedBlock === row + '-' + col ? (document.documentElement.classList
                                                    .contains('dark') ? '#fff' : '#0e1726') : (document
                                                    .documentElement.classList.contains('dark') ? '#3b3f5c' : '#fff'
                                                )"
                                                :stroke-width="selectedBlock === row + '-' + col ? 3 : 1"
                                                class="transition-all duration-300 group-hover:brightness-90 shadow-sm" />
                                            <text x="0" y="5" text-anchor="middle" fill="white" font-size="9"
                                                font-weight="900" class="select-none pointer-events-none"
                                                x-text="row + '-' + col"></text>
                                        </g>
                                    </template>
                                </template>
                            </svg>
                        </div>
                        <div
                            class="absolute top-6 right-6 w-12 h-12 bg-white/90 dark:bg-black/60 backdrop-blur rounded-full flex items-center justify-center border border-white/20 shadow-xl">
                            <span class="text-xs font-black text-primary">N</span>
                        </div>
                    </div>
                </div>

                <!-- CHARTS ROW -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="panel border-0 shadow-sm rounded-3xl p-8 dark:bg-[#1b2e4b]">
                        <h5
                            class="font-black text-[10px] tracking-[0.2em] mb-8 text-gray-400 dark:text-gray-500 text-center italic tracking-tighter">
                            Proporsi Kondisi Pohon (%)</h5>
                        <div x-ref="pieChartDummy" class="min-h-[300px]"></div>
                    </div>
                    <div class="panel border-0 shadow-sm rounded-3xl p-8 dark:bg-[#1b2e4b]">
                        <h5
                            class="font-black text-[10px] tracking-[0.2em] mb-8 text-gray-400 dark:text-gray-500 text-center italic tracking-tighter">
                            Analisis Parameter Areal (%)</h5>
                        <div x-ref="barChartDummy" class="min-h-[300px]"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. AI ANALYSIS SECTION -->
        <div
            class="panel border-0 shadow-2xl p-0 overflow-hidden mt-8 bg-white dark:bg-gradient-to-br dark:from-[#1b2e4b] dark:to-[#060818] rounded-[2rem] border border-gray-100 dark:border-transparent transition-all duration-300">
            <div class="p-8 md:p-10">
                <!-- Header Engine -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-8 mb-12">
                    <div class="flex items-center gap-5">
                        <div
                            class="w-16 h-16 bg-primary text-white rounded-3xl flex items-center justify-center shadow-xl shadow-primary/20 dark:shadow-none transition-transform hover:scale-105">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-black text-xl tracking-tight text-gray-900 dark:text-white">
                                Autonomous Prescriptive Engine</h4>
                            <p
                                class="text-[10px] text-primary dark:text-indigo-400 font-black tracking-[0.4em] mt-1 opacity-80">
                                Diagnostic Node: DSS-SAWIT-V2.4</p>
                        </div>
                    </div>

                    <!-- Input Controls -->
                    <div
                        class="flex items-center gap-3 bg-gray-50 dark:bg-black/40 p-2 rounded-2xl border border-gray-200 dark:border-white/10 w-full md:w-auto shadow-inner">
                        <select
                            class="form-select border-none bg-transparent font-black text-[11px] tracking-widest min-w-[200px] cursor-pointer text-gray-700 dark:text-white focus:ring-0"
                            x-model="selectedBlock">
                            <option value="">Pilih Unit Blok</option>
                            <template x-for="row in ['A','B','C']" :key="row">
                                <template x-for="col in ['01','02','03','04']" :key="col">
                                    <option :value="row + '-' + col" x-text="'ANALISIS UNIT ' + row + '-' + col">
                                    </option>
                                </template>
                            </template>
                        </select>
                        <button
                            class="btn btn-primary px-8 py-3 rounded-xl font-black text-[10px] tracking-[0.2em] shadow-lg shadow-primary/30 transition-all active:scale-95 hover:brightness-110"
                            @click="runInference" :disabled="isAnalyzing">
                            <span x-show="!isAnalyzing">EXECUTE AI</span>
                            <span x-show="isAnalyzing" class="flex items-center gap-2">
                                <span
                                    class="animate-spin h-3 w-3 border-2 border-white border-t-transparent rounded-full"></span>
                                NEURAL...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Result Container -->
                <div class="min-h-[250px] relative transition-all duration-500">

                    <!-- IDLE STATE -->
                    <template x-if="!inferenceResult && !isAnalyzing">
                        <div class="h-full flex flex-col items-center justify-center py-10 opacity-40">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-width="1"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-400 text-[10px] font-black tracking-[0.6em]">System Standby -
                                Select Spatial Node</p>
                        </div>
                    </template>

                    <!-- LOADING STATE -->
                    <template x-if="isAnalyzing">
                        <div
                            class="flex flex-col items-center justify-center py-10 space-y-6 animate__animated animate__fadeIn">
                            <div class="relative w-20 h-20">
                                <div class="absolute inset-0 border-4 border-primary/20 rounded-full"></div>
                                <div
                                    class="absolute inset-0 border-4 border-primary border-t-transparent rounded-full animate-spin">
                                </div>
                            </div>
                            <div class="text-center">
                                <p
                                    class="text-xs font-black tracking-[0.3em] text-primary dark:text-indigo-400 animate-pulse">
                                    Running Monte Carlo Inference...</p>
                                <p class="text-[9px] text-gray-400 mt-2 font-mono tracking-tighter">
                                    Memory-Alloc: Spatial Vigor Index Calculation</p>
                            </div>
                        </div>
                    </template>

                    <!-- RESULT STATE -->
                    <template x-if="inferenceResult && !isAnalyzing">
                        <div class="animate__animated animate__fadeInUp">
                            <!-- Label Metadata -->
                            <div class="flex items-center gap-4 mb-8">
                                <span
                                    class="bg-emerald-500 !text-white text-[9px] font-black px-4 py-1.5 rounded-lg tracking-[0.2em] shadow-lg shadow-emerald-500/20">INFERENCE
                                    SUCCESS</span>
                                <div class="h-[1px] flex-1 bg-gray-100 dark:bg-white/10"></div>
                                <span
                                    class="text-[10px] font-bold text-gray-400 dark:text-gray-500 tracking-widest">Report
                                    ID: <span x-text="'SP-ID-'+Math.floor(Math.random()*1000)"></span></span>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                                <!-- Box Rekomendasi (Diberi Jarak & Contrast) -->
                                <div class="lg:col-span-8 group">
                                    <div
                                        class="h-full p-8 bg-gray-50 dark:bg-white/[0.03] rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm transition-all hover:border-emerald-500/50">
                                        <div class="flex items-center gap-2 mb-6">
                                            <div class="w-1.5 h-6 bg-emerald-500 rounded-full"></div>
                                            <h6
                                                class="text-[11px] font-black text-emerald-600 dark:text-emerald-400 tracking-widest italic">
                                                Rekomendasi Preskriptif:</h6>
                                        </div>
                                        <p class="text-gray-800 dark:text-gray-100 text-lg md:text-xl font-bold leading-relaxed italic tracking-tight px-2"
                                            x-text="inferenceResult.recommendation"></p>
                                    </div>
                                </div>

                                <!-- Box Metrics (Scopus Style) -->
                                <div class="lg:col-span-4 grid grid-cols-1 gap-4">
                                    <div
                                        class="p-6 bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-white/5 shadow-sm flex flex-col justify-center">
                                        <p
                                            class="text-[9px] text-gray-400 dark:text-gray-500 font-black mb-3 tracking-widest">
                                            Confidence Score</p>
                                        <div class="flex items-end gap-2">
                                            <span class="text-3xl font-black text-gray-900 dark:text-white"
                                                x-text="inferenceResult.confidence + '%'"></span>
                                            <svg class="w-5 h-5 text-emerald-500 mb-1" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div
                                        class="p-6 bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-white/5 shadow-sm flex flex-col justify-center">
                                        <p
                                            class="text-[9px] text-gray-400 dark:text-gray-500 font-black mb-3 tracking-widest">
                                            Vigor Index (NDVI)</p>
                                        <div class="flex items-end gap-2 text-indigo-500 dark:text-indigo-400">
                                            <span class="text-3xl font-black" x-text="inferenceResult.vigor"></span>
                                            <span class="text-[10px] font-black mb-2 opacity-60">Value</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("detailKebun", () => ({
                scale: 1,
                panX: 0,
                panY: 0,
                isPanning: false,
                startX: 0,
                startY: 0,
                selectedBlock: '',
                isAnalyzing: false,
                inferenceResult: null,

                // DATA DUMMY (Fallbacks if Backend is Sleep)
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
                blockStatuses: {
                    'A-03': '#f59e0b',
                    'B-02': '#ef4444'
                },

                

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

                init() {
                    setTimeout(() => {
                        this.renderCharts();
                    }, 300);
                },

                getBlockColor(id) {
                    return this.blockStatuses[id] || '#10b981';
                },

                zoomIn() {
                    this.scale = Math.min(this.scale + 0.3, 3);
                },
                zoomOut() {
                    this.scale = Math.max(this.scale - 0.3, 0.5);
                },
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

                renderCharts() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const themeText = isDark ? '#cbd5e1' : '#334155';

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
                                colors: themeText
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
                                    colors: themeText
                                },
                                formatter: v => v + "%"
                            }
                        },
                        legend: {
                            position: 'top',
                            labels: {
                                colors: themeText
                            }
                        },
                        grid: {
                            borderColor: isDark ? '#334155' : '#e2e8f0'
                        }
                    }).render();
                },

                runInference() {
                    if (!this.selectedBlock) {
                        alert('SILA PILIH BLOK TERLEBIH DAHULU');
                        return;
                    }
                    this.isAnalyzing = true;
                    this.inferenceResult = null;

                    

                    // SIMULASI DUMMY (Hapus jika real AI aktif)
                    setTimeout(() => {
                        this.isAnalyzing = false;
                        this.inferenceResult = {
                            recommendation: "Berdasarkan analisis citra spasial & sensor ground-truth pada Blok " +
                                this.selectedBlock +
                                ", terdeteksi defisiensi hara Nitrogen yang signifikan. Rekomendasi: Aplikasi segera Urea 1.5kg/pokok. Segera lakukan normalisasi parit di sisi timur blok untuk mencegah akumulasi genangan.",
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
    </style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d5893b966d42bc9a39e2bb81c9df0c6)): ?>
<?php $attributes = $__attributesOriginal9d5893b966d42bc9a39e2bb81c9df0c6; ?>
<?php unset($__attributesOriginal9d5893b966d42bc9a39e2bb81c9df0c6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d5893b966d42bc9a39e2bb81c9df0c6)): ?>
<?php $component = $__componentOriginal9d5893b966d42bc9a39e2bb81c9df0c6; ?>
<?php unset($__componentOriginal9d5893b966d42bc9a39e2bb81c9df0c6); ?>
<?php endif; ?>
<?php /**PATH C:\simtan-monitoring-palm-oil\resources\views/apps/monitoring/detail-kebun.blade.php ENDPATH**/ ?>