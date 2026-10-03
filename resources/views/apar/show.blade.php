<x-layouts.app title="HAFIS — Detail APAR">
    <!-- 1. Page Header with Breadcrumb & Kode Identitas -->
    <div class="pb-5 mb-6 border-b border-slate-200/80">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ url('/apar') }}" class="hover:text-slate-800 transition-colors">Data APAR</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Detail APAR</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Detail APAR:
                </h1>
                <span class="font-mono text-base sm:text-lg font-extrabold text-slate-900 bg-slate-100 border border-slate-200 px-3 py-1 rounded-lg tabular-nums">
                    APAR-IGD-01
                </span>
            </div>

            <!-- Header Actions: Edit & Kembali -->
            <div class="flex items-center gap-2">
                <x-button variant="secondary" href="{{ url('/penempatan?lantai=1&highlight=' . ($id ?? 'APAR-IGD-01')) }}">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Titik Denah</span>
                </x-button>

                <x-button variant="secondary" href="{{ url('/apar/edit') }}">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span>Edit Unit</span>
                </x-button>

                <x-button variant="secondary" href="{{ url('/apar') }}">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali</span>
                </x-button>
            </div>
        </div>
    </div>

    <!-- Grid Dua Kolom: Identitas Unit (Kiri) & Status Saat Ini + Tindakan (Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- 2. IDENTITAS APAR (2 Kolom Lebar) -->
        <div class="lg:col-span-2">
            <x-card title="Identitas Unit APAR" subtitle="Spesifikasi teknis dan lokasi penempatan fisik di rumah sakit.">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                    <!-- Kode APAR -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Kode APAR</dt>
                        <dd class="font-mono font-bold text-slate-900 text-sm tabular-nums">APAR-IGD-01</dd>
                    </div>

                    <!-- Jenis APAR -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Jenis APAR</dt>
                        <dd class="font-medium text-slate-900 text-sm">Dry Chemical Powder</dd>
                    </div>

                    <!-- Kapasitas -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Kapasitas Tabung</dt>
                        <dd class="font-medium text-slate-900 text-sm">6 Kg</dd>
                    </div>

                    <!-- Lokasi / Zona -->
                    <div class="pb-3 border-b border-slate-100">
                        <dt class="font-semibold text-slate-500 mb-1">Lokasi / Zona Gedung</dt>
                        <dd class="font-medium text-slate-900 text-sm">Gedung Utama (IGD)</dd>
                    </div>

                    <!-- Detail Lokasi -->
                    <div class="pb-3 border-b border-slate-100 sm:col-span-2">
                        <dt class="font-semibold text-slate-500 mb-1">Detail Posisi Penempatan</dt>
                        <dd class="font-medium text-slate-900 text-sm">Lantai 1, samping pintu darurat triase (Ketinggian bracket 120 cm)</dd>
                    </div>

                    <!-- Tanggal Pengadaan -->
                    <div class="pt-1">
                        <dt class="font-semibold text-slate-500 mb-1">Tanggal Pengadaan</dt>
                        <dd class="font-mono font-medium text-slate-900">12/01/2024</dd>
                    </div>

                    <!-- Tanggal Kadaluarsa -->
                    <div class="pt-1">
                        <dt class="font-semibold text-slate-500 mb-1">Tanggal Kadaluarsa / Uji Ulang</dt>
                        <dd class="font-mono font-medium text-slate-900">12/01/2026</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <!-- 3 & 5. STATUS SAAT INI & TINDAKAN (1 Kolom) -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            <!-- 3. Status Saat Ini -->
            <x-card title="Status Saat Ini" subtitle="Hasil pemantauan berkala terakhir unit.">
                <div class="space-y-4 text-xs">
                    <!-- Kondisi Badge -->
                    <div>
                        <span class="block font-semibold text-slate-500 mb-1.5">Kondisi Fisik</span>
                        <x-badge variant="success" :dot="true">
                            Baik
                        </x-badge>
                    </div>

                    <!-- Pemeriksaan Terakhir -->
                    <div class="pt-3 border-t border-slate-100">
                        <span class="block font-semibold text-slate-500 mb-1">Pemeriksaan Terakhir</span>
                        <span class="font-mono font-medium text-slate-900 block tabular-nums">02/10/2026</span>
                        <span class="text-[11px] text-slate-400">Oleh: Budi Santoso (K3RS)</span>
                    </div>

                    <!-- Pemeriksaan Berikutnya -->
                    <div class="pt-3 border-t border-slate-100">
                        <span class="block font-semibold text-slate-500 mb-1">Pemeriksaan Berikutnya</span>
                        <span class="font-mono font-medium text-slate-900 block tabular-nums">02/11/2026</span>
                        <span class="text-[11px] text-slate-400">Jadwal inspeksi bulanan</span>
                    </div>

                    <!-- Keterangan Terakhir -->
                    <div class="pt-3 border-t border-slate-100">
                        <span class="block font-semibold text-slate-500 mb-1">Catatan Fisik Terakhir</span>
                        <p class="text-slate-700 leading-relaxed">
                            Jarum manometer stabil di zona hijau, pin pengaman dan segel kawat utuh, selang elastis tanpa sumbatan.
                        </p>
                    </div>
                </div>
            </x-card>

            <!-- 5. TINDAKAN / REKOMENDASI (Non-alarmist) -->
            <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/40 text-xs">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <h4 class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">
                        Tindakan / Rekomendasi
                    </h4>
                </div>
                <p class="text-emerald-800 leading-relaxed">
                    Unit dalam kondisi prima dan siap digunakan. Tidak ada tindakan darurat yang diperlukan. Pastikan area penempatan tabung tetap bebas dari halangan barang operasional.
                </p>
            </div>
        </div>
    </div>

    <!-- 4. RIWAYAT PEMERIKSAAN SINGKAT -->
    <x-card title="Riwayat Pemeriksaan Terakhir" subtitle="Catatan hasil inspeksi fisik berkala unit ini sebelumnya.">
        <x-table :headers="['Tanggal', 'Pemeriksa', 'Kondisi', 'Catatan Pemeriksaan']">
            <!-- Baris 1: Paling Baru -->
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="px-4 py-3 font-mono text-slate-900 whitespace-nowrap tabular-nums">
                    02/10/2026
                </td>
                <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                    Budi Santoso
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <x-badge variant="success" :dot="true">Baik</x-badge>
                </td>
                <td class="px-4 py-3 text-slate-700">
                    Jarum tekanan hijau, pin dan segel utuh, selang elastis, kartu gantung terpasang.
                </td>
            </tr>

            <!-- Baris 2 -->
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="px-4 py-3 font-mono text-slate-900 whitespace-nowrap tabular-nums">
                    02/09/2026
                </td>
                <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                    Rudi Hartono
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <x-badge variant="success" :dot="true">Baik</x-badge>
                </td>
                <td class="px-4 py-3 text-slate-700">
                    Pemeriksaan bulanan normal, pembersihan debu pada tabung, penandaan kartu kontrol.
                </td>
            </tr>

            <!-- Baris 3 -->
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="px-4 py-3 font-mono text-slate-900 whitespace-nowrap tabular-nums">
                    02/08/2026
                </td>
                <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                    Budi Santoso
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <x-badge variant="success" :dot="true">Baik</x-badge>
                </td>
                <td class="px-4 py-3 text-slate-700">
                    Tekanan stabil, posisi bracket dinding kokoh, tidak ada tanda korosi.
                </td>
            </tr>
        </x-table>

        <!-- Footer Action: Lihat semua riwayat -->
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Menampilkan 3 pemeriksaan fisik terakhir</span>
            <a href="{{ url('/riwayat') }}" class="font-semibold text-slate-700 hover:text-slate-900 hover:underline">
                Lihat semua riwayat pemeriksaan &rarr;
            </a>
        </div>
    </x-card>
</x-layouts.app>