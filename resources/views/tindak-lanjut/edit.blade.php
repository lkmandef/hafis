<x-layouts.app title="HAFIS : Edit Tindak Lanjut {{ $tindakLanjut->apar?->kode ?? ('#' . $tindakLanjut->apar_id) }}">
    <!-- 1. Page Header with Breadcrumb -->
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
            <a href="{{ route('tindak-lanjut.show', $tindakLanjut->id) }}" class="hover:text-gray-900 transition-colors">Tindak Lanjut #{{ $tindakLanjut->id }}</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-semibold">Edit Data</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                        Edit Tindak Lanjut & Pemeliharaan
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
                    Perbarui data pengerjaan perbaikan atau ubah status penyelesaian tindakan.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('tindak-lanjut.show', $tindakLanjut->id) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Batal / Kembali ke Detail</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Error Validasi -->
    @if($errors->any())
        <div class="max-w-4xl mx-auto mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50/70 text-rose-900 text-xs shadow-xs" role="alert">
            <div class="flex items-start gap-2.5">
                <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </span>
                <div>
                    <h4 class="font-bold text-rose-900 mb-1">Terdapat kesalahan pada isian form:</h4>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- 2. Ringkasan Sumber Masalah (Readonly) -->
        <x-card title="Konteks Temuan Masalah" subtitle="Identitas unit APAR dan temuan pemeriksaan fisik yang mendasari tindakan ini.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 space-y-1">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Unit APAR & Lokasi</span>
                    <span class="font-bold text-gray-900 block">
                        <span class="font-mono">{{ $tindakLanjut->apar?->kode ?? ('APAR #' . $tindakLanjut->apar_id) }}</span>
                        @if($tindakLanjut->apar?->trashed())
                            <span class="inline-block text-[10px] text-rose-700 bg-rose-50 px-1 py-0.5 rounded border border-rose-200 ml-1">Terhapus</span>
                        @endif
                        @if($tindakLanjut->apar)
                            &bull; {{ $tindakLanjut->apar->jenis_media }} (<span class="font-mono">{{ $tindakLanjut->apar->kapasitas }}</span>)
                        @endif
                    </span>
                    <span class="text-gray-600 block text-[11px]">{{ $tindakLanjut->apar?->lokasi_lengkap ?? '-' }}</span>
                </div>

                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 space-y-1">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Dasar Temuan Masalah</span>
                    <span class="font-semibold text-rose-950 block">
                        {{ $tindakLanjut->temuan_teks }}
                    </span>
                    @if($tindakLanjut->pemeriksaan)
                        <span class="text-gray-500 block text-[11px]">
                            Inspeksi oleh {{ $tindakLanjut->pemeriksaan->pemeriksa_nama }} (<span class="font-mono">{{ $tindakLanjut->pemeriksaan->tanggal_pemeriksaan->format('d/m/Y') }}</span>)
                        </span>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- 3. Form Update Tindak Lanjut -->
        <form action="{{ route('tindak-lanjut.update', $tindakLanjut->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-card title="Perbarui Data Tindakan Pemeliharaan" subtitle="Sesuaikan jenis tindakan, status pekerjaan, dan tanggal penyelesaian.">
                <div class="space-y-5 text-xs">
                    <!-- Baris 1: Jenis Tindakan & Prioritas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="jenis_tindakan" class="block font-semibold text-gray-800 mb-1">
                                Jenis Tindakan <span class="text-rose-500">*</span>
                            </label>
                            <select id="jenis_tindakan" name="jenis_tindakan" required
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 min-h-[44px]">
                                <option value="perbaikan" {{ old('jenis_tindakan', $tindakLanjut->jenis_tindakan) === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                <option value="pengisian_ulang" {{ old('jenis_tindakan', $tindakLanjut->jenis_tindakan) === 'pengisian_ulang' ? 'selected' : '' }}>Pengisian Ulang</option>
                                <option value="pembersihan" {{ old('jenis_tindakan', $tindakLanjut->jenis_tindakan) === 'pembersihan' ? 'selected' : '' }}>Pembersihan</option>
                                <option value="penggantian_komponen" {{ old('jenis_tindakan', $tindakLanjut->jenis_tindakan) === 'penggantian_komponen' ? 'selected' : '' }}>Penggantian Komponen</option>
                                <option value="pemeriksaan_ulang" {{ old('jenis_tindakan', $tindakLanjut->jenis_tindakan) === 'pemeriksaan_ulang' ? 'selected' : '' }}>Pemeriksaan Ulang</option>
                                <option value="lainnya" {{ old('jenis_tindakan', $tindakLanjut->jenis_tindakan) === 'lainnya' ? 'selected' : '' }}>Tindakan Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label for="prioritas" class="block font-semibold text-gray-800 mb-1">
                                Prioritas Tindakan <span class="text-rose-500">*</span>
                            </label>
                            <select id="prioritas" name="prioritas" required
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 min-h-[44px]">
                                <option value="sedang" {{ old('prioritas', $tindakLanjut->prioritas) === 'sedang' ? 'selected' : '' }}>Sedang (Standar)</option>
                                <option value="tinggi" {{ old('prioritas', $tindakLanjut->prioritas) === 'tinggi' ? 'selected' : '' }}>Tinggi (Mendesak / Segera)</option>
                                <option value="rendah" {{ old('prioritas', $tindakLanjut->prioritas) === 'rendah' ? 'selected' : '' }}>Rendah (Rutin)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 2: Uraian Tindakan -->
                    <div>
                        <label for="uraian_tindakan" class="block font-semibold text-gray-800 mb-1">
                            Uraian Tindakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="uraian_tindakan" name="uraian_tindakan" rows="3" required
                                  class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-xs text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 leading-relaxed">{{ old('uraian_tindakan', $tindakLanjut->uraian_tindakan) }}</textarea>
                    </div>

                    <!-- Baris 3: Status, Pelaksana, Tanggal Tindakan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="status" class="block font-semibold text-gray-800 mb-1">
                                Status Tindak Lanjut <span class="text-rose-500">*</span>
                            </label>
                            <select id="status" name="status" required
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 min-h-[44px]">
                                <option value="belum_ditindaklanjuti" {{ old('status', $tindakLanjut->status) === 'belum_ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                                <option value="dalam_proses" {{ old('status', $tindakLanjut->status) === 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                                <option value="selesai" {{ old('status', $tindakLanjut->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                @if(auth()->user()?->isAdmin())
                                    <option value="dibatalkan" {{ old('status', $tindakLanjut->status) === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                @elseif($tindakLanjut->status === 'dibatalkan')
                                    <option value="dibatalkan" selected disabled>Dibatalkan (Hanya Admin)</option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label for="petugas_pelaksana" class="block font-semibold text-gray-800 mb-1">
                                Petugas Pelaksana <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="petugas_pelaksana" name="petugas_pelaksana" required
                                   value="{{ old('petugas_pelaksana', $tindakLanjut->petugas_pelaksana) }}"
                                   class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 min-h-[44px]">
                        </div>

                        <div>
                            <label for="tanggal_tindakan" class="block font-semibold text-gray-800 mb-1">
                                Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="tanggal_tindakan" name="tanggal_tindakan" required
                                   value="{{ old('tanggal_tindakan', $tindakLanjut->tanggal_tindakan->format('Y-m-d')) }}"
                                   class="w-full font-mono rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 min-h-[44px]">
                        </div>
                    </div>

                    <!-- Baris 4: Tanggal Selesai & Foto Dokumentasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_selesai" class="block font-semibold text-gray-800 mb-1">
                                Tanggal Selesai
                            </label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                   value="{{ old('tanggal_selesai', $tindakLanjut->tanggal_selesai?->format('Y-m-d')) }}"
                                   class="w-full font-mono rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 min-h-[44px]">
                            <span class="text-[11px] text-gray-400 mt-1 block">Otomatis terisi jika status disimpan sebagai Selesai.</span>
                        </div>

                        <div>
                            <label for="foto" class="block font-semibold text-gray-800 mb-1">
                                Foto Dokumentasi Baru (Opsional)
                            </label>
                            <input type="file" id="foto" name="foto" accept="image/*"
                                   class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200 min-h-[44px]">
                            @if($tindakLanjut->foto_path)
                                <span class="text-[11px] text-emerald-600 mt-1 block">File dokumentasi saat ini sudah tersimpan. Upload baru untuk mengganti.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Baris 5: Catatan Tambahan -->
                    <div>
                        <label for="catatan" class="block font-semibold text-gray-800 mb-1">
                            Catatan Tambahan & Hasil Akhir (Opsional)
                        </label>
                        <textarea id="catatan" name="catatan" rows="3"
                                  placeholder="Catatan teknis hasil pengerjaan, serial part pengganti, atau saran pemeliharaan selanjutnya."
                                  class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-xs text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 leading-relaxed">{{ old('catatan', $tindakLanjut->catatan) }}</textarea>
                    </div>

                    <!-- Opsi Pembaruan Kondisi Unit APAR (Section 8) -->
                    <div class="pt-3 border-t border-gray-200">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="update_kondisi_apar" value="1" {{ old('update_kondisi_apar') ? 'checked' : '' }}
                                   class="mt-0.5 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <div>
                                <span class="font-semibold text-gray-800 block">
                                    Perbarui status kondisi operasional unit APAR menjadi "Baik" jika status tindakan adalah Selesai
                                </span>
                                <span class="text-gray-500 text-[11px] block mt-0.5">
                                    Status kondisi tabung APAR saat ini adalah <strong class="text-gray-700">{{ $tindakLanjut->apar?->status_label ?? '-' }}</strong>.
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="mt-6 pt-4 border-t border-gray-200 flex flex-wrap items-center justify-end gap-3">
                    <a href="{{ route('tindak-lanjut.show', $tindakLanjut->id) }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition min-h-[44px]">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg bg-[#111827] hover:bg-gray-800 text-white font-semibold text-xs transition shadow-xs min-h-[44px]">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </x-card>
        </form>
    </div>
</x-layouts.app>
