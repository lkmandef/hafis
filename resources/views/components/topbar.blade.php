@php
    $notifAparPerhatian = \App\Models\Apar::whereIn('status', [\App\Models\Apar::STATUS_PERHATIAN, \App\Models\Apar::STATUS_RUSAK])->count();
    $notifTlPending = \App\Models\TindakLanjut::where('status', '!=', \App\Models\TindakLanjut::STATUS_SELESAI)->count();
    $totalNotif = $notifAparPerhatian + $notifTlPending;
@endphp

<header class="sticky top-0 z-30 bg-white border-b border-gray-200 px-3 sm:px-6 lg:px-8 py-2 sm:py-3 flex items-center justify-between gap-2 sm:gap-4">
    <!-- Left Section: Mobile Toggle & Context Breadcrumb -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        <!-- Mobile Menu Toggle Button -->
        <button type="button" id="mobile-menu-toggle" aria-haspopup="dialog" aria-expanded="false" aria-controls="app-sidebar" class="lg:hidden p-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 min-h-[40px] min-w-[40px] flex items-center justify-center transition-colors shrink-0 cursor-pointer" aria-label="Buka Navigasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Realtime Clock Widget -->
        <div class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-200 text-xs text-gray-700 select-none min-h-[38px] shrink min-w-0" title="Waktu Sistem HAFIS (Realtime)">
            <!-- Clock Icon -->
            <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

            <!-- Hari & Tanggal (Desktop) -->
            <span id="topbar-realtime-date" class="font-medium text-gray-700 hidden sm:inline">{{ \Carbon\Carbon::now()->isoFormat('dddd, DD MMMM Y') }}</span>

            <!-- Separator (Desktop) -->
            <span class="text-gray-300 hidden sm:inline select-none" aria-hidden="true">•</span>

            <!-- Jam Digital (HH:mm:ss WITA) -->
            <span class="inline-flex items-center gap-1 font-mono font-medium text-gray-900 tabular-nums shrink-0">
                <span id="topbar-realtime-time">{{ \Carbon\Carbon::now()->format('H:i:s') }}</span>
                <span class="text-[10px] font-sans font-medium text-gray-500 tracking-wider">WITA</span>
            </span>
        </div>
    </div>

    <!-- Right Section: Quick Action & Petugas Profile -->
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <!-- Scan APAR Quick Action Button (Desktop/Tablet shortcut, disembunyikan khusus di Dashboard karena sudah ada CTA primer di header) -->
        @unless(request()->routeIs('dashboard') || request()->is('/'))
            <a href="{{ url('/scan') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 transition-colors text-xs font-medium shrink-0 min-h-[38px] shadow-2xs" title="Scan APAR" aria-label="Scan APAR">
                <svg class="w-3.5 h-3.5 text-gray-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span>Scan APAR</span>
            </a>
        @endunless

        <!-- Notification Dropdown Component (Desktop & Mobile) -->
        <div class="relative" id="notification-wrapper">
            <button type="button" 
                    id="notification-toggle" 
                    aria-haspopup="dialog" 
                    aria-expanded="false" 
                    aria-controls="notification-panel" 
                    class="flex w-9 h-9 sm:w-9.5 sm:h-9.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 items-center justify-center relative transition-colors shrink-0 min-h-[38px] min-w-[38px] cursor-pointer" 
                    aria-label="Pemberitahuan Sistem{{ $totalNotif > 0 ? " ($totalNotif item butuh tindakan)" : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @if($totalNotif > 0)
                    <span class="absolute top-1.5 right-1.5 flex h-2 w-2" aria-hidden="true">
                        <span class="motion-safe:animate-ping motion-reduce:hidden absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-600"></span>
                    </span>
                @endif
            </button>

            <!-- Dropdown Popover Panel -->
            <div id="notification-panel" 
                 class="hidden absolute right-0 mt-2 w-72 sm:w-88 bg-white rounded-xl border border-gray-200 shadow-lg py-0 z-50 overflow-hidden transform origin-top-right transition-all duration-150 motion-reduce:transition-none"
                 role="dialog" 
                 aria-modal="false"
                 aria-labelledby="notification-heading">
                <div class="px-3.5 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span id="notification-heading" class="text-xs font-semibold text-gray-900 tracking-tight">Pemberitahuan Sistem</span>
                        @if($totalNotif > 0)
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                {{ $totalNotif }} Butuh Tindakan
                            </span>
                        @endif
                    </div>
                    <button type="button" id="notification-close" class="text-gray-400 hover:text-gray-600 p-1 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 cursor-pointer" aria-label="Tutup panel notifikasi">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto overscroll-contain">
                    @if($totalNotif > 0)
                        @if($notifAparPerhatian > 0)
                            <a href="{{ route('laporan.temuan') }}" class="flex items-start gap-3 p-3 hover:bg-gray-50/80 transition-colors group">
                                <div class="w-7 h-7 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="text-xs font-semibold text-gray-900 group-hover:text-gray-900">APAR Perlu Penanganan</p>
                                        <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200">{{ $notifAparPerhatian }} Unit</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5 leading-snug">Tabung berstatus rusak atau mendekati masa kadaluarsa/perlu pengisian ulang.</p>
                                </div>
                            </a>
                        @endif

                        @if($notifTlPending > 0)
                            <a href="{{ route('tindak-lanjut.index') }}" class="flex items-start gap-3 p-3 hover:bg-gray-50/80 transition-colors group">
                                <div class="w-7 h-7 rounded-lg bg-rose-50 border border-rose-200/80 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="text-xs font-semibold text-gray-900 group-hover:text-gray-900">Pemeliharaan Tertunda</p>
                                        <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">{{ $notifTlPending }} Tugas</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5 leading-snug">Tiket tindak lanjut perbaikan atau pemeliharaan fisik belum selesai.</p>
                                </div>
                            </a>
                        @endif
                    @else
                        <div class="p-5 text-center">
                            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2 border border-emerald-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-900">Semua Unit Siap Pakai</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">Tidak ada laporan kerusakan atau tiket perbaikan yang tertunda.</p>
                        </div>
                    @endif
                </div>

                <div class="p-2 bg-gray-50 border-t border-gray-100 text-center">
                    <a href="{{ route('laporan.temuan') }}" class="text-xs font-medium text-gray-700 hover:text-gray-900 transition-colors inline-flex items-center justify-center gap-1 focus:outline-none focus-visible:underline">
                        <span>Lihat Rekap Temuan & Kerusakan</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Petugas / User Info Dinamis -->
        <div class="flex items-center gap-2.5 pl-2 sm:pl-3 border-l border-gray-200 shrink-0">
            <div class="w-8 h-8 rounded-full bg-gray-900 text-white font-semibold text-xs flex items-center justify-center shrink-0 shadow-2xs" title="{{ auth()->user()?->name ?? 'Petugas K3RS' }}">
                {{ strtoupper(substr(auth()->user()?->name ?? 'K3', 0, 2)) }}
            </div>
            <div class="hidden sm:block text-left">
                <span class="block text-xs font-semibold text-gray-900 leading-tight truncate max-w-[150px]">
                    {{ auth()->user()?->name ?? 'Petugas K3RS' }}
                </span>
                <span class="block text-[11px] text-gray-500 leading-tight">
                    {{ auth()->user()?->role_label ?? 'Petugas K3RS' }}
                </span>
            </div>
        </div>
    </div>
