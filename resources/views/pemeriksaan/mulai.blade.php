<x-layouts.app title="HAFIS : Mulai Pemeriksaan {{ $apar->kode }}">
    <!-- 1. Header with Breadcrumb & Navigation -->
    <div class="pb-5 mb-6 border-b border-gray-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2 font-medium" aria-label="Breadcrumb">
            @if($jadwal)
                <a href="{{ route('jadwal.index') }}" class="hover:text-gray-900 transition-colors">Jadwal Inspeksi</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('jadwal.show', $jadwal->id) }}" class="hover:text-gray-900 transition-colors">{{ $jadwal->nama }}</a>
                <span class="text-gray-300">/</span>
            @else
                <a href="{{ route('apar.index') }}" class="hover:text-gray-900 transition-colors">Data APAR</a>
                <span class="text-gray-300">/</span>
            @endif
            <a href="{{ route('apar.show', $apar->id) }}" class="hover:text-gray-900 transition-colors">{{ $apar->kode }}</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-semibold">Mulai Pemeriksaan</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">
                        Mulai Pemeriksaan APAR
                    </h1>
                    <span class="font-mono text-sm font-semibold text-gray-900 bg-white border border-gray-200 px-2.5 py-0.5 rounded-lg tabular-nums">
                        {{ $apar->kode }}
                    </span>
                    <x-badge :variant="$apar->status_badge_variant" :dot="true">
                        {{ $apar->status_label }}
                    </x-badge>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Verifikasi identitas fisik tabung dan status penugasan sebelum inspeksi lapangan.
                </p>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2">
                @if($jadwal)
                    <x-button variant="secondary" href="{{ route('jadwal.show', $jadwal->id) }}" class="min-h-[40px]">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Kembali ke Jadwal</span>
                    </x-button>
                @endif
                <x-button variant="secondary" href="{{ route('apar.show', $apar->id) }}" class="min-h-[40px]">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Detail APAR</span>
                </x-button>
            </div>
        </div>
    </div>

    <!-- Alert Status Jadwal Inspeksi -->
    @if($jadwal)
        <div class="mb-6 p-4 rounded-xl border border-blue-200 bg-blue-50 text-blue-950 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3" role="region" aria-label="Status Jadwal">
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="font-semibold text-blue-950 text-sm">Terhubung dengan Jadwal Inspeksi Aktif</h4>
                        <x-badge :variant="$jadwal->status_badge_variant">
                            {{ $jadwal->status_label }}
                        </x-badge>
                    </div>
                    <p class="text-blue-800 mt-0.5">
                        Pemeriksaan ini tercatat sebagai bagian dari penugasan <span class="font-semibold">{{ $jadwal->nama }}</span> (Periode: {{ $jadwal->tanggal_mulai->format('d/m/Y') }} - {{ $jadwal->tanggal_selesai->format('d/m/Y') }}).
                    </p>
                </div>
            </div>
            <a href="{{ route('jadwal.show', $jadwal->id) }}" class="text-blue-700 hover:text-blue-900 font-medium underline shrink-0 sm:text-right">
                Lihat Target Jadwal &rarr;
            </a>
        </div>
    @else
        <div class="mb-6 p-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-950 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3" role="region" aria-label="Status Jadwal">
            <div class="flex items-start gap-3">
                <span class="w-6 h-6 rounded-full bg-amber-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </span>
                <div>
                    <h4 class="font-semibold text-amber-950 text-sm">APAR ini belum memiliki jadwal inspeksi aktif.</h4>
                    <p class="text-amber-800 mt-0.5">
                        Unit tabung ini tidak sedang terdaftar dalam siklus jadwal berjalan. Pemeriksaan akan dijalankan sebagai inspeksi mandiri (ad-hoc) sesuai SOP K3RS Rumah Sakit.
                    </p>
                </div>
            </div>
            <a href="{{ route('jadwal.index') }}" class="text-amber-800 hover:text-amber-950 font-medium underline shrink-0 sm:text-right">
                Kelola Jadwal &rarr;
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Kolom Kiri: Ringkasan Validasi APAR -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 1. Identitas & Spesifikasi Unit -->
            <x-card title="Verifikasi Identitas & Lokasi Fisik" subtitle="Pastikan unit fisik yang dihadapi petugas sesuai dengan identitas sistem.">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <dt class="font-medium text-gray-500 mb-0.5">Kode APAR</dt>
                        <dd class="font-mono font-semibold text-gray-900 text-sm">{{ $apar->kode }}</dd>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <dt class="font-medium text-gray-500 mb-0.5">Nomor Register RS</dt>
                        <dd class="font-mono font-medium text-gray-800">{{ $apar->nomor_register ?: 'Belum terdaftar' }}</dd>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <dt class="font-medium text-gray-500 mb-0.5">Lokasi Penempatan</dt>
                        <dd class="font-semibold text-gray-900">
                            {{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}
                        </dd>
                        <dd class="text-gray-600 text-[11px] mt-0.5">
                            @if($apar->lokasi)
                                {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                            @else
                                Ruangan: <span class="font-medium text-gray-800">{{ $apar->ruangan?->nama ?? '-' }}</span>
                            @endif
                        </dd>
                        @if($apar->posisi_detail)
                            <dd class="text-gray-500 text-[11px] italic mt-1 border-t border-gray-200/60 pt-1">
                                {{ $apar->posisi_detail }}
                            </dd>
                        @endif
                    </div>

                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <dt class="font-medium text-gray-500 mb-0.5">Spesifikasi Media & Kapasitas</dt>
                        <dd class="font-semibold text-gray-900">{{ $apar->jenis_media }}</dd>
                        <dd class="text-gray-600 text-[11px] font-mono mt-0.5">
                            Kapasitas: {{ $apar->kapasitas }} &bull; Merek: {{ $apar->merk ?: '-' }}
                        </dd>
                    </div>
                </dl>
            </x-card>

            <!-- 2. Informasi Petugas & Sesi Pemeriksaan -->
            <x-card title="Metadata Sesi Pemeriksaan" subtitle="Waktu verifikasi awal sebelum pengambilan data checklist.">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <dt class="font-medium text-gray-500 mb-1">Petugas Pelaksana</dt>
                        <dd class="flex items-center gap-2 font-medium text-gray-900">
                            <span class="w-6 h-6 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-semibold text-[10px]">
                                {{ substr($petugas, 0, 1) }}
                            </span>
                            <span>{{ $petugas }}</span>
                        </dd>
                    </div>

                    <div>
                        <dt class="font-medium text-gray-500 mb-1">Waktu Sesi Dimulai</dt>
                        <dd class="font-mono font-semibold text-gray-900 tabular-nums">
                            {{ $waktuSekarang->format('d/m/Y H:i') }} WITA
                        </dd>
                    </div>
                </dl>
            </x-card>

            <!-- 3. Alur Kelanjutan: Lanjutkan ke Checklist Fisik -->
            <div class="p-5 bg-white border border-gray-200 rounded-xl space-y-4">
                <div class="flex items-start gap-3">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            Identitas APAR Terverifikasi
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Identitas APAR sudah terverifikasi. Silakan lanjutkan pemeriksaan fisik APAR di lokasi.
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a href="{{ route('apar.show', $apar->id) }}" class="w-full sm:w-auto px-4 py-2.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-medium text-xs text-center transition min-h-[44px] flex items-center justify-center">
                        &larr; Kembali ke Detail APAR
                    </a>

                    @php
                        $mulaiBulan = (int) request('bulan', request('periode_bulan', Carbon\Carbon::now()->month));
                        $mulaiTahun = (int) request('tahun', request('periode_tahun', Carbon\Carbon::now()->year));
                        $mulaiParams = [
                            'apar_id' => $apar->id,
                            'periode_bulan' => $mulaiBulan,
                            'periode_tahun' => $mulaiTahun,
                        ];
                        if ($jadwal) {
                            $mulaiParams['jadwal_id'] = $jadwal->id;
                        }
                    @endphp
                    <a href="{{ route('pemeriksaan.create', $mulaiParams) }}"
                       class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-[#111827] hover:bg-gray-800 text-white font-medium text-xs text-center transition min-h-[44px] inline-flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Buka Lembar Checklist</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: QR Code & Verifikasi Tabung -->
        <div class="lg:col-span-1 space-y-6">
            <x-card title="QR Code Terverifikasi" subtitle="Kunci identitas fisik yang dipindai.">
                <div class="text-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="mx-auto w-36 h-36 bg-white p-2 rounded-lg border border-gray-200 flex items-center justify-center">
                        {!! $apar->qr_svg !!}
                    </div>

                    <div class="mt-3">
                        <span class="text-[10px] uppercase font-medium text-gray-400 tracking-wider block">Token QR</span>
                        <div class="font-mono text-xs font-semibold text-gray-800 break-all select-all mt-0.5 bg-white py-1 px-2 rounded border border-gray-200">
                            {{ $apar->qr_code }}
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card title="Status Masa Berlaku" subtitle="Parameter kedaluwarsa media pemadam.">
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="block text-gray-500 font-medium mb-0.5">Kedaluwarsa Tabung / Media</span>
                        @if($apar->tanggal_kadaluarsa)
                            <span class="font-mono font-semibold text-gray-900 block tabular-nums">
                                {{ $apar->tanggal_kadaluarsa->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-gray-400 italic block">Belum dicatat</span>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <span class="block text-gray-500 font-medium mb-0.5">Pemeriksaan Terakhir</span>
                        @if($apar->tanggal_pemeriksaan_terakhir)
                            <span class="font-mono font-medium text-gray-800 block tabular-nums">
                                {{ $apar->tanggal_pemeriksaan_terakhir->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-gray-400 italic block">Belum ada riwayat sebelumnya</span>
                        @endif
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Modal Info Verifikasi Selesai -->
    <div id="modal-verifikasi-info" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 border border-gray-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900">
                Identitas APAR Terverifikasi
            </h3>
            <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                Unit APAR <span class="font-mono font-semibold text-gray-900">{{ $apar->kode }}</span> telah berhasil diidentifikasi melalui sistem dan siap masuk ke pemeriksaan fisik.
            </p>
            <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-700 space-y-1">
                <div class="flex items-center gap-1.5 font-medium">
                    <span class="text-emerald-600">&check;</span> Identitas APAR valid dari Master Data
                </div>
                <div class="flex items-center gap-1.5 font-medium">
                    <span class="text-emerald-600">&check;</span> Validasi status aktif APAR
                </div>
                <div class="flex items-center gap-1.5 font-medium">
                    <span class="text-emerald-600">&check;</span> Integrasi konteks Jadwal Inspeksi
                </div>
            </div>
            <p class="text-[11px] text-gray-500 mt-3 italic">
                Silakan lanjutkan pengisian checklist fisik, dokumentasi foto, dan catatan kondisi lapangan.
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" 
                        onclick="document.getElementById('modal-verifikasi-info').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg bg-[#111827] hover:bg-gray-800 text-white font-medium text-xs transition min-h-[40px] cursor-pointer">
                    Tutup Dialog
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>
