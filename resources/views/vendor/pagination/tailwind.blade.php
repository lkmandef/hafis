@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Navigasi Halaman') }}" class="w-full">

        {{-- Tampilan Mobile (< sm): Tombol Sebelumnya & Berikutnya Gelap Elegan --}}
        <div class="flex items-center justify-between gap-3 sm:hidden w-full">
            {{-- Tombol Sebelumnya (Mobile) --}}
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

            {{-- Tombol Berikutnya (Mobile) --}}
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
        </div>

        {{-- Tampilan Desktop (>= sm): Navigasi Nomor Halaman Elegan --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="text-xs text-gray-500 leading-5">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-gray-900 font-mono">{{ $paginator->firstItem() }}</span>
                        sampai
                        <span class="font-semibold text-gray-900 font-mono">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari
                    <span class="font-semibold text-gray-900 font-mono">{{ $paginator->total() }}</span>
                    hasil
                </p>
            </div>

            <div class="flex items-center gap-1">
                {{-- Previous Page Arrow (Desktop) --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('Halaman sebelumnya') }}" class="p-2 min-h-[36px] min-w-[36px] inline-flex items-center justify-center text-gray-300 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed select-none opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="p-2 min-h-[36px] min-w-[36px] inline-flex items-center justify-center text-gray-700 bg-white hover:bg-gray-100 hover:text-gray-900 border border-gray-200 rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900" aria-label="{{ __('Halaman sebelumnya') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                {{-- Nomor-nomor Halaman --}}
                @foreach ($elements as $element)
                    {{-- Separator Titik Tiga (...) --}}
                    @if (is_string($element))
                        <span aria-disabled="true" class="px-3 py-1.5 min-h-[36px] min-w-[36px] inline-flex items-center justify-center text-xs font-medium text-gray-400 cursor-default select-none">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Link Nomor Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="px-3 py-1.5 min-h-[36px] min-w-[36px] inline-flex items-center justify-center text-xs font-bold text-white bg-gray-900 rounded-lg shadow-xs select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1.5 min-h-[36px] min-w-[36px] inline-flex items-center justify-center text-xs font-medium text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900" aria-label="{{ __('Buka halaman :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Arrow (Desktop) --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="p-2 min-h-[36px] min-w-[36px] inline-flex items-center justify-center text-gray-700 bg-white hover:bg-gray-100 hover:text-gray-900 border border-gray-200 rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900" aria-label="{{ __('Halaman berikutnya') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('Halaman berikutnya') }}" class="p-2 min-h-[36px] min-w-[36px] inline-flex items-center justify-center text-gray-300 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed select-none opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
