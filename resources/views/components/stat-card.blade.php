@props([
    'title',
    'value',
    'isPrimary' => false,
    'status' => 'normal',
    'context' => null
])

@php
    $badges = [
        'normal'  => ['label' => 'Unit', 'class' => 'bg-gray-50 text-gray-700 border-gray-200'],
        'success' => ['label' => 'Layak', 'class' => 'bg-emerald-50/50 text-emerald-700 border-emerald-200/80'],
        'warning' => ['label' => 'Perhatian', 'class' => 'bg-amber-50/50 text-amber-700 border-amber-200/80'],
        'danger'  => ['label' => 'Rusak', 'class' => 'bg-rose-50/50 text-rose-700 border-rose-200/80'],
    ];

    $badge = $badges[$status] ?? $badges['normal'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-200 p-5 sm:p-6 flex flex-col justify-between transition-colors']) }}>
    <div>
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-medium text-gray-500">
                {{ $title }}
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium border {{ $badge['class'] }}" role="status">
                {{ $badge['label'] }}
            </span>
        </div>
        <div class="mt-3 flex items-baseline gap-1.5">
            <span class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 tabular-nums font-mono">
                {{ $value }}
            </span>
            <span class="text-xs text-gray-400 font-medium font-sans">unit</span>
        </div>
    </div>

    @if ($context)
        <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500 flex items-center justify-between">
            <span>{{ $context }}</span>
        </div>
    @endif
</div>
