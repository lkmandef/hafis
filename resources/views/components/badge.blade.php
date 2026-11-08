@props([
    'variant' => 'neutral',
    'dot' => false
])

@php
    $variants = [
        'success' => 'bg-emerald-50/50 text-emerald-700 border-emerald-200/80',
        'warning' => 'bg-amber-50/50 text-amber-700 border-amber-200/80',
        'danger'  => 'bg-rose-50/50 text-rose-700 border-rose-200/80',
        'neutral' => 'bg-gray-50 text-gray-700 border-gray-200',
        'gray'    => 'bg-gray-50 text-gray-700 border-gray-200',
        'default' => 'bg-gray-50 text-gray-700 border-gray-200',
        'info'    => 'bg-sky-50/50 text-sky-700 border-sky-200/80',
        'purple'  => 'bg-purple-50/50 text-purple-700 border-purple-200/80',
    ];

    $classes = 'rounded-md px-2 py-0.5 text-xs font-medium inline-flex items-center border ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes, 'role' => 'status']) }}>
    {{ $slot }}
</span>

