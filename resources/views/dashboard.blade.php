<x-layouts.app title="HAFIS — Dashboard">
    <!-- 1. Header Halaman & 8. Quick Actions -->
    <x-page-header
        title="Dashboard"
        description="Monitoring kondisi dan pemeriksaan APAR.">
        <x-slot:actions>
            <x-button variant="secondary" href="{{ url('/apar/create') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah APAR</span>
            </x-button>

            <x-button variant="secondary" href="{{ url('/pemeriksaan') }}">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>Pemeriksaan</span>
            </x-button>

            <x-button variant="primary" href="{{ url('/scan') }}">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span>Scan Cek</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- 2. Summary Cards (Hierarki: Total APAR Primer, 3 Lainnya Sekunder) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Primary Metric: Total APAR -->
        <x-stat-card
            title="Total APAR"
            value="142"
            :is-primary="true"
            context="Seluruh unit terdaftar di rumah sakit" />

        <!-- Secondary Metric: Kondisi Baik -->
        <x-stat-card
            title="Kondisi Baik"
            value="128"
            status="success"
            context="Siap pakai dan tekanan normal" />

        <!-- Secondary Metric: Perlu Perhatian -->
        <x-stat-card
            title="Perlu Perhatian"
            value="9"
            status="warning"
            context="Mendekati masa uji atau tekanan turun" />

        <!-- Secondary Metric: Rusak / Perlu Tindakan -->
        <x-stat-card
            title="Rusak / Perlu Tindakan"
            value="5"
            status="danger"
            context="Pin lepas, selang retak, atau kosong" />
    </div>

    <!-- 3. Ringkasan Pemeriksaan -->
    <x-card
        title="Ringkasan Pemeriksaan"
        subtitle="Periode berjalan: Oktober 2026"
        class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Pemeriksaan Bulan Ini -->
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Pemeriksaan Bulan Ini
                </span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900 tabular-nums">105</span>
                    <span class="text-xs text-slate-500 font-medium">/ 142 unit selesai</span>
                </div>
                <p class="text-[11px] text-slate-600 mt-2 leading-relaxed">
                    105 unit telah diverifikasi fisik oleh petugas K3RS pada periode berjalan.
                </p>
            </div>

            <!-- Pemeriksaan Belum Dilakukan -->
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Belum Dilakukan
                </span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-amber-700 tabular-nums">37</span>
                    <span class="text-xs text-slate-500 font-medium">unit menunggu inspeksi</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">
                    Prioritas: Area Gedung Rawat Inap Melati dan Selasar Barat.
                </p>
            </div>

            <!-- Pemeriksaan Mendatang -->
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Pemeriksaan Mendatang
                </span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900">Minggu ke-2</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">
                    Jadwal rutin K3RS: Gedung Bedah Sentral (OK) dan Farmasi.
                </p>
            </div>
        </div>
    </x-card>

    <!-- 4. Daftar Pemeriksaan Terbaru (Tabel) -->
    <x-card
        title="Daftar Pemeriksaan Terbaru"
        subtitle="Log pemeriksaan fisik unit APAR terkini oleh petugas K3RS.">

        <x-table :headers="['Nomor / Kode APAR', 'Lokasi', 'Tanggal Pemeriksaan', 'Kondisi', 'Status', 'Aksi']">
            <!-- Row 1: Baik -->
            <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="font-mono font-bold text-xs text-slate-900 tabular-nums">
                        APAR-IGD-01
                    </span>
                    <span class="block text-[11px] text-slate-500">Powder 6 Kg</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-medium text-xs text-slate-900 block">IGD Zona Triase</span>
                    <span class="text-[11px] text-slate-500">Gedung Utama, Lantai 1</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-600 tabular-nums font-mono">
                    02/10/2026
                </td>
                <td class="px-4 py-3 text-xs text-slate-700">
                    Jarum tekanan hijau, pin pengaman dan segel utuh, selang elastis.
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <x-badge variant="success" :dot="true">
                        Baik
                    </x-badge>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right">
                    <a href="{{ url('/riwayat/INS-2026-001') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        Lihat
                    </a>
                </td>
            </tr>

            <!-- Row 2: Perlu Perhatian -->
            <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="font-mono font-bold text-xs text-slate-900 tabular-nums">
                        APAR-LAB-03
                    </span>
                    <span class="block text-[11px] text-slate-500">CO2 5 Kg</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-medium text-xs text-slate-900 block">Laboratorium Patologi</span>
                    <span class="text-[11px] text-slate-500">Gedung Penunjang, Lantai 2</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-600 tabular-nums font-mono">
                    02/10/2026
                </td>
                <td class="px-4 py-3 text-xs text-slate-700">
                    Tekanan turun mendekati batas minimum, segel masih utuh.
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <x-badge variant="warning" :dot="true">
                        Perlu Perhatian
                    </x-badge>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right">
                    <a href="{{ url('/riwayat/INS-2026-002') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        Lihat
                    </a>
                </td>
            </tr>

            <!-- Row 3: Rusak / Perlu Tindakan -->
            <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="font-mono font-bold text-xs text-slate-900 tabular-nums">
                        APAR-RWI-08
                    </span>
                    <span class="block text-[11px] text-slate-500">Powder 6 Kg</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-medium text-xs text-slate-900 block">Rawat Inap Melati</span>
                    <span class="text-[11px] text-slate-500">Gedung Rawat Inap, Koridor Barat</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-600 tabular-nums font-mono">
                    01/10/2026
                </td>
                <td class="px-4 py-3 text-xs text-slate-700">
                    Pin pengaman hilang, selang nozzle retak, perlu penggantian.
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <x-badge variant="danger" :dot="true">
                        Perlu Tindakan
                    </x-badge>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right">
                    <a href="{{ url('/riwayat/INS-2026-005') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        Lihat
                    </a>
                </td>
            </tr>

            <!-- Row 4: Baik -->
            <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="font-mono font-bold text-xs text-slate-900 tabular-nums">
                        APAR-IBS-02
                    </span>
                    <span class="block text-[11px] text-slate-500">Clean Agent 4 Kg</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-medium text-xs text-slate-900 block">Bedah Sentral (OK-2)</span>
                    <span class="text-[11px] text-slate-500">Gedung Operasi, Area Steril</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-600 tabular-nums font-mono">
                    01/10/2026
                </td>
                <td class="px-4 py-3 text-xs text-slate-700">
                    Tekanan stabil di zona hijau, segel utuh, kartu gantung terisi.
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <x-badge variant="success" :dot="true">
                        Baik
                    </x-badge>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right">
                    <a href="{{ url('/riwayat/INS-2026-001') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        Lihat
                    </a>
                </td>
            </tr>

            <!-- Row 5: Baik -->
            <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="font-mono font-bold text-xs text-slate-900 tabular-nums">
                        APAR-GZG-01
                    </span>
                    <span class="block text-[11px] text-slate-500">Wet Chemical 6 L</span>
                </td>
                <td class="px-4 py-3">
                    <span class="font-medium text-xs text-slate-900 block">Dapur Gizi Utama</span>
                    <span class="text-[11px] text-slate-500">Area Masak / Pengolahan Makanan</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-600 tabular-nums font-mono">
                    30/09/2026
                </td>
                <td class="px-4 py-3 text-xs text-slate-700">
                    Tabung bersih, bracket dinding kokoh, jarum indikator normal.
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <x-badge variant="success" :dot="true">
                        Baik
                    </x-badge>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right">
                    <a href="{{ url('/riwayat/INS-2026-001') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                        Lihat
                    </a>
                </td>
            </tr>
        </x-table>
    </x-card>
</x-layouts.app>