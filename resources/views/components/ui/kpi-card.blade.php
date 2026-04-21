<!-- resources/views/ui-components/KPICard.blade.php -->

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm text-gray-600 mb-1">{{ $title }}</p>

            <h3 class="text-3xl font-bold text-gray-900 mb-2">
                {{ $value }}
            </h3>

            <div class="flex items-center gap-2">
                @if ($trend === 'up')
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="text-emerald-600">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="text-red-600">
                        <polyline points="22 17 13.5 8.5 8.5 13.5 2 7"></polyline>
                        <polyline points="16 17 22 17 22 11"></polyline>
                    </svg>
                @endif

                <span class="{{ $trend === 'up' ? 'text-emerald-600' : 'text-red-600' }} text-sm font-medium">
                    {{ number_format(abs($change), 1) }}%
                </span>
                <span class="text-xs text-gray-500">vs bulan lalu</span>
            </div>
        </div>

        <div
            class="w-12 h-12 bg-gradient-to-br {{ $gradient ?? 'from-emerald-500 to-emerald-600' }} rounded-lg flex items-center justify-center">
            <x-dynamic-component :component="'lucide-' . $icon" class="w-6 h-6 text-white" />
        </div>
    </div>
</div>
