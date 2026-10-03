<x-layouts.app title="HAFIS — Data APAR">
    <!-- 1. Page Header -->
    <x-page-header
        title="Data APAR"
        description="Daftar unit APAR yang terdaftar di rumah sakit.">
        <x-slot:actions>
            <x-button variant="primary" href="{{ url('/apar/create') }}">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah APAR</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Card Tunggal untuk Toolbar, Tabel, dan Pagination -->
    <x-card class="!p-0 overflow-hidden">
        <!-- 2. Toolbar Sederhana (Dalam Satu Baris / Container) -->
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-white flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input
                    type="text"
                    placeholder="Cari kode APAR atau lokasi..."
                    class="w-full pl-9 pr-3 py-1.5 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none transition min-h-[38px]" />
            </div>

            <!-- Filters & Reset -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Filter Kondisi -->
                <div class="relative">
                    <select class="appearance-none bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-1.5 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[38px] cursor-pointer">
                        <option value="">Semua Kondisi</option>
                        <option value="baik">Baik</option>
                        <option value="perhatian">Perlu Perhatian</option>
                        <option value="rusak">Rusak / Perlu Tindakan</option>
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
                        <option value="rawat_inap">Rawat Inap</option>
                        <option value="bedah">Bedah Sentral (OK)</option>
                        <option value="laboratorium">Laboratorium</option>
                        <option value="gizi">Instalasi Gizi</option>
                        <option value="penunjang">Gedung Penunjang</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>

                <!-- Reset Filter -->
                <button type="button" class="px-2.5 py-1.5 text-xs font-medium text-slate-500 hover:text-slate-800 focus:outline-none focus:underline min-h-[38px]">
                    Reset
                </button>
            </div>
        </div>

        <!-- 3. Tabel Data APAR (8. Hierarchy: Kode -> Lokasi -> Kondisi -> Terakhir -> Aksi) -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800 border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Kode APAR</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Jenis</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Kapasitas</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Lokasi</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Kondisi</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Pemeriksaan Terakhir</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    <!-- Row 1 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-IGD-01
                        </td>
                        <td class="px-4 py-3 text-slate-600">Dry Chemical Powder</td>
                        <td class="px-4 py-3 font-mono text-slate-700">6 Kg</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            IGD Zona Triase, Lt. 1
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="success" :dot="true">Baik</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">02/10/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-IGD-01?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-LAB-03
                        </td>
                        <td class="px-4 py-3 text-slate-600">Carbon Dioxide (CO2)</td>
                        <td class="px-4 py-3 font-mono text-slate-700">5 Kg</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Lab Patologi Anatomi, Lt. 2
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="warning" :dot="true">Perlu Perhatian</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">02/10/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-LAB-03?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-RWI-08
                        </td>
                        <td class="px-4 py-3 text-slate-600">Dry Chemical Powder</td>
                        <td class="px-4 py-3 font-mono text-slate-700">6 Kg</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Rawat Inap Melati, Koridor Barat
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="danger" :dot="true">Rusak / Perlu Tindakan</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">01/10/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-RWI-08?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-IBS-02
                        </td>
                        <td class="px-4 py-3 text-slate-600">Clean Agent (HFC)</td>
                        <td class="px-4 py-3 font-mono text-slate-700">4 Kg</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Bedah Sentral (OK-2), Area Steril
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="success" :dot="true">Baik</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">01/10/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-IBS-02?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 5 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-GZG-01
                        </td>
                        <td class="px-4 py-3 text-slate-600">Wet Chemical</td>
                        <td class="px-4 py-3 font-mono text-slate-700">6 L</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Instalasi Gizi & Dapur Utama
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="success" :dot="true">Baik</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">30/09/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-GZG-01?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 6 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-FAR-01
                        </td>
                        <td class="px-4 py-3 text-slate-600">Carbon Dioxide (CO2)</td>
                        <td class="px-4 py-3 font-mono text-slate-700">3 Kg</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Depo Farmasi Rawat Jalan, Lt. 1
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="success" :dot="true">Baik</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">29/09/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-FAR-01?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 7 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-RAD-02
                        </td>
                        <td class="px-4 py-3 text-slate-600">Clean Agent (HFC)</td>
                        <td class="px-4 py-3 font-mono text-slate-700">4 Kg</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Ruang CT-Scan Radiologi, Lt. 1
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="success" :dot="true">Baik</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">28/09/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-RAD-02?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 8 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-RWI-14
                        </td>
                        <td class="px-4 py-3 text-slate-600">Dry Chemical Powder</td>
                        <td class="px-4 py-3 font-mono text-slate-700">6 Kg</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Rawat Inap Anggrek, Koridor Timur Lt. 3
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="warning" :dot="true">Perlu Perhatian</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">27/09/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-RWI-14?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 9 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-GSL-01
                        </td>
                        <td class="px-4 py-3 text-slate-600">Dry Chemical Powder</td>
                        <td class="px-4 py-3 font-mono text-slate-700">9 Kg</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Ruang Genset & Trafo Listrik, Basement
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="success" :dot="true">Baik</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">25/09/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-GSL-01?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 10 -->
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            APAR-LND-02
                        </td>
                        <td class="px-4 py-3 text-slate-600">Dry Chemical Powder</td>
                        <td class="px-4 py-3 font-mono text-slate-700">6 Kg</td>
                        <td class="px-4 py-3 font-medium text-slate-900">
                            Ruang Laundry & Linen Rumah Sakit
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge variant="danger" :dot="true">Rusak / Perlu Tindakan</x-badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">24/09/2026</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/apar/detail') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Lihat</a>
                                <a href="{{ url('/apar/edit') }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Edit</a>
                                <button type="button" onclick="if(confirm('Hapus unit APAR-LND-02?')) window.location.reload();" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md text-rose-600 hover:bg-rose-50 transition-colors">Hapus</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 6. Pagination Sederhana -->
        <div class="px-4 sm:px-5 py-3.5 border-t border-slate-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600">
            <div>
                Menampilkan <span class="font-semibold text-slate-900 tabular-nums">1</span> - <span class="font-semibold text-slate-900 tabular-nums">10</span> dari <span class="font-semibold text-slate-900 tabular-nums">142</span> unit APAR
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
                    15
                </button>
                <button type="button" class="px-2.5 py-1.5 rounded border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium">
                    Selanjutnya
                </button>
            </div>
        </div>
    </x-card>
</x-layouts.app>