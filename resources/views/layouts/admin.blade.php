<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Óptica Golden Admin</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#D4AF37">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="/icons/icon-192.svg">
    <link rel="icon" type="image/svg+xml" href="/icons/icon-192.svg">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        @include('partials.admin-sidebar')

        <div class="flex-grow-1 d-flex flex-column min-vh-100">
            @include('partials.admin-navbar')

            <div class="container-fluid p-4">
                @include('partials.alerts')
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.counter-value').forEach(el => {
            const target = parseFloat(el.dataset.target) || 0;
            const suffix = el.dataset.suffix || '';
            const duration = parseInt(el.dataset.duration) || 1000;
            const start = performance.now();
            function update(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(eased * target).toLocaleString() + suffix;
                if (progress < 1) requestAnimationFrame(update);
                else el.textContent = target.toLocaleString() + suffix;
            }
            requestAnimationFrame(update);
        });
    });
    </script>
    @stack('scripts')
</body>
</html>
