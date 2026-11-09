<x-layouts.app title="HAFIS : Detail APAR {{ $apar->kode }}">
    <!-- 1. Page Header with Breadcrumb & Kode Identitas -->
    <div class="pb-4 mb-6 border-b border-gray-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2.5 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('apar.index') }}" class="hover:text-gray-900 transition-colors">Data APAR</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium font-mono">{{ $apar->kode }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Header Kiri: Back arrow + Kode APAR + Badge Status + Sub-line Lokasi -->
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('apar.index') }}" 
                       class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 border border-gray-200 transition shrink-0 min-h-[40px] min-w-[40px]" 
                       title="Kembali ke Data APAR"
                       aria-label="Kembali ke Data APAR">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <h1 class="font-mono text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">
                        {{ $apar->kode }}
                    </h1>
                    <x-badge :variant="$apar->status_badge_variant" :dot="true">
                        {{ $apar->status_label }}
                    </x-badge>
                </div>
                <p class="text-xs text-gray-500 flex items-center gap-1.5 pl-10.5">
                    <span>{{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? 'Area Umum' }}</span>
                    <span>&bull;</span>
                    <span>{{ $apar->lokasi ? ($apar->lokasi->keterangan ?? 'Titik Master K3RS') : ('Lantai ' . ($apar->lantai?->nomor_lantai ?? '-') . ' ' . ($apar->gedung?->nama ?? '')) }}</span>
                </p>
            </div>

            <!-- Header Kanan Actions: Ganti APAR, Cetak QR, Titik Denah, Edit Data, Mulai Pemeriksaan -->
            <div class="flex flex-wrap items-center gap-2">
                @if($apar->can_be_replaced)
                    <a href="{{ route('apar.ganti', $apar->id) }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg text-amber-800 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition min-h-[40px]">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Ganti APAR</span>
                    </a>
                @endif

                <a href="{{ route('apar.edit', $apar->id) }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition min-h-[40px]">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span>Edit Data</span>
                </a>

                @if($apar->status === 'tidak_aktif')
                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed min-h-[40px]" title="APAR berstatus tidak aktif tidak dapat diperiksa">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        <span>Tidak Aktif</span>
                    </span>
                @elseif($apar->lokasi && $apar->lokasi->status === \App\Models\Lokasi::STATUS_STOK)
                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed min-h-[40px]" title="APAR berada di gudang stok sediaan">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Stok Sediaan</span>
                    </span>
                @else
                    @php
                        $selectedBulan = (int) request('bulan', request('periode_bulan', Carbon\Carbon::now()->month));
                        $selectedTahun = (int) request('tahun', request('periode_tahun', Carbon\Carbon::now()->year));
                        $periksaParams = [
                            'apar_id' => $apar->id,
                            'periode_bulan' => $selectedBulan,
                            'periode_tahun' => $selectedTahun,
                        ];
                        if (request('jadwal_id')) {
                            $periksaParams['jadwal_id'] = request('jadwal_id');
                        }
                    @endphp
                    <a href="{{ route('pemeriksaan.create', $periksaParams) }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-medium rounded-lg text-white bg-gray-900 hover:bg-gray-800 transition min-h-[40px]">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Mulai Pemeriksaan</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Feedback Alerts: Sukses, Warning, Error -->
    @if(session('success'))
        <div class="mb-6 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 p-3.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-900 text-xs flex items-center justify-between gap-3" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-amber-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-3.5 rounded-xl border border-red-200 bg-red-50 text-red-900 text-xs flex items-center justify-between gap-3" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-red-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 font-bold text-sm leading-none focus:outline-none min-h-[36px] min-w-[36px] flex items-center justify-center" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @if($apar->is_replaced)
        <!-- Banner APAR Lama yang Sudah Diganti -->
        <div class="mb-6 p-4 rounded-xl border border-red-200 bg-red-50/80 text-red-950 text-xs" role="alert">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center shrink-0 mt-0.5 font-bold">
                        !
                    </span>
                    <div>
                        <h4 class="font-semibold text-red-900 text-sm">Unit APAR Ini Sudah Tidak Aktif (Sudah Diganti)</h4>
                        <p class="text-red-800 mt-1 leading-relaxed">
                            Unit tabung fisik ini telah dinonaktifkan karena telah digantikan oleh unit baru 
                            <strong class="font-mono text-red-950">{{ $apar->aparPengganti?->kode ?? 'Unit Baru' }}</strong> 
                            pada tanggal {{ $apar->tanggal_nonaktif?->format('d/m/Y') ?? '-' }}.
                        </p>
                        <p class="text-red-700 text-[11px] mt-1">
                            Seluruh riwayat pemeriksaan, checklist, dan pemeliharaan pada halaman ini tetap tersimpan utuh sebagai historical record unit lama.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if($apar->replaced_by_apar_id)
                        <a href="{{ route('apar.show', $apar->replaced_by_apar_id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-red-700 hover:bg-red-800 text-white font-medium text-xs transition min-h-[40px]">
                            <span>Lihat APAR Pengganti ({{ $apar->aparPengganti?->kode }}) &rarr;</span>
                        </a>
                    @endif
                    @if($apar->penggantianKeluar)
                        <a href="{{ route('penggantian.show', $apar->penggantianKeluar->id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-red-300 bg-white hover:bg-red-50 text-red-900 font-medium text-xs transition min-h-[40px]">
                            <span>Detail Penggantian</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @elseif($apar->is_replacement)
        <!-- Banner APAR Baru Pengganti -->
        <div class="mb-6 p-4 rounded-xl border border-blue-200 bg-blue-50/80 text-blue-950 text-xs" role="alert">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <div>
                        <h4 class="font-semibold text-blue-900 text-sm">Unit APAR Pengganti Aktif</h4>
                        <p class="text-blue-800 mt-1 leading-relaxed">
                            Unit tabung ini adalah APAR aktif yang menggantikan unit lama 
                            <strong class="font-mono text-blue-950">{{ $apar->aparDigantikan?->kode ?? 'Unit Lama' }}</strong>.
                        </p>
                        <p class="text-blue-700 text-[11px] mt-0.5">
                            Histori pemeriksaan dan checklist dimulai baru secara mandiri untuk unit ini.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if($apar->replaces_apar_id)
                        <a href="{{ route('apar.show', $apar->replaces_apar_id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-blue-300 bg-white hover:bg-blue-50 text-blue-900 font-medium text-xs transition min-h-[40px]">
                            <span>Lihat Histori Unit Lama ({{ $apar->aparDigantikan?->kode }}) &rarr;</span>
                        </a>
                    @endif
                    @if($apar->penggantianMasuk)
                        <a href="{{ route('penggantian.show', $apar->penggantianMasuk->id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-medium text-xs transition min-h-[40px]">
                            <span>Detail Penggantian</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @elseif($apar->status === 'tidak_aktif')
        <!-- Status Tidak Aktif Banner -->
        <div class="mb-6 p-4 rounded-xl border border-red-200 bg-red-50 text-red-900 text-xs flex items-start gap-3" role="alert">
            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h4 class="font-semibold text-red-900">Perhatian: APAR Berstatus Tidak Aktif</h4>
                <p class="text-red-700 mt-0.5 leading-relaxed">
                    Unit tabung ini dinonaktifkan atau dalam status afkir/gudang, sehingga tidak dapat diperiksa dan tidak dianggap sebagai target pemeriksaan aktif.
                </p>
            </div>
        </div>
    @else
        <!-- Banner Status Jadwal Inspeksi Aktif (Hanya Menampilkan Informasi Jadwal) -->
        @php
            $activeJadwalUnit = $apar->active_jadwal;
        @endphp
        @if($activeJadwalUnit)
            <div class="flex mb-6 p-3.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-950 text-xs items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-semibold text-blue-950">Jadwal Inspeksi Aktif: {{ $activeJadwalUnit->nama }}</span>
                        <span class="text-blue-700 block text-[11px] font-mono mt-0.5">
                            Periode {{ $activeJadwalUnit->tanggal_mulai->format('d/m/Y') }} - {{ $activeJadwalUnit->tanggal_selesai->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
        @else
            <div class="flex mb-6 p-3.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-950 text-xs items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-semibold text-amber-950">APAR ini belum memiliki jadwal inspeksi aktif.</span>
                        <span class="text-amber-700 block text-[11px] mt-0.5">
                            Pemeriksaan mandiri (ad-hoc) tetap dapat dimulai jika diperlukan oleh SOP K3RS Rumah Sakit.
                        </span>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Grid Detail Informasi & Riwayat APAR: Kolom Kiri (8/12) & Kolom Kanan (4/12) - Reordered on Mobile for F-06 -->
    <div class="flex flex-col lg:grid lg:grid-cols-12 gap-6 items-start">

        <!-- ==================================================================
             KOLOM KIRI (8/12 Desktop, Order 2 Mobile): Spesifikasi Teknis, Riwayat
             ================================================================== -->
        <div class="order-2 lg:order-1 lg:col-span-8 space-y-6 w-full">

            <!-- Card 1: Identitas & Spesifikasi Teknis -->
            <x-card title="Spesifikasi Teknis APAR" subtitle="Informasi tabung dan media pemadam yang terdaftar di rumah sakit.">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                    <!-- Kode APAR -->
                    <div class="pb-3 border-b border-gray-100">
                        <dt class="font-medium text-gray-500 mb-1">Kode Tabung APAR</dt>
                        <dd class="font-mono font-semibold text-gray-900 text-sm tabular-nums">{{ $apar->kode }}</dd>
                    </div>

                    <!-- Nomor Register / Aset -->
                    <div class="pb-3 border-b border-gray-100">
                        <dt class="font-medium text-gray-500 mb-1">Nomor Register RS</dt>
                        <dd class="font-mono font-medium text-gray-900 text-sm">
                            {{ $apar->nomor_register ?: 'Belum dicatat' }}
                        </dd>
                    </div>

                    <!-- Jenis Media -->
                    <div class="pb-3 border-b border-gray-100">
                        <dt class="font-medium text-gray-500 mb-1">Jenis Media Pemadam</dt>
                        <dd class="font-medium text-gray-900 text-sm">{{ $apar->jenis_media }}</dd>
                    </div>

                    <!-- Kapasitas Tabung -->
                    <div class="pb-3 border-b border-gray-100">
                        <dt class="font-medium text-gray-500 mb-1">Kapasitas Tabung</dt>
                        <dd class="font-medium text-gray-900 text-sm font-mono">{{ $apar->kapasitas }}</dd>
                    </div>

                    <!-- Merek -->
                    <div class="pb-3 border-b border-gray-100">
                        <dt class="font-medium text-gray-500 mb-1">Merek Tabung</dt>
                        <dd class="font-medium text-gray-900 text-sm {{ ! $apar->merek ? 'text-gray-400 italic' : '' }}">{{ $apar->merek ?: 'Belum terdata' }}</dd>
                    </div>

                    <!-- Tipe / Model -->
                    <div class="pb-3 border-b border-gray-100">
                        <dt class="font-medium text-gray-500 mb-1">Tipe / Model Fisik</dt>
                        <dd class="font-medium text-gray-900 text-sm">{{ $apar->tipe_model ?: '-' }}</dd>
                    </div>
                </dl>
            </x-card>

            <!-- Card 2: Lokasi Penempatan Fisik -->
            <x-card title="Lokasi Penempatan" subtitle="Posisi fisik tabung mengikuti hierarki Gedung, Lantai, dan Ruangan atau Area.">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-4 text-xs mb-4">
                    <!-- Gedung -->
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                        <dt class="font-medium text-gray-500 text-[11px] uppercase tracking-wider mb-1">Gedung</dt>
                        <dd class="font-semibold text-gray-900 text-sm">
                            {{ $apar->gedung?->nama ?? '-' }}
                        </dd>
                        <div class="text-[11px] text-gray-400 font-mono mt-0.5">
                            Kode: {{ $apar->gedung?->kode ?? '-' }}
                        </div>
                    </div>

                    <!-- Lantai -->
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                        <dt class="font-medium text-gray-500 text-[11px] uppercase tracking-wider mb-1">Lantai</dt>
                        <dd class="font-semibold text-gray-900 text-sm">
                            {{ $apar->lantai?->nama ?? '-' }}
                        </dd>
                        <div class="text-[11px] text-gray-400 mt-0.5">
                            Nomor Lantai: <span class="font-mono">{{ $apar->lantai?->nomor_lantai ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Ruangan / Area -->
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                        <dt class="font-medium text-gray-500 text-[11px] uppercase tracking-wider mb-1">Lokasi / Ruangan</dt>
                        <dd class="font-semibold text-gray-900 text-sm">
                            {{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}
                        </dd>
                        <div class="text-[11px] text-gray-400 mt-0.5">
                            {{ $apar->lokasi ? ($apar->lokasi->keterangan ?? 'Titik Master K3RS') : ($apar->ruangan?->kategori ?: 'Area umum') }}
                        </div>
                    </div>
                </dl>

                <!-- Posisi Detail -->
                <div class="pt-3 border-t border-gray-100 text-xs">
                    <span class="block font-medium text-gray-500 mb-1">Detail Posisi Spesifik</span>
                    <p class="text-gray-800 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-200">
                        {{ $apar->posisi_detail ?: 'Belum ada catatan detail posisi bracket/dinding.' }}
                    </p>
                </div>
            </x-card>

            <!-- Card 3: Informasi Waktu & Catatan -->
            <x-card title="Informasi Waktu & Catatan" subtitle="Tanggal operasional tabung dan catatan khusus K3RS.">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-xs mb-4">
                    <div class="pb-2 border-b border-gray-100 sm:border-b-0">
                        <dt class="font-medium text-gray-500 mb-1">Tanggal Pemasangan</dt>
                        <dd class="font-mono font-medium text-gray-900">
                            {{ $apar->tanggal_pemasangan ? $apar->tanggal_pemasangan->format('d/m/Y') : 'Belum dicatat' }}
                        </dd>
                    </div>

                    <div class="pb-2 border-b border-gray-100 sm:border-b-0">
                        <dt class="font-medium text-gray-500 mb-1">Kedaluwarsa Media</dt>
                        <dd class="font-mono font-medium text-gray-900">
                            {{ $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format('d/m/Y') : 'Belum dicatat' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-medium text-gray-500 mb-1">Pemeriksaan Terakhir</dt>
                        <dd class="font-mono font-medium text-gray-900">
                            {{ $apar->tanggal_pemeriksaan_terakhir ? $apar->tanggal_pemeriksaan_terakhir->format('d/m/Y') : 'Belum ada data' }}
                        </dd>
                    </div>
                </dl>

                @if($apar->keterangan)
                    <div class="pt-3 border-t border-gray-100 text-xs">
                        <span class="block font-medium text-gray-500 mb-1">Catatan Tambahan</span>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line bg-gray-50 p-3 rounded-lg border border-gray-200">
                            {{ $apar->keterangan }}
                        </p>
                    </div>
                @endif
            </x-card>

            <!-- Card D: Tabel Log Pemeriksaan APAR Lengkap -->
            <x-card title="Log Pemeriksaan APAR" subtitle="Riwayat seluruh verifikasi fisik berkala yang pernah dilakukan untuk unit ini.">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-500 text-xs font-medium uppercase tracking-wider">
                                <th class="px-4 py-3 whitespace-nowrap">Tanggal Pemeriksaan</th>
                                <th class="px-4 py-3">Jadwal Inspeksi</th>
                                <th class="px-4 py-3 whitespace-nowrap">Pemeriksa</th>
                                <th class="px-4 py-3 text-center whitespace-nowrap">Hasil</th>
                                <th class="px-4 py-3">Catatan Singkat</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                            @php
                                $logPemeriksaans = $apar->pemeriksaans;
                            @endphp
                            @forelse($logPemeriksaans as $p)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="px-4 py-3 font-mono font-medium text-gray-900 whitespace-nowrap tabular-nums">
                                        {{ $p->tanggal_pemeriksaan->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($p->jadwal)
                                            <a href="{{ route('jadwal.show', $p->jadwal->id) }}" class="font-medium text-gray-900 hover:underline">
                                                {{ $p->jadwal->nama }}
                                            </a>
                                        @else
                                            <span class="text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 text-[11px] font-medium">
                                                Pemeriksaan Mandiri (Ad-Hoc)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $p->pemeriksa_nama }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <x-badge :variant="$p->hasil_badge_variant" :dot="true">
                                            {{ $p->hasil_label }}
                                        </x-badge>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 max-w-xs truncate">
                                        {{ $p->catatan ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('pemeriksaan.show', $p->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white font-medium text-xs transition min-h-[36px]">
                                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span>Lihat Checklist</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">
                                        Belum ada log pemeriksaan fisik untuk unit APAR ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Card E: Tabel Riwayat Tindak Lanjut & Pemeliharaan APAR -->
            <x-card title="Riwayat Tindak Lanjut & Pemeliharaan APAR" subtitle="Catatan historis seluruh perbaikan, pembersihan, dan pemeliharaan komponen unit APAR ini.">
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-500 text-xs font-medium uppercase tracking-wider">
                                <th class="px-4 py-3 whitespace-nowrap">Tanggal Tindakan</th>
                                <th class="px-4 py-3 whitespace-nowrap">Jenis Tindakan</th>
                                <th class="px-4 py-3">Sumber Pemeriksaan</th>
                                <th class="px-4 py-3">Temuan Masalah</th>
                                <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                                <th class="px-4 py-3 whitespace-nowrap">Pelaksana</th>
                                <th class="px-4 py-3 whitespace-nowrap">Tanggal Selesai</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                            @php
                                $riwayatTindakLanjuts = $apar->tindakLanjuts;
                            @endphp
                            @forelse($riwayatTindakLanjuts as $tl)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="px-4 py-3 font-mono font-medium text-gray-900 whitespace-nowrap tabular-nums">
                                        {{ $tl->tanggal_tindakan->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">
                                        {{ $tl->jenis_label }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($tl->pemeriksaan)
                                            <a href="{{ route('pemeriksaan.show', $tl->pemeriksaan_id) }}" class="font-medium text-gray-900 hover:underline">
                                                Inspeksi {{ $tl->pemeriksaan->tanggal_pemeriksaan->format('d/m/Y') }}
                                            </a>
                                            <span class="text-gray-400 block text-[10px]">Oleh: {{ $tl->pemeriksaan->pemeriksa_nama }}</span>
                                        @else
                                            <span class="text-gray-400 italic text-[11px]">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 max-w-xs">
                                        <span class="font-medium text-gray-900 block truncate" title="{{ $tl->temuan_teks }}">
                                            {{ $tl->temuan_teks }}
                                        </span>
                                        <span class="text-gray-500 block text-[10px] truncate" title="{{ $tl->uraian_tindakan }}">
                                            Tindakan: {{ $tl->uraian_tindakan }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <x-badge :variant="$tl->status_badge_variant" :dot="true">
                                            {{ $tl->status_label }}
                                        </x-badge>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">
                                        {{ $tl->petugas_pelaksana }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-gray-700 whitespace-nowrap tabular-nums">
                                        @if($tl->tanggal_selesai)
                                            <span class="text-emerald-800 font-medium">{{ $tl->tanggal_selesai->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-gray-400 italic text-[11px]">Belum selesai</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('tindak-lanjut.show', $tl->id) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white font-medium text-xs transition min-h-[36px]">
                                                <span>Detail</span>
                                            </a>
                                            @if($tl->status !== 'selesai')
                                                <a href="{{ route('tindak-lanjut.edit', $tl->id) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-medium text-xs transition min-h-[36px]">
                                                    <span>Update</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400 italic">
                                        Belum ada riwayat tindak lanjut atau pemeliharaan untuk unit APAR ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Card F: Tabel Riwayat Penggantian APAR (jika ada) -->
            @if($apar->is_replaced || $apar->is_replacement)
                <x-card title="Riwayat Penggantian APAR" subtitle="Keterhubungan penelusuran unit pengganti dan unit yang digantikan untuk audit keselamatan RS.">
                    <div class="space-y-4 text-xs">
                        @if($apar->is_replaced && $apar->penggantianKeluar)
                            @php $pk = $apar->penggantianKeluar; @endphp
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 font-medium">Digantikan oleh:</span>
                                        <a href="{{ route('apar.show', $pk->apar_baru_id) }}" class="font-mono font-semibold text-gray-900 bg-white border border-gray-200 px-2 py-0.5 rounded text-sm hover:underline">
                                            {{ $pk->aparBaru?->kode }}
                                        </a>
                                        <x-badge variant="success" :dot="true">APAR Baru Aktif</x-badge>
                                    </div>
                                    <span class="font-mono text-gray-600 text-xs">
                                        Tanggal Penggantian: {{ $pk->tanggal_penggantian->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3">
                                    <div>
                                        <span class="text-gray-500 block font-medium">Alasan Penggantian:</span>
                                        <span class="text-gray-900 font-medium">{{ $pk->alasan_label }}</span>
                                        @if($pk->alasan_keterangan)
                                            <span class="text-[11px] text-gray-500 block">({{ $pk->alasan_keterangan }})</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block font-medium">Petugas:</span>
                                        <span class="text-gray-900 font-medium">{{ $pk->petugas_nama }}</span>
                                    </div>
                                    <div class="sm:text-right">
                                        <a href="{{ route('penggantian.show', $pk->id) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white font-medium text-xs transition min-h-[36px]">
                                            <span>Rincian Penggantian &rarr;</span>
                                        </a>
                                    </div>
                                </div>
                                @if($pk->catatan)
                                    <div class="mt-3 pt-2.5 border-t border-gray-200 text-gray-600">
                                        <span class="font-medium text-gray-700">Catatan:</span> {{ $pk->catatan }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($apar->is_replacement && $apar->penggantianMasuk)
                            @php $pm = $apar->penggantianMasuk; @endphp
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 font-medium">Menggantikan Unit:</span>
                                        <a href="{{ route('apar.show', $pm->apar_lama_id) }}" class="font-mono font-semibold text-gray-900 bg-white border border-gray-200 px-2 py-0.5 rounded text-sm hover:underline">
                                            {{ $pm->aparLama?->kode }}
                                        </a>
                                        <x-badge variant="neutral" :dot="true">APAR Lama Dinonaktifkan</x-badge>
                                    </div>
                                    <span class="font-mono text-gray-600 text-xs">
                                        Tanggal Penggantian: {{ $pm->tanggal_penggantian->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3">
                                    <div>
                                        <span class="text-gray-500 block font-medium">Alasan Penggantian:</span>
                                        <span class="text-gray-900 font-medium">{{ $pm->alasan_label }}</span>
                                        @if($pm->alasan_keterangan)
                                            <span class="text-[11px] text-gray-500 block">({{ $pm->alasan_keterangan }})</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block font-medium">Petugas:</span>
                                        <span class="text-gray-900 font-medium">{{ $pm->petugas_nama }}</span>
                                    </div>
                                    <div class="sm:text-right">
                                        <a href="{{ route('penggantian.show', $pm->id) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white font-medium text-xs transition min-h-[36px]">
                                            <span>Rincian Penggantian &rarr;</span>
                                        </a>
                                    </div>
                                </div>
                                @if($pm->catatan)
                                    <div class="mt-3 pt-2.5 border-t border-gray-200 text-gray-600">
                                        <span class="font-medium text-gray-700">Catatan:</span> {{ $pm->catatan }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif
        </div>

        <!-- ==================================================================
             KOLOM KANAN (4/12 Desktop, Order 1 Mobile): QR Code, Status Kesiapan
             ================================================================== -->
        <div class="order-1 lg:order-2 lg:col-span-4 space-y-6 w-full">

            <!-- Card A: QR Code APAR -->
            <x-card title="QR Code APAR" subtitle="Identitas fisik unik untuk verifikasi scan di lapangan.">
                <div class="text-center p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <!-- Dynamic Vector QR SVG -->
                    <div id="apar-qr-preview-box" class="mx-auto w-40 h-40 bg-white p-2.5 rounded-lg border border-gray-200 flex items-center justify-center">
                        {!! $apar->qr_svg !!}
                    </div>

                    <div class="mt-3">
                        <span class="text-[10px] uppercase font-medium text-gray-400 tracking-wider block">Identifier Unik</span>
                        <div class="font-mono text-xs font-semibold text-gray-900 break-all select-all mt-0.5 bg-white py-1.5 px-2 rounded border border-gray-200">
                            {{ $apar->qr_code }}
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-2.5 leading-relaxed text-left">
                        QR Code ini dicetak dan ditempelkan pada fisik tabung APAR untuk mempermudah identifikasi saat inspeksi.
                    </p>

                    <div class="mt-4 pt-3 border-t border-gray-200 flex flex-wrap items-center justify-center gap-2">
                        <a href="{{ route('apar.cetak_qr', $apar->id) }}" target="_blank"
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition min-h-[40px]">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            <span>Cetak Label QR</span>
                        </a>

                        <button type="button" id="btn-unduh-png-show" onclick="unduhLabelPngShow()"
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition min-h-[40px] cursor-pointer" title="Unduh gambar stiker label lengkap format PNG">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span>Unduh PNG</span>
                        </button>
                    </div>
                </div>
            </x-card>

            <!-- Card B: Status Kesiapan -->
            <x-card title="Status Kesiapan" subtitle="Kondisi tabung saat ini.">
                <div class="space-y-3.5 text-xs">
                    <div>
                        <span class="block text-gray-500 font-medium mb-1">Status Operasional</span>
                        <x-badge :variant="$apar->status_badge_variant" :dot="true">
                            {{ $apar->status_label }}
                        </x-badge>
                    </div>

                    <div class="pt-3 border-t border-gray-100">
                        <span class="block text-gray-500 font-medium mb-1">Pemeriksaan Fisik Terakhir</span>
                        @if($apar->tanggal_pemeriksaan_terakhir)
                            <span class="font-mono font-medium text-gray-900 block tabular-nums">
                                {{ $apar->tanggal_pemeriksaan_terakhir->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-gray-400 italic block">Belum ada catatan pemeriksaan</span>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-gray-100">
                        <span class="block text-gray-500 font-medium mb-1">Masa Berlaku Media</span>
                        @if($apar->tanggal_kadaluarsa)
                            <span class="font-mono font-medium text-gray-900 block tabular-nums">
                                {{ $apar->tanggal_kadaluarsa->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-gray-400 italic block">Belum dicatat</span>
                        @endif
                    </div>
                </div>
            </x-card>
        </div>

    </div>

    <!-- Script Unduh PNG Label APAR Langsung dari Halaman Detail -->
    <script>
        async function unduhLabelPngShow() {
            const btn = document.getElementById('btn-unduh-png-show');
            const originalContent = btn.innerHTML;
            btn.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin text-slate-700 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Menyiapkan...</span>`;
            btn.disabled = true;

            try {
                const scale = 3; // 3x HD Retina Resolution (300 DPI)
                const w = 350;
                const hasPosisi = {{ ($apar->posisi_detail && $apar->ruangan?->nama) ? 'true' : 'false' }};
                const h = hasPosisi ? 445 : 420;

                const canvas = document.createElement('canvas');
                canvas.width = w * scale;
                canvas.height = h * scale;
                const ctx = canvas.getContext('2d');
                ctx.scale(scale, scale);

                // 1. Background Putih Murni
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, w, h);

                // 2. Border Luar Tunggal (Solid Navy Slate 2px)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.strokeRect(1, 1, w - 2, h - 2);

                // 3. Header Institusional Sederhana
                ctx.textAlign = 'center';
                
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 8.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('RSUD DR. H. MOCH. ANSARI SALEH', w / 2, 20);

                ctx.fillStyle = '#0f172a';
                ctx.font = '900 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('INSTALASI K3RS • KESELAMATAN & KESEHATAN KERJA', w / 2, 34);

                ctx.fillStyle = '#94a3b8';
                ctx.font = '600 8px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('LABEL INVENTARIS PROTEKSI KEBAKARAN', w / 2, 45);

                // Garis Pembatas Header (Navy Slate 2px)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(0, 52);
                ctx.lineTo(w, 52);
                ctx.stroke();

                // 4. Identitas Utama: Kode Unit APAR Dominan
                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 8.5px ui-monospace, SFMono-Regular, Menlo, monospace';
                ctx.fillText('KODE UNIT APAR', w / 2, 70);

                ctx.fillStyle = '#020617';
                ctx.font = '900 22px ui-monospace, SFMono-Regular, Menlo, monospace';
                ctx.fillText('{{ $apar->kode }}', w / 2, 94);

                // 5. QR Code dengan Quiet Zone Bersih (Diperbesar Optimal)
                const svgElement = document.querySelector('#apar-qr-preview-box svg');
                const qrSize = 152;
                if (svgElement) {
                    const svgXml = new XMLSerializer().serializeToString(svgElement);
                    const svgUri = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgXml);
                    const qrImg = new Image();
                    await new Promise((resolve) => {
                        qrImg.onload = resolve;
                        qrImg.onerror = resolve;
                        qrImg.src = svgUri;
                    });
                    // Gambar QR Code 152x152 di tengah kartu
                    ctx.drawImage(qrImg, (w - qrSize) / 2, 98, qrSize, qrSize);
                }

                ctx.fillStyle = '#94a3b8';
                ctx.font = '600 8px ui-monospace, monospace';
                ctx.fillText('PINDAI UNTUK RIWAYAT & LOG INSPEKSI', w / 2, 260);

                // Garis Pembatas Spesifikasi (Navy Slate 2px)
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(0, 268);
                ctx.lineTo(w, 268);
                ctx.stroke();

                // 6. Spesifikasi Dua Kolom (Tanpa Kotak Bertumpuk)
                let y = 282;
                const col1X = 18;
                const col2X = 185;

                // Baris 1: Media vs Kapasitas
                ctx.textAlign = 'left';
                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 8px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('MEDIA PEMADAM', col1X, y);
                ctx.fillText('BERAT / KAPASITAS', col2X, y);

                y += 12;
                ctx.fillStyle = '#0f172a';
                ctx.font = 'bold 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('{{ $apar->jenis_media }}', col1X, y);

                ctx.font = 'bold 11px ui-monospace, monospace';
                ctx.fillText('{{ $apar->kapasitas }}', col2X, y);

                // Garis Pemisah Antar Baris Spesifikasi (Hairline halus)
                y += 9;
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(col1X, y);
                ctx.lineTo(w - col1X, y);
                ctx.stroke();

                // Baris 2: Gedung vs Ruangan
                y += 12;
                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 8px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('LOKASI GEDUNG', col1X, y);
                ctx.fillText('RUANGAN / AREA', col2X, y);

                y += 12;
                ctx.fillStyle = '#0f172a';
                ctx.font = '600 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                let gedungText = '{{ $apar->gedung?->nama ?? "-" }} (Lt. {{ $apar->lantai?->nomor_lantai ?? "-" }})';
                if (ctx.measureText(gedungText).width > 150) {
                    gedungText = gedungText.slice(0, 20) + '...';
                }
                ctx.fillText(gedungText, col1X, y);

                let ruangText = '{{ $apar->ruangan?->nama ?? $apar->posisi_detail ?? "-" }}';
                if (ctx.measureText(ruangText).width > 150) {
                    ruangText = ruangText.slice(0, 20) + '...';
                }
                ctx.fillText(ruangText, col2X, y);

                @if($apar->posisi_detail && $apar->ruangan?->nama)
                    y += 8;
                    ctx.strokeStyle = '#f1f5f9';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(col1X, y);
                    ctx.lineTo(w - col1X, y);
                    ctx.stroke();

                    y += 11;
                    ctx.fillStyle = '#64748b';
                    ctx.font = 'italic 9.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                    ctx.fillText('Posisi: {{ $apar->posisi_detail }}', col1X, y);
                @endif

                // Garis Pembatas Metadata (Navy Slate 2px)
                y += 13;
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(w, y);
                ctx.stroke();

                // 7. Metadata Tanggal Pemasangan & Kedaluwarsa Sederhana (Merah hanya untuk peringatan)
                y += 14;
                @php
                    $isExpiredShow = $apar->tanggal_kadaluarsa && $apar->tanggal_kadaluarsa->isPast();
                    $isNearExpShow = $apar->tanggal_kadaluarsa && !$isExpiredShow && $apar->tanggal_kadaluarsa->diffInDays(now()) <= 30;
                @endphp
                const isExpired = {{ $isExpiredShow ? 'true' : 'false' }};
                const isNearExp = {{ $isNearExpShow ? 'true' : 'false' }};

                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 8px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                ctx.fillText('TGL PEMASANGAN', col1X, y);

                ctx.fillStyle = isExpired ? '#b91c1c' : (isNearExp ? '#d97706' : '#94a3b8');
                ctx.fillText('KEDALUWARSA (EXP)', col2X, y);

                y += 13;
                ctx.fillStyle = '#1e293b';
                ctx.font = 'bold 11px ui-monospace, monospace';
                ctx.fillText('{{ $apar->tanggal_pemasangan ? $apar->tanggal_pemasangan->format("d/m/Y") : "-" }}', col1X, y);

                ctx.fillStyle = isExpired ? '#b91c1c' : (isNearExp ? '#b45309' : '#1e293b');
                ctx.font = (isExpired || isNearExp ? '900 ' : 'bold ') + '11px ui-monospace, monospace';
                let expText = '{{ $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format("d/m/Y") : "-" }}';
                if (isExpired) {
                    expText += ' (EXP)';
                }
                ctx.fillText(expText, col2X, y);

                // Eksekusi Download Otomatis
                const downloadLink = document.createElement('a');
                downloadLink.download = 'Label-APAR-{{ $apar->kode }}.png';
                downloadLink.href = canvas.toDataURL('image/png', 1.0);
                downloadLink.click();
            } catch (error) {
                console.error('Error generate PNG:', error);
                alert('Terjadi kesalahan saat membuat file gambar PNG.');
            } finally {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        }
    </script>
</x-layouts.app>