<x-layouts.app title="HAFIS : Detail Gedung {{ $gedung->nama }}">
    <!-- 1. Header Halaman -->
    <x-page-header
        title="{{ $gedung->nama }}"
        description="Detail struktur lantai, denah, dan ruangan/area fasilitas gedung">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ route('lokasi.index') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Struktur Lokasi</span>
            </x-button>
            <x-button variant="primary" href="{{ route('penempatan.index', ['gedung_id' => $gedung->id]) }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Buka Titik APAR</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50/80 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">✓</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs shadow-2xs">
            <div class="font-bold mb-1 flex items-center gap-1.5 text-rose-950">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Terdapat kesalahan:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-800 pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Informasi Ringkas Gedung -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-6 shadow-xs text-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <span class="text-slate-400 font-mono text-[11px] block">{{ $gedung->kode }}</span>
                <h2 class="text-base font-bold text-slate-900">{{ $gedung->nama }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <x-badge :variant="$gedung->status === 'aktif' ? 'success' : 'default'">
                    {{ ucfirst($gedung->status) }}
                </x-badge>
                <span class="text-slate-300">•</span>
                <span class="font-semibold text-slate-700">{{ $gedung->lantais->count() }} Lantai</span>
                <span class="text-slate-300">•</span>
                <span class="font-semibold text-slate-700">{{ $gedung->ruangans->count() }} Ruangan/Area</span>
            </div>
        </div>
        @if($gedung->keterangan)
            <p class="text-slate-600 mt-3 text-xs leading-relaxed">{{ $gedung->keterangan }}</p>
        @endif
    </div>

    <!-- 2. Section Denah Lantai (Alur: Pilih Gedung -> Pilih Lantai -> Tampilkan Denah) -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-6 shadow-xs text-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Denah Lantai {{ $gedung->nama }}</h3>
                <p class="text-slate-500 text-[11px]">Pilih lantai untuk melihat gambar denah atau mengunggah denah baru.</p>
            </div>
        </div>

        @if($gedung->lantais->count() > 0)
            <!-- Selector Tab Lantai: [ Lantai 1 ] [ Lantai 2 ] ... -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-4 scrollbar-thin">
                @foreach($gedung->lantais->sortBy('nomor_lantai') as $idx => $l)
                    <button
                        type="button"
                        onclick="selectFloorTab({{ $l->id }})"
                        id="tab-btn-lantai-{{ $l->id }}"
                        class="floor-tab-btn flex items-center gap-2 px-3.5 py-2 rounded-lg font-semibold text-xs transition min-h-[44px] {{ $loop->first ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                    >
                        <span class="font-mono text-[11px]">L{{ $l->nomor_lantai }}</span>
                        <span>{{ $l->nama }}</span>
                        @if($l->denah)
                            <span class="w-2 h-2 rounded-full bg-emerald-400" title="Denah tersedia"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-slate-300" title="Denah belum tersedia"></span>
                        @endif
                    </button>
                @endforeach
            </div>

            <!-- Konten Denah untuk Setiap Lantai -->
            @foreach($gedung->lantais as $l)
                <div id="floor-content-{{ $l->id }}" class="floor-panel {{ $loop->first ? '' : 'hidden' }}">
                    @if($l->denah)
                        <div class="space-y-4">
                            <!-- Baris Aksi & Metadata Denah -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Denah Tersedia
                                    </span>
                                    <span class="text-slate-400">•</span>
                                    <span class="font-mono text-slate-600 text-[11px]">{{ $l->denah->nama_file_asli ?: basename($l->denah->file_path) }}</span>
                                    @if($l->denah->file_size)
                                        <span class="text-slate-400">•</span>
                                        <span class="text-slate-500 text-[11px]">{{ number_format($l->denah->file_size / 1024, 1) }} KB</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <a
                                        href="{{ route('penempatan.index', ['gedung_id' => $gedung->id, 'lantai_id' => $l->id]) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition duration-150 ease-in-out min-h-[36px] focus:outline-none focus:ring-2 focus:ring-slate-900/20"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>Buka Titik APAR</span>
                                    </a>
                                    <button
                                        type="button"
                                        onclick="openUploadDenahModal({{ $l->id }}, '{{ $l->nama }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition min-h-[36px]"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        <span>Ganti Denah</span>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="confirmDeleteDenah({{ $l->id }}, '{{ $l->nama }}')"
                                        class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50 font-medium text-xs transition min-h-[36px]"
                                        title="Hapus Denah"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>

                            @if($l->denah->keterangan)
                                <p class="text-slate-600 text-xs italic px-1">{{ $l->denah->keterangan }}</p>
                            @endif

                            <!-- Display Gambar Denah -->
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 sm:p-4 text-center overflow-hidden">
                                <img
                                    src="{{ $l->denah->url }}"
                                    alt="Denah {{ $l->nama }}"
                                    class="max-h-[460px] mx-auto object-contain rounded-lg border border-slate-200 shadow-xs bg-white"
                                    loading="lazy"
                                />
                            </div>
                        </div>
                    @else
                        <!-- State: Denah Belum Tersedia (Section 12) -->
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/70 p-8 text-center space-y-3">
                            <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Denah belum tersedia</h4>
                                <p class="text-slate-500 text-xs mt-1 max-w-md mx-auto">
                                    Denah untuk {{ $l->nama }} belum tersedia. Silakan upload denah resmi untuk menambahkan titik APAR.
                                </p>
                            </div>
                            <div class="pt-2">
                                <button
                                    type="button"
                                    onclick="openUploadDenahModal({{ $l->id }}, '{{ $l->nama }}')"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition min-h-[44px]"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    <span>Upload Denah {{ $l->nama }}</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="p-6 text-center text-slate-400 italic">
                Belum ada lantai yang terdaftar pada gedung ini.
            </div>
        @endif
    </div>

    <!-- 3. Daftar Lantai & Ruangan di Gedung Ini -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Lantai &amp; Ruangan</h3>
            <button 
                type="button" 
                onclick="document.getElementById('form-add-lantai-box').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition min-h-[36px]"
            >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Lantai</span>
            </button>
        </div>

        <!-- Box Form Tambah Lantai Baru Cepat (Hidden by default) -->
        <div id="form-add-lantai-box" class="hidden bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs">
            <h4 class="font-bold text-slate-900 mb-2">Tambah Lantai untuk {{ $gedung->nama }}</h4>
            <form action="{{ route('lokasi.lantai.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                @csrf
                <input type="hidden" name="gedung_id" value="{{ $gedung->id }}">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Lantai</label>
                    <input type="number" name="nomor_lantai" value="{{ ($gedung->lantais->max('nomor_lantai') ?? 0) + 1 }}" required class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Label Nama Lantai</label>
                    <input type="text" name="nama" value="Lantai {{ ($gedung->lantais->max('nomor_lantai') ?? 0) + 1 }}" required class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800 transition min-h-[36px]">Simpan</button>
                    <button type="button" onclick="document.getElementById('form-add-lantai-box').classList.add('hidden')" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-white min-h-[36px]">Batal</button>
                </div>
            </form>
        </div>

        @forelse($gedung->lantais->sortBy('nomor_lantai') as $l)
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs text-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2.5 border-b border-slate-100 mb-3 gap-2">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-slate-900 text-white font-mono font-bold text-xs flex items-center justify-center">
                            L{{ $l->nomor_lantai }}
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <strong class="font-bold text-slate-900 text-sm">{{ $l->nama }}</strong>
                                @if($l->denah)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Denah Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        Denah Belum Ada
                                    </span>
                                @endif
                            </div>
                            @if($l->keterangan)
                                <p class="text-[11px] text-slate-500 italic">{{ $l->keterangan }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-slate-500 font-semibold text-xs mr-1">{{ $l->ruangans->count() }} Ruangan / Area</span>
                        @if($l->denah)
                            <a
                                href="{{ route('penempatan.index', ['gedung_id' => $gedung->id, 'lantai_id' => $l->id]) }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold text-[11px] hover:bg-emerald-100"
                            >
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                <span>Titik APAR</span>
                            </a>
                        @else
                            <button
                                type="button"
                                onclick="openUploadDenahModal({{ $l->id }}, '{{ $l->nama }}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-semibold text-[11px] hover:bg-slate-200"
                            >
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span>Upload Denah</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Ruangan Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    @forelse($l->ruangans as $r)
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <strong class="font-bold text-slate-900">{{ $r->nama }}</strong>
                                    @if($r->kode)
                                        <span class="font-mono text-[10px] bg-slate-200 px-1 py-0.5 rounded text-slate-700">{{ $r->kode }}</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1">
                                    {{ $r->kategori ?: 'Area Umum' }}
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500">{{ $r->apars->count() }} APAR terdata</span>
                                @if($r->apars->count() > 0)
                                    <span class="font-mono text-slate-600 font-semibold">
                                        {{ $r->apars->whereNotNull('koordinat_x')->count() }}/{{ $r->apars->count() }} titik denah
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-slate-400 py-2 italic">
                            Belum ada ruangan/area terdaftar pada lantai ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-200 rounded-xl p-6 text-center text-slate-400 text-xs">
                Belum ada lantai yang terdaftar di gedung ini.
            </div>
        @endforelse
    </div>

    <!-- Modal Upload Denah Lantai -->
    <div id="modal-upload-denah" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 p-4 flex items-center justify-center">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Upload Denah Lantai</h3>
                    <p id="modal-denah-subtitle" class="text-slate-500 text-[11px] mt-0.5"></p>
                </div>
                <button type="button" onclick="closeUploadDenahModal()" class="text-slate-400 hover:text-slate-700 min-h-[36px] min-w-[36px] flex items-center justify-center">✕</button>
            </div>
            <form id="form-upload-denah" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih File Gambar Denah <span class="text-rose-500">*</span></label>
                    <input 
                        type="file" 
                        name="gambar" 
                        required 
                        accept="image/jpeg,image/png,image/webp,image/svg+xml"
                        class="w-full rounded-lg border border-slate-300 p-2 text-xs bg-slate-50 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800"
                    >
                    <p class="text-[11px] text-slate-400 mt-1">Format didukung: JPG, PNG, WebP, SVG. Ukuran maksimal 5MB.</p>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keterangan / Catatan Denah (Opsional)</label>
                    <input 
                        type="text" 
                        name="keterangan" 
                        placeholder="Contoh: Denah revisi arsitektur 2026" 
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs"
                    >
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeUploadDenahModal()" class="px-3.5 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 min-h-[44px]">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800 min-h-[44px]">Upload Denah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Hapus Denah (Hidden) -->
    <form id="form-delete-denah" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function selectFloorTab(lantaiId) {
            document.querySelectorAll('.floor-tab-btn').forEach(btn => {
                btn.classList.remove('bg-slate-900', 'text-white', 'shadow-xs');
                btn.classList.add('bg-slate-100', 'text-slate-700');
            });
            const activeBtn = document.getElementById('tab-btn-lantai-' + lantaiId);
            if (activeBtn) {
                activeBtn.classList.add('bg-slate-900', 'text-white', 'shadow-xs');
                activeBtn.classList.remove('bg-slate-100', 'text-slate-700');
            }

            document.querySelectorAll('.floor-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            const activePanel = document.getElementById('floor-content-' + lantaiId);
            if (activePanel) {
                activePanel.classList.remove('hidden');
            }
        }

        function openUploadDenahModal(lantaiId, lantaiNama) {
            const form = document.getElementById('form-upload-denah');
            form.action = '/lokasi/lantai/' + lantaiId + '/denah';
            document.getElementById('modal-denah-subtitle').textContent = '{{ $gedung->nama }} - ' + lantaiNama;
            document.getElementById('modal-upload-denah').classList.remove('hidden');
        }

        function closeUploadDenahModal() {
            document.getElementById('modal-upload-denah').classList.add('hidden');
        }

        function confirmDeleteDenah(lantaiId, lantaiNama) {
            if (confirm('Apakah Anda yakin ingin menghapus denah ' + lantaiNama + '? Marker titik APAR yang ada akan tetap tersimpan datanya namun tidak terlihat hingga denah baru diupload.')) {
                const form = document.getElementById('form-delete-denah');
                form.action = '/lokasi/lantai/' + lantaiId + '/denah';
                form.submit();
            }
        }
    </script>
</x-layouts.app>

