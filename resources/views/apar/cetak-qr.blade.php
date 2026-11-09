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
                min-height: auto !important;
                height: auto !important;
                display: block !important;
                width: 100% !important;
            }

            .label-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                page-break-inside: avoid;
                margin: 0 !important;
                background: white !important;
            }

            /* 1. Format Kertas A4: 1 Stiker (Kiri Atas) */
            body.print-format-a4-single {
                padding: 8mm 0 0 8mm !important;
            }
            body.print-format-a4-single #print-grid {
                display: block !important;
            }
            body.print-format-a4-single #slot-stiker-1 {
                display: inline-block !important;
                float: left !important;
                margin: 0 !important;
                width: 88mm !important;
            }
            body.print-format-a4-single #slot-stiker-2 {
                display: none !important;
            }
            body.print-format-a4-single .cut-guide-single {
                display: block !important;
            }
            body.print-format-a4-single .cut-guide-dual {
                display: none !important;
            }

            /* 2. Format Kertas A4: 2 Stiker (Kiri & Kanan Atas Berdampingan) */
            body.print-format-a4-dual {
                padding: 8mm 0 0 10mm !important;
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
            }
            body.print-format-a4-dual .cut-guide-single {
                display: none !important;
            }
            body.print-format-a4-dual .cut-guide-dual {
                display: block !important;
            }

            /* 3. Format Pas Stiker (Thermal / PDF Pas tanpa margin A4) */
            body.print-format-pas {
                padding: 0 !important;
                margin: 0 !important;
            }
            body.print-format-pas #print-grid {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
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

        /* Garis Panduan Potong (Cutting Guide Lines) */
        .cut-guide-single, .cut-guide-dual {
            display: none;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-center font-sans antialiased text-slate-900 print-format-a4-single">

    <!-- Action Bar (Hidden during printing) -->
    <div class="no-print max-w-xl w-full mb-6 flex flex-col gap-3">
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

    <!-- Container Grid Cetak (Mendukung 1 atau 2 Stiker Berdampingan) -->
    <div id="print-grid" class="flex flex-col md:flex-row items-center justify-center gap-6">

        <!-- SLOT STIKER 1 (KIRI ATAS / UTAMA) -->
        <div id="slot-stiker-1" class="relative">
            <!-- Fisik Stiker Label APAR (Kotak Padat Standar K3RS Mudah Digunting) -->
            <div id="label-card" class="label-card max-w-sm w-[350px] bg-white border-2 border-slate-900 shadow-lg text-xs rounded-none">
                
                <!-- Aksen Garis Merah Proteksi K3RS -->
                <div class="h-1.5 bg-red-700 w-full"></div>

                <!-- Bagian Utama Kartu -->
                <div class="p-4 space-y-3">
                    <!-- Header Kop Rumah Sakit Resmi -->
                    <div class="text-center pb-2 border-b-2 border-slate-900">
                        <span class="font-bold text-[8.5px] tracking-wider text-slate-500 uppercase block">PEMERINTAH PROVINSI KALIMANTAN SELATAN</span>
                        <h1 class="text-xs sm:text-[13px] font-black tracking-tight text-slate-950 uppercase mt-0.5 leading-snug">
                            RSUD DR. H. MOCH. ANSARI SALEH
                        </h1>
                        <span class="text-[9.5px] font-bold text-red-700 tracking-wider block mt-0.5 uppercase">
                            INSTALASI K3RS &bull; KESELAMATAN KEBAKARAN
                        </span>
                    </div>

                    <!-- Banner Subheader -->
                    <div class="py-1 px-2 bg-slate-100 border border-slate-300 text-center rounded-none">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-800">
                            LABEL IDENTITAS &amp; INSPEKSI APAR
                        </span>
                    </div>

                    <!-- Visual QR Code Utama (Bersih di atas background putih murni) -->
                    <div class="py-2 flex flex-col items-center justify-center">
                        <div class="w-40 h-40 flex items-center justify-center">
                            {!! $apar->qr_svg !!}
                        </div>
                        
                        <!-- Hero Metric: Kode Tabung APAR -->
                        <div class="mt-1 text-center">
                            <span class="text-[9px] font-bold font-mono text-slate-400 uppercase tracking-widest block">
                                KODE UNIT TABUNG
                            </span>
                            <span class="text-lg font-black font-mono text-slate-950 tracking-tight block">
                                {{ $apar->kode }}
                            </span>
                            <span class="text-[9px] font-mono text-slate-500 block break-all">
                                Token: {{ $apar->qr_code }}
                            </span>
                        </div>
                    </div>

                    <!-- Spesifikasi & Identitas Teknis (Tabel Rapi) -->
                    <div class="border-t border-slate-300 pt-2 space-y-1.5 text-[11px]">
                        <div class="flex justify-between items-baseline gap-2">
                            <span class="text-slate-500 font-medium shrink-0">MEDIA / BERAT:</span>
                            <span class="font-bold text-slate-900 text-right">{{ $apar->jenis_media }} &bull; {{ $apar->kapasitas }}</span>
                        </div>
                        <div class="flex justify-between items-baseline gap-2">
                            <span class="text-slate-500 font-medium shrink-0">LOKASI:</span>
                            <span class="font-semibold text-slate-900 text-right truncate max-w-[210px]">{{ $apar->lokasi_lengkap }}</span>
                        </div>
                        @if($apar->posisi_detail)
                            <div class="text-[10px] text-slate-600 italic bg-slate-50 p-1.5 border border-slate-200 rounded-none">
                                Posisi: {{ $apar->posisi_detail }}
                            </div>
                        @endif
                    </div>

                    <!-- Masa Berlaku: 2 Kotak High-Visibility (Khusus Auditor & Patroli K3RS) -->
                    <div class="border-t-2 border-slate-900 pt-2 grid grid-cols-2 gap-2 text-[10px]">
                        <div class="bg-slate-50 border border-slate-200 rounded-none p-1.5 text-center">
                            <span class="text-[8.5px] font-bold text-slate-500 uppercase block">Tgl Pemasangan</span>
                            <span class="text-xs font-mono font-bold text-slate-800 block mt-0.5">
                                {{ $apar->tanggal_pemasangan ? $apar->tanggal_pemasangan->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                        <div class="bg-red-50/70 border border-red-300 rounded-none p-1.5 text-center">
                            <span class="text-[8.5px] font-black text-red-700 uppercase block tracking-tight">KADALUARSA (EXP)</span>
                            <span class="text-xs font-mono font-black text-red-800 block mt-0.5">
                                {{ $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panduan Potong Mode 1 Stiker: Sisi Kanan -->
            <div class="cut-guide-single absolute top-0 -right-4 bottom-0 flex flex-col items-center justify-center pointer-events-none">
                <div class="h-full border-r-2 border-dashed border-slate-400"></div>
                <span class="absolute top-1/2 -translate-y-1/2 bg-white px-0.5 text-[9px] font-mono text-slate-500 rotate-90 whitespace-nowrap">✂ Potong Kanan</span>
            </div>

            <!-- Panduan Potong Mode 1 Stiker: Sisi Bawah -->
            <div class="cut-guide-single absolute -bottom-4 left-0 right-0 flex items-center justify-center pointer-events-none">
                <div class="w-full border-b-2 border-dashed border-slate-400"></div>
                <span class="absolute left-1/2 -translate-x-1/2 bg-white px-1.5 text-[9px] font-mono text-slate-500 whitespace-nowrap">✂ Potong Bawah (Sisa Kertas Bersih)</span>
            </div>
        </div>

        <!-- SLOT STIKER 2 (KANAN ATAS / DUPLIKASI PASANGAN) -->
        <div id="slot-stiker-2" class="relative hidden">
            <!-- Isi kartu akan diduplikasi secara otomatis via JS -->
        </div>

        <!-- Panduan Potong Mode 2 Stiker (Melintang Garis Tengah & Bawah Keseluruhan) -->
        <div class="cut-guide-dual absolute -bottom-4 left-0 right-0 flex items-center justify-center pointer-events-none">
            <div class="w-full border-b-2 border-dashed border-slate-400"></div>
            <span class="absolute left-1/2 -translate-x-1/2 bg-white px-2 text-[9px] font-mono text-slate-600 whitespace-nowrap">✂ Potong Melintang Bawah (Paruh Atas Selesai • Paruh Bawah Utuh Bersih)</span>
        </div>
    </div>

    <!-- Petunjuk Cetak & Link Cadangan SVG (Non-Print) -->
    <div class="no-print mt-6 text-center space-y-2 max-w-sm">
        <p class="text-[11px] text-slate-500 leading-relaxed">
            Tempelkan stiker ini pada tabung APAR setinggi 120–140 cm dari permukaan lantai agar mudah dipindai oleh petugas K3RS.
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
            if (card1 && slot2 && slot2.innerHTML.trim() === '') {
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
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Menyiapkan...</span>`;
            btn.disabled = true;

            try {
                const scale = 3; // 3x HD Retina Resolution (300 DPI)
                const w = 350;
                const hasPosisi = {{ $apar->posisi_detail ? 'true' : 'false' }};
                const h = hasPosisi ? 545 : 515;

                const canvas = document.createElement('canvas');
                canvas.width = w * scale;
                canvas.height = h * scale;
                const ctx = canvas.getContext('2d');
                ctx.scale(scale, scale);

                // Background Putih
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, w, h);

                // Border Luar Kotak Label (Hitam Tebal 2px - Kotak Padat Tegak Lurus)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.strokeRect(2, 2, w - 4, h - 4);

                // Aksen Garis Merah Proteksi K3RS (Kotak Lurus)
                ctx.fillStyle = '#b91c1c';
                ctx.fillRect(2, 2, w - 4, 6);

                // 1. Header Kop Rumah Sakit
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 8.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('PEMERINTAH PROVINSI KALIMANTAN SELATAN', w / 2, 22);

                ctx.fillStyle = '#020617';
                ctx.font = '900 12.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('RSUD DR. H. MOCH. ANSARI SALEH', w / 2, 38);

                ctx.fillStyle = '#b91c1c';
                ctx.font = 'bold 9px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('INSTALASI K3RS • KESELAMATAN KEBAKARAN', w / 2, 51);

                // Garis Pemisah Kop
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(16, 58);
                ctx.lineTo(w - 16, 58);
                ctx.stroke();

                // Banner Subheader Label (Kotak Lurus)
                ctx.fillStyle = '#f1f5f9';
                ctx.strokeStyle = '#cbd5e1';
                ctx.lineWidth = 1;
                ctx.fillRect(16, 64, w - 32, 20);
                ctx.strokeRect(16, 64, w - 32, 20);

                ctx.fillStyle = '#1e293b';
                ctx.font = '800 9.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('LABEL IDENTITAS & INSPEKSI APAR', w / 2, 77);

                // 2. Render QR Code (Bersih Tanpa Kotak Abu-abu)
                const svgElement = document.querySelector('#slot-stiker-1 svg');
                if (svgElement) {
                    const svgXml = new XMLSerializer().serializeToString(svgElement);
                    const svgUri = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgXml);
                    const qrImg = new Image();
                    await new Promise((resolve) => {
                        qrImg.onload = resolve;
                        qrImg.onerror = resolve;
                        qrImg.src = svgUri;
                    });
                    ctx.drawImage(qrImg, (w - 144) / 2, 92, 144, 144);
                }

                // Hero Metric: Kode Tabung APAR
                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 9px ui-monospace, SFMono-Regular, Menlo, monospace';
                ctx.fillText('KODE UNIT TABUNG', w / 2, 252);

                ctx.fillStyle = '#020617';
                ctx.font = '900 17px ui-monospace, SFMono-Regular, Menlo, monospace';
                ctx.fillText('{{ $apar->kode }}', w / 2, 271);

                ctx.fillStyle = '#64748b';
                ctx.font = '500 9px ui-monospace, monospace';
                ctx.fillText('Token: {{ $apar->qr_code }}', w / 2, 285);

                // Garis Pemisah Spesifikasi
                ctx.strokeStyle = '#cbd5e1';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(16, 297);
                ctx.lineTo(w - 16, 297);
                ctx.stroke();

                // 3. Spesifikasi Teknis
                let y = 315;
                function drawField(label, value, isBold, isMono) {
                    ctx.textAlign = 'left';
                    ctx.fillStyle = '#64748b';
                    ctx.font = '500 10.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                    ctx.fillText(label, 16, y);

                    ctx.textAlign = 'right';
                    ctx.fillStyle = '#020617';
                    ctx.font = (isBold ? '800 ' : '600 ') + (isMono ? '12px ui-monospace, monospace' : '10.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif');

                    let displayVal = value;
                    while (ctx.measureText(displayVal).width > 200 && displayVal.length > 3) {
                        displayVal = displayVal.slice(0, -4) + '...';
                    }
                    ctx.fillText(displayVal, w - 16, y);
                    y += 18;
                }

                drawField('MEDIA / BERAT:', '{{ $apar->jenis_media }} • {{ $apar->kapasitas }}', true, false);
                drawField('LOKASI:', '{{ $apar->lokasi_lengkap }}', false, false);

                @if($apar->posisi_detail)
                    ctx.fillStyle = '#f8fafc';
                    ctx.strokeStyle = '#e2e8f0';
                    ctx.lineWidth = 1;
                    ctx.fillRect(16, y - 4, w - 32, 19);
                    ctx.strokeRect(16, y - 4, w - 32, 19);

                    ctx.textAlign = 'left';
                    ctx.fillStyle = '#475569';
                    ctx.font = 'italic 9.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                    ctx.fillText('Posisi: {{ $apar->posisi_detail }}', 22, y + 9);
                    y += 24;
                @endif

                // Garis Pemisah Footer
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(16, y + 2);
                ctx.lineTo(w - 16, y + 2);
                ctx.stroke();

                // 4. Dua Kotak High-Visibility: Tgl Pemasangan & Kadaluarsa (Kotak Padat Biasa)
                y += 12;
                const boxW = (w - 32 - 8) / 2;
                const boxH = 40;

                // Kotak 1: Tgl Pasang (Slate)
                ctx.fillStyle = '#f8fafc';
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 1;
                ctx.fillRect(16, y, boxW, boxH);
                ctx.strokeRect(16, y, boxW, boxH);

                ctx.textAlign = 'center';
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 8.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('TGL PEMASANGAN', 16 + boxW / 2, y + 14);

                ctx.fillStyle = '#1e293b';
                ctx.font = 'bold 11px ui-monospace, monospace';
                ctx.fillText('{{ $apar->tanggal_pemasangan ? $apar->tanggal_pemasangan->format("d/m/Y") : "-" }}', 16 + boxW / 2, y + 30);

                // Kotak 2: Kadaluarsa EXP (Red Alert Box)
                ctx.fillStyle = '#fef2f2';
                ctx.strokeStyle = '#fca5a5';
                ctx.lineWidth = 1;
                ctx.fillRect(16 + boxW + 8, y, boxW, boxH);
                ctx.strokeRect(16 + boxW + 8, y, boxW, boxH);

                ctx.fillStyle = '#b91c1c';
                ctx.font = '900 8.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('KADALUARSA (EXP)', 16 + boxW + 8 + boxW / 2, y + 14);

                ctx.fillStyle = '#991b1b';
                ctx.font = '900 11.5px ui-monospace, monospace';
                ctx.fillText('{{ $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format("d/m/Y") : "-" }}', 16 + boxW + 8 + boxW / 2, y + 30);

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
