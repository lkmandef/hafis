@if($isPrint)
    <x-layouts.print 
        title="Cetak Laporan Temuan Pemeriksaan APAR - HAFIS RSUD" 
        reportTitle="LAPORAN TEMUAN KERUSAKAN & DEFECT CHECKLIST PEMERIKSAAN APAR" 
        :filters="$filtersSummary">

        <!-- Ringkasan Rekapitulasi Data -->
        <div style="margin-bottom: 12px; display: flex; gap: 10px; font-size: 8pt;">
            <div style="border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px; background: #f8fafc;">
                Total Temuan Tidak Baik: <strong>{{ $rekap['total'] }}</strong>
            </div>
            <div style="border: 1px solid #fecaca; padding: 6px 10px; border-radius: 4px; background: #fef2f2; color: #991b1b;">
                Belum Ditindaklanjuti: <strong>{{ $rekap['belum_ditindaklanjuti'] }}</strong>
            </div>
            <div style="border: 1px solid #fde68a; padding: 6px 10px; border-radius: 4px; background: #fffbeb; color: #92400e;">
                Dalam Penanganan: <strong>{{ $rekap['dalam_proses'] }}</strong>
            </div>
            <div style="border: 1px solid #a7f3d0; padding: 6px 10px; border-radius: 4px; background: #ecfdf5; color: #065f46;">
                Telah Diselesaikan: <strong>{{ $rekap['selesai'] }}</strong>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">No</th>
                    <th>Tgl Pemeriksaan</th>
                    <th>Kode APAR</th>
                    <th>Lokasi (Gedung / Lt / Ruang)</th>
                    <th>Item Parameter</th>
                    <th>Hasil</th>
                    <th>Catatan Temuan Masalah</th>
                    <th>Pemeriksa</th>
                    <th>Status Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($temuans as $idx => $t)
                    @php
                        $tl = $t->tindakLanjut;
                    @endphp
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="font-mono">
                            {{ $t->pemeriksaan?->tanggal_pemeriksaan ? $t->pemeriksaan->tanggal_pemeriksaan->format('d/m/Y') : '-' }}
                        </td>
                        <td class="font-mono font-bold">{{ $t->pemeriksaan?->apar?->kode ?? '-' }}</td>
                        <td>
                            <strong>{{ $t->pemeriksaan?->apar?->gedung?->nama ?? '-' }}</strong> - Lt. {{ $t->pemeriksaan?->apar?->lantai?->nomor_lantai ?? '-' }}
                            <div>{{ $t->pemeriksaan?->apar?->ruangan?->nama ?? '-' }}</div>
                        </td>
                        <td><strong>{{ $t->item_nama }}</strong></td>
                        <td><span class="badge badge-danger">Tidak Baik</span></td>
                        <td>{{ $t->catatan ?: 'Tidak ada keterangan rinci' }}</td>
                        <td>{{ $t->pemeriksaan?->pemeriksa_nama ?? '-' }}</td>
                        <td>
                            @if(!$tl)
                                <span class="badge badge-neutral">Belum Dibuat TL</span>
                            @elseif($tl->status === 'selesai')
                                <span class="badge badge-success">Selesai</span>
                            @elseif($tl->status === 'dalam_proses')
                                <span class="badge badge-warning">Dalam Proses</span>
                            @else
                                <span class="badge badge-danger">Belum Ditindaklanjuti</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px; color: #64748b;">
                            Belum ada data untuk filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-layouts.print>
