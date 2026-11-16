{{-- PWA Manifest & Meta Tags --}}
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<meta name="theme-color" content="#2563eb">

{{-- Service Worker & PWA Install Manager --}}
<script>
    (function() {
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset('sw.js') }}')
                    .then(function(registration) {
                        // console.log('[PWA] Service Worker terdaftar.');
                    })
                    .catch(function(error) {
                        console.warn('[PWA] Service Worker gagal:', error);
                    });
            });
        }

        // Global handler untuk PWA Install Prompt
        window.deferredPWAInstallPrompt = null;
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            window.deferredPWAInstallPrompt = e;
            // Dispatch custom event agar tombol UI bisa merespons
            window.dispatchEvent(new CustomEvent('pwa-installable'));
        });

        window.addEventListener('appinstalled', function() {
            window.deferredPWAInstallPrompt = null;
            window.dispatchEvent(new CustomEvent('pwa-installed'));
        });
    })();
</script>
