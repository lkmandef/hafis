<x-layouts.app title="HAFIS : Rincian Penggantian APAR #{{ $penggantian->id }}">
    <!-- Header with Breadcrumb & Title -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('apar.index') }}" class="hover:text-slate-800 transition-colors">Master Data APAR</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('penggantian.index') }}" class="hover:text-slate-800 transition-colors">Riwayat Penggantian</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Peristiwa #{{ $penggantian->id }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>Dokumentasi Penggantian Unit:</span>
                    <span class="font-mono text-slate-900 bg-white border border-slate-200 px-3 py-1 rounded-lg text-lg">{{ $penggantian->aparLama?->kode }} &rarr; {{ $penggantian->aparBaru?->kode }}</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Rekaman historis pergantian unit tabung APAR secara menyeluruh. Terdaftar di sistem tanggal {{ $penggantian->created_at->format('d/m/Y H:i') }}.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('penggantian.index') }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Daftar Riwayat</span>
                </a>
            </div>
        </div>
    </div>

    <div class="space-y-6">

        <!-- ==============================================================
             CARD 1: METADATA & RINCIAN PERISTIWA PENGGANTIAN (Section 19)
             ============================================================== -->
        <x-card title="Informasi & Legalitas Penggantian" subtitle="Data operasional dan alasan penggantian unit di lingkungan rumah sakit.">
            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                    <dt class="font-semibold text-slate-500 mb-1">Tanggal Pelaksanaan</dt>
                    <dd class="font-mono font-bold text-slate-900 text-sm tabular-nums">
                        {{ $penggantian->tanggal_penggantian->format('d/m/Y') }}
                    </dd>
                </div>

                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                    <dt class="font-semibold text-slate-500 mb-1">Alasan Penggantian</dt>
                    <dd class="font-bold text-slate-900 text-sm">
                        {{ $penggantian->alasan_label }}
                    </dd>
                    @if($penggantian->alasan_keterangan)
                        <span class="text-[11px] text-slate-600 block mt-0.5">{{ $penggantian->alasan_keterangan }}</span>
                    @endif
                </div>

                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                    <dt class="font-semibold text-slate-500 mb-1">Petugas Pelaksana</dt>
                    <dd class="font-medium text-slate-900 text-sm">
                        {{ $penggantian->petugas_nama }}
                    </dd>
                    @if($penggantian->user)
                        <span class="text-[10px] text-slate-400 block mt-0.5">Akun: {{ $penggantian->user->name }}</span>
                    @endif
                </div>

                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                    <dt class="font-semibold text-slate-500 mb-1">Waktu Pencatatan Sistem</dt>
                    <dd class="font-mono text-slate-700 text-xs tabular-nums">
                        {{ $penggantian->created_at->format('d/m/Y H:i') }}
                    </dd>
                    <span class="text-[10px] text-slate-400 block mt-0.5">ID Entri: #{{ $penggantian->id }}</span>
                </div>
            </dl>

            @if($penggantian->catatan)
                <div class="mt-4 p-3.5 rounded-lg bg-amber-50/70 border border-amber-200 text-xs text-amber-950">
                    <span class="font-bold block mb-1">Catatan Operasional:</span>
                    <p class="leading-relaxed">{{ $penggantian->catatan }}</p>
                </div>
            @endif
        </x-card>

        <!-- ==============================================================
             CARD 2: PERBANDINGAN KOMPARATIF UNIT (APAR LAMA VS APAR BARU)
             ============================================================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Kolom APAR Lama -->
            <x-card title="APAR Lama (Unit yang Diganti)" subtitle="Status operasional dinonaktifkan, seluruh histori lama tersimpan.">
                @if($penggantian->aparLama)
                    @php $lama = $penggantian->aparLama; @endphp
                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Kode Tabung</span>
                            <span class="font-mono font-bold text-slate-900 text-sm">{{ $lama->kode }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Status Operasional</span>
                            <x-badge variant="neutral" :dot="true">
                                Tidak Aktif (Sudah Diganti)
                            </x-badge>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Jenis Media Pemadam</span>
                            <span class="font-medium text-slate-900">{{ $lama->jenis_media }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Kapasitas Tabung</span>
                            <span class="font-mono font-medium text-slate-900">{{ $lama->kapasitas }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Merek / Model</span>
                            <span class="font-medium text-slate-900">{{ $lama->merek ?: '-' }} / {{ $lama->tipe_model ?: '-' }}</span>
                        </div>

                        <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Lokasi Terdaftar</span>
                            <div class="text-right">
                                <span class="font-medium text-slate-900 block">{{ $lama->lokasi_lengkap }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $lama->posisi_detail ?: 'Titik umum' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">QR Code Identifier</span>
                            <span class="font-mono text-[11px] text-slate-700 truncate max-w-[200px]" title="{{ $lama->qr_code }}">
                                {{ $lama->qr_code }}
                            </span>
                        </div>

                        <div class="pt-2">
                            <a href="{{ route('apar.show', $lama->id) }}" 
                               class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-semibold text-xs transition min-h-[44px]">
                                <span>Buka Rekam Jejak APAR Lama (Histori Lengkap) &rarr;</span>
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-slate-400 italic text-xs">Data APAR lama tidak ditemukan.</p>
                @endif
            </x-card>

            <!-- Kolom APAR Baru -->
            <x-card title="APAR Baru (Unit Pengganti)" subtitle="Unit aktif pengganti yang menggantikan operasional di lapangan.">
                @if($penggantian->aparBaru)
                    @php $baru = $penggantian->aparBaru; @endphp
                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Kode Tabung</span>
                            <span class="font-mono font-bold text-slate-900 text-sm">{{ $baru->kode }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Status Operasional</span>
                            <x-badge :variant="$baru->status_badge_variant" :dot="true">
                                {{ $baru->status_label }}
                            </x-badge>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Jenis Media Pemadam</span>
                            <span class="font-medium text-slate-900">{{ $baru->jenis_media }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Kapasitas Tabung</span>
                            <span class="font-mono font-medium text-slate-900">{{ $baru->kapasitas }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Merek / Model</span>
                            <span class="font-medium text-slate-900">{{ $baru->merek ?: '-' }} / {{ $baru->tipe_model ?: '-' }}</span>
                        </div>

                        <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">Lokasi Terdaftar</span>
                            <div class="text-right">
                                <span class="font-medium text-slate-900 block">{{ $baru->lokasi_lengkap }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $baru->posisi_detail ?: 'Titik umum' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-semibold">QR Code Identifier Baru</span>
                            <span class="font-mono text-[11px] text-slate-700 truncate max-w-[200px]" title="{{ $baru->qr_code }}">
                                {{ $baru->qr_code }}
                            </span>
                        </div>

                        <div class="pt-2">
                            <a href="{{ route('apar.show', $baru->id) }}" 
                               class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[44px]">
                                <span>Buka Detail APAR Baru (Unit Aktif) &rarr;</span>
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-slate-400 italic text-xs">Data APAR baru tidak ditemukan.</p>
                @endif
            </x-card>
        </div>
    </div>
</x-layouts.app>
