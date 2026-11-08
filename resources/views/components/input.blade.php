@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'value' => null,
    'error' => null,
    'hint' => null,
    'required' => false
])

@php
    $inputId = $id ?? $name ?? 'input-' . uniqid();
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-xs font-medium text-gray-700 mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-red-500 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if (isset($icon))
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                {{ $icon }}
            </div>
        @endif

        <input 
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full rounded-lg ' . 
                    ($error 
                        ? 'border-red-300 bg-red-50/30 text-red-900 placeholder-red-400 focus:border-red-500 focus:ring-1 focus:ring-red-500' 
                        : 'border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900') . 
                    ' border ' .
                    (isset($icon) ? 'pl-9 ' : 'px-3.5 ') .
                    'py-2 text-sm focus:outline-none transition min-h-[40px]'
            ]) }}
        />
    </div>

    @if ($error)
        <p class="text-xs text-red-600 font-medium mt-1.5 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ $error }}</span>
        </p>
    @elseif ($hint)
        <p class="text-xs text-gray-500 mt-1.5 font-normal">
            {{ $hint }}
        </p>
    @endif
</div>
