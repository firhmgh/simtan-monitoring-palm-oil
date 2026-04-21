<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset='utf-8' />
    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
    <title><?php echo e($title ?? 'SIMTAN - Sistem Informasi Monitoring Areal Tanaman'); ?></title>

    <meta name='viewport' content='width=device-width, initial-scale=1' />
    <link rel="icon" type="image/svg" href="/assets/images/logo-ptpn4.png" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <script src="/assets/js/perfect-scrollbar.min.js"></script>
    <script defer src="/assets/js/popper.min.js"></script>
    <script defer src="/assets/js/tippy-bundle.umd.min.js"></script>
    <script defer src="/assets/js/sweetalert.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>

<body x-data="main" class="antialiased relative font-nunito text-sm font-normal overflow-x-hidden"
    :class="[$store.app.sidebar ? 'toggle-sidebar' : '', $store.app.theme === 'dark' || $store.app.isDarkMode ? 'dark' : '',
        $store.app.menu, $store.app.layout, $store.app.rtlClass
    ]">

    <!-- sidebar menu overlay -->
    <div x-cloak class="fixed inset-0 bg-[black]/60 z-50 lg:hidden" :class="{ 'hidden': !$store.app.sidebar }"
        @click="$store.app.toggleSidebar()"></div>

    <!-- screen loader -->
    <div
        class="screen_loader fixed inset-0 bg-[#fafafa] dark:bg-[#060818] z-[60] grid place-content-center animate__animated">
        <svg width="64" height="64" viewBox="0 0 135 135" xmlns="http://www.w3.org/2000/svg" fill="#4361ee">
            <path
                d="M67.447 58c5.523 0 10-4.477 10-10s-4.477-10-10-10-10 4.477-10 10 4.477 10 10 10zm9.448 9.447c0 5.523 4.477 10 10 10 5.522 0 10-4.477 10-10s-4.478-10-10-10c-5.523 0-10 4.477-10 10zm-9.448 9.448c-5.523 0-10 4.477-10 10 0 5.522 4.477 10 10 10s10-4.478 10-10c0-5.523-4.477-10-10-10zM58 67.447c0-5.523-4.477-10-10-10s-10 4.477-10 10 4.477 10 10 10 10-4.477 10-10z">
                <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="-360 67 67" dur="2.5s"
                    repeatCount="indefinite" />
            </path>
            <path
                d="M28.19 40.31c6.627 0 12-5.374 12-12 0-6.628-5.373-12-12-12-6.628 0-12 5.372-12 12 0 6.626 5.372 12 12 12zm30.72-19.825c4.686 4.687 12.284 4.687 16.97 0 4.686-4.686 4.686-12.284 0-16.97-4.686-4.687-12.284-4.687-16.97 0-4.687 4.686-4.687 12.284 0 16.97zm35.74 7.705c0 6.627 5.37 12 12 12 6.626 0 12-5.373 12-12 0-6.628-5.374-12-12-12-6.63 0-12 5.372-12 12zm19.822 30.72c-4.686 4.686-4.686 12.284 0 16.97 4.687 4.686 12.285 4.686 16.97 0 4.687-4.686 4.687-12.284 0-16.97-4.685-4.687-12.283-4.687-16.97 0zm-7.704 35.74c-6.627 0-12 5.37-12 12 0 6.626 5.373 12 12 12s12-5.374 12-12c0-6.63-5.373-12-12-12zm-30.72 19.822c-4.686-4.686-12.284-4.686-16.97 0-4.686 4.687-4.686 12.285 0 16.97 4.686 4.687 12.284 4.687 16.97 0 4.687-4.685 4.687-12.283 0-16.97zm-35.74-7.704c0-6.627-5.372-12-12-12-6.626 0-12 5.373-12 12s5.374 12 12 12c6.628 0 12-5.373 12-12zm-19.823-30.72c4.687-4.686 4.687-12.284 0-16.97-4.686-4.686-12.284-4.686-16.97 0-4.687 4.686-4.687 12.284 0 16.97 4.686 4.687 12.284 4.687 16.97 0z">
                <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="360 67 67" dur="8s"
                    repeatCount="indefinite" />
            </path>
        </svg>
    </div>

    <!-- GLOBAL NOTIFICATION SYSTEM (TOAST) -->
    <div x-data="{ show: false, message: '', type: 'success' }" x-cloak
        @toast.window="
            message = $event.detail.message;
            type = $event.detail.type || 'success';
            show = true;
            setTimeout(() => show = false, 4000);
        "
        class="relative z-[1000]">

        <!-- Case: Success (High Impact Centered) -->
        <template x-if="type === 'success'">
            <div x-show="show" x-transition.opacity
                class="fixed inset-0 flex items-center justify-center bg-black/20 backdrop-blur-[2px] z-[1001]">
                <div
                    class="bg-white dark:bg-[#0e1726] rounded-3xl shadow-2xl w-full max-w-[320px] p-8 text-center border-b-4 border-emerald-500 animate__animated animate__zoomIn">
                    <div class="flex justify-center mb-5">
                        <div
                            class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-emerald-600"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-tight"
                        x-text="message"></h3>
                    <p class="text-[10px] text-gray-400 mt-3 font-bold uppercase tracking-[0.2em]">PTPN IV Regional I
                    </p>
                </div>
            </div>
        </template>

        <!-- Case: Error / Info (Top Bar Style) -->
        <template x-if="type === 'error' || type === 'info'">
            <div x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
                class="fixed top-10 inset-x-0 flex justify-center z-[1001] px-4 pointer-events-none">
                <div :class="type === 'error' ? 'border-red-500 bg-white' : 'border-blue-500 bg-white'"
                    class="dark:bg-[#1b2e4b] border-l-4 rounded-xl shadow-2xl w-full max-w-md p-5 flex items-center gap-4 pointer-events-auto">
                    <div :class="type === 'error' ? 'text-red-500' : 'text-blue-500'">
                        <template x-if="type === 'error'"><svg class="w-8 h-8" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg></template>
                        <template x-if="type === 'info'"><svg class="w-8 h-8" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg></template>
                    </div>
                    <div class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-tight"
                        x-text="message"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- BACKEND SESSION TO EVENT BRIDGE (SINKRONISASI TOTAL) -->
    <script>
        window.onload = () => {
            <?php if(session('success')): ?>
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        message: "<?php echo e(session('success')); ?>",
                        type: 'success'
                    }
                }));
            <?php endif; ?>

            <?php if(session('error')): ?>
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        message: "<?php echo e(session('error')); ?>",
                        type: 'error'
                    }
                }));
            <?php endif; ?>

            <?php if(session('toast')): ?>
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        message: "<?php echo e(session('toast')['message']); ?>",
                        type: "<?php echo e(session('toast')['type']); ?>"
                    }
                }));
            <?php endif; ?>
        }
    </script>

    <div class="fixed bottom-6 ltr:right-6 rtl:left-6 z-50" x-data="scrollToTop">
        <template x-if="showTopButton">
            <button type="button"
                class="btn btn-outline-primary rounded-full p-2 animate-pulse bg-[#fafafa] dark:bg-[#060818] dark:hover:bg-primary"
                @click="goToTop">
                <svg width="24" height="24" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.5" d="M12 20.75V10.75" fill="currentColor" />
                    <path d="M6.00002 10.75L12 3.25L18 10.75H6.00002Z" fill="currentColor" />
                </svg>
            </button>
        </template>
    </div>

    <?php if (isset($component)) { $__componentOriginal4532e835e6ce50c8f5b5a9c3752b0135 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4532e835e6ce50c8f5b5a9c3752b0135 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.theme-customiser','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('common.theme-customiser'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4532e835e6ce50c8f5b5a9c3752b0135)): ?>
