@props([
    'label' => '',
    'value' => 0,
    'icon' => 'activity',
    'trend' => null,
    'trendValue' => null,
    'color' => 'primary',
    'href' => null,
])

@php
    $colorMap = [
        'primary'   => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600', 'icon_bg' => 'bg-primary-500'],
        'secondary' => ['bg' => 'bg-secondary-100', 'text' => 'text-secondary-600', 'icon_bg' => 'bg-secondary-500'],
        'accent'    => ['bg' => 'bg-accent-100', 'text' => 'text-accent-700', 'icon_bg' => 'bg-accent-500'],
        'success'   => ['bg' => 'bg-success-50', 'text' => 'text-success-700', 'icon_bg' => 'bg-success-500'],
        'danger'    => ['bg' => 'bg-danger-50', 'text' => 'text-danger-700', 'icon_bg' => 'bg-danger-500'],
        'warning'   => ['bg' => 'bg-warning-50', 'text' => 'text-warning-700', 'icon_bg' => 'bg-warning-500'],
    ];
    $colors = $colorMap[$color] ?? $colorMap['primary'];
@endphp

<div class="stat-card group {{ $href ? 'cursor-pointer' : '' }}" @if($href) onclick="window.location='{{ $href }}'" @endif>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-surface-500 mb-1">{{ $label }}</p>
            <p class="text-2xl font-bold text-surface-900 font-heading">{{ $value }}</p>
            @if($trend && $trendValue)
                <div class="flex items-center gap-1 mt-2">
                    @if($trend === 'up')
                        <i data-lucide="trending-up" class="w-3.5 h-3.5 text-success-500"></i>
                        <span class="text-xs font-medium text-success-700">+{{ $trendValue }}</span>
                    @elseif($trend === 'down')
                        <i data-lucide="trending-down" class="w-3.5 h-3.5 text-danger-500"></i>
                        <span class="text-xs font-medium text-danger-500">-{{ $trendValue }}</span>
                    @endif
                    <span class="text-xs text-surface-400">dari bulan lalu</span>
                </div>
            @endif
        </div>
        <div class="w-11 h-11 rounded-xl {{ $colors['icon_bg'] }} flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-200">
            <i data-lucide="{{ $icon }}" class="w-5 h-5 text-white"></i>
        </div>
    </div>
</div>
