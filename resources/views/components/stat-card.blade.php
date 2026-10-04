@props([
    'title',
    'value',
    'isPrimary' => false,
    'status' => 'normal',
    'context' => null
])

@php
    $statusTextColors = [
        'normal'  => 'text-slate-900',
        'success' => 'text-emerald-700',
        'warning' => 'text-amber-700',
        'danger'  => 'text-rose-700',
    ];

    $statusBorderColors = [
        'normal'  => 'border-slate-200',
        'success' => 'border-slate-200 border-l-4 border-l-emerald-600',
        'warning' => 'border-slate-200 border-l-4 border-l-amber-500',
        'danger'  => 'border-slate-200 border-l-4 border-l-rose-500',
    ];

    $cardClasses = $isPrimary
        ? 'bg-slate-900 text-white rounded-xl border border-slate-800 p-3.5 sm:p-5 shadow-xs flex flex-col justify-between'
        : 'bg-white text-slate-900 rounded-xl border ' . ($statusBorderColors[$status] ?? 'border-slate-200') . ' p-3 sm:p-5 shadow-xs flex flex-col justify-between';
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    <div>
        <span class="block text-[10px] sm:text-xs font-semibold uppercase tracking-wider {{ $isPrimary ? 'text-slate-400' : 'text-slate-500' }}">
            {{ $title }}
        </span>
        <div class="mt-1 sm:mt-2 flex items-baseline gap-1.5">
            <span class="font-extrabold tracking-tight tabular-nums {{ $isPrimary ? 'text-2xl sm:text-4xl text-white' : 'text-xl sm:text-3xl ' . ($statusTextColors[$status] ?? 'text-slate-900') }}">
                {{ $value }}
            </span>
            <span class="text-[10px] sm:text-xs font-medium {{ $isPrimary ? 'text-slate-400' : 'text-slate-500' }}">Unit</span>
        </div>
    </div>

    @if ($context)
        <div class="mt-2 sm:mt-3 pt-1.5 sm:pt-2.5 border-t {{ $isPrimary ? 'border-slate-800 text-slate-400' : 'border-slate-100 text-slate-500' }} text-[10px] sm:text-xs font-medium">
            {{ $context }}
        </div>
    @endif
</div>
