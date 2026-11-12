@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Navigasi Halaman') }}" class="flex items-center justify-between gap-3 w-full">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 min-h-[44px] text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-xl cursor-not-allowed select-none opacity-60">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Sebelumnya</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 min-h-[44px] text-xs font-semibold text-white bg-gray-900 hover:bg-black rounded-xl shadow-xs transition-colors active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900" aria-label="{{ __('Halaman sebelumnya') }}">
                <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Sebelumnya</span>
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 min-h-[44px] text-xs font-semibold text-white bg-gray-900 hover:bg-black rounded-xl shadow-xs transition-colors active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900" aria-label="{{ __('Halaman berikutnya') }}">
                <span>Berikutnya</span>
                <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <span aria-disabled="true" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 min-h-[44px] text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-xl cursor-not-allowed select-none opacity-60">
                <span>Berikutnya</span>
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif
    </nav>
@endif
