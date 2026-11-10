<x-layouts.app title="HAFIS : Master Data APAR">
    <!-- 1. Page Header -->
    <x-page-header
        title="Master Data APAR"
        description="Pusat pencatatan data identitas, spesifikasi teknis, dan lokasi penempatan unit APAR rumah sakit.">
        <x-slot:actions>
            @php
                $isFiltered = ! empty($search) || ! empty($lokasiId) || ! empty($gedungId) || ! empty($lantaiId) || ! empty($jenisMedia) || (request()->has('status') && request('status') !== 'semua' && request('status') !== 'aktif');
            @endphp

            @if($isFiltered)
                <x-button variant="secondary" href="{{ route('apar.cetak_semua_qr', request()->query()) }}" target="_blank" title="Cetak {{ $apars->total() }} unit APAR hasil pencarian / filter saat ini (Format 4 stiker per lembar A4)">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak QR Terfilter ({{ $apars->total() }})</span>
                </x-button>
            @else
                <x-button variant="secondary" href="{{ route('apar.cetak_semua_qr', request()->query()) }}" target="_blank" title="Cetak label stiker QR massal (4 stiker per lembar kertas A4)">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak Semua QR (A4)</span>
                </x-button>
            @endif

            <x-button variant="primary" href="{{ route('apar.create') }}">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah APAR</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Notifikasi Sukses / Feedback -->
    @if(session('success'))
        <div class="mb-4 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @php
        $statusFilter = $status ?? ($search ? 'semua' : 'aktif');

        $statusItems = [
            [
                'key' => 'semua',
                'label' => 'Semua',
                'count' => $totalApar,
                'dot' => null,
                'active' => $statusFilter === 'semua',
            ],
            [
                'key' => 'aktif',
                'label' => 'Unit Aktif',
                'count' => $totalAktif,
                'dot' => 'bg-emerald-500',
                'active' => $statusFilter === 'aktif',
            ],
            [
                'key' => 'baik',
                'label' => 'Baik',
                'count' => $totalBaik,
                'dot' => 'bg-emerald-500',
                'active' => $statusFilter === 'baik',
            ],
            [
                'key' => 'perhatian',
                'label' => 'Perlu Perhatian',
                'count' => $totalPerhatian,
                'dot' => 'bg-amber-500',
                'active' => $statusFilter === 'perhatian',
            ],
            [
                'key' => 'rusak',
                'label' => 'Rusak',
                'count' => $totalRusak,
                'dot' => 'bg-red-500',
                'active' => $statusFilter === 'rusak',
            ],
            [
                'key' => 'sudah_diganti',
                'label' => 'Sudah Diganti',
                'count' => $totalSudahDiganti,
                'dot' => 'bg-gray-400',
                'active' => $statusFilter === 'sudah_diganti',
            ],
            [
                'key' => 'tidak_aktif',
                'label' => 'Tidak Aktif',
                'count' => $totalTidakAktif,
                'dot' => null,
                'active' => $statusFilter === 'tidak_aktif',
            ],
        ];

        $statusLabelMap = [
            'semua' => 'Semua Status',
            'aktif' => 'Unit Aktif',
            'baik' => 'Kondisi Baik',
            'perhatian' => 'Perlu Perhatian',
            'rusak' => 'Kondisi Rusak',
            'sudah_diganti' => 'Sudah Diganti',
            'tidak_aktif' => 'Tidak Aktif',
        ];
        $activeStatusLabel = $statusLabelMap[$statusFilter] ?? ucfirst($statusFilter);

        $buildStatusUrl = function ($statusKey) use ($search, $lokasiId, $gedungId, $lantaiId, $jenisMedia) {
            return route('apar.index', array_filter([
                'status' => $statusKey,
                'q' => $search,
                'lokasi_id' => $lokasiId,
                'gedung_id' => $gedungId,
                'lantai_id' => $lantaiId,
                'jenis_media' => $jenisMedia,
            ], fn ($v) => ! is_null($v) && $v !== ''));
        };
    @endphp

    <!-- 2. Compact Status & Filter Tabs Row -->
    <div class="mb-4 sm:mb-5">
        <div class="flex items-center flex-nowrap gap-1.5 sm:gap-2 overflow-x-auto scrollbar-none py-1 -my-1 scroll-smooth scroll-fade-r sm:[mask-image:none] pr-6 sm:pr-0" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($statusItems as $item)
                @if($item['active'])
                    <span 
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-medium bg-gray-900 text-white border border-gray-900 shrink-0 min-h-[40px] cursor-default"
                        aria-current="page">
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-xs font-semibold px-1.5 py-0.5 rounded bg-white/20 text-white leading-none tabular-nums">
                            {{ $item['count'] }}
                        </span>
                    </span>
                @else
                    <a 
                        href="{{ $buildStatusUrl($item['key']) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-medium text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 transition-colors shrink-0 min-h-[40px] focus:outline-none focus:ring-1 focus:ring-gray-900">
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-xs font-medium px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 leading-none tabular-nums">
                            {{ $item['count'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Container Utama: Toolbar, Info Bar, Tabel, dan Pagination -->
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden mb-6">
        <!-- 3. Toolbar Pencarian & Filter Ala Cal.com -->
        <div class="p-4 border-b border-gray-200 bg-white">
            <form method="GET" action="{{ route('apar.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                <!-- Status Filter Preserved via Tab -->
                <input type="hidden" name="status" value="{{ $statusFilter }}" />

                <!-- Pencarian Kode APAR, QR, Merek, atau Lokasi -->
                <div class="relative flex-1 min-w-0 sm:min-w-[220px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari kode APAR, QR, merek, atau lokasi..."
                        class="w-full pl-9 pr-3 py-2 text-xs text-gray-900 placeholder-gray-400 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none transition min-h-[40px]" />
                </div>

                <!-- Kontrol Filter Dropdown: Lokasi K3RS -->
                <div class="w-full sm:w-auto sm:min-w-[180px] lg:max-w-[230px] shrink-0">
                    <select 
                        name="lokasi_id" 
                        id="filter_lokasi_id"
                        aria-label="Filter Lokasi K3RS"
                        class="w-full px-3 py-2 text-xs text-gray-900 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none min-h-[40px] cursor-pointer truncate">
                        <option value="">Semua Lokasi K3RS</option>
                        @foreach($lokasis as $lok)
                            <option value="{{ $lok->id }}" {{ (string) $lokasiId === (string) $lok->id ? 'selected' : '' }}>
                                {{ $lok->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Cari & Reset -->
                <div class="flex items-center gap-2 shrink-0">
                    <button 
                        type="submit" 
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-medium hover:bg-gray-800 transition min-h-[40px] focus:outline-none focus:ring-1 focus:ring-gray-900 cursor-pointer">
                        Cari
                    </button>

                    @if($search || $lokasiId || $gedungId || $lantaiId || ($status && $status !== 'aktif') || $jenisMedia)
                        <a 
                            href="{{ route('apar.index', ['status' => 'semua']) }}" 
                            class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition min-h-[40px] border border-gray-200 sm:border-transparent">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Info Bar Hasil & Filter Aktif -->
        <div class="px-4 py-2.5 bg-gray-50/80 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500 font-medium">
            <div class="flex flex-wrap items-center gap-2">
                <span>Filter: <strong class="text-gray-900 font-semibold">{{ $activeStatusLabel }}</strong></span>
                @if($lokasiId)
                    <span class="text-gray-300">&bull;</span>
                    <span>Lokasi: <strong class="text-gray-900 font-semibold">{{ $lokasis->firstWhere('id', $lokasiId)?->nama ?? 'Lokasi Terpilih' }}</strong></span>
                @endif
                @if($search)
                    <span class="text-gray-300">&bull;</span>
                    <span>Pencarian: <strong class="text-gray-900 font-semibold">&ldquo;{{ $search }}&rdquo;</strong></span>
                @endif
            </div>
            <div class="text-xs text-gray-500">
                Total: <strong class="text-gray-900 font-semibold font-mono">{{ $apars->total() }}</strong> unit APAR
            </div>
        </div>

        <!-- 5. Tampilan Kartu Mobile untuk Petugas Lapangan (F-07/F-08) -->
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($apars as $apar)
                <div class="p-4 space-y-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <a href="{{ route('apar.show', $apar->id) }}" class="font-mono font-semibold text-gray-900 text-sm hover:underline">
                                {{ $apar->kode }}
                            </a>
                            @if($apar->nomor_register)
                                <span class="text-xs text-gray-500 font-mono ml-1.5">Reg: {{ $apar->nomor_register }}</span>
                            @endif
                        </div>
                        <x-badge :variant="$apar->status_badge_variant" :dot="true">
                            {{ $apar->status_label }}
                        </x-badge>
                    </div>

                    @if($apar->is_replaced)
                        <div class="text-[11px] text-red-700 font-medium">
                            Diganti: <a href="{{ route('apar.show', $apar->replaced_by_apar_id) }}" class="font-mono font-semibold underline">{{ $apar->aparPengganti?->kode }}</a>
                        </div>
                    @elseif($apar->is_replacement)
                        <div class="text-[11px] text-blue-700 font-medium">
                            Pengganti: <a href="{{ route('apar.show', $apar->replaces_apar_id) }}" class="font-mono font-semibold underline">{{ $apar->aparDigantikan?->kode }}</a>
                        </div>
                    @endif

                    <div class="text-xs text-gray-600 space-y-1">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-gray-500 shrink-0">Lokasi:</span>
                            <div class="text-right truncate">
                                <span class="font-medium text-gray-900">{{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}</span>
                                <span class="text-gray-500 block text-[11px]">
                                    @if($apar->lokasi)
                                        {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                                    @else
                                        {{ $apar->gedung?->nama ?? '-' }} &bull; {{ $apar->lantai?->nama ?? '-' }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-gray-500 shrink-0">Media:</span>
                            <span class="text-gray-900 font-medium text-right">
                                {{ $apar->jenis_media }} <span class="font-mono text-gray-600">({{ $apar->kapasitas }})</span>
                            </span>
                        </div>

                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-gray-500 shrink-0">Merek:</span>
                            <span class="text-gray-900 text-right {{ ! $apar->merek ? 'text-gray-400 italic' : '' }}">
                                {{ $apar->merek ?: '-' }}
                            </span>
                        </div>

                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-gray-500 shrink-0">Inspeksi:</span>
                            <span class="text-right">
                                @if($apar->tanggal_pemeriksaan_terakhir)
                                    <span class="font-mono text-gray-900">{{ $apar->tanggal_pemeriksaan_terakhir->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">Belum ada riwayat</span>
                                @endif
                                @if($apar->tanggal_kadaluarsa)
                                    <span class="text-[11px] text-gray-500 font-mono block">Exp: {{ $apar->tanggal_kadaluarsa->format('d/m/Y') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <a href="{{ route('apar.show', $apar->id) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition min-h-[40px]">
                            Detail
                        </a>
                        @if($apar->can_be_replaced)
                            <a href="{{ route('apar.ganti', $apar->id) }}" 
                               class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-amber-800 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition min-h-[40px]">
                                Ganti
                            </a>
                        @endif
                        <a href="{{ route('apar.edit', $apar->id) }}" 
                           class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition min-h-[40px]">
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center">
                    <div class="max-w-sm mx-auto flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                            </svg>
                        </div>
                        @if($search || $lokasiId || $gedungId || $lantaiId || ($status && $status !== 'aktif') || $jenisMedia)
                            <h4 class="text-sm font-semibold text-gray-900">Tidak ada unit APAR yang cocok</h4>
                            <p class="text-xs text-gray-500 mt-1 mb-4">
                                Tidak ditemukan data unit APAR dengan parameter pencarian atau filter yang Anda pilih.
                            </p>
                            <a href="{{ route('apar.index', ['status' => 'semua']) }}" class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition min-h-[40px]">
                                Reset Filter
                            </a>
                        @else
                            <h4 class="text-sm font-semibold text-gray-900">Belum ada data unit APAR</h4>
                            <p class="text-xs text-gray-500 mt-1 mb-4">
                                Unit APAR rumah sakit belum didaftarkan ke sistem. Mulai tambahkan data unit APAR sesuai lokasi fisik gedung.
                            </p>
                            <x-button variant="primary" href="{{ route('apar.create') }}">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Tambah APAR Baru</span>
                            </x-button>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- 5. Tabel Data Master APAR (Desktop & Tablet) -->
        <div class="hidden sm:block w-full overflow-x-auto">
            <table class="w-full table-fixed text-left text-xs text-gray-900 border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-gray-500 text-xs font-medium uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-3 py-3 w-[13%] whitespace-nowrap">Kode APAR</th>
                        <th scope="col" class="px-3 py-3 w-[25%]">
                            <div class="leading-tight">Lokasi</div>
                            <div class="text-[10px] text-gray-400 font-normal normal-case tracking-normal">Gedung &bull; Lantai &bull; Ruang</div>
                        </th>
                        <th scope="col" class="px-3 py-3 w-[13%] whitespace-normal leading-tight">
                            Media &amp; Kapasitas
                        </th>
                        <th scope="col" class="px-3 py-3 w-[11%] whitespace-normal leading-tight">
                            Merek / Tipe
                        </th>
                        <th scope="col" class="px-3 py-3 w-[10%] whitespace-nowrap">
                            Status
                        </th>
                        <th scope="col" class="px-3 py-3 w-[12%] whitespace-normal leading-tight">
                            Pemeriksaan Terakhir
                        </th>
                        <th scope="col" class="px-2 py-3 w-[16%] text-right whitespace-nowrap">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($apars as $apar)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <!-- Kode APAR & Register (JetBrains Mono) -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <a href="{{ route('apar.show', $apar->id) }}" class="font-mono font-semibold text-gray-900 hover:underline">
                                    {{ $apar->kode }}
                                </a>
                                @if($apar->nomor_register)
                                    <div class="text-[11px] text-gray-500 font-mono truncate">Reg: {{ $apar->nomor_register }}</div>
                                @endif
                                <div class="text-[10px] text-gray-400 font-mono truncate" title="{{ $apar->qr_code }}">QR: {{ $apar->qr_code }}</div>
                                @if($apar->is_replaced)
                                    <div class="text-[10px] text-red-700 font-medium mt-0.5 truncate">
                                        Diganti: <a href="{{ route('apar.show', $apar->replaced_by_apar_id) }}" class="font-mono font-semibold underline">{{ $apar->aparPengganti?->kode }}</a>
                                    </div>
                                @elseif($apar->is_replacement)
                                    <div class="text-[10px] text-blue-700 font-medium mt-0.5 truncate">
                                        Pengganti: <a href="{{ route('apar.show', $apar->replaces_apar_id) }}" class="font-mono font-semibold underline">{{ $apar->aparDigantikan?->kode }}</a>
                                    </div>
                                @endif
                            </td>

                            <!-- Lokasi Berjenjang -->
                            <td class="px-3 py-2.5 break-words">
                                <div class="font-medium text-gray-900 leading-snug">
                                    {{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500 leading-snug">
                                    @if($apar->lokasi)
                                        {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                                    @else
                                        {{ $apar->gedung?->nama ?? '-' }} &bull; {{ $apar->lantai?->nama ?? '-' }}
                                    @endif
                                </div>
                                @if($apar->posisi_detail)
                                    <div class="text-xs text-gray-400 italic mt-0.5 line-clamp-1" title="{{ $apar->posisi_detail }}">
                                        {{ $apar->posisi_detail }}
                                    </div>
                                @endif
                            </td>

                            <!-- Media & Kapasitas (Kapasitas JetBrains Mono) -->
                            <td class="px-3 py-2.5">
                                <div class="text-gray-900 font-medium leading-tight">{{ $apar->jenis_media }}</div>
                                <div class="font-mono text-xs text-gray-500 mt-0.5">{{ $apar->kapasitas }}</div>
                            </td>

                            <!-- Merek & Tipe -->
                            <td class="px-3 py-2.5">
                                <div class="text-gray-900 leading-tight truncate {{ ! $apar->merek ? 'text-gray-400 italic' : '' }}" title="{{ $apar->merek }}">{{ $apar->merek ?: 'Belum terdata' }}</div>
                                <div class="text-xs text-gray-500 truncate mt-0.5">{{ $apar->tipe_model ?: '-' }}</div>
                            </td>

                            <!-- Status Kondisi (Cal.com Pastel Badge) -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <x-badge :variant="$apar->status_badge_variant" :dot="true">
                                    {{ $apar->status_label }}
                                </x-badge>
                            </td>

                            <!-- Pemeriksaan Terakhir & Kadaluarsa (JetBrains Mono) -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                @if($apar->tanggal_pemeriksaan_terakhir)
                                    <span class="font-mono text-gray-900">
                                        {{ $apar->tanggal_pemeriksaan_terakhir->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-xs">Belum ada riwayat</span>
                                @endif
                                @if($apar->tanggal_kadaluarsa)
                                    <div class="text-xs text-gray-500 font-mono mt-0.5">
                                        Exp: {{ $apar->tanggal_kadaluarsa->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-2 py-2.5 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1 justify-end">
                                    <a href="{{ route('apar.show', $apar->id) }}" 
                                       class="inline-flex items-center justify-center px-2 py-1 text-[11px] font-medium rounded-md text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition min-h-[28px]">
                                        Detail
                                    </a>
                                    @if($apar->can_be_replaced)
                                        <a href="{{ route('apar.ganti', $apar->id) }}" 
                                           class="inline-flex items-center justify-center px-2 py-1 text-[11px] font-medium rounded-md text-amber-800 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition min-h-[28px]" title="Ganti Unit APAR">
                                            Ganti
                                        </a>
                                    @endif
                                    <a href="{{ route('apar.edit', $apar->id) }}" 
                                       class="inline-flex items-center justify-center px-2 py-1 text-[11px] font-medium rounded-md text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition min-h-[28px]">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                        </svg>
                                    </div>
                                    @if($search || $lokasiId || $gedungId || $lantaiId || ($status && $status !== 'aktif') || $jenisMedia)
                                        <h4 class="text-sm font-semibold text-gray-900">Tidak ada unit APAR yang cocok</h4>
                                        <p class="text-xs text-gray-500 mt-1 mb-4">
                                            Tidak ditemukan data unit APAR dengan parameter pencarian atau filter yang Anda pilih.
                                        </p>
                                        <a href="{{ route('apar.index', ['status' => 'semua']) }}" class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition min-h-[40px]">
                                            Reset Filter
                                        </a>
                                    @else
                                        <h4 class="text-sm font-semibold text-gray-900">Belum ada data unit APAR</h4>
                                        <p class="text-xs text-gray-500 mt-1 mb-4">
                                            Unit APAR rumah sakit belum didaftarkan ke sistem. Mulai tambahkan data unit APAR sesuai lokasi fisik gedung.
                                        </p>
                                        <x-button variant="primary" href="{{ route('apar.create') }}">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            <span>Tambah APAR Baru</span>
                                        </x-button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 6. Pagination Links -->
        @if($apars->hasPages())
            <div class="p-4 sm:p-5 border-t border-gray-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-xs text-gray-500">
                    Menampilkan <span class="font-semibold text-gray-900 font-mono">{{ $apars->firstItem() }}</span> - <span class="font-semibold text-gray-900 font-mono">{{ $apars->lastItem() }}</span> dari <span class="font-semibold text-gray-900 font-mono">{{ $apars->total() }}</span> unit APAR
                </span>
                <div>
                    {{ $apars->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>