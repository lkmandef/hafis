@if($isPrint)
    <x-layouts.print 
        title="Cetak Laporan Tindak Lanjut & Pemeliharaan - HAFIS RSUD" 
        reportTitle="LAPORAN REKAPITULASI TINDAK LANJUT & PEMELIHARAAN APAR" 
        :filters="$filtersSummary">

        {{-- Baris ringkasan tunggal --}}
        <div style="margin-bottom: 10px; font-size: 8.5pt; color: #0f172a;">
            Total Tindak Lanjut: <strong>{{ $rekap['total'] }}</strong> kegiatan &nbsp;&bull;&nbsp;
            Belum Ditindaklanjuti: <strong>{{ $rekap['belum_ditindaklanjuti'] }}</strong> &nbsp;&bull;&nbsp;
            Dalam Proses: <strong>{{ $rekap['dalam_proses'] }}</strong> &nbsp;&bull;&nbsp;
            Selesai: <strong>{{ $rekap['selesai'] }}</strong>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">No</th>
                    <th>Tgl Tindakan</th>
                    <th>Kode APAR</th>
                    <th>Lokasi (Gedung / Lt / Ruang)</th>
                    <th>Sumber Temuan</th>
                    <th>Jenis Tindakan</th>
                    <th>Uraian Tindakan</th>
                    <th>Teknisi / Pelaksana</th>
                    <th>Status & Tgl Selesai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tindakLanjuts as $idx => $tl)
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="font-mono">
                            {{ $tl->tanggal_tindakan ? $tl->tanggal_tindakan->format('d/m/Y') : '-' }}
                        </td>
                        <td class="font-mono font-bold">{{ $tl->apar?->kode ?? '-' }}</td>
                        <td>
                            <strong>{{ $tl->apar?->lokasi?->nama ?? $tl->apar?->ruangan?->nama ?? '-' }}</strong>
                            <div>
                                @if($tl->apar?->lokasi)
                                    {{ $tl->apar->lokasi->keterangan ?? $tl->apar->lokasi->nama }}
                                @else
                                    {{ $tl->apar?->gedung?->nama ?? '-' }} - Lt. {{ $tl->apar?->lantai?->nomor_lantai ?? '-' }}
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($tl->pemeriksaan)
                                Inspeksi {{ $tl->pemeriksaan->tanggal_pemeriksaan?->format('d/m/Y') }}
                            @else
                                Mandiri
                            @endif
                            <div style="font-size: 8pt; color: #475569;">{{ $tl->deskripsi_temuan ?: '-' }}</div>
                        </td>
                        <td><strong>{{ $tl->jenis_label }}</strong></td>
                        <td>{{ $tl->uraian_tindakan ?: '-' }}</td>
                        <td>{{ $tl->petugas_pelaksana }}</td>
                        <td>
                            @if($tl->status === 'selesai')
                                <span class="status-normal">Selesai</span>
                                @if($tl->tanggal_selesai)
                                    <div style="font-size: 7.5pt; color: #475569;">({{ $tl->tanggal_selesai->format('d/m/Y') }})</div>
                                @endif
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
    <x-layouts.app title="HAFIS : Laporan Tindak Lanjut & Pemeliharaan">
        <!-- Header with Breadcrumb & Action Buttons -->
        <div class="pb-4 mb-4 border-b border-slate-200">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('laporan.index') }}" class="hover:text-slate-800 transition-colors">Laporan & Rekapitulasi</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">Laporan Tindak Lanjut & Pemeliharaan</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Laporan Tindak Lanjut & Pemeliharaan</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-amber-50 border border-amber-200 text-amber-800 font-bold">Laporan 4</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Rekapitulasi aktivitas perbaikan, penggantian suku cadang, pengisian ulang media, dan penanganan kerusakan unit APAR.
                    </p>
                </div>

                <div class="flex items-center gap-2 sm:shrink-0">
                    <a href="{{ request()->fullUrlWithQuery(['format' => 'print']) }}" target="_blank" rel="noopener"
                       aria-label="Cetak Laporan Tindak Lanjut & Pemeliharaan (buka di tab baru)"
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
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Tindakan</span>
                    <span class="text-base sm:text-lg font-bold text-slate-900 tabular-nums">
                        {{ $rekap['total'] }}
                        <span class="text-xs font-normal text-slate-500">tindakan</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Belum Ditindaklanjuti</span>
                    <span class="text-base sm:text-lg font-bold {{ $rekap['belum_ditindaklanjuti'] > 0 ? 'text-rose-800' : 'text-slate-800' }} tabular-nums">
                        {{ $rekap['belum_ditindaklanjuti'] }}
                        <span class="text-xs font-normal text-slate-500">menunggu</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Dalam Proses</span>
                    <span class="text-base sm:text-lg font-bold {{ $rekap['dalam_proses'] > 0 ? 'text-amber-800' : 'text-slate-800' }} tabular-nums">
                        {{ $rekap['dalam_proses'] }}
                        <span class="text-xs font-normal text-slate-500">dikerjakan</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4 last:pr-1 sm:last:pr-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Selesai / Tuntas</span>
                    <span class="text-base sm:text-lg font-bold text-emerald-800 tabular-nums">
                        {{ $rekap['selesai'] }}
                        <span class="text-xs font-normal text-slate-500">selesai</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Compact Filter Form (Section 6) -->
        <div class="mb-4 p-3 sm:p-3.5 rounded-lg border border-slate-200 bg-white shadow-2xs">
            <form method="GET" action="{{ route('laporan.tindak_lanjut') }}" class="space-y-2.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                    <!-- Dari Tanggal -->
                    <div>
                        <label for="filter_tl_dari_tanggal" class="block text-[11px] font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                        <input
                            type="date"
                            id="filter_tl_dari_tanggal"
                            name="dari_tanggal"
                            value="{{ request('dari_tanggal') }}"
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]" />
                    </div>

                    <!-- Sampai Tanggal -->
                    <div>
                        <label for="filter_tl_sampai_tanggal" class="block text-[11px] font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                        <input
                            type="date"
                            id="filter_tl_sampai_tanggal"
                            name="sampai_tanggal"
                            value="{{ request('sampai_tanggal') }}"
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]" />
                    </div>

                    <!-- Lokasi K3RS -->
                    <div>
                        <label for="filter_tl_lokasi" class="block text-[11px] font-semibold text-slate-600 mb-1">Lokasi K3RS</label>
                        <select id="filter_tl_lokasi" name="lokasi_id" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Lokasi K3RS</option>
                            @foreach($lokasis as $lok)
                                <option value="{{ $lok->id }}" {{ (string) request('lokasi_id') === (string) $lok->id ? 'selected' : '' }}>
                                    {{ $lok->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jenis Tindakan -->
                    <div>
                        <label for="filter_tl_jenis_tindakan" class="block text-[11px] font-semibold text-slate-600 mb-1">Jenis Tindakan</label>
                        <select id="filter_tl_jenis_tindakan" name="jenis_tindakan" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Jenis</option>
                            <option value="perbaikan" {{ request('jenis_tindakan') == 'perbaikan' ? 'selected' : '' }}>Perbaikan Ringan</option>
                            <option value="pengisian_ulang" {{ request('jenis_tindakan') == 'pengisian_ulang' ? 'selected' : '' }}>Pengisian Ulang (Refill)</option>
                            <option value="penggantian_komponen" {{ request('jenis_tindakan') == 'penggantian_komponen' ? 'selected' : '' }}>Ganti Komponen / Suku Cadang</option>
                            <option value="pembersihan" {{ request('jenis_tindakan') == 'pembersihan' ? 'selected' : '' }}>Pembersihan</option>
                            <option value="rekomendasi_penggantian" {{ request('jenis_tindakan') == 'rekomendasi_penggantian' ? 'selected' : '' }}>Rekomendasi Penggantian Unit</option>
                            <option value="lainnya" {{ request('jenis_tindakan') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Status Tindakan -->
                    <div>
                        <label for="filter_tl_status" class="block text-[11px] font-semibold text-slate-600 mb-1">Status Progres</label>
                        <select id="filter_tl_status" name="status" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Status</option>
                            <option value="belum_ditindaklanjuti" {{ request('status') == 'belum_ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                            <option value="dalam_proses" {{ request('status') == 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <!-- Petugas Pelaksana -->
                    <div>
                        <label for="filter_tl_petugas" class="block text-[11px] font-semibold text-slate-600 mb-1">Teknisi / Pelaksana</label>
                        <input
                            type="text"
                            id="filter_tl_petugas"
                            name="petugas_pelaksana"
                            value="{{ request('petugas_pelaksana') }}"
                            placeholder="Ketik nama teknisi..."
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]" />
                    </div>

                    <!-- Pencarian Teks -->
                    <div class="lg:col-span-2">
                        <label for="filter_tl_q" class="block text-[11px] font-semibold text-slate-600 mb-1">Cari Kode APAR / Uraian Tindakan</label>
                        <input
                            type="text"
                            id="filter_tl_q"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Ketik kode APAR atau kata kunci uraian..."
                            class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]" />
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold rounded-md text-white bg-slate-900 hover:bg-slate-800 transition min-h-[36px] cursor-pointer shadow-2xs">
                            <span>Terapkan Filter</span>
                        </button>

                        <a href="{{ route('laporan.tindak_lanjut') }}" 
                           class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md text-slate-600 bg-slate-100 hover:bg-slate-200 transition min-h-[36px]">
                            <span>Reset Filter</span>
                        </a>

                        <span class="text-slate-500 text-xs ml-2">
                            Menampilkan <strong class="text-slate-800 font-semibold tabular-nums">{{ $rekap['terfilter'] }}</strong> data tindakan (terfilter)
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
                            <th class="py-3 px-3.5">Tanggal Tindakan</th>
                            <th class="py-3 px-3.5">Kode APAR</th>
                            <th class="py-3 px-3.5">Lokasi</th>
                            <th class="py-3 px-3.5">Temuan / Sumber</th>
                            <th class="py-3 px-3.5">Jenis Tindakan</th>
                            <th class="py-3 px-3.5">Uraian Tindakan</th>
                            <th class="py-3 px-3.5">Pelaksana</th>
                            <th class="py-3 px-3.5">Status & Tgl Selesai</th>
                            <th class="py-3 pl-3.5 pr-5 sm:pr-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tindakLanjuts as $idx => $tl)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 pl-5 sm:pl-6 pr-3.5 text-center font-mono text-slate-500 tabular-nums">
                                    {{ $tindakLanjuts->firstItem() + $idx }}
                                </td>
                                <td class="py-3 px-3.5 font-mono whitespace-nowrap">
                                    <span class="font-bold text-slate-900 tabular-nums">{{ $tl->tanggal_tindakan ? $tl->tanggal_tindakan->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td class="py-3 px-3.5 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    <a href="{{ route('apar.show', $tl->apar_id ?? 0) }}" class="text-blue-600 hover:underline">
                                        {{ $tl->apar?->kode ?? '-' }}
                                    </a>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $tl->apar?->lokasi?->nama ?? $tl->apar?->ruangan?->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">
                                        @if($tl->apar?->lokasi)
                                            {{ $tl->apar->lokasi->keterangan ?? $tl->apar->lokasi->nama }}
                                        @else
                                            {{ $tl->apar?->gedung?->nama ?? '-' }} - Lt. {{ $tl->apar?->lantai?->nomor_lantai ?? '-' }}
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 max-w-xs">
                                    <div class="font-medium text-slate-800">{{ $tl->deskripsi_temuan ?: '-' }}</div>
                                    <div class="text-[10px] text-slate-500">
                                        @if($tl->pemeriksaan)
                                            Dari Inspeksi {{ $tl->pemeriksaan->tanggal_pemeriksaan?->format('d/m/Y') }}
                                        @else
                                            Temuan Langsung
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-800">
                                        {{ $tl->jenis_label }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 max-w-[200px]">
                                    <p class="text-slate-600 text-xs break-words line-clamp-2" title="{{ $tl->uraian_tindakan }}">
                                        {{ $tl->uraian_tindakan ?: '-' }}
                                    </p>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="font-medium text-slate-900">{{ $tl->petugas_pelaksana }}</span>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if($tl->status === 'selesai')
                                        <x-badge variant="success" size="sm">
                                            Selesai
                                        </x-badge>
                                        <div class="text-[10px] text-slate-500 font-mono mt-0.5 tabular-nums">
                                            {{ $tl->tanggal_selesai ? $tl->tanggal_selesai->format('d/m/Y') : '-' }}
                                        </div>
                                    @elseif($tl->status === 'dalam_proses')
                                        <x-badge variant="warning" size="sm">
                                            Dalam Proses
                                        </x-badge>
                                    @else
                                        <x-badge variant="danger" size="sm">
                                            Belum Ditindaklanjuti
                                        </x-badge>
                                    @endif
                                </td>
                                <td class="py-3 pl-3.5 pr-5 sm:pr-6 text-center whitespace-nowrap">
                                    <a href="{{ route('tindak-lanjut.show', $tl->id) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition min-h-[32px]">
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-500">
                                    <p class="text-sm font-semibold text-slate-700">Belum ada data untuk filter yang dipilih.</p>
                                    <p class="text-xs text-slate-500 mt-1">Coba sesuaikan pilihan filter atau kata kunci pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tindakLanjuts->hasPages())
                <div class="p-3.5 border-t border-slate-100">
                    {{ $tindakLanjuts->links() }}
                </div>
            @endif
        </div>
    </x-layouts.app>
@endif