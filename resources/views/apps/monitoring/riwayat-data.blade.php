<x-layout.default>
    <!-- Alpine.js Component Wrapper -->
    <div x-data="riwayatData()" class="space-y-6">

        <!-- Page Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Data Masuk</h1>
            <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">Lacak dan kelola semua aktivitas log konsolidasi
                data</p>
        </div>

        <!-- Stats Cards (Sesuai Desain Gambar Pertama) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <!-- Total -->
            <div
                class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Import</p>
                    <p class="mt-2 text-4xl font-extrabold text-gray-900 dark:text-white" x-text="statusCounts.total"></p>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <path d="M14 2v6h6" />
                        <path d="M16 13H8" />
                        <path d="M16 17H8" />
                        <path d="M10 9H8" />
                    </svg>
                </div>
            </div>

            <!-- Successful -->
            <div
                class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sukses</p>
                    <p class="mt-2 text-4xl font-extrabold text-emerald-600" x-text="statusCounts.success"></p>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100/60 text-emerald-500 dark:bg-emerald-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
            </div>

            <!-- Failed -->
            <div
                class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Gagal</p>
                    <p class="mt-2 text-4xl font-extrabold text-red-600" x-text="statusCounts.error"></p>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100/60 text-red-500 dark:bg-red-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </div>
            </div>

            <!-- Processing -->
            <div
                class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Diproses</p>
                    <p class="mt-2 text-4xl font-extrabold text-blue-600" x-text="statusCounts.processing"></p>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100/60 text-blue-500 dark:bg-blue-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round" class="animate-spin">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div
            class="rounded-2xl border border-gray-100 bg-white p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Cari File -->
                <div class="relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center justify-center w-10">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" x2="16.65" y1="21" y2="16.65" />
                        </svg>
                    </div>
                    <!-- pl-10 (padding-left 40px) memastikan text tidak menyentuh ikon yang ada di w-10 (lebar 40px) -->
                    <input type="text" x-model="searchQuery" placeholder="Cari File..."
                        class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" />
                </div>

                <!-- Filter Pengunggah -->
                <div class="relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center justify-center w-10">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                    <select x-model="filterPengunggah"
                        class="w-full appearance-none rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-10 text-sm font-medium text-gray-700 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer">
                        <option value="all">Semua Pengunggah</option>
                        <option value="Admin">Admin</option>
                        <option value="SuperAdmin">SuperAdmin</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-10">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Periode -->
                <div class="relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center justify-center w-10">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                            <line x1="16" x2="16" y1="2" y2="6" />
                            <line x1="8" x2="8" y1="2" y2="6" />
                            <line x1="3" x2="21" y1="10" y2="10" />
                        </svg>
                    </div>
                    <select x-model="filterPeriode"
                        class="w-full appearance-none rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-10 text-sm font-medium text-gray-700 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer">
                        <option value="all">Semua Periode</option>
                        <option value="01/26">Jan-2026</option>
                        <option value="02/26">Feb-2026</option>
                        <option value="03/26">Mar-2026</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-10">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Jenis Dataset -->
                <div class="relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center justify-center w-10">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2" />
                            <polyline points="2 17 12 22 22 17" />
                            <polyline points="2 12 12 17 22 12" />
                        </svg>
                    </div>
                    <select x-model="filterJenis"
                        class="w-full appearance-none rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-10 text-sm font-medium text-gray-700 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 cursor-pointer">
                        <option value="all">Semua Jenis</option>
                        <option value="Vegetatif">Vegetatif</option>
                        <option value="Rekap TBM III">Rekap TBM III</option>
                        <option value="Map Tiles">Map Tiles</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-10">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Action Buttons for Filter -->
            <div class="mt-5 flex justify-end border-t border-gray-100 dark:border-gray-800 pt-5">
                <button @click="resetFilters()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                        <path d="M3 3v5h5" />
                    </svg>
                    Segarkan Log
                </button>
            </div>
        </div>

        <!-- History Table Card -->
        <div
            class="rounded-2xl border border-gray-100 bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Log Konsolidasi Data</h3>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-800/50">
                            <th class="whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">Tgl
                                Upload</th>
                            <th class="whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                Nama File</th>
                            <th class="whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                Pengunggah</th>
                            <th class="whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                Jenis Dataset</th>
                            <th class="whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                Baris</th>
                            <th class="whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                Status</th>
                            <th
                                class="whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900 dark:text-white text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in filteredData" :key="item.id">
                            <tr
                                class="border-b border-gray-50 hover:bg-gray-50/80 dark:border-gray-800 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"
                                    x-text="item.tglUpload"></td>
                                <td class="px-6 py-4">
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white max-w-[200px] truncate block"
                                        x-text="item.namaFile"></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"
                                    x-text="item.pengunggah"></td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"
                                    x-text="item.jenisDataset"></td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-gray-900 dark:text-white" x-text="item.baris"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold w-fit whitespace-nowrap"
                                        :class="{
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': item
                                                .status === 'Sukses',
                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': item
                                                .status === 'Gagal',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': item
                                                .status === 'Diproses'
                                        }">

                                        <svg x-show="item.status === 'Sukses'" class="w-3.5 h-3.5"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                                clip-rule="evenodd" />
                                        </svg>

                                        <svg x-show="item.status === 'Gagal'" class="w-3.5 h-3.5"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                clip-rule="evenodd" />
                                        </svg>

                                        <svg x-show="item.status === 'Diproses'" class="w-3.5 h-3.5 animate-spin"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>

                                        <span x-text="item.status"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <button
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            Detail
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredData.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom Action Buttons (Ekspor / Cetak) -->
        <div class="flex flex-col sm:flex-row gap-4 pt-2">
            <button
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" x2="12" y1="15" y2="3" />
                </svg>
                Ekspor Data Masuk (CSV)
            </button>
            <button
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9" />
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                    <rect width="12" height="8" x="6" y="14" />
                </svg>
                Cetak Log (PDF)
            </button>
        </div>

    </div>

    <!-- Script Data Alpine.js -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('riwayatData', () => ({
                searchQuery: '',
                filterPengunggah: 'all',
                filterPeriode: 'all',
                filterJenis: 'all',

                historyData: [{
                        id: 1,
                        tglUpload: '12/02/26 14:20',
                        namaFile: 'KorelasiVeg_Jan26.xlsx',
                        pengunggah: 'Admin',
                        jenisDataset: 'Vegetatif',
                        baris: '1.250',
                        status: 'Sukses',
                    },
                    {
                        id: 2,
                        tglUpload: '10/03/26 09:15',
                        namaFile: 'Rekap_JanFebMar26.xlsx',
                        pengunggah: 'Admin',
                        jenisDataset: 'Rekap TBM III',
                        baris: '1.500',
                        status: 'Sukses',
                    },
                    {
                        id: 3,
                        tglUpload: '18/01/26 10:00',
                        namaFile: 'Map_Tiles.zip',
                        pengunggah: 'SuperAdmin',
                        jenisDataset: 'Map Tiles',
                        baris: '-',
                        status: 'Gagal',
                    },
                    {
                        id: 4,
                        tglUpload: '11/03/26 11:30',
                        namaFile: 'Data_Vegetasi_Baru.xlsx',
                        pengunggah: 'Admin',
                        jenisDataset: 'Vegetatif',
                        baris: '850',
                        status: 'Diproses',
                    }
                ],

                get filteredData() {
                    return this.historyData.filter((item) => {
                        const matchesSearch = item.namaFile.toLowerCase().includes(this
                            .searchQuery.toLowerCase());
                        const matchesPengunggah = this.filterPengunggah === 'all' || item
                            .pengunggah === this.filterPengunggah;
                        const matchesPeriode = this.filterPeriode === 'all' || item
                            .tglUpload.includes(this.filterPeriode);
                        const matchesJenis = this.filterJenis === 'all' || item
                            .jenisDataset === this.filterJenis;

                        return matchesSearch && matchesPengunggah && matchesPeriode &&
                            matchesJenis;
                    });
                },

                get statusCounts() {
                    return {
                        total: this.historyData.length,
                        success: this.historyData.filter((d) => d.status === 'Sukses').length,
                        error: this.historyData.filter((d) => d.status === 'Gagal').length,
                        processing: this.historyData.filter((d) => d.status === 'Diproses').length,
                    };
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.filterPengunggah = 'all';
                    this.filterPeriode = 'all';
                    this.filterJenis = 'all';
                }
            }));
        });
    </script>
</x-layout.default>
