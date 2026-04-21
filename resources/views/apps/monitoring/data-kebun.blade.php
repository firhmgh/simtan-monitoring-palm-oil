<x-layout.default>
    <div x-data="monitoringData()">
        <div>
            <!-- Breadcrumbs -->
            <ul class="flex space-x-2 rtl:space-x-reverse text-xs font-semibold mb-3">
                <li><a href="javascript:;" class="text-primary hover:underline">Monitoring</a></li>
                <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 text-gray-400">
                    <span>Data Kebun</span>
                </li>
            </ul>
        </div>
        <!-- 1. Header & Export Button -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4 pt-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Kebun</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                    Kelola dan monitor semua kebun kelapa sawit TBM III
                </p>
            </div>
            <!-- Menggunakan class btn-success bawaan Vristo -->
            <button type="button"
                class="btn btn-success flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold shadow-lg shadow-success/30">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 16L12 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M9 13L12 16L15 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M3 15L3 17C3 18.1046 3.89543 19 5 19L19 19C20.1046 19 21 18.1046 21 17L21 15"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Export Data
            </button>
        </div>

        <!-- 2. KPI Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Card 1: Green -->
            <div class="rounded-2xl shadow-sm p-6 text-white" style="background-color: #00a76f;">
                <div class="text-sm font-medium text-white/80 mb-3">Total Luas Areal<br>TBM III</div>
                <div class="text-[32px] font-bold leading-none mb-4">2.450<br>Ha</div>
                <div class="text-sm text-white/90 font-medium">Seluruh Regional I</div>
            </div>

            <!-- Card 2: Blue -->
            <div class="rounded-2xl shadow-sm p-6 text-white" style="background-color: #1c64f2;">
                <div class="text-sm font-medium text-white/80 mb-3">Total Jumlah Pokok</div>
                <div class="text-[40px] font-bold leading-none mb-4 mt-2">342.300</div>
                <div class="text-sm text-white/90 font-medium mt-6">Tanaman kelapa<br>sawit</div>
            </div>

            <!-- Card 3: Purple -->
            <div class="rounded-2xl shadow-sm p-6 text-white" style="background-color: #8b5cf6;">
                <div class="text-sm font-medium text-white/80 mb-3">Rata-rata Kesehatan</div>
                <div class="text-[40px] font-bold leading-none mb-4 mt-2">82.8%</div>
                <div class="text-sm text-white/90 font-medium mt-6">Tingkat kesehatan<br>tanaman</div>
            </div>
        </div>

        <!-- 3. Main Panel: Search, Filter & Table -->
        <div class="panel p-0 border-0 overflow-hidden rounded-2xl">

            <!-- Search & Filter Bar -->
            <!-- Note: justify-between memisahkan Search ke kiri penuh dan Dropdown ke kanan penuh.
                 Jika Anda ingin keduanya menempel bersebelahan, ubah "justify-between" menjadi "justify-start" -->
            <div
                class="p-5 border-b border-gray-200 dark:border-gray-800 flex flex-col md:flex-row items-center justify-between gap-4">

                <!-- Input Search -->
                <div class="relative w-full md:w-[400px]">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Cari kebun atau distrik..."
                        class="form-input pl-10 py-2.5 w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700" />
                </div>

                <!-- Dropdown Alpine.js -->
                <div class="relative w-full md:w-auto" x-data="{ openDropdown: false, selectedDistrik: 'Semua Distrik' }">

                    <!-- BUTTON DROPDOWN -->
                    <!-- Ditambahkan md:w-auto, min-w-[240px], whitespace-nowrap, dan flex-shrink-0 -->
                    <button @click="openDropdown = !openDropdown" @click.away="openDropdown = false"
                        class="btn bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3 w-full md:w-auto md:min-w-[240px] py-2.5 px-4 rounded-lg text-gray-700 dark:text-gray-300">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <!-- Teks dipaksa satu baris (whitespace-nowrap) -->
                            <span x-text="selectedDistrik" class="font-medium whitespace-nowrap truncate"></span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- MENU DROPDOWN ITEM -->
                    <!-- Ditambahkan right-0 (agar tidak keluar border layar kanan), md:w-max, dan min-w-full -->
                    <div x-show="openDropdown" x-transition.duration.200ms style="display: none;"
                        class="absolute right-0 z-10 w-full md:w-max min-w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1">

                        <template x-for="item in distrikList" :key="item">
                            <button @click="selectedDistrik = item; openDropdown = false"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-between gap-4">
                                <!-- Ditambahkan whitespace-nowrap agar list memanjang ke samping, tidak menumpuk ke bawah -->
                                <span x-text="item"
                                    class="text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap"></span>
                                <svg x-show="selectedDistrik === item" class="w-4 h-4 text-gray-500 flex-shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Table Header Text -->
            <div class="p-5 pb-0">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Showing 6 of 8 kebun</h2>
            </div>

            <!-- Table -->
            <div class="table-responsive p-5">
                <table class="table-hover whitespace-nowrap w-full">
                    <thead>
                        <tr
                            class="border-b border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 font-semibold">
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Distrik</th>
                            <th class="py-3 px-4 text-left">Nama Kebun</th>
                            <th class="py-3 px-4 text-left">Total Blok</th>
                            <th class="py-3 px-4 text-left">Total Areal (Ha)</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 dark:text-gray-300">
                        <!-- Row 1 (Baik) -->
                        <tr
                            class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="py-3 px-4">1</td>
                            <td class="py-3 px-4">Distrik Labuhan Batu 1</td>
                            <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white">Kebun Sungai Daun</td>
                            <td class="py-3 px-4">24</td>
                            <td class="py-3 px-4">1.450</td>
                            <td class="py-3 px-4">
                                <span
                                    class="badge bg-green-100 text-green-600 dark:bg-green-500/20 dark:text-green-400 rounded-full px-3 py-1 font-bold text-xs">Baik</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button class="text-primary hover:text-primary/80">Detail</button>
                            </td>
                        </tr>

                        <!-- Row 2 (Perlu Perhatian) -->
                        <tr
                            class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="py-3 px-4">2</td>
                            <td class="py-3 px-4">Distrik Labuhan Batu 2</td>
                            <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white">Kebun Sei Kebara</td>
                            <td class="py-3 px-4">18</td>
                            <td class="py-3 px-4">980</td>
                            <td class="py-3 px-4">
                                <span
                                    class="badge bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400 rounded-full px-3 py-1 font-bold text-xs">Perlu
                                    Perhatian</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button class="text-primary hover:text-primary/80">Detail</button>
                            </td>
                        </tr>

                        <!-- Row 3 (Kritis) -->
                        <tr
                            class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                            <td class="py-3 px-4">3</td>
                            <td class="py-3 px-4">Distrik Labuhan Batu 3</td>
                            <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white">Kebun Aek Nabara Utara
                            </td>
                            <td class="py-3 px-4">32</td>
                            <td class="py-3 px-4">1.850</td>
                            <td class="py-3 px-4">
                                <span
                                    class="badge bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400 rounded-full px-3 py-1 font-bold text-xs">Kritis</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button class="text-primary hover:text-primary/80">Detail</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Alpine Data Script -->
    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("monitoringData", () => ({
                distrikList: [
                    'Semua Distrik',
                    'Distrik Labuhan Batu I',
                    'Distrik Labuhan Batu II',
                    'Distrik Labuhan Batu III',
                    'Distrik Deli Serdang I',
                    'Distrik Deli Serdang II'
                ],
            }));
        });
    </script>
</x-layout.default>
