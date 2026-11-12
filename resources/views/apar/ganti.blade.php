<x-layouts.app title="HAFIS : Penggantian Unit APAR {{ $aparLama->kode }}">
    <!-- Header with Breadcrumb & Title -->
    <div class="pb-5 mb-6 border-b border-gray-200">
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('apar.index') }}" class="hover:text-gray-900 transition-colors">Data APAR</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('apar.show', $aparLama->id) }}" class="hover:text-gray-900 transition-colors font-mono">{{ $aparLama->kode }}</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">Penggantian Unit</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight flex items-center gap-2.5">
                    <span>Penggantian Unit APAR:</span>
                    <span class="font-mono text-gray-900 bg-gray-100 border border-gray-200 px-2.5 py-0.5 rounded text-lg">{{ $aparLama->kode }}</span>
                </h1>
                <p class="text-xs text-gray-500 mt-1">
                    Formulir resmi penggantian unit tabung APAR secara menyeluruh. Seluruh riwayat unit lama tetap tersimpan sebagai rekaman historis rumah sakit.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <x-button variant="secondary" href="{{ route('apar.show', $aparLama->id) }}">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Detail</span>
                </x-button>
            </div>
        </div>
    </div>

    <!-- Feedback Errors -->
    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl border border-red-200 bg-red-50 text-red-900 text-xs" role="alert">
            <div class="flex items-center gap-2 mb-2 font-semibold text-red-900">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>Mohon perbaiki kendala pada formulir penggantian berikut:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('apar.ganti.store', $aparLama->id) }}" method="POST" id="form-penggantian-apar"
          data-confirm
          data-confirm-title="Proses Penggantian Unit APAR"
          data-confirm-message="Apakah Anda yakin ingin memproses penggantian unit APAR ini? Unit lama akan dinonaktifkan dari operasional aktif dan digantikan oleh data unit fisik baru."
          data-confirm-type="warning"
          data-confirm-text="Proses Penggantian">
        @csrf

        <div class="space-y-6">

            <!-- ==============================================================
                 BAGIAN 1: INFORMASI APAR LAMA (READ-ONLY)
                 ============================================================== -->
            <x-card title="Informasi Unit APAR Lama" subtitle="Unit ini akan dinonaktifkan dari operasional aktif dan disimpan sebagai rekaman riwayat.">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 rounded-xl bg-gray-50 border border-gray-200 text-xs">
                    <div>
                        <span class="block text-gray-500 font-medium mb-0.5">Kode APAR Lama</span>
                        <span class="font-mono font-semibold text-gray-900 text-sm block">{{ $aparLama->kode }}</span>
                        @if($aparLama->nomor_register)
                            <span class="text-[11px] text-gray-500 font-mono block">Reg: {{ $aparLama->nomor_register }}</span>
                        @endif
                    </div>

                    <div>
                        <span class="block text-gray-500 font-medium mb-0.5">Media & Kapasitas</span>
                        <span class="font-medium text-gray-900 block">{{ $aparLama->jenis_media }}</span>
                        <span class="text-[11px] text-gray-500 font-mono block">Kapasitas: {{ $aparLama->kapasitas }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-500 font-medium mb-0.5">Lokasi Penempatan Saat Ini</span>
                        <span class="font-medium text-gray-900 block">{{ $aparLama->lokasi_lengkap }}</span>
                        <span class="text-[11px] text-gray-500 block truncate">{{ $aparLama->posisi_detail ?: 'Titik umum' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-500 font-medium mb-0.5">Status Kondisi Fisik</span>
                        <div class="mt-0.5">
                            <x-badge :variant="$aparLama->status_badge_variant" :dot="true">
                                {{ $aparLama->status_label }}
                            </x-badge>
                        </div>
                    </div>
                </div>

            </x-card>

            <!-- ==============================================================
                 BAGIAN 2: INFORMASI UNIT APAR BARU
                 ============================================================== -->
            <x-card title="Data Unit APAR Baru" subtitle="Identitas fisik dan spesifikasi teknis unit tabung pengganti.">
                <div class="space-y-6">

                    <!-- Identitas Utama -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-700 inline-flex items-center justify-center text-xs font-semibold">1</span>
                            <span>Identitas Unit Baru</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input
                                    name="kode"
                                    label="Kode APAR Baru"
                                    placeholder="Contoh: {{ $aparLama->kode }}"
                                    value="{{ old('kode', $aparLama->kode) }}"
                                    :required="true"
                                    :error="$errors->first('kode')"
                                    hint="Tetap gunakan kode yang sama (kode titik proteksi RS) atau sesuaikan jika ada penomoran baru" />
                            </div>

                            <div>
                                <x-input
                                    name="nomor_register"
                                    label="Nomor Register Aset Baru"
                                    placeholder="Contoh: REG-RS-2026-088"
                                    value="{{ old('nomor_register', $aparLama->nomor_register) }}"
                                    :error="$errors->first('nomor_register')"
                                    hint="Mewarisi nomor register unit lama jika tidak ada perubahan aset" />
                            </div>
                        </div>
                    </div>

                    <!-- Spesifikasi Teknis -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-700 inline-flex items-center justify-center text-xs font-semibold">2</span>
                            <span>Spesifikasi Teknis & Masa Berlaku</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="min-w-0">
                                <x-select
                                    name="jenis_media"
                                    label="Jenis Media Pemadam"
                                    :required="true"
                                    :error="$errors->first('jenis_media')"
                                    hint="Media pemadam tabung baru">
                                    @foreach($mediaOptions as $m)
                                        <option value="{{ $m }}" {{ old('jenis_media', $aparLama->jenis_media) === $m ? 'selected' : '' }}>
                                            {{ $m }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>

                            <div class="min-w-0">
                                <x-input
                                    name="kapasitas"
                                    label="Kapasitas Tabung"
                                    placeholder="Contoh: 6 kg / 3.5 kg"
                                    value="{{ old('kapasitas', $aparLama->kapasitas) }}"
                                    :required="true"
                                    :error="$errors->first('kapasitas')"
                                    hint="Ukuran berat isi tabung" />
                            </div>

                            <div class="min-w-0">
                                <x-input
                                    name="merek"
                                    label="Merek Tabung"
                                    placeholder="Contoh: Yamato, Servvo, Gunnebo"
                                    value="{{ old('merek', $aparLama->merek) }}"
                                    :error="$errors->first('merek')"
                                    hint="Produsen atau merek tabung" />
                            </div>

                            <div class="min-w-0">
                                <x-input
                                    name="tipe_model"
                                    label="Tipe / Model Fisik"
                                    placeholder="Contoh: Portable / Trolley"
                                    value="{{ old('tipe_model', $aparLama->tipe_model) }}"
                                    :error="$errors->first('tipe_model')"
                                    hint="Bentuk fisik tabung APAR" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div class="min-w-0">
                                <x-input
                                    type="date"
                                    name="tanggal_pemasangan"
                                    label="Tanggal Pemasangan Unit Baru"
                                    value="{{ old('tanggal_pemasangan', $tanggalHariIni) }}"
                                    :error="$errors->first('tanggal_pemasangan')"
                                    hint="Tanggal unit tabung mulai dipasang di lokasi" />
                            </div>

                            <div class="min-w-0">
                                <x-input
                                    type="date"
                                    name="tanggal_kadaluarsa"
                                    label="Tanggal Kadaluarsa Media Baru"
                                    value="{{ old('tanggal_kadaluarsa', $aparLama->tanggal_kadaluarsa ? $aparLama->tanggal_kadaluarsa->format('Y-m-d') : '') }}"
                                    :error="$errors->first('tanggal_kadaluarsa')"
                                    hint="Batas masa berlaku isi media pemadam baru (mewarisi data lama jika belum diubah)" />
                            </div>
                        </div>
                    </div>

                    <!-- Lokasi Penempatan Fisik -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between pb-2 mb-4 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-700 inline-flex items-center justify-center text-xs font-semibold">3</span>
                                <span>Lokasi Penempatan APAR Baru</span>
                            </h3>
                            <span class="text-xs text-gray-500">Mewarisi lokasi unit lama secara default</span>
                        </div>

                        @php
                            $defaultGedungId = old('gedung_id', $aparLama->gedung?->id);
                            $defaultLantaiId = old('lantai_id', $aparLama->lantai?->id);
                            $defaultRuanganId = old('ruangan_id', $aparLama->ruangan_id);
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Gedung -->
                            <div class="min-w-0">
                                <label for="gedung_id" class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Gedung <span class="text-red-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        id="gedung_id" 
                                        name="gedung_id" 
                                        required
                                        class="appearance-none w-full rounded-lg border {{ $errors->has('gedung_id') ? 'border-red-300 bg-red-50/20 text-red-900' : 'border-gray-200 bg-white text-gray-900' }} pl-3 pr-8 py-2 text-xs focus:bg-white focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 min-h-[40px] cursor-pointer">
                                        <option value="">-- Pilih Gedung --</option>
                                        @foreach($gedungs as $g)
                                            <option value="{{ $g->id }}" {{ $defaultGedungId == $g->id ? 'selected' : '' }}>
                                                {{ $g->nama }} ({{ $g->kode }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </span>
                                </div>
                                @if($errors->has('gedung_id'))
                                    <p class="text-[11px] text-red-600 font-medium mt-1">{{ $errors->first('gedung_id') }}</p>
                                @endif
                            </div>

                            <!-- Lantai -->
                            <div class="min-w-0">
                                <label for="lantai_id" class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Lantai <span class="text-red-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        id="lantai_id" 
                                        name="lantai_id" 
                                        required
                                        class="appearance-none w-full rounded-lg border {{ $errors->has('lantai_id') ? 'border-red-300 bg-red-50/20 text-red-900' : 'border-gray-200 bg-white text-gray-900' }} pl-3 pr-8 py-2 text-xs focus:bg-white focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 min-h-[40px] cursor-pointer">
                                        <option value="">-- Pilih Lantai --</option>
                                    </select>
                                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </span>
                                </div>
                                @if($errors->has('lantai_id'))
                                    <p class="text-[11px] text-red-600 font-medium mt-1">{{ $errors->first('lantai_id') }}</p>
                                @endif
                            </div>

                            <!-- Ruangan / Area -->
                            <div class="min-w-0">
                                <label for="ruangan_id" class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Ruangan / Area <span class="text-gray-400 font-normal">(Opsional untuk ruang tertutup)</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        id="ruangan_id" 
                                        name="ruangan_id" 
                                        class="appearance-none w-full rounded-lg border {{ $errors->has('ruangan_id') ? 'border-red-300 bg-red-50/20 text-red-900' : 'border-gray-200 bg-white text-gray-900' }} pl-3 pr-8 py-2 text-xs focus:bg-white focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 min-h-[40px] cursor-pointer">
                                        <option value="">-- Pilih Ruangan / Area (Opsional) --</option>
                                    </select>
                                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </span>
                                </div>
                                @if($errors->has('ruangan_id'))
                                    <p class="text-[11px] text-red-600 font-medium mt-1">{{ $errors->first('ruangan_id') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Posisi Penempatan Detail -->
                        <div class="mt-4">
                            <x-input
                                name="posisi_detail"
                                label="Detail Posisi / Titik Pasang"
                                placeholder="Contoh: Dinding pilar barat samping pintu darurat"
                                value="{{ old('posisi_detail', $aparLama->posisi_detail) }}"
                                :error="$errors->first('posisi_detail')"
                                hint="Deskripsi fisik spesifik penempatan unit baru" />
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- ==============================================================
                 BAGIAN 3: ALASAN & CATATAN PENGGANTIAN UNIT
                 ============================================================== -->
            <x-card title="Informasi & Riwayat Penggantian" subtitle="Dokumentasi operasional alasan pergantian unit untuk penelusuran K3RS.">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tanggal Penggantian -->
                        <div class="min-w-0">
                            <x-input
                                type="date"
                                name="tanggal_penggantian"
                                label="Tanggal Penggantian Unit"
                                value="{{ old('tanggal_penggantian', $tanggalHariIni) }}"
                                :required="true"
                                :error="$errors->first('tanggal_penggantian')"
                                hint="Tanggal pelaksanaan penggantian unit di lapangan" />
                        </div>

                        <!-- Petugas Pelaksana -->
                        <div class="min-w-0">
                            <x-input
                                name="petugas_nama"
                                label="Nama Petugas Pelaksana"
                                placeholder="Contoh: Budi Santoso (Petugas K3RS)"
                                value="{{ old('petugas_nama', $petugasDefault) }}"
                                :required="true"
                                :error="$errors->first('petugas_nama')"
                                hint="Personel pelaksana pergantian fisik tabung" />
                        </div>
                    </div>

                    <!-- Alasan Penggantian -->
                    <div>
                        <x-select
                            id="alasan"
                            name="alasan"
                            label="Alasan Penggantian Unit"
                            :required="true"
                            :error="$errors->first('alasan')"
                            hint="Penyebab utama penggantian seluruh unit tabung APAR">
                            <option value="">-- Pilih Alasan Penggantian --</option>
                            @foreach($alasanOptions as $aKey => $aLabel)
                                <option value="{{ $aKey }}" {{ old('alasan') === $aKey ? 'selected' : '' }}>
                                    {{ $aLabel }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <!-- Keterangan Alasan Lainnya -->
                    <div id="alasan-lainnya-wrapper" class="{{ old('alasan') === 'lainnya' ? '' : 'hidden' }}">
                        <x-input
                            id="alasan_keterangan"
                            name="alasan_keterangan"
                            label="Rincian Alasan Lainnya"
                            placeholder="Tuliskan alasan spesifik penggantian unit..."
                            value="{{ old('alasan_keterangan') }}"
                            :error="$errors->first('alasan_keterangan')"
                            hint="Wajib diisi jika memilih opsi Lainnya" />
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label for="catatan" class="block text-xs font-medium text-gray-700 mb-1.5">
                            Catatan Operasional Penggantian (Opsional)
                        </label>
                        <textarea
                            id="catatan"
                            name="catatan"
                            rows="3"
                            placeholder="Contoh: Unit lama mengalami korosi pada dasar tabung saat inspeksi berkala. Digantikan dengan unit baru bermedia sama."
                            class="w-full rounded-lg border {{ $errors->has('catatan') ? 'border-red-300 bg-red-50/20 text-red-900' : 'border-gray-200 bg-white text-gray-900' }} p-3 text-xs focus:bg-white focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition">{{ old('catatan') }}</textarea>
                        @if($errors->has('catatan'))
                            <p class="text-[11px] text-red-600 font-medium mt-1">{{ $errors->first('catatan') }}</p>
                        @endif
                    </div>
                </div>
            </x-card>

            <!-- ==============================================================
                 BAGIAN 4: KONFIRMASI DAN SUBMIT
                 ============================================================== -->
            <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/80 text-amber-950 text-xs">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-amber-600 text-white flex items-center justify-center shrink-0 mt-0.5 font-bold">
                        !
                    </span>
                    <div>
                        <h4 class="font-semibold text-amber-950 text-sm">Pernyataan Konfirmasi Penggantian APAR</h4>
                        <p class="text-amber-900 mt-1 leading-relaxed">
                            Dengan menyimpan formulir ini:
                        </p>
                        <ul class="list-disc list-inside mt-1 space-y-0.5 text-amber-900">
                            <li>Unit baru akan didaftarkan ke sistem dengan status <strong>Aktif</strong> dan QR Code baru yang unik.</li>
                            <li>Unit lama (<strong class="font-mono">{{ $aparLama->kode }}</strong>) akan dinonaktifkan dengan status <strong>Tidak Aktif (Sudah Diganti)</strong>.</li>
                            <li>Seluruh riwayat pemeriksaan, checklist, dan pemeliharaan unit lama <strong>tidak akan terhapus</strong> dan tetap tersimpan sebagai rekaman historis.</li>
                            <li>Tautan penelusuran dua arah antara unit lama dan unit baru akan tercatat permanen.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-6 border-t border-gray-200">
                <x-button variant="secondary" href="{{ route('apar.show', $aparLama->id) }}" class="w-full sm:w-auto">
                    Batal
                </x-button>

                <x-button variant="primary" type="submit" id="btn-submit-penggantian" class="w-full sm:w-auto">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Penggantian Unit APAR</span>
                </x-button>
            </div>
        </div>
    </form>

    <!-- Script Lokasi Berjenjang & Tampilan Alasan Lainnya -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lokasiTree = @json($gedungs);
            const gedungSelect = document.getElementById('gedung_id');
            const lantaiSelect = document.getElementById('lantai_id');
            const ruanganSelect = document.getElementById('ruangan_id');

            const defaultGedungId = '{{ $defaultGedungId }}';
            const defaultLantaiId = '{{ $defaultLantaiId }}';
            const defaultRuanganId = '{{ $defaultRuanganId }}';

            const alasanSelect = document.getElementById('alasan');
            const alasanLainnyaWrapper = document.getElementById('alasan-lainnya-wrapper');
            const alasanKeteranganInput = document.getElementById('alasan_keterangan');

            // Handle toggle rincian alasan lainnya
            if (alasanSelect) {
                alasanSelect.addEventListener('change', function () {
                    if (this.value === 'lainnya') {
                        alasanLainnyaWrapper.classList.remove('hidden');
                        if (alasanKeteranganInput) alasanKeteranganInput.focus();
                    } else {
                        alasanLainnyaWrapper.classList.add('hidden');
                    }
                });
            }

            function populateLantais(selectedGedungId, preselectLantaiId = null) {
                lantaiSelect.innerHTML = '<option value="">-- Pilih Lantai --</option>';
                ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan / Area --</option>';

                if (!selectedGedungId) {
                    lantaiSelect.disabled = true;
                    ruanganSelect.disabled = true;
                    return;
                }

                const gedung = lokasiTree.find(g => String(g.id) === String(selectedGedungId));
                if (!gedung || !gedung.lantais || gedung.lantais.length === 0) {
                    lantaiSelect.innerHTML = '<option value="">-- Tidak ada lantai terdaftar --</option>';
                    lantaiSelect.disabled = true;
                    ruanganSelect.disabled = true;
                    return;
                }

                lantaiSelect.disabled = false;
                gedung.lantais.forEach(l => {
                    const opt = document.createElement('option');
                    opt.value = l.id;
                    opt.textContent = `${l.nama} (Lt. ${l.nomor_lantai})`;
                    if (preselectLantaiId && String(l.id) === String(preselectLantaiId)) {
                        opt.selected = true;
                    }
                    lantaiSelect.appendChild(opt);
                });

                if (preselectLantaiId) {
                    populateRuangans(preselectLantaiId, defaultRuanganId);
                }
            }

            function populateRuangans(selectedLantaiId, preselectRuanganId = null) {
                ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan / Area (Opsional) --</option>';

                if (!selectedLantaiId) {
                    ruanganSelect.disabled = true;
                    return;
                }

                const currentGedungId = gedungSelect.value;
                const gedung = lokasiTree.find(g => String(g.id) === String(currentGedungId));
                if (!gedung) return;

                ruanganSelect.disabled = false;
                const lantai = gedung.lantais.find(l => String(l.id) === String(selectedLantaiId));
                if (!lantai || !lantai.ruangans || lantai.ruangans.length === 0) {
                    ruanganSelect.innerHTML = '<option value="">-- Koridor / Area Terbuka (Tanpa Ruangan Tertutup) --</option>';
                    return;
                }

                ruanganSelect.disabled = false;
                lantai.ruangans.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r.id;
                    opt.textContent = r.kode ? `${r.nama} (${r.kode})` : r.nama;
                    if (preselectRuanganId && String(r.id) === String(preselectRuanganId)) {
                        opt.selected = true;
                    }
                    ruanganSelect.appendChild(opt);
                });
            }

            gedungSelect.addEventListener('change', function () {
                populateLantais(this.value);
            });

            lantaiSelect.addEventListener('change', function () {
                populateRuangans(this.value);
            });

            // Inisialisasi awal dengan lokasi lama / old input
            if (defaultGedungId) {
                populateLantais(defaultGedungId, defaultLantaiId);
            }
        });
    </script>
</x-layouts.app>
