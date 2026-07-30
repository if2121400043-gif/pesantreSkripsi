@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'hint' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
        <label for="{{ $attributes->get('id', $name) }}" class="block text-sm font-medium text-surface-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-danger-500">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $attributes->get('id', $name) }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="form-input {{ $error ? 'is-error' : '' }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class', 'id']) }}
    >

    @if($hint && !$error)
        <p class="mt-1 text-xs text-surface-400">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1 text-xs text-danger-500 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3 h-3"></i>
            {{ $error }}
        </p>
    @endif
</div>
