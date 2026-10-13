<x-layouts.app title="HAFIS : Titik Penempatan APAR">
    <!-- 1. Header Halaman -->
    <x-page-header
        title="Titik Penempatan APAR"
        description="Pemetaan posisi fisik tabung APAR pada denah lantai fasilitas rumah sakit.">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ route('apar.index') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Master Data APAR</span>
            </x-button>

            <x-button variant="secondary" href="{{ route('lokasi.index') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Struktur Lokasi</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Notifikasi Sukses / Feedback -->
    @if(session('success'))
        <div class="mb-5 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs">
            <div class="font-bold mb-1">Gagal memproses:</div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. Navigasi Hierarki Berjenjang: Pilih Gedung -> Pilih Lantai -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 mb-6 shadow-2xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Pilih Gedung -->
            <div class="flex items-center gap-3">
                <label for="select-gedung" class="text-xs font-bold text-slate-700 uppercase tracking-wider shrink-0">
                    Gedung:
                </label>
                <div class="relative min-w-[220px]">
                    <select 
                        id="select-gedung" 
                        onchange="location.href = '{{ route('penempatan.index') }}?gedung_id=' + this.value"
                        class="appearance-none w-full bg-slate-50 border border-slate-300 text-slate-900 text-xs font-bold rounded-lg pl-3 pr-8 py-2 focus:bg-white focus:border-slate-500 focus:ring-1 focus:ring-slate-500 min-h-[44px] cursor-pointer"
                    >
                        @foreach($gedungs as $g)
                            <option value="{{ $g->id }}" {{ $selectedGedung && $selectedGedung->id == $g->id ? 'selected' : '' }}>
                                {{ $g->nama }} ({{ $g->kode }})
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Daftar Lantai Gedung Terpilih (Pills / Tombol Berjejer) -->
            @if($selectedGedung && $selectedGedung->lantais->isNotEmpty())
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                    <span class="text-xs font-semibold text-slate-500 shrink-0 hidden sm:inline">Pilih Lantai:</span>
                    <div class="inline-flex rounded-lg p-1 bg-slate-100 border border-slate-200 gap-1 shrink-0">
                        @foreach($selectedGedung->lantais as $l)
                            @php
                                $isActive = $selectedLantai && $selectedLantai->id == $l->id;
                                $hasDenah = $l->denah !== null;
                            @endphp
                            <a 
                                href="{{ route('penempatan.index', ['gedung_id' => $selectedGedung->id, 'lantai_id' => $l->id]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition min-h-[36px] {{ $isActive ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white' }}"
                            >
                                <span>{{ $l->nama }}</span>
                                @if($hasDenah)
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-400' : 'bg-emerald-600' }}" title="Denah tersedia"></span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300" title="Denah belum ada"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    @if(!$selectedGedung || !$selectedLantai)
        <!-- Keadaan jika belum ada gedung atau lantai terdaftar -->
        <div class="bg-white border border-slate-200 rounded-xl p-12 text-center text-xs text-slate-500">
            Belum ada gedung atau lantai yang dipilih. Silakan konfigurasikan lokasi terlebih dahulu di menu Struktur Lokasi.
        </div>
    @else
        <!-- 3. Grid Utama: Area Denah (Kiri 8 Kolom) & Panel APAR Lantai (Kanan 4 Kolom) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- SISI KIRI: DISPLAY DENAH DENGAN TITIK MARKER (8 Kolom) -->
            <div class="lg:col-span-8 flex flex-col gap-4">

                <!-- Header Denah & Aksi Kelola Denah -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900">
                                Denah {{ $selectedLantai->nama }} : {{ $selectedGedung->nama }}
                            </h2>
                            @if($selectedLantai->has_denah)
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-bold">
                                    Denah Tersedia
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">
                                    Denah Belum Ada
                                </span>
                            @endif
                        </div>
                        <p class="text-slate-500 text-xs mt-0.5">
                            Menampilkan <strong class="text-slate-800 font-mono">{{ $placedApars->count() }}</strong> titik APAR terpetakan dari total {{ $placedApars->count() + $unplacedApars->count() }} unit di lantai ini.
                        </p>
                    </div>

                    <!-- Tombol Upload / Ganti / Hapus Denah -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button 
                            type="button" 
                            onclick="openUploadModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition min-h-[44px]"
                        >
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            <span>{{ $selectedLantai->has_denah ? 'Ganti Denah' : 'Upload Denah' }}</span>
                        </button>

                        @if($selectedLantai->has_denah)
                            <form action="{{ route('lokasi.lantai.denah.destroy', $selectedLantai->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar denah lantai ini?');">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center justify-center p-2 rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200 transition min-h-[44px] min-w-[44px]" 
                                    title="Hapus Denah"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Banner Instruksi Saat Mode Penempatan Sedang Aktif -->
                <div id="placement-guide-banner" class="hidden p-4 rounded-xl border border-sky-300 bg-sky-50 text-sky-950 text-xs shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-sky-600 text-white flex items-center justify-center font-bold shrink-0 animate-pulse">
                                +
                            </span>
                            <div>
                                <strong class="font-bold text-sky-900 block" id="placement-target-text">
                                    Mode Penempatan Aktif
                                </strong>
                                <span class="text-sky-800 text-[11px]">
                                    Klik pada gambar denah di mana unit tabung dipasang, lalu klik Simpan Titik.
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                id="btn-save-placement" 
                                disabled
                                onclick="submitPlacement()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-600 hover:bg-sky-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs rounded-lg transition min-h-[44px]"
                            >
                                <span>Simpan Titik</span>
                            </button>
                            <button 
                                type="button" 
                                onclick="cancelPlacementMode()"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-xs rounded-lg transition min-h-[44px]"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Container Denah Gambar Utama -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs relative">
                    
                    @if($selectedLantai->has_denah)
                        <!-- Jika Denah Tersedia: Render Gambar & Layer Marker -->
                        <div 
                            id="denah-container" 
                            class="relative w-full overflow-hidden bg-slate-50 select-none"
                            style="aspect-ratio: 1000 / 650; min-height: 420px;"
                        >
                            <!-- Gambar Denah -->
                            <img 
                                id="denah-image" 
                                src="{{ $selectedLantai->denah->url }}" 
                                alt="Denah {{ $selectedLantai->nama }}" 
                                class="w-full h-full object-contain pointer-events-none block select-none"
                            />

                            <!-- Click Surface untuk Mode Penempatan -->
                            <div 
                                id="denah-surface" 
                                onclick="handleSurfaceClick(event)"
                                class="absolute inset-0 cursor-default z-10 hidden"
                                title="Klik untuk menempatkan titik APAR"
                            ></div>

                            <!-- Layer Marker yang Sudah Ditempatkan -->
                            <div id="markers-layer" class="absolute inset-0 z-20 pointer-events-none">
                                @foreach($placedApars as $apar)
                                    @php
                                        $dotBg = match($apar->status) {
                                            'baik' => 'bg-emerald-500 ring-emerald-200',
                                            'perhatian' => 'bg-amber-500 ring-amber-200',
                                            'rusak' => 'bg-rose-500 ring-rose-200',
                                            default => 'bg-slate-400 ring-slate-200',
                                        };
                                        $isHighlighted = $highlightApar && $highlightApar->id === $apar->id;
                                    @endphp
                                    <div 
                                        id="marker-apar-{{ $apar->id }}"
                                        data-apar-id="{{ $apar->id }}"
                                        class="apar-marker absolute pointer-events-auto transform -translate-x-1/2 -translate-y-1/2 cursor-pointer transition-all duration-150 hover:scale-110 {{ $isHighlighted ? 'scale-125 z-30' : '' }}"
                                        style="left: {{ $apar->koordinat_x }}%; top: {{ $apar->koordinat_y }}%;"
                                        onclick="showMarkerPopover(event, {{ json_encode([
                                            'id' => $apar->id,
                                            'kode' => $apar->kode,
                                            'gedung' => $selectedGedung->nama,
                                            'lantai' => $selectedLantai->nama,
                                            'ruangan' => $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-',
                                            'posisi_detail' => $apar->posisi_detail ?? 'Belum dicatat secara detail',
                                            'jenis_media' => $apar->jenis_media,
                                            'kapasitas' => $apar->kapasitas,
                                            'status' => $apar->status,
                                            'status_label' => $apar->status_label,
                                            'status_badge_variant' => $apar->status_badge_variant,
                                            'koordinat_x' => $apar->koordinat_x,
                                            'koordinat_y' => $apar->koordinat_y,
                                            'detail_url' => route('apar.show', $apar->id),
                                        ]) }})"
                                    >
                                        <!-- Pin Visual Simpel & Solid Sesuai Desain Standar RS -->
                                        <div class="flex items-center gap-1 bg-white/95 border border-slate-300 rounded-full py-0.5 px-1.5 shadow-sm hover:border-slate-500 text-[10px] font-bold font-mono">
                                            <span class="w-2.5 h-2.5 rounded-full {{ $dotBg }} ring-2"></span>
                                            <span class="text-slate-800">{{ $apar->kode }}</span>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Temporary Placement Pin Marker (preview saat klik denah) -->
                                <div 
                                    id="preview-marker" 
                                    class="hidden absolute z-30 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none"
                                    style="left: 0%; top: 0%;"
                                >
                                    <div class="flex items-center gap-1.5 bg-sky-600 text-white rounded-full py-1 px-2.5 shadow-lg border border-white text-xs font-bold font-mono animate-bounce">
                                        <span class="w-2 h-2 rounded-full bg-white"></span>
                                        <span id="preview-marker-label">Titik Baru</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Popover Info Singkat Saat Marker Diklik -->
                            <div 
                                id="marker-popover" 
                                class="hidden absolute z-40 w-72 bg-white rounded-xl border border-slate-300 shadow-xl p-3.5 text-xs transition-opacity duration-150"
                                style="top: 0; left: 0;"
                            >
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2.5">
                                    <div class="flex items-center gap-1.5">
                                        <span id="popover-status-dot" class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <strong id="popover-kode" class="font-mono font-extrabold text-slate-900 text-sm">APAR-001</strong>
                                    </div>
                                    <button type="button" onclick="closePopover()" class="text-slate-400 hover:text-slate-600 font-bold p-1">✕</button>
                                </div>

                                <div class="space-y-1.5 text-slate-700 text-[11px] mb-3">
                                    <div><span class="text-slate-400">Gedung:</span> <strong id="popover-gedung" class="text-slate-800 font-semibold">-</strong></div>
                                    <div><span class="text-slate-400">Lantai:</span> <strong id="popover-lantai" class="text-slate-800 font-semibold">-</strong></div>
                                    <div><span class="text-slate-400">Ruangan:</span> <strong id="popover-ruangan" class="text-slate-800 font-semibold">-</strong></div>
                                    <div><span class="text-slate-400">Media / Kapasitas:</span> <span id="popover-media" class="font-medium">-</span></div>
                                    <div class="pt-1"><span class="text-slate-400">Posisi:</span> <span id="popover-posisi" class="italic text-slate-600 block">-</span></div>
                                    <div class="font-mono text-[10px] text-slate-400 pt-0.5">Koordinat Denah: <span id="popover-coord">X: 0%, Y: 0%</span></div>
                                </div>

                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                                    <button 
                                        type="button" 
                                        id="popover-btn-pindah"
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs rounded-lg transition"
                                    >
                                        Pindahkan Posisi
                                    </button>

                                    <a 
                                        id="popover-btn-detail" 
                                        href="#" 
                                        class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg transition"
                                    >
                                        Lihat Detail APAR &rarr;
                                    </a>
                                </div>
                            </div>

                        </div>
                    @else
                        <!-- Jika Denah Belum Tersedia: Tampilkan Keadaan Kosong Informatif (Point 12) -->
                        <div class="p-12 text-center flex flex-col items-center justify-center min-h-[360px] bg-slate-50/50">
                            <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center mb-3 shadow-2xs">
                                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">Denah Belum Tersedia</h3>
                            <p class="text-xs text-slate-500 max-w-md mt-1 mb-4 leading-relaxed">
                                Denah untuk <strong>{{ $selectedLantai->nama }}</strong> ({{ $selectedGedung->nama }}) belum diunggah. Silakan upload denah resmi lantai ini untuk mulai menempatkan titik APAR fisik.
                            </p>
                            <button 
                                type="button" 
                                onclick="openUploadModal()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg transition min-h-[44px] shadow-xs"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span>Upload Denah Lantai Ini</span>
                            </button>
                        </div>
                    @endif

                </div>

                <!-- Legenda Status Marker Denah -->
                <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs flex flex-wrap items-center justify-between gap-3">
                    <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">Legenda Status:</span>
                    <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-700">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span>Kondisi Baik</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>Perlu Perhatian</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span>Rusak / Perlu Tindakan</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                            <span>Tidak Aktif</span>
                        </span>
                    </div>
                </div>

            </div>

            <!-- SISI KANAN: PANEL DAFTAR APAR DI LANTAI INI (4 Kolom) -->
            <div class="lg:col-span-4 flex flex-col gap-4">
                
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs">
                    <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">APAR di Lantai Ini</h3>
                            <span class="text-[11px] text-slate-400">Berdasarkan relasi ruangan/area</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-mono font-bold text-xs">
                            {{ $placedApars->count() + $unplacedApars->count() }}
                        </span>
                    </div>

                    <!-- Tab Kategori: Sudah Terpetakan vs Belum Terpetakan -->
                    <div class="mt-3 flex rounded-lg bg-slate-100 p-1 border border-slate-200 text-xs">
                        <button 
                            type="button" 
                            id="tab-btn-unplaced" 
                            onclick="switchAparTab('unplaced')"
                            class="flex-1 py-1.5 text-center font-bold rounded-md transition text-slate-700 hover:text-slate-900"
                        >
                            Belum Ada Titik ({{ $unplacedApars->count() }})
                        </button>
                        <button 
                            type="button" 
                            id="tab-btn-placed" 
                            onclick="switchAparTab('placed')"
                            class="flex-1 py-1.5 text-center font-bold rounded-md transition bg-white text-slate-900 shadow-xs"
                        >
                            Terpetakan ({{ $placedApars->count() }})
                        </button>
                    </div>

                    <!-- Konten Tab 1: Belum Ada Titik (Unplaced) -->
                    <div id="tab-content-unplaced" class="hidden mt-3 space-y-2.5 max-h-[460px] overflow-y-auto pr-1">
                        @forelse($unplacedApars as $a)
                            <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/70 hover:bg-slate-50 text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('apar.show', $a->id) }}" class="font-mono font-bold text-slate-900 hover:underline">
                                        {{ $a->kode }}
                                    </a>
                                    <x-badge :variant="$a->status_badge_variant">
                                        {{ $a->status_label }}
                                    </x-badge>
                                </div>
                                <div class="text-[11px] text-slate-600">
                                    <span>Lokasi: <strong>{{ $a->lokasi?->nama ?? $a->ruangan?->nama ?? '-' }}</strong></span>
                                    <div class="text-slate-400 mt-0.5">{{ $a->jenis_media }} &bull; {{ $a->kapasitas }}</div>
                                </div>
                                <div class="pt-1 flex items-center justify-between border-t border-slate-200">
                                    <span class="text-[10px] text-amber-700 italic font-medium">Posisi denah belum ditentukan</span>
                                    @if($selectedLantai->has_denah)
                                        <button 
                                            type="button" 
                                            onclick="startPlacementMode({{ $a->id }}, '{{ $a->kode }}')"
                                            class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white font-bold text-[11px] rounded-md transition min-h-[32px]"
                                        >
                                            Tempatkan di Denah
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-slate-400 italic">
                                Semua unit APAR di lantai ini sudah memiliki titik posisi di denah.
                            </div>
                        @endforelse
                    </div>

                    <!-- Konten Tab 2: Sudah Terpetakan (Placed) -->
                    <div id="tab-content-placed" class="mt-3 space-y-2.5 max-h-[460px] overflow-y-auto pr-1">
                        @forelse($placedApars as $a)
                            <div class="p-3 rounded-lg border border-slate-200 bg-white hover:border-slate-300 text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('apar.show', $a->id) }}" class="font-mono font-bold text-slate-900 hover:underline">
                                        {{ $a->kode }}
                                    </a>
                                    <x-badge :variant="$a->status_badge_variant">
                                        {{ $a->status_label }}
                                    </x-badge>
                                </div>
                                <div class="text-[11px] text-slate-600">
                                    <div>Lokasi: <strong>{{ $a->lokasi?->nama ?? $a->ruangan?->nama ?? '-' }}</strong></div>
                                    <div class="text-slate-400 mt-0.5">{{ $a->jenis_media }} &bull; {{ $a->kapasitas }}</div>
                                    <div class="font-mono text-[10px] text-slate-400 mt-0.5">X: {{ $a->koordinat_x }}% &bull; Y: {{ $a->koordinat_y }}%</div>
                                </div>
                                <div class="pt-1.5 flex items-center justify-between border-t border-slate-100">
                                    <button 
                                        type="button" 
                                        onclick="highlightMarker({{ $a->id }})"
                                        class="text-[11px] font-semibold text-slate-600 hover:text-slate-900"
                                    >
                                        Tunjuk di Denah
                                    </button>
                                    @if($selectedLantai->has_denah)
                                        <button 
                                            type="button" 
                                            onclick="startPlacementMode({{ $a->id }}, '{{ $a->kode }}', {{ $a->koordinat_x }}, {{ $a->koordinat_y }})"
                                            class="text-[11px] font-bold text-sky-700 hover:text-sky-900"
                                        >
                                            Ubah Titik
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-slate-400 italic">
                                Belum ada unit APAR yang dipetakan pada denah lantai ini.
                            </div>
                        @endforelse
                    </div>

                    <!-- Link Bantuan Tambah APAR ke Ruangan di Lantai Ini -->
                    <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                        <a href="{{ route('apar.create') }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900 hover:underline inline-flex items-center gap-1">
                            <span>+ Daftarkan Unit APAR Baru</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    @endif

    <!-- MODAL UPLOAD DENAH LANTAI -->
    @if($selectedLantai)
        <div id="modal-upload-denah" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 hidden">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-5 text-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-slate-900 text-sm">
                        {{ $selectedLantai->has_denah ? 'Ganti Denah' : 'Upload Denah' }} : {{ $selectedLantai->nama }}
                    </h3>
                    <button type="button" onclick="closeUploadModal()" class="text-slate-400 hover:text-slate-600 font-bold p-1">✕</button>
                </div>

                <form action="{{ route('lokasi.lantai.denah.store', $selectedLantai->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Pilih File Gambar Denah <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="file" 
                            name="gambar" 
                            required 
                            accept="image/jpeg,image/png,image/webp,image/svg+xml"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer border border-slate-200 rounded-lg p-1.5"
                        />
                        <p class="text-[11px] text-slate-500 mt-1">Format diperbolehkan: JPG, PNG, WebP, SVG. Maksimal 5 MB.</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Keterangan / Versi Denah (Opsional)</label>
                        <input 
                            type="text" 
                            name="keterangan" 
                            placeholder="Contoh: Denah arsitektur renovasi 2026"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:bg-white focus:outline-none min-h-[38px]"
                        />
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeUploadModal()" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold min-h-[44px]">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold min-h-[44px]">
                            Unggah Denah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Form Tersembunyi untuk Hapus Titik Koordinat APAR -->
    <form id="form-hapus-titik" method="POST" action="" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Script Interaktivitas Denah, Marker, Popover, dan Mode Penempatan Koordinat Relatif (%) -->
    <script>
        let currentPlacementAparId = null;
        let pendingX = null;
        let pendingY = null;

        function openUploadModal() {
            const modal = document.getElementById('modal-upload-denah');
            if (modal) modal.classList.remove('hidden');
        }

        function closeUploadModal() {
            const modal = document.getElementById('modal-upload-denah');
            if (modal) modal.classList.add('hidden');
        }

        function switchAparTab(tab) {
            const btnUnplaced = document.getElementById('tab-btn-unplaced');
            const btnPlaced = document.getElementById('tab-btn-placed');
            const contentUnplaced = document.getElementById('tab-content-unplaced');
            const contentPlaced = document.getElementById('tab-content-placed');

            if (tab === 'unplaced') {
                btnUnplaced.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                btnUnplaced.classList.remove('text-slate-700');
                btnPlaced.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                btnPlaced.classList.add('text-slate-700');

                contentUnplaced.classList.remove('hidden');
                contentPlaced.classList.add('hidden');
            } else {
                btnPlaced.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                btnPlaced.classList.remove('text-slate-700');
                btnUnplaced.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                btnUnplaced.classList.add('text-slate-700');

                contentPlaced.classList.remove('hidden');
                contentUnplaced.classList.add('hidden');
            }
        }

        function startPlacementMode(aparId, aparKode, currentX = null, currentY = null) {
            closePopover();
            currentPlacementAparId = aparId;
            pendingX = currentX;
            pendingY = currentY;

            const banner = document.getElementById('placement-guide-banner');
            const targetText = document.getElementById('placement-target-text');
            const surface = document.getElementById('denah-surface');
            const previewMarker = document.getElementById('preview-marker');
            const previewLabel = document.getElementById('preview-marker-label');
            const btnSave = document.getElementById('btn-save-placement');

            targetText.textContent = `Menentukan Posisi APAR: ${aparKode}`;
            previewLabel.textContent = aparKode;
            banner.classList.remove('hidden');
            surface.classList.remove('hidden');
            surface.style.cursor = 'crosshair';

            if (currentX !== null && currentY !== null) {
                previewMarker.style.left = currentX + '%';
                previewMarker.style.top = currentY + '%';
                previewMarker.classList.remove('hidden');
                btnSave.disabled = false;
            } else {
                previewMarker.classList.add('hidden');
                btnSave.disabled = true;
            }

            banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function cancelPlacementMode() {
            currentPlacementAparId = null;
            pendingX = null;
            pendingY = null;

            document.getElementById('placement-guide-banner').classList.add('hidden');
            document.getElementById('denah-surface').classList.add('hidden');
            document.getElementById('preview-marker').classList.add('hidden');
        }

        function handleSurfaceClick(event) {
            if (!currentPlacementAparId) return;

            const container = document.getElementById('denah-container');
            const rect = container.getBoundingClientRect();

            // Hitung persentase relatif terhadap gambar denah (0 - 100%)
            const clickX = event.clientX - rect.left;
            const clickY = event.clientY - rect.top;

            let pctX = (clickX / rect.width) * 100;
            let pctY = (clickY / rect.height) * 100;

            // Batasi rentang aman 0 - 100%
            pctX = Math.max(0, Math.min(100, pctX));
            pctY = Math.max(0, Math.min(100, pctY));

            pendingX = Math.round(pctX * 100) / 100;
            pendingY = Math.round(pctY * 100) / 100;

            const previewMarker = document.getElementById('preview-marker');
            previewMarker.style.left = pendingX + '%';
            previewMarker.style.top = pendingY + '%';
            previewMarker.classList.remove('hidden');

            document.getElementById('btn-save-placement').disabled = false;
        }

        function submitPlacement() {
            if (!currentPlacementAparId || pendingX === null || pendingY === null) return;

            const url = `{{ url('/penempatan/apar') }}/${currentPlacementAparId}/titik`;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const btnSave = document.getElementById('btn-save-placement');
            btnSave.disabled = true;
            btnSave.textContent = 'Menyimpan...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    koordinat_x: pendingX,
                    koordinat_y: pendingY
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan posisi: ' + (data.message || 'Terjadi kesalahan'));
                    btnSave.disabled = false;
                    btnSave.textContent = 'Simpan Titik';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
                btnSave.disabled = false;
                btnSave.textContent = 'Simpan Titik';
            });
        }

        function showMarkerPopover(event, apar) {
            event.stopPropagation();
            if (currentPlacementAparId) return; // Nonaktifkan popover jika sedang mode penempatan

            const popover = document.getElementById('marker-popover');
            const container = document.getElementById('denah-container');
            const rect = container.getBoundingClientRect();

            document.getElementById('popover-kode').textContent = apar.kode;
            document.getElementById('popover-gedung').textContent = apar.gedung;
            document.getElementById('popover-lantai').textContent = apar.lantai;
            document.getElementById('popover-ruangan').textContent = apar.ruangan;
            document.getElementById('popover-media').textContent = `${apar.jenis_media} (${apar.kapasitas})`;
            document.getElementById('popover-posisi').textContent = apar.posisi_detail;
            document.getElementById('popover-coord').textContent = `X: ${apar.koordinat_x}%, Y: ${apar.koordinat_y}%`;
            document.getElementById('popover-btn-detail').href = apar.detail_url;

            // Atur tombol pindahkan posisi
            const btnPindah = document.getElementById('popover-btn-pindah');
            btnPindah.onclick = function () {
                startPlacementMode(apar.id, apar.kode, apar.koordinat_x, apar.koordinat_y);
            };

            // Status indicator color
            const dot = document.getElementById('popover-status-dot');
            dot.className = 'w-2.5 h-2.5 rounded-full';
            if (apar.status === 'baik') dot.classList.add('bg-emerald-500');
            else if (apar.status === 'perhatian') dot.classList.add('bg-amber-500');
            else if (apar.status === 'rusak') dot.classList.add('bg-rose-500');
            else dot.classList.add('bg-slate-400');

            // Posisi Popover dekat marker secara proporsional
            let posX = (apar.koordinat_x / 100) * rect.width;
            let posY = (apar.koordinat_y / 100) * rect.height;

            // Cegah overflow popover keluar container
            if (posX + 290 > rect.width) {
                posX = posX - 290;
            } else {
                posX = posX + 15;
            }

            if (posY + 200 > rect.height) {
                posY = posY - 180;
            } else {
                posY = posY - 20;
            }

            posX = Math.max(10, posX);
            posY = Math.max(10, posY);

            popover.style.left = posX + 'px';
            popover.style.top = posY + 'px';
            popover.classList.remove('hidden');
        }

        function closePopover() {
            const popover = document.getElementById('marker-popover');
            if (popover) popover.classList.add('hidden');
        }

        function highlightMarker(aparId) {
            closePopover();
            const marker = document.getElementById('marker-apar-' + aparId);
            if (marker) {
                marker.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                marker.classList.add('scale-125', 'ring-4', 'ring-sky-400', 'z-40');
                setTimeout(() => {
                    marker.classList.remove('scale-125', 'ring-4', 'ring-sky-400', 'z-40');
                }, 2000);
            }
        }

        // Tutup popover jika mengklik di luar
        document.addEventListener('click', function(e) {
            const popover = document.getElementById('marker-popover');
            if (popover && !popover.contains(e.target) && !e.target.closest('.apar-marker')) {
                popover.classList.add('hidden');
            }
        });
    </script>
</x-layouts.app>
