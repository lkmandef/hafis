@props([
    'title' => null,
    'subtitle' => null
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-200/80 shadow-xs p-5 sm:p-6']) }}>
    @if ($title)
        <div class="flex items-start sm:items-center justify-between gap-4 pb-4 mb-5 border-b border-gray-200">
            <div>
                <h2 class="text-sm sm:text-base font-semibold text-gray-900 tracking-tight">
                    {{ $title }}
                </h2>
                @if ($subtitle)
                    <p class="text-xs text-gray-500 mt-0.5">
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
