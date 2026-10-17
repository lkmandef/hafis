<x-layouts.app title="Pengguna & Akses - HAFIS">
    <!-- Header Halaman -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center p-1.5 rounded-lg bg-slate-900 text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </span>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Pengguna & Akses</h1>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Kelola akun petugas, peran pengguna, dan kendali akses operasional sistem K3RS.
                </p>
            </div>

            <!-- Tombol Aksi Tambah -->
            <div>
                <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <span>Tambah Akun Petugas</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Notifikasi Sukses / Galat -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-xs flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Kartu Statistik Pengguna -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Pengguna</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold text-slate-900">{{ $stats['total'] }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Akun terdaftar</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Kepala K3RS (Admin)</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold text-slate-900">{{ $stats['admin'] }}</span>
                <span class="text-[10px] text-slate-500 font-medium">Akses penuh</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Petugas Lapangan</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold text-sky-700">{{ $stats['petugas'] }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Operasional</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Status Aktif</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold text-emerald-700">{{ $stats['aktif'] }}</span>
                @if($stats['nonaktif'] > 0)
                    <span class="text-[10px] text-rose-600 font-medium">{{ $stats['nonaktif'] }} nonaktif</span>
                @else
                    <span class="text-[10px] text-emerald-600 font-medium">Semua aktif</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter dan Pencarian -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 mb-6 shadow-2xs">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Pencarian Teks -->
            <div class="sm:col-span-6 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari nama, username, atau email..."
                    class="w-full pl-9 pr-3 py-2 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none min-h-[38px]" />
            </div>

            <!-- Filter Peran -->
            <div class="sm:col-span-3">
                <select name="role" class="w-full px-3 py-2 text-xs text-slate-900 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none min-h-[38px]">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Kepala K3RS (Admin)</option>
                    <option value="petugas" {{ $roleFilter === 'petugas' ? 'selected' : '' }}>Petugas K3RS</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-3 flex items-center gap-2">
                <select name="status" class="w-full px-3 py-2 text-xs text-slate-900 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none min-h-[38px]">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold rounded-lg border border-slate-200 min-h-[38px] shrink-0 transition-colors">
                    Filter
                </button>
                @if($search || $roleFilter || $statusFilter)
                    <a href="{{ route('users.index') }}" class="p-2 text-slate-500 hover:text-slate-800 text-xs shrink-0" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Pengguna -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/75 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Kontak / Email</th>
                        <th class="py-3 px-4">Peran</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === auth()->id())
                                                <span class="text-[10px] px-1.5 py-0.5 rounded-sm bg-sky-100 text-sky-800 font-medium">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-mono text-slate-500">@<span>{{ $u->username }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-600">{{ $u->email ?: 'Tidak ada email' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->isAdmin())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-900 text-white">
                                        Kepala K3RS (Admin)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                        Petugas K3RS
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->isActive())
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('users.edit', $u->id) }}" class="p-1.5 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 min-h-[34px] min-w-[34px] inline-flex items-center justify-center transition-colors" title="Edit Akun & Reset Kata Sandi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.toggle-status', $u->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin {{ $u->isActive() ? 'menonaktifkan' : 'mengaktifkan kembali' }} akun {{ $u->name }}?');" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-md {{ $u->isActive() ? 'text-amber-600 hover:bg-amber-50 border-amber-200' : 'text-emerald-600 hover:bg-emerald-50 border-emerald-200' }} border min-h-[34px] min-w-[34px] inline-flex items-center justify-center transition-colors cursor-pointer" title="{{ $u->isActive() ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                @if($u->isActive())
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                Tidak ada data pengguna yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
