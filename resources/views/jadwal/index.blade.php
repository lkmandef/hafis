<x-layouts.app title="HAFIS : Jadwal Inspeksi APAR">
    <!-- 1. Header Halaman -->
    <x-page-header
        title="Jadwal Inspeksi"
        description="Manajemen periode dan target penugasan pemeriksaan berkala APAR fasilitas rumah sakit." />

    <!-- Notifikasi Sukses / Feedback -->
    @if(session('success'))
        <div class="mb-4 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">✓</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1 cursor-pointer" aria-label="Tutup notifikasi">✕</button>
        </div>
    @endif

    @php
        $totalDibatalkan = max(0, $totalJadwal - ($totalBelumDimulai + $totalBerjalan + $totalSelesai));

        $statusItems = [
            [
                'key' => '',
                'label' => 'Semua',
                'count' => $totalJadwal,
                'dot' => null,
                'active' => empty($statusFilter),
            ],
            [
                'key' => 'belum_dimulai',
                'label' => 'Belum Dimulai',
                'count' => $totalBelumDimulai,
                'dot' => 'bg-sky-500',
                'active' => $statusFilter === 'belum_dimulai',
            ],
            [
                'key' => 'berjalan',
                'label' => 'Berjalan',
                'count' => $totalBerjalan,
                'dot' => 'bg-amber-500',
                'active' => $statusFilter === 'berjalan',
            ],
            [
                'key' => 'selesai',
                'label' => 'Selesai',
                'count' => $totalSelesai,
                'dot' => 'bg-emerald-500',
                'active' => $statusFilter === 'selesai',
            ],
            [
                'key' => 'dibatalkan',
                'label' => 'Dibatalkan',
                'count' => $totalDibatalkan,
                'dot' => 'bg-rose-500',
                'active' => $statusFilter === 'dibatalkan',
            ],
        ];

        $buildStatusUrl = function ($statusKey) use ($search) {
            return route('jadwal.index', array_filter([
                'status' => $statusKey,
                'q' => $search,
            ], fn ($v) => ! is_null($v) && $v !== ''));
        };
    @endphp

    <!-- 2. Compact Status & Filter Row -->
    <div class="mb-4 sm:mb-5">
        <div class="flex items-center flex-nowrap gap-1.5 sm:gap-2 overflow-x-auto scrollbar-none py-1 -my-1 scroll-smooth scroll-fade-r sm:[mask-image:none] pr-6 sm:pr-0" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($statusItems as $item)
                @if($item['active'])
                    <span 
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-[#111827] text-white border border-[#111827] shadow-2xs shrink-0 min-h-[42px] cursor-default"
                        aria-current="page">
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-[11px] font-bold px-1.5 py-0.5 rounded bg-white/20 text-white leading-none">
                            {{ $item['count'] }}
                        </span>
                    </span>
                @else
                    <a 
                        href="{{ $buildStatusUrl($item['key']) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-medium text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-colors shrink-0 min-h-[42px] focus:outline-none focus:ring-2 focus:ring-gray-900/20 shadow-2xs">
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-[11px] font-semibold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 leading-none">
                            {{ $item['count'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- 3. Container Utama: Toolbar & Tabel Daftar Jadwal -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs">
        <!-- Toolbar Pencarian & Action -->
        <div class="p-3.5 sm:p-4 border-b border-gray-200 bg-white">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 sm:gap-3">
                <!-- Search Form -->
                <form method="GET" action="{{ route('jadwal.index') }}" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-2.5">
                    @if($statusFilter)
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                    @endif

                    <!-- Search Input -->
                    <div class="relative flex-1 min-w-0 sm:min-w-[240px]">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $search }}" 
                            placeholder="Cari nama jadwal, catatan, atau pembuat..." 
                            class="w-full pl-9 pr-3 py-2 text-xs text-gray-900 placeholder-gray-400 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus:outline-none transition min-h-[42px]"
                        />
                    </div>

                    <!-- Tombol Cari & Reset -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="submit" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-[#111827] text-white rounded-lg text-xs font-semibold hover:bg-gray-800 transition duration-150 ease-in-out min-h-[42px] cursor-pointer shadow-xs">
                            Cari
                        </button>

                        @if($search || $statusFilter)
                            <a href="{{ route('jadwal.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition min-h-[42px] border border-gray-200">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Primary Action: Buat Jadwal Inspeksi -->
                <div class="shrink-0 flex items-center pt-2 sm:pt-0 border-t border-gray-100 sm:border-t-0">
                    <a href="{{ route('jadwal.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold text-white bg-[#111827] hover:bg-gray-800 border border-[#111827] shadow-xs transition duration-150 ease-in-out min-h-[42px] shrink-0 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Buat Jadwal Inspeksi</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. Daftar Jadwal Inspeksi (Mobile Card Reflow: sm:hidden) -->
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($jadwals as $j)
                <div class="p-4 space-y-3 bg-white">
                    <!-- Header Kartu: Nama & Status -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <a href="{{ route('jadwal.show', $j->id) }}" class="font-bold text-gray-900 text-sm hover:underline block leading-snug">
                                {{ $j->nama }}
                            </a>
                            <div class="text-[11px] text-gray-500 mt-1 flex items-center gap-1.5 font-mono">
                                <span>{{ $j->tanggal_mulai->format('d/m/Y') }} – {{ $j->tanggal_selesai->format('d/m/Y') }}</span>
                                <span class="text-gray-300">•</span>
                                <span>{{ $j->tanggal_mulai->diffInDays($j->tanggal_selesai) + 1 }} hr</span>
                            </div>
                        </div>
                        <x-badge :variant="$j->status_badge_variant" size="sm">
                            {{ $j->status_label }}
                        </x-badge>
                    </div>

                    <!-- Progress Bar Inspeksi -->
                    <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Progress:</span>
                            <span class="font-bold text-gray-900 font-mono tabular-nums">
                                {{ $j->total_diperiksa }} / {{ $j->total_target }} unit
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                            <div 
                                class="bg-emerald-600 h-1.5 rounded-full transition-all" 
                                style="width: {{ $j->total_target > 0 ? round(($j->total_diperiksa / $j->total_target) * 100) : 0 }}%"
                            ></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-500 pt-0.5">
                            <span>Target: <strong class="text-gray-700">{{ $j->target_label }}</strong></span>
                            <span>{{ $j->total_target > 0 ? round(($j->total_diperiksa / $j->total_target) * 100) : 0 }}%</span>
                        </div>
                    </div>

                    @if($j->deskripsi)
                        <p class="text-xs text-gray-600 italic line-clamp-2">
                            {{ $j->deskripsi }}
                        </p>
                    @endif

                    <div class="text-[11px] text-gray-400">
                        Dibuat oleh: <strong class="text-gray-600 font-medium">{{ $j->dibuat_oleh }}</strong>
                    </div>

                    <!-- Tombol Aksi Mobile -->
                    <div class="pt-1 flex items-center gap-2">
                        <a href="{{ route('jadwal.show', $j->id) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-800 font-semibold text-xs shadow-2xs min-h-[40px] transition">
                            Detail
                        </a>
                        <a href="{{ route('jadwal.edit', $j->id) }}" 
                           class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs shadow-2xs min-h-[40px] transition">
                            Edit
                        </a>
                        @if(!in_array($j->status, ['dibatalkan', 'selesai']))
                            <form action="{{ route('jadwal.batalkan', $j->id) }}" method="POST"
                                  data-confirm
                                  data-confirm-title="Batalkan Jadwal Inspeksi"
                                  data-confirm-message="Apakah Anda yakin ingin membatalkan jadwal inspeksi {{ $j->nama }}?"
                                  data-confirm-type="warning"
                                  data-confirm-text="Batalkan Jadwal"
                                  class="inline">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-2 rounded-lg border border-rose-200 bg-rose-50/50 hover:bg-rose-100 text-rose-700 text-xs font-semibold min-h-[40px] inline-flex items-center justify-center transition cursor-pointer">
                                    Batalkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white">
                    <p class="text-xs text-gray-500">Belum ada jadwal inspeksi.</p>
                </div>
            @endforelse
        </div>

        <!-- 5. Tabel Daftar Jadwal Inspeksi (Desktop Table: hidden sm:block) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full min-w-[780px] text-xs text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Jadwal</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3">Target</th>
                        <th class="px-4 py-3">Progress</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($jadwals as $j)
                        <tr class="hover:bg-gray-50/70 transition">
                            <!-- Kolom Jadwal -->
                            <td class="px-4 py-3.5">
                                <a href="{{ route('jadwal.show', $j->id) }}" class="font-bold text-gray-900 hover:underline text-sm block">
                                    {{ $j->nama }}
                                </a>
                                <div class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1.5">
                                    <span>Dibuat oleh: <strong class="text-gray-700">{{ $j->dibuat_oleh }}</strong></span>
                                    <span class="text-gray-300">•</span>
                                    <span class="font-mono">{{ $j->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($j->deskripsi)
                                    <p class="text-[11px] text-gray-500 mt-1 line-clamp-1 italic">{{ $j->deskripsi }}</p>
                                @endif
                            </td>

                            <!-- Kolom Periode -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono text-gray-900 font-semibold tabular-nums">
                                    {{ $j->tanggal_mulai->format('d/m/Y') }} – {{ $j->tanggal_selesai->format('d/m/Y') }}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5 font-mono">
                                    {{ $j->tanggal_mulai->diffInDays($j->tanggal_selesai) + 1 }} hari kalender
                                </div>
                            </td>

                            <!-- Kolom Target -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-gray-900">
                                    {{ $j->target_label }}
                                </div>
                                <div class="text-[11px] text-gray-500 font-mono mt-0.5">
                                    {{ $j->total_target }} unit APAR
                                </div>
                            </td>

                            <!-- Kolom Progress -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($j->total_diperiksa === 0)
                                    <div class="text-gray-500 font-medium">Belum ada pemeriksaan</div>
                                    <div class="text-[11px] text-gray-400 font-mono">0 / {{ $j->total_target }} diperiksa</div>
                                @else
                                    <div class="font-bold text-gray-900 font-mono tabular-nums">
                                        {{ $j->total_diperiksa }} / {{ $j->total_target }}
                                    </div>
                                    <div class="w-24 bg-gray-200 rounded-full h-1.5 mt-1 overflow-hidden">
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
                                        class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition shadow-2xs"
                                    >
                                        Detail
                                    </a>
                                    <a 
                                        href="{{ route('jadwal.edit', $j->id) }}" 
                                        class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 text-xs transition shadow-2xs"
                                        title="Edit Jadwal"
                                    >
                                        Edit
                                    </a>
                                    @if(!in_array($j->status, ['dibatalkan', 'selesai']))
                                        <form action="{{ route('jadwal.batalkan', $j->id) }}" method="POST"
                                              data-confirm
                                              data-confirm-title="Batalkan Jadwal Inspeksi"
                                              data-confirm-message="Apakah Anda yakin ingin membatalkan jadwal inspeksi {{ $j->nama }}? Status jadwal akan diubah menjadi dibatalkan."
                                              data-confirm-type="warning"
                                              data-confirm-text="Batalkan Jadwal">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50 text-xs transition min-h-[36px] cursor-pointer shadow-2xs"
                                                title="Batalkan Jadwal"
                                            >
                                                Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center mx-auto text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm">Belum ada jadwal inspeksi</h4>
                                        <p class="text-gray-500 text-xs mt-1">
                                            Buat jadwal inspeksi berkala baru untuk menugaskan pemeriksaan seluruh APAR fasilitas rumah sakit.
                                        </p>
                                    </div>
                                    <div>
                                        <a 
                                            href="{{ route('jadwal.create') }}" 
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#111827] text-white font-semibold text-xs hover:bg-gray-800 transition min-h-[42px] shadow-xs cursor-pointer"
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
            <div class="p-4 border-t border-gray-100">
                {{ $jadwals->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>