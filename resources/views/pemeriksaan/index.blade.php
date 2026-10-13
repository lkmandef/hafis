<x-layouts.app title="HAFIS : Ikhtisar Pemeriksaan Bulanan APAR">
    <!-- Header -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-800 transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Pemeriksaan Bulanan</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Pemeriksaan Bulanan APAR
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Kartu kendali dan pemantauan inspeksi fisik 173 unit APAR operasional K3RS per bulan kalender.
                </p>
            </div>

            <!-- Month & Year Selector & Scan Action Form -->
            <div class="flex flex-wrap items-center gap-2">
                <form action="{{ route('pemeriksaan.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    @if($search)
                        <input type="hidden" name="q" value="{{ $search }}">
                    @endif
                    @if($lokasiId)
                        <input type="hidden" name="lokasi_id" value="{{ $lokasiId }}">
                    @endif
                    @if($statusPeriksa)
                        <input type="hidden" name="status_periksa" value="{{ $statusPeriksa }}">
                    @endif

                    <select name="bulan" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 rounded-lg border border-slate-200 bg-white shadow-2xs focus:border-slate-400 focus:outline-none min-h-[40px]">
                        @foreach(\App\Models\Pemeriksaan::NAMA_BULAN as $num => $nama)
                            <option value="{{ $num }}" {{ $bulan === $num ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>

                    <select name="tahun" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 rounded-lg border border-slate-200 bg-white shadow-2xs focus:border-slate-400 focus:outline-none min-h-[40px]">
                        @for($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>

                    <noscript>
                        <button type="submit" class="px-3 py-2 text-xs font-bold bg-slate-900 text-white rounded-lg">Pilih</button>
                    </noscript>
                </form>

                <!-- Scan QR Quick Action (Tahap 4) -->
                <a href="{{ route('scan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition shadow-2xs min-h-[40px]" 
                   title="Scan QR Code APAR untuk periode ini">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Scan APAR</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">&check;</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm leading-none" aria-label="Tutup">&times;</button>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 p-4 rounded-xl border border-sky-200 bg-sky-50 text-sky-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
            <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-sky-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">i</span>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-sky-700 hover:text-sky-900 font-bold text-sm leading-none" aria-label="Tutup">&times;</button>
        </div>
    @endif

    <!-- Summary Metrics Card -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-card class="!p-4">
            <span class="block text-slate-500 text-xs font-medium">Target Periode</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-xl sm:text-2xl font-black text-slate-900">{{ \App\Models\Pemeriksaan::NAMA_BULAN[$bulan] }} {{ $tahun }}</span>
            </div>
            <span class="block text-[11px] text-slate-400 mt-0.5">Bulan kalender operasional</span>
        </x-card>

        <x-card class="!p-4">
            <span class="block text-slate-500 text-xs font-medium">Sudah Diperiksa</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-2xl font-black text-emerald-600 tabular-nums">{{ $totalSudah }}</span>
                <span class="text-xs text-slate-400 font-medium">/ {{ $totalWajib }} tabung</span>
            </div>
            <div class="flex items-center gap-2 mt-0.5 text-[11px]">
                <span class="text-emerald-600 font-semibold">{{ $persentaseKepatuhan }}% Selesai</span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-500">Baik: <strong class="text-emerald-700 font-mono">{{ $totalBaik }}</strong> | Tindakan: <strong class="text-rose-700 font-mono">{{ $totalPerluTindakan }}</strong></span>
            </div>
        </x-card>

        <x-card class="!p-4">
            <span class="block text-slate-500 text-xs font-medium">Belum Diperiksa</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-2xl font-black text-amber-600 tabular-nums">{{ $totalBelum }}</span>
                <span class="text-xs text-slate-400 font-medium">tabung tersisa</span>
            </div>
            <span class="block text-[11px] text-slate-400 mt-0.5">Wajib selesai di bulan berjalan</span>
        </x-card>

        <x-card class="!p-4">
            <span class="block text-slate-500 text-xs font-medium">Total Terpasang</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-900 tabular-nums">{{ $totalWajib }}</span>
                <span class="text-xs text-slate-400 font-medium">unit aktif</span>
            </div>
            <span class="block text-[11px] text-slate-400 mt-0.5">59 lokasi titik operasional</span>
        </x-card>
    </div>

    <!-- Filters & Table Section -->
    <x-card class="!p-0 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <!-- Filter search & status -->
            <form action="{{ route('pemeriksaan.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                <div class="relative min-w-[200px] flex-1 max-w-xs">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode APAR / lokasi..." class="w-full text-xs pl-8 pr-3 py-2 rounded-lg border border-slate-200 bg-white placeholder-slate-400 focus:border-slate-400 focus:outline-none transition min-h-[38px]">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <select name="lokasi_id" onchange="this.form.submit()" class="text-xs px-2.5 py-2 rounded-lg border border-slate-200 bg-white focus:border-slate-400 focus:outline-none min-h-[38px] max-w-[200px] truncate">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasis as $loc)
                        <option value="{{ $loc->id }}" {{ (string)$lokasiId === (string)$loc->id ? 'selected' : '' }}>{{ $loc->nama }}</option>
                    @endforeach
                </select>

                <select name="status_periksa" onchange="this.form.submit()" class="text-xs px-2.5 py-2 rounded-lg border border-slate-200 bg-white focus:border-slate-400 focus:outline-none min-h-[38px]">
                    <option value="">Semua Status</option>
                    <option value="sudah" {{ $statusPeriksa === 'sudah' ? 'selected' : '' }}>Semua Sudah Diperiksa ({{ $totalSudah }})</option>
                    <option value="baik" {{ $statusPeriksa === 'baik' ? 'selected' : '' }}>Sudah - Kondisi Baik ({{ $totalBaik }})</option>
                    <option value="perlu_tindakan" {{ $statusPeriksa === 'perlu_tindakan' ? 'selected' : '' }}>Sudah - Perlu Tindakan ({{ $totalPerluTindakan }})</option>
                    <option value="belum" {{ $statusPeriksa === 'belum' ? 'selected' : '' }}>Belum Diperiksa ({{ $totalBelum }})</option>
                </select>

                @if($search || $lokasiId || $statusPeriksa)
                    <a href="{{ route('pemeriksaan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="text-xs text-rose-600 hover:text-rose-800 font-medium px-2 py-1">Reset</a>
                @endif
            </form>

            <span class="text-xs text-slate-500 font-medium whitespace-nowrap">
                Menampilkan {{ $apars->count() }} unit
            </span>
        </div>

        <!-- Master APAR Monthly Checklist Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Kode APAR</th>
                        <th class="py-3 px-4">Lokasi K3RS</th>
                        <th class="py-3 px-4">Media / Kapasitas</th>
                        <th class="py-3 px-4">Status Tabung</th>
                        <th class="py-3 px-4 text-center">Status Periode {{ \App\Models\Pemeriksaan::NAMA_BULAN[$bulan] }}</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($apars as $index => $apar)
                        @php
                            $pemBulanIni = $apar->pemeriksaans->first();
                            $sudahDiperiksa = !is_null($pemBulanIni);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('apar.show', $apar->id) }}" class="hover:underline hover:text-sky-700">
                                    {{ $apar->kode }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                <span class="font-semibold block text-slate-900">{{ $apar->lokasi?->nama ?? $apar->ruangan?->nama ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400">
                                    @if($apar->lokasi)
                                        {{ $apar->lokasi->keterangan ?? $apar->lokasi->nama }}
                                    @else
                                        {{ $apar->ruangan?->nama ?? '-' }}
                                    @endif
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                <span>{{ $apar->jenis_media }}</span>
                                <span class="block text-[11px] font-mono text-slate-400">{{ $apar->kapasitas }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <x-badge :variant="$apar->status_badge_variant" :dot="true">
                                    {{ $apar->status_label }}
                                </x-badge>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($sudahDiperiksa)
                                    @if($pemBulanIni->hasil_keseluruhan === \App\Models\Pemeriksaan::HASIL_BAIK)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Kondisi Baik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Perlu Tindakan
                                        </span>
                                    @endif
                                    <span class="block text-[10px] text-slate-400 mt-0.5 font-mono">
                                        {{ $pemBulanIni->tanggal_pemeriksaan?->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Belum Diperiksa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                @if($sudahDiperiksa)
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('pemeriksaan.show', $pemBulanIni->id) }}" class="px-2.5 py-1 text-[11px] font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition shadow-2xs">
                                            Lihat Hasil
                                        </a>
                                        <a href="{{ route('pemeriksaan.edit', $pemBulanIni->id) }}" class="px-2.5 py-1 text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-lg hover:bg-sky-100 transition shadow-2xs">
                                            Koreksi
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('pemeriksaan.create', ['apar_id' => $apar->id, 'periode_bulan' => $bulan, 'periode_tahun' => $tahun]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition shadow-2xs">
                                        <span>Periksa</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada data unit APAR yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
