<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGPM SERVICE CENTER | Compliance Shop & Inventory Manager</title>

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        slate: {
                            950: '#060a13',
                        }
                    }
                }
            }
        }
    </script>

    <!-- CDNs: Google Fonts (Plus Jakarta Sans, Inter, JetBrains Mono, Outfit), FontAwesome, Chart.js, SheetJS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js" defer></script>

    <!-- Flatpickr (Indonesian Datepicker dd/mm/yyyy) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- i18n Translation Dictionary -->
    <script src="{{ asset('assets/js/modules/i18n.js') }}"></script>
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body class="custom-scrollbar font-sans light-mode">

    <!-- Toast Notifications (Above all modals) -->
    <div id="toast-container" class="fixed top-6 right-6 z-[99999] flex flex-col gap-3 pointer-events-none"></div>

    <!-- Mobile & Tablet Header Navigation (< 768px) -->
    @include('partials.header-mobile')

    <!-- Mobile Navigation Drawer Overlay & Side Panel (< 768px) -->
    @include('partials.mobile-drawer')

    <!-- Top App Bar Header Navigation (Desktop Full Width Above App Shell Container) -->
    @include('partials.header-topbar')

    <!-- App Shell Container -->
    <div id="app-shell-container" class="w-full flex flex-col md:flex-row relative min-h-[calc(100vh-65px)]">
        <!-- Desktop Persistent Vertical Sidebar (≥ 768px) -->
        @include('partials.sidebar')

        <!-- Main Content Area -->
        <main id="app-main-content" class="flex-1 w-full min-w-0 px-4 sm:px-8 py-6 min-h-screen">
            <!-- Dynamic Workspace Content -->
            @yield('content')
        </main>
    </div>

    <!-- Global Application Modals -->
    @include('modals.profile')
    @include('modals.user-registration')
    @include('modals.inventory-modals')
    @include('modals.management-modals')
    @include('modals.common-modals')

    <!-- Core Modular System Javascript State Engine -->
    <script src="{{ asset('assets/js/modules/store.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/modules/auth.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/modules/navigation.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/app.js') }}?v={{ time() }}"></script>
</body>

</html>
