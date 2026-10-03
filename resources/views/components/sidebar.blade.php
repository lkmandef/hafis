@props([
'active' => 'dashboard'
])

<!-- Mobile Backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 z-40 lg:hidden hidden transition-opacity" aria-hidden="true"></div>

<!-- Sidebar Container -->
<aside id="app-sidebar" class="fixed lg:sticky top-0 left-0 h-screen z-50 w-64 bg-white border-r border-slate-200 p-4 sm:p-5 flex flex-col justify-between shrink-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-150 ease-in-out">

    <!-- Top Part: Brand + Menu -->
    <div class="flex flex-col">
        <!-- Brand Header -->
        <div class="flex items-center justify-between lg:justify-center pb-4 border-b border-slate-200">
            <a href="{{ url('/') }}" class="flex items-center justify-center w-auto lg:w-full focus:outline-none focus:ring-2 focus:ring-slate-900 rounded-lg p-1 group text-center" title="HAFIS">
                <!-- Desktop Sidebar: hafis-logo.svg -->
                <img src="{{ asset('images/brand/hafis-logo.svg') }}" 
                     alt="HAFIS" 
                     class="h-16 lg:h-20 w-auto max-w-full object-contain object-center mx-auto hidden lg:block group-[.sidebar-collapsed]:!hidden" />
                <!-- Mobile / Collapsed: hafis-mark.svg -->
                <img src="{{ asset('images/brand/hafis-mark.svg') }}" 
                     alt="HAFIS" 
                     class="h-11 w-auto object-contain object-center mx-auto block lg:hidden group-[.sidebar-collapsed]:!block" />
            </a>

            <!-- Mobile Close Button -->
            <button type="button" id="mobile-menu-close" class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="mt-4 space-y-1" aria-label="Navigasi Utama">
            @php
            $isDashboard = request()->is('/') || request()->is('dashboard');
            $isApar = request()->is('apar*');
            $isJadwal = request()->is('jadwal*');
            $isTindakLanjut = request()->is('tindak-lanjut*');
            $isPenggantian = request()->is('penggantian*');
            $isLaporan = request()->is('laporan*') || request()->is('riwayat*');
            $isPenempatan = request()->is('penempatan*');
            @endphp

            <!-- Dashboard -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors min-h-[44px] {{ $isDashboard ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} focus:outline-none focus:ring-2 focus:ring-slate-900">
                <svg class="w-4 h-4 shrink-0 {{ $isDashboard ? 'text-slate-900' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Data APAR -->
            <a href="{{ url('/apar') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors min-h-[44px] {{ $isApar ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} focus:outline-none focus:ring-2 focus:ring-slate-900">
                <svg class="w-4 h-4 shrink-0 {{ $isApar ? 'text-slate-900' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Data APAR</span>
            </a>

            <!-- Jadwal Inspeksi -->
            <a href="{{ url('/jadwal') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors min-h-[44px] {{ $isJadwal ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} focus:outline-none focus:ring-2 focus:ring-slate-900">
                <svg class="w-4 h-4 shrink-0 {{ $isJadwal ? 'text-slate-900' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Jadwal Inspeksi</span>
            </a>

            <!-- Tindak Lanjut & Pemeliharaan (Tahap 7) -->
            <a href="{{ url('/tindak-lanjut') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors min-h-[44px] {{ $isTindakLanjut ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} focus:outline-none focus:ring-2 focus:ring-slate-900">
                <svg class="w-4 h-4 shrink-0 {{ $isTindakLanjut ? 'text-slate-900' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Tindak Lanjut APAR</span>
            </a>

            <!-- Penggantian Unit APAR (Tahap 8) -->
            <a href="{{ route('penggantian.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors min-h-[44px] {{ $isPenggantian ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} focus:outline-none focus:ring-2 focus:ring-slate-900">
                <svg class="w-4 h-4 shrink-0 {{ $isPenggantian ? 'text-slate-900' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Penggantian APAR</span>
            </a>

            <!-- Laporan & Rekapitulasi (Tahap 9) -->
            <a href="{{ route('laporan.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors min-h-[44px] {{ $isLaporan ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} focus:outline-none focus:ring-2 focus:ring-slate-900">
                <svg class="w-4 h-4 shrink-0 {{ $isLaporan ? 'text-slate-900' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Laporan & Rekapitulasi</span>
            </a>

            <!-- Titik Penempatan -->
            <a href="{{ url('/penempatan') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors min-h-[44px] {{ $isPenempatan ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} focus:outline-none focus:ring-2 focus:ring-slate-900">
                <svg class="w-4 h-4 shrink-0 {{ $isPenempatan ? 'text-slate-900' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Titik Penempatan</span>
            </a>

            <!-- Struktur Lokasi -->
            @php
            $isLokasi = request()->is('lokasi*');
            @endphp
            <a href="{{ url('/lokasi') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-xs font-semibold transition-colors min-h-[44px] {{ $isLokasi ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} focus:outline-none focus:ring-2 focus:ring-slate-900">
                <svg class="w-4 h-4 shrink-0 {{ $isLokasi ? 'text-slate-900' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Struktur Lokasi</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Part: Akun & Logout -->
    <div class="pt-3 border-t border-slate-200">
        <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors min-h-[44px] focus:outline-none focus:ring-2 focus:ring-slate-900">
            <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Pengaturan Sistem</span>
        </a>

        <button type="button" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors min-h-[44px] focus:outline-none focus:ring-2 focus:ring-rose-600 text-left cursor-pointer mt-0.5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span>Keluar</span>
        </button>
    </div>
</aside>