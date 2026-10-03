<x-layouts.app title="HAFIS : Jadwal Inspeksi APAR">
    <!-- 1. Header Halaman -->
    <x-page-header
        title="Jadwal Inspeksi"
        description="Manajemen periode dan target penugasan pemeriksaan berkala APAR fasilitas rumah sakit.">
        <x-slot:actions>
            <x-button variant="primary" href="{{ route('jadwal.create') }}">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Jadwal Inspeksi</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Notifikasi Sukses / Feedback -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">✓</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1">✕</button>
        </div>
    @endif

    <!-- 2. Ringkasan Metrik Jadwal Inspeksi -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-xs">
            <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Total Jadwal</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-slate-900 tabular-nums">{{ $totalJadwal }}</span>
                <span class="text-xs text-slate-500 font-medium">Periode</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-xs">
            <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Belum Dimulai</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-sky-700 tabular-nums">{{ $totalBelumDimulai }}</span>
                <span class="text-xs text-slate-500 font-medium">Menunggu</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-xs">
            <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Sedang Berjalan</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-amber-700 tabular-nums">{{ $totalBerjalan }}</span>
                <span class="text-xs text-slate-500 font-medium">Aktif</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-xs">
            <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider block mb-1">Selesai</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-emerald-700 tabular-nums">{{ $totalSelesai }}</span>
                <span class="text-xs text-slate-500 font-medium">Periode</span>
            </div>
        </div>
    </div>

    <!-- 3. Toolbar Pencarian & Filter Status -->
    <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 mb-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
        <form method="GET" action="{{ route('jadwal.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full">
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search }}" 
                    placeholder="Cari nama jadwal, catatan, atau pembuat..." 
                    class="w-full pl-9 pr-3 py-1.5 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:outline-none transition min-h-[38px]"
                />
            </div>

            <!-- Filter Status -->
            <div class="flex items-center gap-2">
                <select 
                    name="status" 
                    onchange="this.form.submit()" 
                    class="bg-slate-50 border border-slate-200 text-slate-700 text-xs rounded-lg px-3 py-1.5 focus:bg-white focus:border-slate-400 focus:outline-none min-h-[38px] cursor-pointer"
                >
                    <option value="">Semua Status</option>
                    <option value="belum_dimulai" {{ $statusFilter === 'belum_dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                    <option value="berjalan" {{ $statusFilter === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                    <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="draft" {{ $statusFilter === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="dibatalkan" {{ $statusFilter === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                @if($search || $statusFilter)
                    <a href="{{ route('jadwal.index') }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-medium min-h-[38px] flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Tabel Daftar Jadwal Inspeksi (Section 6) -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Jadwal</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3">Target</th>
                        <th class="px-4 py-3">Progress</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($jadwals as $j)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Kolom Jadwal -->
                            <td class="px-4 py-3.5">
                                <a href="{{ route('jadwal.show', $j->id) }}" class="font-bold text-slate-900 hover:underline text-sm block">
                                    {{ $j->nama }}
                                </a>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <span>Dibuat oleh: <strong class="text-slate-700">{{ $j->dibuat_oleh }}</strong></span>
                                    <span>•</span>
                                    <span>{{ $j->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($j->deskripsi)
                                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-1 italic">{{ $j->deskripsi }}</p>
                                @endif
                            </td>

                            <!-- Kolom Periode -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono text-slate-800 font-semibold">
                                    {{ $j->tanggal_mulai->format('d/m/Y') }} – {{ $j->tanggal_selesai->format('d/m/Y') }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $j->tanggal_mulai->diffInDays($j->tanggal_selesai) + 1 }} hari kalender
                                </div>
                            </td>

                            <!-- Kolom Target -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-900">
                                    {{ $j->target_label }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    {{ $j->total_target }} unit APAR
                                </div>
                            </td>

                            <!-- Kolom Progress (Section 6) -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($j->total_diperiksa === 0)
                                    <div class="text-slate-500 font-medium">Belum ada pemeriksaan</div>
                                    <div class="text-[11px] text-slate-400 font-mono">0 / {{ $j->total_target }} diperiksa</div>
                                @else
                                    <div class="font-bold text-slate-900 font-mono">
                                        {{ $j->total_diperiksa }} / {{ $j->total_target }}
                                    </div>
                                    <div class="w-24 bg-slate-200 rounded-full h-1.5 mt-1 overflow-hidden">
                                        <div 
                                            class="bg-emerald-600 h-1.5 rounded-full" 
                                            style="width: {{ $j->total_target > 0 ? round(($j->total_diperiksa / $j->total_target) * 100) : 0 }}%"
                                        ></div>
                                    </div>
                                @endif
                            </td>

                            <!-- Kolom Status -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <x-badge :variant="$j->status_badge_variant">
                                    {{ $j->status_label }}
                                </x-badge>
                            </td>

                            <!-- Kolom Aksi -->
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a 
                                        href="{{ route('jadwal.show', $j->id) }}" 
                                        class="px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition"
                                    >
                                        Detail
                                    </a>
                                    <a 
                                        href="{{ route('jadwal.edit', $j->id) }}" 
                                        class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs transition"
                                        title="Edit Jadwal"
                                    >
                                        Edit
                                    </a>
                                    @if(!in_array($j->status, ['dibatalkan', 'selesai']))
                                        <form action="{{ route('jadwal.batalkan', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan jadwal inspeksi {{ $j->nama }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="px-2 py-1.5 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50 text-xs transition"
                                                title="Batalkan Jadwal"
                                            >
                                                Batal
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm">Belum ada jadwal inspeksi</h4>
                                        <p class="text-slate-500 text-xs mt-1">
                                            Buat jadwal inspeksi berkala baru untuk menugaskan pemeriksaan seluruh APAR fasilitas rumah sakit.
                                        </p>
                                    </div>
                                    <div>
                                        <a 
                                            href="{{ route('jadwal.create') }}" 
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition min-h-[44px]"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            <span>Buat Jadwal Pertama</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwals->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $jadwals->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>