<x-layouts.app title="HAFIS : Detail Jadwal {{ $jadwal->nama }}">
    <!-- 1. Header Halaman -->
    <x-page-header
        title="{{ $jadwal->nama }}"
        description="Detail periode pelaksanaan dan alokasi unit target inspeksi APAR">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ route('jadwal.index') }}">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Daftar Jadwal</span>
            </x-button>

            <x-button variant="secondary" href="{{ route('jadwal.edit', $jadwal->id) }}">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Jadwal</span>
            </x-button>

            @if(auth()->user()?->isAdmin() && !in_array($jadwal->status, ['dibatalkan', 'selesai']))
                <form action="{{ route('jadwal.batalkan', $jadwal->id) }}" method="POST"
                      data-confirm
                      data-confirm-title="Batalkan Jadwal Inspeksi"
                      data-confirm-message="Apakah Anda yakin ingin membatalkan jadwal inspeksi ini? Status jadwal akan diubah menjadi dibatalkan."
                      data-confirm-type="warning"
                      data-confirm-text="Batalkan Jadwal">
                    @csrf
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-rose-200 text-rose-700 bg-white hover:bg-rose-50 font-semibold text-xs transition min-h-[42px] cursor-pointer shadow-2xs"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Batalkan Jadwal</span>
                    </button>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="mb-5 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">✓</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1 cursor-pointer">✕</button>
        </div>
    @endif

    <!-- 2. Ringkasan Informasi Jadwal -->
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6 shadow-xs text-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100">
            <div>
                <span class="text-gray-400 font-mono text-[11px] block">JADWAL INSPEKSI APAR</span>
                <h2 class="text-base font-bold text-gray-900">{{ $jadwal->nama }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <x-badge :variant="$jadwal->status_badge_variant">
                    {{ $jadwal->status_label }}
                </x-badge>
                <span class="text-gray-300">•</span>
                <span class="font-semibold font-mono text-gray-700">{{ $jadwal->total_target }} APAR Aktif</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-1">
            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-400 font-semibold text-[11px] uppercase tracking-wider block mb-1">Periode Inspeksi</span>
                <div class="font-mono text-gray-900 font-bold text-xs tabular-nums">
                    {{ $jadwal->tanggal_mulai->format('d/m/Y') }} - {{ $jadwal->tanggal_selesai->format('d/m/Y') }}
                </div>
                <div class="text-[11px] text-gray-500 mt-0.5 font-mono">
                    {{ $jadwal->tanggal_mulai->translatedFormat('d F Y') }} - {{ $jadwal->tanggal_selesai->translatedFormat('d F Y') }}
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-400 font-semibold text-[11px] uppercase tracking-wider block mb-1">Target Cakupan</span>
                <div class="font-bold text-gray-900 text-xs">
                    {{ $jadwal->target_label }}
                </div>
                <div class="text-[11px] text-gray-500 font-mono mt-0.5">
                    {{ $jadwal->total_target }} unit APAR terdaftar
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-400 font-semibold text-[11px] uppercase tracking-wider block mb-1">Progress Pemeriksaan</span>
                <div class="font-bold text-gray-900 text-xs font-mono tabular-nums">
                    {{ $jadwal->progress_label }}
                </div>
                <div class="text-[11px] text-gray-500 mt-0.5 font-mono">
                    @if($jadwal->total_diperiksa === 0)
                        Belum ada pemeriksaan dimulai
                    @else
                        {{ round(($jadwal->total_diperiksa / $jadwal->total_target) * 100) }}% terselesaikan
                    @endif
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-400 font-semibold text-[11px] uppercase tracking-wider block mb-1">Dibuat Oleh</span>
                <div class="font-bold text-gray-900 text-xs">
                    {{ $jadwal->dibuat_oleh }}
                </div>
                <div class="text-[11px] text-gray-400 mt-0.5 font-mono">
                    {{ $jadwal->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        @if($jadwal->deskripsi)
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 leading-relaxed">
                <strong class="font-semibold text-gray-900 block text-[11px] mb-0.5">Catatan / Instruksi K3RS:</strong>
                <span class="text-gray-600">{{ $jadwal->deskripsi }}</span>
            </div>
        @endif
    </div>

    <!-- 3. Area Target APAR -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs text-xs">
        <div class="p-4 bg-gray-50/80 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-gray-900 text-sm">Target APAR ({{ $targetApars->total() }} Unit)</h3>
                <p class="text-gray-500 text-[11px] mt-0.5">Daftar unit tabung APAR yang masuk dalam jadwal pemeriksaan ini.</p>
            </div>

            <!-- Filter Target APAR -->
            <form method="GET" action="{{ route('jadwal.show', $jadwal->id) }}" class="flex items-center gap-2">
                <div class="relative w-full sm:w-60">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        name="q_apar" 
                        value="{{ $searchApar }}" 
                        placeholder="Cari kode atau lokasi..." 
                        class="w-full pl-8 pr-3 py-1.5 text-xs text-gray-900 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 min-h-[38px]"
                    />
                </div>

                <select 
                    name="status_apar" 
                    onchange="this.form.submit()" 
                    class="bg-white border border-gray-200 text-gray-700 text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 min-h-[38px] cursor-pointer"
                >
                    <option value="">Semua Kondisi</option>
                    <option value="baik" {{ $statusApar === 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="perhatian" {{ $statusApar === 'perhatian' ? 'selected' : '' }}>Perhatian</option>
                    <option value="rusak" {{ $statusApar === 'rusak' ? 'selected' : '' }}>Rusak</option>
                </select>

                @if($searchApar || $statusApar)
                    <a href="{{ route('jadwal.show', $jadwal->id) }}" class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 text-xs font-medium min-h-[38px] flex items-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[780px] text-xs text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Kode APAR</th>
                        <th class="px-4 py-3">Lokasi (Gedung &bull; Lantai &bull; Ruangan)</th>
                        <th class="px-4 py-3">Media / Kapasitas</th>
                        <th class="px-4 py-3 text-center">Kondisi APAR</th>
                        <th class="px-4 py-3 text-center">Status Pemeriksaan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($targetApars as $a)
                        <tr class="hover:bg-gray-50/70 transition">
                            <!-- Kode APAR -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('apar.show', $a->id) }}" class="font-mono font-bold text-gray-900 hover:underline">
                                    {{ $a->kode }}
                                </a>
                                @if($a->nomor_register)
                                    <div class="text-[10px] text-gray-400 font-mono">{{ $a->nomor_register }}</div>
                                @endif
                            </td>

                            <!-- Lokasi -->
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">
                                    {{ $a->lokasi?->nama ?? ($a->gedung?->nama ? ($a->gedung->nama . ' &bull; ' . ($a->lantai?->nama ?? '-') . ' &bull; ' . ($a->ruangan?->nama ?? '-')) : ($a->ruangan?->nama ?? '-')) }}
                                </div>
                                @if($a->posisi_detail)
                                    <div class="text-[11px] text-gray-500 italic mt-0.5 line-clamp-1">
                                        {{ $a->posisi_detail }}
                                    </div>
                                @endif
                            </td>

                            <!-- Media & Kapasitas -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-gray-800 font-medium">{{ $a->jenis_media }}</div>
                                <div class="text-[11px] text-gray-500 font-mono">{{ $a->kapasitas }}</div>
                            </td>

                            <!-- Kondisi APAR -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-badge :variant="$a->status_badge_variant">
                                    {{ $a->status_label }}
                                </x-badge>
                            </td>

                            <!-- Status Pemeriksaan -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($a->pivot->status_pemeriksaan === 'sudah_diperiksa')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50/50 text-emerald-700 border border-emerald-200/80">
                                        Sudah Diperiksa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                        Belum Diperiksa
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a 
                                        href="{{ route('apar.show', ['id' => $a->id, 'jadwal_id' => $jadwal->id]) }}" 
                                        class="px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-[11px] transition shadow-2xs"
                                    >
                                        Lihat APAR
                                    </a>

                                    @if($a->pivot->status_pemeriksaan === 'sudah_diperiksa')
                                        @php
                                            $pemUnit = \App\Models\Pemeriksaan::where('apar_id', $a->id)->where('jadwal_id', $jadwal->id)->latest('tanggal_pemeriksaan')->first();
                                        @endphp
                                        @if($pemUnit)
                                            <a href="{{ route('pemeriksaan.show', $pemUnit->id) }}" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-semibold text-[11px] transition shadow-2xs"
                                               title="Buka Lembar Hasil Checklist">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span>Lihat Hasil</span>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold text-[11px] cursor-default">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span>Sudah Diperiksa</span>
                                            </span>
                                        @endif
                                    @else
                                        @if($a->status === 'tidak_aktif')
                                            <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-400 font-semibold text-[11px] cursor-not-allowed border border-gray-200" title="APAR Tidak Aktif">
                                                Tidak Aktif
                                            </span>
                                        @else
                                            <a 
                                                href="{{ route('pemeriksaan.mulai', ['apar_id' => $a->id, 'jadwal_id' => $jadwal->id]) }}" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-[#111827] hover:bg-gray-800 text-white font-semibold text-[11px] transition shadow-2xs cursor-pointer"
                                            >
                                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Mulai Pemeriksaan</span>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                Tidak ada unit APAR yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($targetApars->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $targetApars->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
