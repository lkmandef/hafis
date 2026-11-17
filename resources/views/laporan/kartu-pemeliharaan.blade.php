@if($isPrint)
    <x-layouts.print 
        title="Cetak Kartu Ceklist Pemeliharaan APAR - HAFIS RSUD" 
        reportTitle="KARTU CEKLIST PEMELIHARAAN APAR (TAHUNAN)" 
        :filters="$filtersSummary"
        :orientation="$orientation">

        <style>
            .kartu-container {
                page-break-after: always;
                break-after: page;
            }
            .kartu-container:last-child {
                page-break-after: auto;
                break-after: auto;
            }
            .header-identitas-box {
                border: 1.5px solid #0f172a;
                border-radius: 4px;
                padding: 8px 12px;
                margin-bottom: 10px;
                background-color: #f8fafc;
                font-size: 8pt;
            }
            .header-identitas-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 6px 16px;
            }
            .identitas-row {
                display: flex;
                align-items: baseline;
                gap: 6px;
            }
            .identitas-label {
                width: 120px;
                font-weight: 600;
                color: #334155;
            }
            .identitas-value {
                flex: 1;
                font-weight: 700;
                color: #0f172a;
            }
            .matrix-table {
                width: 100%;
                table-layout: fixed;
                border-collapse: collapse;
                margin-bottom: 8px;
                font-size: 7.5pt;
            }
            .matrix-table th, .matrix-table td {
                border: 1px solid #0f172a;
                padding: 4px 5px;
                text-align: center;
                vertical-align: middle;
                word-wrap: break-word;
            }
            .matrix-table th {
                background-color: #f1f5f9;
                font-weight: 700;
                color: #0f172a;
                line-height: 1.2;
            }
            .cell-symbol-baik {
                font-weight: 800;
                font-size: 9pt;
                color: #047857;
            }
            .cell-symbol-rusak {
                font-weight: 800;
                font-size: 9pt;
                color: #b91c1c;
            }
            .cell-symbol-lewati {
                font-weight: 700;
                color: #64748b;
            }
            .alert-banner-print {
                border: 1px solid #b91c1c;
                background: #fef2f2;
                color: #991b1b;
                padding: 4px 8px;
                font-size: 7.5pt;
                margin-bottom: 8px;
                border-radius: 3px;
                font-weight: 600;
            }
            .info-banner-print {
                border: 1px solid #0369a1;
                background: #f0f9ff;
                color: #075985;
                padding: 4px 8px;
                font-size: 7.5pt;
                margin-bottom: 8px;
                border-radius: 3px;
                font-weight: 600;
            }
            .legenda-print-box {
                border: 1px solid #cbd5e1;
                padding: 4px 8px;
                font-size: 7pt;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                background: #fafafa;
                border-radius: 3px;
            }
            @media print {
                .header-identitas-box {
                    border: 1.5px solid #000000 !important;
                    background: transparent !important;
                }
                .matrix-table th, .matrix-table td {
                    border: 1px solid #000000 !important;
                }
                .matrix-table th {
                    background: transparent !important;
                    color: #000000 !important;
                }
                .cell-symbol-baik, .cell-symbol-rusak, .cell-symbol-lewati {
                    color: #000000 !important;
                }
                .legenda-print-box {
                    border: 1px solid #000000 !important;
                    background: transparent !important;
                }
            }
        </style>

        @forelse($cardsData as $cardIdx => $card)
            <div class="kartu-container">
                {{-- Banner Kasus Tepi --}}
                @if($card['meta']['is_replaced'])
                    <div class="alert-banner-print">
                        <strong>STATUS UNIT:</strong> Unit APAR ini telah dinonaktifkan dan digantikan oleh unit <strong>{{ $card['meta']['replaced_by_kode'] ?? 'Unit Baru' }}</strong> per tanggal {{ $card['meta']['replaced_at'] ?? '-' }}. Riwayat di bawah merupakan arsip inspeksi fisik.
                    </div>
                @endif

                @if($card['meta']['is_stok'])
                    <div class="info-banner-print">
                        <strong>KETERANGAN PENEMPATAN:</strong> Unit berada di Stok Persediaan K3RS (Tidak Terpasang di Ruangan).
                    </div>
                @endif

                @if($card['meta']['is_soft_deleted'])
                    <div class="alert-banner-print">
                        <strong>PERHATIAN ARSIP:</strong> Unit APAR ini telah dihapus dari sistem aktif (Soft-Deleted). Data ditampilkan untuk keperluan audit historis.
                    </div>
                @endif

                @if($card['meta']['is_pra_digitalisasi_year'])
                    <div class="info-banner-print">
                        <strong>PERIODE PRA-DIGITALISASI:</strong> Tahun {{ $tahun }} berada sebelum dimulainya pencatatan operasional resmi HAFIS ({{ config('hafis.operational_start_month', 11) }}/{{ config('hafis.operational_start_year', 2026) }}).
                    </div>
                @endif

                {{-- Kepala Kartu Pemeliharaan --}}
                <div class="header-identitas-box">
                    <div class="header-identitas-grid">
                        <div class="space-y-1">
                            <div class="identitas-row">
                                <span class="identitas-label">Nomor / Kode APAR</span>
                                <span class="identitas-value font-mono">{{ $card['meta']['kode'] }} {{ $card['meta']['nomor_register'] ? "({$card['meta']['nomor_register']})" : '' }}</span>
                            </div>
                            <div class="identitas-row">
                                <span class="identitas-label">Ruangan / Lokasi</span>
                                <span class="identitas-value">{{ $card['meta']['lokasi_lengkap'] }}</span>
                            </div>
                            <div class="identitas-row">
                                <span class="identitas-label">Tahun Periode</span>
                                <span class="identitas-value">Tahun {{ $tahun }}</span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <div class="identitas-row">
                                <span class="identitas-label">Jenis Media</span>
                                <span class="identitas-value">{{ $card['meta']['jenis_media'] }}</span>
                            </div>
                            <div class="identitas-row">
                                <span class="identitas-label">Kapasitas Tabung</span>
                                <span class="identitas-value font-mono">{{ str_ends_with(strtolower(trim((string) $card['meta']['kapasitas'])), 'kg') ? $card['meta']['kapasitas'] : $card['meta']['kapasitas'] . ' kg' }}</span>
                            </div>
                            <div class="identitas-row">
                                <span class="identitas-label">Masa Berlaku (Exp)</span>
                                <span class="identitas-value font-mono {{ $card['meta']['is_expired'] ? 'text-red-700 font-bold' : '' }}">
                                    {{ $card['meta']['tanggal_kadaluarsa'] }} {{ $card['meta']['is_expired'] ? '(Kedaluwarsa)' : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Matriks 12 Bulan x N Kolom Checklist --}}
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th style="width: 14%;">Bulan</th>
                            @foreach($columns as $col)
                                <th>
                                    <div>{{ $col['title'] }}</div>
                                </th>
                            @endforeach
                            <th style="width: 18%;">Petugas Pemeriksa</th>
                            <th style="width: 12%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($card['matrix'] as $bulanNum => $row)
                            <tr>
                                <td style="text-align: left; padding-left: 8px; font-weight: 600;">
                                    {{ $row['bulan_nama'] }}
                                </td>
                                @foreach($columns as $col)
                                    @php
                                        $cell = $row['cells'][$col['key']];
                                        $sym = $cell['symbol'];
                                        $symClass = match($sym) {
                                            $symbols['baik'] => 'cell-symbol-baik',
                                            $symbols['tidak_baik'] => 'cell-symbol-rusak',
                                            $symbols['tidak_diperiksa'] => 'cell-symbol-lewati',
                                            default => '',
                                        };
                                    @endphp
                                    <td class="{{ $symClass }}">
                                        {{ $sym ?: '' }}
                                    </td>
                                @endforeach
                                <td style="text-align: left; padding-left: 6px;">
                                    @if($row['has_pemeriksaan'])
                                        <div style="font-weight: 600;">{{ $row['pemeriksa_nama'] }}</div>
                                        <div style="font-size: 6.5pt; color: #475569;">
                                            {{ $row['tanggal_pemeriksaan'] }}
                                            @if($row['is_terlambat'])
                                                <span style="color: #b45309; font-weight: 700;">[Terlambat]</span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: #94a3b8;">–</span>
                                    @endif
                                </td>
                                <td style="font-size: 7pt; color: #334155;">
                                    @if($row['has_pemeriksaan'])
                                        {{ $row['is_terlambat'] ? 'Selesai Terlambat' : 'Tepat Waktu' }}
                                    @elseif($row['is_pra_digitalisasi'])
                                        <span style="color: #64748b;">Pra-Digital</span>
                                    @else
                                        <span style="color: #94a3b8;">Kosong</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Legenda Kartu --}}
                <div class="legenda-print-box">
                    <div>
                        <strong>Keterangan Simbol:</strong>
                        <span style="margin-left: 8px;"><strong class="cell-symbol-baik">{{ $symbols['baik'] }}</strong> = Kondisi Baik / Laik</span>
                        <span style="margin-left: 12px;"><strong class="cell-symbol-rusak">{{ $symbols['tidak_baik'] }}</strong> = Tidak Baik / Rusak</span>
                        <span style="margin-left: 12px;"><strong class="cell-symbol-lewati">{{ $symbols['tidak_diperiksa'] }}</strong> = Dilewati / Tidak Diperiksa</span>
                        <span style="margin-left: 12px;"><strong style="color: #64748b;">(Kosong)</strong> = Belum Ada Inspeksi</span>
                    </div>
                    <div style="font-size: 6.5pt; color: #475569;">
                        Kartu Kendali Pemeliharaan Berkala Instalasi K3RS &bull; Dokumen Sah Rumah Sakit
                    </div>
                </div>
            </div>
        @empty
            <div style="padding: 30px; text-align: center; color: #64748b;">
                Tidak ada data APAR yang memenuhi kriteria filter cetak.
            </div>
        @endforelse
    </x-layouts.print>
