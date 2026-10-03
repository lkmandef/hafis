<x-layouts.app :title="request()->has('preview_print') ? 'HAFIS : Cetak Lembar' : 'HAFIS : Detail Riwayat'">
    @php
    $isPrintPreview = request()->has('preview_print');

    // Data pemeriksaan APAR
    $inspeksi = [
    'id' => 'INS-2026-001',
    'apar_id' => 'APAR-IGD-01',
    'jenis' => 'Dry Chemical Powder',
    'kapasitas' => '6 Kg',
    'lokasi' => 'Gedung Utama (IGD) - Lantai 1',
    'detail_lokasi' => 'Koridor depan pintu darurat triase (Bracket 120 cm)',
    'tanggal' => '02/10/2026',
    'tanggal_cetak' => '02/10/2026',
    'pemeriksa' => 'Budi Santoso',
    'pemeriksa_nip' => '19880415-K3RS',
    'kondisi' => 'Baik',
    'kondisi_variant' => 'success',
    'catatan' => 'APAR dalam kondisi baik dan siap digunakan. Jarum tekanan stabil di zona hijau (14 bar), segel timah dan pin pengaman terpasang utuh, selang elastis tanpa keretakan, serta akses penempatan bersih dari hambatan brankar.',
    'checklist' => [
    ['no' => 1, 'item' => 'Posisi APAR sesuai lokasi', 'hasil' => 'Baik'],
    ['no' => 2, 'item' => 'Akses menuju APAR tidak terhalang', 'hasil' => 'Baik'],
    ['no' => 3, 'item' => 'Kondisi tabung baik', 'hasil' => 'Baik'],
    ['no' => 4, 'item' => 'Segel pengaman masih terpasang', 'hasil' => 'Baik'],
    ['no' => 5, 'item' => 'Pin pengaman tersedia', 'hasil' => 'Baik'],
    ['no' => 6, 'item' => 'Pressure gauge dalam kondisi normal', 'hasil' => 'Baik'],
    ['no' => 7, 'item' => 'Selang/nozzle dalam kondisi baik', 'hasil' => 'Baik'],
    ['no' => 8, 'item' => 'Label/instruksi masih terbaca', 'hasil' => 'Baik'],
    ['no' => 9, 'item' => 'Tidak terdapat kebocoran', 'hasil' => 'Baik'],
    ['no' => 10, 'item' => 'Masa berlaku masih sesuai', 'hasil' => 'Baik'],
    ],
    'foto' => [
    'tersedia' => true,
    'nama' => 'foto_inspeksi_apar_igd_01.jpg',
    'keterangan' => 'Tabung & Manometer (02/10/2026)'
    ]
    ];
    @endphp

    @if ($isPrintPreview)
    <!-- Floating Bar for Print Preview on Screen -->
    <div class="no-print mb-4 p-3 bg-slate-900 text-white rounded-xl shadow-md flex items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
            <span class="font-semibold">Mode Pratinjau: Lembar Pemeriksaan APAR (1 Lembar Formal A4)</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url('/riwayat/' . $inspeksi['id']) }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
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
         WEB SCREEN VIEW (Hidden in Print Mode or Print Preview)
         ========================================================================== -->
    <div class="{{ $isPrintPreview ? 'hidden' : 'no-print print:hidden' }}">
        <!-- 1. Header with Breadcrumb & Actions -->
        <div class="pb-5 mb-6 border-b border-slate-200">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ url('/riwayat') }}" class="hover:text-slate-800 transition-colors">Riwayat Pemeriksaan</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">{{ $inspeksi['id'] }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                            Detail Pemeriksaan
                        </h1>
                        <span class="font-mono text-xs sm:text-sm font-bold text-slate-800 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded tabular-nums">
                            {{ $inspeksi['id'] }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Catatan hasil inspeksi fisik berkala unit proteksi kebakaran rumah sakit.
                    </p>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-2">
                    <!-- Pratinjau Lembar Cetak Button -->
                    <a href="{{ url('/riwayat/' . $inspeksi['id'] . '?preview_print=1') }}" class="px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg transition-colors flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[38px]">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>Pratinjau Lembar</span>
                    </a>

                    <!-- Cetak Lembar Button -->
                    <x-button variant="secondary" onclick="window.print()">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Cetak Lembar</span>
                    </x-button>

                    <!-- Tombol Kembali -->
                    <x-button variant="secondary" href="{{ url('/riwayat') }}">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke Riwayat</span>
                    </x-button>
                </div>
            </div>
        </div>

        <div class="space-y-6 max-w-5xl">
            <!-- 2. Ringkasan Informasi: Informasi APAR & Informasi Pemeriksaan (2 Kolom Bersanding) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Informasi APAR -->
                <x-card title="Informasi Unit APAR" subtitle="Identitas fisik unit yang diperiksa.">
                    <dl class="space-y-3 text-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <dt class="text-slate-500 font-medium">Kode Unit</dt>
                            <dd class="shrink-0">
                                <a href="{{ url('/apar/detail') }}" style="white-space: nowrap;" class="font-mono font-bold text-slate-900 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded border border-slate-200 tabular-nums inline-flex items-center gap-1.5 transition-colors whitespace-nowrap">
                                    <span class="whitespace-nowrap">{{ $inspeksi['apar_id'] }}</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </dd>
                        </div>

                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <dt class="text-slate-500 font-medium">Jenis Media</dt>
                            <dd class="font-semibold text-slate-800">{{ $inspeksi['jenis'] }}</dd>
                        </div>

                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <dt class="text-slate-500 font-medium">Kapasitas Tabung</dt>
                            <dd class="font-semibold text-slate-800">{{ $inspeksi['kapasitas'] }}</dd>
                        </div>

                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <dt class="text-slate-500 font-medium">Lokasi Terpasang</dt>
                            <dd class="font-semibold text-slate-800">{{ $inspeksi['lokasi'] }}</dd>
                        </div>

                        <div class="pt-0.5">
                            <dt class="text-slate-500 font-medium mb-1">Detail Posisi Penempatan</dt>
                            <dd class="text-slate-700 bg-slate-50 p-2 rounded border border-slate-100">{{ $inspeksi['detail_lokasi'] }}</dd>
                        </div>
                    </dl>
                </x-card>

                <!-- Informasi Pemeriksaan -->
                <x-card title="Informasi Pelaksanaan" subtitle="Waktu, pemeriksa, dan kesimpulan operasional.">
                    <dl class="space-y-3 text-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <dt class="text-slate-500 font-medium">Waktu Pemeriksaan</dt>
                            <dd class="font-mono font-semibold text-slate-800 tabular-nums">{{ $inspeksi['tanggal'] }}</dd>
                        </div>

                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <dt class="text-slate-500 font-medium">Petugas Pemeriksa</dt>
                            <dd class="font-medium text-slate-800">
                                {{ $inspeksi['pemeriksa'] }}
                                <span class="text-[11px] text-slate-400 font-mono">(NIP: {{ $inspeksi['pemeriksa_nip'] }})</span>
                            </dd>
                        </div>

                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <dt class="text-slate-500 font-medium">Kondisi Hasil</dt>
                            <dd>
                                <x-badge :variant="$inspeksi['kondisi_variant']" :dot="true">
                                    {{ $inspeksi['kondisi'] }}
                                </x-badge>
                            </dd>
                        </div>

                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <dt class="text-slate-500 font-medium">Status Operasional</dt>
                            <dd class="text-emerald-700 font-semibold flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Siap Digunakan (Laik Pakai)</span>
                            </dd>
                        </div>

                        <div class="pt-0.5">
                            <dt class="text-slate-500 font-medium mb-1">Periode Inspeksi Lanjutan</dt>
                            <dd class="text-slate-700 bg-slate-50 p-2 rounded border border-slate-100">
                                Inspeksi berkala berikutnya dijadwalkan pada <span class="font-mono font-semibold text-slate-900 tabular-nums">02/11/2026</span>.
                            </dd>
                        </div>
                    </dl>
                </x-card>
            </div>

            <!-- 3. Checklist Pemeriksaan Lengkap (10 Item) -->
            <x-card
                title="Hasil Checklist Pemeriksaan Fisik"
                subtitle="Pemeriksaan visual 10 kriteria keselamatan tabung APAR di lokasi.">

                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="w-12 px-3.5 py-2.5 font-semibold text-slate-600 text-center">No.</th>
                                <th class="px-3.5 py-2.5 font-semibold text-slate-600">Parameter Kriteria Pemeriksaan</th>
                                <th class="w-32 px-3.5 py-2.5 font-semibold text-slate-600 text-center">Hasil Inspeksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($inspeksi['checklist'] as $chk)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-3.5 py-2.5 font-mono text-slate-400 text-center tabular-nums text-[11px]">
                                    {{ sprintf('%02d', $chk['no']) }}
                                </td>
                                <td class="px-3.5 py-2.5 font-medium text-slate-900">
                                    {{ $chk['item'] }}
                                </td>
                                <td class="px-3.5 py-2.5 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-semibold {{ $chk['hasil'] === 'Baik' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                        @if($chk['hasil'] === 'Baik')
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        @else
                                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        @endif
                                        <span>{{ $chk['hasil'] }}</span>
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- 4. Catatan Temuan Pemeriksaan -->
            <x-card title="Catatan Pemeriksaan Lapangan" subtitle="Keterangan tambahan dari petugas saat pemeriksaan.">
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 leading-relaxed font-sans">
                    {{ $inspeksi['catatan'] }}
                </div>
            </x-card>

            <!-- 5. Foto Dokumentasi Pemeriksaan -->
            <x-card title="Foto Dokumentasi Lapangan" subtitle="Dokumentasi fisik tabung dan manometer saat inspeksi dilakukan.">
                <div class="border border-slate-200 rounded-lg overflow-hidden bg-white max-w-sm">
                    <div class="h-36 bg-slate-100 border-b border-slate-200 flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                        <svg class="w-8 h-8 text-slate-400 mb-1" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-xs font-semibold text-slate-600">Dokumentasi Tabung & Manometer</span>
                        <span class="text-[10px] text-slate-400 mt-0.5 font-mono">Unit APAR-IGD-01 (Tekanan Stabil)</span>
                    </div>
                    <div class="p-2.5 text-xs flex items-center justify-between">
                        <div>
                            <span class="font-medium text-slate-800 block text-xs">{{ $inspeksi['foto']['nama'] }}</span>
                            <span class="text-[10px] text-slate-400">{{ $inspeksi['foto']['keterangan'] }}</span>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                            Terverifikasi
                        </span>
                    </div>
                </div>
            </x-card>

            <!-- 6. Footer Actions -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                <x-button variant="secondary" href="{{ url('/riwayat') }}">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Riwayat</span>
                </x-button>

                <div class="flex items-center gap-2">
                    <x-button variant="secondary" href="{{ url('/apar/detail') }}">
                        <span>Lihat Profil Unit APAR</span>
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         OFFICIAL HOSPITAL PRINT SHEET: "LEMBAR PEMERIKSAAN APAR"
         Single-page A4 Portrait Formal Document (Only visible in Print or Preview)
         ========================================================================== -->
    <div class="{{ $isPrintPreview ? 'block bg-white p-7 sm:p-9 rounded-xl border border-slate-300 shadow-md max-w-3xl mx-auto' : 'hidden print:block' }} font-sans text-black w-full" style="max-width: 190mm; margin-left: auto; margin-right: auto; line-height: 1.25;">

        <!-- HEADER DOKUMEN RESMI HAFIS -->
        <div class="border-b-2 border-black pb-2 mb-3">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/brand/hafis-logo.svg') }}" alt="HAFIS" class="h-10 w-auto object-contain shrink-0" />
                    <div>
                        <h1 class="text-sm font-extrabold tracking-tight text-black leading-tight">HAFIS</h1>
                        <p class="text-[8pt] text-slate-700 font-medium leading-tight">Hospital APAR & Fire Inspection System</p>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-black mt-0.5">LEMBAR PEMERIKSAAN APAR</h2>
                    </div>
                </div>
                <div class="text-right text-[8pt] text-slate-600 leading-tight shrink-0">
                    <p class="font-semibold text-black">Dokumen Inspeksi K3RS</p>
                    <p class="mt-0.5 font-mono">No: {{ $inspeksi['id'] }}</p>
                </div>
            </div>
        </div>

        <!-- 2 KOLOM IDENTITAS: IDENTITAS APAR & INFORMASI PEMERIKSAAN -->
        <div class="grid grid-cols-2 gap-4 text-[8.5pt] mb-3 pb-2.5 border-b border-slate-300 print-avoid-break">
            <!-- Kolom Kiri: IDENTITAS APAR -->
            <div>
                <h3 class="font-bold uppercase text-[8pt] tracking-wider text-black mb-1.5 border-b border-slate-200 pb-0.5">
                    IDENTITAS APAR
                </h3>
                <table class="w-full text-left">
                    <tr class="leading-tight">
                        <td class="w-24 text-slate-600 py-0.5">Kode APAR</td>
                        <td class="w-3 text-center py-0.5">:</td>
                        <td class="font-mono font-bold text-black py-0.5">{{ $inspeksi['apar_id'] }}</td>
                    </tr>
                    <tr class="leading-tight">
                        <td class="text-slate-600 py-0.5">Jenis APAR</td>
                        <td class="text-center py-0.5">:</td>
                        <td class="font-semibold text-black py-0.5">{{ $inspeksi['jenis'] }}</td>
                    </tr>
                    <tr class="leading-tight">
                        <td class="text-slate-600 py-0.5">Kapasitas</td>
                        <td class="text-center py-0.5">:</td>
                        <td class="text-black py-0.5">{{ $inspeksi['kapasitas'] }}</td>
                    </tr>
                    <tr class="leading-tight">
                        <td class="text-slate-600 py-0.5">Lokasi</td>
                        <td class="text-center py-0.5">:</td>
                        <td class="text-black py-0.5">{{ $inspeksi['lokasi'] }}</td>
                    </tr>
                    <tr class="leading-tight">
                        <td class="text-slate-600 py-0.5 align-top">Detail Lokasi</td>
                        <td class="text-center py-0.5 align-top">:</td>
                        <td class="text-slate-800 py-0.5">{{ $inspeksi['detail_lokasi'] }}</td>
                    </tr>
                </table>
            </div>

            <!-- Kolom Kanan: INFORMASI PEMERIKSAAN -->
            <div>
                <h3 class="font-bold uppercase text-[8pt] tracking-wider text-black mb-1.5 border-b border-slate-200 pb-0.5">
                    INFORMASI PEMERIKSAAN
                </h3>
                <table class="w-full text-left">
                    <tr class="leading-tight">
                        <td class="w-32 text-slate-600 py-0.5">No. Dokumen</td>
                        <td class="w-3 text-center py-0.5">:</td>
                        <td class="font-mono font-bold text-black py-0.5">{{ $inspeksi['id'] }}</td>
                    </tr>
                    <tr class="leading-tight">
                        <td class="text-slate-600 py-0.5">Tanggal Pemeriksaan</td>
                        <td class="text-center py-0.5">:</td>
                        <td class="font-mono font-semibold text-black py-0.5">{{ $inspeksi['tanggal_cetak'] }}</td>
                    </tr>
                    <tr class="leading-tight">
                        <td class="text-slate-600 py-0.5">Pemeriksa</td>
                        <td class="text-center py-0.5">:</td>
                        <td class="font-semibold text-black py-0.5">{{ $inspeksi['pemeriksa'] }}</td>
                    </tr>
                    <tr class="leading-tight">
                        <td class="text-slate-600 py-0.5">Kondisi Akhir</td>
                        <td class="text-center py-0.5">:</td>
                        <td class="py-0.5">
                            <span class="font-bold px-1.5 py-0.5 rounded text-[8pt] {{ $inspeksi['kondisi'] === 'Baik' ? 'bg-slate-100 text-black border border-slate-400' : 'bg-slate-200 text-black border border-black' }}">
                                [ {{ $inspeksi['kondisi'] }} ]
                            </span>
                        </td>
                    </tr>
                    <tr class="leading-tight">
                        <td class="text-slate-600 py-0.5">Masa Berlaku Uji</td>
                        <td class="text-center py-0.5">:</td>
                        <td class="font-mono text-slate-800 py-0.5">Januari 2026</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- CHECKLIST PEMERIKSAAN (10 Item Tabel Formal) -->
        <div class="mb-3 print-avoid-break">
            <h3 class="font-bold uppercase text-[8pt] tracking-wider text-black mb-1">
                CHECKLIST PEMERIKSAAN FISIK
            </h3>
            <table class="w-full border-collapse border border-black text-[8pt]" style="table-layout: fixed;">
                <thead>
                    <tr class="bg-slate-100 border-b border-black text-black">
                        <th class="border border-slate-400 py-1 px-1.5 text-center font-bold" style="width: 7%;">No</th>
                        <th class="border border-slate-400 py-1 px-2 text-left font-bold" style="width: 76%;">Item Pemeriksaan</th>
                        <th class="border border-slate-400 py-1 px-2 text-center font-bold" style="width: 17%;">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inspeksi['checklist'] as $chk)
                    <tr class="border-b border-slate-300">
                        <td class="border border-slate-300 py-0.5 px-1.5 text-center font-mono text-slate-700">
                            {{ $chk['no'] }}
                        </td>
                        <td class="border border-slate-300 py-0.5 px-2 text-slate-900 font-medium" style="word-break: break-word;">
                            {{ $chk['item'] }}
                        </td>
                        <td class="border border-slate-300 py-0.5 px-2 text-center font-semibold text-black" style="white-space: normal; word-break: break-word;">
                            {{ $chk['hasil'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- HASIL PEMERIKSAAN & FOTO DOKUMENTASI (Side by side to fit single page) -->
        <div class="grid grid-cols-3 gap-3 mb-4 print-avoid-break">
            <!-- 2 Kolom: Kondisi Akhir & Catatan -->
            <div class="col-span-2 border border-slate-300 rounded p-2 text-[8pt] bg-slate-50/50">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-bold text-black uppercase tracking-wider text-[7.5pt]">Kondisi Akhir:</span>
                    <span class="font-extrabold text-[8.5pt] text-black bg-white px-2 py-0.5 rounded border border-black inline-block">
                        [ {{ $inspeksi['kondisi'] }} ]
                    </span>
                </div>
                <div class="mt-1">
                    <span class="font-bold text-black uppercase tracking-wider text-[7.5pt] block mb-0.5">Catatan Pemeriksaan:</span>
                    <p class="text-slate-800 leading-snug" style="white-space: normal; overflow: visible; text-overflow: clip; word-break: break-word;">
                        {{ $inspeksi['catatan'] }}
                    </p>
                </div>
            </div>

            <!-- 1 Kolom: Foto Dokumentasi Lapangan (Proporsional & Ringkas) -->
            <div class="col-span-1 border border-slate-300 rounded p-1.5 text-center bg-white flex flex-col justify-between">
                <div class="h-16 bg-slate-100 border border-dashed border-slate-300 rounded flex flex-col items-center justify-center text-slate-500">
                    <svg class="w-5 h-5 text-slate-400 mb-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-[7.5pt] font-semibold text-slate-700">Foto Tabung APAR</span>
                </div>
                <div class="mt-1 text-[7pt] text-slate-500 font-mono leading-tight">
                    {{ $inspeksi['foto']['keterangan'] }}
                </div>
            </div>
        </div>

        <!-- TANDA TANGAN (Format Administratif Standar K3RS) -->
        <div class="pt-2 border-t border-slate-300 grid grid-cols-2 text-[8.5pt] print-avoid-break">
            <!-- Kiri: Mengetahui -->
            <div class="text-left">
                <p class="text-slate-700 leading-tight mb-8">
                    Mengetahui,<br>
                    <span class="font-bold text-black">Kepala Instalasi K3RS</span>
                </p>
                <p class="font-mono text-black text-[8pt]">( ___________________________ )</p>
                <div class="mt-1 text-[8pt] text-slate-800 space-y-0.5">
                    <p><span class="text-slate-600 inline-block w-14">Nama</span>: dr. H. Hendrawan, M.Kes</p>
                    <p><span class="text-slate-600 inline-block w-14">Tanggal</span>: {{ $inspeksi['tanggal_cetak'] }}</p>
                </div>
            </div>

            <!-- Kanan: Petugas Pemeriksa -->
            <div class="text-right">
                <p class="text-slate-700 leading-tight mb-8">
                    Petugas Pemeriksa,<br>
                    <span class="font-bold text-black">Pelaksana Inspeksi Lapangan</span>
                </p>
                <p class="font-mono text-black text-[8pt]">( ___________________________ )</p>
                <div class="mt-1 text-[8pt] text-slate-800 space-y-0.5 inline-block text-left">
                    <p><span class="text-slate-600 inline-block w-14">Nama</span>: {{ $inspeksi['pemeriksa'] }}</p>
                    <p><span class="text-slate-600 inline-block w-14">Tanggal</span>: {{ $inspeksi['tanggal_cetak'] }}</p>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>