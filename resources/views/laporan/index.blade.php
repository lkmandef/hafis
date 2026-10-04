<x-layouts.app title="HAFIS : Pusat Laporan & Rekapitulasi APAR">
    <!-- 1. Header with Breadcrumb & Title -->
    <div class="pb-4 sm:pb-5 mb-5 sm:mb-6 border-b border-slate-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-slate-800 transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Pusat Laporan & Rekapitulasi</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Laporan & Rekapitulasi APAR
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Dokumentasi dan rekapitulasi data keselamatan proteksi kebakaran rumah sakit berbasis data real-time.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>5 Modul Laporan Aktif</span>
                </span>
            </div>
        </div>
    </div>

    <!-- 2. Document Hub: Direktori 5 Modul Laporan Resmi HAFIS -->
    <div class="space-y-4">
        <div>
            <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight mb-1">
                Pilih Jenis Laporan
            </h2>
            <p class="text-xs text-slate-500">
                Direktori dokumen resmi keselamatan dan proteksi kebakaran APAR untuk kebutuhan audit, akreditasi, dan inspeksi K3RS.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

            <!-- DOKUMEN 1: Laporan Data APAR -->
            <div class="p-5 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-all flex flex-col justify-between shadow-2xs group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                            01
                        </span>
                        <span class="text-[11px] font-medium text-slate-500 bg-slate-50 border border-slate-200/80 px-2 py-0.5 rounded">
                            Master Inventaris
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-[#14463C] transition-colors">
                        Laporan Data APAR
                    </h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Rekap data unit APAR rumah sakit: kode, jenis media, kapasitas, merek, posisi detail, lokasi berjenjang (Gedung, Lantai, Ruangan), dan status kondisi fisik.
                    </p>
                </div>

                <div class="mt-5 pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-[11px] text-slate-500 font-medium">
                        Format: <span class="text-slate-700 font-semibold">Cetak A4, CSV</span>
                    </span>

                    <a href="{{ route('laporan.apar') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 hover:bg-[#14463C] text-white font-semibold text-xs transition shadow-2xs min-h-[42px] focus:outline-none focus:ring-2 focus:ring-[#14463C]/30">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- DOKUMEN 2: Laporan Pemeriksaan APAR -->
            <div class="p-5 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-all flex flex-col justify-between shadow-2xs group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                            02
                        </span>
                        <span class="text-[11px] font-medium text-slate-500 bg-slate-50 border border-slate-200/80 px-2 py-0.5 rounded">
                            Inspeksi Fisik
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-[#14463C] transition-colors">
                        Laporan Pemeriksaan APAR
                    </h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Rekapitulasi riwayat verifikasi fisik tabung: tanggal inspeksi, jadwal berkala/mandiri, petugas pemeriksa, hasil evaluasi tabung, dan rincian checklist 10 parameter.
                    </p>
                </div>

                <div class="mt-5 pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-[11px] text-slate-500 font-medium">
                        Format: <span class="text-slate-700 font-semibold">Cetak A4, CSV</span>
                    </span>

                    <a href="{{ route('laporan.pemeriksaan') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 hover:bg-[#14463C] text-white font-semibold text-xs transition shadow-2xs min-h-[42px] focus:outline-none focus:ring-2 focus:ring-[#14463C]/30">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- DOKUMEN 3: Laporan Temuan Pemeriksaan -->
            <div class="p-5 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-all flex flex-col justify-between shadow-2xs group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                            03
                        </span>
                        <span class="text-[11px] font-medium text-slate-500 bg-slate-50 border border-slate-200/80 px-2 py-0.5 rounded">
                            Defek & Masalah
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-[#14463C] transition-colors">
                        Laporan Temuan Pemeriksaan
                    </h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Daftar khusus item checklist fisik yang berstatus Tidak Baik (tekanan drop, selang retak, segel rusak, pin hilang) disertai status penanganan tindak lanjutnya.
                    </p>
                </div>

                <div class="mt-5 pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-[11px] text-slate-500 font-medium">
                        Format: <span class="text-slate-700 font-semibold">Cetak A4, CSV</span>
                    </span>

                    <a href="{{ route('laporan.temuan') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 hover:bg-[#14463C] text-white font-semibold text-xs transition shadow-2xs min-h-[42px] focus:outline-none focus:ring-2 focus:ring-[#14463C]/30">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- DOKUMEN 4: Laporan Tindak Lanjut & Pemeliharaan -->
            <div class="p-5 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-all flex flex-col justify-between shadow-2xs group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                            04
                        </span>
                        <span class="text-[11px] font-medium text-slate-500 bg-slate-50 border border-slate-200/80 px-2 py-0.5 rounded">
                            Pemeliharaan Unit
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-[#14463C] transition-colors">
                        Laporan Tindak Lanjut & Pemeliharaan
                    </h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Log penanganan masalah: perbaikan tabung, pengisian ulang media (refill), pembersihan, penggantian komponen (segel, pin, selang), petugas, dan tanggal selesai.
                    </p>
                </div>

                <div class="mt-5 pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-[11px] text-slate-500 font-medium">
                        Format: <span class="text-slate-700 font-semibold">Cetak A4, CSV</span>
                    </span>

                    <a href="{{ route('laporan.tindak_lanjut') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 hover:bg-[#14463C] text-white font-semibold text-xs transition shadow-2xs min-h-[42px] focus:outline-none focus:ring-2 focus:ring-[#14463C]/30">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- DOKUMEN 5: Laporan Penggantian APAR -->
            <div class="p-5 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-all flex flex-col justify-between shadow-2xs group md:col-span-2 lg:col-span-1">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                            05
                        </span>
                        <span class="text-[11px] font-medium text-slate-500 bg-slate-50 border border-slate-200/80 px-2 py-0.5 rounded">
                            Log Pergantian
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-[#14463C] transition-colors">
                        Laporan Penggantian APAR
                    </h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Rekam jejak historis pergantian unit tabung lama ke unit baru: alasan penggantian (rusak, kadaluarsa, peremajaan), petugas, tanggal, dan keutuhan jejak audit dua arah.
                    </p>
                </div>

                <div class="mt-5 pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-[11px] text-slate-500 font-medium">
                        Format: <span class="text-slate-700 font-semibold">Cetak A4, CSV</span>
                    </span>

                    <a href="{{ route('laporan.penggantian') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 hover:bg-[#14463C] text-white font-semibold text-xs transition shadow-2xs min-h-[42px] focus:outline-none focus:ring-2 focus:ring-[#14463C]/30">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
