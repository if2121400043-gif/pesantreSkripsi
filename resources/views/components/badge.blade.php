@props([
    'type' => 'info',
    'dot' => false,
])

<span {{ $attributes->merge(['class' => "badge badge-{$type}"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    @endif
    {{ $slot }}
</span>
