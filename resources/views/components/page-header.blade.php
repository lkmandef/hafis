@props([
    'title',
    'description' => null
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 sm:pb-5 mb-5 sm:mb-6 border-b border-slate-200/80']) }}>
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
            {{ $title }}
        </h1>
        @if ($description)
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                {{ $description }}
            </p>
        @endif
    </div>

    @if (isset($actions))
        <div class="w-full sm:w-auto flex flex-wrap sm:flex-nowrap items-center gap-2 sm:gap-2.5 sm:shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
