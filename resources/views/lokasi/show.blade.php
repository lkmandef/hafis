<x-layouts.app title="HAFIS : Detail Gedung {{ $gedung->nama }}">
    <!-- 1. Header Halaman -->
    <x-page-header
        title="{{ $gedung->nama }}"
        description="Detail struktur lantai dan ruangan/area fasilitas gedung">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ route('lokasi.index') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Struktur Lokasi</span>
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

    <!-- Daftar Lantai & Ruangan di Gedung Ini -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Lantai &amp; Ruangan</h3>
            <button 
                type="button" 
                onclick="document.getElementById('form-add-lantai-box').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition"
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
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">Simpan</button>
                    <button type="button" onclick="document.getElementById('form-add-lantai-box').classList.add('hidden')" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-white">Batal</button>
                </div>
            </form>
        </div>

        @forelse($gedung->lantais as $l)
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs text-xs">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100 mb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-slate-900 text-white font-mono font-bold text-xs flex items-center justify-center">
                            L{{ $l->nomor_lantai }}
                        </span>
                        <div>
                            <strong class="font-bold text-slate-900 text-sm">{{ $l->nama }}</strong>
                            @if($l->keterangan)
                                <p class="text-[11px] text-slate-500 italic">{{ $l->keterangan }}</p>
                            @endif
                        </div>
                    </div>
                    <span class="text-slate-500 font-semibold text-xs">{{ $l->ruangans->count() }} Ruangan / Area</span>
                </div>

                <!-- Ruangan Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    @forelse($l->ruangans as $r)
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50">
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
</x-layouts.app>
