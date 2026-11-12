<x-layouts.app title="HAFIS : Ikhtisar Pemeriksaan Bulanan APAR">
    <!-- Header Halaman -->
    <x-page-header
        title="Hasil Inspeksi APAR"
        description="Pemantauan status checklist dan hasil inspeksi fisik unit APAR operasional K3RS per bulan kalender.">
        <x-slot:actions>
            <a href="{{ route('scan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-medium rounded-lg text-white bg-[#111827] hover:bg-gray-800 transition min-h-[40px] focus:outline-none focus:ring-2 focus:ring-gray-900/20 cursor-pointer" 
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
        <div class="mb-4 sm:mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">&check;</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center cursor-pointer" aria-label="Tutup">&times;</button>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 sm:mb-6 p-4 rounded-xl border border-blue-200 bg-blue-50 text-blue-900 text-xs flex items-center justify-between gap-3" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">i</span>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-blue-700 hover:text-blue-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center cursor-pointer" aria-label="Tutup">&times;</button>
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
                'dot' => 'bg-red-500',
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
        <div class="flex items-center flex-nowrap gap-1.5 sm:gap-2 overflow-x-auto scrollbar-none py-1 -my-1 scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($statusItems as $item)
                @if($item['active'])
                    <span 
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-medium bg-[#111827] text-white shrink-0 min-h-[40px] cursor-default"
                        aria-current="page">
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-[11px] font-medium px-1.5 py-0.5 rounded bg-white/20 text-white leading-none">
                            {{ $item['count'] }}
                        </span>
                    </span>
                @else
                    <a 
                        href="{{ $buildStatusUrl($item['key']) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-medium text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-colors shrink-0 min-h-[40px] focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-[11px] font-medium px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 leading-none">
                            {{ $item['count'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- 3. Container Utama: Toolbar, Tabel, dan Status -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <!-- Toolbar Pencarian & Filter -->
        <div class="p-3.5 sm:p-4 border-b border-gray-200 bg-white">
            <form method="GET" action="{{ route('pemeriksaan.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 lg:gap-3">
                @if($statusPeriksa && $statusPeriksa !== 'semua')
                    <input type="hidden" name="status_periksa" value="{{ $statusPeriksa }}">
                @endif

                <!-- Pencarian Kode APAR atau Lokasi -->
                <div class="relative flex-1 min-w-0 sm:min-w-[200px] lg:min-w-[220px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $search }}" 
                        placeholder="Cari kode APAR atau lokasi..." 
                        class="w-full pl-9 pr-3 py-2 text-xs text-gray-900 placeholder-gray-400 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition min-h-[40px]"
                    />
                </div>

                <!-- Kontrol Filter Periode & Lokasi -->
                <div class="grid grid-cols-2 sm:flex items-center gap-2 shrink-0">
                    <!-- Filter Bulan -->
                    <div class="w-full sm:w-auto sm:min-w-[130px]">
                        <select 
                            name="bulan" 
                            aria-label="Filter Bulan" 
                            class="w-full px-3 py-2 text-xs font-medium text-gray-900 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none min-h-[40px] cursor-pointer"
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
                            class="w-full px-3 py-2 text-xs font-medium text-gray-900 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none min-h-[40px] cursor-pointer"
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
                        class="w-full px-3 py-2 text-xs text-gray-900 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none min-h-[40px] cursor-pointer truncate"
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
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-[#111827] text-white rounded-lg text-xs font-medium hover:bg-gray-800 transition min-h-[40px] focus:outline-none focus:ring-2 focus:ring-gray-900 cursor-pointer"
                    >
                        Cari
                    </button>

                    @if($search || $lokasiId || ($statusPeriksa && $statusPeriksa !== 'semua'))
                        <a 
                            href="{{ route('pemeriksaan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                            class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition min-h-[40px] border border-gray-200 sm:border-transparent"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Info Bar Hasil & Counter -->
        <div class="px-3.5 py-2.5 sm:px-4 sm:py-2.5 bg-gray-50/70 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500 font-medium">
            <div class="flex items-center gap-2">
                <span>Periode: <strong class="text-gray-900 font-semibold">{{ \App\Models\Pemeriksaan::NAMA_BULAN[$bulan] }} {{ $tahun }}</strong></span>
                @if($statusPeriksa && $statusPeriksa !== 'semua')
                    <span class="text-gray-300">•</span>
                    <span>Status: <strong class="text-gray-900 font-semibold">{{ $statusPeriksa === 'sudah' ? 'Sudah Diperiksa' : ($statusPeriksa === 'perlu_tindakan' ? 'Perlu Tindakan' : ($statusPeriksa === 'baik' ? 'Kondisi Baik' : 'Belum Diperiksa')) }}</strong></span>
                @endif
            </div>
            <span class="font-mono text-[11px] text-gray-500">
                @if(method_exists($apars, 'total'))
                    Menampilkan <strong class="text-gray-900 font-semibold">{{ $apars->total() > 0 ? $apars->firstItem() : 0 }}</strong> - <strong class="text-gray-900 font-semibold">{{ $apars->lastItem() ?? 0 }}</strong> dari <strong class="text-gray-900 font-semibold">{{ $apars->total() }}</strong> unit
                @else
                    Menampilkan <strong class="text-gray-900 font-semibold">{{ $apars->count() }}</strong> unit
                @endif
            </span>
        </div>

        <!-- Master APAR Monthly Checklist Mobile Cards (sm:hidden) -->
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($apars as $index => $apar)
                @php
                    $pemBulanIni = $apar->pemeriksaans->first();
                    $sudahDiperiksa = !is_null($pemBulanIni);
                @endphp
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <a href="{{ route('apar.show', $apar->id) }}" class="font-mono font-bold text-gray-900 text-sm hover:underline">
                            {{ $apar->kode }}
                        </a>
                        @if($sudahDiperiksa)
                            <div class="flex flex-col items-end gap-1">
                                @if($pemBulanIni->hasil_keseluruhan === \App\Models\Pemeriksaan::HASIL_BAIK)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Kondisi Baik
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                        Perlu Tindakan
                                    </span>
                                @endif
                                @if($pemBulanIni->tipe_penyelesaian === \App\Models\Pemeriksaan::TIPE_TERLAMBAT)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        Selesai Terlambat
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                Belum Diperiksa
                            </span>
                        @endif
                    </div>

                    <div class="bg-gray-50/70 rounded-lg p-2.5 space-y-1.5 text-xs border border-gray-100">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-gray-500 shrink-0">Lokasi:</span>
                            <div class="text-right">
                                <span class="font-medium text-gray-900 block">{{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}</span>
                                <span class="text-[11px] text-gray-500 block truncate max-w-[200px]">
                                    @if($apar->lokasi)
                                        {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                                    @else
                                        {{ $apar->ruangan?->nama ?? '-' }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-gray-200/50">
                            <span class="text-gray-500">Media / Kapasitas:</span>
                            <span class="font-medium text-gray-800">{{ $apar->jenis_media }} ({{ $apar->kapasitas }})</span>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-gray-200/50">
                            <span class="text-gray-500">Status Fisik:</span>
                            <x-badge :variant="$apar->status_badge_variant" :dot="true">
                                {{ $apar->status_label }}
                            </x-badge>
                        </div>
                    </div>

                    <!-- Direct Action Touch Target -->
                    <div class="pt-1">
                        @if($sudahDiperiksa)
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pemeriksaan.show', $pemBulanIni->id) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-800 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition min-h-[44px] shadow-2xs">
                                    Lihat Hasil
                                </a>
                                <a href="{{ route('pemeriksaan.edit', $pemBulanIni->pemeriksaan_induk_id ?? $pemBulanIni->id) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px]">
                                    Koreksi
                                </a>
                            </div>
                        @else
                            <a href="{{ route('pemeriksaan.create', ['apar_id' => $apar->id, 'periode_bulan' => $bulan, 'periode_tahun' => $tahun]) }}" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-xs font-semibold text-white bg-[#111827] hover:bg-gray-800 rounded-lg transition min-h-[44px] shadow-2xs">
                                <span>Periksa Sekarang</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-gray-400 text-xs">
                    Tidak ada data unit APAR yang sesuai dengan filter pencarian.
                </div>
            @endforelse
        </div>

        <!-- Master APAR Monthly Checklist Desktop Table (hidden sm:block) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full min-w-[880px] table-fixed text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/80 text-gray-500 font-medium uppercase tracking-wider text-[11px]">
                        <th scope="col" class="py-2.5 px-3 sm:px-4 w-12 text-center">No</th>
                        <th scope="col" class="py-2.5 px-3 sm:px-4 w-[160px]">Kode APAR</th>
                        <th scope="col" class="py-2.5 px-3 sm:px-4 min-w-[200px]">Lokasi K3RS</th>
                        <th scope="col" class="py-2.5 px-3 sm:px-4 w-[140px]">Media / Kapasitas</th>
                        <th scope="col" class="py-2.5 px-3 sm:px-4 w-[120px]">Status Tabung</th>
                        <th scope="col" class="py-2.5 px-3 sm:px-4 w-[170px] text-center">Status Periode {{ \App\Models\Pemeriksaan::NAMA_BULAN[$bulan] }}</th>
                        <th scope="col" class="py-2.5 px-3 sm:px-4 w-[160px] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($apars as $index => $apar)
                        @php
                            $pemBulanIni = $apar->pemeriksaans->first();
                            $sudahDiperiksa = !is_null($pemBulanIni);
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-2.5 px-3 sm:px-4 text-gray-400 font-mono text-[11px] text-center">{{ method_exists($apars, 'firstItem') ? ($apars->firstItem() + $index) : ($index + 1) }}</td>
                            <td class="py-2.5 px-3 sm:px-4 font-mono font-semibold text-gray-900">
                                <a href="{{ route('apar.show', $apar->id) }}" class="hover:underline hover:text-gray-600">
                                    {{ $apar->kode }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3 sm:px-4 text-gray-700 break-words">
                                <span class="font-medium block text-gray-900">{{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}</span>
                                <span class="text-[11px] text-gray-400">
                                    @if($apar->lokasi)
                                        {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                                    @else
                                        {{ $apar->ruangan?->nama ?? '-' }}
                                    @endif
                                </span>
                            </td>
                            <td class="py-2.5 px-3 sm:px-4 text-gray-600">
                                <span>{{ $apar->jenis_media }}</span>
                                <span class="block text-[11px] font-mono text-gray-400">{{ $apar->kapasitas }}</span>
                            </td>
                            <td class="py-2.5 px-3 sm:px-4">
                                <x-badge :variant="$apar->status_badge_variant" :dot="true">
                                    {{ $apar->status_label }}
                                </x-badge>
                            </td>
                            <td class="py-2.5 px-3 sm:px-4 text-center">
                                @if($sudahDiperiksa)
                                    <div class="flex flex-col items-center gap-0.5">
                                        @if($pemBulanIni->hasil_keseluruhan === \App\Models\Pemeriksaan::HASIL_BAIK)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-emerald-50/50 text-emerald-700 border border-emerald-200/80">
                                                Kondisi Baik
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-rose-50/50 text-rose-700 border border-rose-200/80">
                                                Perlu Tindakan
                                            </span>
                                        @endif
                                        @if($pemBulanIni->tipe_penyelesaian === \App\Models\Pemeriksaan::TIPE_TERLAMBAT)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                Selesai Terlambat
                                            </span>
                                        @endif
                                        <span class="block text-[10px] text-gray-400 font-mono">
                                            {{ $pemBulanIni->tanggal_pemeriksaan?->format('d/m/Y') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50/50 text-amber-700 border border-amber-200/80">
                                        Belum Diperiksa
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap">
                                @if($sudahDiperiksa)
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('pemeriksaan.show', $pemBulanIni->id) }}" class="px-2.5 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition min-h-[30px] inline-flex items-center">
                                            Lihat Hasil
                                        </a>
                                        <a href="{{ route('pemeriksaan.edit', $pemBulanIni->pemeriksaan_induk_id ?? $pemBulanIni->id) }}" class="px-2.5 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition min-h-[30px] inline-flex items-center">
                                            Koreksi
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('pemeriksaan.create', ['apar_id' => $apar->id, 'periode_bulan' => $bulan, 'periode_tahun' => $tahun]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-[#111827] rounded-lg hover:bg-gray-800 transition min-h-[30px]">
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
                            <td colspan="7" class="py-8 text-center text-gray-400 text-xs">
                                Tidak ada data unit APAR yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($apars, 'hasPages') && $apars->hasPages())
            <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
                <span>
                    Menampilkan <span class="font-semibold text-gray-900 font-mono">{{ $apars->firstItem() }}</span> - <span class="font-semibold text-gray-900 font-mono">{{ $apars->lastItem() }}</span> dari <span class="font-semibold text-gray-900 font-mono">{{ $apars->total() }}</span> unit APAR
                </span>
                <div>
                    {{ $apars->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
