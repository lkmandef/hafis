<x-layouts.app title="HAFIS : Master Data APAR">
    <!-- 1. Page Header -->
    <x-page-header
        title="Master Data APAR"
        description="Pusat pencatatan data identitas, spesifikasi teknis, dan lokasi penempatan unit APAR rumah sakit.">
        <x-slot:actions>
            <a href="{{ route('apar.create') }}" 
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition duration-150 ease-in-out shadow-2xs min-h-[42px] focus:outline-none focus:ring-2 focus:ring-slate-900/20"
               title="Tambah Master Data APAR Baru">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah APAR</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Notifikasi Sukses / Feedback -->
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
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @php
        $statusFilter = $status ?? ($search ? 'semua' : 'aktif');

        $statusItems = [
            [
                'key' => 'semua',
                'label' => 'Semua',
                'count' => $totalApar,
                'dot' => null,
                'active' => $statusFilter === 'semua',
            ],
            [
                'key' => 'aktif',
                'label' => 'Unit Aktif',
                'count' => $totalAktif,
                'dot' => 'bg-emerald-600',
                'active' => $statusFilter === 'aktif',
            ],
            [
                'key' => 'baik',
                'label' => 'Baik',
                'count' => $totalBaik,
                'dot' => 'bg-emerald-500',
                'active' => $statusFilter === 'baik',
            ],
            [
                'key' => 'perhatian',
                'label' => 'Perlu Perhatian',
                'count' => $totalPerhatian,
                'dot' => 'bg-amber-500',
                'active' => $statusFilter === 'perhatian',
            ],
            [
                'key' => 'rusak',
                'label' => 'Rusak',
                'count' => $totalRusak,
                'dot' => 'bg-rose-500',
                'active' => $statusFilter === 'rusak',
            ],
            [
                'key' => 'sudah_diganti',
                'label' => 'Sudah Diganti',
                'count' => $totalSudahDiganti,
                'dot' => 'bg-slate-400',
                'active' => $statusFilter === 'sudah_diganti',
            ],
        ];

        $statusLabelMap = [
            'semua' => 'Semua Status',
            'aktif' => 'Unit Aktif',
            'baik' => 'Kondisi Baik',
            'perhatian' => 'Perlu Perhatian',
            'rusak' => 'Kondisi Rusak',
            'sudah_diganti' => 'Sudah Diganti',
            'tidak_aktif' => 'Tidak Aktif',
        ];
        $activeStatusLabel = $statusLabelMap[$statusFilter] ?? ucfirst($statusFilter);

        $buildStatusUrl = function ($statusKey) use ($search, $lokasiId, $gedungId, $lantaiId, $jenisMedia) {
            return route('apar.index', array_filter([
                'status' => $statusKey,
                'q' => $search,
                'lokasi_id' => $lokasiId,
                'gedung_id' => $gedungId,
                'lantai_id' => $lantaiId,
                'jenis_media' => $jenisMedia,
            ], fn ($v) => ! is_null($v) && $v !== ''));
        };
    @endphp

    <!-- 2. Compact Status & Filter Tabs Row -->
    <div class="mb-4 sm:mb-5">
        <div class="flex items-center flex-nowrap gap-1.5 sm:gap-2 overflow-x-auto py-1 -my-1 scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($statusItems as $item)
                @if($item['active'])
                    <span 
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-900 text-white border border-slate-900 shadow-2xs shrink-0 min-h-[42px] cursor-default"
                        aria-current="page">
                        @if($item['dot'])
                            <span class="w-2 h-2 rounded-full {{ $item['dot'] }} ring-2 ring-white/30 shrink-0"></span>
                        @endif
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-[11px] font-bold px-1.5 py-0.5 rounded bg-white/20 text-white leading-none">
                            {{ $item['count'] }}
                        </span>
                    </span>
                @else
                    <a 
                        href="{{ $buildStatusUrl($item['key']) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-medium text-slate-700 bg-white border border-slate-200/90 hover:bg-slate-50 hover:text-slate-900 hover:border-slate-300 transition-colors shrink-0 min-h-[42px] focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                        @if($item['dot'])
                            <span class="w-2 h-2 rounded-full {{ $item['dot'] }} shrink-0"></span>
                        @endif
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-[11px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 leading-none">
                            {{ $item['count'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Container Utama: Toolbar, Info Bar, Tabel, dan Pagination -->
    <x-card class="!p-0 overflow-hidden">
        <!-- 3. Toolbar Pencarian & Filter (Konsisten dengan Pola Hasil Inspeksi) -->
        <div class="p-3.5 sm:p-4 border-b border-slate-200 bg-white">
            <form method="GET" action="{{ route('apar.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 lg:gap-3">
                <!-- Pencarian Kode APAR, QR, Merek, atau Lokasi -->
                <div class="relative flex-1 min-w-0 sm:min-w-[200px] lg:min-w-[220px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari kode APAR, QR, merek, atau lokasi..."
                        class="w-full pl-9 pr-3 py-2 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-900/20 focus:outline-none transition min-h-[42px]" />
                </div>

                <!-- Kontrol Filter Dropdown: Status Aset & Lokasi K3RS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex items-center gap-2.5 lg:gap-3 shrink-0">
                    <!-- Filter Status Aset -->
                    <div class="w-full sm:w-auto sm:min-w-[145px] lg:max-w-[175px] shrink-0">
                        <select 
                            name="status" 
                            id="filter_status"
                            aria-label="Filter Status APAR"
                            class="w-full px-3 py-2 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-900/20 focus:outline-none min-h-[42px] cursor-pointer truncate">
                            <option value="semua" {{ $statusFilter === 'semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Unit Aktif</option>
                            <option value="baik" {{ $statusFilter === 'baik' ? 'selected' : '' }}>Kondisi Baik</option>
                            <option value="perhatian" {{ $statusFilter === 'perhatian' ? 'selected' : '' }}>Perlu Perhatian</option>
                            <option value="rusak" {{ $statusFilter === 'rusak' ? 'selected' : '' }}>Kondisi Rusak</option>
                            <option value="sudah_diganti" {{ $statusFilter === 'sudah_diganti' ? 'selected' : '' }}>Sudah Diganti</option>
                            <option value="tidak_aktif" {{ $statusFilter === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    <!-- Filter Lokasi K3RS -->
                    <div class="w-full sm:w-auto sm:min-w-[170px] lg:max-w-[210px] shrink-0">
                        <select 
                            name="lokasi_id" 
                            id="filter_lokasi_id"
                            aria-label="Filter Lokasi K3RS"
                            class="w-full px-3 py-2 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-900/20 focus:outline-none min-h-[42px] cursor-pointer truncate">
                            <option value="">Semua Lokasi K3RS</option>
                            @foreach($lokasis as $lok)
                                <option value="{{ $lok->id }}" {{ (string) $lokasiId === (string) $lok->id ? 'selected' : '' }}>
                                    {{ $lok->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tombol Cari & Reset -->
                <div class="flex items-center gap-2 shrink-0">
                    <button 
                        type="submit" 
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-semibold hover:bg-slate-800 transition duration-150 ease-in-out min-h-[42px] focus:outline-none focus:ring-2 focus:ring-slate-900 cursor-pointer shadow-2xs">
                        Cari
                    </button>

                    @if($search || $lokasiId || $gedungId || $lantaiId || ($status && $status !== 'aktif') || $jenisMedia)
                        <a 
                            href="{{ route('apar.index', ['status' => 'semua']) }}" 
                            class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition min-h-[42px] border border-slate-200/80 sm:border-transparent">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Info Bar Hasil & Filter Aktif -->
        <div class="px-3.5 py-2.5 sm:px-4 sm:py-2.5 bg-slate-50/70 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500 font-medium">
            <div class="flex flex-wrap items-center gap-2">
                <span>Filter Status: <strong class="text-slate-800 font-semibold">{{ $activeStatusLabel }}</strong></span>
                @if($lokasiId)
                    <span class="text-slate-300">&bull;</span>
                    <span>Lokasi: <strong class="text-slate-800 font-semibold">{{ $lokasis->firstWhere('id', $lokasiId)?->nama ?? 'Lokasi Terpilih' }}</strong></span>
                @endif
                @if($search)
                    <span class="text-slate-300">&bull;</span>
                    <span>Pencarian: <strong class="text-slate-800 font-semibold">&ldquo;{{ $search }}&rdquo;</strong></span>
                @endif
            </div>
            <div class="text-[11px] text-slate-500">
                Total: <strong class="text-slate-800 font-semibold">{{ $apars->total() }}</strong> unit APAR
            </div>
        </div>

        <!-- 3. Tabel Data Master APAR -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Kode APAR</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Lokasi (Gedung / Lantai / Area)</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Media & Kapasitas</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Merek / Tipe</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Status</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Pemeriksaan Terakhir</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($apars as $apar)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Kode APAR & Register -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('apar.show', $apar->id) }}" class="font-mono font-bold text-slate-900 hover:text-slate-600 focus:underline focus:outline-none">
                                    {{ $apar->kode }}
                                </a>
                                @if($apar->nomor_register)
                                    <div class="text-[11px] text-slate-400 font-mono">Reg: {{ $apar->nomor_register }}</div>
                                @endif
                                <div class="text-[10px] text-slate-400 font-mono">QR: {{ $apar->qr_code }}</div>
                                @if($apar->is_replaced)
                                    <div class="text-[10px] text-rose-700 font-medium mt-0.5">
                                        Diganti: <a href="{{ route('apar.show', $apar->replaced_by_apar_id) }}" class="font-mono font-bold underline">{{ $apar->aparPengganti?->kode }}</a>
                                    </div>
                                @elseif($apar->is_replacement)
                                    <div class="text-[10px] text-sky-700 font-medium mt-0.5">
                                        Pengganti: <a href="{{ route('apar.show', $apar->replaces_apar_id) }}" class="font-mono font-bold underline">{{ $apar->aparDigantikan?->kode }}</a>
                                    </div>
                                @endif
                            </td>

                            <!-- Lokasi Berjenjang -->
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">
                                    {{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    @if($apar->lokasi)
                                        {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                                    @else
                                        {{ $apar->gedung?->nama ?? '-' }} &bull; {{ $apar->lantai?->nama ?? '-' }}
                                    @endif
                                </div>
                                @if($apar->posisi_detail)
                                    <div class="text-[11px] text-slate-400 italic mt-0.5 line-clamp-1">
                                        {{ $apar->posisi_detail }}
                                    </div>
                                @endif
                                <div class="mt-1">
                                    @if($apar->has_titik)
                                        <a href="{{ route('penempatan.index', ['gedung_id' => $apar->gedung_id, 'lantai_id' => $apar->lantai_id, 'highlight' => $apar->kode_apar]) }}" class="inline-flex items-center gap-1 text-[10px] text-emerald-700 font-semibold hover:underline">
                                            <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            </svg>
                                            <span>Denah: ({{ round($apar->koordinat_x) }}%, {{ round($apar->koordinat_y) }}%)</span>
                                        </a>
                                    @else
                                        <a href="{{ route('penempatan.index', ['gedung_id' => $apar->gedung_id, 'lantai_id' => $apar->lantai_id, 'selected_apar' => $apar->id]) }}" class="text-[10px] text-slate-400 hover:text-slate-600 inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                            <span>Denah: Belum ditentukan</span>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- Media & Kapasitas -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-slate-800 font-medium">{{ $apar->jenis_media }}</div>
                                <div class="font-mono text-slate-500 text-[11px]">{{ $apar->kapasitas }}</div>
                            </td>

                            <!-- Merek & Tipe -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-slate-800 {{ ! $apar->merek ? 'text-slate-400 italic' : '' }}">{{ $apar->merek ?: 'Belum terdata' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $apar->tipe_model ?: '-' }}</div>
                            </td>

                            <!-- Status Kondisi -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge :variant="$apar->status_badge_variant" :dot="true">
                                    {{ $apar->status_label }}
                                </x-badge>
                            </td>

                            <!-- Pemeriksaan Terakhir -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($apar->tanggal_pemeriksaan_terakhir)
                                    <span class="font-mono text-slate-900">
                                        {{ $apar->tanggal_pemeriksaan_terakhir->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum ada riwayat</span>
                                @endif
                                @if($apar->tanggal_kadaluarsa)
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        Exp: {{ $apar->tanggal_kadaluarsa->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <a href="{{ route('apar.show', $apar->id) }}" 
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition-colors min-h-[38px] sm:min-h-[32px] focus:outline-none focus:ring-2 focus:ring-slate-400">
                                        Detail
                                    </a>
                                    @if($apar->can_be_replaced)
                                        <a href="{{ route('apar.ganti', $apar->id) }}" 
                                           class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-amber-800 bg-amber-50 border border-amber-300 hover:bg-amber-100 transition-colors min-h-[38px] sm:min-h-[32px]" title="Ganti Unit APAR">
                                            Ganti
                                        </a>
                                    @endif
                                    <a href="{{ route('apar.edit', $apar->id) }}" 
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition-colors min-h-[38px] sm:min-h-[32px] focus:outline-none focus:ring-2 focus:ring-slate-400">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                        </svg>
                                    </div>
                                    @if($search || $lokasiId || $gedungId || $lantaiId || ($status && $status !== 'aktif') || $jenisMedia)
                                        <h4 class="text-sm font-bold text-slate-800">Tidak ada unit APAR yang cocok</h4>
                                        <p class="text-xs text-slate-500 mt-1 mb-4">
                                            Tidak ditemukan data unit APAR dengan parameter pencarian atau filter yang Anda pilih.
                                        </p>
                                        <a href="{{ route('apar.index', ['status' => 'semua']) }}" class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition min-h-[42px] focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                                            Reset Filter
                                        </a>
                                    @else
                                        <h4 class="text-sm font-bold text-slate-800">Belum ada data unit APAR</h4>
                                        <p class="text-xs text-slate-500 mt-1 mb-4">
                                            Unit APAR rumah sakit belum didaftarkan ke sistem. Mulai tambahkan data unit APAR sesuai lokasi fisik gedung.
                                        </p>
                                        <x-button variant="primary" href="{{ route('apar.create') }}">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            <span>Tambah APAR Baru</span>
                                        </x-button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 4. Pagination Links -->
        @if($apars->hasPages())
            <div class="p-4 sm:p-5 border-t border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <span class="text-xs text-slate-500">
                    Menampilkan <span class="font-semibold text-slate-700">{{ $apars->firstItem() }}</span> - <span class="font-semibold text-slate-700">{{ $apars->lastItem() }}</span> dari <span class="font-semibold text-slate-700">{{ $apars->total() }}</span> unit APAR
                </span>
                <div>
                    {{ $apars->links() }}
                </div>
            </div>
        @endif
    </x-card>
</x-layouts.app>