</header>

<!-- Script Topbar: Realtime Clock & Notification Dropdown HAFIS -->
<script>
    (function() {
        // 1. Clock Logic
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        function updateHafisClock() {
            const dateEl = document.getElementById('topbar-realtime-date');
            const timeEl = document.getElementById('topbar-realtime-time');
            if (!timeEl) return;

            const now = new Date();
            const dayName = days[now.getDay()];
            const date = String(now.getDate()).padStart(2, '0');
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            if (dateEl) {
                dateEl.textContent = `${dayName}, ${date} ${monthName} ${year}`;
            }
            timeEl.textContent = `${hours}:${minutes}:${seconds}`;
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', updateHafisClock);
        } else {
            updateHafisClock();
        }

        setInterval(updateHafisClock, 1000);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateHafisClock();
            }
        });

        // 2. Notification Popover Logic
        const notifToggle = document.getElementById('notification-toggle');
        const notifPanel = document.getElementById('notification-panel');
        const notifClose = document.getElementById('notification-close');

        if (notifToggle && notifPanel) {
            function openNotif() {
                notifPanel.classList.remove('hidden');
                notifToggle.setAttribute('aria-expanded', 'true');
            }

            function closeNotif() {
                notifPanel.classList.add('hidden');
                notifToggle.setAttribute('aria-expanded', 'false');
            }

            notifToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const isExpanded = notifToggle.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    closeNotif();
                } else {
                    openNotif();
                }
            });

            if (notifClose) {
                notifClose.addEventListener('click', function(e) {
                    e.stopPropagation();
                    closeNotif();
                    notifToggle.focus();
                });
            }

            document.addEventListener('click', function(e) {
                if (!notifPanel.contains(e.target) && !notifToggle.contains(e.target)) {
                    closeNotif();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && notifToggle.getAttribute('aria-expanded') === 'true') {
                    closeNotif();
                    notifToggle.focus();
                }
            });
        }
    })();
</script>
