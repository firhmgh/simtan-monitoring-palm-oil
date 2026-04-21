<div :class="{ 'dark text-white-dark': $store.app.semidark }">
    <nav x-data="sidebar"
        class="sidebar fixed min-h-screen h-full top-0 bottom-0 w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-50 transition-all duration-300">
        <div class="bg-white dark:bg-[#0e1726] h-full flex flex-col">

            <!-- LOGO SECTION -->
            <div class="h-20 flex items-center justify-between px-5 border-b border-gray-100 dark:border-[#191e3a]">
                <a href="<?php echo e(route('index')); ?>" class="flex items-center gap-3.5 group shrink-0">
                    <div
                        class="w-[46px] h-[46px] bg-[#00a76f] rounded-xl flex items-center justify-center shrink-0 shadow-sm shadow-emerald-200/50 dark:shadow-none transition-transform group-hover:scale-105">
                        <img class="w-8 h-8 object-contain" src="<?php echo e(asset('assets/images/logo-ptpn4.png')); ?>"
                            alt="Logo PTPN4" />
                    </div>

                    <div class="flex flex-col justify-center h-[46px]">
                        <span
                            class="font-bold text-[#0e1726] dark:text-white-light text-[21px] leading-[24px] tracking-tighter">
                            PTPN IV
                        </span>
                        <span
                            class="text-[12px] text-slate-400 dark:text-white-dark font-black tracking-[0.05em] leading-[18px]">
                            Regional I
                        </span>
                    </div>
                </a>

                <!-- Collapse Button -->
                <button type="button" @click="$store.app.toggleSidebar()"
                    class="text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-600/10 p-2 rounded-full transition-all">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                </button>
            </div>

            <!-- MENU UTAMA -->
            <nav class="flex-1 overflow-y-auto p-3 space-y-1 perfect-scrollbar">
                <?php
                    // Penambahan array 'roles' untuk kontrol akses menu (Sesuai Bab 3.6.1.1 Skripsi)
                    $menus = [
                        [
                            'route' => 'index',
                            'label' => 'Dashboard',
                            'icon' => 'layout-dashboard',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                        [
                            'route' => 'monitoring.data-kebun',
                            'label' => 'Data Kebun',
                            'icon' => 'tabler-database',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                        [
                            'route' => 'monitoring.detail-kebun',
                            'label' => 'Detail Kebun',
                            'icon' => 'leaf',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                        [
                            'route' => 'monitoring.laporan',
                            'label' => 'Generate Laporan',
                            'icon' => 'file-bar-chart-2',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                        [
                            'route' => 'monitoring.import',
                            'label' => 'Upload Data',
                            'icon' => 'upload',
                            'roles' => ['superadmin', 'admin'],
                        ],
                        [
                            'route' => 'monitoring.riwayat-data',
                            'label' => 'Riwayat Data',
                            'icon' => 'activity',
                            'roles' => ['superadmin', 'admin'],
                        ],
                        [
                            'route' => 'monitoring.kelola-akun',
                            'label' => 'Kelola Akun',
                            'icon' => 'users',
                            'roles' => ['superadmin'],
                        ],
                        [
                            'route' => 'monitoring.settings',
                            'label' => 'Settings',
                            'icon' => 'settings',
                            'roles' => ['superadmin', 'admin', 'user'],
                        ],
                    ];
                ?>

                <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        // Logika Pengecekan Hak Akses (Clean Code & Professional)
                        $hasAccess = false;
                        foreach ($menu['roles'] as $role) {
                            if (auth()->user()->hasRole($role)) {
                                $hasAccess = true;
                                break;
                            }
                        }
                    ?>

                    <!-- Menu hanya dirender jika role user diizinkan -->
                    <?php if($hasAccess): ?>
                        <?php
                            $url =
                                isset($menu['route']) && $menu['route'] !== '#' && Route::has($menu['route'])
                                    ? route($menu['route'])
                                    : '#';

                            $isActive = $url !== '#' && request()->routeIs($menu['route']);
                        ?>

                        <a href="<?php echo e($url); ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group
                        <?php echo e($isActive
                            ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-white-dark dark:hover:bg-[#191e3a]'); ?>">

                            <div
                                class="<?php echo e($isActive ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500'); ?>">
                                <?php if(str_starts_with($menu['icon'], 'tabler-')): ?>
                                    <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $menu['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\DynamicComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                                <?php elseif($menu['icon'] == 'layout-dashboard'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7" />
                                        <rect x="14" y="3" width="7" height="7" />
                                        <rect x="14" y="14" width="7" height="7" />
                                        <rect x="3" y="14" width="7" height="7" />
                                    </svg>
                                <?php elseif($menu['icon'] == 'leaf'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" />
                                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" />
                                    </svg>
                                <?php elseif($menu['icon'] == 'file-bar-chart-2'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <path d="M12 18v-4" />
                                        <path d="M8 18v-2" />
                                        <path d="M16 18v-6" />
                                    </svg>
                                <?php elseif($menu['icon'] == 'upload'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="17 8 12 3 7 8" />
                                        <line x1="12" y1="3" x2="12" y2="15" />
                                    </svg>
                                <?php elseif($menu['icon'] == 'users'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                <?php elseif($menu['icon'] == 'activity'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                    </svg>
                                <?php elseif($menu['icon'] == 'settings'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3" />
                                        <path
                                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                    </svg>
                                <?php endif; ?>
                            </div>

                            <span class="text-sm font-medium"><?php echo e($menu['label']); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- LOGOUT -->
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="pt-2">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                        class="flex items-center gap-3 px-3 py-2.5 w-full text-red-500 hover:bg-red-50 rounded-lg transition-all font-semibold">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        <span class="text-sm">Logout</span>
                    </button>
                </form>
            </nav>

            

        </div>
    </nav>
</div>
<?php /**PATH C:\simtan-monitoring-palm-oil\resources\views/components/common/sidebar.blade.php ENDPATH**/ ?>