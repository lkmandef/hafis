@props([
    'headers' => [],
    'empty' => false,
    'emptyMessage' => 'Belum ada data untuk ditampilkan.'
])

<div {{ $attributes->merge(['class' => 'w-full overflow-x-auto rounded-xl border border-gray-200 bg-white overflow-hidden']) }}>
    <table class="w-full text-left text-sm text-gray-900 border-collapse">
        @if (!empty($headers))
            <thead class="bg-gray-50/80 border-b border-gray-200 text-gray-500 text-xs font-medium uppercase tracking-wider">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col" class="px-4 py-3.5 whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-gray-200">
            @if ($empty)
                <tr>
                    <td colspan="{{ max(count($headers), 1) }}" class="px-6 py-10 text-center">
                        @if (isset($emptyState))
                            {{ $emptyState }}
                        @else
                            <div class="flex flex-col items-center justify-center text-center">
                                <p class="text-sm font-medium text-gray-900">Tidak ada catatan data</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $emptyMessage }}</p>
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
