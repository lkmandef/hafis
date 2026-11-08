@props(['title' => 'HAFIS : Hospital APAR & Fire Inspection System'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Favicon: HAFIS Mark SVG -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/hafis-mark.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/brand/hafis-mark.svg') }}">

    <!-- Stable Scrollbar Gutter (Desktop only: preserves gutter without flashing on mobile) -->
    <style>
        @media (min-width: 1024px) {
            html {
                overflow-y: scroll;
                scrollbar-gutter: stable;
            }
        }
    </style>

    <!-- Preload Critical Typography (Root-relative to prevent port/origin mismatch) -->
    <link rel="preload" href="/fonts/inter/inter-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/inter/inter-latin-500-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/inter/inter-latin-600-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/inter/inter-latin-700-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/jetbrains-mono/jetbrains-mono-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/jetbrains-mono/jetbrains-mono-latin-500-normal.woff2" as="font" type="font/woff2" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white text-gray-900 font-sans antialiased flex flex-col lg:flex-row selection:bg-gray-900 selection:text-white">

    <!-- Aksesibilitas: Lewati ke Konten Utama (WCAG 2.4.1 Bypass Blocks) -->
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2.5 focus:bg-gray-900 focus:text-white focus:text-sm focus:font-medium focus:rounded-lg focus:shadow-lg focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-900 focus:outline-none transition-colors">
        Lewati ke Konten Utama
    </a>

    <!-- Sidebar Komponen -->
    <x-sidebar />

    <!-- Main Content Shell -->
    <div id="app-main-shell" class="flex-1 flex flex-col min-w-0 max-w-full min-h-screen bg-white">
        <!-- Topbar / Header Komponen -->
        <x-topbar />

        <!-- Main Content Area (Protected against horizontal page scroll leak) -->
        <main id="main-content" tabindex="-1" class="flex-1 p-4 sm:p-6 lg:p-8 bg-white focus:outline-none min-w-0 max-w-full overflow-x-clip">
            <div class="max-w-7xl mx-auto w-full min-w-0">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Universal Confirmation Dialog Component -->
    <x-modal-confirm />

    <!-- Script navigasi drawer mobile (Aksesibilitas & Focus Trap - WCAG 2.4.3) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const closeBtn = document.getElementById('mobile-menu-close');
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const mainShell = document.getElementById('app-main-shell');

            function isMobile() {
                return window.innerWidth < 1024;
            }

            function openSidebar() {
                if (sidebar && backdrop) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                    toggleBtn?.setAttribute('aria-expanded', 'true');

                    if (isMobile()) {
                        sidebar.setAttribute('role', 'dialog');
                        sidebar.setAttribute('aria-modal', 'true');
                        sidebar.setAttribute('aria-label', 'Navigasi Menu Utama');
                        document.body.classList.add('overflow-hidden');
                        if (mainShell) {
                            mainShell.setAttribute('inert', '');
                        }
                    }

                    // Fokus otomatis ke tombol tutup drawer
                    setTimeout(function() {
                        closeBtn?.focus();
                    }, 50);
                }
            }

            function closeSidebar() {
                if (sidebar && backdrop) {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                    toggleBtn?.setAttribute('aria-expanded', 'false');

                    if (isMobile()) {
                        sidebar.removeAttribute('role');
                        sidebar.removeAttribute('aria-modal');
                        sidebar.removeAttribute('aria-label');
                        document.body.classList.remove('overflow-hidden');
                        if (mainShell) {
                            mainShell.removeAttribute('inert');
                        }
                    }

                    // Kembalikan fokus ke tombol pembuka
                    toggleBtn?.focus();
                }
            }

            // Focus trap navigasi keyboard di drawer mobile
            sidebar?.addEventListener('keydown', function(e) {
                if (!isMobile() || sidebar.classList.contains('-translate-x-full')) {
                    return;
                }

                if (e.key === 'Tab') {
                    const focusableElements = sidebar.querySelectorAll(
                        'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    );
                    if (focusableElements.length === 0) return;

                    const firstEl = focusableElements[0];
                    const lastEl = focusableElements[focusableElements.length - 1];

                    if (e.shiftKey) {
                        if (document.activeElement === firstEl) {
                            e.preventDefault();
                            lastEl.focus();
                        }
                    } else {
                        if (document.activeElement === lastEl) {
                            e.preventDefault();
                            firstEl.focus();
                        }
                    }
                }
            });

            toggleBtn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isMobile() && !sidebar?.classList.contains('-translate-x-full')) {
                    closeSidebar();
                }
            });

            window.addEventListener('resize', function() {
                if (!isMobile()) {
                    document.body.classList.remove('overflow-hidden');
                    if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                        sidebar.removeAttribute('role');
                        sidebar.removeAttribute('aria-modal');
                        sidebar.removeAttribute('aria-label');
                        if (mainShell) {
                            mainShell.removeAttribute('inert');
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>