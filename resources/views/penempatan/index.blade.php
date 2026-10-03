<x-layouts.app title="HAFIS : Tracking APAR">
    @php
    $initialFloor = request('lantai', '1');
    $highlightId = request('highlight', '');
    @endphp

    <!-- 1. Header Halaman -->
    <x-page-header
        title="Tracking APAR"
        description="Pantau posisi dan kondisi APAR berdasarkan denah lokasi">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ url('/apar') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                <span>Daftar APAR</span>
            </x-button>

            <x-button variant="primary" href="{{ url('/penempatan/atur') }}">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Kelola Penempatan</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- 2. Toolbar: Dropdown Lantai, Search APAR, Filter Kondisi & Tombol Aksi -->
    <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 mb-6 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 text-xs">
            
            <!-- Bagian Kiri: Dropdown Lantai & Pencarian -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 flex-1">
                <!-- Dropdown Pilih Lantai -->
                <div class="flex items-center gap-2 shrink-0">
                    <label for="select-floor" class="text-slate-600 font-semibold text-xs whitespace-nowrap">
                        Pilih Lantai:
                    </label>
                    <select 
                        id="select-floor" 
                        onchange="changeFloor(this.value)"
                        class="rounded-lg border border-slate-200 bg-slate-50 text-slate-900 text-xs px-3 py-2 font-semibold focus:bg-white focus:border-slate-400 focus:outline-none transition min-h-[38px] cursor-pointer"
                    >
                        <option value="1" {{ $initialFloor === '1' ? 'selected' : '' }}>Lantai 1 (IGD, Farmasi, Radiologi)</option>
                        <option value="2" {{ $initialFloor === '2' ? 'selected' : '' }}>Lantai 2 (Rawat Inap Melati &amp; Lab)</option>
                        <option value="3" {{ $initialFloor === '3' ? 'selected' : '' }}>Lantai 3 (Bedah Sentral OK &amp; ICU)</option>
                        <option value="4" {{ $initialFloor === '4' ? 'selected' : '' }}>Lantai 4 (Denah Belum Dikonfigurasi)</option>
                    </select>
                </div>

                <!-- Input Pencarian APAR -->
                <div class="relative flex-1 min-w-[200px] max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        id="search-apar" 
                        placeholder="Cari kode APAR atau nama ruangan..." 
                        oninput="handleSearch(this.value)"
                        class="w-full pl-9 pr-3 py-2 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:outline-none transition min-h-[38px]"
                    />
                </div>
            </div>

            <!-- Bagian Kanan: Filter Kondisi & Tombol Kelola -->
            <div class="flex items-center gap-2.5 shrink-0 justify-between sm:justify-end">
                <div class="flex items-center gap-2">
                    <label for="filter-kondisi" class="text-slate-600 font-semibold text-xs whitespace-nowrap hidden sm:inline">
                        Kondisi:
                    </label>
                    <select 
                        id="filter-kondisi" 
                        onchange="applyStatusFilter(this.value)"
                        class="rounded-lg border border-slate-200 bg-slate-50 text-slate-800 text-xs px-2.5 py-2 font-medium focus:bg-white focus:border-slate-400 focus:outline-none transition cursor-pointer min-h-[38px]"
                    >
                        <option value="ALL">Semua Kondisi</option>
                        <option value="Baik">● Kondisi Baik</option>
                        <option value="Perlu Perhatian">● Perlu Perhatian</option>
                        <option value="Rusak / Perlu Tindakan">● Rusak / Tindakan</option>
                    </select>
                </div>

                <!-- Tombol Kelola Penempatan Cepat -->
                <a 
                    href="{{ url('/penempatan/atur') }}" 
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-xs whitespace-nowrap min-h-[38px]"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Kelola Penempatan</span>
                </a>
            </div>

        </div>
    </div>

    <!-- 3. Grid Utama: Area Denah (Kiri 8 Kolom) & Daftar APAR di Lantai Ini (Kanan 4 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- SISI KIRI: DISPLAY DENAH DENGAN MARKER (8 Kolom) -->
        <div class="lg:col-span-8 flex flex-col gap-4">

            <!-- Card Utama Denah -->
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs relative">
                
                <!-- Panel Ringkasan Kecil (Requirement: Angka sebagai informasi, bukan persentase) -->
                <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-sm" id="summary-floor-label">Lantai 1</span>
                        <span class="text-slate-300">•</span>
                        <span class="font-semibold text-slate-700">
                            <span id="summary-total-count">0</span> APAR
                        </span>
                    </div>

                    <!-- Indikator Angka Kondisi -->
                    <div class="flex items-center gap-3 text-slate-600 text-[11px]">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span class="font-medium text-slate-700">
                                <strong class="font-bold text-slate-900" id="summary-baik-count">0</strong> Baik
                            </span>
                        </span>
                        <span class="text-slate-300">·</span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="font-medium text-slate-700">
                                <strong class="font-bold text-slate-900" id="summary-perhatian-count">0</strong> Perlu Perhatian
                            </span>
                        </span>
                        <span class="text-slate-300">·</span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                            <span class="font-medium text-slate-700">
                                <strong class="font-bold text-slate-900" id="summary-tindakan-count">0</strong> Perlu Tindakan
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Sub-header nama denah & petunjuk interaksi -->
                <div class="px-4 py-2 bg-white border-b border-slate-100 flex items-center justify-between gap-2 text-xs">
                    <span class="font-semibold text-slate-800 text-xs truncate" id="floor-title-header">
                        Lantai 1 : Gedung Utama (IGD, Farmasi, Radiologi)
                    </span>
                    <span class="text-slate-400 text-[11px] shrink-0 hidden sm:inline">
                        Klik marker titik untuk info lengkap
                    </span>
                </div>

                <!-- Area Denah Responsif dengan Aspect Ratio Terjaga -->
                <div id="tracking-plan-container" class="relative w-full overflow-hidden bg-slate-50" style="aspect-ratio: 1000 / 650; min-height: 400px;">
                    
                    <!-- Gambar Denah Lantai (SVG) -->
                    <img 
                        id="tracking-plan-image" 
                        src="{{ asset('images/denah/denah-lantai-1.svg') }}" 
                        alt="Denah Lantai Rumah Sakit" 
                        class="w-full h-full object-contain pointer-events-none select-none block transition-opacity duration-150"
                    />

                    <!-- Layer Marker APAR yang Ditempatkan -->
                    <div id="markers-layer" class="absolute inset-0 pointer-events-auto">
                        <!-- Markers diisi via JavaScript -->
                    </div>

                    <!-- POPOVER KECIL KETIKA MARKER DIKLIK (Requirement: Popover kecil, bukan modal besar) -->
                    <div 
                        id="marker-popover" 
                        class="hidden absolute z-30 w-72 bg-white rounded-xl border border-slate-200 shadow-md p-3.5 text-xs transition-all duration-150"
                        style="top: 0; left: 0;"
                        role="dialog"
                        aria-labelledby="popover-title"
                    >
                        <!-- Popover Header: Kode APAR & Tombol Tutup -->
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2.5">
                            <div class="flex items-center gap-2">
                                <span id="popover-status-dot" class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                <h3 id="popover-title" class="font-mono font-bold text-slate-900 text-sm">
                                    APAR-IGD-01
                                </h3>
                            </div>
                            <button 
                                type="button" 
                                onclick="closePopover()" 
                                class="p-1 text-slate-400 hover:text-slate-700 rounded-md hover:bg-slate-100 transition" 
                                aria-label="Tutup popover"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Popover Body: Detail APAR -->
                        <dl class="space-y-1.5 text-xs text-slate-700 mb-3">
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-500 font-medium">Jenis Media:</dt>
                                <dd class="font-semibold text-slate-900 text-right" id="popover-jenis">Dry Chemical Powder</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-500 font-medium">Kapasitas:</dt>
                                <dd class="font-mono font-semibold text-slate-900 text-right" id="popover-kapasitas">6 Kg</dd>
                            </div>
                            <div class="flex items-start justify-between gap-2">
                                <dt class="text-slate-500 font-medium shrink-0">Ruangan:</dt>
                                <dd class="font-medium text-slate-900 text-right" id="popover-ruangan">IGD Zona Triase</dd>
                            </div>
                            <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                                <dt class="text-slate-500 font-medium">Kondisi Fisik:</dt>
                                <dd id="popover-kondisi">
                                    <x-badge variant="success" :dot="true">Baik</x-badge>
                                </dd>
                            </div>
                        </dl>

                        <!-- Popover Action: "Lihat Detail" -->
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a 
                                id="popover-btn-detail" 
                                href="#" 
                                class="flex-1 text-center py-1.5 px-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition"
                            >
                                Lihat Detail
                            </a>
                            <a 
                                id="popover-btn-edit" 
                                href="#" 
                                class="text-center py-1.5 px-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition"
                                title="Edit koordinat posisi APAR ini"
                            >
                                Edit Posisi
                            </a>
                        </div>
                    </div>

                    <!-- KONDISI TANPA DENAH (Requirement: Denah belum tersedia) -->
                    <div id="tracking-no-plan-state" class="hidden absolute inset-0 bg-slate-50 flex flex-col items-center justify-center p-8 text-center z-20">
                        <div class="w-14 h-14 rounded-2xl bg-slate-200/80 border border-slate-300 flex items-center justify-center text-slate-500 mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 tracking-tight">
                            Denah belum tersedia
                        </h3>
                        <p class="text-xs text-slate-500 max-w-sm mt-1 mb-4 leading-relaxed">
                            Belum ada denah yang dikonfigurasi untuk lantai ini.
                        </p>
                        <div class="flex items-center gap-2">
                            <a 
                                href="{{ url('/penempatan/atur') }}" 
                                class="px-3 py-1.5 rounded-lg bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-slate-900 transition"
                            >
                                Kelola Denah
                            </a>
                            <button 
                                type="button" 
                                onclick="changeFloor('1')" 
                                class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-slate-900 transition"
                            >
                                Kembali ke Lantai 1
                            </button>
                        </div>
                    </div>

                    <!-- KONDISI TANPA APAR (Requirement: Belum ada APAR yang ditempatkan pada lantai ini) -->
                    <div id="tracking-no-apar-state" class="hidden absolute inset-0 bg-slate-50/90 flex flex-col items-center justify-center p-8 text-center z-20">
                        <div class="w-12 h-12 rounded-xl bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-500 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight" id="no-apar-title">
                            Belum ada APAR yang ditempatkan pada lantai ini.
                        </h3>
                        <p class="text-xs text-slate-500 max-w-sm mt-1 mb-4 leading-relaxed" id="no-apar-desc">
                            Silakan atur titik koordinat APAR melalui formulir penempatan.
                        </p>
                        <a 
                            id="btn-tambah-penempatan"
                            href="{{ url('/penempatan/atur') }}" 
                            class="px-3.5 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-slate-900 transition"
                        >
                            Tambah Penempatan APAR
                        </a>
                    </div>

                </div>

                <!-- LEGEND STATUS (Requirement: Unobtrusive di sekitar area denah) -->
                <div class="px-4 py-3 bg-white border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-4">
                        <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider">Legend:</span>
                        
                        <!-- Baik -->
                        <div class="flex items-center gap-1.5 cursor-pointer hover:opacity-80 transition" onclick="applyStatusFilter('Baik')">
                            <span class="w-3 h-3 rounded-full bg-emerald-600 ring-2 ring-emerald-100 flex items-center justify-center"></span>
                            <span class="text-slate-700 font-medium">● Baik</span>
                        </div>

                        <!-- Perlu Perhatian -->
                        <div class="flex items-center gap-1.5 cursor-pointer hover:opacity-80 transition" onclick="applyStatusFilter('Perlu Perhatian')">
                            <span class="w-3 h-3 rounded-full bg-amber-500 ring-2 ring-amber-100 flex items-center justify-center"></span>
                            <span class="text-slate-700 font-medium">● Perlu Perhatian</span>
                        </div>

                        <!-- Rusak / Perlu Tindakan -->
                        <div class="flex items-center gap-1.5 cursor-pointer hover:opacity-80 transition" onclick="applyStatusFilter('Rusak / Perlu Tindakan')">
                            <span class="w-3 h-3 rounded-full bg-rose-600 ring-2 ring-rose-100 flex items-center justify-center"></span>
                            <span class="text-slate-700 font-medium">● Rusak / Perlu Tindakan</span>
                        </div>
                    </div>

                    <div class="text-[11px] text-slate-400">
                        *Klik marker titik untuk membuka info ringkas
                    </div>
                </div>

            </div>

        </div>

        <!-- SISI KANAN: DAFTAR APAR DI LANTAI AKTIF (4 Kolom) -->
        <div class="lg:col-span-4 flex flex-col gap-4">

            <!-- Card Daftar APAR di Lantai Ini -->
            <x-card title="Daftar APAR di Lantai Ini" subtitle="Unit terpasang yang terdaftar pada lantai aktif.">
                
                <!-- Status List Counter -->
                <div class="mb-3 flex items-center justify-between text-xs text-slate-500 border-b border-slate-100 pb-2">
                    <span>Menampilkan <strong class="text-slate-900" id="list-shown-count">0</strong> unit</span>
                    <button type="button" onclick="resetFilters()" class="text-slate-600 hover:text-slate-900 font-medium text-[11px]">
                        Reset Filter
                    </button>
                </div>

                <!-- List Items APAR -->
                <div id="unit-list-container" class="space-y-2 max-h-[460px] overflow-y-auto pr-1">
                    <!-- Unit items diisi via JS -->
                </div>

                <!-- Empty Unit List State -->
                <div id="unit-list-empty" class="hidden text-center py-6 text-slate-400 text-xs">
                    Tidak ada APAR yang cocok dengan filter pencarian.
                </div>

            </x-card>

            <!-- Card Petunjuk Standar Keselamatan APAR -->
            <div class="p-4 rounded-xl border border-slate-200 bg-white text-xs shadow-xs">
                <h4 class="font-bold text-slate-900 text-xs mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Standar Penempatan APAR</span>
                </h4>
                <ul class="space-y-1.5 text-slate-600 text-[11px] leading-relaxed">
                    <li class="flex items-start gap-1.5">
                        <span class="text-slate-400 font-bold">•</span>
                        <span>Jarak tempuh maksimal antar tabung APAR adalah 15 meter pada koridor.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span class="text-slate-400 font-bold">•</span>
                        <span>Ketinggian pemasangan bracket 120 cm dari permukaan lantai agar mudah dijangkau.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span class="text-slate-400 font-bold">•</span>
                        <span>Area di depan tabung APAR wajib steril dari halangan operasional rumah sakit.</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>

    <!-- Script Manajemen Tracking Denah & Marker -->
    <script>
        // Database master APAR dengan koordinat default konsisten
        const defaultAparData = {
            // Lantai 1
            'APAR-IGD-01': {
                id: 'APAR-IGD-01',
                kode_singkat: 'AP-001',
                jenis: 'Dry Chemical Powder',
                kapasitas: '6 Kg',
                lantai: '1',
                ruangan: 'Koridor IGD Triase',
                detail: 'Dekat pintu darurat triase (Ketinggian bracket 120 cm)',
                kondisi: 'Baik',
                x: 28.5,
                y: 64.0
            },
            'APAR-PNL-01': {
                id: 'APAR-PNL-01',
                kode_singkat: 'AP-002',
                jenis: 'CO2',
                kapasitas: '5 Kg',
                lantai: '1',
                ruangan: 'Ruang Panel MDP',
                detail: 'Dinding samping panel distribusi listrik utama',
                kondisi: 'Perlu Perhatian',
                x: 82.0,
                y: 22.0
            },
            'APAR-FAR-01': {
                id: 'APAR-FAR-01',
                kode_singkat: 'AP-003',
                jenis: 'Dry Chemical Powder',
                kapasitas: '4 Kg',
                lantai: '1',
                ruangan: 'Depo Farmasi Rawat Jalan',
                detail: 'Pojok loket penyerahan obat',
                kondisi: 'Baik',
                x: 65.0,
                y: 30.0
            },
            'APAR-TNG-01': {
                id: 'APAR-TNG-01',
                kode_singkat: 'AP-004',
                jenis: 'Dry Chemical Powder',
                kapasitas: '6 Kg',
                lantai: '1',
                ruangan: 'Ruang Tunggu Utama',
                detail: 'Pilar tengah ruang tunggu poli rawat jalan',
                kondisi: 'Rusak / Perlu Tindakan',
                x: 48.0,
                y: 50.0
            },
            'APAR-GIZ-01': {
                id: 'APAR-GIZ-01',
                kode_singkat: 'AP-008',
                jenis: 'Clean Agent',
                kapasitas: '5 Kg',
                lantai: '1',
                ruangan: 'Instalasi Dapur Gizi',
                detail: 'Pintu akses staf dapur gizi belakang',
                kondisi: 'Perlu Perhatian',
                x: 74.0,
                y: 54.0
            },
            'APAR-RAD-01': {
                id: 'APAR-RAD-01',
                kode_singkat: 'AP-009',
                jenis: 'Clean Agent (Non-Magnetic)',
                kapasitas: '5 Kg',
                lantai: '1',
                ruangan: 'Ruang Radiologi / MRI',
                detail: 'Koridor akses luar ruang kontrol scan MRI',
                kondisi: 'Baik',
                x: 19.5,
                y: 77.0
            },

            // Lantai 2
            'APAR-RWI-04': {
                id: 'APAR-RWI-04',
                kode_singkat: 'AP-005',
                jenis: 'Water Mist',
                kapasitas: '6 L',
                lantai: '2',
                ruangan: 'Ruang Rawat Melati',
                detail: 'Dekat pos perawat Melati kamar 204',
                kondisi: 'Baik',
                x: 37.0,
                y: 45.0
            },
            'APAR-LAB-02': {
                id: 'APAR-LAB-02',
                kode_singkat: 'AP-006',
                jenis: 'Dry Chemical Powder',
                kapasitas: '6 Kg',
                lantai: '2',
                ruangan: 'Nurse Station / Lab Kimia',
                detail: 'Lorong laboratorium samping emergency shower',
                kondisi: 'Baik',
                x: 66.5,
                y: 35.0
            },
            'APAR-ADM-01': {
                id: 'APAR-ADM-01',
                kode_singkat: 'AP-007',
                jenis: 'CO2',
                kapasitas: '3 Kg',
                lantai: '2',
                ruangan: 'Ruang Administrasi & Manajemen',
                detail: 'Koridor staf administrasi rekam medis',
                kondisi: 'Perlu Perhatian',
                x: 52.0,
                y: 62.0
            },

            // Lantai 3
            'APAR-OK-01': {
                id: 'APAR-OK-01',
                kode_singkat: 'AP-010',
                jenis: 'CO2',
                kapasitas: '5 Kg',
                lantai: '3',
                ruangan: 'Bedah Sentral (OK 3)',
                detail: 'Samping pintu masuk scrub station OK 3',
                kondisi: 'Baik',
                x: 24.5,
                y: 68.0
            },
            'APAR-ICU-01': {
                id: 'APAR-ICU-01',
                kode_singkat: 'AP-011',
                jenis: 'Clean Agent',
                kapasitas: '4 Kg',
                lantai: '3',
                ruangan: 'Ruang Rawat Intensif ICU',
                detail: 'Koridor sentral monitoring ICU',
                kondisi: 'Baik',
                x: 62.0,
                y: 40.0
            },
            'APAR-CSSD-01': {
                id: 'APAR-CSSD-01',
                kode_singkat: 'AP-012',
                jenis: 'CO2',
                kapasitas: '5 Kg',
                lantai: '3',
                ruangan: 'Ruang Sterilisasi CSSD',
                detail: 'Pintu keluar area dekontaminasi CSSD',
                kondisi: 'Rusak / Perlu Tindakan',
                x: 75.0,
                y: 60.0
            }
        };

        // Sinkronisasi data dari LocalStorage
        function getStoredPlacements() {
            try {
                const stored = localStorage.getItem('hafis_apar_placements');
                if (stored) {
                    return Object.assign({}, defaultAparData, JSON.parse(stored));
                }
            } catch (e) {
                console.warn('LocalStorage error:', e);
            }
            return defaultAparData;
        }

        const floorPlanImages = {
            '1': "{{ asset('images/denah/denah-lantai-1.svg') }}",
            '2': "{{ asset('images/denah/denah-lantai-2.svg') }}",
            '3': "{{ asset('images/denah/denah-lantai-3.svg') }}",
            '4': null // Contoh lantai tanpa denah
        };

        const floorNames = {
            '1': 'Lantai 1 : Gedung Utama (IGD, Farmasi, Radiologi, Dapur Gizi)',
            '2': 'Lantai 2 : Gedung Utama (Rawat Inap Melati & Laboratorium)',
            '3': 'Lantai 3 : Gedung Utama (Bedah Sentral OK, ICU, CSSD)',
            '4': 'Lantai 4 : Gedung Utama (Area Manajemen & Pertemuan)'
        };

        let currentFloor = '{{ $initialFloor }}';
        let currentStatusFilter = 'ALL';
        let currentSearchQuery = '';
        let aparPlacements = getStoredPlacements();
        let selectedMarkerId = null;

        // DOM Elements
        const selectFloor = document.getElementById('select-floor');
        const filterKondisi = document.getElementById('filter-kondisi');
        const searchInput = document.getElementById('search-apar');
        const trackingPlanImage = document.getElementById('tracking-plan-image');
        const trackingNoPlanState = document.getElementById('tracking-no-plan-state');
        const trackingNoAparState = document.getElementById('tracking-no-apar-state');
        const markersLayer = document.getElementById('markers-layer');
        const floorTitleHeader = document.getElementById('floor-title-header');
        const summaryFloorLabel = document.getElementById('summary-floor-label');
        const summaryTotalCount = document.getElementById('summary-total-count');
        const summaryBaikCount = document.getElementById('summary-baik-count');
        const summaryPerhatianCount = document.getElementById('summary-perhatian-count');
        const summaryTindakanCount = document.getElementById('summary-tindakan-count');
        const unitListContainer = document.getElementById('unit-list-container');
        const unitListEmpty = document.getElementById('unit-list-empty');
        const listShownCount = document.getElementById('list-shown-count');
        const markerPopover = document.getElementById('marker-popover');
        const btnTambahPenempatan = document.getElementById('btn-tambah-penempatan');

        // Ganti Lantai
        function changeFloor(floorNum) {
            currentFloor = String(floorNum);
            if (selectFloor) selectFloor.value = currentFloor;
            if (btnTambahPenempatan) {
                btnTambahPenempatan.href = `{{ url('/penempatan/atur') }}?lantai=${currentFloor}`;
            }

            closePopover();

            // Update label header & summary
            summaryFloorLabel.textContent = `Lantai ${currentFloor}`;
            floorTitleHeader.textContent = floorNames[currentFloor] || `Lantai ${currentFloor}`;

            // Periksa ketersediaan denah
            const planUrl = floorPlanImages[currentFloor];
            if (!planUrl) {
                // Kondisi Tanpa Denah
                trackingNoPlanState.classList.remove('hidden');
                trackingNoAparState.classList.add('hidden');
                trackingPlanImage.classList.add('opacity-0');
                markersLayer.innerHTML = '';
                unitListContainer.innerHTML = '';
                unitListEmpty.classList.remove('hidden');
                unitListEmpty.textContent = 'Belum ada denah yang dikonfigurasi untuk lantai ini.';
                
                summaryTotalCount.textContent = '0';
                summaryBaikCount.textContent = '0';
                summaryPerhatianCount.textContent = '0';
                summaryTindakanCount.textContent = '0';
                listShownCount.textContent = '0';
            } else {
                trackingNoPlanState.classList.add('hidden');
                trackingPlanImage.classList.remove('opacity-0');
                trackingPlanImage.src = planUrl;
                renderFloorData();
            }
        }

        // Render Data Lantai (Ringkasan, Marker di Denah, dan List Samping)
        function renderFloorData() {
            markersLayer.innerHTML = '';
            unitListContainer.innerHTML = '';
            unitListEmpty.classList.add('hidden');
            trackingNoAparState.classList.add('hidden');

            const allUnits = Object.values(aparPlacements);
            const floorUnits = allUnits.filter(u => String(u.lantai) === currentFloor);

            // Hitung Panel Ringkasan Kecil untuk Seluruh Unit di Lantai Ini (tanpa persentase)
            const totalOnFloor = floorUnits.length;
            const baikOnFloor = floorUnits.filter(u => u.kondisi === 'Baik').length;
            const perhatianOnFloor = floorUnits.filter(u => u.kondisi === 'Perlu Perhatian').length;
            const tindakanOnFloor = floorUnits.filter(u => u.kondisi === 'Rusak / Perlu Tindakan').length;

            summaryTotalCount.textContent = totalOnFloor;
            summaryBaikCount.textContent = baikOnFloor;
            summaryPerhatianCount.textContent = perhatianOnFloor;
            summaryTindakanCount.textContent = tindakanOnFloor;

            // Kondisi Tanpa APAR di lantai ini sama sekali
            if (totalOnFloor === 0) {
                trackingNoAparState.classList.remove('hidden');
                document.getElementById('no-apar-title').textContent = 'Belum ada APAR yang ditempatkan pada lantai ini.';
                document.getElementById('no-apar-desc').textContent = 'Silakan atur titik koordinat APAR melalui formulir penempatan.';
                unitListEmpty.classList.remove('hidden');
                unitListEmpty.textContent = 'Belum ada unit APAR terdaftar di lantai ini.';
                listShownCount.textContent = '0';
                return;
            }

            // Filter berdasarkan Kondisi dan Search Query
            const query = currentSearchQuery.toLowerCase().trim();
            const filteredUnits = floorUnits.filter(u => {
                const matchStatus = (currentStatusFilter === 'ALL') || (u.kondisi === currentStatusFilter);
                const matchSearch = (!query) || 
                    u.id.toLowerCase().includes(query) || 
                    (u.kode_singkat && u.kode_singkat.toLowerCase().includes(query)) ||
                    u.ruangan.toLowerCase().includes(query) || 
                    u.jenis.toLowerCase().includes(query);
                return matchStatus && matchSearch;
            });

            listShownCount.textContent = filteredUnits.length;

            // Jika filter/search tidak menemukan hasil
            if (filteredUnits.length === 0) {
                trackingNoAparState.classList.remove('hidden');
                document.getElementById('no-apar-title').textContent = 'Tidak ada APAR yang sesuai kriteria filter.';
                document.getElementById('no-apar-desc').textContent = 'Silakan sesuaikan kata kunci pencarian atau ubah filter kondisi.';
                unitListEmpty.classList.remove('hidden');
                unitListEmpty.textContent = 'Tidak ditemukan APAR dengan filter yang dipilih.';
                return;
            }

            // Render masing-masing unit yang lolos filter
            filteredUnits.forEach(unit => {
                // Penentuan warna solid flat: Hijau (Baik), Kuning/Oranye (Perlu Perhatian), Merah (Rusak)
                let markerBgClass = 'bg-emerald-600';
                let markerRingClass = 'ring-emerald-200';
                let badgeClass = 'text-emerald-700 bg-emerald-50 border-emerald-200';
                let dotClass = 'bg-emerald-600';

                if (unit.kondisi === 'Perlu Perhatian') {
                    markerBgClass = 'bg-amber-500';
                    markerRingClass = 'ring-amber-200';
                    badgeClass = 'text-amber-700 bg-amber-50 border-amber-200';
                    dotClass = 'bg-amber-500';
                } else if (unit.kondisi === 'Rusak / Perlu Tindakan') {
                    markerBgClass = 'bg-rose-600';
                    markerRingClass = 'ring-rose-200';
                    badgeClass = 'text-rose-700 bg-rose-50 border-rose-200';
                    dotClass = 'bg-rose-600';
                }

                // 1. Render Marker di atas Denah
                // Desain: Flat, sederhana, jelas, tanpa glow/neon/gradient
                const markerEl = document.createElement('div');
                markerEl.className = 'absolute z-20 transform -translate-x-1/2 -translate-y-1/2 cursor-pointer select-none group transition-transform duration-100 hover:scale-115 focus:outline-none';
                markerEl.style.left = (unit.x ?? 50) + '%';
                markerEl.style.top = (unit.y ?? 50) + '%';
                markerEl.id = `marker-${unit.id}`;
                markerEl.setAttribute('tabindex', '0');
                markerEl.setAttribute('role', 'button');
                markerEl.setAttribute('aria-label', `APAR ${unit.id}, Lokasi ${unit.ruangan}, Kondisi ${unit.kondisi}`);

                markerEl.innerHTML = `
                    <div class="relative flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full ${markerBgClass} border-2 border-white shadow-xs flex items-center justify-center text-white ring-2 ${markerRingClass}">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        </div>
                        <span class="mt-0.5 bg-slate-900 text-white font-mono font-bold text-[9px] px-1 py-0.5 rounded shadow-2xs whitespace-nowrap opacity-90 group-hover:opacity-100">
                            ${unit.kode_singkat || unit.id}
                        </span>
                    </div>
                `;

                markerEl.addEventListener('click', function(e) {
                    e.stopPropagation();
                    openPopover(unit, markerEl);
                });

                markerEl.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openPopover(unit, markerEl);
                    }
                });

                markersLayer.appendChild(markerEl);

                // 2. Render Item di List Samping
                const listItemEl = document.createElement('div');
                listItemEl.className = 'p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition-colors cursor-pointer text-xs flex items-center justify-between gap-3 group';
                listItemEl.id = `list-item-${unit.id}`;

                listItemEl.innerHTML = `
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-900 group-hover:text-slate-950">${unit.id}</span>
                            ${unit.kode_singkat ? `<span class="text-[10px] text-slate-400 font-mono">(${unit.kode_singkat})</span>` : ''}
                            <span class="text-slate-300 text-[10px]">•</span>
                            <span class="text-slate-600 font-medium truncate">${unit.jenis}</span>
                        </div>
                        <div class="text-slate-500 text-[11px] mt-0.5 truncate">${unit.ruangan}</div>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full border ${badgeClass}">
                            <span class="w-1.5 h-1.5 rounded-full ${dotClass}"></span>
                            ${unit.kondisi === 'Rusak / Perlu Tindakan' ? 'Tindakan' : unit.kondisi}
                        </span>
                    </div>
                `;

                listItemEl.addEventListener('click', function() {
                    openPopover(unit, markerEl);
                });

                unitListContainer.appendChild(listItemEl);
            });

            // Highlight otomatis jika ada ID di query param
            const highlightParam = '{{ $highlightId }}';
            if (highlightParam && aparPlacements[highlightParam]) {
                const targetMarker = document.getElementById(`marker-${highlightParam}`);
                if (targetMarker) {
                    setTimeout(() => openPopover(aparPlacements[highlightParam], targetMarker), 200);
                }
            }
        }

        // Buka Popover Kecil di Atas/Samping Marker
        function openPopover(unit, markerEl) {
            selectedMarkerId = unit.id;

            // Highlight Marker Aktif
            document.querySelectorAll('#markers-layer > div').forEach(el => {
                el.classList.remove('scale-125', 'z-30');
            });
            markerEl.classList.add('scale-125', 'z-30');

            // Highlight Item List Terkait
            document.querySelectorAll('#unit-list-container > div').forEach(el => {
                el.classList.remove('ring-2', 'ring-slate-900', 'bg-slate-50');
            });
            const matchedItem = document.getElementById(`list-item-${unit.id}`);
            if (matchedItem) {
                matchedItem.classList.add('ring-2', 'ring-slate-900', 'bg-slate-50');
                matchedItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            // Isi Data Popover Sederhana
            document.getElementById('popover-title').textContent = unit.id;
            document.getElementById('popover-jenis').textContent = unit.jenis;
            document.getElementById('popover-kapasitas').textContent = unit.kapasitas;
            document.getElementById('popover-ruangan').textContent = unit.ruangan;

            const popoverDot = document.getElementById('popover-status-dot');
            const popoverKondisi = document.getElementById('popover-kondisi');

            if (unit.kondisi === 'Baik') {
                popoverDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-600';
                popoverKondisi.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Baik</span>';
            } else if (unit.kondisi === 'Perlu Perhatian') {
                popoverDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500';
                popoverKondisi.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Perlu Perhatian</span>';
            } else {
                popoverDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-600';
                popoverKondisi.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>Rusak / Perlu Tindakan</span>';
            }

            // Set Link Aksi Popover
            document.getElementById('popover-btn-detail').href = `{{ url('/apar') }}/${unit.id}`;
            document.getElementById('popover-btn-edit').href = `{{ url('/penempatan/atur') }}?apar_id=${unit.id}`;

            // Posisikan Popover dengan Boundary Safety (Tidak terpotong tepi denah)
            const containerRect = document.getElementById('tracking-plan-container').getBoundingClientRect();
            const markerRect = markerEl.getBoundingClientRect();

            let posX = markerRect.left - containerRect.left + (markerRect.width / 2);
            let posY = markerRect.top - containerRect.top + markerRect.height + 8;

            if (posX + 295 > containerRect.width) {
                posX = containerRect.width - 305;
            } else {
                posX = Math.max(10, posX - 140);
            }

            if (posY + 235 > containerRect.height) {
                posY = (markerRect.top - containerRect.top) - 240;
            }

            markerPopover.style.left = `${posX}px`;
            markerPopover.style.top = `${posY}px`;
            markerPopover.classList.remove('hidden');
        }

        // Tutup Popover
        function closePopover() {
            markerPopover.classList.add('hidden');
            selectedMarkerId = null;
            document.querySelectorAll('#markers-layer > div').forEach(el => el.classList.remove('scale-125', 'z-30'));
            document.querySelectorAll('#unit-list-container > div').forEach(el => el.classList.remove('ring-2', 'ring-slate-900', 'bg-slate-50'));
        }

        // Filter Status Kondisi
        function applyStatusFilter(statusVal) {
            currentStatusFilter = statusVal;
            if (filterKondisi) filterKondisi.value = statusVal;
            closePopover();
            renderFloorData();
        }

        // Handle Input Pencarian
        function handleSearch(query) {
            currentSearchQuery = query;
            closePopover();
            renderFloorData();
        }

        // Reset Filter
        function resetFilters() {
            currentStatusFilter = 'ALL';
            currentSearchQuery = '';
            if (filterKondisi) filterKondisi.value = 'ALL';
            if (searchInput) searchInput.value = '';
            closePopover();
            renderFloorData();
        }

        // Tutup popover jika user klik di luar
        document.addEventListener('click', function(e) {
            if (!markerPopover.contains(e.target) && !e.target.closest('#markers-layer') && !e.target.closest('#unit-list-container')) {
                closePopover();
            }
        });

        // Tutup popover jika user menekan Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePopover();
            }
        });

        // Inisialisasi awal saat DOM siap
        document.addEventListener('DOMContentLoaded', function() {
            changeFloor(currentFloor);
        });
    </script>
</x-layouts.app>
