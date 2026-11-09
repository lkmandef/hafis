<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label QR APAR : {{ $apar->kode }} - RSUD Ansari Saleh</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Dynamic Print Page Size Styling -->
    <style id="dynamic-print-page">
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            html, body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                height: 100% !important;
                max-height: 297mm !important;
                display: block !important;
                width: 100% !important;
                overflow: hidden !important;
            }

            .label-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin: 0 !important;
                background: white !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            /* 1. Format Kertas A4: 1 Stiker (Kiri Atas) */
            body.print-format-a4-single {
                padding: 8mm 0 0 8mm !important;
            }
            body.print-format-a4-single #print-container {
                display: inline-block !important;
                width: 88mm !important;
                position: relative !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
            body.print-format-a4-single #print-grid {
                display: block !important;
                width: 88mm !important;
            }
            body.print-format-a4-single #slot-stiker-1 {
                display: block !important;
                margin: 0 !important;
                width: 88mm !important;
                position: relative !important;
            }
            body.print-format-a4-single #slot-stiker-2 {
                display: none !important;
            }
            body.print-format-a4-single .cut-guide-single {
                display: flex !important;
            }
            body.print-format-a4-single .cut-guide-dual {
                display: none !important;
            }

            /* 2. Format Kertas A4: 2 Stiker (Kiri & Kanan Atas Berdampingan) */
            body.print-format-a4-dual {
                padding: 8mm 0 0 10mm !important;
            }
            body.print-format-a4-dual #print-container {
                display: block !important;
                width: 184mm !important;
                position: relative !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
            body.print-format-a4-dual #print-grid {
                display: grid !important;
                grid-template-columns: 88mm 88mm !important;
                gap: 8mm !important;
                width: 184mm !important;
                margin: 0 !important;
            }
            body.print-format-a4-dual #slot-stiker-1,
            body.print-format-a4-dual #slot-stiker-2 {
                display: block !important;
                margin: 0 !important;
                width: 88mm !important;
                position: relative !important;
            }
            body.print-format-a4-dual .cut-guide-single {
                display: none !important;
            }
            body.print-format-a4-dual .cut-guide-dual {
                display: flex !important;
                width: 184mm !important;
            }

            /* 3. Format Pas Stiker (Thermal / PDF Pas tanpa margin A4) */
            body.print-format-pas {
                padding: 0 !important;
                margin: 0 !important;
            }
            body.print-format-pas #print-container,
            body.print-format-pas #print-grid {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
            body.print-format-pas #slot-stiker-1 {
                display: block !important;
                margin: 0 !important;
                width: 100% !important;
            }
            body.print-format-pas #slot-stiker-2 {
                display: none !important;
            }
            body.print-format-pas .cut-guide-single,
            body.print-format-pas .cut-guide-dual {
                display: none !important;
            }
        }

        /* Garis Panduan Potong (Cutting Guide Lines) - Hanya aktif saat diprint */
        .cut-guide-single, .cut-guide-dual {
            display: none !important;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-center font-sans antialiased text-slate-900 print-format-a4-single">

    <!-- Action Bar (Hidden during printing - Konsisten Lebar max-w-3xl di Semua Format) -->
    <div id="action-bar-container" class="no-print max-w-3xl w-full mb-6 flex flex-col gap-3">
        <!-- Baris 1: Tombol Navigasi & Aksi Utama -->
        <div class="flex flex-wrap items-center justify-between gap-2.5">
            <a href="{{ route('apar.show', $apar->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition min-h-[42px] shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali</span>
            </a>

            <div class="flex items-center gap-2">
                <!-- Tombol Unduh PNG Resolusi Tinggi (Format UI Bersih seperti Unduh SVG) -->
                <button type="button" id="btn-unduh-png" onclick="unduhLabelPng()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 text-xs font-semibold transition min-h-[42px] shadow-2xs cursor-pointer" title="Unduh gambar stiker label lengkap format PNG resolusi tinggi siap cetak/kirim">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Unduh PNG</span>
                </button>

                <!-- Tombol Cetak Label -->
                <button type="button" onclick="cetakLabel()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 active:scale-[0.98] transition min-h-[42px] shadow-xs cursor-pointer">
                    <svg class="w-4 h-4 text-slate-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak Label</span>
                </button>
            </div>
        </div>

        <!-- Baris 2: Pemilih Mode Format Cetak (1 Stiker A4, 2 Stiker A4, Pas Stiker) -->
        <div class="bg-white border border-slate-200 rounded-xl p-2.5 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
            <span class="text-[11px] font-semibold text-slate-600 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>Format Layout Cetak:</span>
            </span>

            <div class="inline-flex rounded-lg bg-slate-100 p-0.5 border border-slate-200 gap-0.5">
                <!-- Pilihan 1: 1 Stiker A4 (Kiri Atas) -->
                <button type="button" 
                        id="opt-a4-single" 
                        onclick="setPrintFormat('a4-single')" 
                        class="px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all bg-white text-slate-900 shadow-2xs cursor-pointer flex items-center gap-1">
                    <span>📄 1 Stiker A4</span>
                    <span class="text-[10px] text-slate-500 font-normal">(Kiri Atas)</span>
                </button>

                <!-- Pilihan 2: 2 Stiker A4 (Kiri & Kanan Atas) -->
                <button type="button" 
                        id="opt-a4-dual" 
                        onclick="setPrintFormat('a4-dual')" 
                        class="px-2.5 py-1.5 rounded-md text-xs font-medium transition-all text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1">
                    <span>📑 2 Stiker A4</span>
                    <span class="text-[10px] text-slate-400 font-normal">(Kiri & Kanan)</span>
                </button>

                <!-- Pilihan 3: Pas Stiker (Thermal / PDF Pas) -->
                <button type="button" 
                        id="opt-pas" 
                        onclick="setPrintFormat('pas')" 
                        class="px-2.5 py-1.5 rounded-md text-xs font-medium transition-all text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1">
                    <span>🏷️ Pas Stiker</span>
                    <span class="text-[10px] text-slate-400 font-normal">(PDF Pas)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Container Utama Cetak & Preview -->
    <div id="print-container" class="relative transition-all duration-200">
        <!-- Grid Stiker (Mendukung 1 atau 2 Stiker Berdampingan) -->
        <div id="print-grid" class="flex flex-col md:flex-row items-center justify-center gap-6 relative">

            <!-- SLOT STIKER 1 (KIRI ATAS / UTAMA) -->
            <div id="slot-stiker-1" class="relative">
                <!-- Fisik Stiker Label APAR (Official Hospital Safety Asset Tag - Padat, Kuat, Bebas Kotak Bertumpuk) -->
                <div id="label-card" class="label-card max-w-sm w-[350px] bg-white border-2 border-slate-900 shadow-md text-xs rounded-none overflow-hidden font-sans">
                    
                    <!-- 1. Header Institusional Sederhana -->
                    <div class="px-4 py-3 border-b-2 border-slate-900 text-center bg-white">
                        <span class="text-[9px] font-bold tracking-[0.18em] text-slate-500 uppercase block">
                            RSUD DR. H. MOCH. ANSARI SALEH
                        </span>
                        <h1 class="text-xs sm:text-[13px] font-black tracking-wider text-slate-900 uppercase mt-0.5 leading-snug">
                            INSTALASI K3RS &bull; KESELAMATAN &amp; KESEHATAN KERJA
                        </h1>
                    </div>

                    <!-- 2. Identitas Utama: Kode Unit APAR Dominan -->
                    <div class="px-4 pt-3.5 pb-1 text-center bg-white">
                        <div class="text-[26px] font-black font-mono tracking-wider text-slate-950 leading-none">
                            {{ $apar->kode }}
                        </div>
                    </div>

                    <!-- 3. Visual QR Code Utama dengan Quiet Zone Bersih -->
                    <div class="px-4 pt-1.5 pb-3 flex flex-col items-center justify-center bg-white">
                        <!-- Quiet Zone: Bantalan putih bersih dengan ukuran QR optimal & proporsional -->
                        <div class="w-44 h-44 bg-white flex items-center justify-center">
                            {!! $apar->qr_svg !!}
                        </div>
                    </div>

                    <!-- 4. Informasi Spesifikasi: Layout 2 Kolom Bersih (Tanpa Kotak Bertumpuk) -->
                    <div class="border-t-2 border-slate-900 px-4 py-2.5 bg-white space-y-2">
                        <div class="grid grid-cols-2 gap-3 text-left">
                            <div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block">MEDIA PEMADAM</span>
                                <span class="text-[11px] font-bold text-slate-900 block truncate leading-tight">{{ $apar->jenis_media }}</span>
                            </div>
                            <div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block">BERAT / KAPASITAS</span>
                                <span class="text-[11px] font-bold font-mono text-slate-900 block leading-tight">{{ $apar->kapasitas }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-left pt-1.5 border-t border-slate-200">
                            <div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI GEDUNG</span>
                                <span class="text-[11px] font-semibold text-slate-900 block truncate leading-tight">
                                    {{ $apar->gedung?->nama ?? '-' }} (Lt. {{ $apar->lantai?->nomor_lantai ?? '-' }})
                                </span>
                            </div>
                            <div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block">RUANGAN / AREA</span>
                                <span class="text-[11px] font-semibold text-slate-900 block truncate leading-tight">
                                    {{ $apar->ruangan?->nama ?? $apar->posisi_detail ?? '-' }}
                                </span>
                            </div>
                        </div>

                        @if($apar->posisi_detail && $apar->ruangan?->nama)
                            <div class="pt-1.5 border-t border-slate-100 text-[9.5px] text-slate-500 italic">
                                <span class="font-bold text-slate-600 not-italic">Posisi:</span> {{ $apar->posisi_detail }}
                            </div>
                        @endif
                    </div>

                    <!-- 5. Metadata Tanggal Pemasangan & Kedaluwarsa Sederhana (Merah hanya untuk peringatan) -->
                    @php
                        $isExpired = $apar->tanggal_kadaluarsa && $apar->tanggal_kadaluarsa->isPast();
                        $isNearExp = $apar->tanggal_kadaluarsa && !$isExpired && $apar->tanggal_kadaluarsa->diffInDays(now()) <= 30;
                    @endphp
                    <div class="border-t-2 border-slate-900 px-4 py-2 bg-white">
                        <div class="grid grid-cols-2 gap-3 text-left">
                            <div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block">TGL PEMASANGAN</span>
                                <span class="text-[11px] font-mono font-bold text-slate-800 block mt-0.5">
                                    {{ $apar->tanggal_pemasangan ? $apar->tanggal_pemasangan->format('d/m/Y') : '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[8px] font-bold uppercase tracking-wider block {{ $isExpired ? 'text-red-600' : ($isNearExp ? 'text-amber-600' : 'text-slate-400') }}">
                                    KEDALUWARSA (EXP)
                                </span>
                                <span class="text-[11px] font-mono font-bold block mt-0.5 {{ $isExpired ? 'text-red-700 font-black' : ($isNearExp ? 'text-amber-700 font-black' : 'text-slate-800') }}">
                                    {{ $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format('d/m/Y') : '-' }}
                                    @if($isExpired)
                                        <span class="text-[8px] font-sans font-bold text-red-700 ml-0.5">(EXPIRED)</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panduan Potong Mode 1 Stiker: Sisi Kanan (Hanya Muncul Saat Print) -->
                <div class="cut-guide-single absolute top-0 -right-4 bottom-0 flex-col items-center justify-center pointer-events-none">
                    <div class="h-full border-r-2 border-dashed border-slate-400"></div>
                    <span class="absolute top-1/2 -translate-y-1/2 bg-white px-0.5 text-[9px] font-mono text-slate-500 rotate-90 whitespace-nowrap">✂ Potong Kanan</span>
                </div>

                <!-- Panduan Potong Mode 1 Stiker: Sisi Bawah (Hanya Muncul Saat Print) -->
                <div class="cut-guide-single absolute -bottom-4 left-0 right-0 items-center justify-center pointer-events-none">
                    <div class="w-full border-b-2 border-dashed border-slate-400"></div>
                    <span class="absolute left-1/2 -translate-x-1/2 bg-white px-1.5 text-[9px] font-mono text-slate-500 whitespace-nowrap">✂ Potong Bawah</span>
                </div>
            </div>

            <!-- SLOT STIKER 2 (KANAN ATAS / DUPLIKASI PASANGAN) -->
            <div id="slot-stiker-2" class="relative hidden">
                <!-- Isi kartu akan diduplikasi secara otomatis via JS -->
            </div>
        </div>

        <!-- Panduan Potong Mode 2 Stiker: Melintang Garis Bawah (Hanya Muncul Saat Print) -->
        <div class="cut-guide-dual absolute -bottom-5 left-0 right-0 items-center justify-center pointer-events-none">
            <div class="w-full border-b-2 border-dashed border-slate-400"></div>
            <span class="absolute left-1/2 -translate-x-1/2 bg-white px-2 text-[9px] font-mono text-slate-600 whitespace-nowrap">✂ Potong Melintang Bawah (Paruh Atas Selesai)</span>
        </div>
    </div>

    <!-- Petunjuk Cetak & Link Cadangan SVG (Non-Print) -->
    <div class="no-print mt-6 text-center space-y-2 max-w-sm">
        <p class="text-[11px] text-slate-500 leading-relaxed">
            Tempelkan stiker ini pada tabung APAR atau Dinding setinggi 120–140 cm dari permukaan lantai agar mudah dipindai oleh petugas K3RS.
        </p>
        <div class="text-[11px] text-slate-400">
            Butuh file vektor QR saja? 
            <a href="{{ route('apar.download_qr', $apar->id) }}" class="underline hover:text-slate-700 transition">Unduh mentahan SVG</a>
        </div>
    </div>

    <!-- Script Format Cetak & Canvas PNG Generator HD -->
    <script>
        let currentFormat = 'a4-single';

        // Duplikasi otomatis Stiker 1 ke Stiker 2 untuk mode dual print
        function syncDualStickers() {
            const card1 = document.getElementById('label-card');
            const slot2 = document.getElementById('slot-stiker-2');
            if (card1 && slot2) {
                slot2.innerHTML = card1.outerHTML;
            }
        }

        function setPrintFormat(format) {
            currentFormat = format;
            const pageStyle = document.getElementById('dynamic-print-page');
            const optSingle = document.getElementById('opt-a4-single');
            const optDual = document.getElementById('opt-a4-dual');
            const optPas = document.getElementById('opt-pas');
            const slot2 = document.getElementById('slot-stiker-2');

            // Reset tombol styling
            const inactiveClass = 'px-2.5 py-1.5 rounded-md text-xs font-medium transition-all text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1';
            const activeClass = 'px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all bg-white text-slate-900 shadow-2xs cursor-pointer flex items-center gap-1';

            optSingle.className = inactiveClass;
            optDual.className = inactiveClass;
            optPas.className = inactiveClass;

            document.body.classList.remove('print-format-a4-single', 'print-format-a4-dual', 'print-format-pas');

            if (format === 'a4-single') {
                document.body.classList.add('print-format-a4-single');
                pageStyle.innerHTML = '@media print { @page { size: A4 portrait; margin: 0; } }';
                optSingle.className = activeClass;
                slot2.classList.add('hidden');
            } else if (format === 'a4-dual') {
                document.body.classList.add('print-format-a4-dual');
                pageStyle.innerHTML = '@media print { @page { size: A4 portrait; margin: 0; } }';
                optDual.className = activeClass;
                syncDualStickers();
                slot2.classList.remove('hidden');
            } else {
                document.body.classList.add('print-format-pas');
                pageStyle.innerHTML = '@media print { @page { size: 90mm 138mm; margin: 0; } }';
                optPas.className = activeClass;
                slot2.classList.add('hidden');
            }
        }

        function cetakLabel() {
            if (currentFormat === 'a4-dual') {
                syncDualStickers();
            }
            window.print();
        }

        async function unduhLabelPng() {
            const btn = document.getElementById('btn-unduh-png');
            const originalContent = btn.innerHTML;
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin text-slate-700 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Menyiapkan...</span>`;
            btn.disabled = true;

            try {
                const scale = 3; // 3x HD Retina Resolution (300 DPI)
                const w = 350;
                const hasPosisi = {{ ($apar->posisi_detail && $apar->ruangan?->nama) ? 'true' : 'false' }};
                const cardEl = document.getElementById('label-card');
                const h = cardEl ? cardEl.offsetHeight : (hasPosisi ? 445 : 420);

                const canvas = document.createElement('canvas');
                canvas.width = w * scale;
                canvas.height = h * scale;
                const ctx = canvas.getContext('2d');
                ctx.scale(scale, scale);

                // 1. Background Putih Murni
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, w, h);

                // 2. Border Luar Tunggal (Solid Navy Slate 2px)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.strokeRect(1, 1, w - 2, h - 2);

                // 3. Header Institusional Sederhana
                ctx.textAlign = 'center';
                
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 9px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('RSUD DR. H. MOCH. ANSARI SALEH', w / 2, 22);

                ctx.fillStyle = '#0f172a';
                ctx.font = '900 12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('INSTALASI K3RS • KESELAMATAN & KESEHATAN KERJA', w / 2, 37);

                // Garis Pembatas Header (Navy Slate 2px)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(0, 47);
                ctx.lineTo(w, 47);
                ctx.stroke();

                // 4. Identitas Utama: Kode Unit APAR Dominan
                ctx.fillStyle = '#020617';
                ctx.font = '900 26px ui-monospace, SFMono-Regular, Menlo, monospace';
                ctx.fillText('{{ $apar->kode }}', w / 2, 77);

                // 5. QR Code dengan Quiet Zone Bersih & Proporsional
                const svgElement = document.querySelector('#slot-stiker-1 svg');
                const qrSize = 168;
                if (svgElement) {
                    const svgXml = new XMLSerializer().serializeToString(svgElement);
                    const svgUri = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgXml);
                    const qrImg = new Image();
                    await new Promise((resolve) => {
                        qrImg.onload = resolve;
                        qrImg.onerror = resolve;
                        qrImg.src = svgUri;
                    });
                    // Gambar QR Code 168x168 di tengah kartu
                    ctx.drawImage(qrImg, (w - qrSize) / 2, 88, qrSize, qrSize);
                }

                // Garis Pembatas Spesifikasi (Navy Slate 2px)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(0, 268);
                ctx.lineTo(w, 268);
                ctx.stroke();

                // 6. Spesifikasi Dua Kolom (Tanpa Kotak Bertumpuk)
                let y = 282;
                const col1X = 18;
                const col2X = 185;

                // Baris 1: Media vs Kapasitas
                ctx.textAlign = 'left';
                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 8px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('MEDIA PEMADAM', col1X, y);
                ctx.fillText('BERAT / KAPASITAS', col2X, y);

                y += 12;
                ctx.fillStyle = '#0f172a';
                ctx.font = 'bold 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('{{ $apar->jenis_media }}', col1X, y);

                ctx.font = 'bold 11px ui-monospace, monospace';
                ctx.fillText('{{ $apar->kapasitas }}', col2X, y);

                // Garis Pemisah Antar Baris Spesifikasi (Hairline halus)
                y += 9;
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(col1X, y);
                ctx.lineTo(w - col1X, y);
                ctx.stroke();

                // Baris 2: Gedung vs Ruangan
                y += 12;
                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 8px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('LOKASI GEDUNG', col1X, y);
                ctx.fillText('RUANGAN / AREA', col2X, y);

                y += 12;
                ctx.fillStyle = '#0f172a';
                ctx.font = '600 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                let gedungText = '{{ $apar->gedung?->nama ?? "-" }} (Lt. {{ $apar->lantai?->nomor_lantai ?? "-" }})';
                if (ctx.measureText(gedungText).width > 150) {
                    gedungText = gedungText.slice(0, 20) + '...';
                }
                ctx.fillText(gedungText, col1X, y);

                let ruangText = '{{ $apar->ruangan?->nama ?? $apar->posisi_detail ?? "-" }}';
                if (ctx.measureText(ruangText).width > 150) {
                    ruangText = ruangText.slice(0, 20) + '...';
                }
                ctx.fillText(ruangText, col2X, y);

                @if($apar->posisi_detail && $apar->ruangan?->nama)
                    y += 8;
                    ctx.strokeStyle = '#f1f5f9';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(col1X, y);
                    ctx.lineTo(w - col1X, y);
                    ctx.stroke();

                    y += 11;
                    ctx.fillStyle = '#64748b';
                    ctx.font = 'italic 9.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                    ctx.fillText('Posisi: {{ $apar->posisi_detail }}', col1X, y);
                @endif

                // Garis Pembatas Metadata (Navy Slate 2px)
                y += 13;
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(w, y);
                ctx.stroke();

                // 7. Metadata Tanggal Pemasangan & Kedaluwarsa Sederhana (Merah hanya untuk peringatan)
                y += 14;
                @php
                    $isExpiredPhp = $apar->tanggal_kadaluarsa && $apar->tanggal_kadaluarsa->isPast();
                    $isNearExpPhp = $apar->tanggal_kadaluarsa && !$isExpiredPhp && $apar->tanggal_kadaluarsa->diffInDays(now()) <= 30;
                @endphp
                const isExpired = {{ $isExpiredPhp ? 'true' : 'false' }};
                const isNearExp = {{ $isNearExpPhp ? 'true' : 'false' }};

                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 8px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('TGL PEMASANGAN', col1X, y);

                ctx.fillStyle = isExpired ? '#b91c1c' : (isNearExp ? '#d97706' : '#94a3b8');
                ctx.fillText('KEDALUWARSA (EXP)', col2X, y);

                y += 13;
                ctx.fillStyle = '#1e293b';
                ctx.font = 'bold 11px ui-monospace, monospace';
                ctx.fillText('{{ $apar->tanggal_pemasangan ? $apar->tanggal_pemasangan->format("d/m/Y") : "-" }}', col1X, y);

                ctx.fillStyle = isExpired ? '#b91c1c' : (isNearExp ? '#b45309' : '#1e293b');
                ctx.font = (isExpired || isNearExp ? '900 ' : 'bold ') + '11px ui-monospace, monospace';
                let expText = '{{ $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format("d/m/Y") : "-" }}';
                if (isExpired) {
                    expText += ' (EXP)';
                }
                ctx.fillText(expText, col2X, y);

                // Eksekusi Download Otomatis
                const downloadLink = document.createElement('a');
                downloadLink.download = 'Label-APAR-{{ $apar->kode }}.png';
                downloadLink.href = canvas.toDataURL('image/png', 1.0);
                downloadLink.click();
            } catch (error) {
                console.error('Error generate PNG:', error);
                alert('Terjadi kesalahan saat membuat file gambar PNG.');
            } finally {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        }

        // Jalankan sinkronisasi awal saat dokumen selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            syncDualStickers();
        });
    </script>
</body>
</html>
