<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    @php
        $apiToken = session('_api_token');
        // Validar que el token siga existiendo en DB (puede haberse eliminado)
        if ($apiToken) {
            $tokenId = (int)(explode('|', $apiToken)[0] ?? 0);
            $isValid = $tokenId && auth()->user()->tokens()->where('id', $tokenId)->exists();
            if (!$isValid) {
                $apiToken = null;
                session()->forget('_api_token');
            }
        }
        if (!$apiToken) {
            auth()->user()->tokens()->where('name', 'web-session')->delete();
            $apiToken = auth()->user()->createToken('web-session')->plainTextToken;
            session(['_api_token' => $apiToken]);
        }
    @endphp
    <meta name="api-token" content="{{ $apiToken }}">
    @else
    <meta name="api-token" content="">
    @endauth
    <title>@yield('title', 'Óptica Golden') - Óptica Golden</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#D4AF37">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Óptica Golden">
    <link rel="apple-touch-icon" href="/icons/icon-192.svg">
    <link rel="icon" type="image/svg+xml" href="/icons/icon-192.svg">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    @include('partials.navbar')

    <main class="flex-grow-1 page-wrapper">
        @include('partials.alerts')
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Scroll reveal animation
    document.addEventListener('DOMContentLoaded', function() {
        const revealElements = document.querySelectorAll('.scroll-reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        revealElements.forEach(el => observer.observe(el));

        // Counter animation for KPI values
        document.querySelectorAll('.counter-value').forEach(el => {
            const target = parseFloat(el.dataset.target) || parseFloat(el.textContent.replace(/[^0-9.-]/g, ''));
            const suffix = el.dataset.suffix || '';
            const duration = parseInt(el.dataset.duration) || 1000;
            const start = performance.now();

            function updateCounter(currentTime) {
                const elapsed = currentTime - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = eased * target;
                el.textContent = Math.round(current).toLocaleString() + suffix;
                if (progress < 1) requestAnimationFrame(updateCounter);
                else el.textContent = target.toLocaleString() + suffix;
            }
            requestAnimationFrame(updateCounter);
        });

        // Ripple effect on buttons
        document.querySelectorAll('.btn-ripple').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
                ripple.classList.add('ripple-effect');
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Cart badge bounce on change
        const cartBadge = document.getElementById('cart-count');
        if (cartBadge) {
            const observer = new MutationObserver(() => {
                cartBadge.classList.remove('cart-badge-bounce');
                void cartBadge.offsetWidth;
                cartBadge.classList.add('cart-badge-bounce');
            });
            observer.observe(cartBadge, { childList: true, characterData: true, subtree: true });
        }
    });
    </script>
    @stack('scripts')
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(reg => {
                    reg.addEventListener('updatefound', () => {
                        const newWorker = reg.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // New content available — could show a toast here
                                console.log('[SW] Nueva versión disponible.');
                            }
                        });
                    });
                })
                .catch(err => console.warn('[SW] Error al registrar:', err));
        });
    }
    </script>
</body>
</html>
