<!-- Early Theme Initializer & UI Stacking Controller -->
<script>
    (function() {
        function applySystemTheme() {
            const currentTheme = localStorage.getItem('theme') || localStorage.getItem('hris_theme') || 'system';
            let dark = false;
            if (currentTheme === 'dark') {
                dark = true;
            } else if (currentTheme === 'system') {
                dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            }
            document.documentElement.classList.toggle('dark', dark);
        }

        applySystemTheme();
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applySystemTheme);
        }

        function elevateChain(target) {
            if (!target || !target.closest) return;
            let curr = target;
            while (curr && curr !== document.body) {
                if (curr.classList && (
                    curr.classList.contains('fi-section') ||
                    curr.classList.contains('fi-sc-section') ||
                    curr.classList.contains('fi-sc-component') ||
                    curr.classList.contains('fi-grid-col') ||
                    curr.classList.contains('fi-input-wrp') ||
                    curr.classList.contains('fi-fo-field-wrp') ||
                    curr.classList.contains('fi-fo-field-wrp-ctn')
                )) {
                    curr.classList.add('fi-elevated-active');
                }
                curr = curr.parentElement;
            }
        }

        function clearElevation() {
            const activePopups = document.querySelectorAll(
                '[aria-expanded="true"], .choices.is-open, .choices__list--dropdown.is-active, .fi-fo-date-time-picker-panel:not([style*="display: none"]), .fi-dropdown-panel:not([style*="display: none"])'
            );
            document.querySelectorAll('.fi-elevated-active').forEach(node => {
                let keep = false;
                if (document.activeElement && document.activeElement !== document.body && node.contains(document.activeElement)) {
                    keep = true;
                }
                activePopups.forEach(popup => {
                    if (node.contains(popup)) keep = true;
                });
                if (!keep) node.classList.remove('fi-elevated-active');
            });
        }

        document.addEventListener('focusin', function(e) {
            clearElevation();
            elevateChain(e.target);
        }, true);

        document.addEventListener('click', function() {
            setTimeout(function() {
                clearElevation();
                document.querySelectorAll(
                    '[aria-expanded="true"], .choices.is-open, .choices__list--dropdown.is-active, .fi-fo-date-time-picker-panel:not([style*="display: none"]), .fi-dropdown-panel:not([style*="display: none"])'
                ).forEach(el => elevateChain(el));
                if (document.activeElement && document.activeElement !== document.body) {
                    elevateChain(document.activeElement);
                }
            }, 10);
        }, true);
    })();
</script>

<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#0E6838" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0F1115" media="(prefers-color-scheme: dark)">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="HRIS BPR">
<link rel="apple-touch-icon" href="{{ asset('images/logo-tight.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/bento-theme.css') }}?v={{ file_exists(public_path('css/bento-theme.css')) ? filemtime(public_path('css/bento-theme.css')) : '1' }}">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
</script>
