<x-layouts.app title="HAFIS : Buat Jadwal Inspeksi APAR">
    <!-- 1. Header Halaman -->
    <x-page-header
        title="Buat Jadwal Inspeksi Baru"
        description="Tetapkan periode inspeksi berkala dan alokasi target unit APAR rumah sakit.">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ route('jadwal.index') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Daftar Jadwal</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

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

    <div class="max-w-3xl">
        <form action="{{ route('jadwal.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Card 1: Informasi Pokok Jadwal -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs text-xs space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Informasi Jadwal &amp; Periode</h3>
                    <p class="text-slate-500 text-[11px] mt-0.5">Tentukan nama jadwal dan rentang waktu kalender pemeriksaan.</p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">
                        Nama / Judul Jadwal <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        value="{{ old('nama', 'Inspeksi APAR ' . now()->translatedFormat('F Y')) }}" 
                        required 
                        placeholder="Contoh: Inspeksi APAR Oktober 2026"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 focus:bg-white bg-slate-50 min-h-[40px]"
                    >
                    <p class="text-[11px] text-slate-400 mt-1">Gunakan format yang konsisten, misalnya per bulan atau triwulan.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Tanggal Mulai <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_mulai" 
                            value="{{ old('tanggal_mulai', now()->startOfMonth()->format('Y-m-d')) }}" 
                            required 
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono focus:outline-none focus:border-slate-500 min-h-[40px]"
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Tanggal Selesai <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_selesai" 
                            value="{{ old('tanggal_selesai', now()->endOfMonth()->format('Y-m-d')) }}" 
                            required 
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono focus:outline-none focus:border-slate-500 min-h-[40px]"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Awal</label>
                        <select 
                            name="status" 
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 bg-white min-h-[40px] cursor-pointer"
                        >
                            <option value="belum_dimulai" {{ old('status') === 'belum_dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                            <option value="berjalan" {{ old('status') === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Status dapat otomatis menyesuaikan dengan tanggal kalender saat ini.</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Dibuat Oleh</label>
                        <input 
                            type="text" 
                            name="dibuat_oleh" 
                            value="{{ old('dibuat_oleh', auth()->user()?->name ?? 'Admin K3RS') }}" 
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 min-h-[40px]"
                        >
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi / Instruksi Khusus (Opsional)</label>
                    <textarea 
                        name="deskripsi" 
                        rows="2" 
                        placeholder="Contoh: Fokuskan pemeriksaan pada tekanan nozzle dan aksesibilitas bracket jalur evakuasi..."
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500"
                    >{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <!-- Card 2: Pemilihan Target APAR (Section 4 & 8) -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs text-xs space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Target Pemeriksaan APAR</h3>
                    <p class="text-slate-500 text-[11px] mt-0.5">Pilih unit APAR yang menjadi sasaran inspeksi pada jadwal ini.</p>
                </div>

                <div class="space-y-3">
                    <!-- Opsi 1: Semua APAR Aktif (Rekomendasi Utama) -->
                    <label class="flex items-start gap-3 p-3.5 rounded-lg border border-slate-200 bg-slate-50/60 hover:bg-slate-50 cursor-pointer transition">
                        <input 
                            type="radio" 
                            name="tipe_target" 
                            value="semua" 
                            checked 
                            onchange="updateTargetUI('semua')"
                            class="mt-0.5 text-slate-900 focus:ring-slate-900"
                        >
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <strong class="text-slate-900 text-xs font-bold">Semua APAR Aktif (Rekomendasi)</strong>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold">{{ $totalAparAktif }} Unit</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Seluruh unit APAR rumah sakit yang berstatus aktif akan otomatis dimasukkan sebagai target inspeksi.
                            </p>
                        </div>
                    </label>

                    <!-- Opsi 2: Berdasarkan Gedung Tertentu -->
                    <label class="flex items-start gap-3 p-3.5 rounded-lg border border-slate-200 bg-slate-50/60 hover:bg-slate-50 cursor-pointer transition">
                        <input 
                            type="radio" 
                            name="tipe_target" 
                            value="gedung" 
                            onchange="updateTargetUI('gedung')"
                            class="mt-0.5 text-slate-900 focus:ring-slate-900"
                        >
                        <div class="flex-1">
                            <strong class="text-slate-900 text-xs font-bold">Berdasarkan Gedung Fasilitas Tertentu</strong>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Hanya unit APAR yang berada di gedung yang dipilih.
                            </p>
                            <!-- Dropdown Gedung (hidden by default) -->
                            <div id="target-gedung-box" class="hidden mt-3 pt-3 border-t border-slate-200">
                                <label class="block font-semibold text-slate-700 mb-1">Pilih Gedung</label>
                                <select 
                                    name="gedung_id" 
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 bg-white"
                                >
                                    <option value="">-- Pilih Gedung Sasaran --</option>
                                    @foreach($gedungs as $g)
                                        <option value="{{ $g->id }}" {{ old('gedung_id') == $g->id ? 'selected' : '' }}>
                                            {{ $g->nama }} ({{ $g->kode }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </label>

                    <!-- Opsi 3: APAR Tertentu -->
                    <label class="flex items-start gap-3 p-3.5 rounded-lg border border-slate-200 bg-slate-50/60 hover:bg-slate-50 cursor-pointer transition">
                        <input 
                            type="radio" 
                            name="tipe_target" 
                            value="custom" 
                            onchange="updateTargetUI('custom')"
                            class="mt-0.5 text-slate-900 focus:ring-slate-900"
                        >
                        <div class="flex-1">
                            <strong class="text-slate-900 text-xs font-bold">Pilih APAR Tertentu Secara Manual</strong>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Memilih satu atau beberapa unit APAR secara spesifik dari daftar.
                            </p>
                            <!-- Checklist APAR (hidden by default) -->
                            <div id="target-custom-box" class="hidden mt-3 pt-3 border-t border-slate-200 space-y-2">
                                <div class="max-h-52 overflow-y-auto divide-y divide-slate-100 border border-slate-200 rounded-lg p-2 bg-white">
                                    @foreach($allApars as $a)
                                        <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                name="apar_ids[]" 
                                                value="{{ $a->id }}" 
                                                class="rounded text-slate-900 focus:ring-slate-900"
                                            >
                                            <div class="min-w-0 flex-1 flex items-center justify-between text-xs">
                                                <span class="font-mono font-bold text-slate-900">{{ $a->kode }}</span>
                                                <span class="text-slate-500 truncate text-[11px]">{{ $a->lokasi_lengkap }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Tombol Aksi Simpan & Batal -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a 
                    href="{{ route('jadwal.index') }}" 
                    class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition min-h-[44px] flex items-center"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-xs min-h-[44px] flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Jadwal Inspeksi</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function updateTargetUI(type) {
            const gedungBox = document.getElementById('target-gedung-box');
            const customBox = document.getElementById('target-custom-box');

            if (type === 'gedung') {
                gedungBox.classList.remove('hidden');
                customBox.classList.add('hidden');
            } else if (type === 'custom') {
                customBox.classList.remove('hidden');
                gedungBox.classList.add('hidden');
            } else {
                gedungBox.classList.add('hidden');
                customBox.classList.add('hidden');
            }
        }
    </script>
</x-layouts.app>
