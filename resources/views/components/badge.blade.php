@props([
    'variant' => 'neutral',
    'dot' => false
])

@php
    $variants = [
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200/80',
        'danger'  => 'bg-rose-50 text-rose-800 border-rose-200/80',
        'neutral' => 'bg-slate-100 text-slate-700 border-slate-200/80',
    ];

    $dotColors = [
        'success' => 'bg-emerald-600',
        'warning' => 'bg-amber-500',
        'danger'  => 'bg-rose-600',
        'neutral' => 'bg-slate-500',
    ];

    $classes = 'rounded-md px-2 py-0.5 text-[11px] font-semibold inline-flex items-center gap-1.5 border ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$variant] ?? $dotColors['neutral'] }}"></span>
    @endif
    {{ $slot }}
</span>
