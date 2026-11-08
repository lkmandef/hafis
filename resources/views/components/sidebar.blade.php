@props([
'active' => 'dashboard'
])

<!-- Mobile Backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-gray-900/40 z-40 lg:hidden hidden transition-opacity" aria-hidden="true"></div>

<!-- Sidebar Container -->
<aside id="app-sidebar" class="fixed lg:sticky top-0 left-0 h-screen h-dvh max-h-dvh lg:h-screen lg:max-h-screen z-50 w-64 bg-gray-50 border-r border-gray-200 shadow-xl lg:shadow-none px-4 pt-4 pb-[max(1rem,calc(1rem+env(safe-area-inset-bottom,0px)))] lg:p-3.5 flex flex-col justify-between shrink-0 transform -translate-x-full lg:translate-x-0 motion-safe:transition-transform motion-reduce:transition-none lg:transition-none duration-200 ease-in-out overflow-hidden">

    <!-- Top Part: Brand + Header -->
    <div class="shrink-0 pb-3 border-b border-gray-200">
        <!-- Brand Header -->
        <div class="flex items-center justify-between lg:justify-center">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 lg:justify-center w-auto lg:w-full focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 rounded-lg p-1 group" title="HAFIS - Hospital APAR & Fire Inspection System">
                <!-- Desktop Sidebar: hafis-logo.svg (compact height for short desktop viewports) -->
                <img src="{{ asset('images/brand/hafis-logo.svg') }}"
                     alt="HAFIS"
                     width="109"
                     height="48"
                     class="h-10 lg:h-12 w-auto max-w-full object-contain object-center mx-auto hidden lg:block group-[.sidebar-collapsed]:!hidden" />

                <!-- Mobile Header Brand Lockup (Mark + Text) -->
                <div class="flex items-center gap-2.5 lg:hidden group-[.sidebar-collapsed]:!hidden">
                    <img src="{{ asset('images/brand/hafis-mark.svg') }}"
                         alt="Logo HAFIS"
                         width="36"
                         height="45"
                         class="h-9 w-auto object-contain object-center shrink-0" />
                    <div class="flex flex-col min-w-0">
                        <span class="text-sm font-bold tracking-tight text-gray-900 leading-none">HAFIS</span>
                        <span class="text-[10px] font-medium text-gray-500 leading-tight mt-1 truncate">RSUD Ansari Saleh</span>
                    </div>
                </div>

                <!-- True Collapsed Icon Rail (if sidebar-collapsed on desktop) -->
                <img src="{{ asset('images/brand/hafis-mark.svg') }}"
                     alt="HAFIS"
                     width="36"
                     height="45"
                     class="h-9 w-auto object-contain object-center mx-auto hidden group-[.sidebar-collapsed]:!block" />
            </a>

            <!-- Mobile Close Button -->
            <button type="button" id="mobile-menu-close" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-200/70 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[44px] min-w-[44px] flex items-center justify-center cursor-pointer transition-colors shrink-0" aria-label="Tutup Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Identitas Institusi (desktop only - grouped cleanly under brand) -->
        <div class="hidden lg:block text-center pt-1 group-[.sidebar-collapsed]:!hidden">
            <p class="text-[11px] font-medium text-gray-700 leading-snug">RSUD dr. H. Moch. Ansari Saleh</p>
            <p class="text-[10px] text-gray-500 leading-tight">Banjarmasin</p>
        </div>
    </div>

    <!-- Middle Part: Scrollable Navigation Links -->
    <nav class="flex-1 min-h-0 overflow-y-auto overscroll-contain py-2.5 lg:py-2 space-y-1 scrollbar-none lg:scrollbar-thin lg:pr-1" aria-label="Navigasi Utama">
        @php
        $isDashboard = request()->routeIs('dashboard') || request()->is('/') || request()->is('dashboard');
        $isApar = request()->routeIs('apar.*') || request()->is('apar*');
        $isHasilInspeksi = request()->routeIs('pemeriksaan.*') || request()->routeIs('scan.*') || request()->is('pemeriksaan*') || request()->is('scan*');
        $isTindakLanjut = request()->routeIs('tindak-lanjut.*') || request()->is('tindak-lanjut*');
        $isPenggantian = request()->routeIs('penggantian.*') || request()->is('penggantian*');
        $isLokasi = request()->routeIs('lokasi.*') || request()->is('lokasi*');
        $isLaporan = request()->routeIs('laporan.*') || request()->is('laporan*') || request()->is('riwayat*');
        $isUsers = request()->routeIs('users.*') || request()->is('pengguna*');
        @endphp

        <!-- Dashboard -->
        <a href="{{ url('/') }}" {{ $isDashboard ? 'aria-current="page"' : '' }} class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors motion-safe:active:scale-[0.98] min-h-[40px] {{ $isDashboard ? 'bg-gray-900 text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-200/60 hover:text-gray-900' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900">
            <svg class="w-4 h-4 shrink-0 {{ $isDashboard ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Data APAR -->
        <a href="{{ url('/apar') }}" {{ $isApar ? 'aria-current="page"' : '' }} class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors motion-safe:active:scale-[0.98] min-h-[40px] {{ $isApar ? 'bg-gray-900 text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-200/60 hover:text-gray-900' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900">
            <svg class="w-4 h-4 shrink-0 {{ $isApar ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span>Data APAR</span>
        </a>

        <!-- Hasil Inspeksi (Tahap 4) -->
        <a href="{{ route('pemeriksaan.index') }}" {{ $isHasilInspeksi ? 'aria-current="page"' : '' }} class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors motion-safe:active:scale-[0.98] min-h-[40px] {{ $isHasilInspeksi ? 'bg-gray-900 text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-200/60 hover:text-gray-900' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900">
            <svg class="w-4 h-4 shrink-0 {{ $isHasilInspeksi ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span>Hasil Inspeksi</span>
        </a>

        <!-- Tindak Lanjut & Pemeliharaan (Tahap 7) -->
        <a href="{{ url('/tindak-lanjut') }}" {{ $isTindakLanjut ? 'aria-current="page"' : '' }} class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors motion-safe:active:scale-[0.98] min-h-[40px] {{ $isTindakLanjut ? 'bg-gray-900 text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-200/60 hover:text-gray-900' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900">
            <svg class="w-4 h-4 shrink-0 {{ $isTindakLanjut ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span>Tindak Lanjut</span>
        </a>

        <!-- Penggantian Unit APAR (Tahap 8) -->
        <a href="{{ route('penggantian.index') }}" {{ $isPenggantian ? 'aria-current="page"' : '' }} class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors motion-safe:active:scale-[0.98] min-h-[40px] {{ $isPenggantian ? 'bg-gray-900 text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-200/60 hover:text-gray-900' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900">
            <svg class="w-4 h-4 shrink-0 {{ $isPenggantian ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Penggantian APAR</span>
        </a>

        <!-- Struktur Lokasi -->
        <a href="{{ url('/lokasi') }}" {{ $isLokasi ? 'aria-current="page"' : '' }} class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors motion-safe:active:scale-[0.98] min-h-[40px] {{ $isLokasi ? 'bg-gray-900 text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-200/60 hover:text-gray-900' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900">
            <svg class="w-4 h-4 shrink-0 {{ $isLokasi ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span>Struktur Lokasi</span>
        </a>

        <!-- Laporan & Rekapitulasi (Tahap 9) -->
        <a href="{{ route('laporan.index') }}" {{ $isLaporan ? 'aria-current="page"' : '' }} class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors motion-safe:active:scale-[0.98] min-h-[40px] {{ $isLaporan ? 'bg-gray-900 text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-200/60 hover:text-gray-900' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900">
            <svg class="w-4 h-4 shrink-0 {{ $isLaporan ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Laporan & Rekapitulasi</span>
        </a>
    </nav>

    <!-- Bottom Part: Pengguna & Akses (Khusus Admin) + Logout -->
    <div class="shrink-0 pt-2.5 border-t border-gray-200 space-y-1">
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('users.index') }}" {{ $isUsers ? 'aria-current="page"' : '' }} class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors motion-safe:active:scale-[0.98] min-h-[40px] {{ $isUsers ? 'bg-gray-900 text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-200/60 hover:text-gray-900' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900">
                <svg class="w-4 h-4 shrink-0 {{ $isUsers ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Pengguna & Akses</span>
            </a>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="w-full"
              data-confirm
              data-confirm-title="Keluar dari Sistem HAFIS"
              data-confirm-message="Apakah Anda yakin ingin keluar dari sistem? Anda harus masuk kembali dengan kredensial akun K3RS untuk melanjutkan inspeksi."
              data-confirm-type="logout"
              data-confirm-text="Ya, Keluar">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors motion-safe:active:scale-[0.98] min-h-[40px] focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 text-left cursor-pointer">
                <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
