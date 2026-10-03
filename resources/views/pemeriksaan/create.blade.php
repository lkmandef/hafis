<x-layouts.app title="HAFIS — Pemeriksaan APAR">
    <!-- 1. Page Header -->
    <x-page-header
        title="Pemeriksaan APAR"
        description="Pemeriksaan kondisi APAR secara berkala.">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ url('/jadwal') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Jadwal</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- 2. Informasi APAR (Compact Single Container) -->
        <x-card class="!p-4 sm:!p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit Diperiksa:</span>
                    <span class="font-mono text-sm font-bold text-slate-900 bg-slate-100 px-2.5 py-0.5 rounded border border-slate-200 tabular-nums">
                        APAR-IGD-01
                    </span>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <span class="text-slate-500">Pilih unit lain:</span>
                    <select class="bg-slate-50 border border-slate-200 rounded text-xs px-2 py-1 text-slate-700 focus:bg-white focus:outline-none focus:border-slate-400">
                        <option value="APAR-IGD-01" selected>APAR-IGD-01 (IGD Triase)</option>
                        <option value="APAR-RWI-04">APAR-RWI-04 (Rawat Inap Melati)</option>
                        <option value="APAR-OK-01">APAR-OK-01 (Bedah Sentral)</option>
                        <option value="APAR-LAB-02">APAR-LAB-02 (Laboratorium Kimia)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="block text-slate-400 text-[11px] font-medium">Jenis Media</span>
                    <span class="font-semibold text-slate-800 text-xs">Dry Chemical Powder</span>
                </div>
                <div>
                    <span class="block text-slate-400 text-[11px] font-medium">Kapasitas Tabung</span>
                    <span class="font-semibold text-slate-800 text-xs">6 Kg</span>
                </div>
                <div>
                    <span class="block text-slate-400 text-[11px] font-medium">Lokasi Terpasang</span>
                    <span class="font-semibold text-slate-800 text-xs">IGD Zona Triase, Lt. 1</span>
                </div>
                <div>
                    <span class="block text-slate-400 text-[11px] font-medium">Pemeriksaan Terakhir</span>
                    <span class="font-mono font-semibold text-slate-800 text-xs tabular-nums">02/09/2026</span>
                </div>
            </div>
        </x-card>

        <!-- Form Pemeriksaan Lapangan -->
        <form action="#" method="POST" onsubmit="event.preventDefault();" class="space-y-6">

            <!-- 3. Checklist Pemeriksaan (10 Item Realistis) -->
            <x-card
                title="Checklist Pemeriksaan Fisik"
                subtitle="Verifikasi langsung 10 parameter kondisi unit di lapangan.">

                <div class="divide-y divide-slate-100 border border-slate-200 rounded-lg overflow-hidden bg-white">
                    @php
                    $checklistItems = [
                    1 => 'Posisi APAR sesuai lokasi',
                    2 => 'Akses menuju APAR tidak terhalang',
                    3 => 'Kondisi tabung baik (bebas korosi & penyok)',
                    4 => 'Segel pengaman masih terpasang',
                    5 => 'Pin pengaman tersedia dan terpasang',
                    6 => 'Pressure gauge dalam kondisi normal (zona hijau)',
                    7 => 'Selang / nozzle dalam kondisi baik (tanpa retak)',
                    8 => 'Label instruksi penggunaan masih terbaca jelas',
                    9 => 'Tidak terdapat kebocoran media',
                    10 => 'Masa berlaku tabung masih sesuai (belum kadaluarsa)',
                    ];
                    @endphp

                    @foreach ($checklistItems as $index => $item)
                    <div class="p-3 sm:p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 hover:bg-slate-50/60 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <span class="font-mono text-xs text-slate-400 w-5 shrink-0">{{ sprintf('%02d', $index) }}.</span>
                            <span class="text-xs font-medium text-slate-800">{{ $item }}</span>
                        </div>

                        <div class="flex items-center gap-2 pl-7 sm:pl-0 shrink-0">
                            <label class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-slate-200 text-xs font-semibold hover:bg-slate-50 transition-colors has-checked:bg-emerald-50 has-checked:border-emerald-300 has-checked:text-emerald-800">
                                <input type="radio" name="check_{{ $index }}" value="baik" checked class="accent-emerald-600">
                                <span>Baik</span>
                            </label>

                            <label class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-slate-200 text-xs font-semibold hover:bg-slate-50 transition-colors has-checked:bg-rose-50 has-checked:border-rose-300 has-checked:text-rose-800">
                                <input type="radio" name="check_{{ $index }}" value="tidak_baik" class="accent-rose-600">
                                <span>Tidak Baik</span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>

            <!-- 4. Hasil Pemeriksaan & Kesimpulan -->
            <x-card
                title="Hasil & Kesimpulan Pemeriksaan"
                subtitle="Penetapan status kelaikan operasional unit APAR.">

                <div class="space-y-4">
                    <!-- Kesimpulan Kondisi -->
                    <div>
                        <x-select
                            name="kesimpulan_kondisi"
                            label="Kesimpulan Kondisi Akhir"
                            :required="true">
                            <option value="baik" selected>Baik (Siap Digunakan)</option>
                            <option value="perhatian">Perlu Perhatian (Terdapat Temuan Ringan)</option>
                            <option value="rusak">Rusak / Perlu Tindakan (Tidak Siap Pakai)</option>
                        </x-select>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Status otomatis disarankan berdasarkan hasil 10 item checklist fisik di atas.
                        </p>
                    </div>

                    <!-- Catatan Pemeriksaan -->
                    <div>
                        <x-textarea
                            name="catatan_pemeriksaan"
                            label="Catatan Temuan Lapangan (Opsional)"
                            placeholder="Tuliskan temuan fisik spesifik, contoh: segel kawat utuh, tekanan jarum di tengah zona hijau, stiker petunjuk sedikit berdebu..."
                            rows="3" />
                    </div>
                </div>
            </x-card>

            <!-- 5. Foto Pemeriksaan (Opsional) -->
            <x-card
                title="Foto Dokumentasi Pemeriksaan"
                subtitle="Dokumentasi visual tabung dan manometer di lapangan (opsional).">

                <div class="space-y-3">
                    <div class="border-2 border-dashed border-slate-200 rounded-lg p-5 text-center bg-slate-50/50">
                        <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>

                        <div class="text-xs text-slate-600 mb-2">
                            <button type="button" class="font-semibold text-slate-900 underline hover:text-slate-800">
                                Pilih file foto
                            </button>
                            <span class="text-slate-400">atau ambil gambar via kamera ponsel</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Format JPG atau PNG (Maks. 5 MB)</p>
                    </div>

                    <!-- Pratinjau Foto Sederhana -->
                    <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-200 bg-white text-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 font-mono text-[10px] shrink-0">
                                JPG
                            </div>
                            <div>
                                <span class="font-medium text-slate-800 block text-xs">foto_inspeksi_apar_igd_01.jpg</span>
                                <span class="text-[11px] text-slate-400">1.4 MB - Siap disimpan</span>
                            </div>
                        </div>

                        <button type="button" class="text-xs text-rose-600 hover:text-rose-800 font-medium px-2 py-1">
                            Hapus
                        </button>
                    </div>
                </div>
            </x-card>

            <!-- 6. Data Petugas & Tanggal Pemeriksaan -->
            <x-card
                title="Data Petugas Inspeksi"
                subtitle="Identitas pemeriksa dan waktu pelaksanaan inspeksi.">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input
                            name="nama_pemeriksa"
                            label="Nama Pemeriksa"
                            value="Budi Santoso (K3RS)"
                            :required="true"
                            hint="Petugas penanggung jawab checklist" />
                    </div>

                    <div>
                        <x-input
                            type="date"
                            name="tanggal_pemeriksaan"
                            label="Tanggal Pemeriksaan"
                            value="{{ date('Y-m-d') }}"
                            :required="true" />
                    </div>
                </div>
            </x-card>

            <!-- 7. Action Buttons di Bagian Bawah -->
            <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-end gap-3">
                <x-button variant="secondary" href="{{ url('/jadwal') }}">
                    Batal
                </x-button>

                <x-button variant="primary" type="submit">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Pemeriksaan</span>
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>