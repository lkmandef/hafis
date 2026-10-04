<x-layouts.app title="HAFIS : Riwayat Penggantian Unit APAR">
    <!-- Header with Breadcrumb & Action -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ route('apar.index') }}" class="hover:text-slate-800 transition-colors">Master Data APAR</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Riwayat Penggantian APAR</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Riwayat Penggantian Unit APAR
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Log audit dan penelusuran pergantian unit tabung pemadam api rumah sakit.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('apar.index') }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>Master Data APAR</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Sukses / Info -->
    @if(session('success'))
        <div class="mb-6 p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
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

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Pergantian</span>
            <span class="font-mono text-2xl font-extrabold text-slate-900 tabular-nums">{{ $stats['total'] }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">Peristiwa tercatat</span>
        </div>

        <div class="p-4 rounded-xl border border-rose-200 bg-rose-50/40 shadow-2xs">
            <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider block mb-1">Akibat Kerusakan</span>
            <span class="font-mono text-2xl font-extrabold text-rose-900 tabular-nums">{{ $stats['rusak'] }}</span>
            <span class="text-[11px] text-rose-600 block mt-1">Tabung rusak fisik</span>
        </div>

        <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/40 shadow-2xs">
            <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider block mb-1">Kadaluarsa Media</span>
            <span class="font-mono text-2xl font-extrabold text-amber-900 tabular-nums">{{ $stats['kadaluarsa'] }}</span>
            <span class="text-[11px] text-amber-600 block mt-1">Lewat batas pakai</span>
        </div>

        <div class="p-4 rounded-xl border border-sky-200 bg-sky-50/40 shadow-2xs">
            <span class="text-[11px] font-semibold text-sky-700 uppercase tracking-wider block mb-1">Peremajaan Unit</span>
            <span class="font-mono text-2xl font-extrabold text-sky-900 tabular-nums">{{ $stats['peremajaan'] }}</span>
            <span class="text-[11px] text-sky-600 block mt-1">SOP pergantian unit</span>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="mb-6 p-4 rounded-xl border border-slate-200 bg-white shadow-2xs">
        <form method="GET" action="{{ route('penggantian.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari kode APAR lama, kode baru, petugas, atau alasan..."
                    class="w-full pl-9 pr-3 py-2 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none transition min-h-[44px]" />
            </div>

            <div class="relative min-w-[200px]">
                <select 
                    name="alasan" 
                    onchange="this.form.submit()"
                    class="appearance-none w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-lg pl-3 pr-8 py-2 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none min-h-[44px] cursor-pointer">
                    <option value="">Semua Alasan</option>
                    @foreach(\App\Models\PenggantianApar::ALASAN_LABELS as $key => $lbl)
                        <option value="{{ $key }}" {{ request('alasan') === $key ? 'selected' : '' }}>
                            {{ $lbl }}
                        </option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition shadow-2xs min-h-[44px] cursor-pointer">
                    <span>Cari</span>
                </button>

                @if(request()->hasAny(['q', 'alasan']))
                    <a href="{{ route('penggantian.index') }}" 
                       class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition min-h-[44px]">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <x-card title="Daftar Peristiwa Penggantian APAR" subtitle="Menampilkan keterhubungan permanen antara unit lama dengan unit baru pengganti.">
        <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <th class="px-4 py-3 font-bold whitespace-nowrap">Tanggal</th>
                        <th class="px-4 py-3 font-bold">APAR Lama (Dinonaktifkan)</th>
                        <th class="px-4 py-3 font-bold">APAR Baru (Aktif)</th>
                        <th class="px-4 py-3 font-bold">Alasan Penggantian</th>
                        <th class="px-4 py-3 font-bold">Lokasi Penempatan</th>
                        <th class="px-4 py-3 font-bold whitespace-nowrap">Petugas</th>
                        <th class="px-4 py-3 font-bold text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                    @forelse($penggantians as $p)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-slate-900 whitespace-nowrap tabular-nums">
                                {{ $p->tanggal_penggantian->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($p->aparLama)
                                    <div class="space-y-0.5">
                                        <a href="{{ route('apar.show', $p->aparLama->id) }}" class="font-mono font-bold text-slate-900 hover:underline">
                                            {{ $p->aparLama->kode }}
                                        </a>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] text-slate-500">{{ $p->aparLama->jenis_media }}</span>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200">Nonaktif</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Unit lama terhapus</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($p->aparBaru)
                                    <div class="space-y-0.5">
                                        <a href="{{ route('apar.show', $p->aparBaru->id) }}" class="font-mono font-bold text-slate-900 hover:underline">
                                            {{ $p->aparBaru->kode }}
                                        </a>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] text-slate-500">{{ $p->aparBaru->jenis_media }}</span>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Aktif</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Unit baru</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-900 block">{{ $p->alasan_label }}</span>
                                @if($p->alasan_keterangan)
                                    <span class="text-slate-500 text-[11px] block truncate max-w-xs">{{ $p->alasan_keterangan }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 max-w-xs truncate">
                                {{ $p->aparBaru?->lokasi_lengkap ?? $p->aparLama?->lokasi_lengkap ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-800">
                                {{ $p->petugas_nama }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('penggantian.show', $p->id) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[36px]">
                                    <span>Detail Pergantian</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </div>
                                    @if(request('q') || request('alasan'))
                                        <h4 class="text-sm font-bold text-slate-800">Tidak ada riwayat yang cocok</h4>
                                        <p class="text-xs text-slate-500 mt-1 mb-4">
                                            Tidak ditemukan data penggantian unit dengan filter atau pencarian saat ini.
                                        </p>
                                        <a href="{{ route('penggantian.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition min-h-[44px]">
                                            Reset Filter
                                        </a>
                                    @else
                                        <h4 class="text-sm font-bold text-slate-800">Belum ada riwayat penggantian APAR</h4>
                                        <p class="text-xs text-slate-500 mt-1">
                                            Riwayat pergantian unit lama ke unit baru akan tercatat secara otomatis di sini saat dilakukan penggantian.
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penggantians->hasPages())
            <div class="pt-4 border-t border-slate-100">
                {{ $penggantians->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
