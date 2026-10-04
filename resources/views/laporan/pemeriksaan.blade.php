@if($isPrint)
    <x-layouts.print 
        title="Cetak Laporan Riwayat Pemeriksaan APAR - HAFIS RSUD" 
        reportTitle="LAPORAN RIWAYAT PEMERIKSAAN & INSPEKSI FISIK APAR" 
        :filters="$filtersSummary">

        <!-- Ringkasan Rekapitulasi Data -->
        <div style="margin-bottom: 12px; display: flex; gap: 10px; font-size: 8pt;">
            <div style="border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px; background: #f8fafc;">
                Total Pemeriksaan: <strong>{{ $rekap['total'] }}</strong>
            </div>
            <div style="border: 1px solid #a7f3d0; padding: 6px 10px; border-radius: 4px; background: #ecfdf5; color: #065f46;">
                Kondisi Baik: <strong>{{ $rekap['baik'] }}</strong>
            </div>
            <div style="border: 1px solid #fecaca; padding: 6px 10px; border-radius: 4px; background: #fef2f2; color: #991b1b;">
                Perlu Tindakan: <strong>{{ $rekap['perlu_tindakan'] }}</strong>
            </div>
            <div style="border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px; background: #f1f5f9;">
                Inspeksi Terjadwal: <strong>{{ $rekap['terjadwal'] }}</strong>
            </div>
            <div style="border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px; background: #f8fafc;">
                Inspeksi Mandiri: <strong>{{ $rekap['mandiri'] }}</strong>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">No</th>
                    <th>Tanggal & Waktu</th>
                    <th>Kode APAR</th>
                    <th>Lokasi (Gedung / Lt / Ruang)</th>
                    <th>Jadwal Inspeksi</th>
                    <th>Petugas Pemeriksa</th>
                    <th>Hasil Keseluruhan</th>
                    <th>Catatan Inspeksi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemeriksaans as $idx => $p)
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="font-mono">
                            {{ $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="font-mono font-bold">{{ $p->apar?->kode ?? '-' }}</td>
                        <td>
                            <strong>{{ $p->apar?->gedung?->nama ?? '-' }}</strong> - Lt. {{ $p->apar?->lantai?->nomor_lantai ?? '-' }}
                            <div>{{ $p->apar?->ruangan?->nama ?? '-' }}</div>
                        </td>
                        <td>
                            @if($p->jadwal)
                                {{ $p->jadwal->nama }}
                            @else
                                <span style="color: #64748b;">Inspeksi Mandiri</span>
                            @endif
                        </td>
                        <td>{{ $p->pemeriksa_nama }}</td>
                        <td>
                            @if($p->hasil_keseluruhan === 'baik')
                                <span class="badge badge-success">Baik</span>
                            @else
                                <span class="badge badge-danger">Perlu Tindakan</span>
                            @endif
                        </td>
                        <td>{{ $p->catatan ?: '-' }}</td>
                        <td>
                            <span class="badge badge-neutral">{{ ucfirst($p->status ?? 'selesai') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px; color: #64748b;">
                            Belum ada data untuk filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-layouts.print>
@else
    <x-layouts.app title="HAFIS : Laporan Pemeriksaan APAR">
        <!-- Header with Breadcrumb & Action Buttons -->
        <div class="pb-4 mb-4 border-b border-slate-200">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('laporan.index') }}" class="hover:text-slate-800 transition-colors">Laporan & Rekapitulasi</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-800 font-semibold">Laporan Pemeriksaan APAR</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Laporan Pemeriksaan APAR</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-700 font-bold">Laporan 2</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Riwayat operasional inspeksi fisik APAR: tanggal, petugas pelaksana, jadwal, dan kesimpulan checklist.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['format' => 'print']) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[38px]">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Cetak Laporan</span>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 transition min-h-[38px]">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Export CSV (Excel)</span>
                    </a>

                    <a href="{{ route('laporan.index') }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition min-h-[38px]">
                        <span>Pusat Laporan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Compact Summary Bar (Document-Oriented) -->
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 sm:p-3 mb-4 overflow-x-auto">
            <div class="flex items-center divide-x divide-slate-200 min-w-max sm:min-w-0">
                <div class="flex-1 px-3 sm:px-4 first:pl-1 sm:first:pl-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Inspeksi</span>
                    <span class="text-base sm:text-lg font-bold text-slate-900 tabular-nums">
                        {{ $rekap['total'] }}
                        <span class="text-xs font-normal text-slate-500">kali</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Hasil Baik</span>
                    <span class="text-base sm:text-lg font-bold text-emerald-800 tabular-nums">
                        {{ $rekap['baik'] }}
                        <span class="text-xs font-normal text-slate-500">layak pakai</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Perlu Tindakan</span>
                    <span class="text-base sm:text-lg font-bold text-rose-800 tabular-nums">
                        {{ $rekap['perlu_tindakan'] }}
                        <span class="text-xs font-normal text-slate-500">ada temuan</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Jadwal Rutin</span>
                    <span class="text-base sm:text-lg font-bold text-slate-800 tabular-nums">
                        {{ $rekap['terjadwal'] }}
                        <span class="text-xs font-normal text-slate-500">kegiatan</span>
                    </span>
                </div>

                <div class="flex-1 px-3 sm:px-4 last:pr-1 sm:last:pr-2">
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pemeriksaan Mandiri</span>
                    <span class="text-base sm:text-lg font-bold text-slate-800 tabular-nums">
                        {{ $rekap['mandiri'] }}
                        <span class="text-xs font-normal text-slate-500">ad-hoc</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Compact Filter Form (Section 4) -->
        <div class="mb-4 p-3 sm:p-3.5 rounded-lg border border-slate-200 bg-white">
            <form method="GET" action="{{ route('laporan.pemeriksaan') }}" class="space-y-2.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                    <!-- Dari Tanggal -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                        <input
                            type="date"
                            name="dari_tanggal"
                            value="{{ request('dari_tanggal') }}"
                            class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-slate-400 min-h-[40px]" />
                    </div>

                    <!-- Sampai Tanggal -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                        <input
                            type="date"
                            name="sampai_tanggal"
                            value="{{ request('sampai_tanggal') }}"
                            class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-slate-400 min-h-[40px]" />
                    </div>

                    <!-- Gedung -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Gedung</label>
                        <select name="gedung_id" onchange="this.form.submit()" class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none min-h-[40px]">
                            <option value="">Semua Gedung</option>
                            @foreach($gedungs as $g)
                                <option value="{{ $g->id }}" {{ request('gedung_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lantai -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Lantai</label>
                        <select name="lantai_id" onchange="this.form.submit()" class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none min-h-[40px]">
                            <option value="">Semua Lantai</option>
                            @foreach($lantais as $l)
                                <option value="{{ $l->id }}" {{ request('lantai_id') == $l->id ? 'selected' : '' }}>
                                    Lt. {{ $l->nomor_lantai }} - {{ $l->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hasil Pemeriksaan -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Hasil Pemeriksaan</label>
                        <select name="hasil" class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none min-h-[40px]">
                            <option value="">Semua Hasil</option>
                            <option value="baik" {{ request('hasil') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="perlu_tindakan" {{ request('hasil') == 'perlu_tindakan' ? 'selected' : '' }}>Perlu Tindakan</option>
                        </select>
                    </div>

                    <!-- Jadwal Inspeksi -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jadwal Inspeksi</label>
                        <select name="jadwal_id" class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none min-h-[40px]">
                            <option value="">Semua Sumber Jadwal</option>
                            <option value="mandiri" {{ request('jadwal_id') == 'mandiri' ? 'selected' : '' }}>Pemeriksaan Mandiri (Ad-Hoc)</option>
                            @foreach($jadwals as $jadwal)
                                <option value="{{ $jadwal->id }}" {{ request('jadwal_id') == $jadwal->id ? 'selected' : '' }}>
                                    {{ Str::limit($jadwal->nama, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Petugas Pemeriksa -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Pemeriksa</label>
                        <input
                            type="text"
                            name="pemeriksa"
                            value="{{ request('pemeriksa') }}"
                            placeholder="Ketik nama pemeriksa..."
                            class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-slate-400 min-h-[40px]" />
                    </div>

                    <!-- Kata Kunci Pencarian -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Cari Kode / Catatan</label>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Ketik kode APAR..."
                            class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-md focus:bg-white focus:outline-none focus:border-slate-400 min-h-[40px]" />
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold rounded-md text-white bg-slate-900 hover:bg-slate-800 transition min-h-[38px] cursor-pointer">
                            <span>Terapkan Filter</span>
                        </button>

                        <a href="{{ route('laporan.pemeriksaan') }}" 
                           class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md text-slate-600 bg-slate-100 hover:bg-slate-200 transition min-h-[38px]">
                            <span>Reset Filter</span>
                        </a>

                        <span class="text-slate-500 text-xs hidden sm:inline-block ml-2">
                            Menampilkan <strong class="text-slate-800 font-semibold">{{ $rekap['terfilter'] }}</strong> pemeriksaan dari filter yang diterapkan
                        </span>
                    </div>

                    <span class="text-slate-500 text-xs sm:hidden">
                        Menampilkan <strong class="text-slate-800 font-semibold">{{ $rekap['terfilter'] }}</strong> pemeriksaan dari filter yang diterapkan
                    </span>
                </div>
            </form>
        </div>

        <!-- Table Container (Horizontal Scroll for Responsive) -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-600 font-bold">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Kode APAR</th>
                            <th class="py-3 px-3.5">Lokasi</th>
                            <th class="py-3 px-3.5">Jadwal</th>
                            <th class="py-3 px-3.5">Pemeriksa</th>
                            <th class="py-3 px-3.5">Hasil</th>
                            <th class="py-3 px-3.5">Catatan</th>
                            <th class="py-3 px-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pemeriksaans as $idx => $p)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-3.5 text-center font-mono text-slate-400">
                                    {{ $pemeriksaans->firstItem() + $idx }}
                                </td>
                                <td class="py-3 px-3.5 font-mono whitespace-nowrap">
                                    <span class="font-bold text-slate-900">{{ $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d/m/Y') : '-' }}</span>
                                    <div class="text-[10px] text-slate-400">{{ $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('H:i') : '' }} WIB</div>
                                </td>
                                <td class="py-3 px-3.5 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    {{ $p->apar?->kode ?? '-' }}
                                    <div class="text-[10px] font-normal text-slate-500">{{ $p->apar?->jenis_media ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $p->apar?->gedung?->nama ?? '-' }} - Lt. {{ $p->apar?->lantai?->nomor_lantai ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $p->apar?->ruangan?->nama ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3.5">
                                    @if($p->jadwal)
                                        <span class="font-medium text-slate-800">{{ $p->jadwal->nama }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                            Mandiri
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-medium text-slate-900">{{ $p->pemeriksa_nama }}</div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if($p->hasil_keseluruhan === 'baik')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Baik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                            Perlu Tindakan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 max-w-xs">
                                    <p class="truncate text-slate-600" title="{{ $p->catatan }}">
                                        {{ $p->catatan ?: '-' }}
                                    </p>
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <a href="{{ route('pemeriksaan.show', $p->id) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition min-h-[32px]">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-medium">Belum ada data untuk filter yang dipilih.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan tanggal atau pilihan filter lainnya.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pemeriksaans->hasPages())
                <div class="p-3.5 border-t border-slate-100">
                    {{ $pemeriksaans->links() }}
                </div>
            @endif
        </div>
    </x-layouts.app>
@endif
