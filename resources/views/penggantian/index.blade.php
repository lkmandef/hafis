<x-layouts.app title="HAFIS : Riwayat Penggantian Unit APAR">
    <!-- 1. Header Halaman & Action -->
    <x-page-header
        title="Riwayat Penggantian Unit APAR"
        description="Log audit dan penelusuran pergantian unit tabung pemadam api rumah sakit.">
        <x-slot:actions>
            <a href="{{ route('apar.index') }}" 
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-colors min-h-[42px] focus:outline-none focus:ring-2 focus:ring-slate-900/20 shadow-2xs">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Master Data APAR</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Alert Sukses / Info -->
    @if(session('success'))
        <div class="mb-4 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center cursor-pointer" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @php
        $statusItems = [
            [
                'key' => '',
                'label' => 'Semua',
                'count' => $stats['total'],
                'dot' => null,
                'active' => empty(request('alasan')),
            ],
            [
                'key' => 'kerusakan',
                'label' => 'Akibat Kerusakan',
                'count' => $stats['rusak'],
                'dot' => 'bg-rose-500',
                'active' => in_array(request('alasan'), ['kerusakan', 'rusak']),
            ],
            [
                'key' => 'kadaluarsa',
                'label' => 'Kedaluwarsa Media',
                'count' => $stats['kadaluarsa'],
                'dot' => 'bg-amber-500',
                'active' => in_array(request('alasan'), ['kadaluarsa', 'kedaluwarsa']),
            ],
            [
                'key' => 'peremajaan',
                'label' => 'Peremajaan Unit',
                'count' => $stats['peremajaan'],
                'dot' => 'bg-blue-500',
                'active' => in_array(request('alasan'), ['peremajaan', 'penggantian_unit']),
            ],
        ];

        $buildFilterUrl = function ($alasanKey) {
            return route('penggantian.index', array_filter([
                'alasan' => $alasanKey,
                'q' => request('q'),
            ], fn ($v) => ! is_null($v) && $v !== ''));
        };
    @endphp

    <!-- 2. Filter Tabs Alasan Penggantian (Pola Konsisten Tindak Lanjut / Jadwal Inspeksi) -->
    <div class="mb-4 sm:mb-5">
        <div class="flex items-center flex-nowrap gap-1.5 sm:gap-2 overflow-x-auto py-1 -my-1 scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($statusItems as $item)
                @if($item['active'])
                    <span 
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-900 text-white border border-slate-900 shadow-2xs shrink-0 min-h-[42px] cursor-default"
                        aria-current="page">
                        @if($item['dot'])
                            <span class="w-2 h-2 rounded-full {{ $item['dot'] }} ring-2 ring-white/30 shrink-0"></span>
                        @endif
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-[11px] font-bold px-1.5 py-0.5 rounded bg-white/20 text-white leading-none">
                            {{ $item['count'] }}
                        </span>
                    </span>
                @else
                    <a 
                        href="{{ $buildFilterUrl($item['key']) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-medium text-slate-700 bg-white border border-slate-200/90 hover:bg-slate-50 hover:text-slate-900 hover:border-slate-300 transition-colors shrink-0 min-h-[42px] focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                        @if($item['dot'])
                            <span class="w-2 h-2 rounded-full {{ $item['dot'] }} shrink-0"></span>
                        @endif
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-[11px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 leading-none">
                            {{ $item['count'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- 3. Container Utama: Toolbar & Tabel Daftar Penggantian -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <!-- Toolbar Pencarian -->
        <div class="p-3.5 sm:p-4 border-b border-slate-200 bg-white">
            <form method="GET" action="{{ route('penggantian.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                @if(request('alasan'))
                    <input type="hidden" name="alasan" value="{{ request('alasan') }}">
                @endif

                <!-- Search Input -->
                <div class="relative flex-1 min-w-0 sm:min-w-[240px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kode APAR lama, kode baru, petugas, atau alasan..."
                        class="w-full pl-9 pr-3 py-2 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-900/20 focus:outline-none transition min-h-[42px]" />
                </div>

                <!-- Tombol Cari & Reset -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" 
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition duration-150 ease-in-out shadow-2xs min-h-[42px] focus:outline-none focus:ring-2 focus:ring-slate-900 cursor-pointer">
                        <span>Cari</span>
                    </button>

                    @if(request()->hasAny(['q', 'alasan']))
                        <a href="{{ route('penggantian.index') }}" 
                           class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition min-h-[42px] border border-slate-200/80 sm:border-transparent">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Tabel Daftar Riwayat Penggantian APAR -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Tanggal</th>
                        <th class="px-4 py-3">APAR Lama (Dinonaktifkan)</th>
                        <th class="px-4 py-3">APAR Baru (Aktif)</th>
                        <th class="px-4 py-3">Alasan Penggantian</th>
                        <th class="px-4 py-3">Lokasi Penempatan</th>
                        <th class="px-4 py-3 whitespace-nowrap">Petugas</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                    @forelse($penggantians as $p)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Tanggal Penggantian -->
                            <td class="px-4 py-3.5 font-mono font-medium text-slate-900 whitespace-nowrap tabular-nums">
                                {{ $p->tanggal_penggantian->format('d/m/Y') }}
                            </td>

                            <!-- APAR Lama (Dinonaktifkan) -->
                            <td class="px-4 py-3.5">
                                @if($p->aparLama)
                                    <div class="space-y-0.5">
                                        <a href="{{ route('apar.show', $p->aparLama->id) }}" class="font-mono font-bold text-slate-900 hover:underline inline-block">
                                            {{ $p->aparLama->kode }}
                                        </a>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[11px] text-slate-500">{{ $p->aparLama->jenis_media }}</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200/90 font-medium">Nonaktif</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Unit lama terhapus</span>
                                @endif
                            </td>

                            <!-- APAR Baru (Aktif) -->
                            <td class="px-4 py-3.5">
                                @if($p->aparBaru)
                                    <div class="space-y-0.5">
                                        <a href="{{ route('apar.show', $p->aparBaru->id) }}" class="font-mono font-bold text-slate-900 hover:underline inline-block">
                                            {{ $p->aparBaru->kode }}
                                        </a>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[11px] text-slate-500">{{ $p->aparBaru->jenis_media }}</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200/90 font-semibold">Aktif</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Unit baru</span>
                                @endif
                            </td>

                            <!-- Alasan Penggantian -->
                            <td class="px-4 py-3.5 max-w-xs">
                                <span class="font-semibold text-slate-900 block leading-snug">{{ $p->alasan_label }}</span>
                                @if($p->alasan_keterangan)
                                    <span class="text-slate-500 text-[11px] block mt-0.5 leading-snug whitespace-normal">{{ $p->alasan_keterangan }}</span>
                                @endif
                            </td>

                            <!-- Lokasi Penempatan -->
                            <td class="px-4 py-3.5 max-w-xs">
                                <span class="text-slate-700 block truncate" title="{{ $p->aparBaru?->lokasi_lengkap ?? $p->aparLama?->lokasi_lengkap ?? '-' }}">
                                    {{ $p->aparBaru?->lokasi_lengkap ?? $p->aparLama?->lokasi_lengkap ?? '-' }}
                                </span>
                            </td>

                            <!-- Petugas -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-medium text-slate-800">
                                {{ $p->petugas_nama }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <a href="{{ route('penggantian.show', $p->id) }}" 
                                   class="px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition min-h-[36px] inline-flex items-center">
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </div>
                                    @if(request('q') || request('alasan'))
                                        <h4 class="text-sm font-bold text-slate-900">Tidak ada riwayat yang cocok</h4>
                                        <p class="text-xs text-slate-500 mt-1 mb-4">
                                            Tidak ditemukan data penggantian unit dengan filter atau pencarian saat ini.
                                        </p>
                                        <a href="{{ route('penggantian.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition min-h-[42px]">
                                            Reset Filter
                                        </a>
                                    @else
                                        <h4 class="text-sm font-bold text-slate-900">Belum ada riwayat penggantian APAR</h4>
                                        <p class="text-xs text-slate-500 mt-1">
                                            Riwayat pergantian unit lama ke unit baru akan tercatat secara otomatis di sini saat dilakukan penggantian.
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penggantians->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $penggantians->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
