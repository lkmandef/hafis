<x-layouts.app title="HAFIS : Struktur Lokasi Rumah Sakit">
    <!-- 1. Page Header -->
    <x-page-header
        title="Struktur Lokasi Rumah Sakit"
        description="Manajemen hierarki Gedung, Lantai, dan Ruangan atau Area fasilitas rumah sakit">
        <x-slot:actions>
            @if(auth()->user()?->isAdmin())
                <button 
                    type="button" 
                    onclick="openModal('modal-tambah-gedung')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#111827] hover:bg-gray-800 active:scale-[0.98] text-white font-semibold text-xs transition duration-150 ease-in-out shadow-xs min-h-[38px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Gedung</span>
                </button>
                <div class="inline-flex items-center gap-1.5">
                    <button 
                        type="button" 
                        onclick="openModal('modal-tambah-lantai')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] text-gray-700 font-semibold text-xs transition duration-150 ease-in-out shadow-xs min-h-[38px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none"
                    >
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Tambah Lantai</span>
                    </button>
                    <button 
                        type="button" 
                        onclick="openModal('modal-tambah-ruangan')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] text-gray-700 font-semibold text-xs transition duration-150 ease-in-out shadow-xs min-h-[38px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none"
                    >
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Tambah Ruangan</span>
                    </button>
                </div>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- Feedback Notifikasi Sukses & Error -->
    @if(session('success'))
        <div role="status" aria-live="polite" class="mb-5 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1 rounded focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:outline-none" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div role="alert" aria-live="assertive" class="mb-5 p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs shadow-2xs">
            <div class="font-semibold mb-1 flex items-center gap-1.5 text-rose-950">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Terdapat kesalahan pengisian data:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-800 pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. Ringkasan Metrik Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-xs">
            <span class="text-gray-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Total Gedung</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tracking-tight text-gray-900 tabular-nums">{{ $totalGedung }}</span>
                <span class="text-xs text-gray-500 font-medium">Bangunan terdaftar</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-xs">
            <span class="text-gray-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Total Lantai</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tracking-tight text-gray-900 tabular-nums">{{ $totalLantai }}</span>
                <span class="text-xs text-gray-500 font-medium">Tingkat lantai fisik</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-xs">
            <span class="text-gray-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Titik Lokasi APAR</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tracking-tight text-gray-900 tabular-nums">{{ $totalLokasi }}</span>
                <span class="text-xs text-gray-500 font-medium">Titik sebaran K3RS</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-xs">
            <span class="text-gray-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Master Ruangan</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tracking-tight text-gray-900 tabular-nums">{{ $totalRuangan }}</span>
                <span class="text-xs text-gray-500 font-medium">Ruang fisik terdata</span>
            </div>
        </div>
    </div>

    <!-- 3. Toolbar Pencarian & Filter Tab -->
    @php
        $activeTab = in_array($tab, ['hirarki', 'gedung', 'lantai', 'ruangan']) ? $tab : 'hirarki';
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-3.5 mb-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
        
        <!-- Tab Navigasi View -->
        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg border border-gray-200/80 overflow-x-auto scrollbar-none max-w-full scroll-fade-r sm:[mask-image:none] pr-6 sm:pr-1">
            <button 
                type="button" 
                onclick="switchViewTab('tab-hirarki')" 
                id="btn-tab-hirarki"
                class="px-3 py-1.5 rounded-md text-xs whitespace-nowrap transition {{ $activeTab === 'hirarki' ? 'font-semibold bg-white text-gray-900 shadow-2xs border border-gray-200/60' : 'font-medium text-gray-600 hover:text-gray-900' }}"
            >
                Hierarki Lokasi
            </button>
            <button 
                type="button" 
                onclick="switchViewTab('tab-gedung')" 
                id="btn-tab-gedung"
                class="px-3 py-1.5 rounded-md text-xs whitespace-nowrap transition {{ $activeTab === 'gedung' ? 'font-semibold bg-white text-gray-900 shadow-2xs border border-gray-200/60' : 'font-medium text-gray-600 hover:text-gray-900' }}"
            >
                Gedung (<span class="tabular-nums">{{ $totalGedung }}</span>)
            </button>
            <button 
                type="button" 
                onclick="switchViewTab('tab-lantai')" 
                id="btn-tab-lantai"
                class="px-3 py-1.5 rounded-md text-xs whitespace-nowrap transition {{ $activeTab === 'lantai' ? 'font-semibold bg-white text-gray-900 shadow-2xs border border-gray-200/60' : 'font-medium text-gray-600 hover:text-gray-900' }}"
            >
                Lantai (<span class="tabular-nums">{{ $totalLantai }}</span>)
            </button>
            <button 
                type="button" 
                onclick="switchViewTab('tab-ruangan')" 
                id="btn-tab-ruangan"
                class="px-3 py-1.5 rounded-md text-xs whitespace-nowrap transition {{ $activeTab === 'ruangan' ? 'font-semibold bg-white text-gray-900 shadow-2xs border border-gray-200/60' : 'font-medium text-gray-600 hover:text-gray-900' }}"
            >
                Ruangan (<span class="tabular-nums">{{ $totalRuangan }}</span>)
            </button>
        </div>

        <!-- Form Pencarian -->
        <form method="GET" action="{{ route('lokasi.index') }}" class="flex items-center gap-2">
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input 
                    type="text" 
                    id="input-search-lokasi"
                    name="q" 
                    value="{{ $search }}" 
                    aria-label="Pencarian struktur gedung, lantai, dan ruangan"
                    autocomplete="off"
                    placeholder="Cari gedung, lantai, ruangan…" 
                    class="w-full pl-9 pr-3 py-1.5 text-xs text-gray-900 placeholder-gray-400 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none transition min-h-[38px]"
                />
            </div>
            @if($search)
                <a href="{{ route('lokasi.index') }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 text-xs font-medium min-h-[38px] inline-flex items-center focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">
                    Reset
                </a>
            @endif
        </form>

    </div>

    <!-- 4. Konten Tampilan (TAB 1: HIERARKI LOKASI TREE VIEW) -->
    <div id="content-tab-hirarki" class="{{ $activeTab === 'hirarki' ? 'space-y-4' : 'hidden space-y-4' }}">
        @forelse($gedungs as $g)
            <div class="bg-white border border-gray-200/90 rounded-2xl overflow-hidden shadow-xs">
                <!-- Header Gedung -->
                <div class="p-4 sm:px-5 bg-gray-50/80 border-b border-gray-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start sm:items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-gray-100 border border-gray-200/80 text-gray-800 font-bold font-mono text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            {{ substr($g->kode, 4) }}
                        </span>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('lokasi.gedung.show', $g->id) }}" class="font-bold text-gray-900 text-sm tracking-tight hover:underline flex items-center gap-1.5 focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none rounded" title="Buka Detail Gedung">
                                    <span>{{ $g->nama }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                                <span class="font-mono text-[11px] text-gray-600 bg-white border border-gray-200 px-1.5 py-0.5 rounded tabular-nums">{{ $g->kode }}</span>
                                <x-badge :variant="$g->status === 'aktif' ? 'success' : 'default'">
                                    {{ ucfirst($g->status) }}
                                </x-badge>
                            </div>
                            @if($g->keterangan)
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $g->keterangan }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                        <span class="text-xs text-gray-500 mr-1">
                            <strong class="text-gray-900 font-mono tabular-nums">{{ $g->lantais_count }}</strong> Lantai · 
                            <strong class="text-gray-900 font-mono tabular-nums">{{ $g->lokasis_count }}</strong> Titik Lokasi
                            @if($g->ruangans_count > 0)
                                · <span class="text-gray-500 font-mono"><strong>{{ $g->ruangans_count }}</strong> Ruangan</span>
                            @endif
                        </span>

                        <a 
                            href="{{ route('lokasi.gedung.show', $g->id) }}" 
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] text-gray-700 transition focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none shadow-2xs"
                            title="Lihat Detail Gedung"
                        >
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            <span>Detail Gedung</span>
                        </a>

                        @if(auth()->user()?->isAdmin())
                            <button 
                                type="button" 
                                onclick="openEditGedungModal(this)"
                                data-id="{{ $g->id }}"
                                data-nama="{{ $g->nama }}"
                                data-kode="{{ $g->kode }}"
                                data-status="{{ $g->status }}"
                                data-keterangan="{{ $g->keterangan ?? '' }}"
                                class="p-1.5 text-gray-500 hover:text-gray-900 rounded-lg hover:bg-gray-100 active:scale-[0.96] transition focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer"
                                title="Edit Gedung"
                                aria-label="Edit Gedung {{ $g->nama }}"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>

                            <button 
                                type="button" 
                                onclick="openAddLantaiForGedung({{ $g->id }}, '{{ $g->nama }}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] text-gray-700 transition focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer shadow-2xs"
                                title="Tambah Lantai untuk Gedung ini"
                                aria-label="Tambah Lantai untuk {{ $g->nama }}"
                            >
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Lantai</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Daftar Lantai di Gedung Ini -->
                <div class="p-4 sm:p-5 space-y-4">
                    @forelse($g->lantais as $l)
                        <div class="rounded-xl border border-gray-200/70 bg-gray-50/70 p-4 text-xs shadow-2xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-200/70 mb-3 gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="w-7 h-7 rounded-lg bg-[#111827] text-white font-mono font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        L{{ $l->nomor_lantai }}
                                    </span>
                                    <strong class="text-gray-900 font-semibold text-xs">{{ $l->nama }}</strong>
                                    @if($l->keterangan)
                                        <span class="text-gray-300">•</span>
                                        <span class="text-[11px] text-gray-500 italic">{{ $l->keterangan }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @php
                                        $floorAparCount = $l->apars_count;
                                    @endphp
                                    <span class="text-[11px] font-mono text-gray-500 mr-1 tabular-nums">
                                        <strong>{{ $l->lokasis->count() }}</strong> Titik Lokasi
                                        @if($l->ruangans->count() > 0)
                                            · <strong>{{ $l->ruangans->count() }}</strong> Ruangan
                                        @endif
                                        · <strong class="{{ $floorAparCount > 0 ? 'text-gray-900' : 'text-gray-400' }}">{{ $floorAparCount }}</strong> APAR
                                    </span>
                                    @if(auth()->user()?->isAdmin())
                                        <button 
                                            type="button" 
                                            onclick="openAddRuanganForLantai({{ $l->id }}, '{{ $g->nama }} - {{ $l->nama }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-md border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] text-gray-700 transition focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer shadow-2xs"
                                            title="Tambah Ruangan untuk {{ $l->nama }}"
                                            aria-label="Tambah Ruangan untuk {{ $l->nama }}"
                                        >
                                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            <span>Ruangan/Area</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            onclick="openEditLantaiModal(this)"
                                            data-id="{{ $l->id }}"
                                            data-nomor="{{ $l->nomor_lantai }}"
                                            data-nama="{{ $l->nama }}"
                                            data-keterangan="{{ $l->keterangan ?? '' }}"
                                            data-gedung-nama="{{ $g->nama }}"
                                            class="p-1 text-gray-400 hover:text-gray-700 rounded hover:bg-gray-200/60 active:scale-[0.96] transition focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer"
                                            title="Edit Lantai"
                                            aria-label="Edit Lantai {{ $l->nama }}"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Daftar Titik Lokasi & Ruangan di Lantai Ini -->
                            <div class="pl-1 sm:pl-2">
                                @php
                                    $hasLokasis = $l->lokasis && $l->lokasis->count() > 0;
                                    $hasRuangans = $l->ruangans && $l->ruangans->count() > 0;
                                @endphp

                                @if($hasLokasis)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                        @foreach($l->lokasis as $lok)
                                            @php
                                                $catLabel = $lok->kategori ?: $lok->tipe_lokasi_label;
                                            @endphp
                                            <div class="p-3 rounded-xl border border-gray-200/90 bg-white flex flex-col justify-between shadow-2xs hover:shadow-xs hover:border-gray-300 transition group">
                                                <div>
                                                    <div class="flex items-start justify-between gap-1.5 mb-1.5">
                                                        <span class="font-bold text-gray-900 text-xs leading-snug break-words">
                                                            {{ $lok->nama }}
                                                        </span>
                                                        @if($lok->kode)
                                                            <span class="font-mono text-[10px] bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded border border-gray-200 shrink-0 tabular-nums">
                                                                {{ $lok->kode }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="flex items-center gap-1.5 flex-wrap mb-2">
                                                        <span class="inline-flex items-center text-[10px] font-medium px-1.5 py-0.5 rounded border border-gray-200 bg-gray-50 text-gray-700">
                                                            {{ $catLabel }}
                                                        </span>

                                                        @if($lok->ruangan && $lok->ruangan->kode)
                                                            <span class="inline-flex items-center font-mono text-[10px] font-medium text-gray-600 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200 tabular-nums" title="Master Ruangan Fisik: {{ $lok->ruangan->nama }}">
                                                                {{ $lok->ruangan->kode }}
                                                            </span>
                                                        @endif

                                                        @if($lok->status === 'stok')
                                                            <span class="inline-flex items-center text-[10px] font-semibold px-1.5 py-0.5 rounded border bg-amber-50 text-amber-800 border-amber-200">
                                                                Stok Sediaan
                                                            </span>
                                                        @elseif($lok->status === 'kosong' || $lok->apars->count() === 0)
                                                            <span class="inline-flex items-center text-[10px] font-medium px-1.5 py-0.5 rounded border bg-gray-100 text-gray-500 border-gray-200 tabular-nums">
                                                                0 APAR
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($lok->keterangan)
                                                        <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed mb-2">
                                                            {{ $lok->keterangan }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="pt-2.5 mt-2 border-t border-gray-100 flex items-start justify-between text-[11px] gap-2">
                                                    <div>
                                                        <div class="font-mono text-gray-700 flex items-center gap-1 font-semibold">
                                                            <svg class="w-3.5 h-3.5 {{ $lok->apars->count() > 0 ? 'text-red-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343a7.975 7.975 0 012.344 5.657c0 2.11-.82 4.126-2.343 5.657z" />
                                                            </svg>
                                                            <span><strong class="tabular-nums">{{ $lok->apars->count() }}</strong> APAR</span>
                                                        </div>
                                                        @if($lok->apars->count() > 0)
                                                            <div class="text-[11px] text-gray-600 font-mono mt-1 font-medium leading-relaxed">
                                                                {{ $lok->rincian_media }}
                                                            </div>
                                                        @else
                                                            <div class="text-[10px] text-gray-400 italic mt-0.5">
                                                                Non-penempatan unit
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if($lok->apars->count() > 0)
                                                        <a 
                                                            href="{{ route('apar.index', ['lokasi' => $lok->id]) }}" 
                                                            class="text-gray-500 hover:text-gray-900 font-semibold inline-flex items-center gap-0.5 transition text-[11px] shrink-0 mt-0.5 focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none rounded active:scale-[0.98]"
                                                            title="Lihat unit APAR di lokasi {{ $lok->nama }}"
                                                        >
                                                            <span>Daftar APAR</span>
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($hasRuangans)
                                    <div class="{{ $hasLokasis ? 'mt-3 pt-3 border-t border-gray-200/70' : '' }}">
                                        <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 mb-2 flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span>Master Ruangan Fisik Terdaftar (<span class="tabular-nums">{{ $l->ruangans->count() }}</span>):</span>
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-normal italic">Klinis / Administratif</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                            @foreach($l->ruangans as $r)
                                                <div class="p-2.5 rounded-lg border border-gray-200/80 bg-white/90 flex items-center justify-between gap-2 shadow-2xs hover:border-gray-300 transition group">
                                                    <div class="min-w-0">
                                                        <div class="font-semibold text-gray-900 text-xs truncate flex items-center gap-1.5">
                                                            <span>{{ $r->nama }}</span>
                                                            @if($r->kode)
                                                                <span class="font-mono text-[10px] bg-gray-100 text-gray-700 px-1 rounded tabular-nums">{{ $r->kode }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-[10px] text-gray-500 flex items-center gap-1.5 mt-0.5">
                                                            @if($r->kategori)
                                                                <span class="truncate">{{ $r->kategori }}</span>
                                                            @endif
                                                            <span class="text-gray-300">•</span>
                                                            <span class="font-mono text-gray-600 font-semibold tabular-nums">{{ $r->apars->count() }} APAR</span>
                                                        </div>
                                                    </div>
                                                    @if(auth()->user()?->isAdmin())
                                                        <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100">
                                                            <button 
                                                                type="button" 
                                                                onclick="openEditRuanganModal(this)"
                                                                data-id="{{ $r->id }}"
                                                                data-nama="{{ $r->nama }}"
                                                                data-kode="{{ $r->kode ?? '' }}"
                                                                data-kategori="{{ $r->kategori ?? '' }}"
                                                                data-keterangan="{{ $r->keterangan ?? '' }}"
                                                                data-lokasi-label="{{ $g->nama }} - {{ $l->nama }}"
                                                                class="p-1 text-gray-400 hover:text-gray-800 rounded hover:bg-gray-100 active:scale-[0.96] transition focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer"
                                                                title="Edit Ruangan"
                                                                aria-label="Edit Ruangan {{ $r->nama }}"
                                                            >
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!$hasLokasis && !$hasRuangans)
                                    <div class="p-3.5 rounded-xl border border-gray-200/80 bg-gray-50/70 text-gray-600 flex items-start gap-2.5">
                                        <span class="p-1 rounded-md bg-gray-200 text-gray-500 shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </span>
                                        <div>
                                            <strong class="font-semibold text-gray-800 block text-xs">Area Non-Penempatan APAR (0 Unit)</strong>
                                            <p class="text-[11px] text-gray-500 leading-relaxed mt-0.5">
                                                Berdasarkan dokumen inventarisasi K3RS resmi, tidak ada titik penempatan APAR aktif pada lantai ini (klausul: <em>"hanya ruangan yang memiliki APAR"</em>).
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-400 text-xs italic">
                            Belum ada konfigurasi lantai untuk gedung ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-500 text-xs">
                Tidak ada data gedung yang cocok dengan pencarian "{{ $search }}".
            </div>
        @endforelse
    </div>

    <!-- TAB 2: TABEL SEMUA GEDUNG -->
    <div id="content-tab-gedung" class="{{ $activeTab === 'gedung' ? 'bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs' : 'hidden bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs' }}">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left min-w-[750px]">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-3 pl-5 sm:pl-6 pr-4">Kode</th>
                        <th class="px-4 py-3">Nama Gedung</th>
                        <th class="px-4 py-3 text-center">Jumlah Lantai</th>
                        <th class="px-4 py-3 text-center">Jumlah Ruangan</th>
                        <th class="px-4 py-3">Keterangan / Catatan K3RS</th>
                        <th class="px-4 py-3">Status</th>
                        @if(auth()->user()?->isAdmin())
                            <th class="py-3 pl-4 pr-5 sm:pr-6 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($allGedungs as $g)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3 pl-5 sm:pl-6 pr-4 font-mono font-bold text-gray-900 tabular-nums">{{ $g->kode }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $g->nama }}</td>
                            <td class="px-4 py-3 text-center font-bold font-mono text-gray-800 tabular-nums">{{ $g->lantais_count }}</td>
                            <td class="px-4 py-3 text-center font-bold font-mono text-gray-800 tabular-nums">{{ $g->ruangans_count }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $g->keterangan ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <x-badge :variant="$g->status === 'aktif' ? 'success' : 'neutral'">
                                    {{ ucfirst($g->status) }}
                                </x-badge>
                            </td>
                            @if(auth()->user()?->isAdmin())
                                <td class="py-3 pl-4 pr-5 sm:pr-6 text-right space-x-1">
                                    <button 
                                        type="button" 
                                        onclick="openEditGedungModal(this)"
                                        data-id="{{ $g->id }}"
                                        data-nama="{{ $g->nama }}"
                                        data-kode="{{ $g->kode }}"
                                        data-status="{{ $g->status }}"
                                        data-keterangan="{{ $g->keterangan ?? '' }}"
                                        class="px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 active:scale-[0.96] text-gray-700 font-medium text-[11px] transition shadow-2xs cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none"
                                        aria-label="Edit Gedung {{ $g->nama }}"
                                    >
                                        Edit
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: TABEL SEMUA LANTAI -->
    <div id="content-tab-lantai" class="{{ $activeTab === 'lantai' ? 'bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs' : 'hidden bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs' }}">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left min-w-[750px]">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-3 pl-5 sm:pl-6 pr-4">Gedung Induk</th>
                        <th class="px-4 py-3 text-center">Nomor Lantai</th>
                        <th class="px-4 py-3">Label Lantai</th>
                        <th class="px-4 py-3 text-center">Titik Lokasi APAR</th>
                        <th class="px-4 py-3 text-center">Master Ruangan</th>
                        <th class="px-4 py-3">Keterangan</th>
                        @if(auth()->user()?->isAdmin())
                            <th class="py-3 pl-4 pr-5 sm:pr-6 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($allLantais as $l)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3 pl-5 sm:pl-6 pr-4 font-semibold text-gray-900">{{ $l->gedung->nama }}</td>
                            <td class="px-4 py-3 text-center font-mono font-bold text-gray-800 tabular-nums">{{ $l->nomor_lantai }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $l->nama }}</td>
                            <td class="px-4 py-3 text-center font-bold font-mono text-gray-800 tabular-nums">{{ $l->lokasis_count }}</td>
                            <td class="px-4 py-3 text-center font-bold font-mono text-gray-800 tabular-nums">{{ $l->ruangans_count }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $l->keterangan ?: '-' }}</td>
                            @if(auth()->user()?->isAdmin())
                                <td class="py-3 pl-4 pr-5 sm:pr-6 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <button 
                                            type="button" 
                                            onclick="openEditLantaiModal(this)"
                                            data-id="{{ $l->id }}"
                                            data-nomor="{{ $l->nomor_lantai }}"
                                            data-nama="{{ $l->nama }}"
                                            data-keterangan="{{ $l->keterangan ?? '' }}"
                                            data-gedung-nama="{{ $l->gedung->nama }}"
                                            class="px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 active:scale-[0.96] text-gray-700 font-medium text-[11px] transition shadow-2xs cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none"
                                            aria-label="Edit Lantai {{ $l->nama }} ({{ $l->gedung->nama }})"
                                        >
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 4: TABEL SEMUA RUANGAN / AREA -->
    <div id="content-tab-ruangan" class="{{ $activeTab === 'ruangan' ? 'bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs' : 'hidden bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs' }}">
        <div class="p-3.5 bg-gray-50/80 border-b border-gray-200 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <strong class="font-semibold text-gray-900 block text-xs">Master Ruangan Fisik Terdaftar</strong>
                <span class="text-gray-500 text-[11px]">Daftar ruangan klinis dan administratif tertutup rumah sakit. Untuk seluruh titik penempatan APAR operasional (termasuk koridor dan zona layanan), lihat tab Hierarki Lokasi.</span>
            </div>
            @if(auth()->user()?->isAdmin())
                <button 
                    type="button" 
                    onclick="openModal('modal-tambah-ruangan')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#111827] hover:bg-gray-800 text-white font-semibold text-xs transition shrink-0 self-start sm:self-auto cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Ruangan</span>
                </button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left min-w-[850px]">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="py-3 pl-5 sm:pl-6 pr-4">Kode</th>
                        <th class="px-4 py-3">Nama Ruangan / Area</th>
                        <th class="px-4 py-3">Gedung &amp; Lantai</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">APAR Terpasang</th>
                        <th class="px-4 py-3">Keterangan</th>
                        @if(auth()->user()?->isAdmin())
                            <th class="py-3 pl-4 pr-5 sm:pr-6 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($allRuangans as $r)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3 pl-5 sm:pl-6 pr-4 font-mono text-gray-600 font-semibold tabular-nums">{{ $r->kode ?: '-' }}</td>
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $r->nama }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                @if($r->lantai && $r->lantai->gedung)
                                    <span>{{ $r->lantai->gedung->nama }}</span>
                                    <span class="text-gray-400">·</span>
                                    <span class="font-medium text-gray-600">{{ $r->lantai->nama }}</span>
                                @elseif($r->lokasi && $r->lokasi->gedung)
                                    <span>{{ $r->lokasi->gedung->nama }}</span>
                                @else
                                    <span class="text-gray-400 italic">Non-Lantai</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-gray-100 text-gray-700 border border-gray-200 px-2 py-0.5 rounded text-[11px] font-medium">
                                    {{ $r->kategori ?: 'Umum' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-mono text-gray-800 font-semibold flex items-center gap-1">
                                    <span class="{{ $r->apars->count() > 0 ? 'text-gray-900' : 'text-gray-400' }} tabular-nums">{{ $r->apars->count() }} Unit</span>
                                    @if($r->apars->count() > 0)
                                        <span class="text-[11px] text-gray-500 font-normal">({{ $r->rincian_media }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $r->keterangan ?: '-' }}</td>
                            @if(auth()->user()?->isAdmin())
                                <td class="py-3 pl-4 pr-5 sm:pr-6 text-right">
                                    <button 
                                        type="button" 
                                        onclick="openEditRuanganModal(this)"
                                        data-id="{{ $r->id }}"
                                        data-nama="{{ $r->nama }}"
                                        data-kode="{{ $r->kode ?? '' }}"
                                        data-kategori="{{ $r->kategori ?? '' }}"
                                        data-keterangan="{{ $r->keterangan ?? '' }}"
                                        data-lokasi-label="{{ $r->lantai ? ($r->lantai->gedung->nama . ' - ' . $r->lantai->nama) : ($r->lokasi && $r->lokasi->gedung ? $r->lokasi->gedung->nama : 'Fasilitas') }}"
                                        class="px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 active:scale-[0.96] text-gray-700 font-medium text-[11px] transition shadow-2xs cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none"
                                        aria-label="Edit Ruangan {{ $r->nama }}"
                                    >
                                        Edit
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-400">
                                Belum ada data ruangan/area.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODALS: TAMBAH & EDIT ENTITAS LOKASI                          -->
    <!-- ============================================================== -->
    @if(auth()->user()?->isAdmin())
    <!-- Modal 1: Tambah Gedung -->
    <div id="modal-tambah-gedung" role="dialog" aria-modal="true" aria-labelledby="title-modal-tambah-gedung" class="hidden fixed inset-0 z-50 overflow-y-auto overscroll-contain bg-black/40 backdrop-blur-xs p-4 flex items-center justify-center">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full p-5 sm:p-6 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 id="title-modal-tambah-gedung" class="font-bold text-gray-900 text-sm">Tambah Gedung Baru</h3>
                <button type="button" onclick="closeModal('modal-tambah-gedung')" aria-label="Tutup modal" class="text-gray-400 hover:text-gray-700 font-bold p-1 rounded focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer">✕</button>
            </div>
            <form action="{{ route('lokasi.gedung.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="tambah-gedung-nama" class="block font-semibold text-gray-700 mb-1">Nama Gedung <span class="text-rose-500">*</span></label>
                    <input type="text" id="tambah-gedung-nama" name="nama" required placeholder="Contoh: Gedung Paviliun Melati" autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>
                <div>
                    <label for="tambah-gedung-kode" class="block font-semibold text-gray-700 mb-1">Kode Gedung (Opsional)</label>
                    <input type="text" id="tambah-gedung-kode" name="kode" placeholder="Contoh: GDG-PVL (otomatis jika kosong)" spellcheck="false" autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-mono text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>
                <div>
                    <label for="tambah-gedung-keterangan" class="block font-semibold text-gray-700 mb-1">Keterangan / Catatan</label>
                    <textarea id="tambah-gedung-keterangan" name="keterangan" rows="2" placeholder="Catatan fungsi operasional atau peruntukan gedung…" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-tambah-gedung')" class="px-3.5 py-2 rounded-lg border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#111827] text-white font-semibold hover:bg-gray-800 transition shadow-xs min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Simpan Gedung</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Edit Gedung -->
    <div id="modal-edit-gedung" role="dialog" aria-modal="true" aria-labelledby="title-modal-edit-gedung" class="hidden fixed inset-0 z-50 overflow-y-auto overscroll-contain bg-black/40 backdrop-blur-xs p-4 flex items-center justify-center">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full p-5 sm:p-6 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 id="title-modal-edit-gedung" class="font-bold text-gray-900 text-sm">Edit Data Gedung</h3>
                <button type="button" onclick="closeModal('modal-edit-gedung')" aria-label="Tutup modal" class="text-gray-400 hover:text-gray-700 font-bold p-1 rounded focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer">✕</button>
            </div>
            <form id="form-edit-gedung" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit-gedung-nama" class="block font-semibold text-gray-700 mb-1">Nama Gedung <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit-gedung-nama" name="nama" required autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>
                <div>
                    <label for="edit-gedung-kode" class="block font-semibold text-gray-700 mb-1">Kode Gedung <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit-gedung-kode" name="kode" required spellcheck="false" autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-mono text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>
                <div>
                    <label for="edit-gedung-status" class="block font-semibold text-gray-700 mb-1">Status Operasional</label>
                    <select id="edit-gedung-status" name="status" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px] cursor-pointer">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif / Renovasi</option>
                    </select>
                </div>
                <div>
                    <label for="edit-gedung-keterangan" class="block font-semibold text-gray-700 mb-1">Keterangan / Catatan</label>
                    <textarea id="edit-gedung-keterangan" name="keterangan" rows="2" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                    <button type="button" onclick="confirmDeleteGedung()" class="text-rose-600 hover:text-rose-800 font-semibold cursor-pointer focus-visible:ring-2 focus-visible:ring-rose-600 focus-visible:outline-none rounded px-1.5 py-0.5">Hapus Gedung</button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeModal('modal-edit-gedung')" class="px-3.5 py-2 rounded-lg border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-[#111827] text-white font-semibold hover:bg-gray-800 transition shadow-xs min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Perbarui</button>
                    </div>
                </div>
            </form>
            <form id="form-delete-gedung" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <!-- Modal 3: Tambah Lantai -->
    <div id="modal-tambah-lantai" role="dialog" aria-modal="true" aria-labelledby="title-modal-tambah-lantai" class="hidden fixed inset-0 z-50 overflow-y-auto overscroll-contain bg-black/40 backdrop-blur-xs p-4 flex items-center justify-center">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full p-5 sm:p-6 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 id="title-modal-tambah-lantai" class="font-bold text-gray-900 text-sm">Tambah Lantai Baru</h3>
                <button type="button" onclick="closeModal('modal-tambah-lantai')" aria-label="Tutup modal" class="text-gray-400 hover:text-gray-700 font-bold p-1 rounded focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer">✕</button>
            </div>
            <form action="{{ route('lokasi.lantai.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="tambah-lantai-gedung-id" class="block font-semibold text-gray-700 mb-1">Gedung Induk <span class="text-rose-500">*</span></label>
                    <select id="tambah-lantai-gedung-id" name="gedung_id" required class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px] cursor-pointer">
                        <option value="">-- Pilih Gedung --</option>
                        @foreach($allGedungs as $g)
                            <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="tambah-lantai-nomor" class="block font-semibold text-gray-700 mb-1">Nomor Lantai <span class="text-rose-500">*</span></label>
                        <input type="number" id="tambah-lantai-nomor" name="nomor_lantai" required value="1" min="-3" max="50" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-mono text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    </div>
                    <div>
                        <label for="tambah-lantai-nama" class="block font-semibold text-gray-700 mb-1">Label Nama <span class="text-rose-500">*</span></label>
                        <input type="text" id="tambah-lantai-nama" name="nama" required value="Lantai 1" placeholder="Contoh: Lantai 1 / Basement" autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    </div>
                </div>
                <div>
                    <label for="tambah-lantai-keterangan" class="block font-semibold text-gray-700 mb-1">Keterangan (Opsional)</label>
                    <textarea id="tambah-lantai-keterangan" name="keterangan" rows="2" placeholder="Catatan penunjang lantai…" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-tambah-lantai')" class="px-3.5 py-2 rounded-lg border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#111827] text-white font-semibold hover:bg-gray-800 transition shadow-xs min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Simpan Lantai</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 4: Edit Lantai -->
    <div id="modal-edit-lantai" role="dialog" aria-modal="true" aria-labelledby="title-modal-edit-lantai" class="hidden fixed inset-0 z-50 overflow-y-auto overscroll-contain bg-black/40 backdrop-blur-xs p-4 flex items-center justify-center">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full p-5 sm:p-6 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 id="title-modal-edit-lantai" class="font-bold text-gray-900 text-sm">Edit Data Lantai</h3>
                <button type="button" onclick="closeModal('modal-edit-lantai')" aria-label="Tutup modal" class="text-gray-400 hover:text-gray-700 font-bold p-1 rounded focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer">✕</button>
            </div>
            <form id="form-edit-lantai" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit-lantai-gedung-label" class="block font-semibold text-gray-500 mb-1">Gedung Induk</label>
                    <input type="text" id="edit-lantai-gedung-label" disabled class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-xs text-gray-600 min-h-[40px]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="edit-lantai-nomor" class="block font-semibold text-gray-700 mb-1">Nomor Lantai <span class="text-rose-500">*</span></label>
                        <input type="number" id="edit-lantai-nomor" name="nomor_lantai" required class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-mono text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    </div>
                    <div>
                        <label for="edit-lantai-nama" class="block font-semibold text-gray-700 mb-1">Label Nama <span class="text-rose-500">*</span></label>
                        <input type="text" id="edit-lantai-nama" name="nama" required autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    </div>
                </div>
                <div>
                    <label for="edit-lantai-keterangan" class="block font-semibold text-gray-700 mb-1">Keterangan</label>
                    <textarea id="edit-lantai-keterangan" name="keterangan" rows="2" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                    <button type="button" onclick="confirmDeleteLantai()" class="text-rose-600 hover:text-rose-800 font-semibold cursor-pointer focus-visible:ring-2 focus-visible:ring-rose-600 focus-visible:outline-none rounded px-1.5 py-0.5">Hapus Lantai</button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeModal('modal-edit-lantai')" class="px-3.5 py-2 rounded-lg border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-[#111827] text-white font-semibold hover:bg-gray-800 transition shadow-xs min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Perbarui</button>
                    </div>
                </div>
            </form>
            <form id="form-delete-lantai" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <!-- Modal 5: Tambah Ruangan / Area -->
    <div id="modal-tambah-ruangan" role="dialog" aria-modal="true" aria-labelledby="title-modal-tambah-ruangan" class="hidden fixed inset-0 z-50 overflow-y-auto overscroll-contain bg-black/40 backdrop-blur-xs p-4 flex items-center justify-center">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full p-5 sm:p-6 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 id="title-modal-tambah-ruangan" class="font-bold text-gray-900 text-sm">Tambah Ruangan / Area Baru</h3>
                <button type="button" onclick="closeModal('modal-tambah-ruangan')" aria-label="Tutup modal" class="text-gray-400 hover:text-gray-700 font-bold p-1 rounded focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer">✕</button>
            </div>
            <form action="{{ route('lokasi.ruangan.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="tambah-ruangan-lantai-id" class="block font-semibold text-gray-700 mb-1">Pilih Lantai Induk <span class="text-rose-500">*</span></label>
                    <select id="tambah-ruangan-lantai-id" name="lantai_id" required class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px] cursor-pointer">
                        <option value="">-- Pilih Lantai --</option>
                        @foreach($allLantais as $l)
                            <option value="{{ $l->id }}">{{ $l->gedung->nama }} - {{ $l->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tambah-ruangan-nama" class="block font-semibold text-gray-700 mb-1">Nama Ruangan / Area <span class="text-rose-500">*</span></label>
                    <input type="text" id="tambah-ruangan-nama" name="nama" required placeholder="Contoh: Poli Mata / Ruang Tindakan Bedah" autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="tambah-ruangan-kode" class="block font-semibold text-gray-700 mb-1">Kode Ruangan</label>
                        <input type="text" id="tambah-ruangan-kode" name="kode" placeholder="Contoh: RNG-101" spellcheck="false" autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-mono text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    </div>
                    <div>
                        <label for="tambah-ruangan-kategori" class="block font-semibold text-gray-700 mb-1">Kategori Area</label>
                        <input type="text" id="tambah-ruangan-kategori" name="kategori" placeholder="Contoh: Pelayanan / Rawat Inap" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    </div>
                </div>
                <div>
                    <label for="tambah-ruangan-keterangan" class="block font-semibold text-gray-700 mb-1">Keterangan</label>
                    <textarea id="tambah-ruangan-keterangan" name="keterangan" rows="2" placeholder="Catatan posisi spesifik atau penanggung jawab…" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-tambah-ruangan')" class="px-3.5 py-2 rounded-lg border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#111827] text-white font-semibold hover:bg-gray-800 transition shadow-xs min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Simpan Ruangan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 6: Edit Ruangan / Area -->
    <div id="modal-edit-ruangan" role="dialog" aria-modal="true" aria-labelledby="title-modal-edit-ruangan" class="hidden fixed inset-0 z-50 overflow-y-auto overscroll-contain bg-black/40 backdrop-blur-xs p-4 flex items-center justify-center">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full p-5 sm:p-6 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 id="title-modal-edit-ruangan" class="font-bold text-gray-900 text-sm">Edit Data Ruangan / Area</h3>
                <button type="button" onclick="closeModal('modal-edit-ruangan')" aria-label="Tutup modal" class="text-gray-400 hover:text-gray-700 font-bold p-1 rounded focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none cursor-pointer">✕</button>
            </div>
            <form id="form-edit-ruangan" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit-ruangan-lokasi-label" class="block font-semibold text-gray-500 mb-1">Lokasi Induk</label>
                    <input type="text" id="edit-ruangan-lokasi-label" disabled class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-xs text-gray-600 min-h-[40px]">
                </div>
                <div>
                    <label for="edit-ruangan-nama" class="block font-semibold text-gray-700 mb-1">Nama Ruangan / Area <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit-ruangan-nama" name="nama" required autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="edit-ruangan-kode" class="block font-semibold text-gray-700 mb-1">Kode Ruangan</label>
                        <input type="text" id="edit-ruangan-kode" name="kode" spellcheck="false" autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-mono text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    </div>
                    <div>
                        <label for="edit-ruangan-kategori" class="block font-semibold text-gray-700 mb-1">Kategori Area</label>
                        <input type="text" id="edit-ruangan-kategori" name="kategori" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                    </div>
                </div>
                <div>
                    <label for="edit-ruangan-keterangan" class="block font-semibold text-gray-700 mb-1">Keterangan</label>
                    <textarea id="edit-ruangan-keterangan" name="keterangan" rows="2" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                    <button type="button" onclick="confirmDeleteRuangan()" class="text-rose-600 hover:text-rose-800 font-semibold cursor-pointer focus-visible:ring-2 focus-visible:ring-rose-600 focus-visible:outline-none rounded px-1.5 py-0.5">Hapus Ruangan</button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeModal('modal-edit-ruangan')" class="px-3.5 py-2 rounded-lg border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-[#111827] text-white font-semibold hover:bg-gray-800 transition shadow-xs min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Perbarui</button>
                    </div>
                </div>
            </form>
            <form id="form-delete-ruangan" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    @endif

    <!-- Script Manajemen Interaksi Modal & Tab -->
    <script>
        let previousActiveElement = null;

        function openModal(id) {
            const el = document.getElementById(id);
            if (el) {
                previousActiveElement = document.activeElement;
                el.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setTimeout(() => {
                    const focusable = el.querySelector('input:not([type=hidden]):not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled])');
                    if (focusable) focusable.focus();
                }, 50);
            }
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                if (previousActiveElement && typeof previousActiveElement.focus === 'function') {
                    previousActiveElement.focus();
                }
            }
        }

        function switchViewTab(tabId) {
            const tabs = ['tab-hirarki', 'tab-gedung', 'tab-lantai', 'tab-ruangan'];
            tabs.forEach(t => {
                const btn = document.getElementById(`btn-${t}`);
                const content = document.getElementById(`content-${t}`);
                if (t === tabId) {
                    btn.className = 'px-3 py-1.5 rounded-md font-semibold text-xs transition bg-white text-gray-900 shadow-2xs border border-gray-200/60';
                    content.classList.remove('hidden');
                } else {
                    btn.className = 'px-3 py-1.5 rounded-md font-medium text-xs text-gray-600 hover:text-gray-900 transition';
                    content.classList.add('hidden');
                }
            });

            try {
                const url = new URL(window.location.href);
                const shortTab = tabId.replace('tab-', '');
                if (shortTab === 'hirarki') {
                    url.searchParams.delete('tab');
                } else {
                    url.searchParams.set('tab', shortTab);
                }
                window.history.replaceState({}, '', url.toString());
            } catch (e) {}
        }

        function openAddLantaiForGedung(gedungId, gedungNama) {
            const select = document.getElementById('tambah-lantai-gedung-id');
            if (select) select.value = gedungId;
            openModal('modal-tambah-lantai');
        }

        function openAddRuanganForLantai(lantaiId, label) {
            const select = document.getElementById('tambah-ruangan-lantai-id');
            if (select) select.value = lantaiId;
            openModal('modal-tambah-ruangan');
        }

        let activeGedungId = null;
        function openEditGedungModal(source) {
            const d = (source && source.dataset) ? source.dataset : (source || {});
            activeGedungId = d.id;
            document.getElementById('edit-gedung-nama').value = d.nama || '';
            document.getElementById('edit-gedung-kode').value = d.kode || '';
            document.getElementById('edit-gedung-status').value = d.status || 'aktif';
            document.getElementById('edit-gedung-keterangan').value = d.keterangan || '';
            document.getElementById('form-edit-gedung').action = `/lokasi/gedung/${d.id}`;
            document.getElementById('form-delete-gedung').action = `/lokasi/gedung/${d.id}`;
            openModal('modal-edit-gedung');
        }

        function confirmDeleteGedung() {
            window.hafisConfirm({
                title: 'Hapus Gedung Rumah Sakit',
                message: 'Apakah Anda yakin ingin menghapus gedung ini beserta seluruh data lantai dan ruangan di dalamnya? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.',
                confirmText: 'Hapus Gedung',
                cancelText: 'Batal',
                type: 'danger',
                onConfirm: function() {
                    document.getElementById('form-delete-gedung').submit();
                }
            });
        }

        let activeLantaiId = null;
        function openEditLantaiModal(source, fallbackGedungNama) {
            const d = (source && source.dataset) ? source.dataset : (source || {});
            activeLantaiId = d.id;
            document.getElementById('edit-lantai-gedung-label').value = d.gedungNama || fallbackGedungNama || '';
            document.getElementById('edit-lantai-nomor').value = d.nomor || d.nomor_lantai || '';
            document.getElementById('edit-lantai-nama').value = d.nama || '';
            document.getElementById('edit-lantai-keterangan').value = d.keterangan || '';
            document.getElementById('form-edit-lantai').action = `/lokasi/lantai/${d.id}`;
            document.getElementById('form-delete-lantai').action = `/lokasi/lantai/${d.id}`;
            openModal('modal-edit-lantai');
        }

        function confirmDeleteLantai() {
            window.hafisConfirm({
                title: 'Hapus Lantai Gedung',
                message: 'Apakah Anda yakin ingin menghapus lantai ini beserta seluruh data ruangan di dalamnya? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.',
                confirmText: 'Hapus Lantai',
                cancelText: 'Batal',
                type: 'danger',
                onConfirm: function() {
                    document.getElementById('form-delete-lantai').submit();
                }
            });
        }

        let activeRuanganId = null;
        function openEditRuanganModal(source, fallbackLokasiLabel) {
            const d = (source && source.dataset) ? source.dataset : (source || {});
            activeRuanganId = d.id;
            document.getElementById('edit-ruangan-lokasi-label').value = d.lokasiLabel || fallbackLokasiLabel || '';
            document.getElementById('edit-ruangan-nama').value = d.nama || '';
            document.getElementById('edit-ruangan-kode').value = d.kode || '';
            document.getElementById('edit-ruangan-kategori').value = d.kategori || '';
            document.getElementById('edit-ruangan-keterangan').value = d.keterangan || '';
            document.getElementById('form-edit-ruangan').action = `/lokasi/ruangan/${d.id}`;
            document.getElementById('form-delete-ruangan').action = `/lokasi/ruangan/${d.id}`;
            openModal('modal-edit-ruangan');
        }

        function confirmDeleteRuangan() {
            window.hafisConfirm({
                title: 'Hapus Ruangan / Area',
                message: 'Apakah Anda yakin ingin menghapus ruangan/area ini? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.',
                confirmText: 'Hapus Ruangan',
                cancelText: 'Batal',
                type: 'danger',
                onConfirm: function() {
                    document.getElementById('form-delete-ruangan').submit();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['modal-tambah-gedung', 'modal-edit-gedung', 'modal-tambah-lantai', 'modal-edit-lantai', 'modal-tambah-ruangan', 'modal-edit-ruangan'].forEach(closeModal);
            }
        });
    </script>
</x-layouts.app>
