<x-layouts.app title="HAFIS : Buat Tindak Lanjut APAR {{ $apar->kode }}">
    <!-- 1. Page Header with Breadcrumb -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('apar.index') }}" class="hover:text-slate-800 transition-colors">Master Data APAR</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('apar.show', $apar->id) }}" class="hover:text-slate-800 transition-colors">{{ $apar->kode }}</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('pemeriksaan.show', $pemeriksaan->id) }}" class="hover:text-slate-800 transition-colors">Hasil Pemeriksaan</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Buat Tindak Lanjut</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Form Tindak Lanjut & Pemeliharaan
                    </h1>
                    <span class="font-mono text-base font-extrabold text-slate-900 bg-white border border-slate-200 px-3 py-0.5 rounded-lg tabular-nums shadow-2xs">
                        {{ $apar->kode }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Pencatatan penanganan dan perbaikan unit APAR berdasarkan hasil temuan pemeriksaan fisik.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('pemeriksaan.show', $pemeriksaan->id) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Pemeriksaan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Error Validasi -->
    @if($errors->any())
        <div class="max-w-4xl mx-auto mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs shadow-2xs" role="alert">
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
        <!-- 2. Informasi Otomatis Sumber Temuan (Readonly Context Cards) -->
        <x-card title="Informasi Sumber Temuan" subtitle="Data unit APAR dan temuan pemeriksaan fisik yang menjadi dasar tindakan.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <!-- Kolom Kiri: APAR & Lokasi -->
                <div class="space-y-3 pb-4 md:pb-0 md:pr-6 border-b md:border-b-0 md:border-r border-slate-100">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Identitas APAR</span>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Kode Tabung</span>
                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $apar->kode }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Media & Kapasitas</span>
                        <span class="font-semibold text-slate-900">{{ $apar->jenis_media }} ({{ $apar->kapasitas }})</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Status Kondisi APAR</span>
                        <x-badge :variant="$apar->status_badge_variant" :dot="true">
                            {{ $apar->status_label }}
                        </x-badge>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-500 font-medium block mb-0.5">Lokasi Penempatan</span>
                        <span class="font-semibold text-slate-900 block">{{ $apar->lokasi_lengkap }}</span>
                        @if($apar->posisi_detail)
                            <span class="text-slate-500 text-[11px] italic block mt-0.5">{{ $apar->posisi_detail }}</span>
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan: Sesi Pemeriksaan & Temuan -->
                <div class="space-y-3">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pemeriksaan Sumber</span>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Tanggal Pemeriksaan</span>
                        <span class="font-mono font-medium text-slate-900 tabular-nums">
                            {{ $pemeriksaan->tanggal_pemeriksaan->format('d/m/Y H:i') }} WIB
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Pemeriksa</span>
                        <span class="font-semibold text-slate-900">{{ $pemeriksaan->pemeriksa_nama }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Hasil Keseluruhan</span>
                        <x-badge :variant="$pemeriksaan->hasil_badge_variant" :dot="true">
                            {{ $pemeriksaan->hasil_label }}
                        </x-badge>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-500 font-medium block mb-1">Dasar Temuan Masalah:</span>
                        @if($detail)
                            <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold text-rose-950 text-xs">{{ $detail->item_nama }}</span>
                                    <x-badge variant="danger" :dot="true">{{ $detail->hasil_label }}</x-badge>
                                </div>
                                <p class="text-rose-800 text-[11px] mt-1 italic">
                                    Catatan: {{ $detail->catatan ?: 'Tidak ada catatan spesifik.' }}
                                </p>
                            </div>
                        @else
                            <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200">
                                <span class="font-bold text-amber-950 text-xs block">Tindak Lanjut Umum Pemeriksaan</span>
                                <p class="text-amber-800 text-[11px] mt-0.5">
                                    {{ $pemeriksaan->catatan ?: 'Tindakan pemeliharaan berkala untuk unit APAR ini.' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>

        <!-- 3. Form Input Tindak Lanjut -->
        <form action="{{ route('tindak-lanjut.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="apar_id" value="{{ $apar->id }}">
            <input type="hidden" name="pemeriksaan_id" value="{{ $pemeriksaan->id }}">
            @if($detail)
                <input type="hidden" name="pemeriksaan_detail_id" value="{{ $detail->id }}">
            @endif

            <x-card title="Rincian Tindakan Pemeliharaan" subtitle="Isi informasi tindakan, pelaksana, dan status penanganan unit.">
                <div class="space-y-5 text-xs">
                    <!-- Baris 1: Jenis Tindakan & Prioritas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="jenis_tindakan" class="block font-bold text-slate-800 mb-1">
                                Jenis Tindakan <span class="text-rose-500">*</span>
                            </label>
                            <select id="jenis_tindakan" name="jenis_tindakan" required
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 min-h-[44px]">
                                <option value="">-- Pilih Jenis Tindakan --</option>
                                <option value="perbaikan" {{ old('jenis_tindakan') === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                <option value="pengisian_ulang" {{ old('jenis_tindakan') === 'pengisian_ulang' ? 'selected' : '' }}>Pengisian Ulang</option>
                                <option value="pembersihan" {{ old('jenis_tindakan') === 'pembersihan' ? 'selected' : '' }}>Pembersihan</option>
                                <option value="penggantian_komponen" {{ old('jenis_tindakan') === 'penggantian_komponen' ? 'selected' : '' }}>Penggantian Komponen</option>
                                <option value="pemeriksaan_ulang" {{ old('jenis_tindakan') === 'pemeriksaan_ulang' ? 'selected' : '' }}>Pemeriksaan Ulang</option>
                                <option value="lainnya" {{ old('jenis_tindakan') === 'lainnya' ? 'selected' : '' }}>Tindakan Lainnya</option>
                            </select>
                            <span class="text-[11px] text-slate-400 mt-1 block">Tentukan jenis penanganan yang diperlukan pada unit APAR.</span>
                        </div>

                        <div>
                            <label for="prioritas" class="block font-bold text-slate-800 mb-1">
                                Prioritas Tindakan <span class="text-rose-500">*</span>
                            </label>
                            <select id="prioritas" name="prioritas" required
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 min-h-[44px]">
                                <option value="sedang" {{ old('prioritas', 'sedang') === 'sedang' ? 'selected' : '' }}>Sedang (Standar)</option>
                                <option value="tinggi" {{ old('prioritas') === 'tinggi' ? 'selected' : '' }}>Tinggi (Mendesak / Segera)</option>
                                <option value="rendah" {{ old('prioritas') === 'rendah' ? 'selected' : '' }}>Rendah (Rutin)</option>
                            </select>
                            <span class="text-[11px] text-slate-400 mt-1 block">Tingkat urgensi penanganan masalah APAR.</span>
                        </div>
                    </div>

                    <!-- Baris 2: Uraian Tindakan (Textarea) -->
                    <div>
                        <label for="uraian_tindakan" class="block font-bold text-slate-800 mb-1">
                            Uraian Tindakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="uraian_tindakan" name="uraian_tindakan" rows="3" required
                                  placeholder="Contoh: Selang diganti karena terdapat keretakan pada bagian sambungan nozzle."
                                  class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 leading-relaxed">{{ old('uraian_tindakan') }}</textarea>
                        <span class="text-[11px] text-slate-400 mt-1 block">Jelaskan secara spesifik tindakan pemeliharaan yang direncanakan atau telah dikerjakan.</span>
                    </div>

                    <!-- Baris 3: Status, Pelaksana, Tanggal Tindakan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="status" class="block font-bold text-slate-800 mb-1">
                                Status Tindak Lanjut <span class="text-rose-500">*</span>
                            </label>
                            <select id="status" name="status" required
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 min-h-[44px]">
                                <option value="belum_ditindaklanjuti" {{ old('status', 'belum_ditindaklanjuti') === 'belum_ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                                <option value="dalam_proses" {{ old('status') === 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                                <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        <div>
                            <label for="petugas_pelaksana" class="block font-bold text-slate-800 mb-1">
                                Petugas Pelaksana <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="petugas_pelaksana" name="petugas_pelaksana" required
                                   value="{{ old('petugas_pelaksana', $petugasDefault) }}"
                                   placeholder="Nama teknisi / petugas K3RS"
                                   class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 min-h-[44px]">
                        </div>

                        <div>
                            <label for="tanggal_tindakan" class="block font-bold text-slate-800 mb-1">
                                Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="tanggal_tindakan" name="tanggal_tindakan" required
                                   value="{{ old('tanggal_tindakan', $tanggalHariIni) }}"
                                   class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 min-h-[44px]">
                        </div>
                    </div>

                    <!-- Baris 4: Tanggal Selesai & Foto Dokumentasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_selesai" class="block font-bold text-slate-800 mb-1">
                                Tanggal Selesai (Opsional)
                            </label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                   value="{{ old('tanggal_selesai') }}"
                                   class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 min-h-[44px]">
                            <span class="text-[11px] text-slate-400 mt-1 block">Wajib diisi jika status dipilih Selesai, atau otomatis terisi tanggal hari ini.</span>
                        </div>

                        <div>
                            <label for="foto" class="block font-bold text-slate-800 mb-1">
                                Foto Dokumentasi Tindakan (Opsional)
                            </label>
                            <input type="file" id="foto" name="foto" accept="image/*"
                                   class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 min-h-[44px]">
                            <span class="text-[11px] text-slate-400 mt-1 block">Maksimal 5 MB. Format JPG, PNG.</span>
                        </div>
                    </div>

                    <!-- Baris 5: Catatan Tambahan -->
                    <div>
                        <label for="catatan" class="block font-bold text-slate-800 mb-1">
                            Catatan Tambahan (Opsional)
                        </label>
                        <textarea id="catatan" name="catatan" rows="2"
                                  placeholder="Catatan teknis hasil pengerjaan, serial part pengganti, atau saran pemeliharaan selanjutnya."
                                  class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 leading-relaxed">{{ old('catatan') }}</textarea>
                    </div>

                    <!-- Opsi Pembaruan Kondisi Unit APAR (Section 8) -->
                    <div class="pt-3 border-t border-slate-200">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="update_kondisi_apar" value="1" {{ old('update_kondisi_apar') ? 'checked' : '' }}
                                   class="mt-0.5 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            <div>
                                <span class="font-bold text-slate-800 block">
                                    Perbarui status operasional APAR menjadi "Baik" jika tindakan ini selesai
                                </span>
                                <span class="text-slate-500 text-[11px] block mt-0.5">
                                    Centang jika penanganan masalah telah tuntas sehingga unit tabung APAR siap digunakan kembali secara operasional.
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="mt-6 pt-4 border-t border-slate-200 flex flex-wrap items-center justify-end gap-3">
                    <a href="{{ route('pemeriksaan.show', $pemeriksaan->id) }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition min-h-[44px]">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-2xs min-h-[44px]">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Simpan Tindak Lanjut</span>
                    </button>
                </div>
            </x-card>
        </form>
    </div>
</x-layouts.app>