@else
    <x-layouts.app title="HAFIS : Laporan Ceklist Pemeliharaan APAR">
        {{-- Vendor Select2 CSS (Offline & Cepat) --}}
        <link rel="stylesheet" href="{{ asset('vendor/select2/select2.min.css') }}">
        <style>
            /* Custom Select2 Theme - HAFIS Clean Design System */
            #apar-select-wrapper {
                position: relative !important;
            }
            #apar-select-wrapper .select2-container {
                width: 100% !important;
                display: block !important;
            }
            #apar-select-wrapper .select2-container .select2-selection--single {
                height: 38px !important;
                min-height: 38px !important;
                border: 1px solid #cbd5e1 !important; /* border-slate-300 */
                border-radius: 0.375rem !important; /* rounded-md */
                padding: 4px 10px !important;
                background-color: #ffffff !important;
                display: flex !important;
                align-items: center !important;
                transition: all 0.15s ease-in-out !important;
            }
            #apar-select-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: normal !important;
                color: #0f172a !important; /* text-slate-900 */
                font-size: 0.75rem !important; /* text-xs */
                font-weight: 500 !important;
                padding-left: 0 !important;
                padding-right: 24px !important;
                width: 100% !important;
            }
            #apar-select-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px !important;
                right: 8px !important;
                display: flex !important;
                align-items: center !important;
            }
            #apar-select-wrapper .select2-container--default.select2-container--open .select2-selection--single,
            #apar-select-wrapper .select2-container--default .select2-selection--single:focus {
                border-color: #64748b !important;
                box-shadow: 0 0 0 1px #64748b !important;
                outline: none !important;
            }
            /* Dropdown Container Wrapper (hanya pembungkus dropdown, bukan input select) */
            #apar-select-wrapper span.select2-container:not(.select2) {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: auto !important;
                width: 100% !important;
                height: 0 !important;
                pointer-events: none !important;
                z-index: 50 !important;
            }
            /* Kunci dropdown tepat di bawah input, selebar field, dan tidak melompat ke atas */
            #apar-select-wrapper .select2-dropdown {
                position: absolute !important;
                top: 40px !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                bottom: auto !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 0.5rem !important; /* rounded-lg */
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.03) !important;
                font-size: 0.75rem !important;
                z-index: 50 !important;
                overflow: hidden !important;
                background-color: #ffffff !important;
                pointer-events: auto !important;
            }
            #apar-select-wrapper .select2-dropdown--above,
            #apar-select-wrapper .select2-dropdown--below {
                top: 40px !important;
                bottom: auto !important;
                border-radius: 0.5rem !important;
                border: 1px solid #e2e8f0 !important;
            }
            .select2-search--dropdown {
                padding: 10px 12px !important;
                background-color: #f8fafc !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }
            .select2-search--dropdown .select2-search__field {
                border: 1px solid #cbd5e1 !important;
                border-radius: 0.375rem !important;
                padding: 7px 12px !important;
                font-size: 0.75rem !important;
                outline: none !important;
                width: 100% !important;
                background-color: #ffffff !important;
            }
            .select2-search--dropdown .select2-search__field:focus {
                border-color: #0f172a !important;
                box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.12) !important;
            }
            .select2-results__options {
                max-height: 280px !important;
                padding: 4px !important;
            }
            .select2-results__option {
                padding: 8px 12px !important;
                font-size: 0.75rem !important;
                border-radius: 0.375rem !important;
                margin-bottom: 2px !important;
                border-bottom: none !important;
                transition: background-color 0.1s ease !important;
            }
            /* Clean Light Highlight / Hover State */
            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: #f1f5f9 !important; /* bg-slate-100 */
                color: #0f172a !important; /* text-slate-900 */
            }
            .select2-container--default .select2-results__option[aria-selected=true] {
                background-color: #eff6ff !important; /* bg-blue-50 */
                color: #1d4ed8 !important; /* text-blue-700 */
                font-weight: 600 !important;
            }
        </style>
        <!-- Header with Breadcrumb & Action Buttons -->
        <div class="pb-4 mb-4 border-b border-slate-200">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('laporan.index') }}" class="hover:text-slate-800 transition-colors">Laporan & Rekapitulasi</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">Ceklist Pemeliharaan APAR</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Ceklist Pemeliharaan APAR</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-700 font-bold">Laporan 6</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Kartu kendali pemeliharaan tahunan digital per unit APAR: matriks inspeksi 12 bulan kalender, riwayat checklist fisik K3RS, dan status masa pakai tabung.
                    </p>
                </div>

                <div class="flex items-center gap-2 sm:shrink-0">
                    @if($selectedApar)
                        <a href="{{ route('laporan.kartu-pemeliharaan', array_merge(request()->query(), ['format' => 'print'])) }}" target="_blank" rel="noopener"
                           aria-label="Cetak Kartu Ceklist Pemeliharaan APAR (buka di tab baru)"
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[38px] shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            <span>Cetak Kartu (A4)</span>
                        </a>

                        <a href="{{ route('laporan.kartu-pemeliharaan', array_merge(request()->query(), ['format' => 'csv'])) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 transition min-h-[38px] shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span>Export CSV (Excel)</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Alert Notifikasi Session --}}
        @if(session('warning'))
            <div class="mb-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-3">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <!-- Compact Summary Bar (Document-Oriented) -->
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 sm:p-3 mb-4 overflow-x-auto">
            <div class="flex items-center divide-x divide-slate-200 min-w-max sm:min-w-0">
                <div class="flex-1 px-3 sm:px-4 first:pl-1 sm:first:pl-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Bulan Terperiksa</span>
                    <span class="text-base sm:text-lg font-bold text-slate-900 tabular-nums">
                        {{ $rekapTahunan['terinspeksi'] ?? 0 }}
                        <span class="text-xs font-normal text-slate-500">/ 12 bulan</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Kepatuhan Tahunan</span>
                    <span class="text-base sm:text-lg font-bold text-emerald-800 tabular-nums">
                        {{ round((($rekapTahunan['terinspeksi'] ?? 0) / 12) * 100) }}%
                        <span class="text-xs font-normal text-slate-500">terpenuhi</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Kondisi Baik</span>
                    <span class="text-base sm:text-lg font-bold text-emerald-800 tabular-nums">
                        {{ $rekapTahunan['baik'] ?? 0 }}
                        <span class="text-xs font-normal text-slate-500">bulan aman</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Perlu Tindakan</span>
                    <span class="text-base sm:text-lg font-bold {{ ($rekapTahunan['perlu_tindakan'] ?? 0) > 0 ? 'text-rose-800' : 'text-slate-800' }} tabular-nums">
                        {{ $rekapTahunan['perlu_tindakan'] ?? 0 }}
                        <span class="text-xs font-normal text-slate-500">ada temuan</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4 last:pr-1 sm:last:pr-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Belum Diperiksa</span>
                    <span class="text-base sm:text-lg font-bold text-slate-700 tabular-nums">
                        {{ $rekapTahunan['belum_inspeksi'] ?? 12 }}
                        <span class="text-xs font-normal text-slate-500">sisa siklus</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Compact Filter Form (Section 3) -->
        <div class="mb-4 p-3 sm:p-3.5 rounded-lg border border-slate-200 bg-white shadow-2xs">
            <form method="GET" action="{{ route('laporan.kartu-pemeliharaan') }}" id="filterForm" class="space-y-2.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5">
                    <!-- Cari / Pilih Unit APAR -->
                    <div class="lg:col-span-2">
                        <label for="apar_id" class="block text-[11px] font-semibold text-slate-600 mb-1 flex items-center justify-between">
                            <span>Pilih Unit APAR</span>
                            <span class="text-[10px] text-slate-400 font-normal">({{ $aparsDropdown->count() }} unit)</span>
                        </label>
                        <div class="relative" id="apar-select-wrapper">
                            <select name="apar_id" id="apar_id" class="w-full">
                                @if($selectedApar && $selectedApar->trashed())
                                    <option value="{{ $selectedApar->id }}" selected
                                            data-kode="{{ $selectedApar->kode }}"
                                            data-arsip="true"
                                            data-lokasi="{{ $selectedApar->lokasi_lengkap }}"
                                            data-media="{{ $selectedApar->jenis_media }}"
                                            data-kapasitas="{{ $selectedApar->kapasitas }}">
                                        {{ $selectedApar->kode }} [TERHAPUS / ARSIP] — {{ $selectedApar->lokasi_lengkap }}
                                    </option>
                                @endif
                                @forelse($aparsDropdown as $aItem)
                                    <option value="{{ $aItem->id }}" 
                                            {{ ($selectedApar && $selectedApar->id == $aItem->id) ? 'selected' : '' }}
                                            data-kode="{{ $aItem->kode }}"
                                            data-lokasi="{{ $aItem->lokasi_lengkap }}"
                                            data-media="{{ $aItem->jenis_media }}"
                                            data-kapasitas="{{ $aItem->kapasitas }}">
                                        {{ $aItem->kode }} — {{ $aItem->lokasi_lengkap }} ({{ $aItem->jenis_media }} {{ $aItem->kapasitas }})
                                    </option>
                                @empty
                                    <option value="">Tidak ada unit APAR</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <!-- Filter Gedung -->
                    <div class="lg:col-span-2">
                        <label for="gedung_id" class="block text-[11px] font-semibold text-slate-600 mb-1 flex items-center justify-between">
                            <span>Filter Gedung</span>
                            @if(request('gedung_id'))
                                <a href="{{ route('laporan.kartu-pemeliharaan', ['tahun' => $tahun]) }}" class="text-[10px] text-rose-600 hover:underline">Reset</a>
                            @endif
                        </label>
                        <select name="gedung_id" id="gedung_id" onchange="onGedungChange()"
                                class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Gedung ({{ $gedungs->count() }} Gedung)</option>
                            @foreach($gedungs as $g)
                                <option value="{{ $g->id }}" {{ request('gedung_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Lantai -->
                    <div class="lg:col-span-1">
                        <label for="lantai_id" class="block text-[11px] font-semibold text-slate-600 mb-1 flex items-center justify-between">
                            <span>Filter Lantai</span>
                            @if(request('lantai_id'))
                                <span class="text-[10px] text-emerald-600 font-semibold">Aktif</span>
                            @endif
                        </label>
                        <select name="lantai_id" id="lantai_id" onchange="resetAparAndSubmit()"
                                class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px]">
                            <option value="">Semua Lantai</option>
                            @foreach($lantais as $l)
                                <option value="{{ $l->id }}" {{ request('lantai_id') == $l->id ? 'selected' : '' }}>
                                    Lt. {{ $l->nomor_lantai }} ({{ $l->nama }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Kalender -->
                    <div class="lg:col-span-1">
                        <label for="tahun" class="block text-[11px] font-semibold text-slate-600 mb-1">Tahun Inspeksi</label>
                        <select name="tahun" id="tahun" onchange="this.form.submit()"
                                class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-md focus:bg-white focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[38px] font-semibold text-slate-800">
                            @foreach($tahunList as $tOpt)
                                <option value="{{ $tOpt }}" {{ $tahun == $tOpt ? 'selected' : '' }}>
                                    Tahun {{ $tOpt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold rounded-md text-white bg-slate-900 hover:bg-slate-800 transition min-h-[36px] cursor-pointer shadow-2xs">
                            <span>Terapkan Filter</span>
                        </button>

                        <a href="{{ route('laporan.kartu-pemeliharaan') }}" 
                           class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md text-slate-600 bg-slate-100 hover:bg-slate-200 transition min-h-[36px]">
                            <span>Reset Filter</span>
                        </a>

                        <span class="text-slate-500 text-xs ml-2 tabular-nums">
                            Menampilkan kartu untuk <strong class="text-slate-800 font-semibold">{{ $selectedApar ? $selectedApar->kode : '-' }}</strong> ({{ $aparsDropdown->count() }} unit tersedia)
                        </span>
                    </div>

                    @if(request()->filled('gedung_id') || request()->filled('lantai_id'))
                        <div class="flex items-center gap-2">
                            <a href="{{ route('laporan.kartu-pemeliharaan', ['format' => 'print', 'mode' => 'batch', 'gedung_id' => request('gedung_id'), 'lantai_id' => request('lantai_id'), 'tahun' => $tahun]) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-2xs transition-colors min-h-[36px]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                <span>Cetak Massal Filter Ini (A4)</span>
                            </a>

                            <a href="{{ route('laporan.kartu-pemeliharaan', ['format' => 'csv', 'mode' => 'batch', 'gedung_id' => request('gedung_id'), 'lantai_id' => request('lantai_id'), 'tahun' => $tahun]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors min-h-[36px]">
                                <span>CSV Rekap Massal</span>
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        @if($selectedApar && !empty($cardsData))
            @php $currentCard = $cardsData[0]; @endphp

            <!-- Lembar Kartu Pemeliharaan Digital -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-2xs overflow-hidden mb-6">
                {{-- Banner Kasus Tepi di Web --}}
                @if($currentCard['meta']['is_replaced'])
                    <div class="px-5 py-3 bg-amber-50 border-b border-amber-200 text-amber-900 text-xs flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <strong>Unit Sudah Diganti:</strong> APAR ini telah dinonaktifkan dan digantikan oleh unit <strong>{{ $currentCard['meta']['replaced_by_kode'] ?? 'Unit Baru' }}</strong> per tanggal {{ $currentCard['meta']['replaced_at'] ?? '-' }}. Riwayat di bawah merupakan arsip kepatuhan sebelum penggantian.
                        </div>
                    </div>
                @endif

                @if($currentCard['meta']['is_stok'])
                    <div class="px-5 py-3 bg-blue-50 border-b border-blue-200 text-blue-900 text-xs flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <strong>Unit Cadangan / Stok:</strong> Tabung ini tercatat di persediaan K3RS dan tidak terpasang di denah operasional aktif.
                        </div>
                    </div>
                @endif

                @if($currentCard['meta']['is_soft_deleted'])
                    <div class="px-5 py-3 bg-rose-50 border-b border-rose-200 text-rose-900 text-xs flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <div>
                            <strong>Arsip Terhapus:</strong> Tabung ini telah dihapus dari sistem aktif (Soft-Deleted). Data ditampilkan murni untuk kebutuhan penelusuran audit.
                        </div>
                    </div>
                @endif

                @if($currentCard['meta']['is_pra_digitalisasi_year'])
                    <div class="px-5 py-3 bg-slate-100 border-b border-slate-200 text-slate-700 text-xs flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <strong>Periode Pra-Digitalisasi HAFIS:</strong> Tahun {{ $tahun }} mendahului peluncuran sistem digital resmi HAFIS. Data sebelum periode ini dikelola dalam format log fisik terdahulu.
                        </div>
                    </div>
                @endif

                {{-- Kepala Kartu Identitas --}}
                <div class="p-5 sm:p-6 bg-slate-50/70 border-b border-slate-200">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span class="font-mono text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                                    {{ $currentCard['meta']['kode'] }}
                                </span>
                                @if($currentCard['meta']['nomor_register'])
                                    <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-200 text-slate-700">
                                        Reg: {{ $currentCard['meta']['nomor_register'] }}
                                    </span>
                                @endif
                                <x-badge :variant="$currentCard['meta']['status_badge_variant']" size="sm">
                                    {{ $currentCard['meta']['status_label'] }}
                                </x-badge>
                            </div>
                            <p class="text-xs text-slate-600 mt-1.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $currentCard['meta']['lokasi_lengkap'] }}</span>
                            </p>
                        </div>

                        {{-- Metadata Ringkas di Sisi Kanan --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                            <div class="p-2.5 bg-white border border-slate-200 rounded-lg">
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">Jenis Media</div>
                                <div class="font-bold text-slate-800 mt-0.5">{{ $currentCard['meta']['jenis_media'] }}</div>
                            </div>
                            <div class="p-2.5 bg-white border border-slate-200 rounded-lg">
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">Kapasitas</div>
                                <div class="font-mono font-bold text-slate-800 mt-0.5">{{ str_ends_with(strtolower(trim((string) $currentCard['meta']['kapasitas'])), 'kg') ? $currentCard['meta']['kapasitas'] : $currentCard['meta']['kapasitas'] . ' kg' }}</div>
                            </div>
                            <div class="p-2.5 bg-white border border-slate-200 rounded-lg col-span-2 sm:col-span-1">
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">Masa Berlaku Tabung</div>
                                <div class="font-mono font-bold mt-0.5 {{ $currentCard['meta']['is_expired'] ? 'text-rose-600' : 'text-slate-800' }}">
                                    {{ $currentCard['meta']['tanggal_kadaluarsa'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Matriks 12 Bulan Interaktif --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-700">
                                <th class="py-3 px-4 font-bold w-36">Bulan Kalender</th>
                                @foreach($columns as $col)
                                    <th class="py-3 px-3 font-bold text-center">
                                        <div>{{ $col['title'] }}</div>
                                        @if(!$col['is_active'])
                                            <span class="text-[10px] text-amber-700 font-normal block">Histori</span>
                                        @endif
                                    </th>
                                @endforeach
                                <th class="py-3 px-4 font-bold w-48 text-left">Petugas Pemeriksa</th>
                                <th class="py-3 px-3 font-bold text-center w-28">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($currentCard['matrix'] as $bulanNum => $row)
                                <tr class="hover:bg-slate-50/80 transition-colors {{ $row['has_pemeriksaan'] ? 'bg-white' : 'bg-slate-50/30' }}">
                                    {{-- Kolom Bulan --}}
                                    <td class="py-3 px-4 font-semibold text-slate-900">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded bg-slate-100 text-slate-600 font-mono text-[11px] font-bold flex items-center justify-center shrink-0">
                                                {{ sprintf('%02d', $bulanNum) }}
                                            </span>
                                            <span>{{ $row['bulan_nama'] }}</span>
                                        </div>
                                    </td>

                                    {{-- Kolom Checklist Items --}}
                                    @foreach($columns as $col)
                                        @php
                                            $cell = $row['cells'][$col['key']];
                                            $sym = $cell['symbol'];
                                        @endphp
                                        <td class="py-3 px-3 text-center align-middle font-mono">
                                            @if($sym === $symbols['baik'])
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 font-bold text-sm shadow-2xs"
                                                      title="Hasil: Baik (Sesuai Standar)">
                                                    {{ $sym }}
                                                </span>
                                            @elseif($sym === $symbols['tidak_baik'])
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-800 font-bold text-sm shadow-2xs"
                                                      title="{{ $cell['catatan'] ? 'Temuan: ' . $cell['catatan'] : 'Hasil: Tidak Baik' }}">
                                                    {{ $sym }}
                                                </span>
                                            @elseif($sym === $symbols['tidak_diperiksa'])
                                                <span class="text-slate-400 font-bold" title="Dilewati / Tidak Diperiksa">
                                                    {{ $sym }}
                                                </span>
                                            @else
                                                <span class="text-slate-300">–</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    {{-- Kolom Petugas & Tanggal Pemeriksaan --}}
                                    <td class="py-3 px-4 text-left">
                                        @if($row['has_pemeriksaan'])
                                            <div class="font-semibold text-slate-900">
                                                {{ $row['pemeriksa_nama'] }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                                <span>{{ $row['tanggal_pemeriksaan'] }}</span>
                                                @if($row['is_terlambat'])
                                                    <span class="px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 text-[10px] font-semibold">
                                                        Terlambat
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Belum diperiksa</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Status Kepatuhan --}}
                                    <td class="py-3 px-3 text-center">
                                        @if($row['has_pemeriksaan'])
                                            @if($row['is_terlambat'])
                                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Terlambat
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Tepat Waktu
                                                </span>
                                            @endif
                                        @elseif($row['is_pra_digitalisasi'])
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500">
                                                Pra-Digital
                                            </span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium text-slate-400">
                                                Kosong
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Legenda Kartu di Layar Web --}}
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-600">
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="font-semibold text-slate-800">Keterangan Simbol:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                {{ $symbols['baik'] }}
                            </span>
                            <span>Kondisi Baik</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-800 font-bold flex items-center justify-center text-xs">
                                {{ $symbols['tidak_baik'] }}
                            </span>
                            <span>Tidak Baik (Rusak)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-bold text-slate-500 font-mono">
                                {{ $symbols['tidak_diperiksa'] }}
                            </span>
                            <span>Dilewati / Tidak Diperiksa</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-400">
                            <span>(–) Belum Ada Inspeksi</span>
                        </div>
                    </div>

                    <div class="text-[11px] text-slate-400">
                        Orientasi Cetak Otomatis: <strong class="text-slate-600">{{ strtoupper($orientation) }}</strong> ({{ count($columns) }} Kolom Parameter)
                    </div>
                </div>
            </div>
        @else
            <div class="p-12 bg-white border border-slate-200 rounded-xl text-center shadow-2xs">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-sm font-bold text-slate-800">Tidak Ada Data APAR yang Dipilih</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Silakan gunakan form filter di atas untuk memilih unit tabung APAR yang ingin ditampilkan riwayat kartu pemeliharaannya.
                </p>
            </div>
        @endif

        {{-- Script jQuery & Select2 untuk Pengalaman Navigasi Cepat & Pencarian Fleksibel --}}
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
        <script>
            function resetAparAndSubmit() {
                var aparSelect = document.getElementById('apar_id');
                if (aparSelect) {
                    aparSelect.value = '';
                }
                var form = document.getElementById('filterForm');
                if (form) {
                    form.submit();
                }
            }

            function onGedungChange() {
                var lantaiSelect = document.getElementById('lantai_id');
                if (lantaiSelect) {
                    lantaiSelect.value = '';
                }
                resetAparAndSubmit();
            }

            document.addEventListener('DOMContentLoaded', function () {
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    // Inisialisasi Select2 untuk Pilih Unit APAR (Option 1: dropdownParent lokal)
                    $('#apar_id').select2({
                        dropdownParent: $('#apar-select-wrapper'),
                        placeholder: 'Cari kode APAR (cth: 119) atau nama gedung/ruangan...',
                        width: '100%',
                        templateResult: function (state) {
                            if (!state.id) return state.text;
                            var $el = $(state.element);
                            var kode = $el.data('kode');
                            var lokasi = $el.data('lokasi');
                            var media = $el.data('media');
                            var kapasitas = $el.data('kapasitas');
                            var isArsip = $el.data('arsip');

                            if (!kode) return state.text;

                            var badgeMedia = '';
                            if (media || kapasitas) {
                                var spec = (media || '') + (kapasitas ? ' • ' + kapasitas : '');
                                badgeMedia = '<span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium shrink-0 border border-slate-200 whitespace-nowrap">' + spec + '</span>';
                            }

                            var badgeArsip = isArsip ? '<span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-50 border border-rose-200 text-rose-700 font-semibold shrink-0">ARSIP</span>' : '';

                            return $(
                                '<div class="py-1 px-0.5">' +
                                    '<div class="flex items-center justify-between gap-3">' +
                                        '<div class="flex items-center gap-2 min-w-0">' +
                                            '<span class="font-mono font-bold text-xs text-slate-900 tracking-tight whitespace-nowrap shrink-0">' + kode + '</span>' +
                                            badgeArsip +
                                        '</div>' +
                                        badgeMedia +
                                    '</div>' +
                                    '<div class="text-[11px] text-slate-500 mt-1 leading-normal truncate flex items-center gap-1.5">' +
                                        '<svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>' +
                                        '<span class="truncate">' + lokasi + '</span>' +
                                    '</div>' +
                                '</div>'
                            );
                        },
                        templateSelection: function (state) {
                            if (!state.id) return state.text;
                            var $el = $(state.element);
                            var kode = $el.data('kode');
                            var lokasi = $el.data('lokasi');
                            var isArsip = $el.data('arsip');

                            if (!kode) return state.text;

                            return $(
                                '<div class="inline-flex items-center gap-2 overflow-hidden text-ellipsis whitespace-nowrap">' +
                                    '<span class="font-mono font-bold text-xs text-slate-900 shrink-0 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">' + kode + '</span>' +
                                    (isArsip ? '<span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-50 border border-rose-200 text-rose-700 font-semibold shrink-0">ARSIP</span>' : '') +
                                    '<span class="text-xs text-slate-700 truncate">' + lokasi + '</span>' +
                                '</div>'
                            );
                        }
                    }).on('select2:open', function () {
                        // Pasang placeholder pada input search di dalam dropdown
                        var searchInput = document.querySelector('.select2-search__field');
                        if (searchInput) {
                            searchInput.setAttribute('placeholder', '🔍 Ketik kode APAR (cth: 119) atau nama lokasi...');
                        }
                    }).on('select2:select', function () {
                        $(this).closest('form').submit();
                    });
                }
            });
        </script>
    </x-layouts.app>
@endif

