<header class="bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
    <!-- Left Section: Mobile Toggle & Context Breadcrumb -->
    <div class="flex items-center gap-3">
        <!-- Mobile Menu Toggle Button -->
        <button type="button" id="mobile-menu-toggle" class="lg:hidden p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900 min-h-[44px] min-w-[44px] flex items-center justify-center transition-colors" aria-label="Buka Navigasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Realtime Clock Widget -->
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-700 select-none min-h-[38px]" title="Waktu Sistem HAFIS (Realtime)">
            <!-- Pulse Indicator -->
            <span class="relative flex h-2 w-2 shrink-0" aria-hidden="true">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>

            <!-- Clock Icon -->
            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

            <!-- Hari & Tanggal (Desktop) -->
            <span id="topbar-realtime-date" class="font-medium text-slate-700 hidden sm:inline">{{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, DD MMMM Y') }}</span>

            <!-- Separator (Desktop) -->
            <span class="text-slate-300 hidden sm:inline" aria-hidden="true">•</span>

            <!-- Jam Digital (HH:mm:ss WIB) -->
            <span class="inline-flex items-center gap-1 font-mono font-bold text-slate-900 tabular-nums">
                <span id="topbar-realtime-time">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') }}</span>
                <span class="text-[10px] font-semibold text-slate-500 font-sans tracking-wide">WIB</span>
            </span>
        </div>
    </div>

    <!-- Right Section: Search & Petugas Profile -->
    <div class="flex items-center gap-3">
        <!-- Search Input (Clean Hairline) -->
        <div class="relative hidden md:block">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input
                type="text"
                placeholder="Cari kode unit atau lokasi..."
                class="w-56 lg:w-64 pl-8 pr-3 py-1.5 text-xs text-slate-900 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 focus:outline-none transition min-h-[38px]" />
        </div>

        <!-- Scan APAR Quick Action Button -->
        <a href="{{ url('/scan') }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900 flex items-center gap-1.5 transition text-xs font-semibold shrink-0 min-h-[44px]" title="Scan APAR">
            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
            <span class="hidden sm:inline">Scan APAR</span>
        </a>

        <!-- Notification Indicator -->
        <button type="button" class="w-10 h-10 sm:w-9 sm:h-9 min-h-[44px] min-w-[44px] sm:min-h-0 sm:min-w-0 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900 flex items-center justify-center relative transition shrink-0" aria-label="Notifikasi">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="w-2 h-2 bg-rose-500 rounded-full absolute top-2 right-2 ring-2 ring-white"></span>
        </button>

        <!-- Petugas Info -->
        <div class="flex items-center gap-2 pl-2 border-l border-slate-200 shrink-0">
            <div class="w-7 h-7 rounded-md bg-slate-800 text-white font-bold text-[11px] flex items-center justify-center">
                K3
            </div>
            <div class="hidden sm:block text-left">
                <span class="block text-xs font-semibold text-slate-800 leading-tight">Petugas K3RS</span>
                <span class="block text-[10px] text-slate-500 leading-tight">Shift Pagi</span>
            </div>
        </div>
    </div>
</header>

<!-- Script Realtime Clock HAFIS -->
<script>
    (function() {
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
    })();
</script>