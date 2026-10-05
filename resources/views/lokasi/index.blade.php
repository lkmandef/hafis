<x-layouts.app title="HAFIS : Struktur Lokasi Rumah Sakit">
    <!-- 1. Page Header -->
    <x-page-header
        title="Struktur Lokasi Rumah Sakit"
        description="Manajemen hierarki Gedung, Lantai, dan Ruangan atau Area fasilitas rumah sakit">
        <x-slot:actions>
            <button 
                type="button" 
                onclick="openModal('modal-tambah-gedung')"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-xs"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Gedung</span>
            </button>
            <button 
                type="button" 
                onclick="openModal('modal-tambah-lantai')"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition shadow-xs"
            >
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Lantai</span>
            </button>
            <button 
                type="button" 
                onclick="openModal('modal-tambah-ruangan')"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition shadow-xs"
            >
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Ruangan</span>
            </button>
        </x-slot:actions>
    </x-page-header>

    <!-- Feedback Notifikasi Sukses & Error -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50/80 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs shadow-2xs">
            <div class="font-bold mb-1 flex items-center gap-1.5 text-rose-950">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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

    <!-- Catatan Status Data Konsultasi K3RS -->
    <div class="mb-6 p-4 rounded-xl border border-amber-200 bg-amber-50/70 text-amber-950 text-xs flex items-start gap-3">
        <span class="p-1 rounded-md bg-amber-100 text-amber-700 shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </span>
        <div class="leading-relaxed">
            <strong class="font-semibold block text-amber-950 mb-0.5">Status Data Awal (Draft Konsultasi K3RS)</strong>
            <span>Data gedung dan lantai berikut merupakan hasil konsultasi pendahuluan dan masih menunggu verifikasi lapangan final K3RS. Entitas seperti <em>Poliklinis</em>, <em>Manajemen</em>, <em>Radiologi</em>, dan <em>Laboratorium</em> telah diposisikan secara fleksibel sebagai Ruangan/Area agar dapat diperbarui sewaktu-waktu tanpa merusak struktur relasi data.</span>
        </div>
    </div>

    <!-- 2. Ringkasan Metrik Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-xs">
            <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Total Gedung</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-slate-900">{{ $totalGedung }}</span>
                <span class="text-xs text-slate-500 font-medium">Bangunan terdaftar</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-xs">
            <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Total Lantai</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-slate-900">{{ $totalLantai }}</span>
                <span class="text-xs text-slate-500 font-medium">Tingkat lantai fisik</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-xs">
            <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Total Ruangan / Area</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-slate-900">{{ $totalRuangan }}</span>
                <span class="text-xs text-slate-500 font-medium">Titik zona terdata</span>
            </div>
        </div>
    </div>

    <!-- 3. Toolbar Pencarian & Filter Tab -->
    <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 mb-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
        
        <!-- Tab Navigasi View -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg">
            <button 
                type="button" 
                onclick="switchViewTab('tab-hirarki')" 
                id="btn-tab-hirarki"
                class="px-3 py-1.5 rounded-md font-semibold text-xs transition bg-white text-slate-900 shadow-2xs"
            >
                Hierarki Lokasi
            </button>
            <button 
                type="button" 
                onclick="switchViewTab('tab-gedung')" 
                id="btn-tab-gedung"
                class="px-3 py-1.5 rounded-md font-medium text-xs text-slate-600 hover:text-slate-900 transition"
            >
                Gedung ({{ $totalGedung }})
            </button>
            <button 
                type="button" 
                onclick="switchViewTab('tab-lantai')" 
                id="btn-tab-lantai"
                class="px-3 py-1.5 rounded-md font-medium text-xs text-slate-600 hover:text-slate-900 transition"
            >
                Lantai ({{ $totalLantai }})
            </button>
            <button 
                type="button" 
                onclick="switchViewTab('tab-ruangan')" 
                id="btn-tab-ruangan"
                class="px-3 py-1.5 rounded-md font-medium text-xs text-slate-600 hover:text-slate-900 transition"
            >
                Ruangan ({{ $totalRuangan }})
            </button>
        </div>

        <!-- Form Pencarian -->
        <form method="GET" action="{{ route('lokasi.index') }}" class="flex items-center gap-2">
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search }}" 
                    placeholder="Cari gedung, lantai, ruangan..." 
                    class="w-full pl-9 pr-3 py-1.5 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:outline-none transition min-h-[36px]"
                />
            </div>
            @if($search)
                <a href="{{ route('lokasi.index') }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium">
                    Reset
                </a>
            @endif
        </form>

    </div>

    <!-- 4. Konten Tampilan (TAB 1: HIERARKI LOKASI TREE VIEW) -->
    <div id="content-tab-hirarki" class="space-y-4">
        @forelse($gedungs as $g)
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                <!-- Header Gedung -->
                <div class="p-4 bg-slate-50/80 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start sm:items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-200/80 text-slate-800 font-bold font-mono text-xs flex items-center justify-center shrink-0">
                            {{ substr($g->kode, 4) }}
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('lokasi.gedung.show', $g->id) }}" class="font-bold text-slate-900 text-sm tracking-tight hover:underline flex items-center gap-1.5" title="Buka Detail & Denah Gedung">
                                    <span>{{ $g->nama }}</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                                <span class="font-mono text-[11px] text-slate-500 bg-white border border-slate-200 px-1.5 py-0.5 rounded">{{ $g->kode }}</span>
                                <x-badge :variant="$g->status === 'aktif' ? 'success' : 'default'">
                                    {{ ucfirst($g->status) }}
                                </x-badge>
                            </div>
                            @if($g->keterangan)
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $g->keterangan }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                        <span class="text-xs text-slate-500 mr-1">
                            <strong class="text-slate-800">{{ $g->lantais_count }}</strong> Lantai · 
                            <strong class="text-slate-800">{{ $g->ruangans_count }}</strong> Ruangan
                        </span>

                        <a 
                            href="{{ route('lokasi.gedung.show', $g->id) }}" 
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition"
                            title="Lihat Detail & Denah Gedung"
                        >
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            <span>Denah &amp; Detail</span>
                        </a>

                        <button 
                            type="button" 
                            onclick="openEditGedungModal({{ json_encode($g) }})"
                            class="p-1.5 text-slate-500 hover:text-slate-800 rounded-md hover:bg-slate-200/60 transition"
                            title="Edit Gedung"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>

                        <button 
                            type="button" 
                            onclick="openAddLantaiForGedung({{ $g->id }}, '{{ $g->nama }}')"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition"
                            title="Tambah Lantai untuk Gedung ini"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Lantai</span>
                        </button>
                    </div>
                </div>

                <!-- Daftar Lantai di Gedung Ini -->
                <div class="p-4 space-y-3">
                    @forelse($g->lantais as $l)
                        <div class="rounded-lg border border-slate-200/90 bg-slate-50/50 p-3 text-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2 border-b border-slate-200/60 mb-2.5 gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="w-6 h-6 rounded-md bg-slate-800 text-white font-mono font-bold text-[11px] flex items-center justify-center shrink-0">
                                        L{{ $l->nomor_lantai }}
                                    </span>
                                    <strong class="text-slate-900 font-semibold">{{ $l->nama }}</strong>
                                    @if($l->keterangan)
                                        <span class="text-slate-400">•</span>
                                        <span class="text-[11px] text-slate-500 italic">{{ $l->keterangan }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($l->denah)
                                        <a 
                                            href="{{ route('penempatan.index', ['gedung_id' => $g->id, 'lantai_id' => $l->id]) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-md border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition"
                                            title="Buka Denah Lantai {{ $l->nama }}"
                                        >
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                            </svg>
                                            <span>Denah Lantai</span>
                                        </a>
                                    @else
                                        <span 
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium rounded border border-slate-200 bg-slate-100/80 text-slate-400 select-none"
                                            title="Denah lantai belum tersedia"
                                        >
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                            </svg>
                                            <span>Denah belum tersedia</span>
                                        </span>
                                    @endif
                                    <button 
                                        type="button" 
                                        onclick="openAddRuanganForLantai({{ $l->id }}, '{{ $g->nama }} - {{ $l->nama }}')"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition"
                                    >
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        <span>Ruangan/Area</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        onclick="openEditLantaiModal({{ json_encode($l) }}, '{{ $g->nama }}')"
                                        class="p-1 text-slate-400 hover:text-slate-700 rounded hover:bg-slate-200/60"
                                        title="Edit Lantai"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Daftar Ruangan / Area di Lantai Ini -->
                            <div class="pl-3 sm:pl-4">
                                @if($l->ruangans->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                        @foreach($l->ruangans as $r)
                                            <div class="p-2.5 rounded-lg border border-slate-200 bg-white flex items-center justify-between gap-2 shadow-2xs group">
                                                <div class="min-w-0">
                                                    <div class="font-semibold text-slate-900 text-xs truncate">{{ $r->nama }}</div>
                                                    <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                                        @if($r->kode)
                                                            <span class="font-mono bg-slate-100 px-1 rounded">{{ $r->kode }}</span>
                                                        @endif
                                                        @if($r->kategori)
                                                            <span class="truncate">{{ $r->kategori }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100">
                                                    <button 
                                                        type="button" 
                                                        onclick="openEditRuanganModal({{ json_encode($r) }}, '{{ $g->nama }} - {{ $l->nama }}')"
                                                        class="p-1 text-slate-400 hover:text-slate-800 rounded hover:bg-slate-100"
                                                        title="Edit Ruangan"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400 italic py-1">
                                        Belum ada ruangan/area terdaftar pada lantai ini.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs italic">
                            Belum ada konfigurasi lantai untuk gedung ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500 text-xs">
                Tidak ada data gedung yang cocok dengan pencarian "{{ $search }}".
            </div>
        @endforelse
    </div>

    <!-- TAB 2: TABEL SEMUA GEDUNG -->
    <div id="content-tab-gedung" class="hidden bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama Gedung</th>
                        <th class="px-4 py-3 text-center">Jumlah Lantai</th>
                        <th class="px-4 py-3 text-center">Jumlah Ruangan</th>
                        <th class="px-4 py-3">Keterangan / Catatan K3RS</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($allGedungs as $g)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $g->kode }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $g->nama }}</td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800">{{ $g->lantais->count() }}</td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800">{{ $g->ruangans->count() }}</td>
                            <td class="px-4 py-3 text-slate-500 max-w-xs truncate">{{ $g->keterangan ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <x-badge :variant="$g->status === 'aktif' ? 'success' : 'default'">
                                    {{ ucfirst($g->status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <button 
                                    type="button" 
                                    onclick="openEditGedungModal({{ json_encode($g) }})"
                                    class="px-2 py-1 rounded border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-[11px]"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: TABEL SEMUA LANTAI -->
    <div id="content-tab-lantai" class="hidden bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Gedung Induk</th>
                        <th class="px-4 py-3 text-center">Nomor Lantai</th>
                        <th class="px-4 py-3">Label Lantai</th>
                        <th class="px-4 py-3 text-center">Jumlah Ruangan</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($allLantais as $l)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $l->gedung->nama }}</td>
                            <td class="px-4 py-3 text-center font-mono font-bold text-slate-800">{{ $l->nomor_lantai }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $l->nama }}</td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800">{{ $l->ruangans->count() }}</td>
                            <td class="px-4 py-3 text-slate-500 max-w-xs truncate">{{ $l->keterangan ?: '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    @if($l->denah)
                                        <a 
                                            href="{{ route('penempatan.index', ['gedung_id' => $l->gedung_id, 'lantai_id' => $l->id]) }}"
                                            class="px-2 py-1 rounded border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-[11px] inline-flex items-center gap-1 transition"
                                            title="Buka Denah Lantai"
                                        >
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                            </svg>
                                            <span>Denah Lantai</span>
                                        </a>
                                    @else
                                        <span class="px-2 py-1 rounded border border-slate-200 bg-slate-50 text-slate-400 font-medium text-[11px] inline-flex items-center gap-1">
                                            <span>Belum ada denah</span>
                                        </span>
                                    @endif
                                    <button 
                                        type="button" 
                                        onclick="openEditLantaiModal({{ json_encode($l) }}, '{{ $l->gedung->nama }}')"
                                        class="px-2 py-1 rounded border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-[11px]"
                                    >
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 4: TABEL SEMUA RUANGAN / AREA -->
    <div id="content-tab-ruangan" class="hidden bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama Ruangan / Area</th>
                        <th class="px-4 py-3">Gedung &amp; Lantai</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @php
                    $allRuangans = \App\Models\Ruangan::with(['lantai.gedung'])->orderBy('nama')->get();
                    @endphp
                    @forelse($allRuangans as $r)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 font-mono text-slate-600">{{ $r->kode ?: '-' }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $r->nama }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $r->lantai->gedung->nama }} - {{ $r->lantai->nama }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-medium">
                                    {{ $r->kategori ?: 'Umum' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 max-w-xs truncate">{{ $r->keterangan ?: '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <button 
                                    type="button" 
                                    onclick="openEditRuanganModal({{ json_encode($r) }}, '{{ $r->lantai->gedung->nama }} - {{ $r->lantai->nama }}')"
                                    class="px-2 py-1 rounded border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-[11px]"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">
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

    <!-- Modal 1: Tambah Gedung -->
    <div id="modal-tambah-gedung" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-slate-900 text-sm">Tambah Gedung Baru</h3>
                <button type="button" onclick="closeModal('modal-tambah-gedung')" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <form action="{{ route('lokasi.gedung.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Gedung <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Gedung Paviliun Melati" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kode Gedung (Opsional)</label>
                    <input type="text" name="kode" placeholder="Contoh: GDG-PVL (otomatis jika kosong)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keterangan / Catatan</label>
                    <textarea name="keterangan" rows="2" placeholder="Catatan fungsi operasional atau peruntukan gedung..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-tambah-gedung')" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Simpan Gedung</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Edit Gedung -->
    <div id="modal-edit-gedung" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-slate-900 text-sm">Edit Data Gedung</h3>
                <button type="button" onclick="closeModal('modal-edit-gedung')" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <form id="form-edit-gedung" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Gedung <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit-gedung-nama" name="nama" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kode Gedung <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit-gedung-kode" name="kode" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Operasional</label>
                    <select id="edit-gedung-status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif / Renovasi</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keterangan / Catatan</label>
                    <textarea id="edit-gedung-keterangan" name="keterangan" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" onclick="confirmDeleteGedung()" class="text-rose-600 hover:text-rose-800 font-semibold">Hapus Gedung</button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeModal('modal-edit-gedung')" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Perbarui</button>
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
    <div id="modal-tambah-lantai" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-slate-900 text-sm">Tambah Lantai Baru</h3>
                <button type="button" onclick="closeModal('modal-tambah-lantai')" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <form action="{{ route('lokasi.lantai.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Gedung Induk <span class="text-rose-500">*</span></label>
                    <select id="tambah-lantai-gedung-id" name="gedung_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 cursor-pointer">
                        <option value="">-- Pilih Gedung --</option>
                        @foreach($allGedungs as $g)
                            <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Lantai <span class="text-rose-500">*</span></label>
                        <input type="number" name="nomor_lantai" required value="1" min="-3" max="50" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Label Nama <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required value="Lantai 1" placeholder="Contoh: Lantai 1 / Basement" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="2" placeholder="Catatan penunjang lantai..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-tambah-lantai')" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Simpan Lantai</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 4: Edit Lantai -->
    <div id="modal-edit-lantai" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-slate-900 text-sm">Edit Data Lantai</h3>
                <button type="button" onclick="closeModal('modal-edit-lantai')" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <form id="form-edit-lantai" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Gedung Induk</label>
                    <input type="text" id="edit-lantai-gedung-label" disabled class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-xs text-slate-600">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Lantai <span class="text-rose-500">*</span></label>
                        <input type="number" id="edit-lantai-nomor" name="nomor_lantai" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Label Nama <span class="text-rose-500">*</span></label>
                        <input type="text" id="edit-lantai-nama" name="nama" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keterangan</label>
                    <textarea id="edit-lantai-keterangan" name="keterangan" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" onclick="confirmDeleteLantai()" class="text-rose-600 hover:text-rose-800 font-semibold">Hapus Lantai</button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeModal('modal-edit-lantai')" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Perbarui</button>
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
    <div id="modal-tambah-ruangan" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-slate-900 text-sm">Tambah Ruangan / Area Baru</h3>
                <button type="button" onclick="closeModal('modal-tambah-ruangan')" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <form action="{{ route('lokasi.ruangan.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Lantai Induk <span class="text-rose-500">*</span></label>
                    <select id="tambah-ruangan-lantai-id" name="lantai_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 cursor-pointer">
                        <option value="">-- Pilih Lantai --</option>
                        @foreach($allLantais as $l)
                            <option value="{{ $l->id }}">{{ $l->gedung->nama }} - {{ $l->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Ruangan / Area <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Poli Mata / Ruang Tindakan Bedah" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kode Ruangan</label>
                        <input type="text" name="kode" placeholder="Contoh: RNG-101" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kategori Area</label>
                        <input type="text" name="kategori" placeholder="Contoh: Pelayanan / Rawat Inap" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2" placeholder="Catatan posisi spesifik atau penanggung jawab..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-tambah-ruangan')" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Simpan Ruangan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 6: Edit Ruangan / Area -->
    <div id="modal-edit-ruangan" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-slate-900 text-sm">Edit Data Ruangan / Area</h3>
                <button type="button" onclick="closeModal('modal-edit-ruangan')" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <form id="form-edit-ruangan" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Lokasi Induk</label>
                    <input type="text" id="edit-ruangan-lokasi-label" disabled class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-xs text-slate-600">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Ruangan / Area <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit-ruangan-nama" name="nama" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kode Ruangan</label>
                        <input type="text" id="edit-ruangan-kode" name="kode" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kategori Area</label>
                        <input type="text" id="edit-ruangan-kategori" name="kategori" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keterangan</label>
                    <textarea id="edit-ruangan-keterangan" name="keterangan" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" onclick="confirmDeleteRuangan()" class="text-rose-600 hover:text-rose-800 font-semibold">Hapus Ruangan</button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeModal('modal-edit-ruangan')" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">Perbarui</button>
                    </div>
                </div>
            </form>
            <form id="form-delete-ruangan" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <!-- Script Manajemen Interaksi Modal & Tab -->
    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('hidden');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        }

        function switchViewTab(tabId) {
            const tabs = ['tab-hirarki', 'tab-gedung', 'tab-lantai', 'tab-ruangan'];
            tabs.forEach(t => {
                const btn = document.getElementById(`btn-${t}`);
                const content = document.getElementById(`content-${t}`);
                if (t === tabId) {
                    btn.className = 'px-3 py-1.5 rounded-md font-semibold text-xs transition bg-white text-slate-900 shadow-2xs';
                    content.classList.remove('hidden');
                } else {
                    btn.className = 'px-3 py-1.5 rounded-md font-medium text-xs text-slate-600 hover:text-slate-900 transition';
                    content.classList.add('hidden');
                }
            });
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
        function openEditGedungModal(gedung) {
            activeGedungId = gedung.id;
            document.getElementById('edit-gedung-nama').value = gedung.nama;
            document.getElementById('edit-gedung-kode').value = gedung.kode;
            document.getElementById('edit-gedung-status').value = gedung.status || 'aktif';
            document.getElementById('edit-gedung-keterangan').value = gedung.keterangan || '';
            document.getElementById('form-edit-gedung').action = `/lokasi/gedung/${gedung.id}`;
            document.getElementById('form-delete-gedung').action = `/lokasi/gedung/${gedung.id}`;
            openModal('modal-edit-gedung');
        }

        function confirmDeleteGedung() {
            if (confirm('Apakah Anda yakin ingin menghapus gedung ini beserta seluruh data lantai dan ruangan di dalamnya? Tindakan ini tidak dapat dibatalkan.')) {
                document.getElementById('form-delete-gedung').submit();
            }
        }

        let activeLantaiId = null;
        function openEditLantaiModal(lantai, gedungNama) {
            activeLantaiId = lantai.id;
            document.getElementById('edit-lantai-gedung-label').value = gedungNama;
            document.getElementById('edit-lantai-nomor').value = lantai.nomor_lantai;
            document.getElementById('edit-lantai-nama').value = lantai.nama;
            document.getElementById('edit-lantai-keterangan').value = lantai.keterangan || '';
            document.getElementById('form-edit-lantai').action = `/lokasi/lantai/${lantai.id}`;
            document.getElementById('form-delete-lantai').action = `/lokasi/lantai/${lantai.id}`;
            openModal('modal-edit-lantai');
        }

        function confirmDeleteLantai() {
            if (confirm('Apakah Anda yakin ingin menghapus lantai ini beserta seluruh data ruangan di dalamnya? Tindakan ini tidak dapat dibatalkan.')) {
                document.getElementById('form-delete-lantai').submit();
            }
        }

        let activeRuanganId = null;
        function openEditRuanganModal(ruangan, lokasiLabel) {
            activeRuanganId = ruangan.id;
            document.getElementById('edit-ruangan-lokasi-label').value = lokasiLabel;
            document.getElementById('edit-ruangan-nama').value = ruangan.nama;
            document.getElementById('edit-ruangan-kode').value = ruangan.kode || '';
            document.getElementById('edit-ruangan-kategori').value = ruangan.kategori || '';
            document.getElementById('edit-ruangan-keterangan').value = ruangan.keterangan || '';
            document.getElementById('form-edit-ruangan').action = `/lokasi/ruangan/${ruangan.id}`;
            document.getElementById('form-delete-ruangan').action = `/lokasi/ruangan/${ruangan.id}`;
            openModal('modal-edit-ruangan');
        }

        function confirmDeleteRuangan() {
            if (confirm('Apakah Anda yakin ingin menghapus ruangan/area ini? Tindakan ini tidak dapat dibatalkan.')) {
                document.getElementById('form-delete-ruangan').submit();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['modal-tambah-gedung', 'modal-edit-gedung', 'modal-tambah-lantai', 'modal-edit-lantai', 'modal-tambah-ruangan', 'modal-edit-ruangan'].forEach(closeModal);
            }
        });
    </script>
</x-layouts.app>
