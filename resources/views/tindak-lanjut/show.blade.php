<x-layouts.app title="HAFIS : Detail Tindak Lanjut {{ $tindakLanjut->apar?->kode ?? ('#' . $tindakLanjut->apar_id) }}">
    <!-- 1. Header with Breadcrumb & Navigation -->
    <div class="pb-5 mb-6 border-b border-gray-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('apar.index') }}" class="hover:text-gray-900 transition-colors">Data APAR</a>
            <span class="text-gray-300">/</span>
            @if($tindakLanjut->apar && ! $tindakLanjut->apar->trashed())
                <a href="{{ route('apar.show', $tindakLanjut->apar_id) }}" class="hover:text-gray-900 transition-colors">{{ $tindakLanjut->apar->kode }}</a>
            @else
                <span class="text-gray-500">{{ $tindakLanjut->apar?->kode ?? ('APAR #' . $tindakLanjut->apar_id) }}</span>
            @endif
            <span class="text-gray-300">/</span>
            @if($tindakLanjut->pemeriksaan_id)
                <a href="{{ route('pemeriksaan.show', $tindakLanjut->pemeriksaan_id) }}" class="hover:text-gray-900 transition-colors">Pemeriksaan</a>
                <span class="text-gray-300">/</span>
            @endif
            <span class="text-gray-900 font-semibold">Tindak Lanjut #{{ $tindakLanjut->id }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                        Detail Tindak Lanjut & Pemeliharaan
                    </h1>
                    <span class="font-mono text-base font-bold text-gray-900 bg-gray-100 border border-gray-200 px-3 py-0.5 rounded-lg tabular-nums">
                        {{ $tindakLanjut->apar?->kode ?? ('APAR #' . $tindakLanjut->apar_id) }}
                    </span>
                    @if($tindakLanjut->apar?->trashed())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                            Unit Telah Dihapus
                        </span>
                    @endif
                    <x-badge :variant="$tindakLanjut->status_badge_variant" :dot="true">
                        {{ $tindakLanjut->status_label }}
                    </x-badge>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Dokumentasi riwayat tindakan perbaikan fisik tabung APAR di rumah sakit.
                </p>
            </div>

            <!-- Action Buttons: Edit, Kembali -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tindak-lanjut.edit', $tindakLanjut->id) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span>Edit Tindakan</span>
                </a>

                @if($tindakLanjut->pemeriksaan_id)
                    <a href="{{ route('pemeriksaan.show', $tindakLanjut->pemeriksaan_id) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition min-h-[44px]">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Lihat Lembar Pemeriksaan</span>
                    </a>
                @endif

                @if($tindakLanjut->apar && ! $tindakLanjut->apar->trashed())
                    <a href="{{ route('apar.show', $tindakLanjut->apar_id) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-white bg-[#111827] hover:bg-gray-800 transition min-h-[44px]">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Detail APAR</span>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed min-h-[44px]" title="Unit APAR sudah dihapus dari sistem operasional aktif.">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Detail APAR (Terhapus)</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="max-w-4xl mx-auto mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50/70 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center cursor-pointer" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- 2. Alur Transisi Status Tindak Lanjut Cepat / Banner Pembatalan (Section 13) -->
        @if($tindakLanjut->status === 'dibatalkan')
            <div class="p-5 bg-rose-50/70 border border-rose-200 rounded-xl shadow-xs">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 border border-rose-300 text-rose-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1 text-xs">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h3 class="font-bold text-rose-950 text-sm">
                                Tiket Tindak Lanjut Ini Telah Dibatalkan
                            </h3>
                            <span class="font-mono text-[11px] font-semibold text-rose-800 bg-rose-100/80 px-2 py-0.5 rounded border border-rose-200">
                                {{ $tindakLanjut->dibatalkan_at?->format('d/m/Y H:i') ?? '-' }} WITA
                            </span>
                        </div>
                        <p class="text-rose-900 mt-1">
                            <span class="font-semibold">Dibatalkan oleh:</span> {{ $tindakLanjut->dibatalkanOleh?->name ?? 'Administrator' }}
                        </p>
                        <div class="mt-2.5 p-3 rounded-lg bg-white/90 border border-rose-200 text-rose-900">
                            <span class="font-semibold block mb-0.5 text-[11px] text-rose-700 uppercase tracking-wider">Alasan Pembatalan Resmi (Audit K3RS):</span>
                            <p class="text-xs leading-relaxed italic">
                                "{{ $tindakLanjut->alasan_pembatalan ?: 'Tidak ada rincian alasan yang tersimpan.' }}"
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Alur Status Pengerjaan:</span>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold {{ $tindakLanjut->status === 'belum_ditindaklanjuti' ? 'bg-amber-50 text-amber-900 border border-amber-300' : 'bg-gray-100 text-gray-500' }}">
                                1. Belum Ditindaklanjuti
                            </span>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold {{ $tindakLanjut->status === 'dalam_proses' ? 'bg-sky-50 text-sky-900 border border-sky-300' : 'bg-gray-100 text-gray-500' }}">
                                2. Dalam Proses
                            </span>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold {{ $tindakLanjut->status === 'selesai' ? 'bg-emerald-50 text-emerald-900 border border-emerald-300' : 'bg-gray-100 text-gray-500' }}">
                                3. Selesai
                            </span>
                        </div>
                    </div>

                    <!-- Tombol Transisi Cepat -->
                    <div class="flex items-center gap-2 shrink-0">
                        @if($tindakLanjut->status === 'belum_ditindaklanjuti')
                            <form action="{{ route('tindak-lanjut.status', $tindakLanjut->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="dalam_proses">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#111827] hover:bg-gray-800 text-white font-semibold text-xs transition shadow-xs min-h-[44px] cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Mulai Proses Pemeliharaan</span>
                                </button>
                            </form>
                        @elseif($tindakLanjut->status === 'dalam_proses')
                            <form action="{{ route('tindak-lanjut.status', $tindakLanjut->id) }}" method="POST" class="flex flex-wrap items-center gap-2"
                                  data-confirm
                                  data-confirm-title="Selesaikan Pemeliharaan APAR"
                                  data-confirm-message="Tandai pemeliharaan ini sebagai selesai dan pulihkan kondisi fisik APAR menjadi Baik (Siap Pakai)?"
                                  data-confirm-type="success"
                                  data-confirm-text="Ya, Selesaikan">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="selesai">
                                <input type="hidden" name="update_kondisi_apar" value="1">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs transition shadow-xs min-h-[44px] cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Selesaikan Pemeliharaan</span>
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Pekerjaan Telah Tuntas</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- 3. Informasi APAR & Sumber Pemeriksaan (Section 10) -->
        <x-card title="Informasi APAR & Sumber Pemeriksaan" subtitle="Data identitas tabung serta konteks pemeriksaan fisik yang mendasarinya.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <!-- Kolom Kiri: Informasi APAR -->
                <div class="space-y-3 pb-4 md:pb-0 md:pr-6 border-b md:border-b-0 md:border-r border-gray-100">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Identitas Unit APAR</span>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Kode APAR</span>
                        @if($tindakLanjut->apar)
                            @if(! $tindakLanjut->apar->trashed())
                                <a href="{{ route('apar.show', $tindakLanjut->apar_id) }}" class="font-mono font-bold text-gray-900 bg-gray-100 hover:bg-gray-200 px-2.5 py-0.5 rounded border border-gray-200 transition-colors">
                                    {{ $tindakLanjut->apar->kode }}
                                </a>
                            @else
                                <span class="font-mono font-bold text-gray-700 bg-gray-100 px-2.5 py-0.5 rounded border border-gray-200">
                                    {{ $tindakLanjut->apar->kode }} <span class="text-rose-600 font-sans text-[11px] font-semibold">(Terhapus)</span>
                                </span>
                            @endif
                        @else
                            <span class="font-mono text-gray-400 italic">APAR #{{ $tindakLanjut->apar_id }} (Tidak Terdaftar)</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Jenis Media & Kapasitas</span>
                        <span class="font-semibold text-gray-900">{{ $tindakLanjut->apar?->jenis_media ?? '-' }} (<span class="font-mono">{{ $tindakLanjut->apar?->kapasitas ?? '-' }}</span>)</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Merek / Tipe</span>
                        <span class="text-gray-800 {{ ! $tindakLanjut->apar?->merek ? 'text-gray-400 italic' : '' }}">{{ $tindakLanjut->apar?->merek ?: 'Belum terdata' }} / {{ $tindakLanjut->apar?->tipe_model ?: '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Kondisi Tabung Saat Ini</span>
                        @if($tindakLanjut->apar)
                            <x-badge :variant="$tindakLanjut->apar->status_badge_variant" :dot="true">
                                {{ $tindakLanjut->apar->status_label }}
                            </x-badge>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-gray-500 font-medium block mb-0.5">Lokasi Penempatan</span>
                        <span class="font-semibold text-gray-900 block">{{ $tindakLanjut->apar?->lokasi_lengkap ?? '-' }}</span>
                        @if($tindakLanjut->apar?->posisi_detail)
                            <span class="text-gray-500 text-[11px] italic block mt-0.5">{{ $tindakLanjut->apar->posisi_detail }}</span>
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan: Sumber Pemeriksaan -->
                <div class="space-y-3">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Sumber Pemeriksaan</span>

                    @if($tindakLanjut->pemeriksaan)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-medium">Tanggal Pemeriksaan</span>
                            <span class="font-mono font-medium text-gray-900 tabular-nums">
                                {{ $tindakLanjut->pemeriksaan->tanggal_pemeriksaan->format('d/m/Y H:i') }} WITA
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-medium">Pemeriksa</span>
                            <span class="font-semibold text-gray-900">{{ $tindakLanjut->pemeriksaan->pemeriksa_nama }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-medium">Jadwal Inspeksi</span>
                            @if($tindakLanjut->pemeriksaan->jadwal)
                                <a href="{{ route('jadwal.show', $tindakLanjut->pemeriksaan->jadwal_id) }}" class="font-semibold text-gray-900 hover:underline">
                                    {{ $tindakLanjut->pemeriksaan->jadwal->nama }}
                                </a>
                            @else
                                <span class="text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 font-medium text-[11px]">Pemeriksaan Mandiri</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-medium">Hasil Keseluruhan</span>
                            <x-badge :variant="$tindakLanjut->pemeriksaan->hasil_badge_variant" :dot="true">
                                {{ $tindakLanjut->pemeriksaan->hasil_label }}
                            </x-badge>
                        </div>
                    @else
                        <p class="text-gray-400 italic text-[11px]">
                            Catatan pemeriksaan sumber telah diarsipkan atau tidak tersedia.
                        </p>
                    @endif

                    <!-- Detail Temuan Checklist -->
                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-gray-500 font-medium block mb-1">Temuan Masalah yang Ditindaklanjuti:</span>
                        @if($tindakLanjut->pemeriksaanDetail)
                            <div class="p-3 rounded-lg bg-rose-50/70 border border-rose-200">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-rose-950 text-xs">{{ $tindakLanjut->pemeriksaanDetail->item_nama }}</span>
                                    <x-badge variant="danger" :dot="true">{{ $tindakLanjut->pemeriksaanDetail->hasil_label }}</x-badge>
                                </div>
                                <p class="text-rose-800 text-[11px] mt-1 italic">
                                    Catatan: {{ $tindakLanjut->pemeriksaanDetail->catatan ?: 'Tidak ada catatan temuan spesifik.' }}
                                </p>
                            </div>
                        @else
                            <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                                <p class="text-gray-800 text-[11px]">
                                    {{ $tindakLanjut->deskripsi_temuan ?: 'Temuan umum dari hasil inspeksi fisik unit APAR.' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>

        <!-- 4. Rincian Pelaksanaan Tindakan Pemeliharaan (Section 10) -->
        <x-card title="Pelaksanaan Tindakan Pemeliharaan" subtitle="Rincian perbaikan yang dilakukan dan hasil penanganannya.">
            <dl class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4 text-xs mb-4">
                <div class="pb-3 border-b border-gray-100">
                    <dt class="font-semibold text-gray-500 mb-1">Jenis Tindakan</dt>
                    <dd class="font-bold text-gray-900 text-sm">{{ $tindakLanjut->jenis_label }}</dd>
                </div>

                <div class="pb-3 border-b border-gray-100">
                    <dt class="font-semibold text-gray-500 mb-1">Status Pengerjaan</dt>
                    <dd>
                        <x-badge :variant="$tindakLanjut->status_badge_variant" :dot="true">
                            {{ $tindakLanjut->status_label }}
                        </x-badge>
                    </dd>
                </div>

                <div class="pb-3 border-b border-gray-100">
                    <dt class="font-semibold text-gray-500 mb-1">Tingkat Prioritas</dt>
                    <dd class="font-medium text-gray-900">{{ $tindakLanjut->prioritas_label }}</dd>
                </div>

                <div class="pb-3 border-b border-gray-100">
                    <dt class="font-semibold text-gray-500 mb-1">Petugas Pelaksana</dt>
                    <dd class="font-semibold text-gray-900">{{ $tindakLanjut->petugas_pelaksana }}</dd>
                </div>

                <div class="pb-3 border-b border-gray-100">
                    <dt class="font-semibold text-gray-500 mb-1">Tanggal Pelaksanaan</dt>
                    <dd class="font-mono font-medium text-gray-900 tabular-nums">
                        {{ $tindakLanjut->tanggal_tindakan->format('d/m/Y') }}
                    </dd>
                </div>

                <div class="pb-3 border-b border-gray-100">
                    <dt class="font-semibold text-gray-500 mb-1">Tanggal Selesai</dt>
                    <dd class="font-mono font-medium text-gray-900 tabular-nums">
                        @if($tindakLanjut->tanggal_selesai)
                            <span class="text-emerald-700 font-bold">{{ $tindakLanjut->tanggal_selesai->format('d/m/Y') }}</span>
                        @else
                            <span class="text-gray-400 italic">Belum selesai</span>
                        @endif
                    </dd>
                </div>
            </dl>

            <!-- Uraian Tindakan -->
            <div class="pt-2 border-t border-gray-100 text-xs">
                <span class="block font-semibold text-gray-500 mb-1">Uraian Rincian Tindakan</span>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-800 leading-relaxed font-medium">
                    {{ $tindakLanjut->uraian_tindakan }}
                </div>
            </div>

            <!-- Catatan Tambahan -->
            @if($tindakLanjut->catatan)
                <div class="pt-3 border-t border-gray-100 text-xs">
                    <span class="block font-semibold text-gray-500 mb-1">Catatan Tambahan & Hasil Evaluasi</span>
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 whitespace-pre-line leading-relaxed">
                        {{ $tindakLanjut->catatan }}
                    </div>
                </div>
            @endif
        </x-card>

        <!-- 5. Foto Dokumentasi Pemeliharaan (jika ada) -->
        @if($tindakLanjut->foto_path)
            <x-card title="Dokumentasi Visual Pemeliharaan" subtitle="Lampiran foto hasil perbaikan unit tabung APAR.">
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 inline-block">
                    <img src="{{ asset('storage/' . $tindakLanjut->foto_path) }}"
                         alt="Foto Dokumentasi Tindak Lanjut {{ $tindakLanjut->apar?->kode ?? ('#' . $tindakLanjut->apar_id) }}"
                         class="max-w-md w-full h-auto rounded-lg border border-gray-300 shadow-xs object-cover">
                </div>
            </x-card>
        @endif

        <!-- 6. Footer Navigation & Actions -->
        <div class="p-4 bg-white border border-gray-200 rounded-xl flex flex-wrap items-center justify-between gap-3 shadow-xs no-print">
            <div class="flex items-center gap-2">
                @if($tindakLanjut->apar && ! $tindakLanjut->apar->trashed())
                    <a href="{{ route('apar.show', $tindakLanjut->apar_id) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition min-h-[44px]">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke Detail APAR</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 font-semibold text-xs min-h-[44px] cursor-not-allowed">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Detail APAR (Unit Terhapus)</span>
                    </span>
                @endif

                @if($tindakLanjut->pemeriksaan_id)
                    <a href="{{ route('pemeriksaan.show', $tindakLanjut->pemeriksaan_id) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition min-h-[44px]">
                        <span>Lembar Pemeriksaan</span>
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('tindak-lanjut.edit', $tindakLanjut->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#111827] hover:bg-gray-800 text-white font-semibold text-xs transition shadow-xs min-h-[44px]">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span>Edit / Update Data</span>
                </a>

                @if(auth()->user()?->isAdmin() && $tindakLanjut->status !== 'dibatalkan')
                    <button type="button"
                            onclick="openBatalkanModal()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-rose-200 bg-white hover:bg-rose-50 text-rose-700 font-semibold text-xs transition min-h-[44px] cursor-pointer">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Batalkan Tiket</span>
                    </button>
                @endif
            </div>
        </div>

        @if(auth()->user()?->isAdmin() && $tindakLanjut->status !== 'dibatalkan')
            <!-- Modal Pembatalan Tiket Tindak Lanjut (Audit K3RS) -->
            <div id="hafis-batalkan-modal"
                 class="hidden fixed inset-0 z-60 overflow-y-auto overscroll-contain p-4 flex items-center justify-center"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="hafis-batalkan-title">
                <!-- Backdrop Gelap -->
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeBatalkanModal()" aria-hidden="true"></div>

                <!-- Kotak Dialog Panel -->
                <div class="relative bg-white rounded-2xl border border-gray-200 shadow-2xl w-full max-w-lg p-5 sm:p-6 overflow-hidden transform transition-all">
                    <form action="{{ route('tindak-lanjut.batalkan', $tindakLanjut->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 id="hafis-batalkan-title" class="text-sm sm:text-base font-bold text-gray-900 tracking-tight">
                                    Batalkan Tiket Tindak Lanjut #{{ $tindakLanjut->id }}
                                </h3>
                                <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                    Tiket ini akan ditandai sebagai <strong>Dibatalkan</strong> dan tidak dihapus secara permanen guna memenuhi standar audit K3RS dan akreditasi rumah sakit.
                                </p>
                            </div>
                        </div>

                        <div>
                            <label for="alasan_pembatalan" class="block font-semibold text-gray-800 text-xs mb-1">
                                Alasan Pembatalan Resmi <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="alasan_pembatalan"
                                      name="alasan_pembatalan"
                                      rows="3"
                                      required
                                      minlength="5"
                                      maxlength="1000"
                                      placeholder="Contoh: Temuan sudah diverifikasi ulang dan dinyatakan normal / Telah ditangani melalui tiket pemeliharaan lain..."
                                      class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 leading-relaxed"></textarea>
                            <p class="text-[11px] text-gray-500 mt-1">Alasan pembatalan dan identitas admin akan dicatat permanen dalam riwayat audit.</p>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
                            <button type="button"
                                    onclick="closeBatalkanModal()"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold min-h-[40px] cursor-pointer transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold min-h-[40px] cursor-pointer transition-colors shadow-xs">
                                Konfirmasi Pembatalan Tiket
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openBatalkanModal() {
                    const modal = document.getElementById('hafis-batalkan-modal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        const textarea = document.getElementById('alasan_pembatalan');
                        if (textarea) textarea.focus();
                    }
                }

                function closeBatalkanModal() {
                    const modal = document.getElementById('hafis-batalkan-modal');
                    if (modal) {
                        modal.classList.add('hidden');
                    }
                }

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeBatalkanModal();
                    }
                });
            </script>
        @endif
    </div>
</x-layouts.app>
