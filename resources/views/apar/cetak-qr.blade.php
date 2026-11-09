<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label QR APAR : {{ $apar->kode }}</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Dynamic Print Page Size Styling -->
    <style id="dynamic-print-page">
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
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

            /* Container Cetak Utama */
            .print-wrapper {
                position: relative !important;
                display: inline-block !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .label-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                page-break-inside: avoid;
                margin: 0 !important;
            }

            /* 1. Format Kertas A4 (Ujung Kiri Atas) */
            body.print-format-a4 {
                padding: 0 !important;
            }
            body.print-format-a4 .print-wrapper {
                float: left !important;
                margin: 0 !important;
            }
            body.print-format-a4 .cut-guide {
                display: block !important;
            }

            /* 2. Format Pas Stiker (Thermal / PDF Pas tanpa margin A4) */
            body.print-format-pas {
                padding: 0 !important;
                margin: 0 !important;
            }
            body.print-format-pas .print-wrapper {
                margin: 0 !important;
                width: 100% !important;
            }
            body.print-format-pas .cut-guide {
                display: none !important;
            }
        }

        /* Garis Panduan Potong (Cutting Guide Lines) di Layar & Cetak */
        .cut-guide {
            display: none;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-center font-sans antialiased text-slate-900 print-format-a4">

    <!-- Action Bar (Hidden during printing) -->
    <div class="no-print max-w-lg w-full mb-5 flex flex-col gap-3">
        <!-- Baris 1: Tombol Navigasi & Aksi Utama -->
        <div class="flex items-center justify-between gap-2">
            <a href="{{ route('apar.show', $apar->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition min-h-[42px] shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali</span>
            </a>

            <div class="flex items-center gap-2">
                <!-- Tombol Unduh PNG Resolusi Tinggi (Pengganti Unduh SVG) -->
                <button type="button" id="btn-unduh-png" onclick="unduhLabelPng()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100/80 text-xs font-semibold transition min-h-[42px] shadow-2xs cursor-pointer" title="Unduh gambar stiker label lengkap format PNG siap kirim/cetak">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Unduh PNG</span>
                    <span class="text-[9px] px-1 py-0.2 rounded bg-emerald-200 text-emerald-900 font-bold uppercase">HD</span>
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

        <!-- Baris 2: Pemilih Mode Format Cetak Kertas -->
        <div class="bg-white border border-slate-200 rounded-xl p-2.5 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
            <span class="text-[11px] font-semibold text-slate-600 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>Format Layout Cetak:</span>
            </span>

            <div class="inline-flex rounded-lg bg-slate-100 p-0.5 border border-slate-200">
                <button type="button" 
                        id="opt-a4" 
                        onclick="setPrintFormat('a4')" 
                        class="px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all bg-white text-slate-900 shadow-2xs cursor-pointer flex items-center gap-1">
                    <span>📄 Kertas A4</span>
                    <span class="text-[10px] text-slate-500 font-normal">(Kiri Atas)</span>
                </button>
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

    <!-- Wrapper Cetak (Berisi Kartu Label + Garis Pandu Potong Gunting) -->
    <div class="print-wrapper relative">
        <!-- Fisik Stiker Label APAR (Standar K3RS Rumah Sakit) -->
        <div id="label-card" class="label-card max-w-sm w-[360px] bg-white rounded-xl border-2 border-slate-900 p-5 shadow-lg space-y-3.5 text-xs">
            
            <!-- Header Label -->
            <div class="text-center pb-2.5 border-b-2 border-slate-900">
                <span class="font-bold text-[10px] tracking-widest text-slate-500 uppercase block">K3RS &bull; KESELAMATAN KEBAKARAN</span>
                <h1 class="text-base font-extrabold tracking-tight text-slate-950 uppercase mt-0.5">LABEL IDENTITAS APAR</h1>
                <span class="text-[11px] font-semibold text-slate-600 block">RUMAH SAKIT HAFIS</span>
            </div>

            <!-- Visual QR Code Utama -->
            <div class="flex flex-col items-center justify-center p-2 bg-slate-50 rounded-lg border border-slate-200">
                <div class="w-44 h-44 flex items-center justify-center">
                    {!! $apar->qr_svg !!}
                </div>
                <div class="mt-2 text-center">
                    <span class="text-[10px] font-mono text-slate-400 block uppercase">Token Identifier</span>
                    <span class="font-mono text-xs font-bold text-slate-800 break-all">{{ $apar->qr_code }}</span>
                </div>
            </div>

            <!-- Spesifikasi & Identitas Teknis -->
            <div class="space-y-1.5 border-t border-slate-200 pt-2 text-[11px]">
                <div class="flex justify-between items-baseline">
                    <span class="text-slate-500 font-medium">KODE TABUNG:</span>
                    <span class="font-mono font-extrabold text-sm text-slate-950">{{ $apar->kode }}</span>
                </div>
                <div class="flex justify-between items-baseline">
                    <span class="text-slate-500 font-medium">MEDIA &amp; BERAT:</span>
                    <span class="font-bold text-slate-900">{{ $apar->jenis_media }} &bull; {{ $apar->kapasitas }}</span>
                </div>
                <div class="flex justify-between items-baseline">
                    <span class="text-slate-500 font-medium">LOKASI:</span>
                    <span class="font-semibold text-slate-900 text-right truncate max-w-[200px]">{{ $apar->lokasi_lengkap }}</span>
                </div>
                @if($apar->posisi_detail)
                    <div class="text-[10px] text-slate-600 italic bg-slate-50 p-1.5 rounded border border-slate-200 mt-1">
                        Posisi: {{ $apar->posisi_detail }}
                    </div>
                @endif
            </div>

            <!-- Footer Masa Berlaku -->
            <div class="pt-2 border-t border-slate-900 flex justify-between items-center text-[10px] font-mono text-slate-700">
                <span>PASANG: {{ $apar->tanggal_pemasangan ? $apar->tanggal_pemasangan->format('d/m/Y') : '-' }}</span>
                <span class="font-bold text-slate-950">EXP: {{ $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format('d/m/Y') : '-' }}</span>
            </div>
        </div>

        <!-- Garis Panduan Potong Sisi Kanan (Khusus Cetak Kertas A4) -->
        <div class="cut-guide absolute top-0 -right-5 bottom-0 flex flex-col items-center justify-center pointer-events-none">
            <div class="h-full border-r-2 border-dashed border-slate-400"></div>
            <span class="absolute top-1/2 -translate-y-1/2 bg-white px-0.5 text-[9px] font-mono text-slate-500 rotate-90 whitespace-nowrap">✂ Potong Sisi</span>
        </div>

        <!-- Garis Panduan Potong Sisi Bawah (Khusus Cetak Kertas A4) -->
        <div class="cut-guide absolute -bottom-5 left-0 right-0 flex items-center justify-center pointer-events-none">
            <div class="w-full border-b-2 border-dashed border-slate-400"></div>
            <span class="absolute left-1/2 -translate-x-1/2 bg-white px-1.5 text-[9px] font-mono text-slate-500 whitespace-nowrap">✂ Potong Bawah</span>
        </div>
    </div>

    <!-- Petunjuk Cetak & Link Cadangan SVG (Non-Print) -->
    <div class="no-print mt-5 text-center space-y-2 max-w-sm">
        <p class="text-[11px] text-slate-500 leading-relaxed">
            Tempelkan stiker ini pada tabung APAR setinggi 120–140 cm dari permukaan lantai agar mudah dipindai oleh petugas K3RS.
        </p>
        <div class="text-[11px] text-slate-400">
            Butuh file vektor QR saja? 
            <a href="{{ route('apar.download_qr', $apar->id) }}" class="underline hover:text-slate-700 transition">Unduh mentahan SVG</a>
        </div>
    </div>

    <!-- Script Manajemen Format Cetak & Ekspor PNG Beresolusi Tinggi -->
    <script>
        let currentFormat = 'a4';

        function setPrintFormat(format) {
            currentFormat = format;
            const pageStyle = document.getElementById('dynamic-print-page');
            const optA4 = document.getElementById('opt-a4');
            const optPas = document.getElementById('opt-pas');

            if (format === 'a4') {
                document.body.classList.remove('print-format-pas');
                document.body.classList.add('print-format-a4');
                pageStyle.innerHTML = '@media print { @page { size: A4 portrait; margin: 8mm; } }';

                optA4.className = 'px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all bg-white text-slate-900 shadow-2xs cursor-pointer flex items-center gap-1';
                optPas.className = 'px-2.5 py-1.5 rounded-md text-xs font-medium transition-all text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1';
            } else {
                document.body.classList.remove('print-format-a4');
                document.body.classList.add('print-format-pas');
                pageStyle.innerHTML = '@media print { @page { size: 95mm 140mm; margin: 0; } }';

                optPas.className = 'px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all bg-white text-slate-900 shadow-2xs cursor-pointer flex items-center gap-1';
                optA4.className = 'px-2.5 py-1.5 rounded-md text-xs font-medium transition-all text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1';
            }
        }

        function cetakLabel() {
            window.print();
        }

        async function unduhLabelPng() {
            const btn = document.getElementById('btn-unduh-png');
            const originalContent = btn.innerHTML;
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Menyiapkan...</span>`;
            btn.disabled = true;

            try {
                const scale = 3; // 3x HD Retina Resolution (300 DPI)
                const w = 360;
                const hasPosisi = {{ $apar->posisi_detail ? 'true' : 'false' }};
                const h = hasPosisi ? 545 : 515;

                const canvas = document.createElement('canvas');
                canvas.width = w * scale;
                canvas.height = h * scale;
                const ctx = canvas.getContext('2d');
                ctx.scale(scale, scale);

                // Background Putih Bersih
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, w, h);

                // Border Luar Kotak Label (Hitam Tebal 2px)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.roundRect(2, 2, w - 4, h - 4, 12);
                ctx.stroke();

                // 1. Header Label Identitas
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 9.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('K3RS • KESELAMATAN KEBAKARAN', w / 2, 24);

                ctx.fillStyle = '#020617';
                ctx.font = '900 15px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('LABEL IDENTITAS APAR', w / 2, 44);

                ctx.fillStyle = '#475569';
                ctx.font = '600 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('RUMAH SAKIT HAFIS', w / 2, 60);

                // Garis Pemisah 1 (Header Line)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(18, 70);
                ctx.lineTo(w - 18, 70);
                ctx.stroke();

                // 2. Kotak Abu-abu QR Code
                ctx.fillStyle = '#f8fafc';
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.roundRect(18, 78, w - 36, 215, 8);
                ctx.fill();
                ctx.stroke();

                // Render Visual QR Code SVG
                const svgElement = document.querySelector('.label-card svg');
                if (svgElement) {
                    const svgXml = new XMLSerializer().serializeToString(svgElement);
                    const svgUri = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgXml);
                    const qrImg = new Image();
                    await new Promise((resolve) => {
                        qrImg.onload = resolve;
                        qrImg.onerror = resolve;
                        qrImg.src = svgUri;
                    });
                    ctx.drawImage(qrImg, (w - 150) / 2, 88, 150, 150);
                }

                // Token Identifier
                ctx.fillStyle = '#94a3b8';
                ctx.font = '600 9.5px ui-monospace, SFMono-Regular, Menlo, monospace';
                ctx.fillText('TOKEN IDENTIFIER', w / 2, 260);

                ctx.fillStyle = '#1e293b';
                ctx.font = 'bold 11.5px ui-monospace, SFMono-Regular, Menlo, monospace';
                ctx.fillText('{{ $apar->qr_code }}', w / 2, 276);

                // Garis Pemisah 2
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(18, 301);
                ctx.lineTo(w - 18, 301);
                ctx.stroke();

                // 3. Tabel Data Teknis APAR
                let y = 321;
                function drawField(label, value, isBold, isMono) {
                    ctx.textAlign = 'left';
                    ctx.fillStyle = '#64748b';
                    ctx.font = '500 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                    ctx.fillText(label, 18, y);

                    ctx.textAlign = 'right';
                    ctx.fillStyle = '#020617';
                    ctx.font = (isBold ? '800 ' : '600 ') + (isMono ? '13px ui-monospace, monospace' : '11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif');

                    let displayVal = value;
                    while (ctx.measureText(displayVal).width > 200 && displayVal.length > 3) {
                        displayVal = displayVal.slice(0, -4) + '...';
                    }
                    ctx.fillText(displayVal, w - 18, y);
                    y += 19;
                }

                drawField('KODE TABUNG:', '{{ $apar->kode }}', true, true);
                drawField('MEDIA & BERAT:', '{{ $apar->jenis_media }} • {{ $apar->kapasitas }}', true, false);
                drawField('LOKASI:', '{{ $apar->lokasi_lengkap }}', false, false);

                @if($apar->posisi_detail)
                    ctx.fillStyle = '#f8fafc';
                    ctx.strokeStyle = '#e2e8f0';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.roundRect(18, y - 4, w - 36, 20, 4);
                    ctx.fill();
                    ctx.stroke();

                    ctx.textAlign = 'left';
                    ctx.fillStyle = '#475569';
                    ctx.font = 'italic 10px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                    ctx.fillText('Posisi: {{ $apar->posisi_detail }}', 24, y + 10);
                    y += 26;
                @endif

                // Garis Pemisah Footer (Hitam)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 1.5;
                ctx.beginPath();
                ctx.moveTo(18, y + 4);
                ctx.lineTo(w - 18, y + 4);
                ctx.stroke();

                // 4. Footer Masa Berlaku
                y += 18;
                ctx.textAlign = 'left';
                ctx.fillStyle = '#334155';
                ctx.font = 'bold 10px ui-monospace, monospace';
                ctx.fillText('PASANG: {{ $apar->tanggal_pemasangan ? $apar->tanggal_pemasangan->format("d/m/Y") : "-" }}', 18, y);

                ctx.textAlign = 'right';
                ctx.fillStyle = '#020617';
                ctx.font = 'bold 10px ui-monospace, monospace';
                ctx.fillText('EXP: {{ $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format("d/m/Y") : "-" }}', w - 18, y);

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
    </script>
</body>
</html>
