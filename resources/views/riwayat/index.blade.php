<x-layouts.app :title="request()->has('preview_print') ? 'HAFIS — Laporan' : 'HAFIS — Riwayat Pemeriksaan'">
    @php
    $isPrintPreview = request()->has('preview_print');

    $riwayatList = [
    [
    'id' => 'INS-2026-001',
    'kode' => 'APAR-IGD-01',
    'jenis' => 'Powder 6 Kg',
    'lokasi' => 'IGD Zona Triase, Lt. 1',
    'tanggal' => '02/10/2026',
    'tanggal_cetak' => '02/10/2026',
    'pemeriksa' => 'Budi Santoso',
    'kondisi' => 'Baik',
    'kondisi_print' => 'Baik',
    'status_variant' => 'success',
    'catatan' => 'Jarum tekanan hijau stabil, segel kawat dan pin utuh, selang fleksibel.',
    ],
    [
    'id' => 'INS-2026-002',
    'kode' => 'APAR-OK-01',
    'jenis' => 'CO₂ 5 Kg',
    'lokasi' => 'Bedah Sentral (OK 3), Lt. 3',
    'tanggal' => '02/10/2026',
    'tanggal_cetak' => '02/10/2026',
    'pemeriksa' => 'Rudi Hartono',
    'kondisi' => 'Baik',
    'kondisi_print' => 'Baik',
    'status_variant' => 'success',
    'catatan' => 'Selang elastis, posisi bracket kokoh dan bebas dari halangan brankar.',
    ],
    [
    'id' => 'INS-2026-003',
    'kode' => 'APAR-LAB-02',
    'jenis' => 'Powder 3 Kg',
    'lokasi' => 'Laboratorium Kimia, Lt. 2',
    'tanggal' => '01/10/2026',
    'tanggal_cetak' => '01/10/2026',
    'pemeriksa' => 'Budi Santoso',
    'kondisi' => 'Perlu Perhatian',
    'kondisi_print' => 'Perlu Perhatian',
    'status_variant' => 'warning',
    'catatan' => 'Stiker kadaluarsa sedikit pudar, jarum mendekati batas bawah zona hijau.',
    ],
    [
    'id' => 'INS-2026-004',
    'kode' => 'APAR-RWI-04',
    'jenis' => 'Powder 6 Kg',
    'lokasi' => 'Rawat Inap Melati, Lt. 2',
    'tanggal' => '01/10/2026',
    'tanggal_cetak' => '01/10/2026',
    'pemeriksa' => 'Siti Rahma',
    'kondisi' => 'Baik',
    'kondisi_print' => 'Baik',
    'status_variant' => 'success',
    'catatan' => 'Kondisi fisik tabung bersih, kartu kontrol fisik telah diparaf.',
    ],
    [
    'id' => 'INS-2026-005',
    'kode' => 'APAR-GIZ-01',
    'jenis' => 'Wet Chemical 6 L',
    'lokasi' => 'Instalasi Dapur Gizi, Lt. 1',
    'tanggal' => '30/09/2026',
    'tanggal_cetak' => '30/09/2026',
    'pemeriksa' => 'Hendra Wijaya',
    'kondisi' => 'Rusak / Perlu Tindakan',
    'kondisi_print' => 'Rusak/Perlu Penggantian',
    'status_variant' => 'danger',
    'catatan' => 'Selang karet retak getas, tekanan turun ke zona merah. Perlu isi ulang segera.',
    ],
    [
    'id' => 'INS-2026-006',
    'kode' => 'APAR-RAD-01',
    'jenis' => 'Clean Agent 5 Kg',
    'lokasi' => 'Ruang MRI / Radiologi, Lt. 1',
    'tanggal' => '28/09/2026',
    'tanggal_cetak' => '28/09/2026',
    'pemeriksa' => 'Rudi Hartono',
    'kondisi' => 'Baik',
    'kondisi_print' => 'Baik',
    'status_variant' => 'success',
    'catatan' => 'Unit non-ferromagnetik terpasang sempurna di dekat operator konsol.',
    ],
    [
    'id' => 'INS-2026-007',
    'kode' => 'APAR-FAS-03',
    'jenis' => 'CO₂ 6.8 Kg',
    'lokasi' => 'Genset & Utilitas, Lt. B1',
    'tanggal' => '25/09/2026',
    'tanggal_cetak' => '25/09/2026',
    'pemeriksa' => 'Hendra Wijaya',
    'kondisi' => 'Perlu Perhatian',
    'kondisi_print' => 'Perlu Perhatian',
    'status_variant' => 'warning',
    'catatan' => 'Terdapat percikan debu oli pada tabung, korosi ringan pada bagian tapak dasar.',
    ],
    [
    'id' => 'INS-2026-008',
    'kode' => 'APAR-POLI-05',
    'jenis' => 'Powder 3 Kg',
    'lokasi' => 'Poliklinik Rawat Jalan, Lt. 2',
    'tanggal' => '20/09/2026',
    'tanggal_cetak' => '20/09/2026',
    'pemeriksa' => 'Siti Rahma',
    'kondisi' => 'Baik',
    'kondisi_print' => 'Baik',
    'status_variant' => 'success',
    'catatan' => 'Akses rambu penunjuk arah terlihat jelas dari koridor pasien.',
    ],
    ];
    @endphp

    @if ($isPrintPreview)
    <!-- Floating Action Bar for Print Preview on Screen -->
    <div class="no-print mb-4 p-3 bg-slate-900 text-white rounded-xl shadow-md flex items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
            <span class="font-semibold">Mode Pratinjau Cetak Laporan (Format A4 Rumah Sakit)</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url('/riwayat') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
                Kembali ke Tampilan Web
            </a>
            <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-lg bg-white text-slate-900 font-bold hover:bg-slate-100 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Sekarang (Ctrl + P)</span>
            </button>
        </div>
    </div>
    @endif

    <!-- ==========================================================================
         WEB SCREEN VIEW (Hidden when previewing print or in Print Mode)
         ========================================================================== -->
    <div class="{{ $isPrintPreview ? 'hidden' : 'no-print print:hidden' }}">
        <!-- 1. Page Header -->
        <x-page-header
            title="Riwayat Pemeriksaan"
            description="Catatan pemeriksaan APAR yang telah dilakukan">
            <x-slot:actions>
                <!-- Pratinjau Cetak / Cetak Rekap Button -->
                <div class="flex items-center gap-1.5">
                    <a href="{{ url('/riwayat?preview_print=1') }}" class="px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg transition-colors flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[38px]">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>Pratinjau Cetak</span>
                    </a>

                    <x-button variant="secondary" onclick="window.print()">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Cetak Rekap</span>
                    </x-button>
                </div>

                <!-- Pemeriksaan Baru Button -->
                <x-button variant="primary" href="{{ url('/pemeriksaan') }}">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Pemeriksaan Baru</span>
                </x-button>
            </x-slot:actions>
        </x-page-header>

        <!-- Card Tunggal untuk Toolbar, Tabel Web, dan Pagination -->
        <x-card class="!p-0 overflow-hidden">
            <!-- 2. Toolbar Sederhana -->
            <div class="p-3.5 sm:p-4 border-b border-slate-200 bg-white">
                <form action="#" method="GET" onsubmit="event.preventDefault();" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                    <!-- Search & Filter Controls -->
                    <div class="flex flex-wrap items-center gap-2.5 flex-1">
                        <!-- Search Input -->
                        <div class="relative min-w-[220px] flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input
                                type="text"
                                placeholder="Cari kode APAR atau lokasi..."
                                class="w-full pl-9 pr-3 py-1.5 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none transition min-h-[38px]" />
                        </div>

                        <!-- Filter Kondisi -->
                        <div class="relative">
                            <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-1.5 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[38px] cursor-pointer">
                                <option value="">Semua Kondisi</option>
                                <option value="baik">Baik</option>
                                <option value="perhatian">Perlu Perhatian</option>
                                <option value="rusak">Rusak / Perlu Tindakan</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </div>

                        <!-- Filter Periode / Tanggal -->
                        <div class="relative">
                            <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-1.5 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[38px] cursor-pointer">
                                <option value="">Semua Periode</option>
                                <option value="bulan_ini" selected>Bulan Ini (Okt 2026)</option>
                                <option value="30_hari">30 Hari Terakhir</option>
                                <option value="triwulan_3">Kuartal III 2026</option>
                                <option value="tahun_2026">Tahun 2026</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </div>

                        <!-- Filter Pemeriksa -->
                        <div class="relative">
                            <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-1.5 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[38px] cursor-pointer">
                                <option value="">Semua Pemeriksa</option>
                                <option value="budi">Budi Santoso (K3RS)</option>
                                <option value="rudi">Rudi Hartono (K3RS)</option>
                                <option value="siti">Siti Rahma (K3RS)</option>
                                <option value="hendra">Hendra Wijaya (K3RS)</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Tombol Reset Filter -->
                    <div class="flex items-center justify-end">
                        <a href="{{ url('/riwayat') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-slate-900">
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabel Riwayat Pemeriksaan (Web View) -->
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-800 border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Kode APAR</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Lokasi</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Tanggal Pemeriksaan</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Pemeriksa</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Kondisi Hasil</th>
                            <th scope="col" class="px-4 py-3">Catatan Singkat</th>
                            <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($riwayatList as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- 1. Kode APAR -->
                            <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap tabular-nums">
                                <a href="{{ url('/riwayat/' . $item['id']) }}" class="hover:underline text-slate-900">
                                    {{ $item['kode'] }}
                                </a>
                            </td>

                            <!-- 2. Lokasi -->
                            <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                                {{ $item['lokasi'] }}
                            </td>

                            <!-- 3. Tanggal Pemeriksaan -->
                            <td class="px-4 py-3 font-mono text-slate-600 whitespace-nowrap tabular-nums text-xs">
                                {{ $item['tanggal'] }}
                            </td>

                            <!-- 4. Pemeriksa -->
                            <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">
                                {{ $item['pemeriksa'] }}
                            </td>

                            <!-- 5. Kondisi Hasil (Semantic status color) -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge :variant="$item['status_variant']" :dot="true">
                                    {{ $item['kondisi'] }}
                                </x-badge>
                            </td>

                            <!-- 6. Catatan Singkat -->
                            <td class="px-4 py-3 text-slate-600 max-w-xs truncate" title="{{ $item['catatan'] }}">
                                {{ $item['catatan'] }}
                            </td>

                            <!-- 7. Aksi -->
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Lihat Detail -->
                                    <a href="{{ url('/riwayat/' . $item['id']) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                                        Lihat
                                    </a>

                                    <!-- Opsi Cetak Lembar Hasil -->
                                    <button
                                        type="button"
                                        onclick="window.print()"
                                        title="Cetak Hasil Pemeriksaan"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-900"
                                        aria-label="Cetak Hasil {{ $item['kode'] }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination & Summary Footer (Web View) -->
            <div class="px-4 py-3 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 bg-white">
                <div>
                    Menampilkan <span class="font-semibold text-slate-800">1</span> sampai <span class="font-semibold text-slate-800">8</span> dari <span class="font-semibold text-slate-800">124</span> total catatan pemeriksaan
                </div>

                <div class="flex items-center gap-1">
                    <button type="button" disabled class="px-2.5 py-1 rounded border border-slate-200 text-slate-300 cursor-not-allowed">
                        Sebelumnya
                    </button>
                    <span class="px-2.5 py-1 rounded bg-slate-900 text-white font-medium">1</span>
                    <button type="button" class="px-2.5 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">2</button>
                    <button type="button" class="px-2.5 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">3</button>
                    <span class="px-1 text-slate-400">...</span>
                    <button type="button" class="px-2.5 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">16</button>
                    <button type="button" class="px-2.5 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </x-card>
    </div>

    <!-- ==========================================================================
         DEDICATED PRINT REPORT CONTAINER (Visible in Print Mode & Print Preview)
         ========================================================================== -->
    <div class="{{ $isPrintPreview ? 'block bg-white p-6 sm:p-8 rounded-xl border border-slate-200 shadow-sm max-w-4xl mx-auto' : 'hidden print:block' }} font-sans text-black w-full">
        <!-- 1. Header Cetak Administratif HAFIS -->
        <div class="border-b-2 border-black pb-2.5 mb-3.5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/brand/hafis-logo.svg') }}" alt="HAFIS" class="h-10 w-auto object-contain shrink-0" />
                    <div>
                        <h1 class="text-sm font-extrabold tracking-tight text-black leading-tight">HAFIS</h1>
                        <p class="text-[8pt] text-slate-700 font-medium leading-tight">Hospital APAR & Fire Inspection System</p>
                        <h2 class="text-xs font-bold text-black uppercase tracking-wider mt-1">LAPORAN REKAP PEMERIKSAAN APAR</h2>
                    </div>
                </div>
                <div class="text-right text-[8.5pt] text-slate-700 leading-tight shrink-0">
                    <p><span class="font-semibold text-black">Periode:</span> 01/09/2026 - 02/10/2026</p>
                    <p class="mt-0.5"><span class="font-semibold text-black">Tanggal Cetak:</span> {{ date('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- 2. Ringkasan Cetak (Compact & Horizontal) -->
        <div class="mb-3.5 px-3.5 py-2 border border-slate-300 bg-slate-50 flex items-center justify-between text-[8.5pt] print-avoid-break">
            <div><span class="text-slate-600">Total Pemeriksaan:</span> <strong class="text-black font-bold">8 Unit</strong></div>
            <div><span class="text-slate-600">Kondisi Baik:</span> <strong class="text-black font-bold">5 Unit</strong></div>
            <div><span class="text-slate-600">Perlu Perhatian:</span> <strong class="text-black font-bold">2 Unit</strong></div>
            <div><span class="text-slate-600">Rusak / Perlu Tindakan:</span> <strong class="text-black font-bold">1 Unit</strong></div>
        </div>

        <!-- 3. Tabel Print Khusus A4 (7 Kolom Lengkap, Zero Truncation, KONDISI Utuh) -->
        <table class="print-table border border-black text-[8.5pt] w-full" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr class="bg-slate-100 border-b border-black text-black font-bold text-[8.5pt]">
                    <th class="border border-slate-400 py-1.5 px-2 text-center" style="width: 5%;">NO</th>
                    <th class="border border-slate-400 py-1.5 px-2 text-left" style="width: 15%;">KODE APAR</th>
                    <th class="border border-slate-400 py-1.5 px-2 text-left" style="width: 14%;">JENIS</th>
                    <th class="border border-slate-400 py-1.5 px-2 text-left" style="width: 25%;">LOKASI</th>
                    <th class="border border-slate-400 py-1.5 px-2 text-center" style="width: 13%;">TGL PEMERIKSAAN</th>
                    <th class="border border-slate-400 py-1.5 px-2 text-left" style="width: 13%;">PEMERIKSA</th>
                    <th class="border border-slate-400 py-1.5 px-2 text-center" style="width: 15%;">KONDISI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($riwayatList as $idx => $item)
                <tr class="border-b border-slate-300">
                    <!-- 1. NO -->
                    <td class="border border-slate-300 py-2 px-2 text-center font-mono">
                        {{ $idx + 1 }}
                    </td>

                    <!-- 2. KODE APAR -->
                    <td class="border border-slate-300 py-2 px-2 font-mono font-bold text-black" style="word-break: break-word;">
                        {{ $item['kode'] }}
                    </td>

                    <!-- 3. JENIS -->
                    <td class="border border-slate-300 py-2 px-2 text-slate-800" style="word-break: break-word;">
                        {{ $item['jenis'] }}
                    </td>

                    <!-- 4. LOKASI -->
                    <td class="border border-slate-300 py-2 px-2 text-slate-800" style="word-break: break-word;">
                        {{ $item['lokasi'] }}
                    </td>

                    <!-- 5. TGL PEMERIKSAAN (Format Pendek DD/MM/YYYY) -->
                    <td class="border border-slate-300 py-2 px-2 text-center font-mono tabular-nums text-slate-900 whitespace-nowrap">
                        {{ $item['tanggal_cetak'] }}
                    </td>

                    <!-- 6. PEMERIKSA -->
                    <td class="border border-slate-300 py-2 px-2 text-slate-800" style="word-break: break-word;">
                        {{ $item['pemeriksa'] }}
                    </td>

                    <!-- 7. KONDISI (Teks Lengkap Tanpa Terpotong / Tanpa Ellipsis) -->
                    <td class="border border-slate-300 py-2 px-2 text-center font-semibold text-slate-900 leading-snug" style="white-space: normal; overflow: visible; text-overflow: clip; word-break: break-word;">
                        {{ $item['kondisi_print'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- 4. Area Pengesahan / Tanda Tangan Administrasi RS -->
        <div class="mt-8 pt-4 grid grid-cols-2 text-[8.5pt] print-avoid-break">
            <div class="text-left">
                <p class="text-slate-600 mb-12">Mengetahui,<br><span class="font-bold text-black">Kepala Instalasi K3RS</span></p>
                <p class="font-bold text-black underline underline-offset-4">dr. H. Hendrawan, M.Kes</p>
                <p class="text-[8pt] text-slate-500 font-mono">NIP: 197509122002121003</p>
            </div>
            <div class="text-right">
                <p class="text-slate-600 mb-12">Petugas Pemeriksa,<br><span class="font-bold text-black">Pelaksana Inspeksi Lapangan</span></p>
                <p class="font-bold text-black underline underline-offset-4">Budi Santoso, A.Md.Kep</p>
                <p class="text-[8pt] text-slate-500 font-mono">NIP: 19880415-K3RS</p>
            </div>
        </div>
    </div>
</x-layouts.app>