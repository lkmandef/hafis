<x-layouts.app title="HAFIS : Dashboard Monitoring APAR">
    <!-- 1. Header Halaman & Quick Actions -->
    <x-page-header
        title="Dashboard Monitoring APAR"
        description="Ringkasan kelaikan unit, aktivitas inspeksi berkala, dan penanganan pemeliharaan APAR rumah sakit.">
        <x-slot:actions>
            <div class="w-full sm:w-auto grid grid-cols-2 sm:flex sm:items-center gap-2 sm:gap-2.5">
                <x-button variant="primary" href="{{ route('scan.index') }}" class="order-1 sm:order-3 col-span-2 sm:col-span-1 w-full sm:w-auto">
                    <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Scan APAR</span>
                </x-button>

                <x-button variant="secondary" href="{{ route('apar.create') }}" class="order-2 sm:order-1 w-full sm:w-auto">
                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah APAR</span>
                </x-button>

                <x-button variant="secondary" href="{{ route('laporan.index') }}" class="order-3 sm:order-2 w-full sm:w-auto">
                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Pusat Laporan</span>
                </x-button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- 2. Ringkasan Utama Dashboard (Step 2: 5 Metrik Inti Terhubung ke Data Riil) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3.5 mb-6">
        <!-- 1. Total APAR Aktif -->
        <a href="{{ route('apar.index', ['status' => 'aktif']) }}" 
           class="group p-3 sm:p-4.5 rounded-xl bg-slate-900 text-white border border-slate-800 shadow-2xs hover:bg-slate-800 transition-all flex flex-col justify-between min-h-[90px] sm:min-h-[110px]">
            <div>
                <span class="block text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-400 group-hover:text-slate-300">
                    Total APAR Aktif
                </span>
                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5">
                    <span class="font-extrabold text-xl sm:text-2xl lg:text-3xl tabular-nums tracking-tight">
                        {{ $summaryStats['apar_aktif'] }}
                    </span>
                    <span class="text-[10px] sm:text-xs text-slate-400 font-medium">unit</span>
                </div>
            </div>
            <div class="mt-2 sm:mt-2.5 pt-1.5 sm:pt-2 border-t border-slate-800 text-[10px] sm:text-[11px] text-slate-400 flex items-center justify-between">
                <span>Total: {{ $summaryStats['apar_total'] }}</span>
                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
            </div>
        </a>

        <!-- 2. APAR Kondisi Baik -->
        <a href="{{ route('apar.index', ['kondisi' => 'baik']) }}" 
           class="group p-3 sm:p-4.5 rounded-xl bg-white text-slate-900 border border-slate-200 border-l-4 border-l-emerald-600 shadow-2xs hover:border-slate-300 transition-all flex flex-col justify-between min-h-[90px] sm:min-h-[110px]">
            <div>
                <span class="block text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    Kondisi Baik
                </span>
                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5">
                    <span class="font-extrabold text-xl sm:text-2xl lg:text-3xl tabular-nums tracking-tight text-emerald-700">
                        {{ $kondisiCounts['baik'] }}
                    </span>
                    <span class="text-[10px] sm:text-xs text-slate-400 font-medium">unit</span>
                </div>
            </div>
            <div class="mt-2 sm:mt-2.5 pt-1.5 sm:pt-2 border-t border-slate-100 text-[10px] sm:text-[11px] text-slate-500 flex items-center justify-between">
                <span>{{ $kondisiPersen['baik'] }}% total</span>
                <span class="text-emerald-700 group-hover:translate-x-0.5 transition-transform">&rarr;</span>
            </div>
        </a>

        <!-- 3. APAR Perlu Perhatian -->
        <a href="{{ route('apar.index', ['kondisi' => 'perhatian']) }}" 
           class="group p-3 sm:p-4.5 rounded-xl bg-white text-slate-900 border border-slate-200 border-l-4 border-l-amber-500 shadow-2xs hover:border-slate-300 transition-all flex flex-col justify-between min-h-[90px] sm:min-h-[110px]">
            <div>
                <span class="block text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    Perlu Perhatian
                </span>
                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5">
                    <span class="font-extrabold text-xl sm:text-2xl lg:text-3xl tabular-nums tracking-tight text-amber-700">
                        {{ $summaryStats['apar_perhatian'] }}
                    </span>
                    <span class="text-[10px] sm:text-xs text-slate-400 font-medium">unit</span>
                </div>
            </div>
            <div class="mt-2 sm:mt-2.5 pt-1.5 sm:pt-2 border-t border-slate-100 text-[10px] sm:text-[11px] text-slate-500 flex items-center justify-between">
                <span>Pantau unit</span>
                <span class="text-amber-700 group-hover:translate-x-0.5 transition-transform">&rarr;</span>
            </div>
        </a>

        <!-- 4. APAR Rusak / Perlu Tindakan -->
        <a href="{{ route('apar.index', ['kondisi' => 'rusak']) }}" 
           class="group p-3 sm:p-4.5 rounded-xl bg-white text-slate-900 border border-slate-200 border-l-4 border-l-rose-500 shadow-2xs hover:border-slate-300 transition-all flex flex-col justify-between min-h-[90px] sm:min-h-[110px]">
            <div>
                <span class="block text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    Rusak / Perlu Tindakan
                </span>
                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5">
                    <span class="font-extrabold text-xl sm:text-2xl lg:text-3xl tabular-nums tracking-tight text-rose-700">
                        {{ $summaryStats['apar_rusak'] }}
                    </span>
                    <span class="text-[10px] sm:text-xs text-slate-400 font-medium">unit</span>
                </div>
            </div>
            <div class="mt-2 sm:mt-2.5 pt-1.5 sm:pt-2 border-t border-slate-100 text-[10px] sm:text-[11px] text-slate-500 flex items-center justify-between">
                <span>Perlu tindakan</span>
                <span class="text-rose-700 group-hover:translate-x-0.5 transition-transform">&rarr;</span>
            </div>
        </a>

        <!-- 5. Tindak Lanjut Belum Selesai -->
        <a href="{{ route('tindak-lanjut.index', ['status' => 'belum_ditindaklanjuti']) }}" 
           class="group p-3 sm:p-4.5 rounded-xl bg-white text-slate-900 border border-slate-200 border-l-4 border-l-indigo-600 shadow-2xs hover:border-slate-300 transition-all flex flex-col justify-between min-h-[90px] sm:min-h-[110px] col-span-2 sm:col-span-1">
            <div>
                <span class="block text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    Tindak Lanjut Pending
                </span>
                <div class="mt-1 sm:mt-1.5 flex items-baseline gap-1.5">
                    <span class="font-extrabold text-xl sm:text-2xl lg:text-3xl tabular-nums tracking-tight text-indigo-700">
                        {{ $summaryStats['tl_belum_selesai'] }}
                    </span>
                    <span class="text-[10px] sm:text-xs text-slate-400 font-medium">tindakan</span>
                </div>
            </div>
            <div class="mt-2 sm:mt-2.5 pt-1.5 sm:pt-2 border-t border-slate-100 text-[10px] sm:text-[11px] text-slate-500 flex items-center justify-between">
                <span>{{ $tindakLanjutStats['belum'] }} baru, {{ $tindakLanjutStats['proses'] }} proses</span>
                <span class="text-indigo-700 group-hover:translate-x-0.5 transition-transform">&rarr;</span>
            </div>
        </a>
    </div>

    <!-- 3. Bagian Status APAR & Jadwal Inspeksi Berjalan (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Kolom Kiri: Status Kelaikan APAR (Step 3) -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-2xs p-5 sm:p-6">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">
                        Status Kelaikan & Kondisi APAR
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Proporsi kondisi fisik seluruh unit tabung APAR berdasarkan log pemeriksaan dan status terkini.
                    </p>
                </div>
                <a href="{{ route('laporan.apar') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    Laporan Lengkap &rarr;
                </a>
            </div>

            <!-- Horizontal Multi-Segment Progress Bar -->
            <div class="mb-5">
                <div class="w-full h-3.5 bg-slate-100 rounded-full overflow-hidden flex shadow-inner">
                    @if($summaryStats['apar_total'] > 0)
                        <div style="width: {{ $kondisiPersen['baik'] }}%" class="bg-emerald-600 h-full transition-all duration-500" title="Baik: {{ $kondisiCounts['baik'] }} unit ({{ $kondisiPersen['baik'] }}%)"></div>
                        <div style="width: {{ $kondisiPersen['perhatian'] }}%" class="bg-amber-500 h-full transition-all duration-500" title="Perlu Perhatian: {{ $kondisiCounts['perhatian'] }} unit ({{ $kondisiPersen['perhatian'] }}%)"></div>
                        <div style="width: {{ $kondisiPersen['rusak'] }}%" class="bg-rose-500 h-full transition-all duration-500" title="Rusak: {{ $kondisiCounts['rusak'] }} unit ({{ $kondisiPersen['rusak'] }}%)"></div>
                        <div style="width: {{ $kondisiPersen['tidak_aktif'] }}%" class="bg-slate-400 h-full transition-all duration-500" title="Tidak Aktif: {{ $kondisiCounts['tidak_aktif'] }} unit ({{ $kondisiPersen['tidak_aktif'] }}%)"></div>
                    @else
                        <div class="w-full bg-slate-200 h-full"></div>
                    @endif
                </div>
            </div>

            <!-- Rincian Legend Status -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 text-xs">
                <a href="{{ route('apar.index', ['kondisi' => 'baik']) }}" class="p-2.5 sm:p-3 rounded-lg bg-emerald-50/60 border border-emerald-100 hover:bg-emerald-50 transition-colors">
                    <div class="flex items-center gap-1.5 font-semibold text-emerald-900 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        <span class="text-xs">Baik</span>
                    </div>
                    <div class="font-extrabold text-base sm:text-lg text-emerald-800 font-mono tabular-nums">
                        {{ $kondisiCounts['baik'] }} <span class="text-xs font-normal text-emerald-700">unit</span>
                    </div>
                    <span class="text-[10px] sm:text-[11px] text-emerald-700">{{ $kondisiPersen['baik'] }}% operasional</span>
                </a>

                <a href="{{ route('apar.index', ['kondisi' => 'perhatian']) }}" class="p-2.5 sm:p-3 rounded-lg bg-amber-50/60 border border-amber-100 hover:bg-amber-50 transition-colors">
                    <div class="flex items-center gap-1.5 font-semibold text-amber-900 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span class="text-xs">Perlu Perhatian</span>
                    </div>
                    <div class="font-extrabold text-base sm:text-lg text-amber-800 font-mono tabular-nums">
                        {{ $summaryStats['apar_perhatian'] }} <span class="text-xs font-normal text-amber-700">unit</span>
                    </div>
                    <span class="text-[10px] sm:text-[11px] text-amber-700">{{ $kondisiPersen['perhatian'] }}% dipantau</span>
                </a>

                <a href="{{ route('apar.index', ['kondisi' => 'rusak']) }}" class="p-2.5 sm:p-3 rounded-lg bg-rose-50/60 border border-rose-100 hover:bg-rose-50 transition-colors">
                    <div class="flex items-center gap-1.5 font-semibold text-rose-900 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-xs">Rusak</span>
                    </div>
                    <div class="font-extrabold text-base sm:text-lg text-rose-800 font-mono tabular-nums">
                        {{ $summaryStats['apar_rusak'] }} <span class="text-xs font-normal text-rose-700">unit</span>
                    </div>
                    <span class="text-[10px] sm:text-[11px] text-rose-700">{{ $kondisiPersen['rusak'] }}% tidak laik</span>
                </a>

                <a href="{{ route('apar.index', ['status' => 'tidak_aktif']) }}" class="p-2.5 sm:p-3 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100/70 transition-colors">
                    <div class="flex items-center gap-1.5 font-semibold text-slate-700 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                        <span class="text-xs">Nonaktif / Arsip</span>
                    </div>
                    <div class="font-extrabold text-base sm:text-lg text-slate-800 font-mono tabular-nums">
                        {{ $kondisiCounts['tidak_aktif'] }} <span class="text-xs font-normal text-slate-500">unit</span>
                    </div>
                    <span class="text-[10px] sm:text-[11px] text-slate-500">{{ $kondisiPersen['tidak_aktif'] }}% diganti</span>
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Jadwal Inspeksi Aktif (Step 6) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">
                            Jadwal Inspeksi
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Status putaran inspeksi berkala.
                        </p>
                    </div>
                    <a href="{{ route('jadwal.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                        Semua Jadwal &rarr;
                    </a>
                </div>

                @if($jadwalData)
                    <div class="space-y-3.5">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-xs font-bold text-slate-900 line-clamp-1" title="{{ $jadwalData['model']->nama }}">
                                    {{ $jadwalData['model']->nama }}
                                </h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ $jadwalData['model']->status_label }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5 font-mono">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $jadwalData['model']->tanggal_mulai ? $jadwalData['model']->tanggal_mulai->format('d/m/Y') : '-' }} s/d {{ $jadwalData['model']->tanggal_selesai ? $jadwalData['model']->tanggal_selesai->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Progress Bar Jadwal -->
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200/80">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="text-slate-600 font-medium">Progres Pemeriksaan</span>
                                <span class="font-bold text-slate-900 font-mono">{{ $jadwalData['progress_pct'] }}%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="bg-slate-900 h-full rounded-full transition-all duration-500" style="width: {{ $jadwalData['progress_pct'] }}%"></div>
                            </div>
                            <div class="mt-2 text-[11px] text-slate-500 flex items-center justify-between">
                                <span>Selesai: <strong class="text-slate-800 font-mono">{{ $jadwalData['sudah_diperiksa'] }}</strong> APAR</span>
                                <span>Target: <strong class="text-slate-800 font-mono">{{ $jadwalData['total_target'] }}</strong> APAR</span>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State Jadwal -->
                    <div class="py-6 text-center">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-700">Tidak ada jadwal inspeksi aktif</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Semua putaran inspeksi berkala telah selesai atau belum dimulai.</p>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100">
                @if($jadwalData)
                    <a href="{{ route('jadwal.show', $jadwalData['model']->id) }}" 
                       class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-slate-900 hover:bg-slate-800 text-white transition min-h-[44px]">
                        <span>Buka Lembar Jadwal</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('jadwal.create') }}" 
                       class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition min-h-[44px]">
                        <span>Buat Jadwal Inspeksi Baru</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- 4. Section Khusus: APAR yang Memerlukan Perhatian (Step 7) -->
    <x-card class="mb-6">
        <x-slot:title>
            <div class="flex items-center gap-2">
                <span>Unit APAR Memerlukan Perhatian</span>
                @if($aparPerluPerhatian->count() > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                        {{ $summaryStats['apar_perhatian'] + $summaryStats['apar_rusak'] }} Unit
                    </span>
                @endif
            </div>
        </x-slot:title>

        <x-slot:subtitle>
            Daftar tabung APAR dengan status Perlu Perhatian atau Rusak yang membutuhkan penanganan teknis dan evaluasi K3RS.
        </x-slot:subtitle>

        @if($aparPerluPerhatian->count() > 0)
            <x-slot:actions>
                <a href="{{ route('laporan.temuan') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    Lihat Semua Temuan &rarr;
                </a>
            </x-slot:actions>
        @endif

        <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-y border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3 px-4">Kode APAR</th>
                        <th class="py-3 px-4">Media & Kapasitas</th>
                        <th class="py-3 px-4">Lokasi Penempatan</th>
                        <th class="py-3 px-4">Kondisi Tabung</th>
                        <th class="py-3 px-4">Status Penanganan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($aparPerluPerhatian as $apar)
                        @php
                            $latestTl = $apar->tindakLanjuts->first();
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 whitespace-nowrap font-mono font-bold text-slate-900">
                                <a href="{{ route('apar.show', $apar->id) }}" class="text-blue-600 hover:underline">
                                    {{ $apar->kode }}
                                </a>
                                <span class="block text-[10px] font-normal text-slate-400">{{ $apar->nomor_register ?: '-' }}</span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-medium text-slate-800">{{ $apar->jenis_media }}</span>
                                <span class="block text-[11px] text-slate-400 font-mono">{{ $apar->kapasitas }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $apar->gedung?->nama ?? '-' }} - Lt. {{ $apar->lantai?->nomor_lantai ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $apar->ruangan?->nama ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($apar->status === 'rusak')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                        Rusak
                                    </span>
                                @elseif($apar->status === 'perhatian')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Perlu Perhatian
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $apar->status_label }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if(!$latestTl)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        Belum Ada Tindak Lanjut
                                    </span>
                                @elseif($latestTl->status === 'selesai')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Selesai Ditangani
                                    </span>
                                @elseif($latestTl->status === 'dalam_proses')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Dalam Proses
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                        Menunggu Tindakan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('apar.show', $apar->id) }}" 
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition min-h-[38px] sm:min-h-[32px]">
                                        Detail APAR
                                    </a>

                                    @if(!$latestTl || $latestTl->status === 'selesai')
                                        <a href="{{ route('tindak-lanjut.create', ['apar_id' => $apar->id]) }}" 
                                           class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-amber-800 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition min-h-[38px] sm:min-h-[32px]">
                                            + Tindak Lanjut
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-8 h-8 text-emerald-600 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-700">Semua Unit APAR dalam Kondisi Baik</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Tidak ditemukan tabung pemadam api yang berstatus rusak atau memerlukan tindakan darurat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- 5. Bagian Aktivitas Pemeriksaan & Tindak Lanjut Terbaru (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Kolom Kiri: Ringkasan Pemeriksaan Terbaru (Step 4) -->
        <x-card>
            <x-slot:title>
                <div class="flex items-center justify-between">
                    <span>Pemeriksaan Terbaru</span>
                </div>
            </x-slot:title>
            <x-slot:subtitle>
                Log verifikasi fisik terkini ({{ $pemeriksaanStats['bulan_ini'] }} pemeriksaan pada {{ $summaryStats['periode_bulan'] }}).
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('laporan.pemeriksaan') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    Semua Pemeriksaan &rarr;
                </a>
            </x-slot:actions>

            <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 border-y border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="py-2.5 px-4">Tanggal</th>
                            <th class="py-2.5 px-4">Kode APAR</th>
                            <th class="py-2.5 px-4">Pemeriksa</th>
                            <th class="py-2.5 px-4">Hasil</th>
                            <th class="py-2.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pemeriksaanTerbaru as $p)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2.5 px-4 whitespace-nowrap font-mono text-slate-600">
                                    {{ $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d/m/Y') : '-' }}
                                    <span class="block text-[10px] text-slate-400">{{ $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('H:i') : '' }} WIB</span>
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-slate-900">{{ $p->apar?->kode ?? '-' }}</span>
                                    <span class="block text-[10px] text-slate-400">{{ $p->apar?->gedung?->nama ?? '-' }}</span>
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap font-medium text-slate-800">
                                    {{ $p->pemeriksa_nama }}
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    @if($p->hasil_keseluruhan === 'baik')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Baik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                            Perlu Tindakan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('pemeriksaan.show', $p->id) }}" 
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition min-h-[38px] sm:min-h-[30px]">
                                        Checklist
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    <p class="text-xs font-semibold text-slate-700">Belum ada pemeriksaan tercatat</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Mulai pemeriksaan APAR melalui menu Scan APAR atau Jadwal Inspeksi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Kolom Kanan: Tindak Lanjut & Pemeliharaan Terkini (Step 5) -->
        <x-card>
            <x-slot:title>
                <div class="flex items-center justify-between">
                    <span>Tindak Lanjut & Pemeliharaan</span>
                </div>
            </x-slot:title>
            <x-slot:subtitle>
                Aktivitas perbaikan, penggantian suku cadang, dan pengisian ulang media APAR.
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('tindak-lanjut.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    Semua Tindak Lanjut &rarr;
                </a>
            </x-slot:actions>

            <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 border-y border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="py-2.5 px-4">Tgl</th>
                            <th class="py-2.5 px-4">APAR & Tindakan</th>
                            <th class="py-2.5 px-4">Teknisi</th>
                            <th class="py-2.5 px-4">Status</th>
                            <th class="py-2.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tindakLanjutTerbaru as $tl)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2.5 px-4 whitespace-nowrap font-mono text-slate-600">
                                    {{ $tl->tanggal_tindakan ? $tl->tanggal_tindakan->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-2.5 px-4">
                                    <span class="font-mono font-bold text-slate-900">{{ $tl->apar?->kode ?? '-' }}</span>
                                    <span class="block text-[11px] text-slate-500 truncate max-w-[180px]" title="{{ $tl->uraian_tindakan }}">
                                        {{ $tl->jenis_label }}: {{ $tl->uraian_tindakan ?: '-' }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap font-medium text-slate-800">
                                    {{ $tl->petugas_pelaksana }}
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    @if($tl->status === 'selesai')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Selesai
                                        </span>
                                    @elseif($tl->status === 'dalam_proses')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            Proses
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('tindak-lanjut.show', $tl->id) }}" 
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition min-h-[38px] sm:min-h-[30px]">
                                        Rincian
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    <p class="text-xs font-semibold text-slate-700">Belum ada catatan tindak lanjut</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Seluruh temuan checklist telah ditindaklanjuti atau belum ada temuan baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <!-- 6. Ringkasan Lokasi Gedung (Step 9) & Histori Penggantian APAR (Step 8) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kolom Kiri: Ringkasan Lokasi Gedung (Step 9) -->
        <x-card>
            <x-slot:title>
                <span>Ringkasan Per Lokasi Gedung</span>
            </x-slot:title>
            <x-slot:subtitle>
                Sebaran ketersediaan dan status fisik tabung APAR per area gedung rumah sakit.
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('apar.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    Semua Lokasi &rarr;
                </a>
            </x-slot:actions>

            <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 border-y border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="py-2.5 px-4">Gedung</th>
                            <th class="py-2.5 px-4 text-center">Total Unit</th>
                            <th class="py-2.5 px-4 text-center">Baik</th>
                            <th class="py-2.5 px-4 text-center">Perhatian / Rusak</th>
                            <th class="py-2.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($gedungRingkasan as $g)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2.5 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $g['nama'] }}
                                    <span class="block text-[10px] font-normal text-slate-400 font-mono">{{ $g['kode'] }}</span>
                                </td>
                                <td class="py-2.5 px-4 text-center font-mono font-bold text-slate-900 tabular-nums">
                                    {{ $g['total'] }}
                                </td>
                                <td class="py-2.5 px-4 text-center font-mono font-bold text-emerald-700 tabular-nums">
                                    {{ $g['baik'] }}
                                </td>
                                <td class="py-2.5 px-4 text-center font-mono font-bold tabular-nums">
                                    @if($g['perhatian'] + $g['rusak'] > 0)
                                        <span class="text-rose-700">{{ $g['perhatian'] + $g['rusak'] }}</span>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('apar.index', ['gedung_id' => $g['id']]) }}" 
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition min-h-[38px] sm:min-h-[30px]">
                                        Filter Gedung
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    <p class="text-xs font-semibold text-slate-700">Belum ada master gedung</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Kelola struktur gedung melalui modul Lokasi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Kolom Kanan: Penggantian APAR Terbaru (Step 8) -->
        <x-card>
            <x-slot:title>
                <span>Penggantian APAR Terbaru</span>
            </x-slot:title>
            <x-slot:subtitle>
                Histori mutasi pergantian unit lama ke unit baru.
            </x-slot:subtitle>
            <x-slot:actions>
                <a href="{{ route('penggantian.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    Semua Penggantian &rarr;
                </a>
            </x-slot:actions>

            <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 border-y border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="py-2.5 px-4">Tgl</th>
                            <th class="py-2.5 px-4">Transisi Unit</th>
                            <th class="py-2.5 px-4">Alasan</th>
                            <th class="py-2.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($penggantianTerbaru as $penggantian)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2.5 px-4 whitespace-nowrap font-mono text-slate-600">
                                    {{ $penggantian->tanggal_penggantian ? $penggantian->tanggal_penggantian->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 font-mono text-xs">
                                        <span class="text-slate-500 line-through">{{ $penggantian->aparLama?->kode ?? '-' }}</span>
                                        <span class="text-slate-400">&rarr;</span>
                                        <span class="font-bold text-emerald-800">{{ $penggantian->aparBaru?->kode ?? '-' }}</span>
                                    </div>
                                    <span class="block text-[10px] text-slate-400">Petugas: {{ $penggantian->petugas_nama }}</span>
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-800">
                                        {{ $penggantian->alasan_label }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('penggantian.show', $penggantian->id) }}" 
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition min-h-[38px] sm:min-h-[30px]">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    <p class="text-xs font-semibold text-slate-700">Belum ada riwayat penggantian unit</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Seluruh tabung masih beroperasi dengan unit awal pemasangan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.app>