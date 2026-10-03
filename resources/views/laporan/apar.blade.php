@if($isPrint)
    <x-layouts.print 
        title="Cetak Laporan Data APAR - HAFIS RSUD" 
        reportTitle="LAPORAN DATA MASTER & STATUS APAR RUMAH SAKIT" 
        :filters="$filtersSummary">

        <!-- Ringkasan Rekapitulasi Data -->
        <div style="margin-bottom: 12px; display: flex; gap: 10px; font-size: 8pt;">
            <div style="border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px; background: #f8fafc;">
                Total Unit: <strong>{{ $rekap['total'] }}</strong>
            </div>
            <div style="border: 1px solid #a7f3d0; padding: 6px 10px; border-radius: 4px; background: #ecfdf5; color: #065f46;">
                Unit Aktif: <strong>{{ $rekap['aktif'] }}</strong>
            </div>
            <div style="border: 1px solid #fecaca; padding: 6px 10px; border-radius: 4px; background: #fef2f2; color: #991b1b;">
                Kondisi Rusak: <strong>{{ $rekap['rusak'] }}</strong>
            </div>
            <div style="border: 1px solid #fde68a; padding: 6px 10px; border-radius: 4px; background: #fffbeb; color: #92400e;">
                Perlu Perhatian: <strong>{{ $rekap['perhatian'] }}</strong>
            </div>
            <div style="border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px; background: #f1f5f9;">
                Tidak Aktif: <strong>{{ $rekap['tidak_aktif'] }}</strong>
            </div>
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
                        <td>{{ $a->merek ?: '-' }} {{ $a->tipe_model ? "({$a->tipe_model})" : '' }}</td>
                        <td>
                            <strong>{{ $a->gedung?->nama ?? '-' }}</strong> - Lt. {{ $a->lantai?->nomor_lantai ?? '-' }}
                            <div>{{ $a->ruangan?->nama ?? '-' }}</div>
                            @if($a->posisi_detail)
                                <div style="font-size: 7pt; color: #64748b;">{{ $a->posisi_detail }}</div>
                            @endif
                        </td>
                        <td>
                            @if($a->status === 'baik')
                                <span class="badge badge-success">Baik</span>
                            @elseif($a->status === 'perhatian')
                                <span class="badge badge-warning">Perhatian</span>
                            @elseif($a->status === 'rusak')
                                <span class="badge badge-danger">Rusak</span>
                            @else
                                <span class="badge badge-neutral">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($a->is_replaced)
                                <span class="badge badge-neutral">Sudah Diganti</span>
                            @elseif($a->status === 'tidak_aktif')
                                <span class="badge badge-neutral">Nonaktif</span>
                            @else
                                <span class="badge badge-success">Aktif</span>
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
        <div class="pb-5 mb-6 border-b border-slate-200">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('laporan.index') }}" class="hover:text-slate-800 transition-colors">Pusat Laporan</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">Laporan Data APAR</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Laporan Data APAR</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-700 font-bold">Laporan 1</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Rekapitulasi seluruh tabung APAR yang terdaftar: spesifikasi fisik, kondisi, masa berlaku, dan penempatan.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['format' => 'print']) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[44px]">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Cetak Laporan</span>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 transition min-h-[44px]">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Export CSV (Excel)</span>
                    </a>

                    <a href="{{ route('laporan.index') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition min-h-[44px]">
                        <span>Pusat Laporan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Cards: Real Database Counters (Section 3) -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
            <div class="p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Unit</span>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="font-mono text-xl font-bold text-slate-900 tabular-nums">{{ $rekap['total'] }}</span>
                    <span class="text-[11px] text-slate-400">tabung</span>
                </div>
            </div>

            <div class="p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs">
                <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block">Unit Aktif</span>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="font-mono text-xl font-bold text-emerald-800 tabular-nums">{{ $rekap['aktif'] }}</span>
                    <span class="text-[11px] text-slate-400">operasional</span>
                </div>
            </div>

            <div class="p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs">
                <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider block">Perlu Perhatian</span>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="font-mono text-xl font-bold text-amber-800 tabular-nums">{{ $rekap['perhatian'] }}</span>
                    <span class="text-[11px] text-slate-400">pantau</span>
                </div>
            </div>

            <div class="p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs">
                <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider block">Kondisi Rusak</span>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="font-mono text-xl font-bold text-rose-800 tabular-nums">{{ $rekap['rusak'] }}</span>
                    <span class="text-[11px] text-slate-400">perlu tindakan</span>
                </div>
            </div>

            <div class="p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs col-span-2 sm:col-span-1">
                <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Tidak Aktif / Diganti</span>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="font-mono text-xl font-bold text-slate-700 tabular-nums">{{ $rekap['tidak_aktif'] }}</span>
                    <span class="text-[11px] text-slate-400">arsip</span>
                </div>
            </div>
        </div>

        <!-- Filter Form (Section 3) -->
        <div class="mb-6 p-4 rounded-xl border border-slate-200 bg-white shadow-2xs">
            <form method="GET" action="{{ route('laporan.apar') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <!-- Cari Teks -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Cari Kode / Register / Merek</label>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Ketik kode APAR..."
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:border-slate-400 min-h-[44px]" />
                    </div>

                    <!-- Gedung -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Gedung</label>
                        <select name="gedung_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none min-h-[44px]">
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
                        <select name="lantai_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none min-h-[44px]">
                            <option value="">Semua Lantai</option>
                            @foreach($lantais as $l)
                                <option value="{{ $l->id }}" {{ request('lantai_id') == $l->id ? 'selected' : '' }}>
                                    Lt. {{ $l->nomor_lantai }} - {{ $l->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kondisi Fisik -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kondisi</label>
                        <select name="kondisi" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none min-h-[44px]">
                            <option value="">Semua Kondisi</option>
                            <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="perhatian" {{ request('kondisi') == 'perhatian' ? 'selected' : '' }}>Perlu Perhatian</option>
                            <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                    </div>

                    <!-- Status Penggunaan -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status Operasional</label>
                        <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none min-h-[44px]">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="sudah_diganti" {{ request('status') == 'sudah_diganti' ? 'selected' : '' }}>Sudah Diganti</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                    <span class="text-slate-500 font-medium">
                        Menampilkan <strong>{{ $rekap['terfilter'] }}</strong> unit dari filter yang diterapkan
                    </span>

                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition min-h-[44px]">
                            <span>Terapkan Filter</span>
                        </button>

                        <a href="{{ route('laporan.apar') }}" 
                           class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition min-h-[44px]">
                            <span>Reset Filter</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Data Laporan (Section 3) -->
        <x-card title="Data Unit Tabung APAR Rumah Sakit" subtitle="Informasi lengkap inventaris sarana proteksi kebakaran aktif dan historis.">
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Kode APAR</th>
                            <th class="px-4 py-3 font-bold">Lokasi Penempatan</th>
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Media & Kapasitas</th>
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Merek / Model</th>
                            <th class="px-4 py-3 font-bold text-center whitespace-nowrap">Kondisi</th>
                            <th class="px-4 py-3 font-bold text-center whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Kadaluarsa</th>
                            <th class="px-4 py-3 font-bold text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                        @forelse($apars as $a)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('apar.show', $a->id) }}" class="font-mono font-bold text-slate-900 hover:underline">
                                        {{ $a->kode }}
                                    </a>
                                    @if($a->nomor_register)
                                        <div class="text-[11px] text-slate-400 font-mono">Reg: {{ $a->nomor_register }}</div>
                                    @endif
                                    <div class="text-[10px] text-slate-400 font-mono">QR: {{ $a->qr_code }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $a->ruangan?->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $a->gedung?->nama ?? '-' }} &bull; Lt. {{ $a->lantai?->nomor_lantai ?? '-' }}
                                    </div>
                                    @if($a->posisi_detail)
                                        <div class="text-[11px] text-slate-400 italic line-clamp-1">{{ $a->posisi_detail }}</div>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-medium text-slate-900">{{ $a->jenis_media }}</div>
                                    <div class="font-mono text-slate-500 text-[11px]">{{ $a->kapasitas }}</div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-slate-800">{{ $a->merek ?: '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $a->tipe_model ?: '-' }}</div>
                                </td>

                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <x-badge :variant="$a->status_badge_variant" :dot="true">
                                        {{ $a->status_label }}
                                    </x-badge>
                                </td>

                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($a->is_replaced)
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            Sudah Diganti
                                        </span>
                                    @elseif($a->status === 'tidak_aktif')
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Nonaktif
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 font-mono whitespace-nowrap tabular-nums">
                                    @if($a->tanggal_kadaluarsa)
                                        {{ $a->tanggal_kadaluarsa->format('d/m/Y') }}
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('apar.show', $a->id) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[36px]">
                                        <span>Detail Unit</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-400 italic">
                                    Belum ada data untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($apars->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ $apars->links() }}
                </div>
            @endif
        </x-card>
    </x-layouts.app>
@endif
