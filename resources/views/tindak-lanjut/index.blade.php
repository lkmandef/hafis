<x-layouts.app title="HAFIS : Tindak Lanjut & Pemeliharaan APAR">
    <!-- 1. Header Halaman & Action -->
    <x-page-header
        title="Tindak Lanjut & Pemeliharaan APAR"
        description="Daftar penanganan perbaikan dan riwayat pemeliharaan berkala unit APAR rumah sakit.">
        <x-slot:actions>
            <a href="{{ route('apar.index') }}"
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors min-h-[42px] focus:outline-none focus:ring-2 focus:ring-gray-900/20 shadow-2xs">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Master Data APAR</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="mb-4 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center cursor-pointer" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @php
        $statusItems = [
            [
                'key' => '',
                'label' => 'Semua',
                'count' => $stats['total'],
                'dot' => null,
                'active' => empty(request('status')),
            ],
            [
                'key' => 'belum_ditindaklanjuti',
                'label' => 'Belum Ditindaklanjuti',
                'count' => $stats['belum'],
                'dot' => 'bg-amber-500',
                'active' => request('status') === 'belum_ditindaklanjuti',
            ],
            [
                'key' => 'dalam_proses',
                'label' => 'Dalam Proses',
                'count' => $stats['proses'],
                'dot' => 'bg-blue-500',
                'active' => request('status') === 'dalam_proses',
            ],
            [
                'key' => 'selesai',
                'label' => 'Selesai',
                'count' => $stats['selesai'],
                'dot' => 'bg-emerald-500',
                'active' => request('status') === 'selesai',
            ],
        ];

        $buildStatusUrl = function ($statusKey) {
            return route('tindak-lanjut.index', array_filter([
                'status' => $statusKey,
                'q' => request('q'),
                'jenis' => request('jenis'),
            ], fn ($v) => ! is_null($v) && $v !== ''));
        };
    @endphp

    <!-- 2. Compact Status & Filter Row -->
    <div class="mb-4 sm:mb-5 relative">
        <div class="flex items-center flex-nowrap gap-1.5 sm:gap-2 overflow-x-auto scrollbar-none py-1 -my-1 scroll-smooth scroll-fade-r sm:[mask-image:none] pr-8 sm:pr-0" style="scrollbar-width: none; -ms-overflow-style: none;">
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

    <!-- 3. Container Utama: Toolbar & Tabel Daftar Tindak Lanjut -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs">
        <!-- Toolbar Pencarian & Filter -->
        <div class="p-3.5 sm:p-4 border-b border-gray-200 bg-white">
            <form method="GET" action="{{ route('tindak-lanjut.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
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
                        value="{{ request('q') }}" 
                        placeholder="Cari kode APAR, uraian, pelaksana, catatan..." 
                        class="w-full pl-9 pr-3 py-2 text-xs text-gray-900 placeholder-gray-400 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus:ring-1 focus:ring-gray-900/20 focus:outline-none transition min-h-[42px]"
                    />
                </div>

                <!-- Filter Jenis Tindakan -->
                <div class="w-full sm:w-auto sm:min-w-[190px]">
                    <select 
                        name="jenis" 
                        class="w-full px-3 py-2 text-xs text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-gray-900 focus:ring-1 focus:ring-gray-900/20 focus:outline-none transition min-h-[42px] cursor-pointer"
                    >
                        <option value="">Semua Jenis Tindakan</option>
                        <option value="perbaikan" {{ request('jenis') === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="pengisian_ulang" {{ request('jenis') === 'pengisian_ulang' ? 'selected' : '' }}>Pengisian Ulang</option>
                        <option value="pembersihan" {{ request('jenis') === 'pembersihan' ? 'selected' : '' }}>Pembersihan</option>
                        <option value="penggantian_komponen" {{ request('jenis') === 'penggantian_komponen' ? 'selected' : '' }}>Penggantian Komponen</option>
                        <option value="pemeriksaan_ulang" {{ request('jenis') === 'pemeriksaan_ulang' ? 'selected' : '' }}>Pemeriksaan Ulang</option>
                        <option value="lainnya" {{ request('jenis') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Tombol Cari & Reset -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-[#111827] text-white rounded-lg text-xs font-semibold hover:bg-gray-800 transition duration-150 ease-in-out min-h-[42px] focus:outline-none focus:ring-2 focus:ring-gray-900 cursor-pointer shadow-xs">
                        Cari
                    </button>

                    @if(request('q') || request('status') || request('jenis'))
                        <a href="{{ route('tindak-lanjut.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition min-h-[42px] border border-gray-200">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Tampilan Kartu Mobile untuk Petugas Lapangan (sm:hidden) -->
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($tindakLanjuts as $tl)
                <div class="p-4 space-y-3">
                    <!-- Header Kartu: Status Badge & Tanggal -->
                    <div class="flex items-center justify-between gap-2">
                        <x-badge :variant="$tl->status_badge_variant" :dot="true">
                            {{ $tl->status_label }}
                        </x-badge>
                        <span class="font-mono text-xs text-gray-500 tabular-nums">
                            {{ $tl->tanggal_tindakan->format('d/m/Y') }}
                        </span>
                    </div>

                    <!-- Temuan Masalah & Uraian Tindakan -->
                    <div>
                        <span class="font-bold text-gray-900 text-sm block leading-snug">
                            {{ $tl->temuan_teks }}
                        </span>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                            <span class="font-medium text-gray-700">Tindakan:</span> {{ $tl->uraian_tindakan }}
                        </p>
                    </div>

                    <!-- Metadata Grid: APAR, Lokasi, Jenis, Pelaksana -->
                    <div class="bg-gray-50/70 rounded-lg p-2.5 space-y-1.5 text-xs border border-gray-100">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-gray-500 shrink-0">Unit APAR:</span>
                            <div class="text-right">
                                <a href="{{ route('apar.show', $tl->apar_id) }}" class="font-mono font-bold text-gray-900 hover:underline">
                                    {{ $tl->apar->kode }}
                                </a>
                                <span class="text-gray-500 block text-[11px] truncate max-w-[200px]" title="{{ $tl->apar->lokasi_lengkap }}">
                                    {{ $tl->apar->lokasi_lengkap }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-gray-200/50">
                            <span class="text-gray-500">Jenis:</span>
                            <span class="font-medium text-gray-800">{{ $tl->jenis_label }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-gray-200/50">
                            <span class="text-gray-500">Pelaksana:</span>
                            <span class="font-medium text-gray-900">{{ $tl->petugas_pelaksana }}</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi Ramah Sentuh (min-h-[44px]) -->
                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}"
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-800 font-semibold text-xs shadow-2xs transition min-h-[44px]">
                            <span>Lihat Detail</span>
                        </a>
                        @if($tl->status !== 'selesai')
                            <a href="{{ route('tindak-lanjut.edit', $tl->id) }}"
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[#111827] hover:bg-gray-800 text-white font-semibold text-xs shadow-2xs transition min-h-[44px]">
                                <span>Perbarui</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    @if(request('q') || request('status') || request('jenis'))
                        <h4 class="text-sm font-bold text-gray-900">Tidak ada catatan yang cocok</h4>
                        <p class="text-xs text-gray-500 mt-1 mb-4">
                            Tidak ditemukan data tindak lanjut dengan parameter pencarian atau filter yang Anda pilih.
                        </p>
                        <a href="{{ route('tindak-lanjut.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition min-h-[42px]">
                            Reset Filter
                        </a>
                    @else
                        <h4 class="text-sm font-bold text-gray-900">Belum ada catatan tindak lanjut</h4>
                        <p class="text-xs text-gray-500 mt-1">
                            Catatan tindak lanjut dan pemeliharaan atas temuan pemeriksaan fisik APAR akan ditampilkan di sini.
                        </p>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- 5. Tabel Desktop (hidden sm:block) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full min-w-[860px] text-xs text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Temuan & Tindakan</th>
                        <th class="px-4 py-3">Unit APAR & Lokasi</th>
                        <th class="px-4 py-3 whitespace-nowrap">Tanggal</th>
                        <th class="px-4 py-3 whitespace-nowrap">Jenis</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 whitespace-nowrap">Pelaksana</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 bg-white">
                    @forelse($tindakLanjuts as $tl)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <!-- 1. Temuan Masalah & Uraian Tindakan -->
                            <td class="px-4 py-3.5 max-w-sm">
                                <span class="font-bold text-gray-900 text-xs block leading-snug" title="{{ $tl->temuan_teks }}">
                                    {{ $tl->temuan_teks }}
                                </span>
                                <span class="text-gray-500 block text-[11px] mt-1 leading-snug" title="{{ $tl->uraian_tindakan }}">
                                    <strong class="text-gray-700 font-medium">Tindakan:</strong> {{ $tl->uraian_tindakan }}
                                </span>
                            </td>

                            <!-- 2. Unit APAR & Lokasi -->
                            <td class="px-4 py-3.5">
                                <a href="{{ route('apar.show', $tl->apar_id) }}" class="font-mono font-bold text-gray-900 hover:underline inline-block">
                                    {{ $tl->apar->kode }}
                                </a>
                                <span class="text-gray-500 block text-[11px] mt-0.5 truncate max-w-xs" title="{{ $tl->apar->lokasi_lengkap }}">
                                    {{ $tl->apar->lokasi_lengkap }}
                                </span>
                            </td>

                            <!-- 3. Tanggal Tindakan -->
                            <td class="px-4 py-3.5 font-mono font-medium text-gray-900 whitespace-nowrap tabular-nums">
                                {{ $tl->tanggal_tindakan->format('d/m/Y') }}
                            </td>

                            <!-- 4. Jenis Tindakan -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-medium text-gray-800">{{ $tl->jenis_label }}</span>
                            </td>

                            <!-- 5. Status -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <x-badge :variant="$tl->status_badge_variant" :dot="true">
                                    {{ $tl->status_label }}
                                </x-badge>
                            </td>

                            <!-- 6. Pelaksana -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-medium text-gray-900">
                                {{ $tl->petugas_pelaksana }}
                            </td>

                            <!-- 7. Aksi -->
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('tindak-lanjut.show', $tl->id) }}"
                                       class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition min-h-[36px] inline-flex items-center shadow-2xs">
                                        <span>Detail</span>
                                    </a>
                                    @if($tl->status !== 'selesai')
                                        <a href="{{ route('tindak-lanjut.edit', $tl->id) }}"
                                           class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 text-xs font-medium transition min-h-[36px] inline-flex items-center shadow-2xs">
                                            <span>Update</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </div>
                                    @if(request('q') || request('status') || request('jenis'))
                                        <h4 class="text-sm font-bold text-gray-900">Tidak ada catatan yang cocok</h4>
                                        <p class="text-xs text-gray-500 mt-1 mb-4">
                                            Tidak ditemukan data tindak lanjut dengan parameter pencarian atau filter yang Anda pilih.
                                        </p>
                                        <a href="{{ route('tindak-lanjut.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition min-h-[42px]">
                                            Reset Filter
                                        </a>
                                    @else
                                        <h4 class="text-sm font-bold text-gray-900">Belum ada catatan tindak lanjut</h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Catatan tindak lanjut dan pemeliharaan atas temuan pemeriksaan fisik APAR akan ditampilkan di sini.
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tindakLanjuts->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $tindakLanjuts->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
