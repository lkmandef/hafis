<x-layouts.app title="HAFIS : Ikhtisar Pemeriksaan Bulanan APAR">
    <!-- Header Halaman -->
    <x-page-header
        title="Hasil Inspeksi APAR"
        description="Pemantauan status checklist dan hasil inspeksi fisik unit APAR operasional K3RS per bulan kalender.">
        <x-slot:actions>
            <a href="{{ route('scan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition shadow-2xs min-h-[42px] focus:outline-none focus:ring-2 focus:ring-slate-900/20" 
               title="Scan QR Code APAR untuk periode ini">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span>Scan APAR</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="mb-4 sm:mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">&check;</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center cursor-pointer" aria-label="Tutup">&times;</button>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 sm:mb-6 p-4 rounded-xl border border-sky-200 bg-sky-50 text-sky-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-sky-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">i</span>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-sky-700 hover:text-sky-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center cursor-pointer" aria-label="Tutup">&times;</button>
        </div>
    @endif

    @php
        $statusFilter = in_array($statusPeriksa, ['sudah', 'belum', 'perlu_tindakan']) ? $statusPeriksa : 'semua';

        $statusItems = [
            [
                'key' => 'semua',
                'label' => 'Semua',
                'count' => $totalWajib,
                'dot' => null,
                'active' => $statusFilter === 'semua',
            ],
            [
                'key' => 'sudah',
                'label' => 'Sudah Diperiksa',
                'count' => $totalSudah,
                'dot' => 'bg-emerald-500',
                'active' => $statusFilter === 'sudah',
            ],
            [
                'key' => 'belum',
                'label' => 'Belum Diperiksa',
                'count' => $totalBelum,
                'dot' => 'bg-amber-500',
                'active' => $statusFilter === 'belum',
            ],
            [
                'key' => 'perlu_tindakan',
                'label' => 'Perlu Tindakan',
                'count' => $totalPerluTindakan,
                'dot' => 'bg-rose-500',
                'active' => $statusFilter === 'perlu_tindakan',
            ],
        ];

        $buildStatusUrl = function ($statusKey) use ($bulan, $tahun, $search, $lokasiId) {
            return route('pemeriksaan.index', array_filter([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'status_periksa' => ($statusKey === 'semua' || empty($statusKey)) ? null : $statusKey,
                'q' => $search,
                'lokasi_id' => $lokasiId,
            ], fn ($v) => ! is_null($v) && $v !== ''));
        };
    @endphp

    <!-- 2. Compact Status & Filter Tabs Row -->
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
                        href="{{ $buildStatusUrl($item['key']) }}"
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

    <!-- 3. Container Utama: Toolbar, Tabel, dan Status -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <!-- Toolbar Pencarian & Filter -->
        <div class="p-3.5 sm:p-4 border-b border-slate-200 bg-white">
            <form method="GET" action="{{ route('pemeriksaan.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 lg:gap-3">
                @if($statusPeriksa && $statusPeriksa !== 'semua')
                    <input type="hidden" name="status_periksa" value="{{ $statusPeriksa }}">
                @endif

                <!-- Pencarian Kode APAR atau Lokasi -->
                <div class="relative flex-1 min-w-0 sm:min-w-[200px] lg:min-w-[220px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $search }}" 
                        placeholder="Cari kode APAR atau lokasi..." 
                        class="w-full pl-9 pr-3 py-2 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-900/20 focus:outline-none transition min-h-[42px]"
                    />
                </div>

                <!-- Kontrol Filter Periode & Lokasi -->
                <div class="grid grid-cols-2 sm:flex items-center gap-2 shrink-0">
                    <!-- Filter Bulan -->
                    <div class="w-full sm:w-auto sm:min-w-[130px]">
                        <select 
                            name="bulan" 
                            aria-label="Filter Bulan" 
                            class="w-full px-3 py-2 text-xs font-semibold text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-900/20 focus:outline-none min-h-[42px] cursor-pointer"
                        >
                            @foreach(\App\Models\Pemeriksaan::NAMA_BULAN as $num => $nama)
                                <option value="{{ $num }}" {{ $bulan === $num ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div class="w-full sm:w-auto sm:min-w-[95px]">
                        <select 
                            name="tahun" 
                            aria-label="Filter Tahun" 
                            class="w-full px-3 py-2 text-xs font-semibold text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-900/20 focus:outline-none min-h-[42px] cursor-pointer"
                        >
                            @for($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 2; $y++)
                                <option value="{{ $y }}" {{ $tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Filter Lokasi -->
                <div class="w-full sm:w-auto sm:min-w-[170px] lg:max-w-[210px] shrink-0">
                    <select 
                        name="lokasi_id" 
                        aria-label="Filter Lokasi" 
                        class="w-full px-3 py-2 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-900/20 focus:outline-none min-h-[42px] cursor-pointer truncate"
                    >
                        <option value="">Semua Lokasi</option>
                        @foreach($lokasis as $loc)
                            <option value="{{ $loc->id }}" {{ (string)$lokasiId === (string)$loc->id ? 'selected' : '' }}>{{ $loc->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Cari & Reset -->
                <div class="flex items-center gap-2 shrink-0">
                    <button 
                        type="submit" 
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-semibold hover:bg-slate-800 transition duration-150 ease-in-out min-h-[42px] focus:outline-none focus:ring-2 focus:ring-slate-900 cursor-pointer shadow-2xs"
                    >
                        Cari
                    </button>

                    @if($search || $lokasiId || ($statusPeriksa && $statusPeriksa !== 'semua'))
                        <a 
                            href="{{ route('pemeriksaan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                            class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition min-h-[42px] border border-slate-200/80 sm:border-transparent"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Info Bar Hasil & Counter -->
        <div class="px-3.5 py-2.5 sm:px-4 sm:py-2.5 bg-slate-50/70 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500 font-medium">
            <div class="flex items-center gap-2">
                <span>Periode: <strong class="text-slate-800 font-semibold">{{ \App\Models\Pemeriksaan::NAMA_BULAN[$bulan] }} {{ $tahun }}</strong></span>
                @if($statusPeriksa && $statusPeriksa !== 'semua')
                    <span class="text-slate-300">•</span>
                    <span>Status: <strong class="text-slate-800 font-semibold">{{ $statusPeriksa === 'sudah' ? 'Sudah Diperiksa' : ($statusPeriksa === 'perlu_tindakan' ? 'Perlu Tindakan' : ($statusPeriksa === 'baik' ? 'Kondisi Baik' : 'Belum Diperiksa')) }}</strong></span>
                @endif
            </div>
            <span class="font-mono text-[11px] text-slate-500">
                Menampilkan <strong class="text-slate-800 font-bold">{{ $apars->count() }}</strong> unit
            </span>
        </div>

        <!-- Master APAR Monthly Checklist Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Kode APAR</th>
                        <th class="py-3 px-4">Lokasi K3RS</th>
                        <th class="py-3 px-4">Media / Kapasitas</th>
                        <th class="py-3 px-4">Status Tabung</th>
                        <th class="py-3 px-4 text-center">Status Periode {{ \App\Models\Pemeriksaan::NAMA_BULAN[$bulan] }}</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($apars as $index => $apar)
                        @php
                            $pemBulanIni = $apar->pemeriksaans->first();
                            $sudahDiperiksa = !is_null($pemBulanIni);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('apar.show', $apar->id) }}" class="hover:underline hover:text-sky-700">
                                    {{ $apar->kode }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                <span class="font-semibold block text-slate-900">{{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400">
                                    @if($apar->lokasi)
                                        {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                                    @else
                                        {{ $apar->ruangan?->nama ?? '-' }}
                                    @endif
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                <span>{{ $apar->jenis_media }}</span>
                                <span class="block text-[11px] font-mono text-slate-400">{{ $apar->kapasitas }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <x-badge :variant="$apar->status_badge_variant" :dot="true">
                                    {{ $apar->status_label }}
                                </x-badge>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($sudahDiperiksa)
                                    @if($pemBulanIni->hasil_keseluruhan === \App\Models\Pemeriksaan::HASIL_BAIK)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Kondisi Baik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Perlu Tindakan
                                        </span>
                                    @endif
                                    <span class="block text-[10px] text-slate-400 mt-0.5 font-mono">
                                        {{ $pemBulanIni->tanggal_pemeriksaan?->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Belum Diperiksa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                @if($sudahDiperiksa)
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('pemeriksaan.show', $pemBulanIni->id) }}" class="px-2.5 py-1 text-[11px] font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition shadow-2xs">
                                            Lihat Hasil
                                        </a>
                                        <a href="{{ route('pemeriksaan.edit', $pemBulanIni->id) }}" class="px-2.5 py-1 text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-lg hover:bg-sky-100 transition shadow-2xs">
                                            Koreksi
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('pemeriksaan.create', ['apar_id' => $apar->id, 'periode_bulan' => $bulan, 'periode_tahun' => $tahun]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition shadow-2xs">
                                        <span>Periksa</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada data unit APAR yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($apars, 'hasPages') && $apars->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
                {{ $apars->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
