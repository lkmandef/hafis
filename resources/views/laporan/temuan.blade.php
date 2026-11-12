@if($isPrint)
    <x-layouts.print 
        title="Cetak Laporan Temuan Pemeriksaan APAR - HAFIS RSUD" 
        reportTitle="LAPORAN TEMUAN KERUSAKAN CHECKLIST PEMERIKSAAN APAR" 
        :filters="$filtersSummary">

        {{-- Baris Rekapitulasi Formal & Tanggal di Atas Tabel --}}
        <div class="report-meta-bar">
            <div class="report-summary-formal">
                Total Temuan Kerusakan: <strong>{{ $rekap['total'] }} item</strong> (Belum Ditindaklanjuti: {{ $rekap['belum_ditindaklanjuti'] }}, Dalam Penanganan: {{ $rekap['dalam_proses'] }}, Telah Diselesaikan: {{ $rekap['selesai'] }})
            </div>
            <div class="report-date-formal">
                {{ now()->timezone('Asia/Makassar')->locale('id')->translatedFormat('l, d F Y') }}
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
                            <strong>{{ $t->pemeriksaan?->apar?->lokasi?->nama ?? $t->pemeriksaan?->apar?->ruangan?->nama ?? '-' }}</strong>
                            <div>
                                @if($t->pemeriksaan?->apar?->lokasi)
                                    {{ $t->pemeriksaan->apar->lokasi->keterangan ?? $t->pemeriksaan->apar->lokasi->nama }}
                                @else
                                    {{ $t->pemeriksaan?->apar?->gedung?->nama ?? '-' }} - Lt. {{ $t->pemeriksaan?->apar?->lantai?->nomor_lantai ?? '-' }}
                                @endif
                            </div>
                        </td>
                        <td><strong>{{ $t->item_nama }}</strong></td>
                        <td><span class="status-kritis">Tidak Baik</span></td>
                        <td>{{ $t->catatan ?: 'Tidak ada keterangan rinci' }}</td>
                        <td>{{ $t->pemeriksaan?->pemeriksa_nama ?? '-' }}</td>
                        <td>
                            @if(!$tl)
                                <span>Belum Dibuat TL</span>
                            @elseif($tl->status === 'selesai')
                                <span class="status-normal">Selesai</span>
                            @elseif($tl->status === 'dalam_proses')
                                <span class="status-perhatian">Dalam Proses</span>
                            @else
                                <span class="status-kritis">Belum Ditindaklanjuti</span>
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
        <div class="pb-4 mb-4 border-b border-slate-200">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('laporan.index') }}" class="hover:text-slate-800 transition-colors">Laporan & Rekapitulasi</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">Laporan Temuan Pemeriksaan</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Laporan Temuan Pemeriksaan</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-rose-50 border border-rose-200 text-rose-800 font-bold">Laporan 3</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Daftar khusus item checklist berstatus Tidak Baik dari hasil inspeksi berkala beserta status progres perbaikannya.
                    </p>
                </div>

                <div class="flex items-center gap-2 sm:shrink-0">
                    <a href="{{ request()->fullUrlWithQuery(['format' => 'print']) }}" target="_blank" rel="noopener"
                       aria-label="Cetak Laporan Temuan Pemeriksaan (buka di tab baru)"
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
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Temuan Masalah</span>
                    <span class="text-base sm:text-lg font-bold text-rose-800 tabular-nums">
                        {{ $rekap['total'] }}
                        <span class="text-xs font-normal text-slate-500">item temuan</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Belum Ditindaklanjuti</span>
                    <span class="text-base sm:text-lg font-bold {{ $rekap['belum_ditindaklanjuti'] > 0 ? 'text-rose-800' : 'text-slate-800' }} tabular-nums">
                        {{ $rekap['belum_ditindaklanjuti'] }}
                        <span class="text-xs font-normal text-slate-500">perlu atensi</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Dalam Penanganan</span>
                    <span class="text-base sm:text-lg font-bold {{ $rekap['dalam_proses'] > 0 ? 'text-amber-800' : 'text-slate-800' }} tabular-nums">
                        {{ $rekap['dalam_proses'] }}
                        <span class="text-xs font-normal text-slate-500">proses teknisi</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4 last:pr-1 sm:last:pr-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Telah Selesai</span>
                    <span class="text-base sm:text-lg font-bold text-emerald-800 tabular-nums">
                        {{ $rekap['selesai'] }}
                        <span class="text-xs font-normal text-slate-500">tuntas</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Compact Filter Form (Section 5) -->
        <div class="mb-4 p-3 sm:p-3.5 rounded-lg border border-slate-200 bg-white shadow-2xs">
            <form method="GET" action="{{ route('laporan.temuan') }}" class="space-y-2.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                    <!-- Dari Tanggal -->
                    <div>
                        <label for="filter_tem_dari_tanggal" class="block text-[11px] font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                        <input
                            type="date"
                            id="filter_tem_dari_tanggal"
                            name="dari_tanggal"
                            value="{{ request('dari_tanggal') }}"
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]" />
                    </div>

                    <!-- Sampai Tanggal -->
                    <div>
                        <label for="filter_tem_sampai_tanggal" class="block text-[11px] font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                        <input
                            type="date"
                            id="filter_tem_sampai_tanggal"
                            name="sampai_tanggal"
                            value="{{ request('sampai_tanggal') }}"
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]" />
                    </div>

                    <!-- Lokasi K3RS -->
                    <div>
                        <label for="filter_tem_lokasi" class="block text-[11px] font-semibold text-slate-600 mb-1">Lokasi K3RS</label>
                        <select id="filter_tem_lokasi" name="lokasi_id" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Lokasi K3RS</option>
                            @foreach($lokasis as $lok)
                                <option value="{{ $lok->id }}" {{ (string) request('lokasi_id') === (string) $lok->id ? 'selected' : '' }}>
                                    {{ $lok->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Tindak Lanjut -->
                    <div>
                        <label for="filter_tem_status_tl" class="block text-[11px] font-semibold text-slate-600 mb-1">Status Tindak Lanjut</label>
                        <select id="filter_tem_status_tl" name="status_tl" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Status Tindak Lanjut</option>
                            <option value="belum_ada_tl" {{ request('status_tl') == 'belum_ada_tl' ? 'selected' : '' }}>Belum Dibuat TL</option>
                            <option value="belum_ditindaklanjuti" {{ request('status_tl') == 'belum_ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                            <option value="dalam_proses" {{ request('status_tl') == 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="selesai" {{ request('status_tl') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <!-- Pencarian Teks -->
                    <div class="lg:col-span-4">
                        <label for="filter_tem_q" class="block text-[11px] font-semibold text-slate-600 mb-1">Cari Parameter / Catatan Temuan / Kode APAR</label>
                        <input
                            type="text"
                            id="filter_tem_q"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Ketik nama item temuan (contoh: selang, nozzle, tekanan) atau kode APAR..."
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]" />
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold rounded-md text-white bg-slate-900 hover:bg-slate-800 transition min-h-[36px] cursor-pointer shadow-2xs">
                            <span>Terapkan Filter</span>
                        </button>

                        <a href="{{ route('laporan.temuan') }}" 
                           class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md text-slate-600 bg-slate-100 hover:bg-slate-200 transition min-h-[36px]">
                            <span>Reset Filter</span>
                        </a>

                        <span class="text-slate-500 text-xs ml-2">
                            Menampilkan <strong class="text-slate-800 font-semibold tabular-nums">{{ $rekap['terfilter'] }}</strong> temuan (terfilter)
                        </span>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Container (Horizontal Scroll for Responsive) -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 min-w-[960px]">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-600 font-bold">
                        <tr>
                            <th class="py-3 pl-5 sm:pl-6 pr-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Kode APAR</th>
                            <th class="py-3 px-3.5">Lokasi</th>
                            <th class="py-3 px-3.5">Item Parameter</th>
                            <th class="py-3 px-3.5">Hasil</th>
                            <th class="py-3 px-3.5">Catatan Temuan Masalah</th>
                            <th class="py-3 px-3.5">Pemeriksa</th>
                            <th class="py-3 pl-3.5 pr-5 sm:pr-6">Status Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($temuans as $idx => $t)
                            @php
                                $tl = $t->tindakLanjut;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 pl-5 sm:pl-6 pr-3.5 text-center font-mono text-slate-500 tabular-nums">
                                    {{ $temuans->firstItem() + $idx }}
                                </td>
                                <td class="py-3 px-3.5 font-mono whitespace-nowrap">
                                    <span class="font-bold text-slate-900 tabular-nums">
                                        {{ $t->pemeriksaan?->tanggal_pemeriksaan ? $t->pemeriksaan->tanggal_pemeriksaan->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    <a href="{{ route('apar.show', $t->pemeriksaan?->apar_id ?? 0) }}" class="text-blue-600 hover:underline">
                                        {{ $t->pemeriksaan?->apar?->kode ?? '-' }}
                                    </a>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $t->pemeriksaan?->apar?->lokasi?->nama ?? $t->pemeriksaan?->apar?->ruangan?->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">
                                        @if($t->pemeriksaan?->apar?->lokasi)
                                            {{ $t->pemeriksaan->apar->lokasi->keterangan ?? $t->pemeriksaan->apar->lokasi->nama }}
                                        @else
                                            {{ $t->pemeriksaan?->apar?->gedung?->nama ?? '-' }} - Lt. {{ $t->pemeriksaan?->apar?->lantai?->nomor_lantai ?? '-' }}
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap font-medium text-slate-900">
                                    {{ $t->item_nama }}
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <x-badge variant="danger" size="sm">
                                        Tidak Baik
                                    </x-badge>
                                </td>
                                <td class="py-3 px-3.5 text-slate-700 max-w-xs">
                                    <div class="p-2 rounded bg-rose-50/50 border border-rose-100 text-rose-900 text-xs break-words">
                                        {{ $t->catatan ?: 'Tidak ada catatan rinci dari petugas.' }}
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="font-medium text-slate-800">{{ $t->pemeriksaan?->pemeriksa_nama ?? '-' }}</span>
                                </td>
                                <td class="py-3 pl-3.5 pr-5 sm:pr-6 whitespace-nowrap">
                                    @if(!$tl)
                                        <a href="{{ route('tindak-lanjut.create', ['pemeriksaan_id' => $t->pemeriksaan_id, 'pemeriksaan_detail_id' => $t->id]) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100 transition min-h-[30px]">
                                            <span>Buat Tindak Lanjut</span>
                                        </a>
                                    @elseif($tl->status === 'selesai')
                                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}" class="inline-flex items-center">
                                            <x-badge variant="success" size="sm">Selesai</x-badge>
                                        </a>
                                    @elseif($tl->status === 'dalam_proses')
                                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}" class="inline-flex items-center">
                                            <x-badge variant="warning" size="sm">Dalam Proses</x-badge>
                                        </a>
                                    @else
                                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}" class="inline-flex items-center">
                                            <x-badge variant="danger" size="sm">Belum Ditindaklanjuti</x-badge>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-500">
                                    <p class="text-sm font-semibold text-slate-700">Belum ada temuan masalah pada filter yang dipilih.</p>
                                    <p class="text-xs text-slate-500 mt-1">Seluruh item checklist berada dalam kondisi baik atau sesuaikan filter pencarian.</p>
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