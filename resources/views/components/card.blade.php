@props([
    'title' => null,
    'subtitle' => null
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 shadow-xs p-5 sm:p-6']) }}>
    @if ($title)
        <div class="flex items-start sm:items-center justify-between gap-4 pb-3.5 mb-4 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">
                    {{ $title }}
                </h3>
                @if ($subtitle)
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            @if (isset($actions))
                <div class="shrink-0">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
