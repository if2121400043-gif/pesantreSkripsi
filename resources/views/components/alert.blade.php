@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => false,
])

@php
    $icons = [
        'success' => 'check-circle',
        'danger' => 'x-circle',
        'warning' => 'alert-triangle',
        'info' => 'info',
    ];
@endphp

<div class="alert alert-{{ $type }}" role="alert" x-data="{ show: true }" x-show="show" x-transition>
    <i data-lucide="{{ $icons[$type] ?? 'info' }}" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
    <div class="flex-1">
        {{ $message }}{{ $slot }}
    </div>
    @if($dismissible)
        <button @click="show = false" class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity" aria-label="Tutup">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    @endif
</div>
