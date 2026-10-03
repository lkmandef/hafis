@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold transition-colors min-h-[40px] focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-1 active:scale-[0.99] cursor-pointer shrink-0';

    $variants = [
        'primary'   => 'bg-slate-900 hover:bg-slate-800 text-white border border-slate-900 shadow-xs',
        'secondary' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 shadow-xs',
        'ghost'     => 'bg-transparent hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-transparent',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
