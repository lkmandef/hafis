<x-layouts.app title="HAFIS — Hospital APAR & Fire Inspection System">
    <!-- Page Header Komponen -->
    <x-page-header
        title="Fondasi Antarmuka UI"
        description="Pustaka komponen dasar Blade dengan penerapan sistem desain Claymorphism.">
        <x-slot:actions>
            <x-button variant="secondary">
                <svg class="w-4 h-4 text-[#52586D]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <span>Unduh Spesifikasi</span>
            </x-button>

            <x-button variant="primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Aksi Utama</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- 1. Row Metrik / Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <x-stat-card
            title="Komponen Primer"
            value="10 Unit"
            trend="+2 modul baru"
            trend-type="up">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card
            title="Indikator Status"
            value="5 Varian"
            trend="Standar WCAG AA"
            trend-type="up"
            icon-bg="bg-[#D0F2E4] text-[#065F46]">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card
            title="Pustaka Tombol"
            value="3 Gaya"
            trend="Fokus keyboard aktif"
            trend-type="up"
            icon-bg="bg-[#FFE1B9] text-[#9A3412]">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card
            title="Struktur Tabel"
            value="1 Modul"
            trend="Mendukung empty state"
            trend-type="neutral"
            icon-bg="bg-[#F0F2F8] text-[#334155]">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- 2. Section Dua Kolom: Form Input & Status Badges -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Card Form Input Dasar -->
        <x-card title="Form Input Dasar" subtitle="Pola input dengan inset shadow khas Claymorphism.">
            <form action="#" method="POST" onsubmit="event.preventDefault();" class="space-y-4">
                <x-input
                    name="sample_code"
                    label="Label Input Standar"
                    placeholder="Masukkan kode referensi..."
                    hint="Teks bantuan informatif untuk panduan pengguna.">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                    </x-slot:icon>
                </x-input>

                <x-input
                    name="sample_error"
                    label="Contoh Input Error State"
                    value="Input Tidak Sesuai"
                    error="Format karakter tidak valid atau kolom belum dilengkapi.">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </x-slot:icon>
                </x-input>

                <div class="pt-3 flex flex-wrap items-center gap-3">
                    <x-button variant="primary" type="button">
                        Tombol Primer
                    </x-button>

                    <x-button variant="secondary" type="button">
                        Tombol Sekunder
                    </x-button>

                    <x-button variant="ghost" type="button">
                        Tombol Ghost
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Card Koleksi Badge / Status -->
        <x-card title="Badge & Status Semantik" subtitle="Elemen pill dengan kontras tinggi sesuai standar WCAG AA.">
            <div class="space-y-6">
                <div>
                    <span class="block text-xs font-bold text-[#8F95A8] uppercase tracking-wider mb-2.5">
                        Varian Status dengan Indikator Titik
                    </span>
                    <div class="flex flex-wrap gap-2.5">
                        <x-badge variant="success" dot="true">Kondisi Baik</x-badge>
                        <x-badge variant="warning" dot="true">Perlu Pengecekan</x-badge>
                        <x-badge variant="danger" dot="true">Kadaluwarsa / Rusak</x-badge>
                        <x-badge variant="purple" dot="true">Sedang Diperiksa</x-badge>
                        <x-badge variant="neutral" dot="true">Belum Terdata</x-badge>
                    </div>
                </div>

                <div>
                    <span class="block text-xs font-bold text-[#8F95A8] uppercase tracking-wider mb-2.5">
                        Varian Status Standar (Label Kategori)
                    </span>
                    <div class="flex flex-wrap gap-2.5">
                        <x-badge variant="purple">Powder 6 Kg</x-badge>
                        <x-badge variant="neutral">CO2 3 Kg</x-badge>
                        <x-badge variant="success">Zona Gedung A</x-badge>
                        <x-badge variant="warning">Lantai 2</x-badge>
                    </div>
                </div>

                <!-- Callout Box Informasi -->
                <div class="p-4 rounded-[18px] bg-[#F6F7FB] border border-[#E6E9F2] flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#EDE9FE] text-[#6A50E7] flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-xs text-[#52586D] leading-relaxed">
                        <span class="font-bold text-[#1E2038] block mb-0.5">Prinsip Desain Anti Slop:</span>
                        Semua komponen mempertahankan perpaduan warna lembut claymorphism tanpa mengorbankan keterbacaan, rasio kontras teks, dan kenyamanan target sentuh.
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- 3. Section Tabel Dasar -->
    <x-card title="Struktur Tabel Dasar" subtitle="Format data tabular dengan pemisah halus dan aksi kontekstual.">
        <x-table :headers="['Kode Identifikasi', 'Nama Komponen', 'Status Kelayakan', 'Tipe Varian', 'Aksi']">
            <!-- Row 1 -->
            <tr class="hover:bg-[#FBFBFE] transition-colors">
                <td class="px-5 py-4 font-mono font-bold text-xs text-[#6A50E7]">
                    UI-BTN-01
                </td>
                <td class="px-5 py-4 font-semibold text-[#1E2038]">
                    Tombol Utama (Primary Button)
                </td>
                <td class="px-5 py-4">
                    <x-badge variant="success" dot="true">Aktif</x-badge>
                </td>
                <td class="px-5 py-4 text-xs text-[#52586D]">
                    Elevated Clay Shadow
                </td>
                <td class="px-5 py-4">
                    <x-button variant="secondary" class="!px-3 !py-1 !text-xs !min-h-[36px]">
                        Periksa
                    </x-button>
                </td>
            </tr>

            <!-- Row 2 -->
            <tr class="hover:bg-[#FBFBFE] transition-colors">
                <td class="px-5 py-4 font-mono font-bold text-xs text-[#6A50E7]">
                    UI-INP-02
                </td>
                <td class="px-5 py-4 font-semibold text-[#1E2038]">
                    Input Teks (Inset Shadow)
                </td>
                <td class="px-5 py-4">
                    <x-badge variant="purple" dot="true">Terverifikasi</x-badge>
                </td>
                <td class="px-5 py-4 text-xs text-[#52586D]">
                    Carved Clay Inset
                </td>
                <td class="px-5 py-4">
                    <x-button variant="secondary" class="!px-3 !py-1 !text-xs !min-h-[36px]">
                        Periksa
                    </x-button>
                </td>
            </tr>

            <!-- Row 3 -->
            <tr class="hover:bg-[#FBFBFE] transition-colors">
                <td class="px-5 py-4 font-mono font-bold text-xs text-[#6A50E7]">
                    UI-CRD-03
                </td>
                <td class="px-5 py-4 font-semibold text-[#1E2038]">
                    Panel Kartu (Content Card)
                </td>
                <td class="px-5 py-4">
                    <x-badge variant="warning" dot="true">Perlu Tinjauan</x-badge>
                </td>
                <td class="px-5 py-4 text-xs text-[#52586D]">
                    Rounded 24px Surface
                </td>
                <td class="px-5 py-4">
                    <x-button variant="secondary" class="!px-3 !py-1 !text-xs !min-h-[36px]">
                        Periksa
                    </x-button>
                </td>
            </tr>
        </x-table>
    </x-card>
</x-layouts.app>