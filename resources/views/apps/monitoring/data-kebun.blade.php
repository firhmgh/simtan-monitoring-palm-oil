<x-layout.default>
    <!-- Optimasi Tipografi & Font Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <div x-data="monitoringData()"
        class="space-y-6 pb-10 font-['Plus_Jakarta_Sans'] antialiased text-slate-800 dark:text-white-light">

        <!-- 1. BAGIAN HEADER (Dukungan Tema Dinamis) -->
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center justify-between">
            <div class="space-y-1">
                <nav
                    class="flex items-center gap-2 text-[10px] font-black shadow-sm tracking-[0.2em] text-primary/60 dark:text-primary/80">
                    <a href="/" class="hover:text-primary transition-colors">Analisis</a>
                    <span class="text-slate-300 dark:text-slate-600">/</span>
                    <span class="text-slate-400">Mesin Inventarisasi</span>
                </nav>
                <h1 class="text-3xl font-black tracking-tighter leading-none italic text-slate-900 dark:text-white">
                    Data Kebun <span class="text-primary underline decoration-primary/20">Regional I</span>
                </h1>
                <p
                    class="text-slate-500 dark:text-slate-400 text-[10px] font-bold tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Pemantauan Areal Terintegrasi TBM III
                </p>
            </div>

            <!-- 2. FILTER TEMPORAL (Sinkronisasi Vristo) -->
            <div
                class="flex items-center gap-4 p-2 pl-5 bg-white dark:bg-[#0e1726] rounded-2xl border border-slate-100 dark:border-white-dark/10 shadow-sm transition-all hover:shadow-md">
                <div class="hidden lg:block text-right border-r border-slate-100 dark:border-slate-800 pr-4">
                    <p class="text-[9px] font-black text-slate-400 tracking-widest leading-none mb-1">Dimensi
                        Waktu</p>
                    <p class="text-[10px] font-black text-primary italic">Tersinkronisasi</p>
                </div>
                <select x-model="selectedPeriode" @change="changePeriode"
                    class="form-select py-2.5 text-xs font-black rounded-xl border-none bg-slate-50 dark:bg-black/20 focus:ring-2 focus:ring-primary/20 cursor-pointer w-[240px] text-slate-700 dark:text-white">
                    @foreach ($listPeriode as $slug => $info)
                        <option value="{{ $slug }}" {{ $activeSlug == $slug ? 'selected' : '' }}>
                            {{ strtoupper($info['label']) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- 3. KARTU RINGKASAN INDIKATOR KINERJA UTAMA (IKU) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <template x-for="kpi in kpiCards" :key="kpi.label">
                <div class="panel bg-white dark:bg-[#0e1726] border-none shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-all border-b-4"
                    :class="kpi.border">
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div :class="`p-2 rounded-xl ${kpi.bg} ${kpi.color}`" x-html="kpi.icon"></div>
                                <span class="text-[10px] font-black text-gray-400 tracking-widest"
                                    x-text="kpi.label"></span>
                            </div>
                            <div class="flex items-baseline gap-1.5">
                                <h2 class="text-3xl font-black tracking-tighter leading-none text-slate-800 dark:text-white"
                                    x-text="kpi.value"></h2>
                                <span class="text-[10px] font-bold text-gray-400" x-text="kpi.unit"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mt-5 pt-4 border-t border-gray-100 dark:border-white-dark/5">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 leading-none mb-1">Tingkat
                                    Kepatuhan</p>
                                <p class="text-xs font-black italic"
                                    :class="parseFloat(kpi.compliance) >= 100 ? 'text-emerald-500' : 'text-rose-500'"
                                    x-text="kpi.compliance + '%'"></p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 leading-none mb-1">Status
                                    Evaluasi</p>
                                <p class="text-[10px] font-black italic"
                                    :class="parseFloat(kpi.compliance) >= 100 && parseFloat(kpi.value.replace(/[^0-9.-]+/g,
                                        '')) > 0 ? 'text-emerald-500' : 'text-amber-500'"
                                    x-text="parseFloat(kpi.compliance) >= 100 && parseFloat(kpi.value.replace(/[^0-9.-]+/g,'')) > 0 ? 'Optimal' : 'Deviasi'">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- 4. PANEL EKSPLORASI DATA -->
        <div class="panel border-none shadow-xl dark:shadow-none bg-white dark:bg-[#0e1726] rounded-[2.5rem] overflow-hidden transition-all duration-500"
            x-show="!isLoading">
            <!-- Header Filter & Pencarian -->
            <div
                class="p-8 border-b border-slate-50 dark:border-white-dark/5 flex flex-col md:flex-row items-center justify-between gap-6">

                <!-- Pencarian Unit -->
                <div class="relative w-full md:w-[450px] group">
                    <form action="{{ route('monitoring.data-kebun') }}" method="GET">
                        <input type="hidden" name="periode" value="{{ $activeSlug }}">
                        <div
                            class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari unit kebun atau distrik operasional..."
                            class="form-input pl-14 py-4 w-full bg-slate-50 dark:bg-black/20 border-none rounded-2xl font-bold text-xs tracking-tight text-slate-600 dark:text-white-dark focus:ring-4 focus:ring-primary/5 transition-all" />
                    </form>
                </div>

                <!-- Dropdown Distrik (Filter Spasial) -->
                <div class="relative w-full md:w-auto" x-data="{ openDropdown: false, selectedDistrik: '{{ request('distrik', 'Semua Distrik') }}' }">
                    <button @click="openDropdown = !openDropdown" @click.away="openDropdown = false"
                        class="flex items-center justify-between gap-6 w-full md:min-w-[280px] py-4 px-6 rounded-2xl bg-slate-50 dark:bg-black/20 text-slate-700 dark:text-white-dark hover:bg-slate-100 dark:hover:bg-black/40 transition-all border-none shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                            </div>
                            <span x-text="selectedDistrik" class="text-[10px] font-black tracking-widest"></span>
                        </div>
                        <svg class="w-4 h-4 opacity-40 transition-transform" :class="openDropdown ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openDropdown" x-transition x-cloak
                        class="absolute right-0 z-[100] w-full mt-3 bg-white dark:bg-[#191e3a] border border-slate-100 dark:border-white-dark/10 rounded-2xl shadow-2xl overflow-hidden py-2">
                        <button @click="window.location.href='?periode={{ $activeSlug }}'"
                            class="w-full text-left px-6 py-4 hover:bg-primary/5 dark:hover:bg-primary/10 flex items-center justify-between transition-colors group">
                            <span
                                class="text-[10px] font-black tracking-widest text-slate-600 dark:text-slate-300">SEMUA
                                DISTRIK</span>
                        </button>
                        @foreach ($distrikList as $distrik)
                            @php $infoDistrik = App\Helpers\ExcelDataHelper::getInfoKebun('', $distrik, 0); @endphp
                            <button
                                @click="window.location.href='?periode={{ $activeSlug }}&distrik={{ $distrik }}'"
                                class="w-full text-left px-6 py-4 hover:bg-primary/5 dark:hover:bg-primary/10 flex items-center justify-between transition-colors group">
                                <span
                                    class="text-[10px] font-black tracking-widest text-slate-600 dark:text-slate-300 group-hover:text-primary">{{ $infoDistrik['distrik'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tabel Data Pintar -->
            <div class="table-responsive">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="bg-slate-50/50 dark:bg-black/10 text-slate-400 dark:text-slate-500 text-[10px] font-black tracking-[0.2em] border-b border-slate-50 dark:border-white-dark/5">
                            <th class="py-6 px-8 text-center">No</th>
                            <th class="py-6 px-6">Identitas Unit</th>
                            <th class="py-6 px-6">Wilayah Distrik</th>
                            <th class="py-6 px-6 text-center">Total Blok</th>
                            <th class="py-6 px-6 text-center">Luas Areal (Ha)</th>
                            <th class="py-6 px-6 text-center">Status Kesehatan</th>
                            <th class="py-6 px-8 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-white-dark/5 text-xs font-bold">
                        @forelse($kebun as $index => $item)
                            <tr class="group hover:bg-slate-50/80 dark:hover:bg-primary/5 transition-all duration-300">
                                <td
                                    class="py-6 px-8 text-center font-black text-slate-300 group-hover:text-primary transition-colors">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-6 px-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-slate-800 dark:text-white tracking-tighter italic group-hover:translate-x-1 transition-transform inline-block">{{ $item->nama_kebun }}</span>
                                        <span
                                            class="text-[9px] font-bold text-slate-400 mt-0.5 tracking-widest">{{ $item->kebun }}</span>
                                    </div>
                                </td>
                                <td class="py-6 px-6 text-[10px] font-black text-slate-500 tracking-widest">
                                    {{ $item->distrik }}</td>
                                <td class="py-6 px-6 text-center">
                                    <span
                                        class="inline-block px-3 py-1 bg-slate-100 dark:bg-gray-800 rounded-lg font-black text-slate-600 dark:text-slate-300">{{ $item->total_blok }}</span>
                                </td>
                                <td class="py-6 px-6 text-center font-black text-slate-800 dark:text-white">
                                    {{ number_format($item->total_luas, 1, ',', '.') }}</td>
                                <td class="py-6 px-6 text-center">
                                    @php
                                        $softColor = match ($item->status_color) {
                                            'bg-success'
                                                => 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400',
                                            'bg-warning'
                                                => 'bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400',
                                            'bg-danger'
                                                => 'bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400',
                                            default
                                                => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                        };
                                    @endphp
                                    <span
                                        class="inline-block px-4 py-1.5 rounded-full text-[9px] font-black tracking-[0.2em] {{ $softColor }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="py-6 px-8 text-center">
                                    {{-- Jika item->id kosong, kirimkan item->kebun (Kode) --}}
                                    <a href="{{ route('monitoring.detail', ['id' => $item->id ?? $item->kebun, 'periode' => $activeSlug]) }}"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white text-[9px] font-black uppercase tracking-widest rounded-xl shadow-lg hover:-translate-y-0.5 transition-all">
                                        Detail Areal
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-32 text-center">
                                    <div class="flex flex-col items-center gap-6 opacity-30">
                                        <div class="p-6 bg-slate-100 dark:bg-slate-800 rounded-[2.5rem]">
                                            <svg class="w-16 h-16 text-slate-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="1"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <p class="text-[10px] font-black tracking-[0.3em]">Data Kosong:
                                            Silakan pilih periode yang valid</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Analisis Cerdas -->
            <div
                class="p-8 bg-slate-50/50 dark:bg-black/10 border-t border-slate-50 dark:border-white-dark/5 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-[9px] font-black text-slate-400 tracking-widest italic">Hasil analisis untuk
                    {{ strtoupper($activeSlug) }} | Total {{ $kebun->count() }} titik data teridentifikasi</p>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-black text-slate-500 dark:text-slate-400 tracking-widest">Sinkronisasi
                        Gudang Data Pintar PTPN IV</span>
                </div>
            </div>
        </div>

        <!-- LOADING SKELETON (Visual UX Profesional) -->
        <div x-show="isLoading" x-transition class="space-y-6">
            <div
                class="panel h-[600px] rounded-[2.5rem] bg-white dark:bg-[#0e1726] p-8 space-y-8 animate-pulse border-none">
                <div class="flex justify-between items-center">
                    <div class="h-10 bg-slate-100 dark:bg-slate-800 rounded-2xl w-1/3"></div>
                    <div class="h-10 bg-slate-100 dark:bg-slate-800 rounded-2xl w-1/4"></div>
                </div>
                <div class="space-y-4">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl w-full"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Script Alpine.js & Logika IKU -->
    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("monitoringData", () => ({
                selectedPeriode: '{{ $activeSlug }}',
                isLoading: false,

                // Definisi Data IKU (Indikator Kinerja Utama)
                kpiCards: [{
                        label: 'Cakupan Areal Total',
                        value: '{{ number_format($kpi['total_luas'], 0, ',', '.') }}',
                        unit: 'Ha',
                        bg: 'bg-emerald-500/10',
                        color: 'text-emerald-600 dark:text-emerald-400',
                        border: 'border-emerald-500/30',
                        compliance: '100.0',
                        icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>'
                    },
                    {
                        label: 'Total Populasi Aktif',
                        value: '{{ number_format($kpi['total_pokok'], 0, ',', '.') }}',
                        unit: 'Pkk',
                        bg: 'bg-blue-500/10',
                        color: 'text-blue-600 dark:text-blue-400',
                        border: 'border-blue-500/30',
                        compliance: '{{ $kpi['populasi_compliance'] }}',
                        icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>'
                    },
                    {
                        label: 'Tingkat Kelangsungan Hidup',
                        value: '{{ $kpi['avg_health'] }}',
                        unit: '%',
                        bg: 'bg-purple-500/10',
                        color: 'text-purple-600 dark:text-purple-400',
                        border: 'border-purple-500/30',
                        compliance: '{{ $kpi['health_compliance'] }}',
                        icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>'
                    },
                    {
                        label: 'Kepatuhan Agronomi',
                        value: '{{ $kpi['agronomy_compliance'] }}',
                        unit: '%',
                        bg: 'bg-amber-500/10',
                        color: 'text-amber-600 dark:text-amber-400',
                        border: 'border-amber-500/30',
                        compliance: '{{ $kpi['agronomy_compliance'] }}',
                        icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    }
                ],

                changePeriode() {
                    this.isLoading = true;
                    setTimeout(() => {
                        window.location.href = window.location.pathname + "?periode=" + this
                            .selectedPeriode;
                    }, 400);
                }
            }));
        });
    </script>

    <style>
        /* Tipografi & Kerapian */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        /* Navigasi Scroll Tabel Halus */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            @apply bg-slate-200 dark:bg-slate-800 rounded-full hover:bg-primary/50 transition-colors;
        }

        /* Penanganan x-cloak */
        [x-cloak] {
            display: none !important;
        }

        /* Gaya Input & Form */
        .form-input,
        .form-select {
            @apply transition-all duration-300;
        }

        /* Ketinggian Panel Tabel */
        .panel {
            @apply shadow-none dark:bg-[#0e1726];
        }
    </style>
</x-layout.default>
