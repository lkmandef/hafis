<x-layouts.app title="HAFIS : Edit APAR">
    <!-- Page Header -->
    <x-page-header
        title="Edit Master Data APAR"
        description="Perbarui informasi identitas, lokasi penempatan, atau spesifikasi teknis unit tabung.">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ route('apar.show', $apar->id) }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Batal / Lihat Detail</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Notifikasi Error Validasi Global jika ada -->
    @if ($errors->any())
        <div class="max-w-4xl mx-auto mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs">
            <div class="flex items-center gap-2 font-bold mb-1.5 text-rose-800">
                <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                </svg>
                <span>Terdapat kesalahan pada isian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-[11px] text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container Tunggal -->
    <x-card class="max-w-4xl mx-auto">
        <!-- Banner Info Unit -->
        <div class="p-3.5 mb-6 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="text-slate-500">Mengedit Unit:</span>
                <span class="font-mono font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">
                    {{ $apar->kode }}
                </span>
                <span class="text-slate-400 font-mono text-[11px]">QR: {{ $apar->qr_code }}</span>
            </div>
            <span class="text-[11px] text-slate-400">
                Terdaftar sejak: {{ $apar->created_at ? $apar->created_at->format('d/m/Y') : '-' }}
            </span>
        </div>

        <form action="{{ route('apar.update', $apar->id) }}" method="POST" class="space-y-7">
            @csrf
            @method('PUT')

            <!-- Kelompok 1: Identitas & Status APAR -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3.5 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] font-bold">1</span>
                    <span>Identitas Unit & Kondisi</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Kode APAR -->
                    <div>
                        <x-input
                            name="kode"
                            label="Kode APAR"
                            placeholder="Contoh: APAR-IGD-01"
                            value="{{ old('kode', $apar->kode) }}"
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
                            value="{{ old('nomor_register', $apar->nomor_register) }}"
                            :error="$errors->first('nomor_register')"
                            hint="Nomor register internal jika tersedia" />
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
                                <option value="{{ $sKey }}" {{ old('status', $apar->status) === $sKey ? 'selected' : '' }}>
                                    {{ $sLabel }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                </div>
            </div>

            <!-- Kelompok 2: Lokasi Penempatan Fisik (Hierarki Berjenjang) -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3.5 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] font-bold">2</span>
                    <span>Lokasi Penempatan (Gedung &rarr; Lantai &rarr; Ruangan/Area)</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Gedung -->
                    <div>
                        <label for="gedung_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Gedung <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="gedung_id" 
                                name="gedung_id" 
                                required
                                class="appearance-none w-full rounded-lg border {{ $errors->has('gedung_id') ? 'border-rose-400 bg-rose-50/20 text-rose-900' : 'border-slate-200 bg-slate-50 text-slate-800' }} pl-3 pr-8 py-2 text-xs focus:bg-white focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 min-h-[44px] cursor-pointer">
                                <option value="">-- Pilih Gedung --</option>
                                @php
                                    $currentGedungId = old('gedung_id', $apar->ruangan?->lantai?->gedung_id);
                                @endphp
                                @foreach($gedungs as $g)
                                    <option value="{{ $g->id }}" {{ $currentGedungId == $g->id ? 'selected' : '' }}>
                                        {{ $g->nama }} ({{ $g->kode }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </div>
                        @if($errors->has('gedung_id'))
                            <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $errors->first('gedung_id') }}</p>
                        @endif
                    </div>

                    <!-- Lantai (Cascaded) -->
                    <div>
                        <label for="lantai_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Lantai <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="lantai_id" 
                                name="lantai_id" 
                                required
                                class="appearance-none w-full rounded-lg border {{ $errors->has('lantai_id') ? 'border-rose-400 bg-rose-50/20 text-rose-900' : 'border-slate-200 bg-slate-50 text-slate-800' }} pl-3 pr-8 py-2 text-xs focus:bg-white focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 min-h-[44px] cursor-pointer">
                                <option value="">-- Pilih Lantai --</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </div>
                        @if($errors->has('lantai_id'))
                            <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $errors->first('lantai_id') }}</p>
                        @endif
                    </div>

                    <!-- Ruangan / Area (Cascaded) -->
                    <div>
                        <label for="ruangan_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Ruangan / Area <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="ruangan_id" 
                                name="ruangan_id" 
                                required
                                class="appearance-none w-full rounded-lg border {{ $errors->has('ruangan_id') ? 'border-rose-400 bg-rose-50/20 text-rose-900' : 'border-slate-200 bg-slate-50 text-slate-800' }} pl-3 pr-8 py-2 text-xs focus:bg-white focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 min-h-[44px] cursor-pointer">
                                <option value="">-- Pilih Ruangan --</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </div>
                        @if($errors->has('ruangan_id'))
                            <p class="text-[11px] text-rose-600 font-medium mt-1">{{ $errors->first('ruangan_id') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Informasi Ruangan Kosong jika lantai belum punya ruangan -->
                <div id="no-ruangan-alert" class="hidden mt-3 p-3 rounded-lg border border-amber-200 bg-amber-50 text-amber-900 text-xs">
                    Lantai yang dipilih belum memiliki daftar ruangan/area terdaftar. Silakan tambahkan ruangan di menu <a href="{{ route('lokasi.index') }}" target="_blank" class="font-bold underline text-amber-950">Struktur Lokasi</a> terlebih dahulu.
                </div>

                <!-- Detail Posisi Pemasangan -->
                <div class="mt-4">
                    <x-input
                        name="posisi_detail"
                        label="Detail Posisi Penempatan"
                        placeholder="Contoh: Samping pintu darurat triase, ketinggian bracket 120 cm dari lantai"
                        value="{{ old('posisi_detail', $apar->posisi_detail) }}"
                        :error="$errors->first('posisi_detail')"
                        hint="Deskripsi fisik titik penempatan tabung di lokasi" />
                </div>
            </div>

            <!-- Kelompok 3: Spesifikasi Teknis APAR -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3.5 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] font-bold">3</span>
                    <span>Spesifikasi Teknis Tabung</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Jenis Media -->
                    <div>
                        <x-select
                            name="jenis_media"
                            label="Jenis Media Pemadam"
                            :required="true"
                            :error="$errors->first('jenis_media')">
                            @foreach($mediaOptions as $media)
                                <option value="{{ $media }}" {{ old('jenis_media', $apar->jenis_media) === $media ? 'selected' : '' }}>
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
                            value="{{ old('kapasitas', $apar->kapasitas) }}"
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
                            value="{{ old('merek', $apar->merek) }}"
                            :error="$errors->first('merek')" />
                    </div>

                    <!-- Tipe / Model -->
                    <div>
                        <x-input
                            name="tipe_model"
                            label="Tipe / Model (Opsional)"
                            placeholder="Contoh: Portable / Trolley"
                            value="{{ old('tipe_model', $apar->tipe_model) }}"
                            :error="$errors->first('tipe_model')" />
                    </div>
                </div>
            </div>

            <!-- Kelompok 4: Informasi Waktu & Tanggal -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3.5 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] font-bold">4</span>
                    <span>Informasi Tanggal</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Tanggal Pemasangan -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_pemasangan"
                            label="Tanggal Pemasangan"
                            value="{{ old('tanggal_pemasangan', $apar->tanggal_pemasangan?->format('Y-m-d')) }}"
                            :error="$errors->first('tanggal_pemasangan')"
                            hint="Boleh kosong jika belum diketahui" />
                    </div>

                    <!-- Tanggal Kedaluwarsa Media -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_kadaluarsa"
                            label="Tanggal Kedaluwarsa Media"
                            value="{{ old('tanggal_kadaluarsa', $apar->tanggal_kadaluarsa?->format('Y-m-d')) }}"
                            :error="$errors->first('tanggal_kadaluarsa')"
                            hint="Jadwal isi ulang / expired media" />
                    </div>

                    <!-- Tanggal Pemeriksaan Terakhir -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_pemeriksaan_terakhir"
                            label="Pemeriksaan Terakhir"
                            value="{{ old('tanggal_pemeriksaan_terakhir', $apar->tanggal_pemeriksaan_terakhir?->format('Y-m-d')) }}"
                            :error="$errors->first('tanggal_pemeriksaan_terakhir')"
                            hint="Boleh kosong jika belum ada data" />
                    </div>
                </div>
            </div>

            <!-- Kelompok 5: Keterangan / Catatan Tambahan -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3.5 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] font-bold">5</span>
                    <span>Catatan Tambahan (Opsional)</span>
                </h3>

                <div>
                    <x-textarea
                        name="keterangan"
                        label="Catatan Lapangan K3RS"
                        placeholder="Informasi kondisi fisik khusus, petunjuk aksesibilitas tabung, atau riwayat pengadaan..."
                        rows="3"
                        :error="$errors->first('keterangan')"
                        hint="Informasi penunjang untuk petugas pemeriksa saat bertugas di lapangan.">
                        {{ old('keterangan', $apar->keterangan) }}
                    </x-textarea>
                </div>
            </div>

            <!-- Tombol Aksi di Bagian Bawah Form -->
            <div class="pt-5 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Tombol Hapus Soft Delete (Danger) -->
                <div>
                    <button 
                        type="button"
                        onclick="if(confirm('Apakah Anda yakin ingin menghapus unit APAR {{ $apar->kode }}? Data riwayat unit tetap disimpan di arsip sistem.')) document.getElementById('delete-apar-form').submit();"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-rose-700 hover:text-rose-900 hover:bg-rose-50 text-xs font-semibold transition border border-rose-200 min-h-[44px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Hapus Unit APAR</span>
                    </button>
                </div>

                <!-- Primary & Secondary Actions -->
                <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-2.5">
                    <x-button variant="secondary" href="{{ route('apar.show', $apar->id) }}" class="w-full sm:w-auto">
                        Batal
                    </x-button>

                    <x-button variant="primary" type="submit" class="w-full sm:w-auto">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Simpan Perubahan</span>
                    </x-button>
                </div>
            </div>
        </form>

        <!-- Form Terpisah untuk Hapus Unit (DELETE) -->
        <form id="delete-apar-form" action="{{ route('apar.destroy', $apar->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </x-card>

    <!-- Script Cascading Location Selection -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lokasiTree = @json($gedungs);
            const initialGedungId = "{{ old('gedung_id', $apar->ruangan?->lantai?->gedung_id) }}";
            const initialLantaiId = "{{ old('lantai_id', $apar->ruangan?->lantai_id) }}";
            const initialRuanganId = "{{ old('ruangan_id', $apar->ruangan_id) }}";

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
                    populateRuangans(preselectLantaiId, initialRuanganId);
                }
            }

            function populateRuangans(selectedLantaiId, preselectRuanganId = null) {
                ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan / Area --</option>';
                alertNoRuangan.classList.add('hidden');

                if (!selectedLantaiId) {
                    ruanganSelect.disabled = true;
                    return;
                }

                const currentGedungId = gedungSelect.value;
                const gedung = lokasiTree.find(g => String(g.id) === String(currentGedungId));
                if (!gedung) return;

                const lantai = gedung.lantais.find(l => String(l.id) === String(selectedLantaiId));
                if (!lantai || !lantai.ruangans || lantai.ruangans.length === 0) {
                    ruanganSelect.innerHTML = '<option value="">-- Belum ada ruangan di lantai ini --</option>';
                    ruanganSelect.disabled = true;
                    alertNoRuangan.classList.remove('hidden');
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

            // Inisialisasi awal dengan data eksisting APAR
            if (initialGedungId) {
                populateLantais(initialGedungId, initialLantaiId);
            }
        });
    </script>
</x-layouts.app>