<?php $attributes = $__attributesOriginal4532e835e6ce50c8f5b5a9c3752b0135; ?>
<?php unset($__attributesOriginal4532e835e6ce50c8f5b5a9c3752b0135); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4532e835e6ce50c8f5b5a9c3752b0135)): ?>
<?php $component = $__componentOriginal4532e835e6ce50c8f5b5a9c3752b0135; ?>
<?php unset($__componentOriginal4532e835e6ce50c8f5b5a9c3752b0135); ?>
<?php endif; ?>

    <div class="main-container text-black dark:text-white-dark min-h-screen" :class="[$store.app.navbar]">
        <?php if (isset($component)) { $__componentOriginalb452accb78b4116e5d057094b9f3361b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb452accb78b4116e5d057094b9f3361b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('common.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb452accb78b4116e5d057094b9f3361b)): ?>
<?php $attributes = $__attributesOriginalb452accb78b4116e5d057094b9f3361b; ?>
<?php unset($__attributesOriginalb452accb78b4116e5d057094b9f3361b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb452accb78b4116e5d057094b9f3361b)): ?>
<?php $component = $__componentOriginalb452accb78b4116e5d057094b9f3361b; ?>
<?php unset($__componentOriginalb452accb78b4116e5d057094b9f3361b); ?>
<?php endif; ?>
        <div class="main-content flex flex-col min-h-screen">
            <?php if (isset($component)) { $__componentOriginald28a8bb735c743494aab3aa3bad09829 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald28a8bb735c743494aab3aa3bad09829 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.header','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('common.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald28a8bb735c743494aab3aa3bad09829)): ?>
<?php $attributes = $__attributesOriginald28a8bb735c743494aab3aa3bad09829; ?>
<?php unset($__attributesOriginald28a8bb735c743494aab3aa3bad09829); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald28a8bb735c743494aab3aa3bad09829)): ?>
<?php $component = $__componentOriginald28a8bb735c743494aab3aa3bad09829; ?>
<?php unset($__componentOriginald28a8bb735c743494aab3aa3bad09829); ?>
<?php endif; ?>
            <div class="dvanimation p-6 animate__animated" :class="[$store.app.animation]">
                <?php echo e($slot); ?>

            </div>
            <?php if (isset($component)) { $__componentOriginalc7767d3a8a9b46033c64e207f06d76b6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7767d3a8a9b46033c64e207f06d76b6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('common.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7767d3a8a9b46033c64e207f06d76b6)): ?>
<?php $attributes = $__attributesOriginalc7767d3a8a9b46033c64e207f06d76b6; ?>
<?php unset($__attributesOriginalc7767d3a8a9b46033c64e207f06d76b6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7767d3a8a9b46033c64e207f06d76b6)): ?>
<?php $component = $__componentOriginalc7767d3a8a9b46033c64e207f06d76b6; ?>
<?php unset($__componentOriginalc7767d3a8a9b46033c64e207f06d76b6); ?>
<?php endif; ?>
        </div>
    </div>

    <script src="/assets/js/alpine-collaspe.min.js"></script>
    <script src="/assets/js/alpine-persist.min.js"></script>
    <script defer src="/assets/js/alpine-ui.min.js"></script>
    <script defer src="/assets/js/alpine-focus.min.js"></script>
    <script defer src="/assets/js/alpine.min.js"></script>
    <script src="/assets/js/custom.js"></script>
</body>

</html>
<?php /**PATH C:\simtan-monitoring-palm-oil\resources\views/components/layout/default.blade.php ENDPATH**/ ?>