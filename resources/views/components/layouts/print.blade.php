@props([
    'title' => 'Laporan HAFIS Rumah Sakit',
    'reportTitle' => 'LAPORAN REKAPITULASI APAR',
    'filters' => []
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
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #0f172a;
            background: #ffffff;
            margin: 0;
            padding: 20px;
        }

        .no-print {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        .no-print button, .no-print a {
            background: #ffffff;
            color: #0f172a;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 8.5pt;
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

        .header-kop {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-text h1 {
            font-size: 14pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }

        .brand-text h2 {
            font-size: 9.5pt;
            font-weight: 600;
            margin: 2px 0 0 0;
            color: #334155;
        }

        .brand-text p {
            font-size: 8pt;
            margin: 2px 0 0 0;
            color: #64748b;
        }

        .report-meta {
            text-align: right;
            font-size: 8pt;
            color: #475569;
        }

        .report-meta .doc-code {
            font-family: monospace;
            font-weight: 700;
            color: #0f172a;
            font-size: 9pt;
        }

        .title-section {
            text-align: center;
            margin-bottom: 14px;
        }

        .title-section h3 {
            font-size: 12pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            color: #0f172a;
        }

        .filter-summary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 14px;
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
            font-size: 8pt;
            margin-bottom: 16px;
        }

        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            text-align: left;
            vertical-align: top;
        }

        table.data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
        .font-bold { font-weight: 700; }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 700;
            border: 1px solid transparent;
        }
        .badge-success { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
        .badge-warning { background: #fffbeb; color: #92400e; border-color: #fde68a; }
        .badge-danger { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
        .badge-neutral { background: #f1f5f9; color: #334155; border-color: #cbd5e1; }

        .footer-signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            font-size: 8.5pt;
        }

        .signature-box {
            width: 220px;
            text-align: center;
        }

        .signature-space {
            height: 60px;
        }

        .signature-line {
            border-bottom: 1px solid #0f172a;
            font-weight: 700;
            padding-bottom: 2px;
        }

        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Aksi Layar (Disembunyikan Otomatis Saat Dicetak) -->
    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
            <strong>Mode Cetak Laporan Resmi HAFIS</strong>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="javascript:window.history.back()">
                &larr; Kembali ke Tampilan Web
            </a>
            <button type="button" onclick="window.print()">
                Cetak Sekarang (Ctrl + P)
            </button>
        </div>
    </div>

    <!-- Kop Header Laporan Resmi -->
    <div class="header-kop">
        <div class="brand-text">
            <h1>HAFIS : Hospital APAR & Fire Inspection System</h1>
            <h2>Instalasi Keselamatan & Kesehatan Kerja Rumah Sakit (K3RS)</h2>
            <p>Sistem Pengawasan dan Pemeliharaan Sarana Proteksi Kebakaran Rumah Sakit</p>
        </div>
        <div class="report-meta">
            <div class="doc-code">DOC: K3RS-HAFIS-{{ date('Ymd') }}</div>
            <div>Tanggal Cetak: <strong>{{ date('d/m/Y H:i') }} WIB</strong></div>
            <div>Petugas Cetak: <strong>{{ auth()->user()?->name ?? 'Petugas K3RS' }}</strong></div>
        </div>
    </div>

    <!-- Judul Laporan -->
    <div class="title-section">
        <h3>{{ $reportTitle }}</h3>
    </div>

    <!-- Ringkasan Filter Aktif -->
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

    <!-- Konten Utama Laporan -->
    <div class="report-content">
        {{ $slot }}
    </div>

    <!-- Bagian Tanda Tangan / Verifikasi K3RS -->
    <div class="footer-signatures">
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div style="font-weight: 600;">Kepala Instalasi K3RS</div>
            <div class="signature-space"></div>
            <div class="signature-line">( .................................................... )</div>
            <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">NIP / ID Petugas</div>
        </div>

        <div class="signature-box">
            <div>Dibuat / Diverifikasi Oleh,</div>
            <div style="font-weight: 600;">Petugas Inspeksi Lapangan</div>
            <div class="signature-space"></div>
            <div class="signature-line">{{ auth()->user()?->name ?? '( .................................................... )' }}</div>
            <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Staf Teknis K3RS</div>
        </div>
    </div>

</body>
</html>
