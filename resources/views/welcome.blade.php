<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Masuk — Core HRIS PT BPR Banda Raya</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo-tight.png') }}">

<!-- Google Fonts: Plus Jakarta Sans (Unified Across Lara-HRIS) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<script>
  (function() {
    const savedTheme = localStorage.getItem('hris_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
  })();
</script>

<style>
  /* =============================================================================
     MATERIAL 3 EXPRESSIVE - MINIMALIST SPLIT SCREEN LOGIN
     PT BANK PEREKONOMIAN RAKYAT BANDA RAYA
     Strict Typography: Plus Jakarta Sans 100%
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

    --m3-toggle-bg: rgba(255, 255, 255, 0.08);
    --m3-toggle-border: rgba(255, 255, 255, 0.14);
    --m3-card-border: rgba(255, 255, 255, 0.08);
  }

  :root[data-theme="light"] {
    --m3-bg: #E8EFEA;
    --m3-surface: #FFFFFF;
    --m3-surface-container: #DFEAE2;
    --m3-surface-container-high: #D6E3D9;
    --m3-surface-container-highest: #CBDCD0;
    --m3-outline: #5E7365;
    --m3-outline-variant: #B6CAC0;

    --m3-on-surface: #0F1E14;
    --m3-on-surface-variant: #33473B;

    --m3-primary: #0E6838;
    --m3-on-primary: #FFFFFF;
    --m3-primary-container: #CCE8D7;
    --m3-on-primary-container: #03361B;

    --m3-secondary: #EA580C;
    --m3-secondary-container: #FFEDD5;
    --m3-on-secondary-container: #7C2D12;

    --m3-toggle-bg: rgba(14, 104, 56, 0.08);
    --m3-toggle-border: #B6CAC0;
    --m3-card-border: #B6CAC0;
  }

  *, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
  }

  html, body {
    height: 100%;
    width: 100%;
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
     LEFT SIDE: CLEAN HERO BANNER (Visual & Narrative Focus)
     ============================================================================= */
  .hero-panel {
    flex: 1 1 50%;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 4.5rem 4rem;
    overflow: hidden;
    background-color: #081a10;
  }

  .hero-slides-wrapper {
    position: absolute;
    inset: 0;
    overflow: hidden;
  }

  .hero-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    transform: scale(1.05);
    transition: opacity 1.2s ease-in-out, transform 8s cubic-bezier(0.2, 0, 0, 1);
    filter: saturate(1.08) contrast(1.03);
  }

  .hero-slide.active {
    opacity: 0.92;
    transform: scale(1);
    z-index: 1;
  }

  .hero-location-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.95rem;
    border-radius: 9999px;
    background: rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 0.78rem;
    font-weight: 700;
    color: #FFFFFF;
    margin-bottom: 1.25rem;
    letter-spacing: 0.2px;
  }

  .hero-location-badge .pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #4ADE80;
    box-shadow: 0 0 8px #4ADE80;
    animation: pulseGlow 2s infinite;
  }

  @keyframes pulseGlow {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.85); }
  }

  .hero-slide-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
  }

  .hero-dot {
    width: 9px;
    height: 9px;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.35);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
  }

  .hero-dot:hover {
    background: rgba(255, 255, 255, 0.75);
  }

  .hero-dot.active {
    width: 28px;
    background: #4ADE80;
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
  }

  /* Elegant Gradient Scrim */
  .hero-panel-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      180deg,
      rgba(8, 26, 16, 0.45) 0%,
      rgba(10, 32, 20, 0.72) 40%,
      rgba(7, 18, 12, 0.96) 100%
    );
    backdrop-filter: blur(1px);
  }

  :root[data-theme="light"] .hero-panel-overlay {
    background: linear-gradient(
      180deg,
      rgba(14, 104, 56, 0.4) 0%,
      rgba(11, 79, 39, 0.75) 45%,
      rgba(4, 38, 18, 0.95) 100%
    );
  }

  .hero-content {
    position: relative;
    z-index: 10;
    max-width: 580px;
  }

  .hero-headline {
    font-size: 2.75rem;
    font-weight: 800;
    line-height: 1.18;
    letter-spacing: -0.025em;
    color: #FFFFFF;
    margin-bottom: 1.25rem;
    text-shadow: 0 4px 24px rgba(0, 0, 0, 0.45);
  }

  .hero-headline span.accent {
    background: linear-gradient(135deg, #4ADE80 0%, #FACC15 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .hero-subtext {
    font-size: 1.05rem;
    line-height: 1.65;
    color: #D1EADB;
    text-shadow: 0 2px 12px rgba(0, 0, 0, 0.35);
    font-weight: 400;
  }

  /* =============================================================================
     RIGHT SIDE: CLEAN LOGIN FORM PANEL
     ============================================================================= */
  .form-panel {
    flex: 1 1 50%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 3rem 3.5rem;
    background-color: var(--m3-bg);
    position: relative;
    overflow-y: auto;
  }

  /* Minimalist Top Bar: Just the Modern Icon Toggle Button */
  .form-top-bar {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    width: 100%;
    margin-bottom: 2rem;
  }

  /* Modern Icon-Only Theme Switcher Button */
  .modern-theme-btn {
    width: 42px;
    height: 42px;
    border-radius: 50%; /* Modern Circular / Tactile Button */
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--m3-toggle-bg);
    border: 1px solid var(--m3-toggle-border);
    color: var(--m3-on-surface);
    cursor: pointer;
    backdrop-filter: blur(12px);
    transition: all 0.25s cubic-bezier(0.2, 0, 0, 1);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  }

  .modern-theme-btn:hover {
    transform: translateY(-2px) scale(1.05);
    background: var(--m3-surface-container-high);
    border-color: var(--m3-primary);
    color: var(--m3-primary);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
  }

  .modern-theme-btn:active {
    transform: translateY(0) scale(0.98);
  }

  /* Form Container */
  .form-center-box {
    width: 100%;
    max-width: 440px;
    margin: 0 auto;
  }

  .brand-logo-wrap {
    margin-bottom: 2rem;
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
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--m3-on-surface);
    letter-spacing: -0.02em;
    margin-bottom: 0.35rem;
  }

  .form-header p {
    font-size: 0.925rem;
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
    background: #FFFFFF;
    border: 1.5px solid var(--m3-outline-variant);
    border-radius: 16px; /* M3 Squircle */
    padding: 0.85rem 1rem 0.85rem 2.85rem;
    color: var(--m3-on-surface);
    font-size: 0.925rem;
    outline: none;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
  }

  :root[data-theme="dark"] input.m3-input {
    background: var(--m3-surface-container);
    border-color: var(--m3-outline-variant);
    box-shadow: none;
  }

  input.m3-input.has-trail {
    padding-right: 2.85rem;
  }

  input.m3-input:focus {
    border-color: var(--m3-primary);
    box-shadow: 0 0 0 3px rgba(14, 104, 56, 0.16);
    background: #FFFFFF;
  }

  :root[data-theme="dark"] input.m3-input:focus {
    background: var(--m3-surface);
    box-shadow: 0 0 0 2px var(--m3-primary-container);
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

  /* Clean Minimalist Footer */
  .form-footer {
    margin-top: 2.25rem;
    font-size: 0.8rem;
    color: var(--m3-on-surface-variant);
    text-align: center;
    opacity: 0.85;
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
      min-height: 320px;
    }

    .hero-headline {
      font-size: 2.15rem;
    }

    .form-panel {
      padding: 2.5rem 1.75rem;
    }
  }

  @media (max-width: 640px) {
    .hero-panel {
      display: none; /* Focus entirely on login */
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
         LEFT: CLEAN VISUAL HERO BANNER
         Spacious, elegant, uncluttered showcase
         ======================================================================= -->
    <section class="hero-panel" aria-label="Visual Jaringan Kantor PT BPR Banda Raya">
      <div class="hero-slides-wrapper">
        <div class="hero-slide active" style="background-image: url('{{ asset('images/fotokantor/TAMPAK%20DEPAN%20KANTOR%20PUSAT%20JODOH.png') }}');" data-name="Kantor Pusat Jodoh" data-city="Nagoya / Jodoh, Batam"></div>
        <div class="hero-slide" style="background-image: url('{{ asset('images/fotokantor/TAMPAK%20DEPAN%20KANTOR%20CABANG%20BOTANIA.png') }}');" data-name="Kantor Cabang Botania" data-city="Botania Garden, Batam"></div>
        <div class="hero-slide" style="background-image: url('{{ asset('images/fotokantor/TAMPAK%20DEPAN%20KANTOR%20CABANG%20AVIARI.png') }}');" data-name="Kantor Cabang Aviari" data-city="Komp. Aviari Pratama, Batu Aji"></div>
        <div class="hero-slide" style="background-image: url('{{ asset('images/fotokantor/TAMPAK%20DEPAN%20KANTOR%20CABANG%20BENGKONG.png') }}');" data-name="Kantor Cabang Bengkong" data-city="Bengkong Indah, Batam"></div>
      </div>
      <div class="hero-panel-overlay"></div>

      <div class="hero-content">
        <div class="hero-location-badge">
          <span class="pulse-dot"></span>
          <span id="hero-office-label">Kantor Pusat Jodoh</span>
          <span id="hero-office-city" style="opacity: 0.85; font-weight: 500;">&bull; Nagoya / Jodoh, Batam</span>
        </div>

        <h2 class="hero-headline">
          Tata Kelola SDM Modern, <br>
          <span class="accent">Akurat & Terintegrasi</span>
        </h2>
        <p class="hero-subtext">
          Mendukung operasional perbankan yang tangguh, aman, dan akuntabel di seluruh jaringan kantor operasional PT Bank Perekonomian Rakyat Banda Raya.
        </p>

        <!-- Branch Slide Dots -->
        <div class="hero-slide-nav" id="hero-slide-nav">
          <button type="button" class="hero-dot active" onclick="goToSlide(0)" title="Kantor Pusat Jodoh" aria-label="Kantor Pusat Jodoh"></button>
          <button type="button" class="hero-dot" onclick="goToSlide(1)" title="Kantor Cabang Botania" aria-label="Kantor Cabang Botania"></button>
          <button type="button" class="hero-dot" onclick="goToSlide(2)" title="Kantor Cabang Aviari" aria-label="Kantor Cabang Aviari"></button>
          <button type="button" class="hero-dot" onclick="goToSlide(3)" title="Kantor Cabang Bengkong" aria-label="Kantor Cabang Bengkong"></button>
        </div>
      </div>
    </section>

    <!-- =======================================================================
         RIGHT: MINIMALIST LOGIN FORM PANEL
         ======================================================================= -->
    <main class="form-panel">
      <!-- Top Action Bar: Modern Minimalist Icon Button -->
      <div class="form-top-bar">
        <button type="button" class="modern-theme-btn" onclick="toggleTheme()" id="btn-theme-toggle" title="Ganti Mode Tampilan (Terang / Gelap)" aria-label="Ganti mode tampilan">
          <!-- Heroicon: moon (dark mode active) -->
          <svg id="svg-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 20px; height: 20px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
          </svg>
          <!-- Heroicon: sun (light mode active) -->
          <svg id="svg-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 20px; height: 20px; display: none; color: #EA580C;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
          </svg>
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
              <input id="email" name="email" type="email" class="m3-input" placeholder="nama@bprbandaraya.com" value="{{ old('email') }}" required autofocus autocomplete="username">
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
              <input id="pass" name="password" type="password" class="m3-input has-trail" placeholder="Masukkan kata sandi (default: password)" required autocomplete="current-password">
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
            <a href="javascript:void(0)" class="forgot-link" onclick="openForgotModal()">Lupa sandi?</a>
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
            <span>Akses Cepat Pengujian (Semua Password: <code>password</code>):</span>
          </div>
          <div style="margin-bottom: 0.45rem; display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center;">
            <span style="font-size: 0.72rem; font-weight: 700; width: 100%; color: var(--m3-primary-light);">Direksi & Kantor Pusat (/admin):</span>
            <span class="quick-pill" onclick="fillDemo('direksi@bprbandaraya.com', 'password')" title="Direksi (Persetujuan Final Cuti & Pengawasan)">
              direksi@bprbandaraya.com
            </span>
            <span class="quick-pill" onclick="fillDemo('admin@bprbandaraya.com', 'password')" title="Super Admin (Akses Penuh Seluruh Sistem)">
              admin@bprbandaraya.com
            </span>
            <span class="quick-pill" onclick="fillDemo('ratna.dewi@bprbandaraya.com', 'password')" title="HR Officer (Verifikasi & Personalia)">
              ratna.dewi@bprbandaraya.com
            </span>
          </div>
          <div style="margin-bottom: 0.45rem; display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center;">
            <span style="font-size: 0.72rem; font-weight: 700; width: 100%; color: var(--m3-primary-light);">Pemimpin Cabang (/admin):</span>
            <span class="quick-pill" onclick="fillDemo('pincab.botania@bprbandaraya.com', 'password')" title="Pincab KC Botania">
              pincab.botania@bprbandaraya.com
            </span>
            <span class="quick-pill" onclick="fillDemo('pincab.aviari@bprbandaraya.com', 'password')" title="Pincab KC Aviari">
              pincab.aviari@bprbandaraya.com
            </span>
            <span class="quick-pill" onclick="fillDemo('pincab.bengkong@bprbandaraya.com', 'password')" title="Pincab KC Bengkong">
              pincab.bengkong@bprbandaraya.com
            </span>
          </div>
          <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center;">
            <span style="font-size: 0.72rem; font-weight: 700; width: 100%; color: var(--m3-primary-light);">Portal Pegawai (/portal ESS - Presensi & Cuti):</span>
            <span class="quick-pill" onclick="fillDemo('teller.botania@bprbandaraya.com', 'password')" title="Teller Botania">
              teller.botania@bprbandaraya.com
            </span>
            <span class="quick-pill" onclick="fillDemo('cs.botania@bprbandaraya.com', 'password')" title="CS Botania">
              cs.botania@bprbandaraya.com
            </span>
            <span class="quick-pill" onclick="fillDemo('ao.botania@bprbandaraya.com', 'password')" title="AO Botania">
              ao.botania@bprbandaraya.com
            </span>
          </div>
        </div>
      </div>

      <!-- Clean Minimalist Footer -->
      <footer class="form-footer">
        &copy; 2026 PT Bank Perekonomian Rakyat Banda Raya. Hak Cipta Dilindungi Undang-Undang.
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
      const brandLogo = document.getElementById('brand-logo');

      if (theme === 'light') {
        if (svgMoon) svgMoon.style.display = 'none';
        if (svgSun) svgSun.style.display = 'block';
        if (brandLogo) brandLogo.src = lightLogo;
      } else {
        if (svgMoon) svgMoon.style.display = 'block';
        if (svgSun) svgSun.style.display = 'none';
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

    // Interactive Slideshow for BPR Banda Raya Office Photos
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    const labelEl = document.getElementById('hero-office-label');
    const cityEl = document.getElementById('hero-office-city');

    function goToSlide(index) {
      if (!slides.length) return;
      slides[currentSlide].classList.remove('active');
      if (dots[currentSlide]) dots[currentSlide].classList.remove('active');

      currentSlide = (index + slides.length) % slides.length;

      slides[currentSlide].classList.add('active');
      if (dots[currentSlide]) dots[currentSlide].classList.add('active');

      if (labelEl) labelEl.textContent = slides[currentSlide].getAttribute('data-name');
      if (cityEl) cityEl.textContent = '• ' + slides[currentSlide].getAttribute('data-city');
    }

    if (slides.length > 1) {
      setInterval(() => {
        goToSlide(currentSlide + 1);
      }, 6000);
    }
  </script>

  <!-- Forgot Password Security Modal -->
  <div id="forgotPasswordModal" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.55); backdrop-filter: blur(4px); padding: 1.25rem;">
    <div style="background: #ffffff; border-radius: 24px; max-width: 480px; width: 100%; padding: 2rem; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25); text-align: left; animation: modalPop 0.25s ease-out;">
      <div style="display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1.2rem;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(14, 104, 56, 0.12); color: #0E6838; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 24px; height: 24px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
          </svg>
        </div>
        <div>
          <h3 style="font-size: 1.15rem; font-weight: 800; color: #111827; margin: 0;">Bantuan Pemulihan Kata Sandi</h3>
          <p style="font-size: 0.8rem; color: #6b7280; margin: 0.15rem 0 0 0;">Prosedur Keamanan Akun Core HRIS BPR</p>
        </div>
      </div>
      <div style="font-size: 0.875rem; color: #374151; line-height: 1.6; margin-bottom: 1.5rem; background: #f9fafb; padding: 1.1rem; border-radius: 16px; border: 1px solid #e5e7eb;">
        <p style="margin: 0 0 0.75rem 0;">Demi menjaga kepatuhan tata kelola teknologi perbankan, penyetelan ulang kata sandi dilakukan secara terverifikasi:</p>
        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.825rem; color: #4b5563;">
          <li style="margin-bottom: 0.35rem;">Hubungi <strong>Unit IT & Administrator Sistem</strong> di Kantor Pusat Nagoya Hill.</li>
          <li style="margin-bottom: 0.35rem;">Atau informasikan ke <strong>Unit SDM / HR BPR Banda Raya</strong> untuk verifikasi NIP Anda.</li>
          <li>Default kata sandi reset baru dapat langsung digunakan untuk login ulang.</li>
        </ul>
      </div>
      <div style="display: flex; justify-content: flex-end;">
        <button type="button" onclick="closeForgotModal()" style="padding: 0.65rem 1.4rem; border-radius: 12px; background: #0E6838; color: #ffffff; font-weight: 700; font-size: 0.875rem; border: none; cursor: pointer;">
          Saya Mengerti
        </button>
      </div>
    </div>
  </div>

  <style>
    @keyframes modalPop {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
  </style>

  <script>
    function openForgotModal() {
      const modal = document.getElementById('forgotPasswordModal');
      if (modal) modal.style.display = 'flex';
    }
    function closeForgotModal() {
      const modal = document.getElementById('forgotPasswordModal');
      if (modal) modal.style.display = 'none';
    }
    window.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeForgotModal();
    });
  </script>
</body>
</html>
