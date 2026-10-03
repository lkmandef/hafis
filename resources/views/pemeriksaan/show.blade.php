<x-layouts.app title="HAFIS : Hasil Pemeriksaan {{ $pemeriksaan->apar->kode }}">
    <!-- 1. Header with Breadcrumb & Navigation -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-slate-800 transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            @if($pemeriksaan->jadwal)
                <a href="{{ route('jadwal.show', $pemeriksaan->jadwal->id) }}" class="hover:text-slate-800 transition-colors">{{ $pemeriksaan->jadwal->nama }}</a>
                <span class="text-slate-300">/</span>
            @endif
            <a href="{{ route('apar.show', $pemeriksaan->apar_id) }}" class="hover:text-slate-800 transition-colors">{{ $pemeriksaan->apar->kode }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Hasil Pemeriksaan</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Hasil Checklist Pemeriksaan Fisik
                    </h1>
                    <span class="font-mono text-base font-extrabold text-slate-900 bg-white border border-slate-200 px-3 py-0.5 rounded-lg tabular-nums shadow-2xs">
                        {{ $pemeriksaan->apar->kode }}
                    </span>
                    <x-badge :variant="$pemeriksaan->hasil_badge_variant" :dot="true">
                        {{ $pemeriksaan->hasil_label }}
                    </x-badge>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Dokumentasi catatan verifikasi fisik komponen unit APAR di lapangan.
                </p>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak Lembar</span>
                </button>

                @if($pemeriksaan->jadwal)
                    <x-button variant="secondary" href="{{ route('jadwal.show', $pemeriksaan->jadwal->id) }}" class="min-h-[44px]">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Jadwal Inspeksi</span>
                    </x-button>
                @endif

                <x-button variant="primary" href="{{ route('apar.show', $pemeriksaan->apar_id) }}" class="min-h-[44px]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Detail APAR</span>
                </x-button>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Flash (jika baru disimpan) -->
    @if(session('success'))
        <div class="max-w-4xl mx-auto mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- 2. Informasi Pemeriksaan & APAR (Section 12) -->
        <x-card title="Informasi Pemeriksaan & Unit APAR" subtitle="Konteks pelaksanaan inspeksi fisik tabung APAR di lokasi rumah sakit.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <!-- Kolom Kiri: Identitas APAR & Lokasi -->
                <div class="space-y-3.5 border-b md:border-b-0 md:border-r border-slate-100 pb-4 md:pb-0 md:pr-6">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Identitas Fisik Unit</span>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Kode APAR</span>
                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $pemeriksaan->apar->kode }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Nomor Register RS</span>
                        <span class="font-mono text-slate-800">{{ $pemeriksaan->apar->nomor_register ?: 'Belum dicatat' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Jenis Media & Kapasitas</span>
                        <span class="font-semibold text-slate-900">{{ $pemeriksaan->apar->jenis_media }} ({{ $pemeriksaan->apar->kapasitas }})</span>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-500 font-medium block mb-0.5">Lokasi Penempatan</span>
                        <span class="font-semibold text-slate-900 block">
                            {{ $pemeriksaan->apar->gedung?->nama ?? '-' }} &bull; {{ $pemeriksaan->apar->lantai?->nama ?? '-' }}
                        </span>
                        <span class="text-slate-600 block text-[11px] mt-0.5">
                            Ruangan: {{ $pemeriksaan->apar->ruangan?->nama ?? '-' }}
                        </span>
                        @if($pemeriksaan->apar->posisi_detail)
                            <span class="text-slate-400 text-[11px] italic block mt-0.5">
                                {{ $pemeriksaan->apar->posisi_detail }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan: Sesi Pemeriksaan -->
                <div class="space-y-3.5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pelaksanaan Inspeksi</span>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Jadwal Inspeksi</span>
                        @if($pemeriksaan->jadwal)
                            <a href="{{ route('jadwal.show', $pemeriksaan->jadwal->id) }}" class="font-semibold text-sky-800 hover:underline">
                                {{ $pemeriksaan->jadwal->nama }}
                            </a>
                        @else
                            <span class="text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 font-medium">
                                Pemeriksaan Mandiri (Ad-Hoc)
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Petugas Pemeriksa</span>
                        <span class="font-semibold text-slate-900">{{ $pemeriksaan->pemeriksa_nama }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Waktu Pemeriksaan</span>
                        <span class="font-mono font-medium text-slate-900 tabular-nums">
                            {{ $pemeriksaan->tanggal_pemeriksaan->format('d/m/Y H:i') }} WIB
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Hasil Keseluruhan</span>
                        <x-badge :variant="$pemeriksaan->hasil_badge_variant" :dot="true">
                            {{ $pemeriksaan->hasil_label }}
                        </x-badge>
                    </div>

                    @if($pemeriksaan->catatan)
                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-slate-500 font-medium block mb-1">Catatan Umum:</span>
                            <p class="text-slate-800 italic bg-slate-50 p-2.5 rounded border border-slate-200 leading-relaxed text-[11px]">
                                {{ $pemeriksaan->catatan }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- 3. Tabel Hasil Checklist Pemeriksaan Fisik (Section 12) -->
        <x-card title="Hasil Checklist Pemeriksaan Fisik" subtitle="Rincian evaluasi terhadap setiap parameter kondisi fisik APAR.">
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                            <th class="w-12 px-3.5 py-3 text-center font-bold">No.</th>
                            <th class="px-4 py-3 font-bold">Item Kriteria Pemeriksaan</th>
                            <th class="w-36 px-4 py-3 text-center font-bold">Hasil Evaluasi</th>
                            <th class="px-4 py-3 font-bold">Catatan Temuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                        @forelse($pemeriksaan->details as $index => $detail)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-3.5 py-3 text-center font-mono text-slate-400 font-medium tabular-nums text-[11px]">
                                {{ sprintf('%02d', $index + 1) }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">
                                {{ $detail->item_nama }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-badge :variant="$detail->hasil_badge_variant" :dot="true">
                                    {{ $detail->hasil_label }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                @if($detail->catatan)
                                    <span class="font-medium text-slate-800">{{ $detail->catatan }}</span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-400 italic">
                                Belum ada detail checklist tercatat untuk pemeriksaan ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- 4. Foto Dokumentasi Pemeriksaan (jika ada) -->
        @if($pemeriksaan->foto_path)
            <x-card title="Foto Dokumentasi Lapangan" subtitle="Lampiran visual kondisi tabung saat dilakukan pemeriksaan.">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 inline-block">
                    <img src="{{ asset('storage/' . $pemeriksaan->foto_path) }}" 
                         alt="Foto Pemeriksaan {{ $pemeriksaan->apar->kode }}" 
                         class="max-w-md w-full h-auto rounded-lg border border-slate-300 shadow-2xs object-cover">
                </div>
            </x-card>
        @endif

        <!-- 5. Tombol Aksi Bawah -->
        <div class="p-4 bg-white border border-slate-200 rounded-xl flex flex-wrap items-center justify-between gap-3 shadow-2xs no-print">
            <a href="{{ route('apar.show', $pemeriksaan->apar_id) }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition min-h-[44px]">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Detail APAR</span>
            </a>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-2xs min-h-[44px] inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak Lembar Hasil</span>
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>
