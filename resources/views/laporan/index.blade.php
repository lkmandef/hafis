<x-layouts.app title="HAFIS : Pusat Laporan & Rekapitulasi APAR">
    <!-- Header with Breadcrumb & Title -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-slate-800 transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Pusat Laporan & Rekapitulasi</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Laporan & Rekapitulasi APAR
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Dokumentasi dan rekapitulasi data keselamatan proteksi kebakaran rumah sakit berbasis data real-time.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>5 Modul Laporan Aktif</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Ringkasan Cepat Keselamatan APAR Rumah Sakit (Real Database) -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-8">
        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Unit Terdaftar</span>
            <div class="flex items-baseline gap-1.5">
                <span class="font-mono text-2xl font-extrabold text-slate-900 tabular-nums">{{ $stats['apar_total'] }}</span>
                <span class="text-[11px] text-slate-400">tabung</span>
            </div>
            <span class="text-[10px] text-slate-400 block mt-1">Aktif: {{ $stats['apar_aktif'] }} unit</span>
        </div>

        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-2xs">
            <span class="text-[11px] font-semibold text-sky-700 uppercase tracking-wider block mb-1">Total Pemeriksaan</span>
            <div class="flex items-baseline gap-1.5">
                <span class="font-mono text-2xl font-extrabold text-sky-900 tabular-nums">{{ $stats['pemeriksaan_total'] }}</span>
                <span class="text-[11px] text-sky-600">kali</span>
            </div>
            <span class="text-[10px] text-sky-700 block mt-1">Hasil Baik: {{ $stats['pemeriksaan_baik'] }}</span>
        </div>

        <div class="p-4 bg-white border border-rose-200 bg-rose-50/20 rounded-xl shadow-2xs">
            <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider block mb-1">Temuan Masalah</span>
            <div class="flex items-baseline gap-1.5">
                <span class="font-mono text-2xl font-extrabold text-rose-900 tabular-nums">{{ $stats['temuan_total'] }}</span>
                <span class="text-[11px] text-rose-600">item</span>
            </div>
            <span class="text-[10px] text-rose-600 font-medium block mt-1">Belum Selesai: {{ $stats['temuan_belum_selesai'] }}</span>
        </div>

        <div class="p-4 bg-white border border-amber-200 bg-amber-50/20 rounded-xl shadow-2xs">
            <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider block mb-1">Tindak Lanjut & Pemeliharaan</span>
            <div class="flex items-baseline gap-1.5">
                <span class="font-mono text-2xl font-extrabold text-amber-900 tabular-nums">{{ $stats['tindak_lanjut_total'] }}</span>
                <span class="text-[11px] text-amber-600">tindakan</span>
            </div>
            <span class="text-[10px] text-amber-700 block mt-1">Selesai: {{ $stats['tindak_lanjut_selesai'] }}</span>
        </div>

        <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-2xs col-span-2 lg:col-span-1">
            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block mb-1">Penggantian Unit</span>
            <div class="flex items-baseline gap-1.5">
                <span class="font-mono text-2xl font-extrabold text-slate-900 tabular-nums">{{ $stats['penggantian_total'] }}</span>
                <span class="text-[11px] text-slate-500">peristiwa</span>
            </div>
            <span class="text-[10px] text-slate-500 block mt-1">Rusak: {{ $stats['penggantian_rusak'] }}</span>
        </div>
    </div>

    <!-- Grid 5 Modul Laporan Resmi HAFIS (Section 2 & 8) -->
    <div class="space-y-4">
        <div>
            <h2 class="text-sm font-bold text-slate-900 tracking-tight mb-1">Pilih Jenis Laporan</h2>
            <p class="text-xs text-slate-500">Setiap laporan dirancang khusus untuk kebutuhan audit, pemeliharaan, dan inspeksi K3RS.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            <!-- LAPORAN 1: Data APAR -->
            <div class="p-5 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-all flex flex-col justify-between shadow-2xs group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                            01
                        </span>
                        <span class="font-mono text-[11px] text-slate-500 font-semibold">
                            {{ $stats['apar_total'] }} Unit
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-slate-800 transition-colors">
                        Laporan Data APAR
                    </h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Master data tabung pemadam api rumah sakit: kode, jenis media, kapasitas, merek, lokasi berjenjang (Gedung, Lantai, Ruangan), kondisi fisik, dan status operasional.
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-1.5 text-[11px]">
                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold">{{ $stats['apar_aktif'] }} Aktif</span>
                        <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-800 border border-rose-200 font-semibold">{{ $stats['apar_rusak'] }} Rusak</span>
                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-semibold">{{ $stats['apar_perhatian'] }} Perhatian</span>
                    </div>
                </div>

                <div class="mt-5 pt-3">
                    <a href="{{ route('laporan.apar') }}" 
                       class="w-full inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[44px]">
                        <span>Buka Laporan Data APAR</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- LAPORAN 2: Pemeriksaan APAR -->
            <div class="p-5 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-all flex flex-col justify-between shadow-2xs group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-800 flex items-center justify-center font-bold text-xs">
                            02
                        </span>
                        <span class="font-mono text-[11px] text-sky-800 font-semibold">
                            {{ $stats['pemeriksaan_total'] }} Inspeksi
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-slate-800 transition-colors">
                        Laporan Pemeriksaan APAR
                    </h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Rekapitulasi riwayat verifikasi fisik tabung: tanggal inspeksi, jadwal berkala/mandiri, petugas pemeriksa, hasil evaluasi tabung, dan tautan rincian checklist 10 parameter.
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-1.5 text-[11px]">
                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold">{{ $stats['pemeriksaan_baik'] }} Baik</span>
                        <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-800 border border-rose-200 font-semibold">{{ $stats['pemeriksaan_perlu_tindakan'] }} Perlu Tindakan</span>
                    </div>
                </div>

                <div class="mt-5 pt-3">
                    <a href="{{ route('laporan.pemeriksaan') }}" 
                       class="w-full inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[44px]">
                        <span>Buka Laporan Pemeriksaan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- LAPORAN 3: Temuan Pemeriksaan -->
            <div class="p-5 bg-white border border-rose-200 rounded-xl hover:border-rose-300 transition-all flex flex-col justify-between shadow-2xs group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center font-bold text-xs">
                            03
                        </span>
                        <span class="font-mono text-[11px] text-rose-800 font-semibold">
                            {{ $stats['temuan_total'] }} Masalah
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-slate-800 transition-colors">
                        Laporan Temuan Pemeriksaan
                    </h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Daftar khusus item checklist fisik yang berstatus Tidak Baik (misal tekanan drop, selang retak, segel rusak, pin hilang) disertai status penanganan tindak lanjutnya.
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-1.5 text-[11px]">
                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-900 font-bold">{{ $stats['temuan_belum_selesai'] }} Belum Tuntas</span>
                        <span class="text-slate-400">dari {{ $stats['temuan_total'] }} temuan</span>
                    </div>
                </div>

                <div class="mt-5 pt-3">
                    <a href="{{ route('laporan.temuan') }}" 
                       class="w-full inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg bg-rose-700 hover:bg-rose-800 text-white font-semibold text-xs transition shadow-2xs min-h-[44px]">
                        <span>Buka Laporan Temuan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- LAPORAN 4: Tindak Lanjut & Pemeliharaan -->
            <div class="p-5 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-all flex flex-col justify-between shadow-2xs group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs">
                            04
                        </span>
                        <span class="font-mono text-[11px] text-amber-800 font-semibold">
                            {{ $stats['tindak_lanjut_total'] }} Tindakan
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-slate-800 transition-colors">
                        Laporan Tindak Lanjut & Pemeliharaan
                    </h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Log penanganan masalah: perbaikan tabung, pengisian ulang media (refill), pembersihan, penggantian komponen (segel, pin, selang), pelaksana teknis, dan tanggal selesai.
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-1.5 text-[11px]">
                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold">{{ $stats['tindak_lanjut_selesai'] }} Selesai</span>
                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-semibold">{{ $stats['tindak_lanjut_proses'] }} Dalam Proses</span>
                    </div>
                </div>

                <div class="mt-5 pt-3">
                    <a href="{{ route('laporan.tindak_lanjut') }}" 
                       class="w-full inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[44px]">
                        <span>Buka Laporan Pemeliharaan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- LAPORAN 5: Penggantian APAR -->
            <div class="p-5 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-all flex flex-col justify-between shadow-2xs group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold text-xs">
                            05
                        </span>
                        <span class="font-mono text-[11px] text-indigo-800 font-semibold">
                            {{ $stats['penggantian_total'] }} Penggantian
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 group-hover:text-slate-800 transition-colors">
                        Laporan Penggantian APAR
                    </h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Rekam jejak historis pergantian unit tabung lama ke unit baru: alasan penggantian (rusak, kadaluarsa, peremajaan), petugas, tanggal, dan keutuhan jejak audit dua arah.
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-1.5 text-[11px]">
                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold">Integritas Riwayat Utuh</span>
                    </div>
                </div>

                <div class="mt-5 pt-3">
                    <a href="{{ route('laporan.penggantian') }}" 
                       class="w-full inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[44px]">
                        <span>Buka Laporan Penggantian</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
