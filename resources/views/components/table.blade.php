@props([
    'headers' => [],
    'empty' => false,
    'emptyMessage' => 'Belum ada data untuk ditampilkan.'
])

<div class="w-full overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="w-full text-left text-xs sm:text-sm text-slate-800 border-collapse">
        @if (!empty($headers))
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-slate-100">
            @if ($empty)
                <tr>
                    <td colspan="{{ max(count($headers), 1) }}" class="px-6 py-10 text-center">
                        @if (isset($emptyState))
                            {{ $emptyState }}
                        @else
                            <div class="flex flex-col items-center justify-center text-center">
                                <p class="text-xs font-semibold text-slate-700">Tidak ada catatan data</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $emptyMessage }}</p>
                            </div>
                        @endif
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>
