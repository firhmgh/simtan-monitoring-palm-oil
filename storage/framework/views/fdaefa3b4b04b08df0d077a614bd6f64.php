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
    

    <div x-data="monitoringData()" class="space-y-5">
        <!-- 1. Breadcrumbs & Header -->
        <div>
            <ul class="flex space-x-2 rtl:space-x-reverse text-xs font-semibold mb-3">
                <li><a href="javascript:;" class="text-primary hover:underline">Monitoring</a></li>
                <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 text-gray-400 dark:text-gray-500">
                    <span>Data Kebun</span>
                </li>
            </ul>

            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white-light font-heading">Daftar Kebun</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm font-medium">
                        Kelola dan monitor semua kebun kelapa sawit TBM III Regional I
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. KPI Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Green (Total Luas) -->
            <div class="relative overflow-hidden rounded-3xl p-6 shadow-sm"
                style="background: linear-gradient(135deg, #00a76f 0%, #007850 100%);">
                <div class="relative z-10 text-white">
                    <div class="text-[10px] font-black text-white/70 tracking-widest mb-3">Total Luas Areal
                    </div>
                    <div class="flex items-end gap-2">
                        
                        <span class="text-4xl font-black">2.450</span>
                        <span class="text-lg font-bold text-white/80 mb-1">Ha</span>
                    </div>
                    <div
                        class="mt-4 flex items-center text-[10px] font-bold text-white/90 bg-black/10 w-fit px-3 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-white mr-2"></span>
                        Seluruh Regional I
                    </div>
                </div>
            </div>

            <!-- Card 2: Blue (Total Pokok) -->
            <div class="relative overflow-hidden rounded-3xl p-6 shadow-sm"
                style="background: linear-gradient(135deg, #1c64f2 0%, #154ec1 100%);">
                <div class="relative z-10 text-white">
                    <div class="text-[10px] font-black text-white/70 tracking-widest mb-3">Total Jumlah Pokok
                    </div>
                    <div class="flex items-end gap-2">
                        
                        <span class="text-4xl font-black">342.300</span>
                    </div>
                    <div
                        class="mt-4 flex items-center text-[10px] font-bold text-white/90 bg-black/10 w-fit px-3 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-white mr-2"></span>
                        Pokok Terdata
                    </div>
                </div>
            </div>

            <!-- Card 3: Purple (Avg Health) -->
            <div class="relative overflow-hidden rounded-3xl p-6 shadow-sm"
                style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                <div class="relative z-10 text-white">
                    <div class="text-[10px] font-black text-white/70 tracking-widest mb-3">Rata-rata Kesehatan
                    </div>
                    <div class="flex items-end gap-2">
                        
                        <span class="text-4xl font-black">82.8</span>
                        <span class="text-lg font-bold text-white/80 mb-1">%</span>
                    </div>
                    <div
                        class="mt-4 flex items-center text-[10px] font-bold text-white/90 bg-black/10 w-fit px-3 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-white mr-2"></span>
                        Kondisi TBM III
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Main Panel: Search & Table -->
        <div class="panel p-0 border-0 overflow-hidden rounded-3xl shadow-sm dark:bg-[#1b2e4b] relative">
            <div
                class="p-6 border-b border-gray-100 dark:border-gray-800 flex flex-col md:flex-row items-center justify-between gap-4 relative z-[50]">

                <!-- Input Search -->
                <div class="relative w-full md:w-[400px]">
                    <form action="#" method="GET"> 
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" placeholder="Cari kebun atau distrik..."
                            class="form-input pl-10 py-2.5 w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl" />
                    </form>
                </div>

                <!-- Dropdown Distrik -->
                <div class="relative w-full md:w-auto" x-data="{ openDropdown: false, selectedDistrik: 'Semua Distrik' }">
                    <button @click="openDropdown = !openDropdown" @click.away="openDropdown = false"
                        class="btn bg-white dark:bg-black/20 border border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3 w-full md:min-w-[240px] py-3 px-5 rounded-xl text-gray-700 dark:text-gray-200 hover:border-primary transition-all shadow-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span x-text="selectedDistrik" class="font-bold text-xs tracking-widest truncate"></span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" :class="openDropdown ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openDropdown" x-transition x-cloak
                        class="absolute right-0 z-[999] w-full md:w-max min-w-full mt-2 bg-white dark:bg-[#191e3a] border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl py-2">
                        <template x-for="item in distrikList" :key="item">
                            <button @click="selectedDistrik = item; openDropdown = false" 
                                class="w-full text-left px-5 py-3 hover:bg-gray-100 dark:hover:bg-primary/10 flex items-center justify-between gap-4 transition-colors">
                                <span x-text="item"
                                    class="text-xs font-bold tracking-widest text-gray-700 dark:text-gray-200"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive overflow-x-auto rounded-b-3xl">
                <table class="table-hover whitespace-nowrap w-full">
                    <thead>
                        <tr
                            class="bg-gray-50 dark:bg-black/20 text-gray-500 dark:text-gray-400 font-black text-[10px] tracking-[2px]">
                            <th class="py-4 px-6 text-left">No</th>
                            <th class="py-4 px-6 text-left">Distrik</th>
                            <th class="py-4 px-6 text-left">Nama Kebun</th>
                            <th class="py-4 px-6 text-left">Total Blok</th>
                            <th class="py-4 px-6 text-left">Total Areal (Ha)</th>
                            <th class="py-4 px-6 text-left">Status</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 dark:text-gray-300 font-semibold text-sm">

                        

                        <!-- DATA DUMMY (Akan Muncul Selama DB Belum Diaktifkan) -->
                        <tr
                            class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6">01</td>
                            <td class="py-4 px-6 text-[11px] font-bold text-gray-500 dark:text-gray-400">
                                Distrik Labuhan Batu 1</td>
                            <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">Kebun Sungai Daun</td>
                            <td class="py-4 px-6">24 <span class="text-[10px] text-gray-400 ml-1">Blok</span></td>
                            <td class="py-4 px-6 font-bold">1.450</td>
                            <td class="py-4 px-6">
                                <span
                                    class="badge bg-success !text-white rounded-full px-4 py-1 text-[10px] font-black shadow-sm">Baik</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button
                                    class="btn btn-sm btn-outline-primary rounded-lg text-[10px] font-black tracking-widest">Detail</button>
                            </td>
                        </tr>
                        <tr
                            class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6">02</td>
                            <td class="py-4 px-6 text-[11px] font-bold text-gray-500 dark:text-gray-400">
                                Distrik Labuhan Batu 2</td>
                            <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">Kebun Sei Kebara</td>
                            <td class="py-4 px-6">18 <span class="text-[10px] text-gray-400 ml-1">Blok</span></td>
                            <td class="py-4 px-6 font-bold">980</td>
                            <td class="py-4 px-6">
                                <span
                                    class="badge bg-warning !text-white rounded-full px-4 py-1 text-[10px] font-black shadow-sm">Perhatian</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button
                                    class="btn btn-sm btn-outline-primary rounded-lg text-[10px] font-black tracking-widest">Detail</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6">03</td>
                            <td class="py-4 px-6 text-[11px] font-bold text-gray-500 dark:text-gray-400">
                                Distrik Labuhan Batu 3</td>
                            <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">Kebun Aek Nabara Utara</td>
                            <td class="py-4 px-6">32 <span class="text-[10px] text-gray-400 ml-1">Blok</span></td>
                            <td class="py-4 px-6 font-bold">1.850</td>
                            <td class="py-4 px-6">
                                <span
                                    class="badge bg-danger !text-white rounded-full px-4 py-1 text-[10px] font-black shadow-sm">Kritis</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button
                                    class="btn btn-sm btn-outline-primary rounded-lg text-[10px] font-black tracking-widest">Detail</button>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50 dark:bg-black/10 border-t border-gray-100 dark:border-gray-800 text-center">
                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 tracking-widest">
                    
                    Menampilkan 3 dari total 8 kebun aktif dalam database SIMTAN
                </p>
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
<?php /**PATH C:\simtan-monitoring-palm-oil\resources\views/apps/monitoring/data-kebun.blade.php ENDPATH**/ ?>