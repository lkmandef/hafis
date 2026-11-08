@props([
    'title',
    'description' => null
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 sm:pb-5 mb-5 sm:mb-6 border-b border-gray-200']) }}>
    <div>
        <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">
            {{ $title }}
        </h1>
        @if ($description)
            <p class="text-sm text-gray-500 mt-1">
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
