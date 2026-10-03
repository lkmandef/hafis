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

            <!-- Header Actions: Edit, Titik Denah, Kembali -->
            <div class="flex flex-wrap items-center gap-2">
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

    <!-- Notifikasi Sukses / Feedback -->
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

            <!-- Card QR Code Identifier -->
            <x-card title="QR Code Identifier" subtitle="Struktur data unik untuk verifikasi scan di lapangan.">
                <div class="text-center p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <!-- SVG QR Matrix Placeholder Realistis Berdasarkan Token -->
                    <div class="mx-auto w-36 h-36 bg-white p-2.5 rounded-lg border border-slate-300 shadow-2xs flex flex-col items-center justify-center relative">
                        <svg class="w-full h-full text-slate-900" viewBox="0 0 100 100" fill="currentColor">
                            <!-- Sudut Kiri Atas -->
                            <rect x="5" y="5" width="28" height="28" fill="none" stroke="currentColor" stroke-width="4" />
                            <rect x="11" y="11" width="16" height="16" />
                            <!-- Sudut Kanan Atas -->
                            <rect x="67" y="5" width="28" height="28" fill="none" stroke="currentColor" stroke-width="4" />
                            <rect x="73" y="11" width="16" height="16" />
                            <!-- Sudut Kiri Bawah -->
                            <rect x="5" y="67" width="28" height="28" fill="none" stroke="currentColor" stroke-width="4" />
                            <rect x="11" y="73" width="16" height="16" />
                            <!-- Pola Matrix Data Realistis -->
                            <rect x="40" y="8" width="6" height="6" />
                            <rect x="52" y="8" width="6" height="6" />
                            <rect x="40" y="20" width="6" height="6" />
                            <rect x="46" y="26" width="6" height="6" />
                            <rect x="8" y="40" width="6" height="6" />
                            <rect x="20" y="40" width="6" height="6" />
                            <rect x="14" y="46" width="6" height="6" />
                            <rect x="26" y="52" width="6" height="6" />
                            <rect x="40" y="40" width="8" height="8" />
                            <rect x="52" y="46" width="6" height="6" />
                            <rect x="62" y="40" width="6" height="6" />
                            <rect x="74" y="46" width="6" height="6" />
                            <rect x="86" y="40" width="6" height="6" />
                            <rect x="40" y="60" width="6" height="6" />
                            <rect x="52" y="60" width="6" height="6" />
                            <rect x="46" y="72" width="6" height="6" />
                            <rect x="40" y="84" width="6" height="6" />
                            <rect x="58" y="84" width="6" height="6" />
                            <rect x="68" y="66" width="6" height="6" />
                            <rect x="80" y="72" width="6" height="6" />
                            <rect x="74" y="84" width="6" height="6" />
                            <rect x="86" y="84" width="6" height="6" />
                        </svg>
                    </div>

                    <div class="mt-3">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Identifier Unik</span>
                        <div class="font-mono text-xs font-extrabold text-slate-800 break-all select-all mt-0.5 bg-white py-1 px-2 rounded border border-slate-200">
                            {{ $apar->qr_code }}
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-500 mt-2.5 leading-relaxed text-left">
                        Token unik ini akan menjadi kunci validasi fisik saat pemindaian kamera pada Tahap 3 (Workflow Scan APAR).
                    </p>
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

            <!-- Placeholder Ramah Riwayat Pemeriksaan -->
            <div class="p-4 rounded-xl border border-slate-200 bg-white text-xs">
                <div class="flex items-center gap-2 mb-1.5 text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h4 class="font-bold uppercase tracking-wider text-[11px]">
                        Log Riwayat Pemeriksaan
                    </h4>
                </div>
                <p class="text-slate-500 text-[11px] leading-relaxed">
                    Checklist fisik, riwayat inspeksi bulanan, dan tindak lanjut pemeliharaan untuk unit ini akan terhubung pada Tahap 3.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>