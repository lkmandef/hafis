<x-layouts.app title="HAFIS — Tambah APAR">
    <!-- Page Header -->
    <x-page-header
        title="Tambah APAR"
        description="Formulir pendaftaran unit APAR baru ke sistem rumah sakit.">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ url('/apar') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Daftar</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Form Container Tunggal (Flat UI, Hairline Border) -->
    <x-card class="max-w-4xl mx-auto">
        <form action="#" method="POST" onsubmit="event.preventDefault();" class="space-y-6">

            <!-- Petunjuk Pengisian Singkat -->
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center justify-between">
                <span>Tanda bintang (<span class="text-rose-500 font-bold">*</span>) wajib diisi sebelum menyimpan data unit.</span>
                <span class="text-[11px] text-slate-400">Pratinjau Formulir</span>
            </div>

            <!-- Kelompok 1: Identifikasi & Spesifikasi Unit -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">
                    1. Identifikasi & Spesifikasi Unit
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- 1. Kode APAR -->
                    <div>
                        <x-input
                            name="kode_apar"
                            label="Kode APAR"
                            placeholder="APAR-001"
                            value="APAR-001"
                            :required="true"
                            hint="Kode unik pengenal tabung" />
                    </div>

                    <!-- 2. Jenis APAR -->
                    <div>
                        <x-select
                            name="jenis_apar"
                            label="Jenis APAR"
                            :required="true"
                            placeholder="-- Pilih Jenis --">
                            <option value="powder" selected>Powder</option>
                            <option value="co2">CO₂</option>
                            <option value="foam">Foam</option>
                            <option value="wet_chemical">Wet Chemical</option>
                        </x-select>
                    </div>

                    <!-- 3. Kapasitas -->
                    <div>
                        <x-input
                            name="kapasitas"
                            label="Kapasitas"
                            placeholder="Contoh: 6 kg"
                            value="6 kg"
                            :required="true" />
                    </div>
                </div>
            </div>

            <!-- Kelompok 2: Lokasi Penempatan -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">
                    2. Lokasi Penempatan
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- 4. Lokasi / Zona Gedung -->
                    <div>
                        <x-select
                            name="lokasi"
                            label="Zona Gedung"
                            :required="true"
                            placeholder="-- Pilih Zona Gedung --">
                            <option value="igd" selected>Gedung Utama (IGD)</option>
                            <option value="rawat_inap">Gedung Rawat Inap Melati</option>
                            <option value="bedah">Instalasi Bedah Sentral (OK)</option>
                            <option value="lab">Laboratorium Patologi</option>
                            <option value="farmasi">Instalasi Farmasi</option>
                            <option value="gizi">Instalasi Gizi & Dapur</option>
                            <option value="penunjang">Gedung Penunjang / Genset</option>
                        </x-select>
                    </div>

                    <!-- 5. Detail Lokasi -->
                    <div>
                        <x-input
                            name="detail_lokasi"
                            label="Detail Lokasi"
                            placeholder="Contoh: Lantai 2, depan ruang ICU"
                            value="Lantai 1, samping pintu darurat triase"
                            :required="true"
                            hint="Posisi spesifik pemasangan bracket" />
                    </div>
                </div>
            </div>

            <!-- Kelompok 3: Masa Berlaku & Kondisi Fisik -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">
                    3. Masa Berlaku & Kondisi Awal
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- 6. Tanggal Pengadaan -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_pengadaan"
                            label="Tanggal Pengadaan"
                            value="{{ date('Y-m-d') }}"
                            :required="true" />
                    </div>

                    <!-- 7. Tanggal Kadaluarsa -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_kadaluarsa"
                            label="Tanggal Kadaluarsa"
                            value="{{ date('Y-m-d', strtotime('+2 years')) }}"
                            :required="true" />
                    </div>

                    <!-- 8. Kondisi -->
                    <div>
                        <x-select
                            name="kondisi"
                            label="Kondisi Fisik"
                            :required="true"
                            placeholder="-- Pilih Kondisi --">
                            <option value="baik" selected>Baik</option>
                            <option value="perhatian">Perlu Perhatian</option>
                            <option value="rusak">Rusak / Perlu Tindakan</option>
                        </x-select>
                    </div>
                </div>
            </div>

            <!-- Kelompok 4: Keterangan Tambahan -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">
                    4. Keterangan Tambahan
                </h3>

                <!-- 9. Keterangan -->
                <div>
                    <x-textarea
                        name="keterangan"
                        label="Catatan Lapangan (Opsional)"
                        placeholder="Catatan tambahan mengenai kondisi fisik, riwayat pemindahan, atau kebutuhan perlengkapan APAR..."
                        rows="3"
                        hint="Informasi penunjang untuk pemeriksaan berkala K3RS." />
                </div>
            </div>

            <!-- Contoh Pratinjau State Validasi UI (Required, Invalid Input, Error Message) -->
            <div class="pt-2">
                <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/50">
                    <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Contoh Tampilan Validasi UI (State Error):
                    </span>
                    <div class="max-w-md">
                        <x-input
                            name="contoh_error"
                            label="Kode Verifikasi Uji"
                            value="APAR-INVALID#99"
                            :required="true"
                            error="Format kode mengandung karakter ilegal. Gunakan kombinasi alfanumerik standar (contoh: APAR-001)." />
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi di Bagian Bawah Form -->
            <div class="pt-5 border-t border-slate-200 flex flex-wrap items-center justify-end gap-3">
                <x-button variant="secondary" href="{{ url('/apar') }}">
                    Batal
                </x-button>

                <x-button variant="primary" type="submit">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan APAR</span>
                </x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>