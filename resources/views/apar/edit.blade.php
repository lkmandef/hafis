<x-layouts.app title="HAFIS — Edit APAR">
    <!-- Page Header -->
    <x-page-header
        title="Edit Data APAR"
        description="Perbarui informasi teknis dan posisi penempatan unit APAR.">
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

            <!-- Unit Information Banner -->
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center justify-between">
                <div>
                    Sedang mengubah data unit: <span class="font-mono font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">APAR-IGD-01</span>
                </div>
                <span class="text-[11px] text-slate-400">Terdaftar sejak 12 Jan 2024</span>
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
                            value="APAR-IGD-01"
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
                            <option value="powder" selected>Dry Chemical Powder</option>
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
                            value="6 Kg"
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
                            value="Lantai 1, samping pintu darurat triase (Bracket 120 cm)"
                            :required="true"
                            hint="Posisi spesifik pemasangan bracket" />
                    </div>
                </div>
            </div>

            <!-- Kelompok 3: Masa Berlaku & Kondisi Fisik -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">
                    3. Masa Berlaku & Kondisi Fisik
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- 6. Tanggal Pengadaan -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_pengadaan"
                            label="Tanggal Pengadaan"
                            value="2024-01-12"
                            :required="true" />
                    </div>

                    <!-- 7. Tanggal Kadaluarsa -->
                    <div>
                        <x-input
                            type="date"
                            name="tanggal_kadaluarsa"
                            label="Tanggal Kadaluarsa / Uji Ulang"
                            value="2026-01-12"
                            :required="true" />
                    </div>

                    <!-- 8. Kondisi -->
                    <div>
                        <x-select
                            name="kondisi"
                            label="Kondisi Fisik Saat Ini"
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
                        label="Catatan Pengelolaan Unit"
                        placeholder="Catatan tambahan mengenai kondisi fisik, riwayat pemindahan, atau kebutuhan perlengkapan APAR..."
                        rows="3"
                        hint="Informasi penunjang untuk pemeriksaan berkala K3RS.">Tabung terpasang pada bracket dinding standar dengan tinggi 120 cm dari lantai. Stiker instruksi penggunaan jelas dan mudah diakses dari zona triase IGD.</x-textarea>
                </div>
            </div>

            <!-- Tombol Aksi di Bagian Bawah Form (Hierarchy Sesuai Aturan Sistem Button) -->
            <div class="pt-5 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <!-- Destructive Action (Danger) -->
                <div>
                    <button
                        type="button"
                        onclick="if(confirm('Apakah Anda yakin ingin menghapus unit APAR ini? Tindakan ini tidak dapat dibatalkan.')) window.location.href='{{ url('/apar') }}';"
                        class="px-3 py-2 text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-rose-600 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Hapus Unit</span>
                    </button>
                </div>

                <!-- Primary & Secondary Actions -->
                <div class="flex items-center gap-2.5">
                    <x-button variant="secondary" href="{{ url('/apar') }}">
                        Batal
                    </x-button>

                    <x-button variant="primary" type="submit">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Simpan Perubahan</span>
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>
</x-layouts.app>