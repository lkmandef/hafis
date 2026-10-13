@if($isPrint)
    <x-layouts.print 
        title="Cetak Laporan Penggantian APAR - HAFIS RSUD" 
        reportTitle="LAPORAN HISTORI MUTASI & PENGGANTIAN UNIT APAR" 
        :filters="$filtersSummary">

        {{-- Baris ringkasan tunggal --}}
        <div style="margin-bottom: 10px; font-size: 8pt; color: #0f172a;">
            Total Penggantian: <strong>{{ $rekap['total'] }}</strong> unit &nbsp;&bull;&nbsp;
            Karena Rusak: <strong>{{ $rekap['rusak'] }}</strong> &nbsp;&bull;&nbsp;
            Kadaluarsa: <strong>{{ $rekap['kadaluarsa'] }}</strong> &nbsp;&bull;&nbsp;
            Peremajaan: <strong>{{ $rekap['peremajaan'] }}</strong>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">No</th>
                    <th>Tgl Ganti</th>
                    <th>APAR Lama (Diganti)</th>
                    <th>APAR Baru (Pengganti)</th>
                    <th>Lokasi Penempatan</th>
                    <th>Alasan Penggantian</th>
                    <th>Petugas Pelaksana</th>
                    <th>Catatan Mutasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penggantians as $idx => $p)
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="font-mono">
                            {{ $p->tanggal_penggantian ? $p->tanggal_penggantian->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            <strong class="font-mono">{{ $p->aparLama?->kode ?? '-' }}</strong>
                            <div style="font-size: 7.5pt; color: #64748b;">{{ $p->aparLama?->jenis_media ?? '-' }} ({{ $p->aparLama?->kapasitas ?? '-' }})</div>
                        </td>
                        <td>
                            <strong class="font-mono">{{ $p->aparBaru?->kode ?? '-' }}</strong>
                            <div style="font-size: 7.5pt; color: #64748b;">{{ $p->aparBaru?->jenis_media ?? '-' }} ({{ $p->aparBaru?->kapasitas ?? '-' }})</div>
                        </td>
                        <td>
                            <strong>{{ $p->aparBaru?->lokasi?->nama ?? $p->aparLama?->lokasi?->nama ?? $p->aparBaru?->ruangan?->nama ?? $p->aparLama?->ruangan?->nama ?? '-' }}</strong>
                            <div>
                                @if($p->aparBaru?->lokasi || $p->aparLama?->lokasi)
                                    {{ ($p->aparBaru?->lokasi ?? $p->aparLama?->lokasi)->keterangan ?? ($p->aparBaru?->lokasi ?? $p->aparLama?->lokasi)->nama }}
                                @else
                                    {{ $p->aparBaru?->gedung?->nama ?? $p->aparLama?->gedung?->nama ?? '-' }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong>{{ $p->alasan_label }}</strong>
                            @if($p->alasan_keterangan)
                                <div style="font-size: 7pt; color: #64748b;">{{ $p->alasan_keterangan }}</div>
                            @endif
                        </td>
                        <td>{{ $p->petugas_nama }}</td>
                        <td>{{ $p->catatan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">
                            Belum ada data untuk filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-layouts.print>
@else
    <x-layouts.app title="HAFIS : Laporan Penggantian APAR">
        <!-- Header with Breadcrumb & Action Buttons -->
        <div class="pb-4 mb-4 border-b border-slate-200">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('laporan.index') }}" class="hover:text-slate-800 transition-colors">Laporan & Rekapitulasi</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">Laporan Penggantian APAR</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Laporan Penggantian APAR</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-purple-50 border border-purple-200 text-purple-800 font-bold">Laporan 5</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Histori pergantian unit tabung APAR yang menjamin integritas pelacakan transisi: APAR Lama ke APAR Baru.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['format' => 'print']) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[38px]">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Cetak Laporan</span>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 transition min-h-[38px]">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Export CSV (Excel)</span>
                    </a>

                    <a href="{{ route('laporan.index') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition min-h-[38px]">
                        <span>Pusat Laporan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Compact Summary Bar (Document-Oriented) -->
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 sm:p-3 mb-4 overflow-x-auto">
            <div class="flex items-center divide-x divide-slate-200 min-w-max sm:min-w-0">
                <div class="flex-1 px-3 sm:px-4 first:pl-1 sm:first:pl-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Pergantian Unit</span>
                    <span class="text-base sm:text-lg font-bold text-slate-900 tabular-nums">
                        {{ $rekap['total'] }}
                        <span class="text-xs font-normal text-slate-500">peristiwa</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Kondisi Rusak</span>
                    <span class="text-base sm:text-lg font-bold text-rose-800 tabular-nums">
                        {{ $rekap['rusak'] }}
                        <span class="text-xs font-normal text-slate-500">unit</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Habis Masa Berlaku</span>
                    <span class="text-base sm:text-lg font-bold text-amber-800 tabular-nums">
                        {{ $rekap['kadaluarsa'] }}
                        <span class="text-xs font-normal text-slate-500">unit</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4 last:pr-1 sm:last:pr-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Peremajaan Rutin</span>
                    <span class="text-base sm:text-lg font-bold text-slate-800 tabular-nums">
                        {{ $rekap['peremajaan'] }}
                        <span class="text-xs font-normal text-slate-500">unit</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Compact Filter Form (Section 7) -->
        <div class="mb-4 p-3 sm:p-3.5 rounded-lg border border-slate-200 bg-white">
            <form method="GET" action="{{ route('laporan.penggantian') }}" class="space-y-2.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                    <!-- Dari Tanggal -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                        <input
                            type="date"
                            name="dari_tanggal"
                            value="{{ request('dari_tanggal') }}"
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-400 min-h-[40px]" />
                    </div>

                    <!-- Sampai Tanggal -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                        <input
                            type="date"
                            name="sampai_tanggal"
                            value="{{ request('sampai_tanggal') }}"
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-400 min-h-[40px]" />
                    </div>

                    <!-- Gedung -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Gedung</label>
                        <select name="gedung_id" onchange="this.form.submit()" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none min-h-[40px]">
                            <option value="">Semua Gedung</option>
                            @foreach($gedungs as $g)
                                <option value="{{ $g->id }}" {{ request('gedung_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lantai -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Lantai</label>
                        <select name="lantai_id" onchange="this.form.submit()" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none min-h-[40px]">
                            <option value="">Semua Lantai</option>
                            @foreach($lantais as $l)
                                <option value="{{ $l->id }}" {{ request('lantai_id') == $l->id ? 'selected' : '' }}>
                                    Lt. {{ $l->nomor_lantai }} - {{ $l->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Alasan Penggantian -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Alasan Penggantian</label>
                        <select name="alasan" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none min-h-[40px]">
                            <option value="">Semua Alasan</option>
                            <option value="rusak" {{ request('alasan') == 'rusak' ? 'selected' : '' }}>Kondisi Rusak</option>
                            <option value="kadaluarsa" {{ request('alasan') == 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                            <option value="penggantian_unit" {{ request('alasan') == 'penggantian_unit' ? 'selected' : '' }}>Penggantian Unit (Peremajaan)</option>
                            <option value="hilang" {{ request('alasan') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                            <option value="lainnya" {{ request('alasan') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Pencarian Teks -->
                    <div class="lg:col-span-3">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Cari Kode APAR Lama / Baru / Petugas / Keterangan</label>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Ketik kode APAR lama atau baru, atau nama petugas..."
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-400 min-h-[40px]" />
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold rounded-md text-white bg-slate-900 hover:bg-slate-800 transition min-h-[38px] cursor-pointer">
                            <span>Terapkan Filter</span>
                        </button>

                        <a href="{{ route('laporan.penggantian') }}" 
                           class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md text-slate-600 bg-slate-100 hover:bg-slate-200 transition min-h-[38px]">
                            <span>Reset Filter</span>
                        </a>

                        <span class="text-slate-500 text-xs hidden sm:inline-block ml-2">
                            Menampilkan <strong class="text-slate-800 font-semibold">{{ $rekap['terfilter'] }}</strong> peristiwa pergantian dari filter yang diterapkan
                        </span>
                    </div>

                    <span class="text-slate-500 text-xs sm:hidden">
                        Menampilkan <strong class="text-slate-800 font-semibold">{{ $rekap['terfilter'] }}</strong> peristiwa pergantian dari filter yang diterapkan
                    </span>
                </div>
            </form>
        </div>

        <!-- Table Container (Horizontal Scroll for Responsive) -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-600 font-bold">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Tanggal Ganti</th>
                            <th class="py-3 px-3.5">APAR Lama (Digantikan)</th>
                            <th class="py-3 px-3.5">APAR Baru (Pengganti)</th>
                            <th class="py-3 px-3.5">Lokasi Penempatan</th>
                            <th class="py-3 px-3.5">Alasan Pergantian</th>
                            <th class="py-3 px-3.5">Petugas</th>
                            <th class="py-3 px-3.5">Catatan</th>
                            <th class="py-3 px-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($penggantians as $idx => $p)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-3.5 text-center font-mono text-slate-400">
                                    {{ $penggantians->firstItem() + $idx }}
                                </td>
                                <td class="py-3 px-3.5 font-mono whitespace-nowrap">
                                    <span class="font-bold text-slate-900">{{ $p->tanggal_penggantian ? $p->tanggal_penggantian->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-600 line-through">
                                        {{ $p->aparLama?->kode ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $p->aparLama?->jenis_media ?? '-' }} ({{ $p->aparLama?->kapasitas ?? '-' }})
                                    </div>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] bg-slate-100 text-slate-600 mt-0.5">
                                        Diarsipkan
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-mono font-bold text-emerald-800">
                                        <a href="{{ route('apar.show', $p->apar_baru_id ?? 0) }}" class="hover:underline">
                                            {{ $p->aparBaru?->kode ?? '-' }}
                                        </a>
                                    </div>
                                    <div class="text-[10px] text-slate-500">
                                        {{ $p->aparBaru?->jenis_media ?? '-' }} ({{ $p->aparBaru?->kapasitas ?? '-' }})
                                    </div>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] bg-emerald-50 text-emerald-800 border border-emerald-200 mt-0.5">
                                        Aktif Beroperasi
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-semibold text-slate-900">
                                        {{ $p->aparBaru?->lokasi?->nama ?? $p->aparLama?->lokasi?->nama ?? $p->aparBaru?->ruangan?->nama ?? $p->aparLama?->ruangan?->nama ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        @if($p->aparBaru?->lokasi || $p->aparLama?->lokasi)
                                            {{ ($p->aparBaru?->lokasi ?? $p->aparLama?->lokasi)->keterangan ?? ($p->aparBaru?->lokasi ?? $p->aparLama?->lokasi)->nama }}
                                        @else
                                            {{ $p->aparBaru?->gedung?->nama ?? $p->aparLama?->gedung?->nama ?? '-' }} - Lt. {{ $p->aparBaru?->lantai?->nomor_lantai ?? $p->aparLama?->lantai?->nomor_lantai ?? '-' }}
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                        {{ $p->alasan_label }}
                                    </span>
                                    @if($p->alasan_keterangan)
                                        <div class="text-[10px] text-slate-500 mt-0.5 max-w-xs truncate" title="{{ $p->alasan_keterangan }}">
                                            {{ $p->alasan_keterangan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="font-medium text-slate-900">{{ $p->petugas_nama }}</span>
                                </td>
                                <td class="py-3 px-3.5 max-w-xs text-slate-600 truncate" title="{{ $p->catatan }}">
                                    {{ $p->catatan ?: '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <a href="{{ route('penggantian.show', $p->id) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition min-h-[32px]">
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-medium">Belum ada data untuk filter yang dipilih.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan pilihan filter atau kata kunci pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($penggantians->hasPages())
                <div class="p-3.5 border-t border-slate-100">
                    {{ $penggantians->links() }}
                </div>
            @endif
        </div>
    </x-layouts.app>
@endif
