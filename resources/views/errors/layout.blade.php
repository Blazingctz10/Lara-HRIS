<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') &bull; Core HRIS PT BPR Banda Raya</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tight.png') }}">

    <script>
        (function () {
            try {
                const saved = localStorage.getItem('theme') || localStorage.getItem('hris_theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = saved ? saved === 'dark' : prefersDark;
                document.documentElement.classList.toggle('dark', isDark);
            } catch (e) {}
        })();
    </script>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --m3-font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --m3-font-mono: 'JetBrains Mono', monospace;

            --bg: #EEF4F0;
            --surface: rgba(255, 255, 255, 0.92);
            --surface-container: #F2F7F4;
            --on-surface: #0F1E14;
            --on-surface-variant: #475B4E;
            --outline: #9FB5A7;
            --outline-variant: rgba(14, 104, 56, 0.14);

            --primary: #0E6838;
            --primary-btn-text: #FFFFFF;
            --primary-glow: rgba(14, 104, 56, 0.25);

            --badge-bg: #FEE2E2;
            --badge-text: #991B1B;
            --badge-border: #FECACA;
            --icon-bg: rgba(220, 38, 38, 0.1);
            --icon-color: #DC2626;
        }

        .dark {
            --bg: #0A110D;
            --surface: rgba(17, 26, 20, 0.9);
            --surface-container: rgba(25, 38, 30, 0.85);
            --on-surface: #ECFDF5;
            --on-surface-variant: #9EB3A6;
            --outline: #4B6355;
            --outline-variant: rgba(255, 255, 255, 0.09);

            --primary: #4ADE80;
            --primary-btn-text: #052E16;
            --primary-glow: rgba(74, 222, 128, 0.24);

            --badge-bg: rgba(127, 29, 29, 0.55);
            --badge-text: #FECACA;
            --badge-border: rgba(248, 113, 113, 0.35);
            --icon-bg: rgba(239, 68, 68, 0.16);
            --icon-color: #F87171;
        }

        /* Variant: Warning (404, 419, 429) */
        [data-variant="warning"] {
            --badge-bg: #FEF3C7;
            --badge-text: #92400E;
            --badge-border: #FDE68A;
            --icon-bg: rgba(234, 88, 12, 0.12);
            --icon-color: #EA580C;
        }
        .dark [data-variant="warning"] {
            --badge-bg: rgba(120, 53, 15, 0.55);
            --badge-text: #FDE68A;
            --badge-border: rgba(251, 191, 36, 0.35);
            --icon-bg: rgba(251, 146, 60, 0.16);
            --icon-color: #FB923C;
        }

        /* Variant: Maintenance (503) */
        [data-variant="maintenance"] {
            --badge-bg: #CCFBF1;
            --badge-text: #0F766E;
            --badge-border: #99F6E4;
            --icon-bg: rgba(14, 104, 56, 0.12);
            --icon-color: #0E6838;
        }
        .dark [data-variant="maintenance"] {
            --badge-bg: rgba(6, 78, 59, 0.6);
            --badge-text: #A7F3D0;
            --badge-border: rgba(52, 211, 153, 0.35);
            --icon-bg: rgba(74, 222, 128, 0.16);
            --icon-color: #4ADE80;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--m3-font-sans);
            background-color: var(--bg);
            background-image:
                radial-gradient(at 15% 15%, rgba(14, 104, 56, 0.14) 0px, transparent 50%),
                radial-gradient(at 85% 85%, rgba(234, 88, 12, 0.08) 0px, transparent 50%);
            color: var(--on-surface);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            line-height: 1.5;
            transition: background-color 0.25s ease;
        }

        .m3-error-card {
            background: var(--surface);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--outline-variant);
            border-radius: 28px;
            box-shadow: 0 24px 60px -12px rgba(0, 0, 0, 0.22), 0 4px 16px rgba(14, 104, 56, 0.08);
            max-width: 640px;
            width: 100%;
            padding: 2.25rem;
            position: relative;
            overflow: hidden;
            animation: cardAppear 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardAppear {
            from { opacity: 0; transform: translateY(12px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .m3-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.85rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--outline-variant);
        }

        .m3-logo-img {
            height: 34px;
            width: auto;
            display: block;
        }

        .m3-logo-dark { display: none; }
        .dark .m3-logo-dark { display: block; }
        .dark .m3-logo-light { display: none; }

        .m3-code-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            background: var(--badge-bg);
            color: var(--badge-text);
            border: 1px solid var(--badge-border);
            font-family: var(--m3-font-mono);
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }

        .m3-header-row {
            display: flex;
            align-items: flex-start;
            gap: 1.15rem;
            margin-bottom: 1.1rem;
        }

        .m3-content-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: var(--icon-bg);
            color: var(--icon-color);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .m3-heading {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--on-surface);
            line-height: 1.25;
            margin-bottom: 0.35rem;
        }

        .m3-subtitle-meta {
            font-size: 0.78rem;
            color: var(--on-surface-variant);
            font-weight: 600;
        }

        .m3-desc {
            font-size: 0.94rem;
            color: var(--on-surface-variant);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .m3-action-box {
            background: var(--surface-container);
            border: 1px solid var(--outline-variant);
            border-radius: 18px;
            padding: 1.15rem 1.25rem;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
        }

        .m3-action-icon {
            color: var(--primary);
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .m3-action-text {
            font-size: 0.86rem;
            color: var(--on-surface);
            line-height: 1.5;
        }

        .m3-buttons {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .m3-btn-primary {
            font-family: var(--m3-font-sans);
            font-size: 0.875rem;
            font-weight: 700;
            background-color: var(--primary);
            color: var(--primary-btn-text);
            border: none;
            padding: 0.75rem 1.55rem;
            border-radius: 9999px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 16px var(--primary-glow);
            transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1), opacity 0.2s ease;
        }

        .m3-btn-primary:hover {
            transform: translateY(-2px);
            opacity: 0.95;
        }

        .m3-btn-tonal {
            font-family: var(--m3-font-sans);
            font-size: 0.875rem;
            font-weight: 700;
            background-color: transparent;
            color: var(--on-surface);
            border: 1px solid var(--outline);
            padding: 0.75rem 1.35rem;
            border-radius: 9999px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .m3-btn-tonal:hover {
            background-color: var(--surface-container);
            transform: translateY(-1px);
        }

        .m3-footer-note {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--outline-variant);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--on-surface-variant);
        }

        .m3-details {
            margin-top: 1.25rem;
            border-top: 1px solid var(--outline-variant);
            padding-top: 1rem;
        }

        .m3-details summary {
            font-family: var(--m3-font-mono);
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--on-surface-variant);
            cursor: pointer;
            padding: 0.35rem 0;
            user-select: none;
        }

        .m3-trace-box {
            margin-top: 0.75rem;
            background: #090e0b;
            color: #86efac;
            font-family: var(--m3-font-mono);
            font-size: 0.74rem;
            padding: 1rem;
            border-radius: 14px;
            overflow-x: auto;
            max-height: 240px;
            line-height: 1.45;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        @media (max-width: 540px) {
            .m3-error-card { padding: 1.5rem; border-radius: 22px; }
            .m3-header-row { flex-direction: column; gap: 0.85rem; }
            .m3-heading { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="m3-error-card" data-variant="@yield('variant', 'danger')">
        <div class="m3-topbar">
            <div>
                <img src="{{ asset('images/logo-horizontal-dark.png') }}" alt="PT BPR Banda Raya" class="m3-logo-img m3-logo-dark">
                <img src="{{ asset('images/logo-horizontal-light.png') }}" alt="PT BPR Banda Raya" class="m3-logo-img m3-logo-light">
            </div>
            <div>
                <span class="m3-code-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <span>KODE: @yield('code', 'SYS500')</span>
                </span>
            </div>
        </div>

        <div class="m3-header-row">
            <div class="m3-content-icon">
                @yield('icon')
            </div>
            <div>
                <h1 class="m3-heading">@yield('heading', 'Terjadi Kendala pada Sistem')</h1>
                <div class="m3-subtitle-meta">Core HRIS &bull; PT BPR Banda Raya</div>
            </div>
        </div>

        <p class="m3-desc">@yield('description')</p>

        <div class="m3-action-box">
            <div class="m3-action-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
            </div>
            <div class="m3-action-text">
                <strong>Rekomendasi Tindakan:</strong><br>
                @yield('action', 'Silakan muat ulang halaman atau hubungi Divisi IT PT BPR Banda Raya.')
            </div>
        </div>

        <div class="m3-buttons">
            <a href="{{ url('/') }}" class="m3-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
            <button type="button" onclick="window.location.reload()" class="m3-btn-tonal">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Muat Ulang Halaman</span>
            </button>
        </div>

        @hasSection('technical_details')
            <details class="m3-details">
                <summary>Detail Teknis Diagnostik (Khusus Admin / Developer)</summary>
                <div class="m3-trace-box">
                    @yield('technical_details')
                </div>
            </details>
        @endif

        <div class="m3-footer-note">
            <span>Waktu Sistem: {{ now()->format('d M Y, H:i') }} WIB</span>
            <span>Divisi IT &bull; PT BPR Banda Raya</span>
        </div>
    </div>
</body>
</html>