@else
    <x-layouts.app title="HAFIS : Laporan Temuan Pemeriksaan">
        <!-- Header with Breadcrumb & Action Buttons -->
        <div class="pb-5 mb-6 border-b border-slate-200">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('laporan.index') }}" class="hover:text-slate-800 transition-colors">Pusat Laporan</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">Laporan Temuan Pemeriksaan</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Laporan Temuan Pemeriksaan</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-rose-50 border border-rose-200 text-rose-800 font-bold">Laporan 3</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Daftar khusus item checklist berstatus Tidak Baik dari hasil inspeksi berkala beserta status progres perbaikannya.
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

        <!-- Metric Cards: Real Database Counters (Section 5) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs">
                <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider block">Total Temuan Masalah</span>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="font-mono text-xl font-bold text-rose-800 tabular-nums">{{ $rekap['total'] }}</span>
                    <span class="text-[11px] text-slate-400">item defect</span>
                </div>
            </div>

            <div class="p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs">
                <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider block">Belum Ditindaklanjuti</span>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="font-mono text-xl font-bold text-rose-800 tabular-nums">{{ $rekap['belum_ditindaklanjuti'] }}</span>
                    <span class="text-[11px] text-slate-400">perlu atensi</span>
                </div>
            </div>

            <div class="p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs">
                <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider block">Dalam Penanganan</span>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="font-mono text-xl font-bold text-amber-800 tabular-nums">{{ $rekap['dalam_proses'] }}</span>
                    <span class="text-[11px] text-slate-400">proses teknisi</span>
                </div>
            </div>

            <div class="p-3.5 bg-white border border-slate-200 rounded-xl shadow-2xs">
                <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block">Telah Selesai</span>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="font-mono text-xl font-bold text-emerald-800 tabular-nums">{{ $rekap['selesai'] }}</span>
                    <span class="text-[11px] text-slate-400">tuntas</span>
                </div>
            </div>
        </div>

        <!-- Filter Form (Section 5) -->
        <div class="mb-6 p-4 rounded-xl border border-slate-200 bg-white shadow-2xs">
            <form method="GET" action="{{ route('laporan.temuan') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Dari Tanggal -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                        <input
                            type="date"
                            name="dari_tanggal"
                            value="{{ request('dari_tanggal') }}"
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:border-slate-400 min-h-[44px]" />
                    </div>

                    <!-- Sampai Tanggal -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                        <input
                            type="date"
                            name="sampai_tanggal"
                            value="{{ request('sampai_tanggal') }}"
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

                    <!-- Status Tindak Lanjut -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status Tindak Lanjut</label>
                        <select name="status_tl" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none min-h-[44px]">
                            <option value="">Semua Status Tindak Lanjut</option>
                            <option value="belum_ada_tl" {{ request('status_tl') == 'belum_ada_tl' ? 'selected' : '' }}>Belum Dibuat TL</option>
                            <option value="belum_ditindaklanjuti" {{ request('status_tl') == 'belum_ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                            <option value="dalam_proses" {{ request('status_tl') == 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="selesai" {{ request('status_tl') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <!-- Pencarian Teks -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Cari Parameter / Catatan Temuan / Kode APAR</label>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Ketik nama item temuan (contoh: selang, nozzle, tekanan) atau kode APAR..."
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:border-slate-400 min-h-[44px]" />
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                    <span class="text-slate-500 font-medium">
                        Menampilkan <strong>{{ $rekap['terfilter'] }}</strong> temuan dari filter yang diterapkan
                    </span>

                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition min-h-[44px]">
                            <span>Terapkan Filter</span>
                        </button>

                        <a href="{{ route('laporan.temuan') }}" 
                           class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition min-h-[44px]">
                            <span>Reset Filter</span>
                        </a>
                    </div>
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
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Kode APAR</th>
                            <th class="py-3 px-3.5">Lokasi</th>
                            <th class="py-3 px-3.5">Item Parameter</th>
                            <th class="py-3 px-3.5">Hasil</th>
                            <th class="py-3 px-3.5">Catatan Temuan Masalah</th>
                            <th class="py-3 px-3.5">Pemeriksa</th>
                            <th class="py-3 px-3.5">Status Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($temuans as $idx => $t)
                            @php
                                $tl = $t->tindakLanjut;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-3.5 text-center font-mono text-slate-400">
                                    {{ $temuans->firstItem() + $idx }}
                                </td>
                                <td class="py-3 px-3.5 font-mono whitespace-nowrap">
                                    <span class="font-bold text-slate-900">
                                        {{ $t->pemeriksaan?->tanggal_pemeriksaan ? $t->pemeriksaan->tanggal_pemeriksaan->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    <a href="{{ route('apar.show', $t->pemeriksaan?->apar_id ?? 0) }}" class="text-blue-600 hover:underline">
                                        {{ $t->pemeriksaan?->apar?->kode ?? '-' }}
                                    </a>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-semibold text-slate-900">
                                        {{ $t->pemeriksaan?->apar?->gedung?->nama ?? '-' }} - Lt. {{ $t->pemeriksaan?->apar?->lantai?->nomor_lantai ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">{{ $t->pemeriksaan?->apar?->ruangan?->nama ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap font-medium text-slate-900">
                                    {{ $t->item_nama }}
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                        Tidak Baik
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-slate-700">
                                    <div class="p-2 rounded bg-rose-50/50 border border-rose-100 text-rose-900">
                                        {{ $t->catatan ?: 'Tidak ada catatan rinci dari petugas.' }}
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="font-medium text-slate-800">{{ $t->pemeriksaan?->pemeriksa_nama ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if(!$tl)
                                        <a href="{{ route('tindak-lanjut.create', ['pemeriksaan_id' => $t->pemeriksaan_id, 'pemeriksaan_detail_id' => $t->id]) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100 transition">
                                            <span>Buat Tindak Lanjut</span>
                                        </a>
                                    @elseif($tl->status === 'selesai')
                                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100">
                                            Selesai
                                        </a>
                                    @elseif($tl->status === 'dalam_proses')
                                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100">
                                            Dalam Proses
                                        </a>
                                    @else
                                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200 hover:bg-rose-100">
                                            Belum Ditindaklanjuti
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-medium">Belum ada temuan masalah pada filter yang dipilih.</p>
                                    <p class="text-xs text-slate-400 mt-1">Seluruh item checklist berada dalam kondisi baik atau sesuaikan filter pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($temuans->hasPages())
                <div class="p-3.5 border-t border-slate-100">
                    {{ $temuans->links() }}
                </div>
            @endif
        </div>
    </x-layouts.app>
@endif
