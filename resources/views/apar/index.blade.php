<x-layouts.app title="HAFIS : Master Data APAR">
    <!-- 1. Page Header -->
    <x-page-header
        title="Master Data APAR"
        description="Pusat pencatatan data identitas, spesifikasi teknis, dan lokasi penempatan unit APAR rumah sakit.">
        <x-slot:actions>
            <x-button variant="primary" href="{{ route('apar.create') }}">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah APAR</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Notifikasi Sukses / Feedback -->
    @if(session('success'))
        <div class="mb-5 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
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

    <!-- Ringkasan Status Unit APAR (Real Data) -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <div class="p-3.5 bg-white border border-slate-200 rounded-xl">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Unit</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-xl font-bold text-slate-900 font-mono">{{ $totalApar }}</span>
                <span class="text-[11px] text-slate-400">tabung</span>
            </div>
        </div>

        <div class="p-3.5 bg-white border border-slate-200 rounded-xl">
            <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block">Kondisi Baik</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-xl font-bold text-emerald-800 font-mono">{{ $totalBaik }}</span>
                <span class="text-[11px] text-slate-400">siap guna</span>
            </div>
        </div>

        <div class="p-3.5 bg-white border border-slate-200 rounded-xl">
            <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider block">Perlu Perhatian</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-xl font-bold text-amber-800 font-mono">{{ $totalPerhatian }}</span>
                <span class="text-[11px] text-slate-400">pantau</span>
            </div>
        </div>

        <div class="p-3.5 bg-white border border-slate-200 rounded-xl">
            <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider block">Rusak / Tindakan</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-xl font-bold text-rose-800 font-mono">{{ $totalRusak }}</span>
                <span class="text-[11px] text-slate-400">perbaikan</span>
            </div>
        </div>

        <div class="p-3.5 bg-white border border-slate-200 rounded-xl col-span-2 sm:col-span-1">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Tidak Aktif</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-xl font-bold text-slate-700 font-mono">{{ $totalTidakAktif }}</span>
                <span class="text-[11px] text-slate-400">arsip / diganti</span>
            </div>
        </div>
    </div>

    <!-- Container Utama: Toolbar, Tabel, dan Pagination -->
    <x-card class="!p-0 overflow-hidden">
        <!-- 2. Toolbar Pencarian & Filter Sederhana -->
        <form method="GET" action="{{ route('apar.index') }}" class="p-4 sm:p-5 border-b border-slate-200 bg-white">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[240px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari kode, register, merek, lokasi, atau keterangan..."
                        class="w-full pl-9 pr-3 py-2 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none transition min-h-[44px]" />
                </div>

                <!-- Filters & Reset -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Filter Gedung -->
                    <div class="relative">
                        <select 
                            name="gedung_id" 
                            id="filter_gedung_id"
                            onchange="this.form.submit()"
                            class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-2 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[44px] cursor-pointer">
                            <option value="">Semua Gedung</option>
                            @foreach($gedungs as $g)
                                <option value="{{ $g->id }}" {{ $gedungId == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>

                    <!-- Filter Lantai -->
                    <div class="relative">
                        <select 
                            name="lantai_id" 
                            id="filter_lantai_id"
                            onchange="this.form.submit()"
                            class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-2 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[44px] cursor-pointer">
                            <option value="">Semua Lantai</option>
                            @if($gedungId && $lantaisForFilter->isNotEmpty())
                                @foreach($lantaisForFilter as $l)
                                    <option value="{{ $l->id }}" {{ $lantaiId == $l->id ? 'selected' : '' }}>
                                        {{ $l->nama }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>

                    <!-- Filter Status -->
                    <div class="relative">
                        <select 
                            name="status" 
                            onchange="this.form.submit()"
                            class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-2 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[44px] cursor-pointer">
                            <option value="">Semua Kondisi</option>
                            @foreach(\App\Models\Apar::STATUS_LABELS as $key => $lbl)
                                <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center px-3 py-2 bg-slate-900 text-white rounded-lg text-xs font-semibold hover:bg-slate-800 transition min-h-[44px] focus:outline-none focus:ring-2 focus:ring-slate-900">
                        Cari
                    </button>

                    @if($search || $gedungId || $lantaiId || $status || $jenisMedia)
                        <a href="{{ route('apar.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition min-h-[44px]">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

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
                            </td>

                            <!-- Lokasi Berjenjang -->
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">
                                    {{ $apar->ruangan?->nama ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $apar->gedung?->nama ?? '-' }} &bull; {{ $apar->lantai?->nama ?? '-' }}
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
                                <div class="text-slate-800">{{ $apar->merek ?: '-' }}</div>
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
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition-colors min-h-[36px] focus:outline-none focus:ring-2 focus:ring-slate-400">
                                        Detail
                                    </a>
                                    <a href="{{ route('apar.edit', $apar->id) }}" 
                                       class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition-colors min-h-[36px] focus:outline-none focus:ring-2 focus:ring-slate-400">
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
                                    @if($search || $gedungId || $lantaiId || $status)
                                        <h4 class="text-sm font-bold text-slate-800">Tidak ada unit APAR yang cocok</h4>
                                        <p class="text-xs text-slate-500 mt-1 mb-4">
                                            Tidak ditemukan data unit APAR dengan parameter pencarian atau filter yang Anda pilih.
                                        </p>
                                        <a href="{{ route('apar.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition min-h-[44px]">
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