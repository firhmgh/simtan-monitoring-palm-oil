<x-layout.default>
    <div x-data="riwayatData({{ json_encode($logsJson) }})" class="relative text-gray-900 dark:text-white-light" x-init="initRiwayat()" x-cloak>

        <!-- 1. HEADER SECTION -->
        <div
            class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="text-left">
                <ul class="flex space-x-2 text-xs mb-2 text-white-dark tracking-widest font-black">
                    <li><a href="javascript:;" class="text-primary hover:underline">Monitoring</a></li>
                    <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 font-black">Riwayat Data</li>
                </ul>
                <h1
                    class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white tracking-tighter leading-none italic">
                    Log Konsolidasi Data</h1>
                <p
                    class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-2 border-l-2 border-primary pl-2 tracking-widest">
                    Audit Trail & Transparansi Aktivitas Ingesti Multimoda
                </p>
            </div>
        </div>

        <!-- NOTIFIKASI KONDISI AWAL (DATA KOSONG) -->
        @if (count($logsJson) === 0)
            <div
                class="flex items-center p-5 mb-8 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-500 shadow-sm animate__animated animate__fadeIn">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="font-black italic tracking-widest text-xs">Informasi Log:</span>
                    <p class="text-sm font-bold">
                        Belum ada aktivitas ingesti data yang tercatat. Silakan menuju halaman <a
                            href="{{ route('monitoring.import') }}"
                            class="underline decoration-2 font-black text-primary">Upload Data</a> untuk memulai
                        sinkronisasi.
                    </p>
                </div>
            </div>
        @endif

        <!-- 2. ANALYTICAL STATS CARDS (Dynamic Counters) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 font-black">
            <!-- Total -->
            <div
                class="panel bg-white dark:bg-[#0e1726] border-0 shadow-sm p-6 flex items-center justify-between group hover:shadow-md transition-all rounded-xl">
                <div class="text-left">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 tracking-[0.2em] mb-1 font-black">
                        Total Ingesti</p>
                    <p class="text-4xl font-black text-gray-900 dark:text-white leading-none tracking-tighter"
                        x-text="statusCounts.total"></p>
                </div>
                <div
                    class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>

            <!-- Success -->
            <div
                class="panel bg-white dark:bg-[#0e1726] border-0 shadow-sm p-6 flex items-center justify-between group hover:shadow-md transition-all rounded-xl">
                <div class="text-left">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 tracking-[0.2em] mb-1 font-black">
                        Data Sinkron</p>
                    <p class="text-4xl font-black text-emerald-600 leading-none tracking-tighter"
                        x-text="statusCounts.success"></p>
                </div>
                <div
                    class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <!-- Failed -->
            <div
                class="panel bg-white dark:bg-[#0e1726] border-0 shadow-sm p-6 flex items-center justify-between group hover:shadow-md transition-all rounded-xl">
                <div class="text-left">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 tracking-[0.2em] mb-1 font-black">
                        Anomali/Gagal</p>
                    <p class="text-4xl font-black text-rose-600 leading-none tracking-tighter"
                        x-text="statusCounts.error"></p>
                </div>
                <div
                    class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>

            <!-- Processing -->
            <div
                class="panel bg-white dark:bg-[#0e1726] border-0 shadow-sm p-6 flex items-center justify-between group hover:shadow-md transition-all rounded-xl">
                <div class="text-left">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 tracking-[0.2em] mb-1 font-black">
                        Queue / Antrean</p>
                    <p class="text-4xl font-black text-blue-600 leading-none tracking-tighter"
                        x-text="statusCounts.processing"></p>
                </div>
                <div
                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm">
                    <svg class="w-7 h-7 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2.5">
                        <path
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- 3. SMART FILTER PANEL (Vristo Standard) -->
        <div
            class="panel bg-white dark:bg-[#0e1726] border border-gray-200 dark:border-[#1b2e4b] shadow-sm p-5 mb-8 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end font-black">
                <div class="text-left">
                    <label
                        class="text-[10px] text-gray-400 dark:text-gray-500 mb-1.5 block tracking-[0.2em] italic">Search
                        Files</label>
                    <input type="text" x-model="searchQuery" placeholder="INPUT FILENAME..."
                        class="form-input py-2.5 text-xs">
                </div>

                <div class="text-left">
                    <label
                        class="text-[10px] text-gray-400 dark:text-gray-500 mb-1.5 block tracking-[0.2em] italic">Authority</label>
                    <select x-model="filterPengunggah" class="form-select py-2.5 text-xs">
                        <option value="all">SEMUA PENGUNGGAH</option>
                        @foreach ($listPengunggah as $name)
                            <option value="{{ $name }}">{{ strtoupper($name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-left">
                    <label
                        class="text-[10px] text-gray-400 dark:text-gray-500 mb-1.5 block tracking-[0.2em] italic">Dataset
                        Type</label>
                    <select x-model="filterJenis" class="form-select py-2.5 text-xs">
                        <option value="all">SEMUA JENIS</option>
                        @foreach ($listJenis as $jenis)
                            <option value="{{ $jenis }}">{{ strtoupper($jenis) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button @click="resetFilters()"
                        class="btn btn-primary w-full py-2.5 text-xs font-black tracking-widest italic shadow-lg shadow-primary/20 transition-all hover:scale-95">
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- 4. DATA LOG TABLE (Adaptive Mode) -->
        <div class="panel bg-white dark:bg-[#0e1726] border-0 shadow-sm p-0 overflow-hidden rounded-xl">
            <div class="table-responsive">
                <table class="w-full text-left border-collapse table-hover">
                    <thead>
                        <tr
                            class="bg-gray-50 dark:bg-black/20 text-gray-700 dark:text-gray-300 border-b dark:border-[#1b2e4b]">
                            <th class="px-6 py-4 text-[11px] font-black tracking-widest italic">Timestamp</th>
                            <th class="px-6 py-4 text-[11px] font-black tracking-widest italic">Laporan /
                                Berkas</th>
                            <th class="px-6 py-4 text-[11px] font-black tracking-widest italic text-center">
                                Otoritas</th>
                            <th class="px-6 py-4 text-[11px] font-black tracking-widest italic text-center">
                                Dataset</th>
                            <th class="px-6 py-4 text-[11px] font-black tracking-widest italic text-center">
                                Volume</th>
                            <th class="px-6 py-4 text-[11px] font-black tracking-widest italic text-center">
                                Integrasi</th>
                            <th class="px-6 py-4 text-[11px] font-black tracking-widest italic text-center">
                                Navigasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-[13px] font-bold">
                        <template x-for="item in filteredData" :key="item.id">
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-black/10 transition-all">
                                <td class="px-6 py-5 text-gray-500 dark:text-gray-400 font-black italic tracking-tighter"
                                    x-text="item.tglUpload"></td>
                                <td class="px-6 py-5">
                                    <div class="text-gray-900 dark:text-white font-black truncate max-w-[250px] leading-none mb-1.5"
                                        x-text="item.namaFile"></div>
                                    <div class="text-[9px] text-gray-400 font-bold tracking-widest"
                                        x-text="'UID: #' + item.id"></div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="text-primary dark:text-primary-light font-black tracking-tighter leading-none"
                                        x-text="item.pengunggah"></span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-black rounded border border-primary/20"
                                        x-text="item.jenisDataset"></span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="text-gray-900 dark:text-white font-black italic shadow-inner px-2 py-0.5 bg-gray-50 dark:bg-gray-800 rounded"
                                        x-text="numberFormat(item.baris) + ' Baris'"></span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-[9px] font-black tracking-widest border"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800': item
                                                .status === 'Sukses',
                                            'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800': item
                                                .status === 'Gagal',
                                            'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800': item
                                                .status === 'Diproses'
                                        }">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                            :class="{
                                                'bg-emerald-500': item.status === 'Sukses',
                                                'bg-rose-500': item
                                                    .status === 'Gagal',
                                                'bg-blue-500 animate-ping': item
                                                    .status === 'Diproses'
                                            }"></span>
                                        <span x-text="item.status"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <template x-if="item.formId">
                                        <a :href="'/monitoring/detail-areal/' + item.formId"
                                            class="btn btn-sm btn-outline-primary font-black text-[9px] tracking-[0.1em] py-1.5 px-4 rounded-md transition-all hover:scale-95 shadow-sm">
                                            Analisa
                                        </a>
                                    </template>
                                    <template x-if="!item.formId">
                                        <span class="text-[9px] text-gray-400 italic">No Reference</span>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <template x-if="filteredData.length === 0">
                            <tr>
                                <td colspan="7" class="py-24 text-center">
                                    <div class="flex flex-col items-center opacity-30">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" stroke-width="1.5">
                                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <p class="text-xl font-black tracking-[0.4em] italic">Data Tidak
                                            Ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. BOTTOM ACTIONS -->
        <div class="flex flex-wrap gap-4 mt-8 justify-start font-black">
            <button
                class="btn btn-dark text-[10px] tracking-widest italic py-3 px-8 rounded-lg shadow-xl transition-all active:scale-95">
                Download Master CSV
            </button>
            <button
                class="btn btn-outline-dark text-[10px] tracking-widest italic py-3 px-8 rounded-lg shadow-sm transition-all active:scale-95">
                Print Official PDF
            </button>
        </div>
    </div>

    <!-- REFACTOR SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('riwayatData', (initialData) => ({
                searchQuery: '',
                filterPengunggah: 'all',
                filterJenis: 'all',
                historyData: Array.isArray(initialData) ? initialData : [],

                initRiwayat() {
                    // Notifikasi Toast Jika Data Kosong total di Database
                    if (this.historyData.length === 0) {
                        const toast = window.Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                        });
                        toast.fire({
                            icon: 'info',
                            title: 'Database Kosong',
                            text: 'Sistem belum mendeteksi adanya data riwayat.',
                            padding: '10px 20px'
                        });
                    }
                },

                get filteredData() {
                    return this.historyData.filter((item) => {
                        const namaFile = item.namaFile ? item.namaFile.toLowerCase() : '';
                        const search = this.searchQuery.toLowerCase();
                        const matchesSearch = namaFile.includes(search);
                        const matchesPengunggah = this.filterPengunggah === 'all' ||
                            item.pengunggah === this.filterPengunggah;
                        const matchesJenis = this.filterJenis === 'all' ||
                            item.jenisDataset === this.filterJenis;
                        return matchesSearch && matchesPengunggah && matchesJenis;
                    });
                },

                get statusCounts() {
                    return {
                        total: this.historyData.length,
                        success: this.historyData.filter(d => d.status === 'Sukses').length,
                        error: this.historyData.filter(d => d.status === 'Gagal').length,
                        processing: this.historyData.filter(d => d.status === 'Diproses').length,
                    };
                },

                numberFormat(x) {
                    if (!x || x === 0) return '0';
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.filterPengunggah = 'all';
                    this.filterJenis = 'all';
                }
            }));
        });
    </script>

    <style>
        .table-responsive::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            @apply bg-slate-200 dark:bg-slate-800 rounded-full transition-all;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            @apply bg-primary/30;
        }

        .panel {
            @apply transition-all duration-300;
        }

        .form-input,
        .form-select {
            @apply dark:bg-[#1b2e4b] dark:border-[#253b5c] dark:text-white-light rounded-lg transition-all duration-300 !important;
            @apply focus:border-primary focus:ring-0 !important;
        }

        .table-hover tr:hover {
            @apply cursor-pointer !important;
        }
    </style>
</x-layout.default>
