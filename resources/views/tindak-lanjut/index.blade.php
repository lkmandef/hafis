<x-layouts.app title="HAFIS : Tindak Lanjut & Pemeliharaan APAR">
    <!-- 1. Header with Breadcrumb & Statistics -->
    <div class="pb-5 mb-6 border-b border-slate-200">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2 font-medium" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-slate-800 transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Tindak Lanjut & Pemeliharaan</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Tindak Lanjut & Pemeliharaan APAR
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Daftar penanganan perbaikan dan riwayat pemeliharaan berkala unit APAR rumah sakit.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('apar.index') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>Master Data APAR</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs" role="alert">
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

    <!-- 2. Ringkasan Status Tab / Statistik -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="p-3.5 rounded-xl border border-slate-200 bg-white shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Tindak Lanjut</span>
            <span class="font-mono text-xl sm:text-2xl font-extrabold text-slate-900 mt-1 block tabular-nums">
                {{ $stats['total'] }}
            </span>
        </div>

        <div class="p-3.5 rounded-xl border border-amber-200 bg-amber-50/60 shadow-2xs">
            <span class="text-[11px] font-semibold text-amber-800 uppercase tracking-wider block">Belum Ditindaklanjuti</span>
            <span class="font-mono text-xl sm:text-2xl font-extrabold text-amber-900 mt-1 block tabular-nums">
                {{ $stats['belum'] }}
            </span>
        </div>

        <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/60 shadow-2xs">
            <span class="text-[11px] font-semibold text-blue-800 uppercase tracking-wider block">Dalam Proses</span>
            <span class="font-mono text-xl sm:text-2xl font-extrabold text-blue-900 mt-1 block tabular-nums">
                {{ $stats['proses'] }}
            </span>
        </div>

        <div class="p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/60 shadow-2xs">
            <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider block">Tuntas Selesai</span>
            <span class="font-mono text-xl sm:text-2xl font-extrabold text-emerald-900 mt-1 block tabular-nums">
                {{ $stats['selesai'] }}
            </span>
        </div>
    </div>

    <!-- 3. Form Filter & Pencarian -->
    <div class="p-4 bg-white border border-slate-200 rounded-xl mb-6 shadow-2xs">
        <form action="{{ route('tindak-lanjut.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
            <div class="sm:col-span-5">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari kode APAR, uraian, pelaksana, catatan..."
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 min-h-[44px]">
            </div>

            <div class="sm:col-span-3">
                <select name="status" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 min-h-[44px]">
                    <option value="">-- Semua Status --</option>
                    <option value="belum_ditindaklanjuti" {{ request('status') === 'belum_ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                    <option value="dalam_proses" {{ request('status') === 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <select name="jenis" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 min-h-[44px]">
                    <option value="">-- Semua Jenis Tindakan --</option>
                    <option value="perbaikan" {{ request('jenis') === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    <option value="pengisian_ulang" {{ request('jenis') === 'pengisian_ulang' ? 'selected' : '' }}>Pengisian Ulang</option>
                    <option value="pembersihan" {{ request('jenis') === 'pembersihan' ? 'selected' : '' }}>Pembersihan</option>
                    <option value="penggantian_komponen" {{ request('jenis') === 'penggantian_komponen' ? 'selected' : '' }}>Penggantian Komponen</option>
                    <option value="pemeriksaan_ulang" {{ request('jenis') === 'pemeriksaan_ulang' ? 'selected' : '' }}>Pemeriksaan Ulang</option>
                    <option value="lainnya" {{ request('jenis') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="sm:col-span-1 flex items-center gap-1.5">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-2xs min-h-[44px]">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- 4. Tabel Daftar Riwayat Tindak Lanjut -->
    <x-card title="Riwayat Seluruh Tindak Lanjut & Pemeliharaan" subtitle="Daftar pekerjaan perbaikan unit tabung APAR yang tercatat dalam sistem.">
        <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <th class="px-4 py-3 font-bold whitespace-nowrap">Tanggal Tindakan</th>
                        <th class="px-4 py-3 font-bold">Unit APAR & Lokasi</th>
                        <th class="px-4 py-3 font-bold">Temuan Masalah</th>
                        <th class="px-4 py-3 font-bold whitespace-nowrap">Jenis Tindakan</th>
                        <th class="px-4 py-3 font-bold text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 font-bold whitespace-nowrap">Pelaksana</th>
                        <th class="px-4 py-3 font-bold text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                    @forelse($tindakLanjuts as $tl)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-slate-900 whitespace-nowrap tabular-nums">
                                {{ $tl->tanggal_tindakan->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('apar.show', $tl->apar_id) }}" class="font-mono font-bold text-slate-900 hover:underline">
                                    {{ $tl->apar->kode }}
                                </a>
                                <span class="text-slate-500 block text-[11px] mt-0.5 truncate max-w-xs">
                                    {{ $tl->apar->lokasi_lengkap }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <span class="font-medium text-slate-900 block truncate" title="{{ $tl->temuan_teks }}">
                                    {{ $tl->temuan_teks }}
                                </span>
                                <span class="text-slate-500 block text-[11px] truncate mt-0.5" title="{{ $tl->uraian_tindakan }}">
                                    Tindakan: {{ $tl->uraian_tindakan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-semibold text-slate-800">{{ $tl->jenis_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-badge :variant="$tl->status_badge_variant" :dot="true">
                                    {{ $tl->status_label }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-800">
                                {{ $tl->petugas_pelaksana }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('tindak-lanjut.show', $tl->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-2xs min-h-[36px]">
                                        <span>Detail</span>
                                    </a>
                                    @if($tl->status !== 'selesai')
                                        <a href="{{ route('tindak-lanjut.edit', $tl->id) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition min-h-[36px]">
                                            <span>Update</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </div>
                                    @if(request('q') || request('status') || request('jenis'))
                                        <h4 class="text-sm font-bold text-slate-800">Tidak ada catatan yang cocok</h4>
                                        <p class="text-xs text-slate-500 mt-1 mb-4">
                                            Tidak ditemukan data tindak lanjut dengan parameter pencarian atau filter yang Anda pilih.
                                        </p>
                                        <a href="{{ route('tindak-lanjut.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition min-h-[44px]">
                                            Reset Filter
                                        </a>
                                    @else
                                        <h4 class="text-sm font-bold text-slate-800">Belum ada catatan tindak lanjut</h4>
                                        <p class="text-xs text-slate-500 mt-1">
                                            Catatan tindak lanjut dan pemeliharaan atas temuan pemeriksaan fisik APAR akan ditampilkan di sini.
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tindakLanjuts->hasPages())
            <div class="mt-4 pt-3 border-t border-slate-100">
                {{ $tindakLanjuts->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
