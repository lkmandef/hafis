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
            margin: 10mm 10mm 14mm 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.35;
            color: #0f172a;
            background: #f1f5f9;
            margin: 0;
            padding: 24px 12px;
        }

        /* Toolbar Aksi Pratinjau Layar */
        .no-print {
            max-width: {{ $orientation === 'landscape' ? '297mm' : '210mm' }};
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 8.5pt;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.12);
        }

        .no-print-meta {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .no-print-badge {
            background: #334155;
            color: #f8fafc;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .no-print-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .no-print button, .no-print a {
            border: none;
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 8pt;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s ease, opacity 0.15s ease;
        }

        .btn-print {
            background: #ffffff;
            color: #0f172a;
        }
        .btn-print:hover {
            background: #f8fafc;
            opacity: 0.95;
        }

        .btn-close-preview, .btn-back {
            background: #334155;
            color: #f8fafc;
        }
        .btn-close-preview:hover, .btn-back:hover {
            background: #475569;
        }

        /* Lembar Cetak A4 Fisik */
        .print-container {
            width: {{ $orientation === 'landscape' ? '297mm' : '210mm' }};
            max-width: 100%;
            min-height: {{ $orientation === 'landscape' ? '210mm' : '297mm' }};
            margin: 0 auto;
            background: #ffffff;
            padding: 10mm 10mm 14mm 10mm;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
        }

        .report-content {
            flex: 1 0 auto;
        }

        /* KOP SURAT RESMI PEMPROV KALSEL / RSUD ANSARI SALEH (SVG) */
        .header-kop {
            width: 100%;
            text-align: center;
            margin-bottom: 6px;
        }

        .kop-svg-img {
            width: {{ $orientation === 'landscape' ? '62%' : '85%' }};
            max-width: {{ $orientation === 'landscape' ? '175mm' : '162mm' }};
            height: auto;
            margin: 0 auto;
            display: block;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Judul Dokumen Laporan */
        .title-section {
            text-align: center;
            margin: 8px 0 6px 0;
        }

        .title-section h3 {
            font-size: 11pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            color: #000000;
            letter-spacing: 0.3px;
        }

        /* Baris Metadata Cetak & Ringkasan Formal Di Atas Tabel */
        .report-meta-bar {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
            gap: 12px;
        }

        .report-summary-formal {
            font-size: 8pt;
            color: #000000;
            margin-bottom: 0;
            line-height: 1.35;
        }

        .report-date-formal {
            font-size: 7.5pt;
            color: #000000;
            font-weight: 500;
            white-space: nowrap;
            text-align: right;
            margin-bottom: 0;
        }

        /* Ringkasan Filter/Parameter Laporan */
        .filter-summary {
            background: #fafafa;
            border: 1px solid #cbd5e1;
            padding: 4px 8px;
            margin-bottom: 10px;
            font-size: 7.5pt;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 14px;
        }

        .filter-heading {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7pt;
            color: #334155;
            letter-spacing: 0.2px;
        }

        .filter-item {
            display: inline-flex;
            gap: 4px;
        }

        .filter-item .label {
            color: #475569;
            font-weight: 500;
        }

        .filter-item .val {
            color: #000000;
            font-weight: 700;
        }

        /* Tabel Data Sederhana & Bersih Sesuai Dokumen Fisik */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.2pt;
            line-height: 1.25;
            margin-bottom: 12px;
            table-layout: auto;
            border: 1px solid #000000;
        }

        table.data-table thead {
            display: table-header-group;
        }

        table.data-table tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        table.data-table th, table.data-table td {
            border: 1px solid #000000;
            padding: 4px 5px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
            color: #000000;
        }

        table.data-table th {
            background-color: transparent;
            color: #000000;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7pt;
            letter-spacing: 0.2px;
            text-align: center;
            border: 1px solid #000000;
        }

        table.data-table tbody tr {
            background-color: transparent;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: transparent;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
        .font-bold { font-weight: 700; }

        /* Status & Badge Respon */
        .status-normal {
            font-weight: 600;
            color: #000000;
        }

        .status-perhatian {
            font-weight: 700;
            color: #000000;
        }

        .status-kritis {
            font-weight: 800;
            color: #000000;
            text-decoration: underline;
        }

        .badge {
            display: inline;
            padding: 0;
            border-radius: 0;
            font-size: inherit;
            border: none;
            background: transparent !important;
            color: #000000 !important;
        }
        .badge-success { font-weight: 600; }
        .badge-warning { font-weight: 700; }
        .badge-danger  { font-weight: 800; }
        .badge-neutral { font-weight: 400; }

        /* Blok Pengesahan & Tanda Tangan */
        .footer-signatures {
            margin-top: auto;
            break-inside: avoid;
            page-break-inside: avoid;
            font-size: 7.5pt;
            padding-top: 18px;
        }

        .signature-city-date {
            text-align: right;
            margin-bottom: 8px;
            font-size: 7.5pt;
            padding-right: 18px;
        }

        .signature-cols {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .signature-box {
            width: 230px;
            text-align: center;
        }

        .sig-role {
            font-weight: 400;
            font-size: 7.5pt;
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
            height: 52px;
        }

        .signature-name {
            font-weight: 700;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
            min-height: 16px;
        }

        .signature-nip {
            font-size: 7pt;
            color: #334155;
            margin-top: 2px;
        }

        .print-footer {
            display: none;
        }

        /* MEDIA CETAK (@media print) */
        @media print {
            * {
                color: #000000 !important;
                text-shadow: none !important;
                box-shadow: none !important;
            }

            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                font-family: "Liberation Sans", Arial, "Helvetica Neue", sans-serif;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                display: block !important;
            }

            .report-content {
                display: block !important;
            }

            .header-kop {
                width: 100% !important;
                text-align: center !important;
                margin-bottom: 6px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .kop-svg-img {
                width: {{ $orientation === 'landscape' ? '62%' : '85%' }} !important;
                max-width: {{ $orientation === 'landscape' ? '175mm' : '162mm' }} !important;
                height: auto !important;
                margin: 0 auto !important;
                display: block !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .report-meta-bar {
                display: flex !important;
                justify-content: space-between !important;
                align-items: baseline !important;
                margin-bottom: 8px !important;
                gap: 12px !important;
            }

            .report-meta-bar .report-summary-formal {
                font-size: 8pt !important;
                color: #000000 !important;
                margin-bottom: 0 !important;
            }

            .report-meta-bar .report-date-formal {
                font-size: 7.5pt !important;
                color: #000000 !important;
                white-space: nowrap !important;
                text-align: right !important;
            }

            .filter-summary {
                background: transparent !important;
                border: 1px solid #000000 !important;
            }

            .filter-heading, .filter-item .label, .sig-hospital, .signature-nip {
                color: #000000 !important;
            }

            table.data-table,
            table.data-table th, 
            table.data-table td {
                border: 1px solid #000000 !important;
            }

            table.data-table th {
                background-color: transparent !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
            }

            table.data-table tbody tr,
            table.data-table tbody tr:nth-child(even) {
                background-color: transparent !important;
            }

            .footer-signatures {
                margin-top: 20px !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                padding-top: 12px !important;
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
                border-top: 1px solid #000000;
                font-size: 6.5pt;
                color: #000000 !important;
            }
        }
    </style>
</head>
<body>

    {{-- Bar Aksi Pratinjau Layar (disembunyikan otomatis saat dicetak) --}}
    <div class="no-print">
        <div class="no-print-meta">
            <span class="no-print-badge">A4 {{ strtoupper($orientation) }}</span>
            <strong>Mode Pratinjau Cetak &bull; Dokumen Resmi HAFIS RSUD</strong>
        </div>
        <div class="no-print-actions">
            <button type="button"
                    class="btn-close-preview"
                    onclick="closePrintPreview()"
                    title="Tutup pratinjau cetak dan kembali ke aplikasi (Shortcut: Esc)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Tutup Pratinjau (Esc)
            </button>
            <button type="button" class="btn-print" onclick="window.print()" title="Cetak dokumen ke printer atau simpan sebagai PDF (Ctrl + P)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Cetak Sekarang (Ctrl + P)
            </button>
        </div>
    </div>

    {{-- Lembar Fisik Dokumen Cetak --}}
    <div class="print-container" id="printContainer">
        {{-- KOP Surat Resmi RSUD dr. H. Moch. Ansari Saleh (Format Vektor SVG) --}}
        <div class="header-kop">
            <img src="{{ asset('images/brand/Kop_RSUD_Ansari_Saleh.svg') }}"
                 alt="Kop Surat Resmi RSUD dr. H. Moch. Ansari Saleh"
                 class="kop-svg-img" />
        </div>

        {{-- Judul Laporan Resmi --}}
        <div class="title-section">
            <h3>{{ $reportTitle }}</h3>
        </div>

        {{-- Ringkasan Parameter Filter Aktif --}}
        @if(!empty($filters))
            <div class="filter-summary">
                <span class="filter-heading">Parameter:</span>
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

        {{-- Tanda Tangan Formal Pengesahan K3RS --}}
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

    {{-- Footer Cetak (Halaman Bawah Cetak) --}}
    <div class="print-footer">
        <span>HAFIS &ndash; Hospital APAR &amp; Fire Inspection System &bull; Dokumen Resmi K3RS (Format: A4 {{ ucfirst($orientation) }})</span>
        <span>RSUD dr. H. Moch. Ansari Saleh Banjarmasin</span>
    </div>

    <script>
        function closePrintPreview() {
            // 1. Coba tutup tab peramban saat dibuka di tab baru
            window.close();

            // 2. Fallback: jika browser membatasi window.close (misal tab dibuka langsung via URL manual),
            // kembalikan navigasi ke halaman web laporan
            setTimeout(function() {
                if (!window.closed) {
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        window.location.href = "{{ request()->fullUrlWithoutQuery(['format']) }}";
                    }
                }
            }, 120);
        }

        // Shortcut keyboard: tombol 'Esc' langsung menutup pratinjau
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' || event.key === 'Esc') {
                event.preventDefault();
                closePrintPreview();
            }
        });

        function autoFitPrintPages() {
            // Memastikan tata letak siap dicetak melalui CSS Paged Media standar (@page & page-break)
            const container = document.getElementById('printContainer');
            if (container) {
                container.setAttribute('data-print-ready', 'true');
            }
        }

        window.addEventListener('DOMContentLoaded', autoFitPrintPages);
        window.addEventListener('beforeprint', autoFitPrintPages);
        autoFitPrintPages();
    </script>
</body>
</html>
