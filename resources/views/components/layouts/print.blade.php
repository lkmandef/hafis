@props([
    'title' => 'Laporan HAFIS Rumah Sakit',
    'reportTitle' => 'LAPORAN REKAPITULASI APAR',
    'filters' => [],
    'orientation' => 'portrait'
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/hafis-mark.svg') }}">

    <style>
        @page {
            size: A4 {{ $orientation }};
            margin: 10mm 10mm 15mm 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #0f172a;
            background: #f1f5f9;
            margin: 0;
            padding: 20px 10px;
        }

        .no-print {
            max-width: {{ $orientation === 'landscape' ? '297mm' : '210mm' }};
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 8.5pt;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
        }

        .no-print button, .no-print a {
            background: #ffffff;
            color: #0f172a;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 8pt;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .no-print a {
            background: #334155;
            color: #f8fafc;
        }

        /* Lembar preview & cetak A4 (210mm portrait / 297mm landscape) */
        .print-container {
            width: {{ $orientation === 'landscape' ? '297mm' : '210mm' }};
            max-width: 100%;
            min-height: calc(var(--print-pages, 1) * {{ $orientation === 'landscape' ? '210mm' : '297mm' }});
            margin: 0 auto;
            background: #ffffff;
            padding: 10mm 10mm 15mm 10mm;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border-radius: 2px;
            display: flex;
            flex-direction: column;
        }

        .report-content {
            flex: 1 0 auto;
        }

        /* KOP: logo berdampingan tepat di samping blok teks kop */
        .header-kop {
            text-align: center;
        }

        .kop-inner {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding-bottom: 6px;
            text-align: left;
        }

        .kop-logo {
            flex-shrink: 0;
        }

        .kop-logo img {
            display: block;
            object-fit: contain;
            object-position: center;
        }

        .kop-text {
            text-align: center;
        }

        .kop-text .kop-prov {
            font-size: 8.5pt;
            font-weight: 600;
            margin: 0;
            color: #0f172a;
            letter-spacing: 0.2px;
        }

        .kop-text .kop-rs {
            font-size: 14pt;
            font-weight: 800;
            margin: 1px 0 2px 0;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            line-height: 1.1;
        }

        .kop-text .kop-detail {
            font-size: 7pt;
            color: #334155;
            margin: 0;
            line-height: 1.4;
        }

        .kop-border-outer {
            border-bottom: 3px solid #0f172a;
            margin-bottom: 2px;
        }

        .kop-border-inner {
            border-bottom: 1px solid #0f172a;
            margin-bottom: 12px;
        }

        .title-section {
            text-align: center;
            margin-bottom: 6px;
        }

        .title-section h3 {
            font-size: 10.5pt;
            font-weight: 800;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.2px;
        }

        .title-section .title-sub {
            font-size: 7pt;
            color: #475569;
            margin: 0;
        }

        .print-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 7.5pt;
            color: #475569;
            margin-bottom: 8px;
        }

        .filter-summary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 5px 10px;
            margin-bottom: 10px;
            font-size: 8pt;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .filter-item {
            display: inline-flex;
            gap: 4px;
        }

        .filter-item .label {
            color: #64748b;
            font-weight: 600;
        }

        .filter-item .val {
            color: #0f172a;
            font-weight: 700;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            line-height: 1.25;
            margin-bottom: 12px;
            table-layout: auto;
        }

        table.data-table thead {
            display: table-header-group;
        }

        table.data-table tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 3px 4px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        table.data-table th {
            background-color: #0F172A;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 6.5pt;
            letter-spacing: 0.2px;
            text-align: center;
            padding: 4px 3px;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
        .font-bold { font-weight: 700; }

        /* Tipografi status formal tanpa badge/pill/pastel */
        .status-normal {
            font-weight: 500;
            color: #0f172a;
        }

        .status-perhatian {
            font-weight: 600;
            color: #0f172a;
        }

        .status-kritis {
            font-weight: 700;
            color: #0f172a;
        }

        /* Reset aman untuk elemen badge agar tidak menghasilkan background pastel atau pill */
        .badge {
            display: inline;
            padding: 0;
            border-radius: 0;
            font-size: inherit;
            border: none;
            background: transparent !important;
            color: #0f172a !important;
        }
        .badge-success { font-weight: 500; }
        .badge-warning { font-weight: 600; }
        .badge-danger  { font-weight: 700; }
        .badge-neutral { font-weight: 400; }

        .footer-signatures {
            margin-top: auto;
            break-inside: avoid;
            page-break-inside: avoid;
            font-size: 7.5pt;
            padding-top: 16px;
        }

        .signature-city-date {
            text-align: right;
            margin-bottom: 10px;
            font-size: 7.5pt;
        }

        .signature-cols {
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 220px;
            text-align: center;
        }

        .sig-role {
            font-weight: 400;
        }

        .sig-unit {
            font-weight: 700;
            font-size: 7.5pt;
        }

        .sig-hospital {
            font-weight: 400;
            font-size: 7pt;
            color: #334155;
        }

        .signature-space {
            height: 48px;
        }

        .signature-name {
            font-weight: 700;
            border-bottom: 1px solid #0f172a;
            padding-bottom: 2px;
            min-height: 16px;
        }

        .signature-nip {
            font-size: 7pt;
            color: #475569;
            margin-top: 2px;
        }

        .print-footer {
            display: none;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                width: 100% !important;
                max-width: 100% !important;
                min-height: calc(var(--print-pages, 1) * {{ $orientation === 'landscape' ? '185mm' : '272mm' }}) !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                display: flex !important;
                flex-direction: column !important;
            }
            .report-content {
                flex: 1 0 auto !important;
            }
            .footer-signatures {
                margin-top: auto !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                padding-top: 16px !important;
            }
            .print-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 4px 0 0 0;
                border-top: 1px solid #e2e8f0;
                font-size: 6.5pt;
                color: #64748b;
            }
        }
    </style>
