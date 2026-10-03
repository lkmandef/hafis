<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label QR APAR : {{ $apar->kode }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .label-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-center font-sans antialiased text-slate-900">

    <!-- Action Bar (Hidden during printing) -->
    <div class="no-print max-w-sm w-full mb-4 flex items-center justify-between gap-2">
        <a href="{{ route('apar.show', $apar->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('apar.download_qr', $apar->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition min-h-[44px]">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Unduh SVG</span>
            </a>

            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Label</span>
            </button>
        </div>
    </div>

    <!-- Fisik Stiker Label APAR (Standar K3RS Rumah Sakit) -->
    <div class="label-card max-w-sm w-full bg-white rounded-xl border-2 border-slate-900 p-5 shadow-lg space-y-3.5 text-xs">
        
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
                <span class="text-[10px] font-mono text-slate-400 block">TOKEN IDENTIFIER</span>
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

    <!-- Petunjuk Cetak (Non-Print) -->
    <p class="no-print text-center text-xs text-slate-400 mt-4 max-w-sm">
        Tempelkan stiker ini pada tabung APAR setinggi 120-140 cm dari permukaan lantai agar mudah dipindai oleh petugas K3RS.
    </p>
</body>
</html>
