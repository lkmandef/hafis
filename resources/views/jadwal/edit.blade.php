<x-layouts.app title="HAFIS : Edit Jadwal {{ $jadwal->nama }}">
    <!-- 1. Header Halaman -->
    <x-page-header
        title="Edit Jadwal Inspeksi"
        description="Perbarui informasi periode dan status operasional jadwal inspeksi.">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ route('jadwal.show', $jadwal->id) }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Detail</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

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

    <div class="max-w-2xl">
        <form action="{{ route('jadwal.update', $jadwal->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs text-xs space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Informasi Jadwal</h3>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">
                        Nama / Judul Jadwal <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        value="{{ old('nama', $jadwal->nama) }}" 
                        required 
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 bg-white min-h-[40px]"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Tanggal Mulai <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_mulai" 
                            value="{{ old('tanggal_mulai', $jadwal->tanggal_mulai->format('Y-m-d')) }}" 
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
                            value="{{ old('tanggal_selesai', $jadwal->tanggal_selesai->format('Y-m-d')) }}" 
                            required 
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-mono focus:outline-none focus:border-slate-500 min-h-[40px]"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Operasional <span class="text-rose-500">*</span></label>
                        <select 
                            name="status" 
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500 bg-white min-h-[40px] cursor-pointer"
                        >
                            <option value="draft" {{ old('status', $jadwal->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="belum_dimulai" {{ old('status', $jadwal->status) === 'belum_dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                            <option value="berjalan" {{ old('status', $jadwal->status) === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="selesai" {{ old('status', $jadwal->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ old('status', $jadwal->status) === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Target Alokasi (Tetap)</label>
                        <input 
                            type="text" 
                            disabled 
                            value="{{ $jadwal->target_label }} ({{ $jadwal->total_target }} APAR)" 
                            class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-xs text-slate-600 min-h-[40px]"
                        >
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi / Catatan Tambahan</label>
                    <textarea 
                        name="deskripsi" 
                        rows="3" 
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:border-slate-500"
                    >{{ old('deskripsi', $jadwal->deskripsi) }}</textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button 
                        type="button" 
                        onclick="confirmDeleteJadwal()" 
                        class="text-rose-600 hover:text-rose-800 font-semibold text-xs"
                    >
                        Hapus Jadwal
                    </button>

                    <div class="flex items-center gap-2">
                        <a 
                            href="{{ route('jadwal.show', $jadwal->id) }}" 
                            class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition min-h-[40px] flex items-center"
                        >
                            Batal
                        </a>
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-xs min-h-[40px]"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <form id="form-delete-jadwal" action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        function confirmDeleteJadwal() {
            if (confirm('Apakah Anda yakin ingin menghapus jadwal "{{ $jadwal->nama }}"? Data master APAR tidak akan terhapus.')) {
                document.getElementById('form-delete-jadwal').submit();
            }
        }
    </script>
</x-layouts.app>
