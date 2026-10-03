<x-layouts.app title="HAFIS : Penempatan APAR">
    @php
    $selectedId = request('apar_id', request('id', 'APAR-IGD-01'));
    @endphp

    <!-- 1. Page Header -->
    <x-page-header
        title="Penempatan APAR"
        description="Tentukan posisi APAR pada denah lokasi rumah sakit">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ url('/penempatan') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Denah</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Feedback Banner Notifikasi Sukses -->
    <div id="save-alert" class="hidden mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50/70 text-emerald-900 text-xs flex items-center justify-between gap-3 transition-all duration-200">
        <div class="flex items-center gap-2.5">
            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </span>
            <div>
                <strong class="font-semibold block text-emerald-950">Penempatan Posisi Berhasil Disimpan</strong>
                <span id="save-alert-message" class="text-emerald-800">Koordinat titik APAR telah diperbarui pada denah lokasi.</span>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('save-alert').classList.add('hidden')" class="text-emerald-700 hover:text-emerald-950 p-1" aria-label="Tutup notifikasi">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Main Grid: Denah Interaktif (Kiri 8 Kolom) & Form Konfigurasi (Kanan 4 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- SISI KIRI: CONTAINER INTERAKTIF DENAH (8 Kolom) -->
        <div class="lg:col-span-8 flex flex-col gap-4">
            
            <!-- Toolbar Denah & Petunjuk Singkat -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-md bg-slate-100 text-slate-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-semibold text-slate-800 block leading-tight">Interaksi Penempatan Titik</span>
                        <span class="text-slate-500 text-[11px] leading-tight">Klik pada area denah atau geser marker untuk mengatur posisi tabung APAR.</span>
                    </div>
                </div>

                <!-- Status Koordinat Live -->
                <div class="flex items-center gap-2 shrink-0 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg">
                    <span class="w-2 h-2 rounded-full bg-slate-400" id="coord-indicator-dot"></span>
                    <span class="font-mono text-[11px] text-slate-700 font-semibold" id="coord-display">
                        Belum Ditentukan
                    </span>
                </div>
            </div>

            <!-- Frame Denah Berukuran Proporsional -->
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs relative">
                
                <!-- Floor Plan Area -->
                <div id="plan-wrapper" class="relative w-full overflow-hidden bg-slate-50" style="aspect-ratio: 1000 / 650; min-height: 380px;">
                    
                    <!-- Gambar Denah SVG Sesuai Lantai -->
                    <img 
                        id="plan-image" 
                        src="{{ asset('images/denah/denah-lantai-1.svg') }}" 
                        alt="Denah Rumah Sakit" 
                        class="w-full h-full object-contain pointer-events-none select-none block"
                    />

                    <!-- Interactive Click Surface -->
                    <div 
                        id="plan-surface" 
                        class="absolute inset-0 cursor-crosshair z-10"
                        title="Klik untuk menentukan posisi titik APAR"
                        role="button"
                        tabindex="0"
                        aria-label="Area interaktif denah lantai"
                    ></div>

                    <!-- Marker Titik APAR yang Sedang Dikonfigurasi -->
                    <div 
                        id="active-marker" 
                        class="absolute z-20 transform -translate-x-1/2 -translate-y-1/2 transition-transform duration-75 cursor-grab active:cursor-grabbing select-none"
                        style="left: 28.5%; top: 64.0%;"
                        title="Tarik untuk memindahkan posisi APAR"
                    >
                        <!-- Pin Marker Simpel Flat -->
                        <div class="relative flex items-center justify-center">
                            <!-- Halo Ring Kontras -->
                            <div id="marker-pin" class="w-8 h-8 rounded-full bg-emerald-600 border-2 border-white shadow-md flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            
                            <!-- Label Kode Mengambang -->
                            <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/90 text-white font-mono text-[9px] px-1.5 py-0.5 rounded shadow-xs whitespace-nowrap pointer-events-none">
                                <span id="marker-label-text">APAR-IGD-01</span>
                            </div>
                        </div>
                    </div>

                    <!-- EMPTY STATE (Requirement 5): Jika denah lantai belum tersedia -->
                    <div id="empty-plan-state" class="hidden absolute inset-0 bg-slate-50 flex flex-col items-center justify-center p-8 text-center z-30">
                        <div class="w-14 h-14 rounded-2xl bg-slate-200/80 border border-slate-300 flex items-center justify-center text-slate-500 mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 tracking-tight">
                            Denah belum tersedia
                        </h3>
                        <p class="text-xs text-slate-500 max-w-sm mt-1 mb-4 leading-relaxed">
                            Tambahkan denah untuk dapat menentukan posisi APAR pada lantai ini.
                        </p>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="setFloor('1')" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-slate-900 transition">
                                Kembali ke Lantai 1
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Footer Denah: Legend Status Marker Sederhana -->
                <div class="px-4 py-3 bg-white border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-4">
                        <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider">Status APAR:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <span class="text-slate-700 font-medium">Baik</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                            <span class="text-slate-700 font-medium">Perlu Perhatian</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                            <span class="text-slate-700 font-medium">Rusak / Perlu Tindakan</span>
                        </div>
                    </div>

                    <div class="text-[11px] text-slate-400">
                        *Warna penanda menunjukkan kondisi fisik unit
                    </div>
                </div>

            </div>

        </div>

        <!-- SISI KANAN: FORM PENEMPATAN & INFORMASI UNIT (4 Kolom) -->
        <div class="lg:col-span-4 flex flex-col gap-5">

            <!-- Card 1: Informasi Unit APAR Terpilih -->
            <x-card title="Informasi Unit APAR" subtitle="Spesifikasi unit yang sedang dipetakan.">
                
                <!-- Dropdown Pilih APAR -->
                <div class="mb-4">
                    <label for="select-apar-unit" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Pilih Kode APAR <span class="text-rose-500 font-bold">*</span>
                    </label>
                    <select id="select-apar-unit" class="w-full rounded-lg border border-slate-300 bg-white text-slate-800 text-xs px-3 py-2 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 focus:outline-none transition min-h-[38px] cursor-pointer">
                        <option value="APAR-IGD-01" {{ $selectedId === 'APAR-IGD-01' ? 'selected' : '' }}>APAR-IGD-01 (IGD Zona Triase - Lt. 1)</option>
                        <option value="APAR-OK-01" {{ $selectedId === 'APAR-OK-01' ? 'selected' : '' }}>APAR-OK-01 (Bedah Sentral OK 3 - Lt. 3)</option>
                        <option value="APAR-LAB-02" {{ $selectedId === 'APAR-LAB-02' ? 'selected' : '' }}>APAR-LAB-02 (Laboratorium Kimia - Lt. 2)</option>
                        <option value="APAR-RNI-04" {{ $selectedId === 'APAR-RNI-04' ? 'selected' : '' }}>APAR-RNI-04 (Rawat Inap Melati - Lt. 2)</option>
                        <option value="APAR-GIZ-01" {{ $selectedId === 'APAR-GIZ-01' ? 'selected' : '' }}>APAR-GIZ-01 (Dapur Gizi - Lt. 1)</option>
                        <option value="APAR-RAD-01" {{ $selectedId === 'APAR-RAD-01' ? 'selected' : '' }}>APAR-RAD-01 (Radiologi MRI - Lt. 1)</option>
                        <option value="APAR-FAR-01" {{ $selectedId === 'APAR-FAR-01' ? 'selected' : '' }}>APAR-FAR-01 (Depo Farmasi - Lt. 1)</option>
                    </select>
                </div>

                <!-- Ringkasan Spesifikasi Unit -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-lg p-3 text-xs space-y-2.5">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Jenis Media</span>
                        <span id="spec-jenis" class="font-semibold text-slate-900 text-right">Dry Chemical Powder</span>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Kapasitas Tabung</span>
                        <span id="spec-kapasitas" class="font-mono font-semibold text-slate-900 text-right">6 Kg</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Status Kondisi</span>
                        <span id="spec-kondisi">
                            <x-badge variant="success" :dot="true">Baik</x-badge>
                        </span>
                    </div>
                </div>

            </x-card>

            <!-- Card 2: Form Lokasi & Titik Penempatan -->
            <x-card title="Konfigurasi Lokasi" subtitle="Tentukan lantai, ruangan, dan detail bracket.">
                <form id="form-penempatan" onsubmit="event.preventDefault(); savePlacement();">
                    
                    <div class="space-y-4 text-xs">
                        
                        <!-- 1. Pilihan Lantai -->
                        <div>
                            <label for="select-floor" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Lantai <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select 
                                id="select-floor" 
                                class="w-full rounded-lg border border-slate-300 bg-white text-slate-800 text-xs px-3 py-2 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 focus:outline-none transition min-h-[38px] cursor-pointer"
                                onchange="setFloor(this.value)"
                            >
                                <option value="1">Lantai 1 (IGD, Farmasi, Radiologi, Gizi)</option>
                                <option value="2">Lantai 2 (Rawat Inap Melati, Lab Kimia)</option>
                                <option value="3">Lantai 3 (Bedah Sentral OK, ICU, CSSD)</option>
                                <option value="4">Lantai 4 (Contoh: Denah Belum Ada)</option>
                            </select>
                        </div>

                        <!-- 2. Ruangan -->
                        <div>
                            <label for="input-ruangan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Ruangan / Zona <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="input-ruangan" 
                                value="IGD Zona Triase" 
                                required
                                placeholder="Contoh: IGD Zona Triase, OK 3, dsb."
                                class="w-full rounded-lg border border-slate-300 bg-white text-slate-800 text-xs px-3 py-2 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 focus:outline-none transition min-h-[38px]"
                            />
                        </div>

                        <!-- 3. Detail Lokasi -->
                        <div>
                            <label for="input-detail-lokasi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Detail Lokasi &amp; Ketinggian
                            </label>
                            <input 
                                type="text" 
                                id="input-detail-lokasi" 
                                value="Dekat pintu darurat triase (Ketinggian bracket 120 cm)" 
                                placeholder="Posisi dinding, kolom, atau patokan bracket..."
                                class="w-full rounded-lg border border-slate-300 bg-white text-slate-800 text-xs px-3 py-2 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 focus:outline-none transition min-h-[38px]"
                            />
                        </div>

                        <!-- 4. Posisi pada Denah (Otomatis dari Interaksi) -->
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                Posisi pada Denah
                            </span>
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-bold text-slate-900" id="form-coord-text">
                                    X: 28.5%, Y: 64.0%
                                </span>
                                <span class="text-[10px] text-emerald-700 font-semibold bg-emerald-100/70 px-2 py-0.5 rounded" id="form-coord-badge">
                                    Terpilih
                                </span>
                            </div>
                            <span class="block text-[10px] text-slate-400 mt-1">
                                Nilai koordinat terhitung otomatis dari klik atau geser marker pada denah.
                            </span>
                        </div>

                        <!-- 5. Tombol Aksi: Batalkan & Simpan Penempatan -->
                        <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2.5">
                            <x-button variant="secondary" href="{{ url('/penempatan') }}">
                                Batalkan
                            </x-button>

                            <x-button variant="primary" type="submit" id="btn-submit-penempatan">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Simpan Penempatan</span>
                            </x-button>
                        </div>

                    </div>
                </form>
            </x-card>

        </div>

    </div>

    <!-- Script Manajemen Data & Interaksi Penempatan -->
    <script>
        // Database lokal APAR untuk pengalaman interaktif
        const defaultAparData = {
            'APAR-IGD-01': {
                id: 'APAR-IGD-01',
                jenis: 'Dry Chemical Powder',
                kapasitas: '6 Kg',
                lantai: '1',
                ruangan: 'IGD Zona Triase',
                detail: 'Dekat pintu darurat triase (Ketinggian bracket 120 cm)',
                kondisi: 'Baik',
                x: 28.5,
                y: 64.0
            },
            'APAR-OK-01': {
                id: 'APAR-OK-01',
                jenis: 'CO2',
                kapasitas: '5 Kg',
                lantai: '3',
                ruangan: 'Bedah Sentral (OK 3)',
                detail: 'Samping pintu masuk scrub station OK 3',
                kondisi: 'Baik',
                x: 24.5,
                y: 68.0
            },
            'APAR-LAB-02': {
                id: 'APAR-LAB-02',
                jenis: 'Dry Chemical Powder',
                kapasitas: '6 Kg',
                lantai: '2',
                ruangan: 'Laboratorium Kimia',
                detail: 'Lorong utama laboratorium samping emergency shower',
                kondisi: 'Perlu Perhatian',
                x: 66.5,
                y: 35.0
            },
            'APAR-RNI-04': {
                id: 'APAR-RNI-04',
                jenis: 'Water Mist',
                kapasitas: '6 L',
                lantai: '2',
                ruangan: 'Rawat Inap Melati',
                detail: 'Dekat pos perawat (Nurse Station) Melati',
                kondisi: 'Baik',
                x: 37.0,
                y: 45.0
            },
            'APAR-GIZ-01': {
                id: 'APAR-GIZ-01',
                jenis: 'Clean Agent',
                kapasitas: '5 Kg',
                lantai: '1',
                ruangan: 'Instalasi Dapur Gizi',
                detail: 'Pintu akses staf dapur gizi belakang',
                kondisi: 'Rusak / Perlu Tindakan',
                x: 74.0,
                y: 54.0
            },
            'APAR-RAD-01': {
                id: 'APAR-RAD-01',
                jenis: 'Clean Agent (Non-Magnetic)',
                kapasitas: '5 Kg',
                lantai: '1',
                ruangan: 'Ruang MRI / Radiologi',
                detail: 'Koridor akses luar ruang kontrol scan MRI',
                kondisi: 'Baik',
                x: 19.5,
                y: 77.0
            },
            'APAR-FAR-01': {
                id: 'APAR-FAR-01',
                jenis: 'Dry Chemical Powder',
                kapasitas: '4 Kg',
                lantai: '1',
                ruangan: 'Depo Farmasi Rawat Jalan',
                detail: 'Pojok loket penyerahan obat',
                kondisi: 'Baik',
                x: 65.0,
                y: 30.0
            }
        };

        // Ambil data penempatan dari localStorage jika ada
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

        // Simpan data penempatan ke localStorage
        function setStoredPlacements(data) {
            try {
                localStorage.setItem('hafis_apar_placements', JSON.stringify(data));
            } catch (e) {
                console.warn('LocalStorage save error:', e);
            }
        }

        let aparPlacements = getStoredPlacements();
        let currentUnitId = '{{ $selectedId }}';
        let currentFloor = '1';
        let currentX = 28.5;
        let currentY = 64.0;
        let isDragging = false;

        // Path denah per lantai
        const floorPlanImages = {
            '1': "{{ asset('images/denah/denah-lantai-1.svg') }}",
            '2': "{{ asset('images/denah/denah-lantai-2.svg') }}",
            '3': "{{ asset('images/denah/denah-lantai-3.svg') }}",
            '4': null // Contoh lantai tanpa denah untuk Empty State
        };

        // DOM elements
        const planSurface = document.getElementById('plan-surface');
        const planWrapper = document.getElementById('plan-wrapper');
        const planImage = document.getElementById('plan-image');
        const activeMarker = document.getElementById('active-marker');
        const markerPin = document.getElementById('marker-pin');
        const markerLabelText = document.getElementById('marker-label-text');
        const emptyPlanState = document.getElementById('empty-plan-state');
        const selectFloor = document.getElementById('select-floor');
        const selectAparUnit = document.getElementById('select-apar-unit');
        const inputRuangan = document.getElementById('input-ruangan');
        const inputDetail = document.getElementById('input-detail-lokasi');
        const formCoordText = document.getElementById('form-coord-text');
        const coordDisplay = document.getElementById('coord-display');
        const coordIndicatorDot = document.getElementById('coord-indicator-dot');
        const specJenis = document.getElementById('spec-jenis');
        const specKapasitas = document.getElementById('spec-kapasitas');
        const specKondisi = document.getElementById('spec-kondisi');

        // Ganti Lantai
        function setFloor(floorNum) {
            currentFloor = String(floorNum);
            selectFloor.value = currentFloor;

            const planUrl = floorPlanImages[currentFloor];
            if (!planUrl) {
                // Tampilkan Empty State
                emptyPlanState.classList.remove('hidden');
                activeMarker.classList.add('hidden');
                planImage.classList.add('opacity-0');
                coordDisplay.textContent = 'Denah Tidak Tersedia';
                coordIndicatorDot.className = 'w-2 h-2 rounded-full bg-slate-300';
            } else {
                emptyPlanState.classList.add('hidden');
                activeMarker.classList.remove('hidden');
                planImage.classList.remove('opacity-0');
                planImage.src = planUrl;
                updateCoordDisplays();
            }
        }

        // Update Posisi Marker
        function setMarkerPosition(xPercent, yPercent) {
            currentX = Math.max(3, Math.min(97, parseFloat(xPercent.toFixed(1))));
            currentY = Math.max(5, Math.min(95, parseFloat(yPercent.toFixed(1))));

            activeMarker.style.left = currentX + '%';
            activeMarker.style.top = currentY + '%';
            updateCoordDisplays();
        }

        // Update teks koordinat
        function updateCoordDisplays() {
            const text = `X: ${currentX.toFixed(1)}%, Y: ${currentY.toFixed(1)}%`;
            formCoordText.textContent = text;
            coordDisplay.textContent = `${text} (Lantai ${currentFloor})`;
            coordIndicatorDot.className = 'w-2 h-2 rounded-full bg-emerald-500';
        }

        // Ganti Unit APAR
        function loadUnitData(unitId) {
            currentUnitId = unitId;
            const unit = aparPlacements[unitId] || {
                id: unitId,
                jenis: 'Dry Chemical Powder',
                kapasitas: '6 Kg',
                lantai: '1',
                ruangan: 'Area RS',
                detail: 'Bracket dinding',
                kondisi: 'Baik',
                x: 50.0,
                y: 50.0
            };

            // Set Form Values
            inputRuangan.value = unit.ruangan;
            inputDetail.value = unit.detail || '';
            markerLabelText.textContent = unit.id;
            specJenis.textContent = unit.jenis;
            specKapasitas.textContent = unit.kapasitas;

            // Set Badge & Marker Color Sesuai Kondisi
            let badgeHtml = '';
            let pinColorClass = 'bg-emerald-600';

            if (unit.kondisi === 'Baik') {
                badgeHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Baik</span>';
                pinColorClass = 'bg-emerald-600';
            } else if (unit.kondisi === 'Perlu Perhatian') {
                badgeHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60"><span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>Perlu Perhatian</span>';
                pinColorClass = 'bg-amber-600';
            } else {
                badgeHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200/60"><span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>Rusak / Perlu Tindakan</span>';
                pinColorClass = 'bg-rose-600';
            }

            specKondisi.innerHTML = badgeHtml;
            markerPin.className = `w-8 h-8 rounded-full ${pinColorClass} border-2 border-white shadow-md flex items-center justify-center text-white`;

            // Pindah Lantai & Posisi
            setFloor(unit.lantai || '1');
            setMarkerPosition(unit.x ?? 50.0, unit.y ?? 50.0);
        }

        // Hitung koordinat klik permukaan denah
        function handleSurfaceClick(e) {
            if (isDragging) return;
            const rect = planWrapper.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            setMarkerPosition(x, y);
        }

        planSurface.addEventListener('click', handleSurfaceClick);

        // Fitur Drag Marker
        activeMarker.addEventListener('pointerdown', function(e) {
            e.preventDefault();
            isDragging = true;
            activeMarker.setPointerCapture(e.pointerId);

            function onPointerMove(moveEvent) {
                if (!isDragging) return;
                const rect = planWrapper.getBoundingClientRect();
                const x = ((moveEvent.clientX - rect.left) / rect.width) * 100;
                const y = ((moveEvent.clientY - rect.top) / rect.height) * 100;
                setMarkerPosition(x, y);
            }

            function onPointerUp(upEvent) {
                isDragging = false;
                activeMarker.removeEventListener('pointermove', onPointerMove);
                activeMarker.removeEventListener('pointerup', onPointerUp);
            }

            activeMarker.addEventListener('pointermove', onPointerMove);
            activeMarker.addEventListener('pointerup', onPointerUp);
        });

        // Event listener ganti unit APAR
        selectAparUnit.addEventListener('change', function() {
            loadUnitData(this.value);
        });

        // Simpan Penempatan
        function savePlacement() {
            if (!aparPlacements[currentUnitId]) {
                aparPlacements[currentUnitId] = { id: currentUnitId };
            }

            aparPlacements[currentUnitId].lantai = currentFloor;
            aparPlacements[currentUnitId].ruangan = inputRuangan.value.trim();
            aparPlacements[currentUnitId].detail = inputDetail.value.trim();
            aparPlacements[currentUnitId].x = currentX;
            aparPlacements[currentUnitId].y = currentY;

            setStoredPlacements(aparPlacements);

            // Tampilkan Alert Sukses
            const alertEl = document.getElementById('save-alert');
            const alertMsg = document.getElementById('save-alert-message');
            alertMsg.textContent = `Titik penempatan untuk ${currentUnitId} di Lantai ${currentFloor} (${currentX}%, ${currentY}%) berhasil disimpan.`;
            alertEl.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Beri waktu 1.5 detik lalu navigasi kembali ke halaman peta tracking
            setTimeout(function() {
                window.location.href = `{{ url('/penempatan') }}?lantai=${currentFloor}&highlight=${currentUnitId}`;
            }, 1200);
        }

        // Inisialisasi awal saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadUnitData(currentUnitId);
        });
    </script>
</x-layouts.app>
