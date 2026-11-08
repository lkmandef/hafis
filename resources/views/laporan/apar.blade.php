@if($isPrint)
    <x-layouts.print 
        title="Cetak Laporan Data APAR - HAFIS RSUD" 
        reportTitle="LAPORAN DATA MASTER & STATUS APAR RUMAH SAKIT" 
        :filters="$filtersSummary">

        {{-- Baris ringkasan tunggal --}}
        <div style="margin-bottom: 10px; font-size: 8.5pt; color: #0f172a;">
            Total Tabung APAR: <strong>{{ $rekap['total'] }}</strong> unit &nbsp;&bull;&nbsp;
            Aktif: <strong>{{ $rekap['aktif'] }}</strong> &nbsp;&bull;&nbsp;
            Perlu Perhatian: <strong>{{ $rekap['perhatian'] }}</strong> &nbsp;&bull;&nbsp;
            Rusak: <strong>{{ $rekap['rusak'] }}</strong> &nbsp;&bull;&nbsp;
            Tidak Aktif: <strong>{{ $rekap['tidak_aktif'] }}</strong>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">No</th>
                    <th>Kode APAR</th>
                    <th>Register</th>
                    <th>Media</th>
                    <th>Kapasitas</th>
                    <th>Merek / Model</th>
                    <th>Lokasi (Gedung / Lantai / Ruangan)</th>
                    <th>Kondisi Fisik</th>
                    <th>Status</th>
                    <th>Tgl Exp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($apars as $idx => $a)
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="font-mono font-bold">{{ $a->kode }}</td>
                        <td class="font-mono">{{ $a->nomor_register ?: '-' }}</td>
                        <td>{{ $a->jenis_media }}</td>
                        <td class="font-mono">{{ $a->kapasitas }}</td>
                        <td>{{ $a->merek ?: 'Belum terdata' }} {{ $a->tipe_model ? "({$a->tipe_model})" : '' }}</td>
                        <td>
                            <strong>{{ $a->lokasi?->nama ?? $a->ruangan?->nama ?? '-' }}</strong>
                            <div>
                                @if($a->lokasi)
                                    {{ $a->lokasi->keterangan ?? $a->lokasi->nama }}
                                @else
                                    {{ $a->gedung?->nama ?? '-' }} - Lt. {{ $a->lantai?->nomor_lantai ?? '-' }}
                                @endif
                            </div>
                            @if($a->posisi_detail)
                                <div style="font-size: 8pt; color: #475569;">{{ $a->posisi_detail }}</div>
                            @endif
                        </td>
                        <td>
                            @if($a->status === 'baik')
                                <span class="status-normal">Baik</span>
                            @elseif($a->status === 'perhatian')
                                <span class="status-perhatian">Perhatian</span>
                            @elseif($a->status === 'rusak')
                                <span class="status-kritis">Rusak</span>
                            @else
                                <span>Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($a->is_replaced)
                                <span>Sudah Diganti</span>
                            @elseif($a->status === 'tidak_aktif')
                                <span>Nonaktif</span>
                            @else
                                <span class="status-normal">Aktif</span>
                            @endif
                        </td>
                        <td class="font-mono">
                            {{ $a->tanggal_kadaluarsa ? $a->tanggal_kadaluarsa->format('d/m/Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                            Belum ada data untuk filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-layouts.print>
@else
    <x-layouts.app title="HAFIS : Laporan Data APAR">
        <!-- Header with Breadcrumb & Action Buttons -->
        <div class="pb-4 mb-4 border-b border-slate-200">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('laporan.index') }}" class="hover:text-slate-800 transition-colors">Laporan & Rekapitulasi</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">Laporan Data APAR</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Laporan Data APAR</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-700 font-bold">Laporan 1</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Rekapitulasi seluruh tabung APAR yang terdaftar: spesifikasi fisik, kondisi, masa berlaku, dan penempatan.
                    </p>
                </div>

                <div class="flex items-center gap-2 sm:shrink-0">
                    <a href="{{ request()->fullUrlWithQuery(['format' => 'print']) }}" target="_blank" rel="noopener"
                       aria-label="Cetak Laporan Data APAR (buka di tab baru)"
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[38px] shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Cetak Laporan</span>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 transition min-h-[38px] shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Export CSV (Excel)</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Compact Summary Bar (Document-Oriented) -->
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 sm:p-3 mb-4 overflow-x-auto">
            <div class="flex items-center divide-x divide-slate-200 min-w-max sm:min-w-0">
                <div class="flex-1 px-3 sm:px-4 first:pl-1 sm:first:pl-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Unit</span>
                    <span class="text-base sm:text-lg font-bold text-slate-900 tabular-nums">
                        {{ $rekap['total'] }}
                        <span class="text-xs font-normal text-slate-500">tabung</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Unit Aktif</span>
                    <span class="text-base sm:text-lg font-bold text-emerald-800 tabular-nums">
                        {{ $rekap['aktif'] }}
                        <span class="text-xs font-normal text-slate-500">operasional</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Perlu Perhatian</span>
                    <span class="text-base sm:text-lg font-bold {{ $rekap['perhatian'] > 0 ? 'text-amber-800' : 'text-slate-800' }} tabular-nums">
                        {{ $rekap['perhatian'] }}
                        <span class="text-xs font-normal text-slate-500">pantau</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Kondisi Rusak</span>
                    <span class="text-base sm:text-lg font-bold {{ $rekap['rusak'] > 0 ? 'text-rose-800' : 'text-slate-800' }} tabular-nums">
                        {{ $rekap['rusak'] }}
                        <span class="text-xs font-normal text-slate-500">perlu tindakan</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4 last:pr-1 sm:last:pr-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tidak Aktif / Diganti</span>
                    <span class="text-base sm:text-lg font-bold text-slate-700 tabular-nums">
                        {{ $rekap['tidak_aktif'] }}
                        <span class="text-xs font-normal text-slate-500">arsip</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Compact Filter Form (Section 3) -->
        <div class="mb-4 p-3 sm:p-3.5 rounded-lg border border-slate-200 bg-white shadow-2xs">
            <form method="GET" action="{{ route('laporan.apar') }}" class="space-y-2.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5">
                    <!-- Cari Teks -->
                    <div class="lg:col-span-2">
                        <label for="filter_apar_q" class="block text-[11px] font-semibold text-slate-600 mb-1">Cari Kode / Register / Merek</label>
                        <input
                            type="text"
                            id="filter_apar_q"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Ketik kode APAR, register, atau merek..."
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]" />
                    </div>

                    <!-- Lokasi K3RS -->
                    <div class="lg:col-span-2">
                        <label for="filter_apar_lokasi" class="block text-[11px] font-semibold text-slate-600 mb-1">Lokasi K3RS</label>
                        <select id="filter_apar_lokasi" name="lokasi_id" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Lokasi K3RS</option>
                            @foreach($lokasis as $lok)
                                <option value="{{ $lok->id }}" {{ (string) request('lokasi_id') === (string) $lok->id ? 'selected' : '' }}>
                                    {{ $lok->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kondisi Fisik -->
                    <div>
                        <label for="filter_apar_kondisi" class="block text-[11px] font-semibold text-slate-600 mb-1">Kondisi</label>
                        <select id="filter_apar_kondisi" name="kondisi" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Kondisi</option>
                            <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="perhatian" {{ request('kondisi') == 'perhatian' ? 'selected' : '' }}>Perlu Perhatian</option>
                            <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                    </div>

                    <!-- Status Penggunaan -->
                    <div>
                        <label for="filter_apar_status" class="block text-[11px] font-semibold text-slate-600 mb-1">Status Operasional</label>
                        <select id="filter_apar_status" name="status" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="sudah_diganti" {{ request('status') == 'sudah_diganti' ? 'selected' : '' }}>Sudah Diganti</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold rounded-md text-white bg-slate-900 hover:bg-slate-800 transition min-h-[36px] cursor-pointer shadow-2xs">
                            <span>Terapkan Filter</span>
                        </button>

                        <a href="{{ route('laporan.apar') }}" 
                           class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md text-slate-600 bg-slate-100 hover:bg-slate-200 transition min-h-[36px]">
                            <span>Reset Filter</span>
                        </a>

                        <span class="text-slate-500 text-xs ml-2 tabular-nums">
                            Menampilkan <strong class="text-slate-800 font-semibold">{{ $rekap['terfilter'] }}</strong> unit (terfilter)
                        </span>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Data Laporan (Container Seragam dengan Sibling View) -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs min-w-[960px]">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-600 font-bold">
                        <tr>
                            <th class="py-3 pl-5 sm:pl-6 pr-4 whitespace-nowrap">Kode APAR</th>
                            <th class="px-4 py-3">Lokasi Penempatan</th>
                            <th class="px-4 py-3 whitespace-nowrap">Media & Kapasitas</th>
                            <th class="px-4 py-3 whitespace-nowrap">Merek / Model</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">Kondisi</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 whitespace-nowrap">Kadaluarsa</th>
                            <th class="py-3 pl-4 pr-5 sm:pr-6 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                        @forelse($apars as $a)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 pl-5 sm:pl-6 pr-4 whitespace-nowrap">
                                    <a href="{{ route('apar.show', $a->id) }}" class="font-mono font-bold text-slate-900 hover:underline">
                                        {{ $a->kode }}
                                    </a>
                                    @if($a->nomor_register)
                                        <div class="text-[11px] text-slate-500 font-mono">Reg: {{ $a->nomor_register }}</div>
                                    @endif
                                    <div class="text-[10px] text-slate-500 font-mono">QR: {{ $a->qr_code }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $a->lokasi?->nama ?? $a->ruangan?->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">
                                        @if($a->lokasi)
                                            {{ $a->lokasi->keterangan ?? $a->lokasi->nama }}
                                        @else
                                            {{ $a->gedung?->nama ?? '-' }} &bull; Lt. {{ $a->lantai?->nomor_lantai ?? '-' }}
                                        @endif
                                    </div>
                                    @if($a->posisi_detail)
                                        <div class="text-[11px] text-slate-500 italic line-clamp-1" title="{{ $a->posisi_detail }}">{{ $a->posisi_detail }}</div>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-medium text-slate-900">{{ $a->jenis_media }}</div>
                                    <div class="font-mono text-slate-500 text-[11px] tabular-nums">{{ $a->kapasitas }}</div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-slate-800 {{ ! $a->merek ? 'text-slate-400 italic' : '' }}">{{ $a->merek ?: 'Belum terdata' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $a->tipe_model ?: '-' }}</div>
                                </td>

                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <x-badge :variant="$a->status_badge_variant">
                                        {{ $a->status_label }}
                                    </x-badge>
                                </td>

                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($a->is_replaced)
                                        <x-badge variant="neutral" size="sm">Sudah Diganti</x-badge>
                                    @elseif($a->status === 'tidak_aktif')
                                        <x-badge variant="danger" size="sm">Nonaktif</x-badge>
                                    @else
                                        <x-badge variant="success" size="sm">Aktif</x-badge>
                                    @endif
                                </td>

                                <td class="px-4 py-3 font-mono whitespace-nowrap tabular-nums">
                                    @if($a->tanggal_kadaluarsa)
                                        {{ $a->tanggal_kadaluarsa->format('d/m/Y') }}
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>

                                <td class="py-3 pl-4 pr-5 sm:pr-6 text-center whitespace-nowrap">
                                    <a href="{{ route('apar.show', $a->id) }}" 
                                       class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition min-h-[32px]">
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500">
                                    <p class="text-sm font-semibold text-slate-700">Belum ada data untuk filter yang dipilih.</p>
                                    <p class="text-xs text-slate-500 mt-1">Coba sesuaikan pilihan filter atau kata kunci pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($apars->hasPages())
                <div class="p-3.5 border-t border-slate-100">
                    {{ $apars->links() }}
                </div>
            @endif
        </div>
    </x-layouts.app>
@endif