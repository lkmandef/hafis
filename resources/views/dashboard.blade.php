<x-layouts.app title="HAFIS : Dashboard Monitoring APAR">
    <!-- 1. Header Halaman & Quick Actions -->
    <x-page-header
        title="Dashboard Monitoring APAR"
        description="Ringkasan kelaikan unit, aktivitas inspeksi berkala, dan penanganan pemeliharaan APAR rumah sakit.">
        <x-slot:actions>
            <div class="w-full sm:w-auto grid grid-cols-2 sm:flex sm:items-center gap-2 sm:gap-2.5">
                <x-button variant="primary" href="{{ route('scan.index') }}" class="order-1 sm:order-3 col-span-2 sm:col-span-1 w-full sm:w-auto min-h-[44px]">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Scan APAR</span>
                </x-button>

                <x-button variant="secondary" href="{{ route('apar.create') }}" class="order-2 sm:order-1 w-full sm:w-auto min-h-[44px]">
                    <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah APAR</span>
                </x-button>

                <x-button variant="secondary" href="{{ route('laporan.index') }}" class="order-3 sm:order-2 w-full sm:w-auto min-h-[44px]">
                    <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Pusat Laporan</span>
                </x-button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- 2. Hero Section: 1 Card Lebar Terpadu (Single Integrated Hero Monitoring Panel) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-4 sm:p-6 mb-6">
        <!-- Top Metrics & Fleet Summary -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-gray-100">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 mb-1">
                    Kesiapsiagaan Proteksi Kebakaran Rumah Sakit
                </p>
                <div class="flex items-baseline gap-3 flex-wrap">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-emerald-800 font-mono tabular-nums">
                        {{ $kondisiPersen['baik'] }}%
                    </h2>
                    <span class="text-sm sm:text-base font-bold text-gray-900">
                        Kelaikan Operasional Armada APAR
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                    Total <a href="{{ route('apar.index', ['status' => 'aktif']) }}" class="font-semibold text-gray-900 underline-offset-2 hover:underline"><span class="font-mono font-bold">{{ $summaryStats['apar_aktif'] }}</span> APAR Terpasang</a> dari <span class="font-mono font-medium">{{ $summaryStats['apar_total'] }}</span> unit terdaftar, tersebar di <strong class="font-semibold text-gray-800">{{ $summaryStats['total_gedung'] }}</strong> gedung aktif RSUD dr. H. Moch. Ansari Saleh.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('laporan.apar') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 transition-colors shadow-2xs focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Laporan Kelaikan Lengkap</span>
                </a>
            </div>
        </div>

        <!-- Multi-Segment Visual Progress Bar -->
        <div class="py-4 border-b border-gray-100">
            <div class="flex items-center justify-between text-xs text-gray-600 mb-2 font-medium">
                <span>Distribusi Status Tabung Aktual</span>
                <span class="font-mono font-semibold text-gray-800">{{ $summaryStats['apar_total'] }} Unit Terdata</span>
            </div>
            <div class="w-full h-2.5 bg-gray-100 border border-gray-200/60 rounded-full overflow-hidden flex gap-0.5 p-0.5"
                 role="progressbar"
                 aria-label="Distribusi Kondisi Kelaikan APAR"
                 aria-valuenow="{{ $kondisiPersen['baik'] }}"
                 aria-valuemin="0"
                 aria-valuemax="100">
                @if($summaryStats['apar_total'] > 0)
                    @if($kondisiPersen['baik'] > 0)
                        <div style="width: {{ $kondisiPersen['baik'] }}%" class="bg-emerald-600 h-full rounded-full motion-safe:transition-[width] motion-safe:duration-500" title="Baik: {{ $kondisiCounts['baik'] }} unit ({{ $kondisiPersen['baik'] }}%)"></div>
                    @endif
                    @if($kondisiPersen['perhatian'] > 0)
                        <div style="width: {{ $kondisiPersen['perhatian'] }}%" class="bg-amber-500 h-full rounded-full motion-safe:transition-[width] motion-safe:duration-500" title="Perlu Perhatian: {{ $kondisiCounts['perhatian'] }} unit ({{ $kondisiPersen['perhatian'] }}%)"></div>
                    @endif
                    @if($kondisiPersen['rusak'] > 0)
                        <div style="width: {{ $kondisiPersen['rusak'] }}%" class="bg-rose-500 h-full rounded-full motion-safe:transition-[width] motion-safe:duration-500" title="Rusak: {{ $kondisiCounts['rusak'] }} unit ({{ $kondisiPersen['rusak'] }}%)"></div>
                    @endif
                    @if($kondisiPersen['tidak_aktif'] > 0)
                        <div style="width: {{ $kondisiPersen['tidak_aktif'] }}%" class="bg-gray-400 h-full rounded-full motion-safe:transition-[width] motion-safe:duration-500" title="Tidak Aktif: {{ $kondisiCounts['tidak_aktif'] }} unit ({{ $kondisiPersen['tidak_aktif'] }}%)"></div>
                    @endif
                @else
                    <div class="w-full bg-gray-200 h-full rounded-full"></div>
                @endif
            </div>
        </div>

        <!-- 4 Interactive Filter Pillars -->
        <div class="pt-4 grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
            <!-- 1. Kondisi Baik (Emerald Semantic) -->
            <a href="{{ route('apar.index', ['status' => 'baik']) }}"
               class="group p-3.5 rounded-xl bg-emerald-50/20 hover:bg-emerald-50/50 border border-emerald-200/70 border-l-[3.5px] border-l-emerald-600 transition-all flex flex-col justify-between focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 shadow-2xs min-h-[110px]">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-xs font-bold text-gray-800 group-hover:text-emerald-950">Kondisi Baik</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold font-mono bg-emerald-100/80 text-emerald-800 border border-emerald-200">
                        {{ $kondisiPersen['baik'] }}%
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-extrabold tracking-tight text-emerald-800 font-mono tabular-nums">
                        {{ $kondisiCounts['baik'] }}
                    </span>
                    <span class="text-xs text-emerald-900/70 font-semibold font-sans">unit</span>
                </div>
                <span class="text-[11px] text-gray-500 mt-1 block">Kelaikan terjaga siap pakai</span>
            </a>

            <!-- 2. Mendekati Expired (Amber Semantic) -->
            <a href="{{ route('apar.index', ['status' => 'perhatian']) }}"
               class="group p-3.5 rounded-xl {{ $summaryStats['apar_perhatian'] > 0 ? 'bg-amber-50/40 border-amber-300' : 'bg-gray-50/30 border-gray-200' }} border-l-[3.5px] border-l-amber-500 hover:bg-amber-50/60 hover:border-amber-300 transition-all flex flex-col justify-between focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 shadow-2xs min-h-[110px]">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-xs font-bold text-gray-800 group-hover:text-amber-950">Mendekati Expired</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold font-mono {{ $summaryStats['apar_perhatian'] > 0 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                        {{ $kondisiPersen['perhatian'] }}%
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-extrabold tracking-tight font-mono tabular-nums {{ $summaryStats['apar_perhatian'] > 0 ? 'text-amber-800' : 'text-gray-700' }}">
                        {{ $summaryStats['apar_perhatian'] }}
                    </span>
                    <span class="text-xs text-gray-500 font-semibold font-sans">unit</span>
                </div>
                <span class="text-[11px] text-gray-500 mt-1 block">Perlu isi ulang media tabung</span>
            </a>

            <!-- 3. Kondisi Rusak (Rose Semantic) -->
            <a href="{{ route('apar.index', ['status' => 'rusak']) }}"
               class="group p-3.5 rounded-xl {{ $summaryStats['apar_rusak'] > 0 ? 'bg-rose-50/40 border-rose-300' : 'bg-gray-50/30 border-gray-200' }} border-l-[3.5px] border-l-rose-500 hover:bg-rose-50/60 hover:border-rose-300 transition-all flex flex-col justify-between focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-600 shadow-2xs min-h-[110px]">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-xs font-bold text-gray-800 group-hover:text-rose-950">Kondisi Rusak</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold font-mono {{ $summaryStats['apar_rusak'] > 0 ? 'bg-rose-100 text-rose-900 border border-rose-300' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                        {{ $kondisiPersen['rusak'] }}%
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-extrabold tracking-tight font-mono tabular-nums {{ $summaryStats['apar_rusak'] > 0 ? 'text-rose-700' : 'text-gray-700' }}">
                        {{ $summaryStats['apar_rusak'] }}
                    </span>
                    <span class="text-xs text-gray-500 font-semibold font-sans">unit</span>
                </div>
                <span class="text-[11px] text-gray-500 mt-1 block">Tidak laik butuh perbaikan</span>
            </a>

            <!-- 4. Total APAR Terpasang (Slate Anchor Semantic) -->
            <a href="{{ route('apar.index', ['status' => 'aktif']) }}"
               class="group p-3.5 rounded-xl bg-slate-50/50 hover:bg-slate-100/60 border border-slate-200/90 border-l-[3.5px] border-l-slate-800 transition-all flex flex-col justify-between focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 shadow-2xs min-h-[110px]">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-xs font-bold text-gray-900">Total Terpasang</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold font-mono bg-slate-200/70 text-slate-800 border border-slate-300/60">
                        {{ $summaryStats['total_gedung'] }} Gedung
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 font-mono tabular-nums">
                        {{ $summaryStats['apar_aktif'] }}
                    </span>
                    <span class="text-xs text-slate-600 font-semibold font-sans">unit</span>
                </div>
                <span class="text-[11px] text-gray-500 mt-1 block">Semua unit aktif operasional</span>
            </a>
        </div>
    </div>

    <!-- 3. Section Khusus: Unit APAR Memerlukan Perhatian (Alert Banner Prioritas K3RS) -->
    <div class="mb-6">
        @if($aparPerluPerhatian->count() > 0)
            <div class="bg-white rounded-xl border {{ $summaryStats['apar_rusak'] > 0 ? 'border-red-300 bg-red-50/20' : 'border-amber-300 bg-amber-50/20' }} shadow-xs p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-200/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg {{ $summaryStats['apar_rusak'] > 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800' }} flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-sm sm:text-base font-bold text-gray-900 tracking-tight">
                                    Unit APAR Memerlukan Perhatian
                                </h2>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold font-mono {{ $summaryStats['apar_rusak'] > 0 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-900' }}">
                                    {{ $aparPerluPerhatian->count() }} Unit Butuh Tindakan
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Seluruh tabung berstatus Rusak atau Mendekati Expired yang diprioritaskan untuk penanganan teknis dan evaluasi K3RS.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('laporan.temuan') }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900 transition-colors whitespace-nowrap focus:outline-none focus-visible:underline self-start sm:self-auto">
                        Lihat Semua Temuan
                    </a>
                </div>

                <!-- Mobile Card View (sm:hidden) -->
                <div class="sm:hidden divide-y divide-gray-100 -mx-4 -mb-4">
                    @foreach($aparPerluPerhatian as $apar)
                        @php
                            $latestTl = $apar->tindakLanjuts->first();
                        @endphp
                        <div class="p-4 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <a href="{{ route('apar.show', $apar->id) }}" class="font-mono font-bold text-gray-900 text-sm hover:underline focus:outline-none focus-visible:underline">
                                        {{ $apar->kode }}
                                    </a>
                                    <span class="text-xs text-gray-500 font-mono ml-1.5">{{ $apar->nomor_register ?: '-' }}</span>
                                </div>
                                @if($apar->status === 'rusak')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                                        Rusak
                                    </span>
                                @elseif($apar->status === 'perhatian')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        Perlu Perhatian
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $apar->status_label }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs text-gray-600 space-y-1">
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-gray-500">Lokasi:</span>
                                    <span class="font-medium text-gray-900 text-right truncate">
                                        {{ $apar->lokasi_lengkap }}
                                    </span>
                                </div>
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-gray-500">Media:</span>
                                    <span class="text-gray-900 text-right font-medium">
                                        {{ $apar->jenis_media }} <span class="font-mono text-gray-600">({{ $apar->kapasitas }})</span>
                                    </span>
                                </div>
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-gray-500">Penanganan:</span>
                                    <span>
                                        @if(!$latestTl)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                                Belum Ada Tindak Lanjut
                                            </span>
                                        @elseif($latestTl->status === 'selesai')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Selesai Ditangani
                                            </span>
                                        @elseif($latestTl->status === 'dalam_proses')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                Dalam Proses
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                                Menunggu Tindakan
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2 flex items-center gap-2">
                                <a href="{{ route('apar.show', $apar->id) }}"
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[44px]">
                                    Detail APAR
                                </a>

                                @if(!$latestTl || $latestTl->status === 'selesai')
                                    <a href="{{ route('tindak-lanjut.create', ['apar_id' => $apar->id]) }}"
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-lg text-white bg-gray-900 hover:bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[44px]">
                                        + Tindak Lanjut
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table View (hidden sm:block) -->
                <div class="hidden sm:block overflow-x-auto -mx-6 -mb-6 mt-4">
                    <table class="w-full text-left text-xs text-gray-900">
                        <thead class="bg-gray-50/80 border-y border-gray-200 text-xs font-medium uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="py-3 pl-6 pr-4">Kode APAR</th>
                                <th class="py-3 px-4">Media & Kapasitas</th>
                                <th class="py-3 px-4">Lokasi Penempatan</th>
                                <th class="py-3 px-4">Kondisi Tabung</th>
                                <th class="py-3 px-4">Status Penanganan</th>
                                <th class="py-3 pl-4 pr-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($aparPerluPerhatian as $apar)
                                @php
                                    $latestTl = $apar->tindakLanjuts->first();
                                @endphp
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-3 pl-6 pr-4 whitespace-nowrap">
                                        <a href="{{ route('apar.show', $apar->id) }}" class="font-mono font-bold text-gray-900 hover:underline">
                                            {{ $apar->kode }}
                                        </a>
                                        <span class="block text-[11px] font-normal text-gray-500 font-mono">{{ $apar->nomor_register ?: '-' }}</span>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="font-semibold text-gray-900">{{ $apar->jenis_media }}</span>
                                        <span class="block text-xs text-gray-500 font-mono">{{ $apar->kapasitas }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-gray-900">{{ $apar->lokasiNama }}</div>
                                        <div class="text-[11px] text-gray-500">
                                            {{ $apar->lokasiLengkap }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if($apar->status === 'rusak')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                Rusak
                                            </span>
                                        @elseif($apar->status === 'perhatian')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                                Perlu Perhatian
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $apar->status_label }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if(!$latestTl)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                                Belum Ada Tindak Lanjut
                                            </span>
                                        @elseif($latestTl->status === 'selesai')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Selesai Ditangani
                                            </span>
                                        @elseif($latestTl->status === 'dalam_proses')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                Dalam Proses
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                                Menunggu Tindakan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 pl-4 pr-6 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('apar.show', $apar->id) }}"
                                               class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-medium rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[38px]">
                                                Detail APAR
                                            </a>

                                            @if(!$latestTl || $latestTl->status === 'selesai')
                                                <a href="{{ route('tindak-lanjut.create', ['apar_id' => $apar->id]) }}"
                                                   class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-white bg-gray-900 hover:bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[38px]">
                                                    + Tindak Lanjut
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Reassuring All-Clear Banner saat tidak ada APAR bermasalah -->
            <div class="bg-emerald-50/50 rounded-xl border border-emerald-200/80 p-4 sm:p-5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-emerald-950">Semua Unit APAR dalam Kondisi Baik</p>
                        <p class="text-xs text-emerald-800/80 mt-0.5">Tidak ditemukan tabung berstatus rusak atau memerlukan tindakan.</p>
                    </div>
                </div>
                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                    Standar K3RS Terpenuhi
                </span>
            </div>
        @endif
    </div>

    <!-- 4. Bagian Aktivitas Inspeksi Bulanan & Jadwal Berkala (Cakupan Penuh 1 s/d Akhir Bulan) -->
    <div class="bg-white rounded-xl border border-gray-200/90 shadow-xs p-5 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-gray-200">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-sm sm:text-base font-semibold text-gray-900 tracking-tight">
                        Hasil Inspeksi Bulanan
                    </h2>
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                        Periode {{ $inspeksiBulanan['periode_label'] }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Siklus inspeksi aktif: <strong class="font-medium text-gray-800">{{ $inspeksiBulanan['rentang_label'] }}</strong> (mencakup seluruh tanggal bulan berjalan, sisa <strong class="font-semibold text-gray-900">{{ $inspeksiBulanan['sisa_hari'] }}</strong> hari kalender).
                </p>
            </div>
            <a href="{{ route('pemeriksaan.index', ['bulan' => $inspeksiBulanan['bulan'], 'tahun' => $inspeksiBulanan['tahun']]) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900 transition-colors whitespace-nowrap focus:outline-none focus-visible:underline self-start sm:self-auto">
                Rekap Inspeksi
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom 1 & 2: Progress Kepatuhan Inspeksi Bulanan -->
            <div class="lg:col-span-2 space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/80">
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="text-gray-700 font-semibold">Kepatuhan Inspeksi Seluruh Gedung</span>
                        <span class="font-bold text-gray-900 font-mono text-sm">{{ $inspeksiBulanan['progress_pct'] }}%</span>
                    </div>
                    <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden"
                         role="progressbar"
                         aria-label="Kepatuhan inspeksi bulanan"
                         aria-valuenow="{{ $inspeksiBulanan['progress_pct'] }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                        <div class="bg-gray-900 h-full rounded-full motion-safe:transition-[width] motion-safe:duration-500 motion-reduce:transition-none" style="width: {{ $inspeksiBulanan['progress_pct'] }}%"></div>
                    </div>
                    <div class="mt-3 text-xs text-gray-600 flex items-center justify-between flex-wrap gap-2">
                        <span>Sudah Diperiksa: <strong class="text-gray-900 font-mono font-bold">{{ $inspeksiBulanan['sudah_diperiksa'] }}</strong> dari <strong class="font-mono text-gray-900">{{ $inspeksiBulanan['total_target'] }}</strong> APAR</span>
                        <span>Tersisa: <strong class="text-amber-800 font-mono font-bold">{{ $inspeksiBulanan['belum_diperiksa'] }}</strong> APAR</span>
                    </div>
                </div>

                <!-- Evaluasi Temuan Fisik Bulan Berjalan -->
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3.5 rounded-lg border border-gray-200/80 bg-white shadow-2xs">
                        <div class="text-[11px] font-semibold text-gray-600 uppercase tracking-wider">
                            Kondisi Baik
                        </div>
                        <span class="text-xl sm:text-2xl font-bold text-gray-900 font-mono tabular-nums mt-1 block">{{ $inspeksiBulanan['baik'] }}</span>
                        <span class="text-[11px] text-gray-500 mt-0.5 block">Kelaikan terjaga tanpa temuan</span>
                    </div>
                    <div class="p-3.5 rounded-lg border border-gray-200/80 bg-white shadow-2xs">
                        <div class="text-[11px] font-semibold text-gray-600 uppercase tracking-wider">
                            Perlu Tindakan
                        </div>
                        <span class="text-xl sm:text-2xl font-bold text-gray-900 font-mono tabular-nums mt-1 block">{{ $inspeksiBulanan['perlu_tindakan'] }}</span>
                        <span class="text-[11px] text-gray-500 mt-0.5 block">Temuan fisik lapangan</span>
                    </div>
                </div>
            </div>

            <!-- Kolom 3: Status Jadwal Berkala Terkait -->
            <div class="p-4 bg-gray-50/70 rounded-xl border border-gray-200/80 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 pb-2 border-b border-gray-200">
                        <span class="text-xs font-semibold text-gray-700">Jadwal Berkala K3RS</span>
                        @if($jadwalData)
                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $jadwalData['model']->status_label }}
                            </span>
                        @endif
                    </div>

                    @if($jadwalData)
                        <div class="mt-3">
                            <a href="{{ route('jadwal.show', $jadwalData['model']->id) }}" class="font-semibold text-sm text-gray-900 hover:underline block truncate focus:outline-none focus-visible:underline" title="{{ $jadwalData['model']->nama }}">
                                {{ $jadwalData['model']->nama }}
                            </a>
                            <div class="mt-2 text-xs text-gray-600 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span>Target Terdaftar:</span>
                                    <strong class="font-mono text-gray-900">{{ $jadwalData['total_target'] }}</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Sudah Diperiksa:</span>
                                    <strong class="font-mono text-gray-900">{{ $jadwalData['sudah_diperiksa'] }}</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Progres Jadwal:</span>
                                    <strong class="font-mono text-gray-900">{{ $jadwalData['progress_pct'] }}%</strong>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-6 text-center text-gray-500">
                            <p class="text-xs">Tidak ada jadwal inspeksi aktif</p>
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-gray-200 mt-4">
                    <a href="{{ route('pemeriksaan.index', ['bulan' => $inspeksiBulanan['bulan'], 'tahun' => $inspeksiBulanan['tahun']]) }}"
                       class="w-full inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[40px] shadow-2xs">
                        Lihat Rekap Pemeriksaan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Bagian Aktivitas Pemeriksaan & Tindak Lanjut Terbaru (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Kolom Kiri: Ringkasan Pemeriksaan Terbaru -->
        <x-card>
            <x-slot:title>
                <span>Pemeriksaan Terbaru</span>
            </x-slot:title>
            <x-slot:subtitle>
                Log verifikasi fisik terkini ({{ $pemeriksaanStats['bulan_ini'] }} pemeriksaan pada {{ $summaryStats['periode_bulan'] }}).
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('laporan.pemeriksaan') }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 rounded">
                    Semua Pemeriksaan
                </a>
            </x-slot:actions>

            {{-- Mobile List View (sm:hidden) --}}
            <div class="sm:hidden divide-y divide-gray-100 -mx-5 -mb-5">
                @forelse($pemeriksaanTerbaru as $p)
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="font-mono font-bold text-sm text-gray-900">{{ $p->apar?->kode ?? '-' }}</span>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    {{ $p->pemeriksa_nama ?: 'Petugas K3RS' }} &bull; {{ $p->apar?->gedung?->nama ?? '-' }}
                                </div>
                            </div>
                            @if($p->hasil_keseluruhan === 'baik')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                                    Baik
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                                    Perlu Tindakan
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="font-mono text-gray-500 text-[11px]">
                                {{ $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d/m/Y H:i') . ' WITA' : '-' }}
                            </span>
                            <a href="{{ route('pemeriksaan.show', $p->id) }}"
                               class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[44px]">
                                Checklist
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500 px-4">
                        <p class="text-sm font-semibold text-gray-900">Belum ada pemeriksaan tercatat</p>
                        <p class="text-xs text-gray-500 mt-1">Mulai pemeriksaan APAR melalui menu Scan APAR atau Jadwal Inspeksi.</p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View (hidden sm:block) --}}
            <div class="hidden sm:block overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="w-full min-w-[480px] text-left text-xs text-gray-900">
                    <thead class="bg-gray-50/80 border-y border-gray-200 text-[11px] font-medium uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="py-2.5 pl-5 sm:pl-6 pr-3">Waktu</th>
                            <th class="py-2.5 px-3">APAR & Pemeriksa</th>
                            <th class="py-2.5 px-3 text-center">Hasil</th>
                            <th class="py-2.5 pl-3 pr-5 sm:pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pemeriksaanTerbaru as $p)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-2.5 pl-5 sm:pl-6 pr-3 whitespace-nowrap font-mono text-gray-600 text-xs">
                                    {{ $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d/m/Y') : '-' }}
                                    <span class="block text-[11px] text-gray-500 font-mono">{{ $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('H:i') : '' }} WITA</span>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="font-mono font-bold text-gray-900 block">{{ $p->apar?->kode ?? '-' }}</span>
                                    <span class="block text-[11px] text-gray-500 truncate max-w-[110px] sm:max-w-[150px]" title="{{ $p->pemeriksa_nama }} • {{ $p->apar?->gedung?->nama ?? '-' }}">
                                        {{ $p->pemeriksa_nama ?: 'Petugas K3RS' }} • {{ $p->apar?->gedung?->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    @if($p->hasil_keseluruhan === 'baik')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Baik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Perlu Tindakan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 pl-3 pr-5 sm:pr-6 text-right whitespace-nowrap">
                                    <a href="{{ route('pemeriksaan.show', $p->id) }}"
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[38px]">
                                        Checklist
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500">
                                    <p class="text-sm font-semibold text-gray-900">Belum ada pemeriksaan tercatat</p>
                                    <p class="text-xs text-gray-500 mt-1">Mulai pemeriksaan APAR melalui menu Scan APAR atau Jadwal Inspeksi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Kolom Kanan: Tindak Lanjut & Pemeliharaan Terkini -->
        <x-card>
            <x-slot:title>
                <span>Tindak Lanjut & Pemeliharaan</span>
            </x-slot:title>
            <x-slot:subtitle>
                Aktivitas perbaikan, penggantian suku cadang, dan pengisian ulang media APAR.
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('tindak-lanjut.index') }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 rounded">
                    Semua Tindak Lanjut
                </a>
            </x-slot:actions>

            {{-- Mobile List View (sm:hidden) --}}
            <div class="sm:hidden divide-y divide-gray-100 -mx-5 -mb-5">
                @forelse($tindakLanjutTerbaru as $tl)
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <span class="font-mono font-bold text-sm text-gray-900">{{ $tl->apar?->kode ?? '-' }}</span>
                                <div class="text-xs text-gray-700 mt-0.5 line-clamp-2">
                                    <span class="font-semibold text-gray-900">{{ $tl->jenis_label }}:</span> {{ $tl->uraian_tindakan ?: '-' }}
                                </div>
                            </div>
                            @if($tl->status === 'selesai')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                                    Selesai
                                </span>
                            @elseif($tl->status === 'dalam_proses')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                    Proses
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                                    Pending
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[11px] text-gray-500 font-mono">
                                {{ $tl->tanggal_tindakan ? $tl->tanggal_tindakan->format('d/m/Y') : '-' }} &bull; {{ $tl->petugas_pelaksana }}
                            </span>
                            <a href="{{ route('tindak-lanjut.show', $tl->id) }}"
                               class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[44px]">
                                Rincian
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500 px-4">
                        <p class="text-sm font-semibold text-gray-900">Belum ada catatan tindak lanjut</p>
                        <p class="text-xs text-gray-500 mt-1">Belum ada riwayat pengerjaan pemeliharaan yang tercatat pada periode ini.</p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View (hidden sm:block) --}}
            <div class="hidden sm:block overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="w-full min-w-[500px] text-left text-xs text-gray-900">
                    <thead class="bg-gray-50/80 border-y border-gray-200 text-[11px] font-medium uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="py-2.5 pl-5 sm:pl-6 pr-3">Tgl</th>
                            <th class="py-2.5 px-3">APAR & Tindakan</th>
                            <th class="py-2.5 px-3">Teknisi</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 pl-3 pr-5 sm:pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($tindakLanjutTerbaru as $tl)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-2.5 pl-5 sm:pl-6 pr-3 whitespace-nowrap font-mono text-gray-600 text-xs">
                                    {{ $tl->tanggal_tindakan ? $tl->tanggal_tindakan->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="font-mono font-bold text-gray-900 block">{{ $tl->apar?->kode ?? '-' }}</span>
                                    <span class="block text-[11px] text-gray-500 truncate max-w-[140px] sm:max-w-[170px]" title="{{ $tl->uraian_tindakan }}">
                                        {{ $tl->jenis_label }}: {{ $tl->uraian_tindakan ?: '-' }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="font-semibold text-gray-900 block truncate max-w-[90px] sm:max-w-[110px]" title="{{ $tl->petugas_pelaksana }}">
                                        {{ $tl->petugas_pelaksana }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    @if($tl->status === 'selesai')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Selesai
                                        </span>
                                    @elseif($tl->status === 'dalam_proses')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            Proses
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 pl-3 pr-5 sm:pr-6 text-right whitespace-nowrap">
                                    <a href="{{ route('tindak-lanjut.show', $tl->id) }}"
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[38px]">
                                        Rincian
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">
                                    <p class="text-sm font-semibold text-gray-900">Belum ada catatan tindak lanjut</p>
                                    <p class="text-xs text-gray-500 mt-1">Belum ada riwayat pengerjaan pemeliharaan yang tercatat pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <!-- 6. Ringkasan Lokasi Gedung & Histori Penggantian APAR (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kolom Kiri: Ringkasan Lokasi Gedung -->
        <x-card>
            <x-slot:title>
                <span>Ringkasan Per Lokasi Gedung</span>
            </x-slot:title>
            <x-slot:subtitle>
                Sebaran ketersediaan dan status fisik tabung APAR per area gedung rumah sakit.
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('apar.index') }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 rounded">
                    Semua Lokasi
                </a>
            </x-slot:actions>

            {{-- Mobile List View (sm:hidden) --}}
            <div class="sm:hidden divide-y divide-gray-100 -mx-5 -mb-5">
                @forelse($gedungRingkasan as $g)
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="font-bold text-sm text-gray-900 block">{{ $g['nama'] }}</span>
                                <span class="text-[11px] font-mono text-gray-500">{{ $g['kode'] }}</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-semibold bg-gray-100 text-gray-800">
                                {{ $g['total'] }} Unit
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <div class="flex items-center gap-3 text-xs">
                                <span class="inline-flex items-center gap-1 font-mono text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600" aria-hidden="true"></span>
                                    <span class="font-bold">{{ $g['baik'] }}</span> Baik
                                </span>
                                @if($g['perhatian'] + $g['rusak'] > 0)
                                    <span class="inline-flex items-center gap-1 font-mono text-rose-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600" aria-hidden="true"></span>
                                        <span class="font-bold">{{ $g['perhatian'] + $g['rusak'] }}</span> Rusak/Perhatian
                                    </span>
                                @else
                                    <span class="text-gray-500 font-mono text-[11px]">0 Rusak</span>
                                @endif
                            </div>
                            <a href="{{ route('apar.index', ['gedung_id' => $g['id']]) }}"
                               class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[44px]">
                                Filter
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500 px-4">
                        <p class="text-sm font-semibold text-gray-900">Belum ada master gedung</p>
                        <p class="text-xs text-gray-500 mt-1">Kelola struktur gedung melalui modul Lokasi.</p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View (hidden sm:block) --}}
            <div class="hidden sm:block overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="w-full text-left text-xs text-gray-900">
                    <thead class="bg-gray-50/80 border-y border-gray-200 text-[11px] font-medium uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="py-2.5 px-3">Gedung</th>
                            <th class="py-2.5 px-3 text-center">Total Unit</th>
                            <th class="py-2.5 px-3 text-center">Baik</th>
                            <th class="py-2.5 px-3 text-center">Perhatian / Rusak</th>
                            <th class="py-2.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($gedungRingkasan as $g)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-2.5 px-3 font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $g['nama'] }}
                                    <span class="block text-[11px] font-normal text-gray-500 font-mono">{{ $g['kode'] }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold text-gray-900 tabular-nums">
                                    {{ $g['total'] }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold text-emerald-800 tabular-nums">
                                    {{ $g['baik'] }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold tabular-nums">
                                    @if($g['perhatian'] + $g['rusak'] > 0)
                                        <span class="text-rose-700">{{ $g['perhatian'] + $g['rusak'] }}</span>
                                    @else
                                        <span class="text-gray-500">0</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <a href="{{ route('apar.index', ['gedung_id' => $g['id']]) }}"
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[38px]">
                                        Filter Gedung
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">
                                    <p class="text-sm font-semibold text-gray-900">Belum ada master gedung</p>
                                    <p class="text-xs text-gray-500 mt-1">Kelola struktur gedung melalui modul Lokasi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Kolom Kanan: Penggantian APAR Terbaru -->
        <x-card>
            <x-slot:title>
                <span>Penggantian APAR Terbaru</span>
            </x-slot:title>
            <x-slot:subtitle>
                Histori mutasi pergantian unit lama ke unit baru.
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('penggantian.index') }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 rounded">
                    Semua Penggantian
                </a>
            </x-slot:actions>

            {{-- Mobile List View (sm:hidden) --}}
            <div class="sm:hidden divide-y divide-gray-100 -mx-5 -mb-5">
                @forelse($penggantianTerbaru as $penggantian)
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-1.5 font-mono text-sm">
                                    <span class="text-gray-500 line-through">{{ $penggantian->aparLama?->kode ?? '-' }}</span>
                                    <span class="text-gray-400" aria-hidden="true">&rarr;</span>
                                    <span class="font-bold text-emerald-800">{{ $penggantian->aparBaru?->kode ?? '-' }}</span>
                                </div>
                                <span class="block text-[11px] text-gray-500 mt-0.5">Petugas: {{ $penggantian->petugas_nama }}</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-800 shrink-0">
                                {{ $penggantian->alasan_label }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="font-mono text-gray-500 text-[11px]">
                                {{ $penggantian->tanggal_penggantian ? $penggantian->tanggal_penggantian->format('d/m/Y') : '-' }}
                            </span>
                            <a href="{{ route('penggantian.show', $penggantian->id) }}"
                               class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[44px]">
                                Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500 px-4">
                        <p class="text-sm font-semibold text-gray-900">Belum ada riwayat penggantian unit</p>
                        <p class="text-xs text-gray-500 mt-1">Seluruh tabung masih beroperasi dengan unit awal pemasangan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View (hidden sm:block) --}}
            <div class="hidden sm:block overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="w-full text-left text-xs text-gray-900">
                    <thead class="bg-gray-50/80 border-y border-gray-200 text-[11px] font-medium uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="py-2.5 px-3">Tgl</th>
                            <th class="py-2.5 px-3">Transisi Unit</th>
                            <th class="py-2.5 px-3">Alasan</th>
                            <th class="py-2.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($penggantianTerbaru as $penggantian)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-2.5 px-3 whitespace-nowrap font-mono text-gray-600">
                                    {{ $penggantian->tanggal_penggantian ? $penggantian->tanggal_penggantian->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 font-mono text-xs">
                                        <span class="text-gray-500 line-through">{{ $penggantian->aparLama?->kode ?? '-' }}</span>
                                        <span class="text-gray-400" aria-hidden="true">&rarr;</span>
                                        <span class="font-bold text-emerald-800">{{ $penggantian->aparBaru?->kode ?? '-' }}</span>
                                    </div>
                                    <span class="block text-[11px] text-gray-500">Petugas: {{ $penggantian->petugas_nama }}</span>
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-800">
                                        {{ $penggantian->alasan_label }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <a href="{{ route('penggantian.show', $penggantian->id) }}"
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition min-h-[38px]">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500">
                                    <p class="text-sm font-semibold text-gray-900">Belum ada riwayat penggantian unit</p>
                                    <p class="text-xs text-gray-500 mt-1">Seluruh tabung masih beroperasi dengan unit awal pemasangan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.app>
