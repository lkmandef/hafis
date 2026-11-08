<x-layouts.app title="HAFIS : Detail Tindak Lanjut {{ $tindakLanjut->apar->kode }}">
    <!-- 1. Header with Breadcrumb & Navigation -->
    <div class="pb-5 mb-6 border-b border-gray-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('apar.index') }}" class="hover:text-gray-900 transition-colors">Data APAR</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('apar.show', $tindakLanjut->apar_id) }}" class="hover:text-gray-900 transition-colors">{{ $tindakLanjut->apar->kode }}</a>
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
                        {{ $tindakLanjut->apar->kode }}
                    </span>
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

                <a href="{{ route('apar.show', $tindakLanjut->apar_id) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-white bg-[#111827] hover:bg-gray-800 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Detail APAR</span>
                </a>
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
        <!-- 2. Alur Transisi Status Tindak Lanjut Cepat (Section 13) -->
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

        <!-- 3. Informasi APAR & Sumber Pemeriksaan (Section 10) -->
        <x-card title="Informasi APAR & Sumber Pemeriksaan" subtitle="Data identitas tabung serta konteks pemeriksaan fisik yang mendasarinya.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <!-- Kolom Kiri: Informasi APAR -->
                <div class="space-y-3 pb-4 md:pb-0 md:pr-6 border-b md:border-b-0 md:border-r border-gray-100">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Identitas Unit APAR</span>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Kode APAR</span>
                        <a href="{{ route('apar.show', $tindakLanjut->apar_id) }}" class="font-mono font-bold text-gray-900 bg-gray-100 hover:bg-gray-200 px-2.5 py-0.5 rounded border border-gray-200 transition-colors">
                            {{ $tindakLanjut->apar->kode }}
                        </a>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Jenis Media & Kapasitas</span>
                        <span class="font-semibold text-gray-900">{{ $tindakLanjut->apar->jenis_media }} (<span class="font-mono">{{ $tindakLanjut->apar->kapasitas }}</span>)</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Merek / Tipe</span>
                        <span class="text-gray-800 {{ ! $tindakLanjut->apar->merek ? 'text-gray-400 italic' : '' }}">{{ $tindakLanjut->apar->merek ?: 'Belum terdata' }} / {{ $tindakLanjut->apar->tipe_model ?: '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Kondisi Tabung Saat Ini</span>
                        <x-badge :variant="$tindakLanjut->apar->status_badge_variant" :dot="true">
                            {{ $tindakLanjut->apar->status_label }}
                        </x-badge>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-gray-500 font-medium block mb-0.5">Lokasi Penempatan</span>
                        <span class="font-semibold text-gray-900 block">{{ $tindakLanjut->apar->lokasi_lengkap }}</span>
                        @if($tindakLanjut->apar->posisi_detail)
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
                         alt="Foto Dokumentasi Tindak Lanjut {{ $tindakLanjut->apar->kode }}"
                         class="max-w-md w-full h-auto rounded-lg border border-gray-300 shadow-xs object-cover">
                </div>
            </x-card>
        @endif

        <!-- 6. Footer Navigation & Actions -->
        <div class="p-4 bg-white border border-gray-200 rounded-xl flex flex-wrap items-center justify-between gap-3 shadow-xs no-print">
            <div class="flex items-center gap-2">
                <a href="{{ route('apar.show', $tindakLanjut->apar_id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition min-h-[44px]">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Detail APAR</span>
                </a>

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

                <form action="{{ route('tindak-lanjut.destroy', $tindakLanjut->id) }}" method="POST"
                      data-confirm
                      data-confirm-title="Hapus Catatan Tindak Lanjut"
                      data-confirm-message="Apakah Anda yakin ingin menghapus catatan tindak lanjut ini dari riwayat? Tindakan ini bersifat permanen dan tidak dapat dibatalkan."
                      data-confirm-type="danger"
                      data-confirm-text="Hapus Catatan">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-3.5 py-2.5 rounded-lg border border-rose-200 bg-white hover:bg-rose-50 text-rose-700 font-semibold text-xs transition min-h-[44px] cursor-pointer">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
