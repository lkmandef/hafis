<x-layouts.app title="HAFIS — Jadwal Inspeksi">
    <!-- 1. Page Header -->
    <x-page-header
        title="Jadwal Inspeksi"
        description="Jadwal pemeriksaan berkala APAR fasilitas rumah sakit.">
        <x-slot:actions>
            <x-button variant="primary" type="button">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Jadwal</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- 2. Ringkasan Sederhana & Compact (Single Hairline-Divided Card) -->
    <x-card class="!p-0 overflow-hidden mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
            <!-- Jadwal Hari Ini -->
            <div class="p-4 sm:p-5">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Jadwal Hari Ini
                </span>
                <div class="mt-1.5 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums">12</span>
                    <span class="text-xs text-slate-500 font-medium">Unit</span>
                </div>
                <span class="block text-[11px] text-amber-700 mt-1 font-medium">
                    Perlu diperiksa hari ini
                </span>
            </div>

            <!-- Akan Datang -->
            <div class="p-4 sm:p-5">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Akan Datang
                </span>
                <div class="mt-1.5 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums">45</span>
                    <span class="text-xs text-slate-500 font-medium">Unit</span>
                </div>
                <span class="block text-[11px] text-slate-500 mt-1 font-medium">
                    Jadwal 14 hari ke depan
                </span>
            </div>

            <!-- Terlambat -->
            <div class="p-4 sm:p-5">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Terlambat
                </span>
                <div class="mt-1.5 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-bold text-rose-600 tabular-nums">4</span>
                    <span class="text-xs text-slate-500 font-medium">Unit</span>
                </div>
                <span class="block text-[11px] text-rose-600 mt-1 font-medium">
                    Melewati tenggat inspeksi
                </span>
            </div>

            <!-- Selesai -->
            <div class="p-4 sm:p-5">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Selesai
                </span>
                <div class="mt-1.5 flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-bold text-emerald-700 tabular-nums">81</span>
                    <span class="text-xs text-slate-500 font-medium">Unit</span>
                </div>
                <span class="block text-[11px] text-emerald-700 mt-1 font-medium">
                    Telah diuji bulan ini
                </span>
            </div>
        </div>
    </x-card>

    <!-- Card Tunggal untuk Toolbar, Tabel Jadwal, dan Pagination -->
    <x-card class="!p-0 overflow-hidden">
        <!-- 3. Filter Toolbar (Sederhana dalam Satu Baris) -->
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-white flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Search Kode APAR -->
            <div class="relative flex-1 max-w-sm">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input
                    type="text"
                    placeholder="Cari kode APAR..."
                    class="w-full pl-9 pr-3 py-1.5 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none transition min-h-[38px]" />
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Filter Periode -->
                <div class="relative">
                    <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-1.5 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[38px] cursor-pointer">
                        <option value="">Semua Periode</option>
                        <option value="hari_ini">Hari Ini</option>
                        <option value="minggu_ini">Minggu Ini</option>
                        <option value="bulan_ini" selected>Bulan Ini (Okt 2026)</option>
                        <option value="bulan_depan">Bulan Depan</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>

                <!-- Filter Status -->
                <div class="relative">
                    <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-1.5 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[38px] cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="hari_ini">Hari Ini</option>
                        <option value="terjadwal">Terjadwal</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="selesai">Selesai</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>

                <!-- Filter Lokasi -->
                <div class="relative">
                    <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-1.5 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[38px] cursor-pointer">
                        <option value="">Semua Lokasi</option>
                        <option value="igd">Gedung Utama (IGD)</option>
                        <option value="rawat_inap">Rawat Inap Melati</option>
                        <option value="bedah">Bedah Sentral (OK)</option>
                        <option value="lab">Laboratorium</option>
                        <option value="gizi">Instalasi Gizi</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>

                <!-- Reset -->
                <button type="button" class="px-2.5 py-1.5 text-xs font-medium text-slate-500 hover:text-slate-800 focus:outline-none focus:underline min-h-[38px]">
                    Reset
                </button>
            </div>
        </div>

        <!-- 4. DAFTAR JADWAL (Tabel) -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Kode APAR</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Lokasi</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Jadwal Pemeriksaan</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Pemeriksa</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Status</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    <!-- Baris 1: Status Hari Ini (Dapat Diperiksa) -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-IGD-01
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Lantai 1 - Koridor IGD Triase
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-900 whitespace-nowrap font-medium">
                            02/10/2026
                        </td>
                        <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                            Budi Santoso
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="warning" :dot="true">Hari Ini</x-badge>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                                    Lihat
                                </a>
                                <a href="{{ url('/pemeriksaan') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition-colors">
                                    Mulai Pemeriksaan
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Baris 2: Status Terlambat (Dapat Diperiksa) - ACTION != STATUS (Tombol tetap slate-900) -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-RWI-04
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Lantai 2 - Ruang Perawat Melati
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-900 whitespace-nowrap font-medium">
                            28/09/2026
                        </td>
                        <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                            Rudi Hartono
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="danger" :dot="true">Terlambat</x-badge>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                                    Lihat
                                </a>
                                <a href="{{ url('/pemeriksaan') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition-colors">
                                    Mulai Pemeriksaan
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Baris 3: Status Terjadwal -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-OK-01
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Lantai 3 - Koridor Bedah Sentral
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-600 whitespace-nowrap font-medium">
                            05/10/2026
                        </td>
                        <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                            Budi Santoso
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="neutral" :dot="true">Terjadwal</x-badge>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                                    Lihat
                                </a>
                                <a href="{{ url('/pemeriksaan') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition-colors">
                                    Mulai Pemeriksaan
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Baris 4: Status Terjadwal -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-LAB-02
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Lantai 2 - Depan Laboratorium Kimia
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-600 whitespace-nowrap font-medium">
                            08/10/2026
                        </td>
                        <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                            Rudi Hartono
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="neutral" :dot="true">Terjadwal</x-badge>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                                    Lihat
                                </a>
                                <a href="{{ url('/pemeriksaan') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition-colors">
                                    Mulai Pemeriksaan
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Baris 5: Status Selesai (Hanya tombol Lihat) -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-GZG-01
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Lantai 1 - Dapur Gizi Utama
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-600 whitespace-nowrap font-medium">
                            30/09/2026
                        </td>
                        <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                            Budi Santoso
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="success" :dot="true">Selesai</x-badge>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                                    Lihat
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Baris 6: Status Selesai (Hanya tombol Lihat) -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-FAR-01
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Lantai 1 - Depo Farmasi Rawat Jalan
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-600 whitespace-nowrap font-medium">
                            29/09/2026
                        </td>
                        <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                            Rudi Hartono
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="success" :dot="true">Selesai</x-badge>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                                    Lihat
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Sederhana -->
        <div class="px-4 sm:px-5 py-3.5 border-t border-slate-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600">
            <div>
                Menampilkan <span class="font-semibold text-slate-900 tabular-nums">1</span> - <span class="font-semibold text-slate-900 tabular-nums">6</span> dari <span class="font-semibold text-slate-900 tabular-nums">142</span> jadwal inspeksi
            </div>

            <div class="flex items-center gap-1">
                <button type="button" class="px-2.5 py-1.5 rounded border border-slate-200 text-slate-400 hover:bg-slate-50 cursor-not-allowed text-xs font-medium" disabled>
                    Sebelumnya
                </button>
                <button type="button" class="w-7 h-7 rounded bg-slate-900 text-white font-semibold text-xs flex items-center justify-center">
                    1
                </button>
                <button type="button" class="w-7 h-7 rounded border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center justify-center">
                    2
                </button>
                <button type="button" class="w-7 h-7 rounded border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center justify-center">
                    3
                </button>
                <span class="px-1 text-slate-400">...</span>
                <button type="button" class="w-7 h-7 rounded border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center justify-center">
                    24
                </button>
                <button type="button" class="px-2.5 py-1.5 rounded border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium">
                    Selanjutnya
                </button>
            </div>
        </div>
    </x-card>
</x-layouts.app>