<x-layouts.app title="Pengguna & Akses - Pengaturan Sistem - HAFIS">
    <!-- Header Halaman & Tab Navigasi Pengaturan -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center p-1.5 rounded-lg bg-[#111827] text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </span>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Pengaturan & Akses</h1>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Konfigurasi sistem HAFIS: manajemen akun petugas dan parameter checklist pemeriksaan fisik APAR.
                </p>
            </div>

            <!-- Tombol Aksi Tambah -->
            <div>
                <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#111827] hover:bg-gray-800 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <span>Tambah Akun Petugas</span>
                </a>
            </div>
        </div>

        <!-- Tab Navigasi Pengaturan -->
        <div class="flex items-center gap-2 border-b border-gray-200">
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold border-b-2 border-gray-900 text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Pengguna & Akses</span>
            </a>
            <a href="{{ route('pengaturan.checklist.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>Checklist Pemeriksaan</span>
            </a>
        </div>
    </div>

    <!-- Notifikasi Sukses / Galat -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50/70 text-emerald-900 text-xs flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl border border-rose-200 bg-rose-50/70 text-rose-900 text-xs flex items-center justify-between gap-3 shadow-xs">
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
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Pengguna</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold font-mono text-gray-900 tabular-nums">{{ $stats['total'] }}</span>
                <span class="text-[10px] text-gray-400 font-medium">Akun terdaftar</span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Kepala K3RS (Admin)</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold font-mono text-gray-900 tabular-nums">{{ $stats['admin'] }}</span>
                <span class="text-[10px] text-gray-500 font-medium">Akses penuh</span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Petugas Lapangan</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold font-mono text-gray-900 tabular-nums">{{ $stats['petugas'] }}</span>
                <span class="text-[10px] text-gray-400 font-medium">Operasional</span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Status Aktif</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-xl sm:text-2xl font-bold font-mono text-emerald-700 tabular-nums">{{ $stats['aktif'] }}</span>
                @if($stats['nonaktif'] > 0)
                    <span class="text-[10px] text-rose-600 font-medium font-mono">{{ $stats['nonaktif'] }} nonaktif</span>
                @else
                    <span class="text-[10px] text-emerald-600 font-medium">Semua aktif</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter dan Pencarian -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6 shadow-xs">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Pencarian Teks -->
            <div class="sm:col-span-6 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari nama, username, atau email..."
                    class="w-full pl-9 pr-3 py-2 text-xs text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none min-h-[42px]" />
            </div>

            <!-- Filter Peran -->
            <div class="sm:col-span-3">
                <select name="role" class="w-full px-3 py-2 text-xs text-gray-900 bg-white border border-gray-200 rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none min-h-[42px]">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Kepala K3RS (Admin)</option>
                    <option value="petugas" {{ $roleFilter === 'petugas' ? 'selected' : '' }}>Petugas K3RS</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-3 flex items-center gap-2">
                <select name="status" class="w-full px-3 py-2 text-xs text-gray-900 bg-white border border-gray-200 rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 focus:outline-none min-h-[42px]">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold rounded-lg border border-gray-200 min-h-[42px] shrink-0 transition-colors cursor-pointer">
                    Filter
                </button>
                @if($search || $roleFilter || $statusFilter)
                    <a href="{{ route('users.index') }}" class="px-2 py-2 text-gray-500 hover:text-gray-900 text-xs shrink-0 font-medium transition-colors" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Pengguna -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs">
        <!-- Mobile Card Reflow (sm:hidden) -->
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse($users as $u)
                <div class="p-4 space-y-3 bg-white">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#111827] text-white font-bold font-mono text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-xs flex items-center gap-1.5">
                                    <span>{{ $u->name }}</span>
                                    @if($u->id === auth()->id())
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-800 border border-gray-200 font-medium">Anda</span>
                                    @endif
                                </div>
                                <div class="text-[11px] font-mono text-gray-500">@<span>{{ $u->username }}</span></div>
                            </div>
                        </div>
                        <div>
                            @if($u->isActive())
                                <x-badge variant="success" size="sm">Aktif</x-badge>
                            @else
                                <x-badge variant="neutral" size="sm">Nonaktif</x-badge>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 p-2 rounded-lg border border-gray-100">
                        <div class="text-[11px] font-mono text-gray-600 truncate mr-2">
                            {{ $u->email ?: 'Tidak ada email' }}
                        </div>
                        <div>
                            @if($u->isAdmin())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#111827] text-white">
                                    Admin K3RS
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    Petugas
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-1 flex items-center gap-2">
                        <a href="{{ route('users.edit', $u->id) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 text-xs font-semibold shadow-2xs min-h-[40px] transition-colors">
                            Edit Akun
                        </a>
                        @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.toggle-status', $u->id) }}"
                                  data-confirm
                                  data-confirm-title="{{ $u->isActive() ? 'Nonaktifkan Akun Pengguna' : 'Aktifkan Kembali Akun' }}"
                                  data-confirm-message="Apakah Anda yakin ingin {{ $u->isActive() ? 'menonaktifkan' : 'mengaktifkan kembali' }} hak akses akun {{ $u->name }}?"
                                  data-confirm-type="{{ $u->isActive() ? 'warning' : 'success' }}"
                                  data-confirm-text="{{ $u->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}"
                                  class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-2 rounded-lg {{ $u->isActive() ? 'text-amber-700 hover:bg-amber-50 border-amber-200 bg-amber-50/50' : 'text-emerald-700 hover:bg-emerald-50 border-emerald-200 bg-emerald-50/50' }} border min-h-[40px] inline-flex items-center justify-center text-xs font-semibold transition-colors cursor-pointer">
                                    {{ $u->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white">
                    <p class="text-xs text-gray-500">Tidak ada data pengguna.</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table (hidden sm:block) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs min-w-[720px]">
                <thead>
                    <tr class="border-b border-gray-200 bg-[#f8f9fa] text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3 pl-5 sm:pl-6 pr-4">Pengguna</th>
                        <th class="py-3 px-4">Kontak / Email</th>
                        <th class="py-3 px-4">Peran</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 pl-4 pr-5 sm:pr-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 pl-5 sm:pl-6 pr-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#111827] text-white font-bold font-mono text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 flex items-center gap-1.5">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === auth()->id())
                                                <span class="text-[10px] px-1.5 py-0.5 rounded-sm bg-gray-100 text-gray-800 border border-gray-200 font-medium">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-mono text-gray-500">@<span>{{ $u->username }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-gray-600 font-mono text-[11px]">{{ $u->email ?: 'Tidak ada email' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->isAdmin())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#111827] text-white">
                                        Kepala K3RS (Admin)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                        Petugas K3RS
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->isActive())
                                    <x-badge variant="success" size="sm">Aktif</x-badge>
                                @else
                                    <x-badge variant="neutral" size="sm">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="py-3.5 pl-4 pr-5 sm:pr-6 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('users.edit', $u->id) }}" class="p-1.5 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 border border-gray-200 min-h-[36px] min-w-[36px] inline-flex items-center justify-center transition-colors" title="Edit Akun & Reset Kata Sandi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.toggle-status', $u->id) }}"
                                              data-confirm
                                              data-confirm-title="{{ $u->isActive() ? 'Nonaktifkan Akun Pengguna' : 'Aktifkan Kembali Akun' }}"
                                              data-confirm-message="Apakah Anda yakin ingin {{ $u->isActive() ? 'menonaktifkan' : 'mengaktifkan kembali' }} hak akses akun {{ $u->name }}?"
                                              data-confirm-type="{{ $u->isActive() ? 'warning' : 'success' }}"
                                              data-confirm-text="{{ $u->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}"
                                              class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg {{ $u->isActive() ? 'text-amber-600 hover:bg-amber-50 border-amber-200' : 'text-emerald-600 hover:bg-emerald-50 border-emerald-200' }} border min-h-[36px] min-w-[36px] inline-flex items-center justify-center transition-colors cursor-pointer" title="{{ $u->isActive() ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
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
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                Tidak ada data pengguna yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-gray-200 bg-[#f8f9fa]">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
