<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') &bull; Core HRIS PT BPR Banda Raya</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tight.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --m3-font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --m3-font-display: 'Plus Jakarta Sans', var(--m3-font-sans);
            --m3-font-mono: 'JetBrains Mono', monospace;

            --bg: #E8EFEA;
            --surface: #FFFFFF;
            --surface-container: #DFEAE2;
            --surface-container-high: #D6E3D9;
            --on-surface: #0F1E14;
            --on-surface-variant: #33473B;
            --outline: #5E7365;
            --outline-variant: #B6CAC0;

            --primary: #0E6838;
            --primary-container: #CCE8D7;
            --on-primary-container: #03361B;
            
            --error: #DC2626;
            --error-container: #FEE2E2;
            --on-error-container: #7F1D1D;

            --amber: #D97706;
            --amber-container: #FEF3C7;
            --on-amber-container: #78350F;
        }

        .dark {
            --bg: #0D1410;
            --surface: #111A14;
            --surface-container: #19261E;
            --surface-container-high: #203026;
            --on-surface: #E2E8E4;
            --on-surface-variant: #9EABA3;
            --outline: #7A8980;
            --outline-variant: rgba(255, 255, 255, 0.1);

            --primary: #4ADE80;
            --primary-container: #0B4F27;
            --on-primary-container: #BBF7D0;

            --error: #F87171;
            --error-container: #7F1D1D;
            --on-error-container: #FECACA;

            --amber: #FBBF24;
            --amber-container: #78350F;
            --on-amber-container: #FDE68A;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--m3-font-sans);
            background-color: var(--bg);
            color: var(--on-surface);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            line-height: 1.5;
            transition: background-color 0.2s ease;
        }

        .m3-error-card {
            background-color: var(--surface);
            border: 1px solid var(--outline-variant);
            border-radius: 28px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.18), 0 2px 8px rgba(14, 104, 56, 0.08);
            max-width: 680px;
            width: 100%;
            padding: 2.25rem;
            position: relative;
            overflow: hidden;
        }

        .m3-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .m3-logo-img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        .m3-code-pill {
            font-family: var(--m3-font-mono);
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            background-color: var(--error-container);
            color: var(--on-error-container);
            border: 1px solid rgba(220, 38, 38, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .m3-content-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background-color: var(--error-container);
            color: var(--error);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .m3-heading {
            font-family: var(--m3-font-display);
            font-size: 1.65rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: var(--on-surface);
            margin-bottom: 0.6rem;
            line-height: 1.25;
        }

        .m3-desc {
            font-size: 0.95rem;
            color: var(--on-surface-variant);
            margin-bottom: 1.5rem;
        }

        .m3-action-box {
            background-color: var(--surface-container);
            border: 1px solid var(--outline-variant);
            border-radius: 18px;
            padding: 1.25rem;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
        }

        .m3-action-icon {
            color: var(--primary);
            flex-shrink: 0;
            margin-top: 0.15rem;
        }

        .m3-action-text {
            font-size: 0.875rem;
            color: var(--on-surface);
            line-height: 1.45;
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
            color: #ffffff;
            border: none;
            padding: 0.75rem 1.6rem;
            border-radius: 9999px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(14, 104, 56, 0.28);
            transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
        }

        .dark .m3-btn-primary {
            color: #003a19;
            box-shadow: 0 4px 16px rgba(74, 222, 128, 0.25);
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
            padding: 0.75rem 1.4rem;
            border-radius: 9999px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .m3-btn-tonal:hover {
            background-color: var(--surface-container);
            transform: translateY(-1px);
        }

        .m3-details {
            margin-top: 1.75rem;
            border-top: 1px solid var(--outline-variant);
            padding-top: 1.25rem;
        }

        .m3-details summary {
            font-family: var(--m3-font-mono);
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--on-surface-variant);
            cursor: pointer;
            padding: 0.4rem 0;
            user-select: none;
        }

        .m3-trace-box {
            margin-top: 0.75rem;
            background: #090e0b;
            color: #86efac;
            font-family: var(--m3-font-mono);
            font-size: 0.75rem;
            padding: 1rem;
            border-radius: 14px;
            overflow-x: auto;
            max-height: 240px;
            line-height: 1.45;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body>
    <div class="m3-error-card">
        <div class="m3-topbar">
            <div>
                <img src="{{ asset('images/logo-horizontal-dark.png') }}" alt="PT BPR Banda Raya" class="m3-logo-img">
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

        <div class="m3-content-icon">
            @yield('icon')
        </div>

        <h1 class="m3-heading">@yield('heading', 'Terjadi Kendala pada Sistem')</h1>
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
            <button onclick="window.location.reload()" class="m3-btn-tonal">
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
    </div>
</body>
</html>
