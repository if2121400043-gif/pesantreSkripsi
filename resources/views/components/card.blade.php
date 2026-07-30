@props([
    'title' => null,
    'padding' => true,
    'headerActions' => null,
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || $headerActions)
        <div class="card-header">
            @if($title)
                <h3 class="text-base font-semibold text-surface-900 font-heading">{{ $title }}</h3>
            @endif
            @if($headerActions)
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $padding ? 'card-body' : '' }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>
