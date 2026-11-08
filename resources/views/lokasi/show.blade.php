<x-layouts.app title="HAFIS : Detail Gedung {{ $gedung->nama }}">
    <!-- 1. Header Halaman -->
    <x-page-header
        title="{{ $gedung->nama }}"
        description="Detail struktur lantai dan ruangan/area fasilitas gedung">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ route('lokasi.index') }}">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Struktur Lokasi</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div role="status" aria-live="polite" class="mb-5 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">✓</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1 rounded focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:outline-none" aria-label="Tutup notifikasi">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div role="alert" aria-live="assertive" class="mb-5 p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs shadow-2xs">
            <div class="font-semibold mb-1 flex items-center gap-1.5 text-rose-950">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
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
    <div class="bg-white border border-gray-200/90 rounded-2xl p-5 mb-6 shadow-xs text-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100">
            <div>
                <span class="text-gray-400 font-mono text-[11px] block tabular-nums">{{ $gedung->kode }}</span>
                <h2 class="text-base font-bold text-gray-900">{{ $gedung->nama }}</h2>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <x-badge :variant="$gedung->status === 'aktif' ? 'success' : 'default'">
                    {{ ucfirst($gedung->status) }}
                </x-badge>
                <span class="text-gray-300">•</span>
                <span class="font-semibold font-mono text-gray-700 tabular-nums">{{ $gedung->lantais->count() }} Lantai</span>
                <span class="text-gray-300">•</span>
                <span class="font-semibold font-mono text-gray-700 tabular-nums">{{ $gedung->lokasis->count() }} Titik Lokasi APAR</span>
                @if($gedung->ruangans->count() > 0)
                    <span class="text-gray-300">•</span>
                    <span class="font-semibold font-mono text-gray-700 tabular-nums">{{ $gedung->ruangans->count() }} Ruangan Fisik</span>
                @endif
            </div>
        </div>
        @if($gedung->keterangan)
            <p class="text-gray-600 mt-3 text-xs leading-relaxed">{{ $gedung->keterangan }}</p>
        @endif
    </div>

    <!-- 2. Daftar Lantai & Ruangan di Gedung Ini -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-sm">Daftar Lantai &amp; Ruangan</h3>
            <button 
                type="button" 
                onclick="document.getElementById('form-add-lantai-box').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#111827] text-white font-semibold text-xs hover:bg-gray-800 active:scale-[0.98] transition min-h-[36px] shadow-xs cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none"
            >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Lantai</span>
            </button>
        </div>

        <!-- Box Form Tambah Lantai Baru Cepat (Hidden by default) -->
        <div id="form-add-lantai-box" class="hidden bg-gray-50 border border-gray-200 rounded-xl p-4 text-xs">
            <h4 class="font-bold text-gray-900 mb-2">Tambah Lantai untuk {{ $gedung->nama }}</h4>
            <form action="{{ route('lokasi.lantai.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                @csrf
                <input type="hidden" name="gedung_id" value="{{ $gedung->id }}">
                <div>
                    <label for="quick-lantai-nomor" class="block font-semibold text-gray-700 mb-1">Nomor Lantai</label>
                    <input type="number" id="quick-lantai-nomor" name="nomor_lantai" value="{{ ($gedung->lantais->max('nomor_lantai') ?? 0) + 1 }}" required class="w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-mono text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px] tabular-nums">
                </div>
                <div>
                    <label for="quick-lantai-nama" class="block font-semibold text-gray-700 mb-1">Label Nama Lantai</label>
                    <input type="text" id="quick-lantai-nama" name="nama" value="Lantai {{ ($gedung->lantais->max('nomor_lantai') ?? 0) + 1 }}" required autocomplete="off" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-900 focus:outline-none focus:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px]">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#111827] text-white font-semibold hover:bg-gray-800 active:scale-[0.98] transition min-h-[40px] shadow-xs cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Simpan</button>
                    <button type="button" onclick="document.getElementById('form-add-lantai-box').classList.add('hidden')" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-white active:scale-[0.98] min-h-[40px] cursor-pointer focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none">Batal</button>
                </div>
            </form>
        </div>

        @forelse($gedung->lantais->sortBy('nomor_lantai') as $l)
            <div class="bg-white border border-gray-200/90 rounded-2xl p-4 sm:p-5 shadow-xs text-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 mb-3.5 gap-2">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-[#111827] text-white font-mono font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            L{{ $l->nomor_lantai }}
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <strong class="font-bold text-gray-900 text-sm">{{ $l->nama }}</strong>
                            </div>
                            @if($l->keterangan)
                                <p class="text-[11px] text-gray-500 italic">{{ $l->keterangan }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @php
                            $floorAparCount = $l->apars_count;
                        @endphp
                        <span class="text-gray-500 font-semibold text-xs mr-1 font-mono tabular-nums">
                            {{ $l->lokasis->count() }} Titik Lokasi
                            @if($l->ruangans->count() > 0)
                                · {{ $l->ruangans->count() }} Ruangan
                            @endif
                            · <strong class="{{ $floorAparCount > 0 ? 'text-gray-900' : 'text-gray-400' }}">{{ $floorAparCount }}</strong> APAR
                        </span>
                    </div>
                </div>

                @php
                    $hasLokasis = $l->lokasis && $l->lokasis->count() > 0;
                    $hasRuangans = $l->ruangans && $l->ruangans->count() > 0;
                @endphp

                <!-- Daftar Titik Lokasi Operasional APAR -->
                @if($hasLokasis)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        @foreach($l->lokasis as $lok)
                            @php
                                $catLabel = $lok->kategori ?: $lok->tipe_lokasi_label;
                            @endphp
                            <div class="p-3.5 rounded-xl border border-gray-200/90 bg-white flex flex-col justify-between shadow-2xs hover:shadow-xs hover:border-gray-300 transition group">
                                <div>
                                    <div class="flex items-start justify-between gap-1.5 mb-1.5">
                                        <strong class="font-bold text-gray-900 text-xs leading-snug break-words">{{ $lok->nama }}</strong>
                                        @if($lok->kode)
                                            <span class="font-mono text-[10px] bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded border border-gray-200 shrink-0 tabular-nums">{{ $lok->kode }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center gap-1.5 flex-wrap mb-2">
                                        <span class="inline-flex items-center text-[10px] font-medium px-1.5 py-0.5 rounded border border-gray-200 bg-gray-50 text-gray-700">
                                            {{ $catLabel }}
                                        </span>

                                        @if($lok->ruangan && $lok->ruangan->kode)
                                            <span class="inline-flex items-center font-mono text-[10px] font-medium text-gray-600 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200 tabular-nums" title="Master Ruangan: {{ $lok->ruangan->nama }}">
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

                                <div class="mt-2.5 pt-2.5 border-t border-gray-100 flex items-start justify-between text-[11px] gap-2">
                                    <div>
                                        <div class="text-gray-700 font-mono flex items-center gap-1 font-semibold">
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
                                            class="text-gray-500 hover:text-gray-900 font-semibold inline-flex items-center gap-0.5 transition shrink-0 mt-0.5 focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:outline-none rounded active:scale-[0.98]"
                                            title="Lihat unit APAR di {{ $lok->nama }}"
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

                <!-- Master Ruangan Fisik (Jika Ada) -->
                @if($hasRuangans)
                    <div class="{{ $hasLokasis ? 'mt-3 pt-3 border-t border-gray-200/80' : '' }}">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Master Ruangan Fisik Terdaftar (<span class="tabular-nums">{{ $l->ruangans->count() }}</span>):</span>
                            </span>
                            <span class="text-[10px] text-gray-400 font-normal italic">Klinis / Administratif</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                            @foreach($l->ruangans as $r)
                                <div class="p-2.5 rounded-lg border border-gray-200/80 bg-gray-50/70 flex flex-col justify-between shadow-2xs hover:border-gray-300 transition group">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <strong class="font-bold text-gray-900 text-xs">{{ $r->nama }}</strong>
                                            @if($r->kode)
                                                <span class="font-mono text-[10px] bg-gray-200 px-1 py-0.5 rounded text-gray-700 tabular-nums">{{ $r->kode }}</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-gray-500 mt-0.5">
                                            {{ $r->kategori ?: 'Area Umum' }}
                                        </div>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-200/60 flex items-center justify-between text-[11px]">
                                        <span class="text-gray-600 font-mono font-semibold tabular-nums">{{ $r->apars->count() }} APAR terdata</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!$hasLokasis && !$hasRuangans)
                    <div class="p-3.5 rounded-xl border border-gray-200/80 bg-gray-50/70 text-gray-600 flex items-start gap-2.5">
                        <span class="p-1 rounded-md bg-gray-200 text-gray-500 shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
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
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-6 text-center text-gray-400 text-xs">
                Belum ada lantai yang terdaftar di gedung ini.
            </div>
        @endforelse
    </div>
</x-layouts.app>
