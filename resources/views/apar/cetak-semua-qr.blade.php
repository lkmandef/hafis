<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Massal Label QR APAR ({{ $apars->count() }} Unit) - RSUD Ansari Saleh</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            html, body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .sheet-scroll-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                width: 100% !important;
                overflow: visible !important;
            }

            .a4-sheet {
                width: 210mm !important;
                height: 297mm !important;
                max-height: 297mm !important;
                padding: 0 !important;
                box-sizing: border-box !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                display: grid !important;
                grid-template-columns: 105mm 105mm !important;
                grid-template-rows: 148.5mm 148.5mm !important;
                gap: 0 !important;
                position: relative !important;
                overflow: hidden !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                background: white !important;
            }

            .a4-sheet:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            .slot-quadrant {
                width: 105mm !important;
                height: 148.5mm !important;
                max-height: 148.5mm !important;
                box-sizing: border-box !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 4mm !important;
                margin: 0 !important;
            }

            .label-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin: 0 !important;
                background: white !important;
                width: 88mm !important;
                max-width: 88mm !important;
            }

            .cut-line-v,
            .cut-line-h,
            .cut-badge-center,
            .cut-badge-edge {
                display: flex !important;
            }
        }

        /* Screen Preview */
        .a4-sheet {
            width: 210mm;
            min-height: 297mm;
            height: 297mm;
            display: grid;
            grid-template-columns: 105mm 105mm;
            grid-template-rows: 148.5mm 148.5mm;
            box-sizing: border-box;
            background: white;
            position: relative;
        }

        .slot-quadrant {
            width: 105mm;
            height: 148.5mm;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4mm;
        }

        .label-card {
            width: 88mm;
            max-width: 88mm;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center font-sans antialiased text-slate-900">

    <!-- Action Bar (Hidden during printing) -->
    <div class="no-print max-w-4xl w-full mb-6 flex flex-col gap-3">
        <!-- Baris 1: Tombol Navigasi & Cetak -->
        <div class="flex flex-wrap items-center justify-between gap-2.5">
            <a href="{{ route('apar.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition min-h-[42px] shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Data APAR</span>
            </a>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 active:scale-[0.98] transition min-h-[42px] shadow-sm cursor-pointer">
                    <svg class="w-4 h-4 text-slate-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>{{ $isFiltered ? 'Cetak Hasil Filter (' . $apars->count() . ' APAR)' : 'Cetak Semua Label (' . $apars->count() . ' APAR)' }}</span>
                </button>
            </div>
        </div>

        <!-- Baris 2: Informasi Rekapitulasi Lembar Kertas A4 -->
        <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0">
                    A4
                </span>
                <div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="font-bold text-slate-900">
                            Format Lembar A4 (Grid 2 &times; 2 &bull; 4 Stiker per Halaman)
                        </span>
                        @if($isFiltered)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200 font-semibold text-[10.5px]">
                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                <span>Filter: {{ $search ? '"'.$search.'"' : ($activeLokasi?->nama ?? 'Lokasi Terpilih') }}</span>
                            </span>
                        @endif
                    </div>
                    <span class="text-[11px] text-slate-500 block mt-0.5">
                        Total <strong>{{ $apars->count() }} unit APAR</strong> {{ $isFiltered ? 'hasil filter' : '' }} tercakup dalam <strong>{{ ceil($apars->count() / 4) }} lembar kertas A4</strong>.
                    </span>
                </div>
            </div>

            <div class="text-[11px] text-slate-500 bg-slate-50 border border-slate-200 px-2.5 py-1.5 rounded-lg flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Gunakan opsi <strong>"Margins: None / Default"</strong> pada dialog printer.</span>
            </div>
        </div>
    </div>

    <!-- Lembar Halaman Cetak A4 (Di-chunk 4 per lembar) -->
    @php
        $totalLembar = ceil($apars->count() / 4);
    @endphp

    @forelse($apars->chunk(4) as $pageIndex => $chunkApars)
        <!-- Preview Indicator Lembar (Non-Print) -->
        <div class="no-print max-w-[210mm] w-full mb-2 flex items-center justify-between text-xs text-slate-500 px-1">
            <span class="font-mono font-semibold">
                📄 Lembar {{ $pageIndex + 1 }} dari {{ $totalLembar }}
            </span>
            <span class="text-[11px]">
                {{ $chunkApars->count() }} Stiker pada lembar ini
            </span>
        </div>

        <!-- Kontainer Fisik Kertas A4 (4 Stiker: 2 Kolom x 2 Baris) -->
        <div class="sheet-scroll-wrapper w-full overflow-x-auto flex justify-center pb-6">
            <div class="a4-sheet bg-white border border-slate-200 shadow-md relative">
                
                <!-- Garis Panduan Potong Presisi (Vertical, Horizontal & Scissor Badges) -->
                <!-- 1. Garis Vertikal Tengah (X = 105mm) -->
                <div class="cut-line-v absolute left-[105mm] top-2 bottom-2 -translate-x-1/2 w-0 border-r border-dashed border-slate-300 pointer-events-none z-10"></div>
                
                <!-- 2. Garis Horizontal Tengah (Y = 148.5mm) -->
                <div class="cut-line-h absolute top-[148.5mm] left-2 right-2 -translate-y-1/2 h-0 border-b border-dashed border-slate-300 pointer-events-none z-10"></div>

                <!-- 3. Titik Silang Pusat (Pusat Potong A4) -->
                <div class="cut-badge-center absolute left-[105mm] top-[148.5mm] -translate-x-1/2 -translate-y-1/2 bg-white border border-slate-300 rounded-full w-5 h-5 flex items-center justify-center text-[10px] text-slate-500 font-mono shadow-2xs z-20 pointer-events-none">
                    ✂
                </div>

                <!-- 4. Panduan Gunting Sisi Atas -->
                <div class="cut-badge-edge absolute left-[105mm] top-1.5 -translate-x-1/2 bg-white border border-slate-200 px-1.5 py-0.5 rounded text-[8px] font-mono text-slate-400 z-20 pointer-events-none flex items-center gap-1 shadow-2xs">
                    <span>✂</span><span>POTONG</span>
                </div>

                <!-- 5. Panduan Gunting Sisi Bawah -->
                <div class="cut-badge-edge absolute left-[105mm] bottom-1.5 -translate-x-1/2 bg-white border border-slate-200 px-1.5 py-0.5 rounded text-[8px] font-mono text-slate-400 z-20 pointer-events-none flex items-center gap-1 shadow-2xs">
                    <span>✂</span><span>POTONG</span>
                </div>

                <!-- 6. Panduan Gunting Sisi Kiri -->
                <div class="cut-badge-edge absolute top-[148.5mm] left-1.5 -translate-y-1/2 bg-white border border-slate-200 px-1.5 py-0.5 rounded text-[8px] font-mono text-slate-400 z-20 pointer-events-none flex items-center gap-1 shadow-2xs">
                    <span>✂</span><span>POTONG</span>
                </div>

                <!-- 7. Panduan Gunting Sisi Kanan -->
                <div class="cut-badge-edge absolute top-[148.5mm] right-1.5 -translate-y-1/2 bg-white border border-slate-200 px-1.5 py-0.5 rounded text-[8px] font-mono text-slate-400 z-20 pointer-events-none flex items-center gap-1 shadow-2xs">
                    <span>✂</span><span>POTONG</span>
                </div>

                @foreach($chunkApars as $a)
                    <div class="slot-quadrant">
                        <!-- Fisik Stiker Label APAR (Official Hospital Safety Asset Tag) -->
                        <div class="label-card w-full max-w-sm bg-white border-2 border-slate-900 shadow-none text-xs rounded-none overflow-hidden font-sans">
                            
                            <!-- 1. Header Institusional Sederhana -->
                            <div class="px-3.5 py-2.5 border-b-2 border-slate-900 text-center bg-white">
                                <span class="text-[8.5px] font-bold tracking-[0.16em] text-slate-500 uppercase block leading-none">
                                    RSUD DR. H. MOCH. ANSARI SALEH
                                </span>
                                <h2 class="text-xs font-black tracking-wider text-slate-900 uppercase mt-1 leading-snug">
                                    INSTALASI K3RS &bull; KESELAMATAN &amp; KESEHATAN KERJA
                                </h2>
                            </div>

                            <!-- 2. Identitas Utama: Kode Unit APAR Dominan -->
                            <div class="px-3.5 pt-2.5 pb-1 text-center bg-white">
                                <div class="text-[24px] font-black font-mono tracking-wider text-slate-950 leading-none">
                                    {{ $a->kode }}
                                </div>
                            </div>

                            <!-- 3. Visual QR Code Utama dengan Quiet Zone Bersih -->
                            <div class="px-3.5 pt-1 pb-2 flex flex-col items-center justify-center bg-white">
                                <div class="w-38 h-38 bg-white flex items-center justify-center">
                                    {!! $a->qr_svg !!}
                                </div>
                            </div>

                            <!-- 4. Informasi Spesifikasi: Layout 2 Kolom Bersih -->
                            <div class="border-t-2 border-slate-900 px-3.5 py-2 bg-white space-y-1.5">
                                <div class="grid grid-cols-2 gap-2 text-left">
                                    <div>
                                        <span class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wider block">MEDIA PEMADAM</span>
                                        <span class="text-[10.5px] font-bold text-slate-900 block truncate leading-tight">{{ $a->jenis_media }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wider block">BERAT / KAPASITAS</span>
                                        <span class="text-[10.5px] font-bold font-mono text-slate-900 block leading-tight">{{ $a->kapasitas }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-left pt-1 border-t border-slate-200">
                                    <div>
                                        <span class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI GEDUNG</span>
                                        <span class="text-[10.5px] font-semibold text-slate-900 block truncate leading-tight">
                                            {{ $a->gedung?->nama ?? '-' }} (Lt. {{ $a->lantai?->nomor_lantai ?? '-' }})
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wider block">RUANGAN / AREA</span>
                                        <span class="text-[10.5px] font-semibold text-slate-900 block truncate leading-tight">
                                            {{ $a->ruangan?->nama ?? $a->posisi_detail ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                                @if($a->posisi_detail && $a->ruangan?->nama)
                                    <div class="pt-1 border-t border-slate-100 text-[9px] text-slate-500 italic">
                                        <span class="font-bold text-slate-600 not-italic">Posisi:</span> {{ $a->posisi_detail }}
                                    </div>
                                @endif
                            </div>

                            <!-- 5. Metadata Tanggal Pemasangan & Kedaluwarsa Sederhana -->
                            @php
                                $isExpired = $a->tanggal_kadaluarsa && $a->tanggal_kadaluarsa->isPast();
                                $isNearExp = $a->tanggal_kadaluarsa && !$isExpired && $a->tanggal_kadaluarsa->diffInDays(now()) <= 30;
                            @endphp
                            <div class="border-t-2 border-slate-900 px-3.5 py-1.5 bg-white">
                                <div class="grid grid-cols-2 gap-2 text-left">
                                    <div>
                                        <span class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wider block">TGL PEMASANGAN</span>
                                        <span class="text-[10.5px] font-mono font-bold text-slate-800 block mt-0.5">
                                            {{ $a->tanggal_pemasangan ? $a->tanggal_pemasangan->format('d/m/Y') : '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[7.5px] font-bold uppercase tracking-wider block {{ $isExpired ? 'text-red-600' : ($isNearExp ? 'text-amber-600' : 'text-slate-400') }}">
                                            KEDALUWARSA (EXP)
                                        </span>
                                        <span class="text-[10.5px] font-mono font-bold block mt-0.5 {{ $isExpired ? 'text-red-700 font-black' : ($isNearExp ? 'text-amber-700 font-black' : 'text-slate-800') }}">
                                            {{ $a->tanggal_kadaluarsa ? $a->tanggal_kadaluarsa->format('d/m/Y') : '-' }}
                                            @if($isExpired)
                                                <span class="text-[7.5px] font-sans font-bold text-red-700 ml-0.5">(EXPIRED)</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Placeholder Kosong Jika Halaman Terakhir Kurang dari 4 APAR -->
                @for($i = $chunkApars->count(); $i < 4; $i++)
                    <div class="slot-quadrant"></div>
                @endfor
            </div>
        </div>
    @empty
        <div class="bg-white border border-slate-200 rounded-xl p-12 text-center max-w-xl mx-auto shadow-sm">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-sm font-bold text-slate-900 mb-1">Tidak Ada Data APAR</h3>
            <p class="text-xs text-slate-500 mb-4">
                @if($isFiltered)
                    Tidak ditemukan unit APAR yang cocok dengan kriteria filter saat ini ({{ $search ? 'Pencarian: "'.$search.'"' : ($activeLokasi?->nama ?? 'Lokasi Terpilih') }}).
                @else
                    Tidak ditemukan unit APAR aktif yang dapat dicetak.
                @endif
            </p>
            <a href="{{ route('apar.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                <span>Kembali ke Master Data</span>
            </a>
        </div>
    @endforelse

</body>
</html>
