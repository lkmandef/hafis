<x-layouts.app title="HAFIS — Scan Cek">
    @php
    // State preview: scanner (default), found, not_found, permission
    $state = request()->get('state', 'scanner');
    $kodeInput = request()->get('kode', 'APAR-IGD-01');
    @endphp

    <!-- 1. Header with Breadcrumb & Navigation -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-slate-800 transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Scan Cek</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Scan Cek
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Scan kode APAR untuk melihat informasi dan memulai pemeriksaan
                </p>
            </div>

            <!-- Header Action: Kembali -->
            <div class="flex items-center gap-2">
                <x-button variant="secondary" href="{{ url('/') }}">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Dashboard</span>
                </x-button>
            </div>
        </div>
    </div>

    <!-- Mode/State Selector Pills (Untuk kemudahan pengujian UI & demo inspeksi) -->
    <div class="max-w-xl mx-auto mb-6 p-1.5 bg-slate-100 rounded-lg border border-slate-200 flex flex-wrap items-center justify-center gap-1 text-xs">
        <a href="{{ url('/scan?state=scanner') }}" class="px-3 py-1.5 rounded-md font-semibold transition-colors {{ $state === 'scanner' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            1. Scanner Aktif
        </a>
        <a href="{{ url('/scan?state=found') }}" class="px-3 py-1.5 rounded-md font-semibold transition-colors {{ $state === 'found' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            2. Hasil Ditemukan
        </a>
        <a href="{{ url('/scan?state=not_found') }}" class="px-3 py-1.5 rounded-md font-semibold transition-colors {{ $state === 'not_found' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            3. Tidak Ditemukan
        </a>
        <a href="{{ url('/scan?state=permission') }}" class="px-3 py-1.5 rounded-md font-semibold transition-colors {{ $state === 'permission' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            4. Izin Kamera
        </a>
    </div>

    <div class="max-w-xl mx-auto">
        <!-- ==================================================================
             STATE 1: AREA SCANNER AKTIF (Default State)
             ================================================================== -->
        @if ($state === 'scanner')
        <div class="space-y-6">
            <!-- Frame Scanner Utama -->
            <x-card class="!p-5 sm:!p-6 text-center">
                <div class="relative w-full aspect-[4/3] max-w-md mx-auto bg-slate-900 rounded-xl overflow-hidden flex flex-col items-center justify-center p-6 border border-slate-800 shadow-xs">

                    <!-- Realistic Camera Viewfinder Corners (Hairline slate-400, no glow, no neon) -->
                    <div class="relative w-52 h-52 flex items-center justify-center">
                        <!-- Top-Left Corner -->
                        <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-slate-300"></div>
                        <!-- Top-Right Corner -->
                        <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-slate-300"></div>
                        <!-- Bottom-Left Corner -->
                        <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-slate-300"></div>
                        <!-- Bottom-Right Corner -->
                        <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-slate-300"></div>

                        <!-- Subtle Scanning Guide Center Box -->
                        <div class="w-40 h-40 border border-slate-700/60 rounded flex flex-col items-center justify-center text-slate-500">
                            <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Status Keterangan di Dalam Scanner -->
                    <div class="mt-4 text-center">
                        <p class="text-xs font-semibold text-slate-200">
                            Arahkan kamera ke QR Code APAR
                        </p>
                        <p class="text-[11px] text-slate-400 mt-0.5 flex items-center justify-center gap-1.5 font-mono">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Menunggu pemindaian...</span>
                        </p>
                    </div>
                </div>

                <!-- Shortcut Cepat Simulasikan Scan Sukses -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Contoh kode terdeteksi kamera:</span>
                    <a href="{{ url('/scan?state=found&kode=APAR-IGD-01') }}" class="font-semibold text-slate-900 hover:underline inline-flex items-center gap-1">
                        <span>Simulasi Scan APAR-IGD-01</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </x-card>

            <!-- AKSI ALTERNATIF: INPUT KODE MANUAL -->
            <x-card title="Atau Masukkan Kode APAR Secara Manual" subtitle="Gunakan opsi ini jika label QR Code buram atau kamera tidak berfungsi.">
                <form action="{{ url('/scan') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="state" value="found">

                    <div>
                        <label for="kode" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kode APAR
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-mono text-xs">
                                    #
                                </span>
                                <input
                                    type="text"
                                    id="kode"
                                    name="kode"
                                    placeholder="Contoh: APAR-001"
                                    value="APAR-IGD-01"
                                    required
                                    class="w-full pl-8 pr-3 py-2 text-xs font-mono font-medium text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none transition min-h-[42px] uppercase" />
                            </div>

                            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold transition-colors min-h-[42px] shrink-0 focus:outline-none focus:ring-2 focus:ring-slate-900">
                                Periksa APAR
                            </button>
                        </div>
                    </div>

                    <!-- Contoh Kode Cepat Untuk Pengujian -->
                    <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500 pt-2 border-t border-slate-100">
                        <span>Kode cepat:</span>
                        <a href="{{ url('/scan?state=found&kode=APAR-IGD-01') }}" class="font-mono text-slate-700 bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded border border-slate-200">APAR-IGD-01</a>
                        <a href="{{ url('/scan?state=found&kode=APAR-GIZ-01') }}" class="font-mono text-slate-700 bg-slate-100 hover:bg-slate-200 px-2 py-0.5 rounded border border-slate-200">APAR-GIZ-01</a>
                        <a href="{{ url('/scan?state=not_found') }}" class="font-mono text-rose-600 bg-rose-50 hover:bg-rose-100 px-2 py-0.5 rounded border border-rose-200">APAR-999 (Error)</a>
                    </div>
                </form>
            </x-card>
        </div>
        @endif

        <!-- ==================================================================
             STATE 2: HASIL SETELAH SCAN DITEMUKAN
             ================================================================== -->
        @if ($state === 'found')
        @php
        $isRusak = ($kodeInput === 'APAR-GIZ-01');
        $unitFound = [
        'kode' => $kodeInput ?: 'APAR-IGD-01',
        'jenis' => $isRusak ? 'Wet Chemical' : 'Dry Chemical Powder',
        'kapasitas' => $isRusak ? '6 L' : '6 Kg',
        'lokasi' => $isRusak ? 'Instalasi Dapur Gizi - Lantai 1' : 'Gedung Utama (IGD) - Lantai 1',
        'detail_lokasi' => $isRusak ? 'Dekat kompor utama pengolahan gizi (Bracket 120 cm)' : 'Koridor depan pintu darurat triase (Bracket 120 cm)',
        'kondisi' => $isRusak ? 'Rusak / Perlu Tindakan' : 'Baik',
        'kondisi_variant' => $isRusak ? 'danger' : 'success',
        'tgl_terakhir' => '02/10/2026',
        'pemeriksa_terakhir' => 'Budi Santoso (K3RS)',
        ];
        @endphp

        <div class="space-y-5">
            <!-- Banner Notifikasi Sukses Pemindaian -->
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium">Unit APAR berhasil teridentifikasi dari QR Code.</span>
                </div>
                <a href="{{ url('/scan') }}" class="font-semibold text-emerald-900 underline hover:text-emerald-950">
                    Scan Ulang
                </a>
            </div>

            <!-- Informasi APAR Hasil Pemindaian (Card Sederhana) -->
            <x-card title="Informasi Unit APAR" subtitle="Data teknis unit yang terpasang di lokasi fisik.">
                <dl class="space-y-3 text-xs">
                    <!-- Kode APAR -->
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <dt class="text-slate-500 font-medium">Kode APAR</dt>
                        <dd class="font-mono font-bold text-slate-900 text-sm tabular-nums bg-slate-100 px-2.5 py-0.5 rounded border border-slate-200">
                            {{ $unitFound['kode'] }}
                        </dd>
                    </div>

                    <!-- Jenis Media & Kapasitas -->
                    <div class="grid grid-cols-2 gap-3 pb-2 border-b border-slate-100">
                        <div>
                            <dt class="text-slate-500 font-medium mb-0.5">Jenis APAR</dt>
                            <dd class="font-semibold text-slate-800">{{ $unitFound['jenis'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 font-medium mb-0.5">Kapasitas</dt>
                            <dd class="font-semibold text-slate-800">{{ $unitFound['kapasitas'] }}</dd>
                        </div>
                    </div>

                    <!-- Lokasi & Detail Lokasi -->
                    <div class="pb-2 border-b border-slate-100">
                        <dt class="text-slate-500 font-medium mb-0.5">Lokasi Terpasang</dt>
                        <dd class="font-semibold text-slate-900">{{ $unitFound['lokasi'] }}</dd>
                        <dd class="text-slate-500 text-[11px] mt-0.5">{{ $unitFound['detail_lokasi'] }}</dd>
                    </div>

                    <!-- Kondisi Terakhir (Semantic Status Color Only on Badge) -->
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <dt class="text-slate-500 font-medium">Kondisi Terakhir</dt>
                        <dd>
                            <x-badge :variant="$unitFound['kondisi_variant']" :dot="true">
                                {{ $unitFound['kondisi'] }}
                            </x-badge>
                        </dd>
                    </div>

                    <!-- Pemeriksaan Terakhir -->
                    <div class="flex items-center justify-between pt-1">
                        <dt class="text-slate-500 font-medium">Pemeriksaan Terakhir</dt>
                        <dd class="text-right">
                            <span class="font-mono text-slate-800 font-medium block tabular-nums">{{ $unitFound['tgl_terakhir'] }}</span>
                            <span class="text-[11px] text-slate-400">Oleh: {{ $unitFound['pemeriksa_terakhir'] }}</span>
                        </dd>
                    </div>
                </dl>
            </x-card>

            <!-- DUA AKSI UTAMA HASIL SCAN -->
            <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-2.5">
                <!-- Primary Action: Mulai Pemeriksaan -->
                <x-button variant="primary" href="{{ url('/pemeriksaan') }}" class="w-full justify-center !py-2.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>Mulai Pemeriksaan Unit Ini</span>
                </x-button>

                <!-- Secondary Action: Lihat Detail APAR -->
                <x-button variant="secondary" href="{{ url('/apar/detail') }}" class="w-full justify-center !py-2.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>Lihat Detail Lengkap APAR</span>
                </x-button>
            </div>

            <!-- Tombol Scan Unit Lain -->
            <div class="text-center pt-1">
                <a href="{{ url('/scan') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Scan Unit APAR Lainnya</span>
                </a>
            </div>
        </div>
        @endif

        <!-- ==================================================================
             STATE 3: ERROR STATE (APAR TIDAK DITEMUKAN)
             ================================================================== -->
        @if ($state === 'not_found')
        <div class="space-y-4">
            <x-card class="!p-6 text-center border-rose-200 bg-white">
                <div class="w-10 h-10 rounded-full bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>

                <h2 class="text-sm font-bold text-slate-900">
                    APAR tidak ditemukan
                </h2>
                <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto leading-relaxed">
                    Periksa kembali kode APAR yang dimasukkan atau pastikan stiker QR Code fisik pada tabung tidak rusak/tertutup kotoran.
                </p>

                <!-- Tombol Aksi: Coba Lagi -->
                <div class="mt-5 flex items-center justify-center gap-2">
                    <x-button variant="primary" href="{{ url('/scan') }}">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Coba Lagi</span>
                    </x-button>

                    <x-button variant="secondary" href="{{ url('/apar') }}">
                        <span>Cari di Data APAR</span>
                    </x-button>
                </div>
            </x-card>
        </div>
        @endif

        <!-- ==================================================================
             STATE 4: PERMISSION / CAMERA STATE
             ================================================================== -->
        @if ($state === 'permission')
        <div class="space-y-4">
            <x-card class="!p-6 text-center bg-white">
                <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 text-slate-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>

                <h2 class="text-sm font-bold text-slate-900">
                    Kamera membutuhkan izin
                </h2>
                <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto leading-relaxed">
                    Berikan akses kamera untuk melakukan pemindaian QR Code pada perangkat ini.
                </p>

                <!-- Tombol Aksi Izin Kamera -->
                <div class="mt-5 flex flex-col sm:flex-row items-center justify-center gap-2">
                    <button
                        type="button"
                        onclick="alert('Izin kamera diberikan (Simulasi)'); window.location.href='{{ url('/scan?state=scanner') }}';"
                        class="w-full sm:w-auto px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-slate-900">
                        Izinkan Kamera
                    </button>

                    <x-button variant="secondary" href="{{ url('/scan?state=scanner') }}" class="w-full sm:w-auto">
                        Gunakan Input Kode Manual
                    </x-button>
                </div>
            </x-card>
        </div>
        @endif
    </div>
</x-layouts.app>