@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'rows' => 3,
    'error' => null,
    'hint' => null,
    'required' => false
])

@php
    $textareaId = $id ?? $name ?? 'textarea-' . uniqid();
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $textareaId }}" class="block text-xs font-semibold text-slate-700 mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500 font-bold">*</span>
            @endif
        </label>
    @endif

    <textarea 
        id="{{ $textareaId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg ' . 
                ($error ? 'border-rose-400 bg-rose-50/20 text-rose-900 focus:border-rose-500 focus:ring-1 focus:ring-rose-200' : 'border-slate-200 bg-slate-50 text-slate-800 focus:border-slate-400 focus:ring-1 focus:ring-slate-400') . 
                ' border px-3 py-2 text-xs placeholder-slate-400 focus:bg-white focus:outline-none transition'
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @if ($error)
        <p class="text-[11px] text-rose-600 font-medium mt-1 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ $error }}</span>
        </p>
    @elseif ($hint)
        <p class="text-[11px] text-slate-500 mt-1 font-normal">
            {{ $hint }}
        </p>
    @endif
</div>
