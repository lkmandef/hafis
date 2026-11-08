@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors min-h-[40px] focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-1 motion-safe:active:scale-[0.98] motion-reduce:transform-none cursor-pointer sm:shrink-0';

    $variants = [
        'primary'   => 'bg-gray-900 text-white hover:bg-black border border-transparent shadow-xs',
        'secondary' => 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200 shadow-2xs',
        'outline'   => 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200 shadow-2xs',
        'danger'    => 'bg-rose-600 text-white hover:bg-rose-700 border border-transparent shadow-xs',
        'ghost'     => 'bg-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100 border border-transparent',
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
