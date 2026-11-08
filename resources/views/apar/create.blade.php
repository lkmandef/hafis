<x-layouts.app title="HAFIS : Tambah APAR">
    <!-- Page Header -->
    <x-page-header
        title="Tambah Master Data APAR"
        description="Formulir pendaftaran unit tabung APAR baru dengan verifikasi penempatan berjenjang.">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ route('apar.index') }}">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Daftar</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Notifikasi Error Validasi Global -->
    @if ($errors->any())
        <div class="max-w-4xl mx-auto mb-6 p-4 rounded-xl border border-red-200 bg-red-50 text-red-900 text-xs">
            <div class="flex items-center gap-2 font-semibold mb-1.5 text-red-800">
                <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                </svg>
                <span>Terdapat kesalahan pada isian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container Tunggal Bergaya Cal.com -->
    <div class="max-w-4xl mx-auto rounded-xl border border-gray-200 bg-white p-5 sm:p-8 mb-8">
        <form action="{{ route('apar.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Petunjuk Pengisian Singkat -->
            <div class="p-3.5 rounded-lg bg-gray-50 border border-gray-200 text-xs text-gray-600 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <span>Kolom dengan tanda bintang (<span class="text-red-500 font-bold">*</span>) wajib diisi untuk integritas data K3RS.</span>
                <span class="text-[11px] text-gray-400 font-mono">ID Token QR otomatis dibuat</span>
            </div>

            <!-- Kelompok 1: Identitas & Status APAR -->
            <div>
                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-700 inline-flex items-center justify-center text-xs font-semibold">1</span>
                    <span>Identitas Unit & Kondisi</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Kode APAR -->
                    <div>
                        <x-input
                            name="kode"
                            label="Kode APAR"
                            placeholder="Contoh: APAR-EMR1-01"
                            value="{{ old('kode') }}"
                            :required="true"
                            :error="$errors->first('kode')"
                            hint="Kode unik pengenal tabung fisik di RS" />
                    </div>

                    <!-- Nomor Register / Aset RS -->
                    <div>
                        <x-input
                            name="nomor_register"
                            label="Nomor Register / Aset RS"
                            placeholder="Contoh: REG-K3-2026-004"
                            value="{{ old('nomor_register') }}"
                            :error="$errors->first('nomor_register')"
                            hint="Nomor register aset rumah sakit jika tersedia" />
                    </div>

                    <!-- Status Kondisi -->
                    <div>
                        <x-select
                            name="status"
                            label="Status Kondisi"
                            :required="true"
                            :error="$errors->first('status')"
                            hint="Status kesiapan operasional unit">
                            @foreach($statusLabels as $sKey => $sLabel)
                                <option value="{{ $sKey }}" {{ old('status', 'baik') === $sKey ? 'selected' : '' }}>
                                    {{ $sLabel }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                </div>
            </div>

            <!-- Kelompok 2: Lokasi Penempatan Fisik (Hierarki Berjenjang) -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-700 inline-flex items-center justify-center text-xs font-semibold">2</span>
                    <span>Lokasi Penempatan (Gedung &rarr; Lantai &rarr; Ruangan/Area)</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Gedung -->
                    <div>
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
                                    <option value="{{ $g->id }}" {{ old('gedung_id') == $g->id ? 'selected' : '' }}>
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

                    <!-- Lantai (Cascaded) -->
                    <div>
                        <label for="lantai_id" class="block text-xs font-medium text-gray-700 mb-1.5">
                            Lantai <span class="text-red-500 font-bold">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="lantai_id" 
                                name="lantai_id" 
                                required
                                disabled
                                class="appearance-none w-full rounded-lg border {{ $errors->has('lantai_id') ? 'border-red-300 bg-red-50/20 text-red-900' : 'border-gray-200 bg-white text-gray-900' }} pl-3 pr-8 py-2 text-xs focus:bg-white focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 min-h-[40px] cursor-pointer disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                                <option value="">-- Pilih Gedung Dulu --</option>
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

                    <!-- Ruangan / Area (Cascaded) -->
                    <div>
                        <label for="ruangan_id" class="block text-xs font-medium text-gray-700 mb-1.5">
                            Ruangan / Area <span class="text-gray-400 font-normal">(Opsional untuk ruang tertutup)</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="ruangan_id" 
                                name="ruangan_id" 
                                disabled
                                class="appearance-none w-full rounded-lg border {{ $errors->has('ruangan_id') ? 'border-red-300 bg-red-50/20 text-red-900' : 'border-gray-200 bg-white text-gray-900' }} pl-3 pr-8 py-2 text-xs focus:bg-white focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 min-h-[40px] cursor-pointer disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
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

                <!-- Informasi Ruangan Kosong jika lantai belum punya ruangan -->
                <div id="no-ruangan-alert" class="hidden mt-3 p-3 rounded-lg border border-amber-200 bg-amber-50 text-amber-900 text-xs">
                    Lantai yang dipilih belum memiliki daftar ruangan/area terdaftar. Silakan tambahkan ruangan di menu <a href="{{ route('lokasi.index') }}" target="_blank" class="font-semibold underline text-amber-950">Struktur Lokasi</a> terlebih dahulu agar dapat menempatkan APAR.
                </div>

                <!-- Detail Posisi Pemasangan -->
                <div class="mt-4">
                    <x-input
                        name="posisi_detail"
                        label="Detail Posisi Penempatan"
                        placeholder="Contoh: Samping pintu darurat triase, ketinggian bracket 120 cm dari lantai"
                        value="{{ old('posisi_detail') }}"
                        :error="$errors->first('posisi_detail')"
                        hint="Deskripsi fisik titik penempatan tabung di lokasi" />
                </div>
            </div>

            <!-- Kelompok 3: Spesifikasi Teknis APAR -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-700 inline-flex items-center justify-center text-xs font-semibold">3</span>
                    <span>Spesifikasi Teknis Tabung</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Jenis Media -->
                    <div>
                        <x-select
                            name="jenis_media"
                            label="Jenis Media Pemadam"
                            :required="true"
                            :error="$errors->first('jenis_media')"
                            placeholder="-- Pilih Media --">
                            @foreach($mediaOptions as $media)
                                <option value="{{ $media }}" {{ old('jenis_media') === $media ? 'selected' : '' }}>
                                    {{ $media }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <!-- Kapasitas Tabung -->
                    <div>
                        <x-input
                            name="kapasitas"
                            label="Kapasitas Tabung"
                            placeholder="Contoh: 6 kg / 2 kg / 9 kg"
                            value="{{ old('kapasitas', '6 kg') }}"
                            :required="true"
                            :error="$errors->first('kapasitas')"
                            hint="Format konsisten, misal: 6 kg" />
                    </div>

                    <!-- Merek -->
                    <div>
                        <x-input
                            name="merek"
                            label="Merek Tabung (Opsional)"
                            placeholder="Contoh: Yamato / Servvo / Chubb"
                            value="{{ old('merek') }}"
                            :error="$errors->first('merek')" />
                    </div>

                    <!-- Tipe / Model -->
                    <div>
                        <x-input
                            name="tipe_model"
                            label="Tipe / Model (Opsional)"
                            placeholder="Contoh: Portable / Trolley"
                            value="{{ old('tipe_model') }}"
                            :error="$errors->first('tipe_model')" />
                    </div>
                </div>
            </div>

            <!-- Kelompok 4: Informasi Waktu & Tanggal -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-700 inline-flex items-center justify-center text-xs font-semibold">4</span>
                    <span>Informasi Tanggal (Boleh Dikosongkan Jika Belum Diketahui)</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Tanggal Pemasangan -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_pemasangan"
                            label="Tanggal Pemasangan"
                            value="{{ old('tanggal_pemasangan') }}"
                            :error="$errors->first('tanggal_pemasangan')"
                            hint="Kosongkan jika belum tersedia data asli" />
                    </div>

                    <!-- Tanggal Kedaluwarsa Media -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_kadaluarsa"
                            label="Tanggal Kedaluwarsa Media"
                            value="{{ old('tanggal_kadaluarsa') }}"
                            :error="$errors->first('tanggal_kadaluarsa')"
                            hint="Jadwal isi ulang / expired media" />
                    </div>

                    <!-- Tanggal Pemeriksaan Terakhir -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_pemeriksaan_terakhir"
                            label="Pemeriksaan Terakhir"
                            value="{{ old('tanggal_pemeriksaan_terakhir') }}"
                            :error="$errors->first('tanggal_pemeriksaan_terakhir')"
                            hint="Kosongkan jika belum pernah diperiksa" />
                    </div>
                </div>
            </div>

            <!-- Kelompok 5: Keterangan / Catatan Tambahan -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-700 inline-flex items-center justify-center text-xs font-semibold">5</span>
                    <span>Catatan Tambahan (Opsional)</span>
                </h3>

                <div>
                    <x-textarea
                        name="keterangan"
                        label="Catatan Lapangan K3RS"
                        placeholder="Informasi kondisi fisik khusus, petunjuk aksesibilitas tabung, atau riwayat pengadaan..."
                        rows="3"
                        :error="$errors->first('keterangan')"
                        hint="Informasi penunjang untuk petugas pemeriksa saat bertugas di lapangan." />
                </div>
            </div>

            <!-- Tombol Aksi di Bagian Bawah Form -->
            <div class="pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                <x-button variant="secondary" href="{{ route('apar.index') }}" class="w-full sm:w-auto">
                    Batal
                </x-button>

                <x-button variant="primary" type="submit" class="w-full sm:w-auto">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Data APAR</span>
                </x-button>
            </div>
        </form>
    </div>

    <!-- Script Cascading Location Selection -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lokasiTree = @json($gedungs);
            const oldGedungId = "{{ old('gedung_id') }}";
            const oldLantaiId = "{{ old('lantai_id') }}";
            const oldRuanganId = "{{ old('ruangan_id') }}";

            const gedungSelect = document.getElementById('gedung_id');
            const lantaiSelect = document.getElementById('lantai_id');
            const ruanganSelect = document.getElementById('ruangan_id');
            const alertNoRuangan = document.getElementById('no-ruangan-alert');

            function populateLantais(selectedGedungId, preselectLantaiId = null) {
                lantaiSelect.innerHTML = '<option value="">-- Pilih Lantai --</option>';
                ruanganSelect.innerHTML = '<option value="">-- Pilih Lantai Dulu --</option>';
                ruanganSelect.disabled = true;
                alertNoRuangan.classList.add('hidden');

                if (!selectedGedungId) {
                    lantaiSelect.disabled = true;
                    return;
                }

                const gedung = lokasiTree.find(g => String(g.id) === String(selectedGedungId));
                if (!gedung || !gedung.lantais || gedung.lantais.length === 0) {
                    lantaiSelect.innerHTML = '<option value="">-- Tidak ada lantai terdaftar --</option>';
                    lantaiSelect.disabled = true;
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
                    populateRuangans(preselectLantaiId, oldRuanganId);
                }
            }

            function populateRuangans(selectedLantaiId, preselectRuanganId = null) {
                ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan / Area (Opsional) --</option>';
                alertNoRuangan.classList.add('hidden');

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

            // Inisialisasi jika ada old values dari validasi error
            if (oldGedungId) {
                populateLantais(oldGedungId, oldLantaiId);
            }
        });
    </script>
</x-layouts.app>