@props(['title' => 'HAFIS — Hospital APAR & Fire Inspection System'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Favicon: HAFIS Mark SVG -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/hafis-mark.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/brand/hafis-mark.svg') }}">

    <!-- Typography: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100/60 text-slate-900 font-sans antialiased flex flex-col lg:flex-row">

    <!-- Sidebar Komponen -->
    <x-sidebar />

    <!-- Main Content Shell -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Topbar / Header Komponen -->
        <x-topbar />

        <!-- Main Content Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto w-full">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Script navigasi drawer mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const closeBtn = document.getElementById('mobile-menu-close');
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');

            function openSidebar() {
                if (sidebar && backdrop) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                    toggleBtn?.setAttribute('aria-expanded', 'true');
                }
            }

            function closeSidebar() {
                if (sidebar && backdrop) {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                    toggleBtn?.setAttribute('aria-expanded', 'false');
                }
            }

            toggleBtn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });
        });
    </script>
</body>

</html>