</head>
<body>

    {{-- Bar Aksi Layar (disembunyikan saat dicetak) --}}
    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
            <strong>Mode Cetak Laporan Resmi HAFIS (A4 {{ ucfirst($orientation) }})</strong>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ request()->fullUrlWithoutQuery(['format']) }}"
               onclick="if (window.opener && !window.opener.closed) { try { window.close(); } catch(e) {} } if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { window.history.back(); return false; }">
                &larr; Kembali ke Tampilan Web
            </a>
            <button type="button" onclick="window.print()">
                Cetak Sekarang (Ctrl + P)
            </button>
        </div>
    </div>

    {{-- Lembar Cetak Dokumen A4 {{ ucfirst($orientation) }} --}}
    <div class="print-container" id="printContainer">
        {{-- Kop Resmi RSUD --}}
        <div class="header-kop">
            <div class="kop-inner">
                <div class="kop-logo">
                    <img src="{{ asset('images/brand/lambang-provinsi.svg') }}"
                         alt="Lambang Provinsi Kalimantan Selatan"
                         style="height: 66px; width: auto;" />
                </div>
                <div class="kop-text">
                    <p class="kop-prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</p>
                    <p class="kop-rs">RSUD dr. H. MOCH. ANSARI SALEH</p>
                    <p class="kop-detail">
                        Jl. Brig. Jend. H. Hasan Basri No.1 Banjarmasin, Kalimantan Selatan 70125<br>
                        Telepon 0511-6710000, Faksimile: 0511-6710001<br>
                        Laman http://rsas.kalselprov.go.id; Pos-el: rsas@kalselprov.go.id
                    </p>
                </div>
            </div>
            <div class="kop-border-outer"></div>
            <div class="kop-border-inner"></div>
        </div>

        {{-- Judul Laporan --}}
        <div class="title-section">
            <h3>{{ $reportTitle }}</h3>
            <p class="title-sub">Instalasi K3RS &bull; RSUD dr. H. Moch. Ansari Saleh Banjarmasin &bull; Dokumen Resmi</p>
        </div>

        {{-- Meta Cetak --}}
        <div class="print-meta-row">
            <span>{{ $slot->isEmpty() ? '' : '' }}</span>
            <span style="color: #1e293b; font-weight: 600;">
                {{ now()->timezone('Asia/Makassar')->locale('id')->translatedFormat('l, d F Y') }} - {{ now()->timezone('Asia/Makassar')->format('H:i:s') }} WITA
            </span>
        </div>

        {{-- Ringkasan Filter Aktif --}}
        @if(!empty($filters))
            <div class="filter-summary">
                @foreach($filters as $label => $val)
                    @if($val)
                        <div class="filter-item">
                            <span class="label">{{ $label }}:</span>
                            <span class="val">{{ $val }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Konten Utama Laporan --}}
        <div class="report-content">
            {{ $slot }}
        </div>

        {{-- Tanda Tangan Formal K3RS --}}
        <div class="footer-signatures" id="footerSignatures">
            <div class="signature-city-date">
                Banjarmasin, {{ now()->timezone('Asia/Makassar')->locale('id')->translatedFormat('d F Y') }}
            </div>
            <div class="signature-cols">
                <div class="signature-box">
                    <div class="sig-role">Petugas Pelaksana K3RS,</div>
                    <div class="sig-unit">RSUD dr. H. Moch. Ansari Saleh</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">( .................................................... )</div>
                    <div class="signature-nip">NIP. .....................................</div>
                </div>
                <div class="signature-box">
                    <div class="sig-role">Mengetahui,</div>
                    <div class="sig-unit">Kepala Instalasi K3RS</div>
                    <div class="sig-hospital">RSUD dr. H. Moch. Ansari Saleh Banjarmasin</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">( .................................................... )</div>
                    <div class="signature-nip">NIP. .....................................</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer (tampil saat dicetak) --}}
    <div class="print-footer">
        <span>HAFIS &ndash; Hospital APAR &amp; Fire Inspection System &bull; Dokumen Resmi K3RS (Format Kertas: A4 {{ ucfirst($orientation) }})</span>
        <span>RSUD dr. H. Moch. Ansari Saleh Banjarmasin</span>
    </div>

    <script>
        function autoFitPrintPages() {
            const container = document.getElementById('printContainer');
            if (!container) return;

            // Reset dynamic page count and minHeight to measure natural content scrollHeight
            container.style.setProperty('--print-pages', '1');
            container.style.minHeight = '0px';

            const isLandscape = '{{ $orientation }}' === 'landscape';
            const pageHeightMm = isLandscape ? 185 : 272;
            const mmToPx = 96 / 25.4;
            const pageHeightPx = pageHeightMm * mmToPx;

            // Ukur computed padding vertikal kontainer pada tampilan layar (10mm atas + 15mm bawah = 25mm).
            // Pada media cetak (@media print), padding di-reset ke 0 karena margin fisik ditangani @page.
            // Pengurangan ini memastikan hanya tinggi isi konten riil yang diukur terhadap kapasitas cetak.
            const style = window.getComputedStyle(container);
            const padTop = parseFloat(style.paddingTop) || 0;
            const padBottom = parseFloat(style.paddingBottom) || 0;
            const borderTop = parseFloat(style.borderTopWidth) || 0;
            const borderBottom = parseFloat(style.borderBottomWidth) || 0;
            const verticalInset = padTop + padBottom + borderTop + borderBottom;

            // Tinggi konten murni tanpa padding layar kontainer
            const contentHeight = Math.max(container.scrollHeight - verticalInset, 0);

            const thead = document.querySelector('table.data-table thead');
            const theadHeight = thead ? thead.offsetHeight : 0;

            let remHeight = contentHeight;
            let pages = 1;
            if (remHeight > pageHeightPx) {
                remHeight -= pageHeightPx;
                const subPageCap = Math.max(pageHeightPx - theadHeight, 100);
                pages += Math.ceil(remHeight / subPageCap);
            }

            container.style.removeProperty('min-height');
            container.style.setProperty('--print-pages', pages.toString());
        }

        window.addEventListener('DOMContentLoaded', autoFitPrintPages);
        window.addEventListener('load', autoFitPrintPages);
        window.addEventListener('resize', autoFitPrintPages);
        window.addEventListener('beforeprint', autoFitPrintPages);
        autoFitPrintPages();
    </script>
</body>
</html>
