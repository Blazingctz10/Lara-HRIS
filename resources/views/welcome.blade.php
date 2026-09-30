<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Masuk — Core HRIS PT BPR Banda Raya</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo-tight.png') }}">

<!-- Google Fonts: Plus Jakarta Sans & Outfit -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

<script>
  (function() {
    const savedTheme = localStorage.getItem('hris_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
  })();
</script>

<style>
  /* =============================================================================
     MATERIAL 3 EXPRESSIVE DESIGN TOKENS - SPLIT SCREEN LOGIN
     BPR BANDA RAYA CORE HRIS
     ============================================================================= */

  :root[data-theme="dark"] {
    --m3-bg: #0D1410;
    --m3-surface: #141E18;
    --m3-surface-container: #19261E;
    --m3-surface-container-high: #203026;
    --m3-surface-container-highest: #283C30;
    --m3-outline: #7A8980;
    --m3-outline-variant: #304036;

    --m3-on-surface: #E2E8E4;
    --m3-on-surface-variant: #9EABA3;

    --m3-primary: #4ADE80;
    --m3-on-primary: #003A19;
    --m3-primary-container: #0B4F27;
    --m3-on-primary-container: #BBF7D0;

    --m3-secondary: #FB923C;
    --m3-secondary-container: #7C2D12;
    --m3-on-secondary-container: #FED7AA;

    --m3-tertiary: #FACC15;

    --m3-toggle-bg: rgba(255, 255, 255, 0.08);
    --m3-toggle-border: rgba(255, 255, 255, 0.12);
    --m3-card-border: rgba(255, 255, 255, 0.08);
    --m3-input-bg: #141E18;
  }

  :root[data-theme="light"] {
    --m3-bg: #F4F6F4;
    --m3-surface: #FFFFFF;
    --m3-surface-container: #F0F5F2;
    --m3-surface-container-high: #E8EFEA;
    --m3-surface-container-highest: #DDE7E0;
    --m3-outline: #707C74;
    --m3-outline-variant: #C8D4CC;

    --m3-on-surface: #131A15;
    --m3-on-surface-variant: #404B44;

    --m3-primary: #0E6838;
    --m3-on-primary: #FFFFFF;
    --m3-primary-container: #D1EADB;
    --m3-on-primary-container: #03361B;

    --m3-secondary: #EA580C;
    --m3-secondary-container: #FFEDD5;
    --m3-on-secondary-container: #7C2D12;

    --m3-tertiary: #CA8A04;

    --m3-toggle-bg: rgba(0, 0, 0, 0.05);
    --m3-toggle-border: rgba(0, 0, 0, 0.1);
    --m3-card-border: rgba(14, 104, 56, 0.12);
    --m3-input-bg: #FFFFFF;
  }

  *, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  html, body {
    height: 100%;
    width: 100%;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    background-color: var(--m3-bg);
    color: var(--m3-on-surface);
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
    transition: background-color 0.25s ease, color 0.25s ease;
  }

  /* Main Split-Screen Container */
  .split-layout {
    display: flex;
    min-height: 100vh;
    width: 100%;
  }

  /* =============================================================================
     LEFT SIDE: VISUAL HERO BANNER (Modern Banking Corporate Showcase)
     ============================================================================= */
  .hero-panel {
    flex: 1 1 50%;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 3.5rem 3.5rem;
    overflow: hidden;
    background-color: #081a10;
  }

  /* Background Image with Dark & Light Responsive Blend Overlay */
  .hero-panel-bg {
    position: absolute;
    inset: 0;
    background-image: url('{{ asset("images/login-hero.jpg") }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0.88;
    filter: saturate(1.15) contrast(1.05);
    transition: transform 10s ease-out;
  }

  .hero-panel:hover .hero-panel-bg {
    transform: scale(1.03);
  }

  /* Gradient Overlay */
  .hero-panel-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      180deg,
      rgba(8, 26, 16, 0.72) 0%,
      rgba(10, 32, 20, 0.82) 40%,
      rgba(7, 18, 12, 0.96) 100%
    );
    backdrop-filter: blur(1.5px);
  }

  :root[data-theme="light"] .hero-panel-overlay {
    background: linear-gradient(
      180deg,
      rgba(14, 104, 56, 0.68) 0%,
      rgba(11, 79, 39, 0.84) 45%,
      rgba(4, 38, 18, 0.96) 100%
    );
  }

  /* Content inside Left Hero Panel */
  .hero-content {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
  }

  .hero-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(12px);
    color: #BBF7D0;
    padding: 0.45rem 1rem;
    border-radius: 9999px; /* M3 Full Pill */
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    width: fit-content;
  }

  .hero-title-box {
    margin-top: auto;
    margin-bottom: 2.25rem;
  }

  .hero-headline {
    font-family: 'Outfit', sans-serif;
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1.15;
    letter-spacing: -0.02em;
    color: #FFFFFF;
    margin-bottom: 1.15rem;
    text-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);
  }

  .hero-headline span.accent {
    background: linear-gradient(135deg, #4ADE80 0%, #FACC15 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .hero-subtext {
    font-size: 1.05rem;
    line-height: 1.6;
    color: #D1EADB;
    max-width: 520px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
  }

  /* Floating Metrics / Highlights (M3 Glassmorphic Cards) */
  .hero-cards-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 2.25rem;
  }

  .hero-metric-card {
    background: rgba(20, 36, 26, 0.55);
    border: 1px solid rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(16px);
    border-radius: 20px;
    padding: 1.15rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    transition: all 0.25s cubic-bezier(0.2, 0, 0, 1);
  }

  .hero-metric-card:hover {
    background: rgba(20, 36, 26, 0.75);
    transform: translateY(-2px);
    border-color: rgba(74, 222, 128, 0.4);
  }

  .hero-metric-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: rgba(74, 222, 128, 0.16);
    color: #4ADE80;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .hero-metric-info h4 {
    font-size: 0.95rem;
    font-weight: 800;
    color: #FFFFFF;
    margin-bottom: 0.15rem;
  }

  .hero-metric-info p {
    font-size: 0.775rem;
    color: #A7F3D0;
    line-height: 1.35;
  }

  /* Regulatory LPS/OJK footer */
  .hero-footer-reg {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-top: 1.25rem;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
    font-size: 0.75rem;
    color: #9EABA3;
    line-height: 1.45;
  }

  .hero-footer-reg svg {
    flex-shrink: 0;
    color: #FACC15;
  }

  /* =============================================================================
     RIGHT SIDE: LOGIN FORM PANEL
     ============================================================================= */
  .form-panel {
    flex: 1 1 50%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2.5rem 3rem;
    background-color: var(--m3-bg);
    position: relative;
    overflow-y: auto;
  }

  /* Top Bar with Switcher */
  .form-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 2rem;
  }

  .portal-switch-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.35rem 0.85rem;
    border-radius: 9999px;
    background: var(--m3-primary-container);
    color: var(--m3-on-primary-container);
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.4px;
    border: 1px solid rgba(14, 104, 56, 0.15);
  }

  .theme-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--m3-toggle-bg);
    border: 1px solid var(--m3-toggle-border);
    color: var(--m3-on-surface);
    padding: 0.45rem 1rem;
    border-radius: 9999px; /* M3 Pill */
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    backdrop-filter: blur(12px);
    transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
    user-select: none;
  }

  .theme-toggle-btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.08);
  }

  /* Form Container */
  .form-center-box {
    width: 100%;
    max-width: 440px;
    margin: 0 auto;
  }

  .brand-logo-wrap {
    margin-bottom: 1.75rem;
  }

  .brand-logo-img {
    height: 72px;
    width: auto;
    max-width: 250px;
    object-fit: contain;
    transition: opacity 0.2s ease;
  }

  .form-header {
    margin-bottom: 1.75rem;
  }

  .form-header h1 {
    font-family: 'Outfit', sans-serif;
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--m3-on-surface);
    letter-spacing: -0.015em;
    margin-bottom: 0.35rem;
  }

  .form-header p {
    font-size: 0.9rem;
    color: var(--m3-on-surface-variant);
    line-height: 1.5;
  }

  /* Error Banner */
  .alert-box {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.35);
    color: #ef4444;
    padding: 0.85rem 1rem;
    border-radius: 16px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.65rem;
  }

  :root[data-theme="dark"] .alert-box {
    color: #fca5a5;
  }

  /* Input Groups */
  .input-field-group {
    margin-bottom: 1.25rem;
  }

  .input-label {
    display: block;
    font-size: 0.825rem;
    font-weight: 700;
    color: var(--m3-on-surface-variant);
    margin-bottom: 0.45rem;
  }

  .input-container {
    position: relative;
    display: flex;
    align-items: center;
  }

  .input-lead-icon {
    position: absolute;
    left: 1rem;
    width: 20px;
    height: 20px;
    color: var(--m3-outline);
    pointer-events: none;
    transition: color 0.2s ease;
  }

  .input-trail-btn {
    position: absolute;
    right: 0.85rem;
    background: none;
    border: none;
    color: var(--m3-outline);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem;
    border-radius: 8px;
    transition: color 0.15s ease;
  }

  .input-trail-btn:hover {
    color: var(--m3-on-surface);
  }

  input.m3-input {
    width: 100%;
    background: var(--m3-surface-container);
    border: 1px solid var(--m3-outline-variant);
    border-radius: 16px; /* M3 Squircle */
    padding: 0.85rem 1rem 0.85rem 2.85rem;
    color: var(--m3-on-surface);
    font-size: 0.925rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
  }

  input.m3-input.has-trail {
    padding-right: 2.85rem;
  }

  input.m3-input:focus {
    border-color: var(--m3-primary);
    box-shadow: 0 0 0 2px var(--m3-primary-container);
    background: var(--m3-surface);
  }

  .input-container:focus-within .input-lead-icon {
    color: var(--m3-primary);
  }

  input.m3-input::placeholder {
    color: var(--m3-outline);
    opacity: 0.8;
  }

  /* Form Meta Row (Remember & Forgot) */
  .form-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.4rem;
    margin-bottom: 1.5rem;
  }

  .checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.825rem;
    color: var(--m3-on-surface-variant);
    cursor: pointer;
    user-select: none;
    font-weight: 600;
  }

  .checkbox-label input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--m3-primary);
    cursor: pointer;
    border-radius: 4px;
  }

  .forgot-link {
    font-size: 0.825rem;
    font-weight: 700;
    color: var(--m3-primary);
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .forgot-link:hover {
    text-decoration: underline;
  }

  /* Submit Action Pill Button */
  button.submit-btn {
    width: 100%;
    border: none;
    border-radius: 9999px; /* M3 Full Pill */
    padding: 0.95rem 1.5rem;
    font-size: 0.95rem;
    font-weight: 800;
    letter-spacing: 0.3px;
    cursor: pointer;
    background: linear-gradient(135deg, #0E6838 0%, #15803d 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    box-shadow: 0 4px 14px rgba(14, 104, 56, 0.3);
    transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
  }

  :root[data-theme="dark"] button.submit-btn {
    background: linear-gradient(135deg, #4ADE80 0%, #22c55e 100%);
    color: #003A19;
    box-shadow: 0 4px 16px rgba(74, 222, 128, 0.3);
  }

  button.submit-btn:hover {
    filter: brightness(1.08);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(14, 104, 56, 0.4);
  }

  button.submit-btn:active {
    transform: translateY(0);
  }

  /* Demo Credentials Quick Switcher */
  .quick-accounts-box {
    margin-top: 1.75rem;
    background: var(--m3-surface-container);
    border: 1px dashed var(--m3-outline-variant);
    border-radius: 18px;
    padding: 1.15rem 1.25rem;
    font-size: 0.8rem;
    color: var(--m3-on-surface-variant);
    line-height: 1.6;
    transition: background 0.25s ease;
  }

  .quick-accounts-heading {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--m3-outline);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .quick-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: var(--m3-primary-container);
    color: var(--m3-on-primary-container);
    padding: 0.25rem 0.65rem;
    border-radius: 9999px; /* M3 Pill Chip */
    font-size: 0.775rem;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid rgba(14, 104, 56, 0.15);
    transition: all 0.15s ease;
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
  }

  .quick-pill:hover {
    background: var(--m3-primary);
    color: var(--m3-on-primary);
    transform: translateY(-1px);
  }

  /* Form Footer */
  .form-footer {
    margin-top: 2rem;
    font-size: 0.775rem;
    color: var(--m3-on-surface-variant);
    display: flex;
    align-items: center;
    justify-content: space-between;
    opacity: 0.85;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .security-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    color: var(--m3-primary);
    font-weight: 600;
  }

  /* =============================================================================
     RESPONSIVE BREAKPOINTS
     ============================================================================= */
  @media (max-width: 1024px) {
    .split-layout {
      flex-direction: column;
    }

    .hero-panel {
      padding: 3rem 2rem;
      min-height: 380px;
    }

    .hero-headline {
      font-size: 2rem;
    }

    .hero-cards-grid {
      grid-template-columns: 1fr;
    }

    .form-panel {
      padding: 2.5rem 1.75rem;
    }
  }

  @media (max-width: 640px) {
    .hero-panel {
      display: none; /* Mobile view: focus entirely on ergonomic login */
    }

    .form-panel {
      padding: 2rem 1.25rem;
    }
  }
</style>
</head>
<body>

  <div class="split-layout">

    <!-- =======================================================================
         LEFT: VISUAL HERO BANNER (Modern Banking Corporate Showcase)
         ======================================================================= -->
    <section class="hero-panel" aria-label="Informasi Perusahaan BPR Banda Raya">
      <div class="hero-panel-bg"></div>
      <div class="hero-panel-overlay"></div>

      <div class="hero-content">
        <!-- Top Pill Badge -->
        <div class="hero-badge-pill">
          <!-- Heroicon: shield-check -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 16px; height: 16px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
          </svg>
          <span>Sistem Informasi SDM Terpadu</span>
        </div>

        <!-- Headline & Story -->
        <div class="hero-title-box">
          <h2 class="hero-headline">
            Tata Kelola SDM Modern, <br>
            <span class="accent">Akurat & Terintegrasi</span>
          </h2>
          <p class="hero-subtext">
            Mendukung operasional perbankan yang tangguh, aman, dan akuntabel di seluruh jaringan kantor operasional PT Bank Perekonomian Rakyat Banda Raya.
          </p>
        </div>

        <!-- Glassmorphic Highlights Grid -->
        <div class="hero-cards-grid">
          <div class="hero-metric-card">
            <div class="hero-metric-icon">
              <!-- Heroicon: clock -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 22px; height: 22px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
            </div>
            <div class="hero-metric-info">
              <h4>Absensi Multi-Terminal</h4>
              <p>Biometric Solution X105 & GPS Radius Kantor Cabang</p>
            </div>
          </div>

          <div class="hero-metric-card">
            <div class="hero-metric-icon">
              <!-- Heroicon: building-office-2 -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 22px; height: 22px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
              </svg>
            </div>
            <div class="hero-metric-info">
              <h4>Jaringan Kantor Terpadu</h4>
              <p>Kantor Pusat, Botania, Aviari, & Bengkong</p>
            </div>
          </div>

          <div class="hero-metric-card">
            <div class="hero-metric-icon">
              <!-- Heroicon: calendar-days -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 22px; height: 22px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
              </svg>
            </div>
            <div class="hero-metric-info">
              <h4>Evaluasi PKWT Otomatis</h4>
              <p>Peringatan dini jatuh tempo kontrak & masa percobaan</p>
            </div>
          </div>

          <div class="hero-metric-card">
            <div class="hero-metric-icon">
              <!-- Heroicon: banknotes -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 22px; height: 22px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v10.5m0-10.5h16.5m0 0a2.25 2.25 0 0 1 2.25 2.25v10.5m-2.25-12.75V4.5a.75.75 0 0 0-.75-.75H3.75m16.5 0a2.25 2.25 0 0 1 2.25 2.25v.75m-18 0v10.5" />
              </svg>
            </div>
            <div class="hero-metric-info">
              <h4>Payroll & Klaim Terverifikasi</h4>
              <p>Kompensasi transparan & persetujuan berjenjang</p>
            </div>
          </div>
        </div>

        <!-- Regulatory Notice -->
        <div class="hero-footer-reg">
          <!-- Heroicon: shield-exclamation -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 18px; height: 18px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
          </svg>
          <span>PT Bank Perekonomian Rakyat Banda Raya berizin dan diawasi oleh Otoritas Jasa Keuangan (OJK) serta merupakan peserta penjaminan LPS.</span>
        </div>
      </div>
    </section>

    <!-- =======================================================================
         RIGHT: LOGIN FORM PANEL
         ======================================================================= -->
    <main class="form-panel">
      <!-- Top Action Bar -->
      <div class="form-top-bar">
        <div class="portal-switch-pill">
          <!-- Heroicon: lock-closed -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 13px; height: 13px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
          </svg>
          <span>Single Sign-On Terpadu</span>
        </div>

        <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" id="btn-theme-toggle" aria-label="Ganti mode tampilan">
          <span id="theme-icon" style="display: flex; align-items: center;">
            <!-- Heroicon: moon (dark mode active) -->
            <svg id="svg-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 15px; height: 15px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
            </svg>
            <!-- Heroicon: sun (light mode active) -->
            <svg id="svg-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 15px; height: 15px; display: none;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
          </span>
          <span id="theme-text">Mode Gelap</span>
        </button>
      </div>

      <!-- Center Box -->
      <div class="form-center-box">
        <!-- Official Logo -->
        <div class="brand-logo-wrap">
          <img id="brand-logo" src="{{ asset('images/logo-horizontal-clean.png') }}" alt="PT Bank Perekonomian Rakyat Banda Raya" class="brand-logo-img">
        </div>

        <div class="form-header">
          <h1>Selamat Datang Kembali</h1>
          <p>Masuk ke portal HRIS untuk mengelola administrasi kepegawaian dan layanan mandiri staf.</p>
        </div>

        <!-- Error Notification with Domain Error Codes -->
        @if ($errors->any())
          <div class="alert-box" role="alert">
            <!-- Heroicon: exclamation-circle -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 20px; height: 20px; flex-shrink: 0;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <span>{{ $errors->first() }}</span>
          </div>
        @endif

        <form method="POST" action="/login">
          @csrf

          {{-- Anti-Bot Honeypot Trap --}}
          <div style="display:none; opacity:0; position:absolute; left:-9999px;" aria-hidden="true">
            <label for="website_hp">Leave this field blank</label>
            <input type="text" name="website_hp" id="website_hp" tabindex="-1" autocomplete="off">
          </div>

          <!-- Email Input -->
          <div class="input-field-group">
            <label for="email" class="input-label">Alamat Email Kantor</label>
            <div class="input-container">
              <!-- Heroicon: envelope -->
              <svg class="input-lead-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
              </svg>
              <input id="email" name="email" type="email" class="m3-input" placeholder="nama@bprbandaraya.co.id" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
          </div>

          <!-- Password Input with Eye Toggle -->
          <div class="input-field-group">
            <label for="pass" class="input-label">Kata Sandi Akun</label>
            <div class="input-container">
              <!-- Heroicon: lock-closed -->
              <svg class="input-lead-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
              </svg>
              <input id="pass" name="password" type="password" class="m3-input has-trail" placeholder="Masukkan kata sandi" required autocomplete="current-password">
              <button type="button" class="input-trail-btn" onclick="togglePasswordVisibility()" aria-label="Lihat kata sandi">
                <!-- Heroicon: eye -->
                <svg id="eye-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 20px; height: 20px;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <!-- Heroicon: eye-slash -->
                <svg id="eye-hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 20px; height: 20px; display: none;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember & Forgot -->
          <div class="form-meta-row">
            <label class="checkbox-label">
              <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
              <span>Ingat sesi saya</span>
            </label>
            <a href="javascript:void(0)" class="forgot-link" onclick="alert('Bantuan Lupa Sandi:\nSilakan hubungi Unit IT & SDM BPR Banda Raya untuk verifikasi identitas dan reset kredensial Anda.')">Lupa sandi?</a>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="submit-btn">
            <span>Masuk ke Sistem</span>
            <!-- Heroicon: arrow-right -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 18px; height: 18px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
          </button>
        </form>

        <!-- Demo Accounts Quick Fill -->
        <div class="quick-accounts-box">
          <div class="quick-accounts-heading">
            <!-- Heroicon: key -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
            </svg>
            <span>Akses Cepat Pengujian:</span>
          </div>
          <div style="margin-bottom: 0.4rem;">
            Backoffice HRD:
            <span class="quick-pill" onclick="fillDemo('admin@bprbandaraya.co.id', 'password')" title="Klik untuk isi form">
              admin@bprbandaraya.co.id
            </span>
          </div>
          <div>
            Staf Operasional:
            <span class="quick-pill" onclick="fillDemo('fajar.nugraha@bprbandaraya.co.id', 'password')" title="Klik untuk isi form">
              fajar.nugraha@bprbandaraya.co.id
            </span>
          </div>
        </div>
      </div>

      <!-- Footer Info -->
      <footer class="form-footer">
        <div>&copy; 2026 PT Bank Perekonomian Rakyat Banda Raya.</div>
        <div class="security-indicator">
          <!-- Heroicon: lock-closed -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 13px; height: 13px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
          </svg>
          <span>Enkripsi SSL 256-Bit</span>
        </div>
      </footer>
    </main>

  </div>

  <script>
    const darkLogo = "{{ asset('images/logo-horizontal-dark.png') }}";
    const lightLogo = "{{ asset('images/logo-horizontal-clean.png') }}";

    function applyTheme(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('hris_theme', theme);

      const svgMoon = document.getElementById('svg-moon');
      const svgSun = document.getElementById('svg-sun');
      const themeText = document.getElementById('theme-text');
      const brandLogo = document.getElementById('brand-logo');

      if (theme === 'light') {
        if (svgMoon) svgMoon.style.display = 'none';
        if (svgSun) svgSun.style.display = 'block';
        if (themeText) themeText.textContent = 'Mode Terang';
        if (brandLogo) brandLogo.src = lightLogo;
      } else {
        if (svgMoon) svgMoon.style.display = 'block';
        if (svgSun) svgSun.style.display = 'none';
        if (themeText) themeText.textContent = 'Mode Gelap';
        if (brandLogo) brandLogo.src = darkLogo;
      }
    }

    function toggleTheme() {
      const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      applyTheme(newTheme);
    }

    const savedTheme = localStorage.getItem('hris_theme') || 'dark';
    applyTheme(savedTheme);

    function togglePasswordVisibility() {
      const passInput = document.getElementById('pass');
      const eyeShow = document.getElementById('eye-show');
      const eyeHide = document.getElementById('eye-hide');

      if (passInput.type === 'password') {
        passInput.type = 'text';
        eyeShow.style.display = 'none';
        eyeHide.style.display = 'block';
      } else {
        passInput.type = 'password';
        eyeShow.style.display = 'block';
        eyeHide.style.display = 'none';
      }
    }

    function fillDemo(email, password) {
      document.getElementById('email').value = email;
      document.getElementById('pass').value = password;
      document.getElementById('pass').focus();
    }
  </script>

</body>
</html>
