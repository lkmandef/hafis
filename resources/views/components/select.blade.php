@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'value' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
    'placeholder' => null
])

@php
    $selectId = $id ?? $name ?? 'select-' . uniqid();
@endphp

<div class="w-full min-w-0 max-w-full">
    @if ($label)
        <label for="{{ $selectId }}" class="block text-xs font-medium text-gray-700 mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-red-500 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="relative w-full min-w-0 max-w-full">
        <select 
            id="{{ $selectId }}"
            name="{{ $name }}"
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'appearance-none w-full min-w-0 max-w-full rounded-lg ' . 
                    ($error 
                        ? 'border-red-300 bg-red-50/30 text-red-900 focus:border-red-500 focus:ring-1 focus:ring-red-500' 
                        : 'border-gray-200 bg-white text-gray-900 focus:border-gray-900 focus:ring-1 focus:ring-gray-900') . 
                    ' border pl-3.5 pr-9 py-2 text-sm focus:outline-none transition min-h-[40px] cursor-pointer'
            ]) }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            {{ $slot }}
        </select>

        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
            </svg>
        </span>
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
