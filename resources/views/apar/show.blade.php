<x-layouts.app title="HAFIS : Detail APAR {{ $apar->kode }}">
    <!-- 1. Page Header with Breadcrumb & Kode Identitas -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('apar.index') }}" class="hover:text-slate-800 transition-colors">Master Data APAR</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">{{ $apar->kode }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Unit APAR:
                </h1>
                <span class="font-mono text-base sm:text-lg font-extrabold text-slate-900 bg-white border border-slate-200 px-3 py-1 rounded-lg tabular-nums shadow-2xs">
                    {{ $apar->kode }}
                </span>
                <x-badge :variant="$apar->status_badge_variant" :dot="true">
                    {{ $apar->status_label }}
                </x-badge>
            </div>

            <!-- Header Actions: Mulai Pemeriksaan, Ganti APAR, Cetak QR, Titik Denah, Edit, Kembali -->
            <div class="flex flex-wrap items-center gap-2">
                @if($apar->can_be_replaced)
                    <a href="{{ route('apar.ganti', $apar->id) }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-amber-900 bg-amber-50 border border-amber-300 hover:bg-amber-100 hover:border-amber-400 transition min-h-[44px]">
                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Ganti APAR</span>
                    </a>
                @endif

                @if($apar->status === 'tidak_aktif')
                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed min-h-[44px]" title="APAR berstatus tidak aktif tidak dapat diperiksa">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        <span>Tidak Aktif</span>
                    </span>
                @else
                    <a href="{{ route('pemeriksaan.mulai', ['apar_id' => $apar->id, 'jadwal_id' => request('jadwal_id') ?? $apar->active_jadwal?->id]) }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition shadow-2xs min-h-[44px]">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Mulai Pemeriksaan</span>
                    </a>
                @endif

                <a href="{{ route('apar.cetak_qr', $apar->id) }}" target="_blank"
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak QR</span>
                </a>

                <a href="{{ url('/penempatan?lantai=' . ($apar->lantai?->nomor_lantai ?? 1) . '&highlight=' . $apar->kode) }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Titik Denah</span>
                </a>

                <a href="{{ route('apar.edit', $apar->id) }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span>Edit Data</span>
                </a>

                <a href="{{ route('apar.index') }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Feedback Alerts: Sukses, Warning, Error -->
    @if(session('success'))
        <div class="mb-6 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
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

    @if(session('warning'))
        <div class="mb-6 p-3.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-amber-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900 font-bold text-sm leading-none focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold text-sm leading-none focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    @if($apar->is_replaced)
        <!-- Banner APAR Lama yang Sudah Diganti (Tahap 8, Section 3, 21, 22) -->
        <div class="mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50/90 text-rose-950 text-xs shadow-2xs" role="alert">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5 font-bold">
                        !
                    </span>
                    <div>
                        <h4 class="font-bold text-rose-900 text-sm">Unit APAR Ini Sudah Tidak Aktif (Sudah Diganti)</h4>
                        <p class="text-rose-800 mt-1 leading-relaxed">
                            Unit tabung fisik ini telah dinonaktifkan karena telah digantikan oleh unit baru 
                            <strong class="font-mono text-rose-950">{{ $apar->aparPengganti?->kode ?? 'Unit Baru' }}</strong> 
                            pada tanggal {{ $apar->tanggal_nonaktif?->format('d/m/Y') ?? '-' }}.
                        </p>
                        <p class="text-rose-700 text-[11px] mt-1">
                            Seluruh riwayat pemeriksaan, checklist, dan pemeliharaan pada halaman ini tetap tersimpan utuh sebagai historical record unit lama.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if($apar->replaced_by_apar_id)
                        <a href="{{ route('apar.show', $apar->replaced_by_apar_id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-rose-700 hover:bg-rose-800 text-white font-bold text-xs transition shadow-2xs min-h-[44px]">
                            <span>Lihat APAR Pengganti ({{ $apar->aparPengganti?->kode }}) &rarr;</span>
                        </a>
                    @endif
                    @if($apar->penggantianKeluar)
                        <a href="{{ route('penggantian.show', $apar->penggantianKeluar->id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-rose-300 bg-white hover:bg-rose-50 text-rose-900 font-semibold text-xs transition min-h-[44px]">
                            <span>Detail Penggantian</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @elseif($apar->is_replacement)
        <!-- Banner APAR Baru Pengganti (Tahap 8, Section 18) -->
        <div class="mb-6 p-4 rounded-xl border border-sky-200 bg-sky-50/90 text-sky-950 text-xs shadow-2xs" role="alert">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-sky-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <div>
                        <h4 class="font-bold text-sky-900 text-sm">Unit APAR Pengganti Aktif</h4>
                        <p class="text-sky-800 mt-1 leading-relaxed">
                            Unit tabung ini adalah APAR aktif yang menggantikan unit lama 
                            <strong class="font-mono text-sky-950">{{ $apar->aparDigantikan?->kode ?? 'Unit Lama' }}</strong>.
                        </p>
                        <p class="text-sky-700 text-[11px] mt-0.5">
                            Histori pemeriksaan dan checklist dimulai baru secara mandiri untuk unit ini.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if($apar->replaces_apar_id)
                        <a href="{{ route('apar.show', $apar->replaces_apar_id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-sky-300 bg-white hover:bg-sky-50 text-sky-900 font-semibold text-xs transition min-h-[44px]">
                            <span>Lihat Histori Unit Lama ({{ $apar->aparDigantikan?->kode }}) &rarr;</span>
                        </a>
                    @endif
                    @if($apar->penggantianMasuk)
                        <a href="{{ route('penggantian.show', $apar->penggantianMasuk->id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-sky-700 hover:bg-sky-800 text-white font-bold text-xs transition shadow-2xs min-h-[44px]">
                            <span>Detail Penggantian</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @elseif($apar->status === 'tidak_aktif')
        <!-- Status Tidak Aktif Banner (Section 5) -->
        <div class="mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs flex items-start gap-3 shadow-2xs" role="alert">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h4 class="font-bold text-rose-900">Perhatian: APAR Berstatus Tidak Aktif</h4>
                <p class="text-rose-700 mt-0.5 leading-relaxed">
                    Unit tabung ini dinonaktifkan atau dalam status afkir/gudang, sehingga tidak dapat diperiksa dan tidak dianggap sebagai target pemeriksaan aktif.
                </p>
            </div>
        </div>
    @else
        <!-- Banner Status Jadwal Inspeksi Aktif (Section 7) -->
        @php
            $activeJadwalUnit = $apar->active_jadwal;
        @endphp
        @if($activeJadwalUnit)
            <div class="mb-6 p-3.5 rounded-xl border border-sky-200 bg-sky-50 text-sky-950 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-sky-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-bold text-sky-950">Jadwal Inspeksi Aktif: {{ $activeJadwalUnit->nama }}</span>
                        <span class="text-sky-700 block text-[11px] font-mono mt-0.5">
                            Periode {{ $activeJadwalUnit->tanggal_mulai->format('d/m/Y') }} - {{ $activeJadwalUnit->tanggal_selesai->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('pemeriksaan.mulai', ['apar_id' => $apar->id, 'jadwal_id' => $activeJadwalUnit->id]) }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-700 hover:bg-sky-800 text-white font-bold text-xs transition shrink-0 min-h-[44px]">
                    <span>Mulai Pemeriksaan Jadwal</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        @else
            <div class="mb-6 p-3.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-950 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="font-bold text-amber-950">APAR ini belum memiliki jadwal inspeksi aktif.</span>
                        <span class="text-amber-700 block text-[11px] mt-0.5">
                            Pemeriksaan mandiri (ad-hoc) tetap dapat dimulai jika diperlukan oleh SOP K3RS Rumah Sakit.
                        </span>
                    </div>
                </div>
                <a href="{{ route('pemeriksaan.mulai', ['apar_id' => $apar->id]) }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs transition shrink-0 min-h-[44px]">
                    <span>Mulai Pemeriksaan Mandiri</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        @endif
    @endif

    <!-- Grid Detail Informasi: Kolom Kiri (Spesifikasi & Lokasi) & Kolom Kanan (QR Identifier & Status) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- Kolom Kiri (2 Kolom Lebar) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card 1: Identitas & Spesifikasi Teknis -->
            <x-card title="Spesifikasi Teknis APAR" subtitle="Informasi tabung dan media pemadam yang terdaftar di rumah sakit.">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                    <!-- Kode APAR -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Kode Tabung APAR</dt>
                        <dd class="font-mono font-bold text-slate-900 text-sm tabular-nums">{{ $apar->kode }}</dd>
                    </div>

                    <!-- Nomor Register / Aset -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Nomor Register RS</dt>
                        <dd class="font-mono font-medium text-slate-900 text-sm">
                            {{ $apar->nomor_register ?: 'Belum dicatat' }}
                        </dd>
                    </div>

                    <!-- Jenis Media -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Jenis Media Pemadam</dt>
                        <dd class="font-medium text-slate-900 text-sm">{{ $apar->jenis_media }}</dd>
                    </div>

                    <!-- Kapasitas Tabung -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Kapasitas Tabung</dt>
                        <dd class="font-medium text-slate-900 text-sm font-mono">{{ $apar->kapasitas }}</dd>
                    </div>

                    <!-- Merek -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Merek Tabung</dt>
                        <dd class="font-medium text-slate-900 text-sm">{{ $apar->merek ?: '-' }}</dd>
                    </div>

                    <!-- Tipe / Model -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Tipe / Model Fisik</dt>
                        <dd class="font-medium text-slate-900 text-sm">{{ $apar->tipe_model ?: '-' }}</dd>
                    </div>
                </dl>
            </x-card>

            <!-- Card 2: Lokasi Penempatan Fisik -->
            <x-card title="Lokasi Penempatan" subtitle="Posisi fisik tabung mengikuti hierarki Gedung, Lantai, dan Ruangan atau Area.">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-4 text-xs mb-4">
                    <!-- Gedung -->
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <dt class="font-semibold text-slate-500 text-[11px] uppercase tracking-wider mb-1">Gedung</dt>
                        <dd class="font-bold text-slate-900 text-sm">
                            {{ $apar->gedung?->nama ?? '-' }}
                        </dd>
                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                            Kode: {{ $apar->gedung?->kode ?? '-' }}
                        </div>
                    </div>

                    <!-- Lantai -->
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <dt class="font-semibold text-slate-500 text-[11px] uppercase tracking-wider mb-1">Lantai</dt>
                        <dd class="font-bold text-slate-900 text-sm">
                            {{ $apar->lantai?->nama ?? '-' }}
                        </dd>
                        <div class="text-[11px] text-slate-400 mt-0.5">
                            Nomor Lantai: {{ $apar->lantai?->nomor_lantai ?? '-' }}
                        </div>
                    </div>

                    <!-- Ruangan / Area -->
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <dt class="font-semibold text-slate-500 text-[11px] uppercase tracking-wider mb-1">Ruangan / Area</dt>
                        <dd class="font-bold text-slate-900 text-sm">
                            {{ $apar->ruangan?->nama ?? '-' }}
                        </dd>
                        <div class="text-[11px] text-slate-400 mt-0.5">
                            {{ $apar->ruangan?->kategori ?: 'Area umum' }}
                        </div>
                    </div>
                </dl>

                <!-- Posisi Detail -->
                <div class="pt-3 border-t border-slate-100 text-xs">
                    <span class="block font-semibold text-slate-500 mb-1">Detail Posisi Spesifik</span>
                    <p class="text-slate-800 leading-relaxed bg-slate-50/70 p-3 rounded-lg border border-slate-200">
                        {{ $apar->posisi_detail ?: 'Belum ada catatan detail posisi bracket/dinding.' }}
                    </p>
                </div>

                <!-- Status & Titik Penempatan Denah (Tahap 3) -->
                <div class="pt-3 border-t border-slate-100 text-xs">
                    <div class="flex items-center justify-between pb-1.5">
                        <span class="font-semibold text-slate-700">Posisi Titik pada Denah Lantai</span>
                        @if($apar->has_titik)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                Titik Ditentukan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                Posisi denah belum ditentukan
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <div>
                            @if($apar->has_titik)
                                <span class="text-slate-700 font-medium">Koordinat Denah:</span>
                                <span class="font-mono text-slate-900 font-bold ml-1">X: {{ $apar->koordinat_x }}%, Y: {{ $apar->koordinat_y }}%</span>
                                <span class="text-slate-400 block text-[11px] mt-0.5">Relatif terhadap container denah lantai {{ $apar->lantai?->nama }}.</span>
                            @else
                                <span class="text-slate-600 font-medium">Posisi denah belum ditentukan</span>
                                <span class="text-slate-400 block text-[11px] mt-0.5">APAR ini belum ditempatkan pada denah lantai {{ $apar->lantai?->nama ?? 'terkait' }}.</span>
                            @endif
                        </div>
                        <div>
                            <a
                                href="{{ route('penempatan.index', ['gedung_id' => $apar->gedung_id, 'lantai_id' => $apar->lantai_id, 'highlight' => $apar->kode_apar]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $apar->has_titik ? 'bg-slate-900 hover:bg-slate-800 text-white' : 'bg-emerald-700 hover:bg-emerald-800 text-white' }} font-semibold text-xs transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $apar->has_titik ? 'Lihat di Denah' : 'Atur Titik pada Denah' }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Card 3: Informasi Waktu & Catatan -->
            <x-card title="Informasi Waktu & Catatan" subtitle="Tanggal operasional tabung dan catatan khusus K3RS.">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-xs mb-4">
                    <div class="pb-2 border-b border-slate-100 sm:border-b-0">
                        <dt class="font-semibold text-slate-500 mb-1">Tanggal Pemasangan</dt>
                        <dd class="font-mono font-medium text-slate-900">
                            {{ $apar->tanggal_pemasangan ? $apar->tanggal_pemasangan->format('d/m/Y') : 'Belum dicatat' }}
                        </dd>
                    </div>

                    <div class="pb-2 border-b border-slate-100 sm:border-b-0">
                        <dt class="font-semibold text-slate-500 mb-1">Kedaluwarsa Media</dt>
                        <dd class="font-mono font-medium text-slate-900">
                            {{ $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format('d/m/Y') : 'Belum dicatat' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-semibold text-slate-500 mb-1">Pemeriksaan Terakhir</dt>
                        <dd class="font-mono font-medium text-slate-900">
                            {{ $apar->tanggal_pemeriksaan_terakhir ? $apar->tanggal_pemeriksaan_terakhir->format('d/m/Y') : 'Belum ada data' }}
                        </dd>
                    </div>
                </dl>

                @if($apar->keterangan)
                    <div class="pt-3 border-t border-slate-100 text-xs">
                        <span class="block font-semibold text-slate-500 mb-1">Catatan Tambahan</span>
                        <p class="text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/60 p-3 rounded-lg border border-slate-200">
                            {{ $apar->keterangan }}
                        </p>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Kolom Kanan (1 Kolom Samping) -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Card QR Code Identifier (Tahap 5) -->
            <x-card title="QR Code APAR" subtitle="Identitas fisik unik untuk verifikasi scan di lapangan.">
                <div class="text-center p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <!-- Dynamic Vector QR SVG -->
                    <div class="mx-auto w-40 h-40 bg-white p-2.5 rounded-lg border border-slate-300 shadow-2xs flex items-center justify-center">
                        {!! $apar->qr_svg !!}
                    </div>

                    <div class="mt-3">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Identifier Unik</span>
                        <div class="font-mono text-xs font-extrabold text-slate-800 break-all select-all mt-0.5 bg-white py-1.5 px-2 rounded border border-slate-200">
                            {{ $apar->qr_code }}
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-500 mt-2.5 leading-relaxed text-left">
                        QR Code ini dicetak dan ditempelkan pada fisik tabung APAR untuk mempermudah identifikasi saat inspeksi.
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-200 flex flex-wrap items-center justify-center gap-2">
                        <a href="{{ route('apar.cetak_qr', $apar->id) }}" target="_blank"
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[44px]">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            <span>Cetak Label QR</span>
                        </a>

                        <a href="{{ route('apar.download_qr', $apar->id) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[44px]">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span>Download SVG</span>
                        </a>
                    </div>
                </div>
            </x-card>

            <!-- Card Status Kondisi & Kesiapan -->
            <x-card title="Status Kesiapan" subtitle="Kondisi tabung saat ini.">
                <div class="space-y-3.5 text-xs">
                    <div>
                        <span class="block text-slate-500 font-semibold mb-1">Status Operasional</span>
                        <x-badge :variant="$apar->status_badge_variant" :dot="true">
                            {{ $apar->status_label }}
                        </x-badge>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <span class="block text-slate-500 font-semibold mb-1">Pemeriksaan Fisik Terakhir</span>
                        @if($apar->tanggal_pemeriksaan_terakhir)
                            <span class="font-mono font-medium text-slate-900 block tabular-nums">
                                {{ $apar->tanggal_pemeriksaan_terakhir->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-slate-400 italic block">Belum ada catatan pemeriksaan</span>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <span class="block text-slate-500 font-semibold mb-1">Masa Berlaku Media</span>
                        @if($apar->tanggal_kadaluarsa)
                            <span class="font-mono font-medium text-slate-900 block tabular-nums">
                                {{ $apar->tanggal_kadaluarsa->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-slate-400 italic block">Belum dicatat</span>
                        @endif
                    </div>
                </div>
            </x-card>

            <!-- Card Jadwal Inspeksi Terkait (Tahap 4) -->
            <x-card title="Jadwal Inspeksi" subtitle="Penugasan periode inspeksi berkala.">
                <div class="space-y-2 text-xs">
                    @php
                    $terkaitJadwals = $apar->jadwals()->orderBy('tanggal_mulai', 'desc')->take(3)->get();
                    @endphp
                    @forelse($terkaitJadwals as $tj)
                        <div class="p-2.5 rounded-lg border border-slate-200 bg-slate-50/60 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <a href="{{ route('jadwal.show', $tj->id) }}" class="font-bold text-slate-900 hover:underline block truncate">
                                    {{ $tj->nama }}
                                </a>
                                <span class="font-mono text-[10px] text-slate-500 block mt-0.5">
                                    {{ $tj->tanggal_mulai->format('d/m/Y') }} – {{ $tj->tanggal_selesai->format('d/m/Y') }}
                                </span>
                            </div>
                            <x-badge :variant="$tj->status_badge_variant">
                                {{ $tj->status_label }}
                            </x-badge>
                        </div>
                    @empty
                        <div class="text-slate-400 italic text-[11px] py-1 text-center">
                            Belum ada jadwal inspeksi aktif untuk unit ini.
                        </div>
                    @endforelse
                    <div class="pt-2 border-t border-slate-100 text-center">
                        <a href="{{ route('jadwal.index') }}" class="text-slate-600 hover:text-slate-900 font-semibold text-[11px] hover:underline">
                            Lihat Semua Jadwal &rarr;
                        </a>
                    </div>
                </div>
            </x-card>

            <!-- Widget Ringkasan Log Pemeriksaan (Tahap 6) -->
            <x-card title="Log Pemeriksaan" subtitle="Catatan riwayat inspeksi unit ini.">
                @php
                    $totalLog = $apar->pemeriksaans()->count();
                    $lastLog = $apar->pemeriksaans()->first();
                @endphp
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Total Pemeriksaan</span>
                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2.5 py-0.5 rounded border border-slate-200 tabular-nums">
                            {{ $totalLog }} Kali
                        </span>
                    </div>

                    @if($lastLog)
                        <div>
                            <span class="text-slate-500 font-medium block mb-1">Pemeriksaan Terakhir:</span>
                            <div class="p-2.5 rounded-lg border border-slate-200 bg-slate-50/60 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-[11px] text-slate-800 font-medium tabular-nums">
                                        {{ $lastLog->tanggal_pemeriksaan->format('d/m/Y') }}
                                    </span>
                                    <x-badge :variant="$lastLog->hasil_badge_variant">
                                        {{ $lastLog->hasil_label }}
                                    </x-badge>
                                </div>
                                <span class="text-[10px] text-slate-500 block truncate">
                                    Oleh: {{ $lastLog->pemeriksa_nama }}
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="text-slate-400 italic text-[11px] text-center py-1">
                            Belum ada catatan inspeksi fisik.
                        </p>
                    @endif
                </div>
            </x-card>

            <!-- Widget Ringkasan Riwayat Tindak Lanjut (Tahap 7) -->
            <x-card title="Riwayat Tindak Lanjut" subtitle="Pekerjaan perbaikan & pemeliharaan.">
                @php
                    $totalTl = $apar->tindakLanjuts()->count();
                    $lastTl = $apar->tindakLanjuts()->first();
                    $pendingTl = $apar->tindakLanjuts()->where('status', '!=', 'selesai')->count();
                @endphp
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Total Tindakan</span>
                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2.5 py-0.5 rounded border border-slate-200 tabular-nums">
                            {{ $totalTl }} Kali
                        </span>
                    </div>

                    @if($pendingTl > 0)
                        <div class="p-2 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-between">
                            <span class="text-amber-800 font-bold text-[11px]">Perlu Diselesaikan</span>
                            <span class="font-mono font-extrabold text-amber-900 bg-amber-200/70 px-2 py-0.5 rounded text-[11px]">
                                {{ $pendingTl }}
                            </span>
                        </div>
                    @endif

                    @if($lastTl)
                        <div>
                            <span class="text-slate-500 font-medium block mb-1">Tindakan Terakhir:</span>
                            <div class="p-2.5 rounded-lg border border-slate-200 bg-slate-50/60 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-[11px] text-slate-800 font-medium tabular-nums">
                                        {{ $lastTl->tanggal_tindakan->format('d/m/Y') }}
                                    </span>
                                    <x-badge :variant="$lastTl->status_badge_variant">
                                        {{ $lastTl->status_label }}
                                    </x-badge>
                                </div>
                                <span class="text-[11px] font-bold text-slate-800 block truncate">
                                    {{ $lastTl->jenis_label }}
                                </span>
                                <span class="text-[10px] text-slate-500 block truncate">
                                    Oleh: {{ $lastTl->petugas_pelaksana }}
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="text-slate-400 italic text-[11px] text-center py-1">
                            Belum ada catatan pemeliharaan.
                        </p>
                    @endif
                </div>
            </x-card>
        </div>
    </div>

    <!-- ==================================================================
         TABEL LOG PEMERIKSAAN APAR LENGKAP (Section 11)
         ================================================================== -->
    <div class="mt-6">
        <x-card title="Log Pemeriksaan APAR" subtitle="Riwayat seluruh verifikasi fisik berkala yang pernah dilakukan untuk unit ini.">
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Tanggal Pemeriksaan</th>
                            <th class="px-4 py-3 font-bold">Jadwal Inspeksi</th>
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Pemeriksa</th>
                            <th class="px-4 py-3 font-bold text-center whitespace-nowrap">Hasil</th>
                            <th class="px-4 py-3 font-bold">Catatan Singkat</th>
                            <th class="px-4 py-3 font-bold text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                        @php
                            $logPemeriksaans = $apar->pemeriksaans()->with(['jadwal'])->get();
                        @endphp
                        @forelse($logPemeriksaans as $p)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3 font-mono font-medium text-slate-900 whitespace-nowrap tabular-nums">
                                    {{ $p->tanggal_pemeriksaan->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($p->jadwal)
                                        <a href="{{ route('jadwal.show', $p->jadwal->id) }}" class="font-semibold text-sky-800 hover:underline">
                                            {{ $p->jadwal->nama }}
                                        </a>
                                    @else
                                        <span class="text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 text-[11px] font-medium">
                                            Pemeriksaan Mandiri (Ad-Hoc)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">
                                    {{ $p->pemeriksa_nama }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <x-badge :variant="$p->hasil_badge_variant" :dot="true">
                                        {{ $p->hasil_label }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-3 text-slate-600 max-w-xs truncate">
                                    {{ $p->catatan ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('pemeriksaan.show', $p->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[36px]">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>Lihat Hasil Checklist</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400 italic">
                                    Belum ada log pemeriksaan fisik untuk unit APAR ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <!-- ==================================================================
         TABEL RIWAYAT TINDAK LANJUT & PEMELIHARAAN APAR (Tahap 7, Section 9 & 20)
         ================================================================== -->
    <div class="mt-6">
        <x-card title="Riwayat Tindak Lanjut & Pemeliharaan APAR" subtitle="Catatan historis seluruh perbaikan, pembersihan, dan pemeliharaan komponen unit APAR ini.">
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Tanggal Tindakan</th>
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Jenis Tindakan</th>
                            <th class="px-4 py-3 font-bold">Sumber Pemeriksaan</th>
                            <th class="px-4 py-3 font-bold">Temuan Masalah</th>
                            <th class="px-4 py-3 font-bold text-center whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Pelaksana</th>
                            <th class="px-4 py-3 font-bold whitespace-nowrap">Tanggal Selesai</th>
                            <th class="px-4 py-3 font-bold text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                        @php
                            $riwayatTindakLanjuts = $apar->tindakLanjuts()->with(['pemeriksaan'])->get();
                        @endphp
                        @forelse($riwayatTindakLanjuts as $tl)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3 font-mono font-medium text-slate-900 whitespace-nowrap tabular-nums">
                                    {{ $tl->tanggal_tindakan->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap font-bold text-slate-900">
                                    {{ $tl->jenis_label }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($tl->pemeriksaan)
                                        <a href="{{ route('pemeriksaan.show', $tl->pemeriksaan_id) }}" class="font-semibold text-sky-800 hover:underline">
                                            Inspeksi {{ $tl->pemeriksaan->tanggal_pemeriksaan->format('d/m/Y') }}
                                        </a>
                                        <span class="text-slate-400 block text-[10px]">Oleh: {{ $tl->pemeriksaan->pemeriksa_nama }}</span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <span class="font-medium text-slate-900 block truncate" title="{{ $tl->temuan_teks }}">
                                        {{ $tl->temuan_teks }}
                                    </span>
                                    <span class="text-slate-500 block text-[10px] truncate" title="{{ $tl->uraian_tindakan }}">
                                        Tindakan: {{ $tl->uraian_tindakan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <x-badge :variant="$tl->status_badge_variant" :dot="true">
                                        {{ $tl->status_label }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-800">
                                    {{ $tl->petugas_pelaksana }}
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-700 whitespace-nowrap tabular-nums">
                                    @if($tl->tanggal_selesai)
                                        <span class="text-emerald-700 font-semibold">{{ $tl->tanggal_selesai->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum selesai</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('tindak-lanjut.show', $tl->id) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[36px]">
                                            <span>Lihat Detail</span>
                                        </a>
                                        @if($tl->status !== 'selesai')
                                            <a href="{{ route('tindak-lanjut.edit', $tl->id) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition min-h-[36px]">
                                                <span>Update</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-400 italic">
                                    Belum ada riwayat tindak lanjut atau pemeliharaan untuk unit APAR ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <!-- ==================================================================
         TABEL RIWAYAT PENGGANTIAN APAR (Tahap 8, Section 18)
         ================================================================== -->
    @if($apar->is_replaced || $apar->is_replacement)
        <div class="mt-6">
            <x-card title="Riwayat Penggantian APAR" subtitle="Keterhubungan penelusuran unit pengganti dan unit yang digantikan untuk audit keselamatan RS.">
                <div class="space-y-4 text-xs">
                    @if($apar->is_replaced && $apar->penggantianKeluar)
                        @php $pk = $apar->penggantianKeluar; @endphp
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500 font-semibold">Digantikan oleh:</span>
                                    <a href="{{ route('apar.show', $pk->apar_baru_id) }}" class="font-mono font-bold text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded text-sm hover:underline">
                                        {{ $pk->aparBaru?->kode }}
                                    </a>
                                    <x-badge variant="success" :dot="true">APAR Baru Aktif</x-badge>
                                </div>
                                <span class="font-mono text-slate-600 text-xs">
                                    Tanggal Penggantian: {{ $pk->tanggal_penggantian->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3">
                                <div>
                                    <span class="text-slate-500 block font-semibold">Alasan Penggantian:</span>
                                    <span class="text-slate-900 font-medium">{{ $pk->alasan_label }}</span>
                                    @if($pk->alasan_keterangan)
                                        <span class="text-[11px] text-slate-500 block">({{ $pk->alasan_keterangan }})</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-slate-500 block font-semibold">Petugas:</span>
                                    <span class="text-slate-900 font-medium">{{ $pk->petugas_nama }}</span>
                                </div>
                                <div class="sm:text-right">
                                    <a href="{{ route('penggantian.show', $pk->id) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[36px]">
                                        <span>Lihat Rincian Penggantian &rarr;</span>
                                    </a>
                                </div>
                            </div>
                            @if($pk->catatan)
                                <div class="mt-3 pt-2.5 border-t border-slate-200/60 text-slate-600">
                                    <span class="font-semibold text-slate-700">Catatan:</span> {{ $pk->catatan }}
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($apar->is_replacement && $apar->penggantianMasuk)
                        @php $pm = $apar->penggantianMasuk; @endphp
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500 font-semibold">Menggantikan Unit:</span>
                                    <a href="{{ route('apar.show', $pm->apar_lama_id) }}" class="font-mono font-bold text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded text-sm hover:underline">
                                        {{ $pm->aparLama?->kode }}
                                    </a>
                                    <x-badge variant="neutral" :dot="true">APAR Lama Dinonaktifkan</x-badge>
                                </div>
                                <span class="font-mono text-slate-600 text-xs">
                                    Tanggal Penggantian: {{ $pm->tanggal_penggantian->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3">
                                <div>
                                    <span class="text-slate-500 block font-semibold">Alasan Penggantian:</span>
                                    <span class="text-slate-900 font-medium">{{ $pm->alasan_label }}</span>
                                    @if($pm->alasan_keterangan)
                                        <span class="text-[11px] text-slate-500 block">({{ $pm->alasan_keterangan }})</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-slate-500 block font-semibold">Petugas:</span>
                                    <span class="text-slate-900 font-medium">{{ $pm->petugas_nama }}</span>
                                </div>
                                <div class="sm:text-right">
                                    <a href="{{ route('penggantian.show', $pm->id) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[36px]">
                                        <span>Lihat Rincian Penggantian &rarr;</span>
                                    </a>
                                </div>
                            </div>
                            @if($pm->catatan)
                                <div class="mt-3 pt-2.5 border-t border-slate-200/60 text-slate-600">
                                    <span class="font-semibold text-slate-700">Catatan:</span> {{ $pm->catatan }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    @endif
</x-layouts.app>