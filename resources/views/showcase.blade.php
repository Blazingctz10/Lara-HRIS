<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Evaluasi Desain: Material 3 Expressive vs Liquid Glass | PT BPR Banda Raya</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logo-tight.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&family=Roboto+Flex:opsz,wght@8..144,300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* ==========================================================================
       GLOBAL CORE & SWITCHER STYLES
       ========================================================================== */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      min-height: 100vh;
      line-height: 1.5;
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      transition: background-color 0.25s ease, color 0.25s ease;
      overflow-x: hidden;
    }

    /* Fixed Evaluation Header Bar */
    .eval-sticky-header {
      position: sticky;
      top: 0;
      z-index: 999;
      background: rgba(10, 16, 12, 0.94);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.12);
      padding: 0.75rem 1.5rem;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    [data-theme="light"] .eval-sticky-header {
      background: rgba(255, 255, 255, 0.94);
      border-bottom: 1px solid rgba(14, 104, 56, 0.15);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .eval-brand {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .eval-brand-badge {
      background: linear-gradient(135deg, #0E6838 0%, #15803d 100%);
      color: #ffffff;
      padding: 0.35rem 0.65rem;
      border-radius: 8px;
      font-size: 0.75rem;
      font-weight: 800;
      letter-spacing: 0.5px;
    }

    .eval-brand-title {
      font-size: 0.95rem;
      font-weight: 800;
      color: #ffffff;
    }

    [data-theme="light"] .eval-brand-title {
      color: #0f172a;
    }

    .eval-tabs-group {
      display: flex;
      align-items: center;
      background: rgba(255, 255, 255, 0.08);
      padding: 4px;
      border-radius: 9999px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      gap: 4px;
    }

    [data-theme="light"] .eval-tabs-group {
      background: #e2e8e4;
      border-color: rgba(0, 0, 0, 0.06);
    }

    .eval-tab-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.45rem 1.15rem;
      border-radius: 9999px;
      font-size: 0.8rem;
      font-weight: 700;
      color: #94a3b8;
      border: none;
      background: transparent;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
      user-select: none;
    }

    [data-theme="light"] .eval-tab-btn {
      color: #475569;
    }

    .eval-tab-btn.active {
      background: #0E6838;
      color: #ffffff;
      box-shadow: 0 4px 14px rgba(14, 104, 56, 0.4);
    }

    .eval-tab-btn.active.liquid-btn {
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.05) 100%);
      color: #38bdf8;
      border: 1px solid rgba(56, 189, 248, 0.4);
      backdrop-filter: blur(12px);
      box-shadow: 0 0 15px rgba(56, 189, 248, 0.3);
    }

    .eval-controls {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .eval-theme-toggle {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.14);
      color: inherit;
      padding: 0.45rem 0.85rem;
      border-radius: 9999px;
      font-size: 0.775rem;
      font-weight: 700;
      cursor: pointer;
    }

    [data-theme="light"] .eval-theme-toggle {
      background: #ffffff;
      border-color: #cbd5e1;
      color: #0f172a;
    }

    /* Tab Content Wrappers */
    .tab-view-container {
      display: none;
      width: 100%;
      animation: fadeIn 0.3s cubic-bezier(0.2, 0, 0, 1);
    }

    .tab-view-container.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ==========================================================================
       1. PURE MATERIAL 3 EXPRESSIVE STYLING (Google M3 Standards)
       ========================================================================== */
    .theme-m3 {
      --m3-primary: #0E6838;
      --m3-on-primary: #ffffff;
      --m3-primary-container: #bbf7d0;
      --m3-on-primary-container: #00210e;
      --m3-secondary: #ea580c;
      --m3-secondary-container: #ffedd5;
      --m3-tertiary: #ca8a04;
      --m3-tertiary-container: #fef08a;

      --m3-surface: #f8faf9;
      --m3-surface-container-low: #f0f4f1;
      --m3-surface-container: #e7ece8;
      --m3-surface-container-high: #dfe5e1;
      --m3-surface-container-highest: #d7ded9;
      --m3-on-surface: #141c17;
      --m3-on-surface-variant: #49544d;
      --m3-outline: #727f77;
      --m3-outline-variant: #c3cec7;
      --m3-card-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
      --m3-elevated-shadow: 0 4px 12px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.05);

      background-color: var(--m3-surface);
      color: var(--m3-on-surface);
      padding: 2.5rem 1.5rem 5rem;
    }

    [data-theme="dark"] .theme-m3 {
      --m3-primary: #4ade80;
      --m3-on-primary: #003916;
      --m3-primary-container: #065f2c;
      --m3-on-primary-container: #bbf7d0;
      --m3-secondary: #fb923c;
      --m3-secondary-container: #7c2d12;
      --m3-tertiary: #facc15;
      --m3-tertiary-container: #713f12;

      --m3-surface: #0f1511;
      --m3-surface-container-low: #141b16;
      --m3-surface-container: #19211c;
      --m3-surface-container-high: #1e2822;
      --m3-surface-container-highest: #243029;
      --m3-on-surface: #e1e7e2;
      --m3-on-surface-variant: #a4b1a8;
      --m3-outline: #818e86;
      --m3-outline-variant: #3a453e;
      --m3-card-shadow: 0 2px 6px rgba(0,0,0,0.4);
      --m3-elevated-shadow: 0 8px 24px rgba(0,0,0,0.5);
    }

    .m3-layout {
      max-width: 1280px;
      margin: 0 auto;
    }

    .m3-card {
      background: var(--m3-surface-container-low);
      border-radius: 28px;
      padding: 1.75rem;
      border: 1px solid var(--m3-outline-variant);
      box-shadow: var(--m3-card-shadow);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .m3-card:hover {
      box-shadow: var(--m3-elevated-shadow);
    }

    .m3-pill-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      border-radius: 9999px;
      font-size: 0.875rem;
      font-weight: 700;
      cursor: pointer;
      border: none;
      transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
      user-select: none;
    }

    .m3-btn-filled {
      background: var(--m3-primary);
      color: var(--m3-on-primary);
    }

    .m3-btn-filled:hover {
      filter: brightness(1.1);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(14, 104, 56, 0.35);
    }

    .m3-btn-tonal {
      background: var(--m3-surface-container-high);
      color: var(--m3-on-surface);
    }

    .m3-btn-tonal:hover {
      background: var(--m3-surface-container-highest);
    }

    .m3-select {
      width: 100%;
      background: var(--m3-surface-container);
      border: 1.5px solid var(--m3-outline-variant);
      border-radius: 16px;
      padding: 0.85rem 1.15rem;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--m3-on-surface);
      cursor: pointer;
      outline: none;
      appearance: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .m3-select:focus {
      border-color: var(--m3-primary);
      box-shadow: 0 0 0 3px rgba(14, 104, 56, 0.2);
    }

    /* ==========================================================================
       2. PURE LIQUID GLASS STYLING (Apple-Style Glassmorphism)
       ========================================================================== */
    .theme-liquid-glass {
      position: relative;
      background: #08110b;
      color: #f1f5f3;
      padding: 2.5rem 1.5rem 5rem;
      min-height: 100vh;
      overflow: hidden;
    }

    [data-theme="light"] .theme-liquid-glass {
      background: #e9f0ec;
      color: #0c1a11;
    }

    /* Ambient Fluid Orbs */
    .liquid-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      pointer-events: none;
      opacity: 0.6;
      animation: floatOrb 12s infinite alternate ease-in-out;
    }

    .liquid-orb-1 {
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, #0E6838 0%, rgba(14, 104, 56, 0.1) 70%);
      top: -100px;
      left: 10%;
    }

    .liquid-orb-2 {
      width: 450px;
      height: 450px;
      background: radial-gradient(circle, #F37A20 0%, rgba(243, 122, 32, 0.1) 70%);
      bottom: 5%;
      right: 5%;
      animation-duration: 16s;
    }

    .liquid-orb-3 {
      width: 350px;
      height: 350px;
      background: radial-gradient(circle, #0284c7 0%, rgba(2, 132, 199, 0.1) 70%);
      top: 40%;
      right: 35%;
      animation-duration: 10s;
    }

    @keyframes floatOrb {
      0% { transform: translate(0, 0) scale(1); }
      100% { transform: translate(40px, 30px) scale(1.1); }
    }

    .glass-panel {
      position: relative;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(32px) saturate(200%);
      -webkit-backdrop-filter: blur(32px) saturate(200%);
      border: 1px solid rgba(255, 255, 255, 0.16);
      border-radius: 24px;
      padding: 1.85rem;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.35);
      overflow: hidden;
    }

    [data-theme="light"] .glass-panel {
      background: rgba(255, 255, 255, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.8);
      box-shadow: 0 20px 40px -10px rgba(14, 104, 56, 0.15), inset 0 1px 0 #ffffff;
    }

    .glass-panel::after {
      content: '';
      position: absolute;
      top: 0;
      left: 15%;
      right: 15%;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
    }

    .glass-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1.6rem;
      border-radius: 16px;
      font-size: 0.875rem;
      font-weight: 700;
      cursor: pointer;
      background: linear-gradient(135deg, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0.04) 100%);
      border: 1px solid rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(20px);
      color: #ffffff;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.5);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    [data-theme="light"] .glass-btn {
      color: #064e26;
      background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.5) 100%);
      box-shadow: 0 8px 20px rgba(14, 104, 56, 0.15);
    }

    .glass-btn:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 12px 30px rgba(14, 104, 56, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .glass-select {
      width: 100%;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(16px);
      border: 1.5px solid rgba(255, 255, 255, 0.22);
      border-radius: 16px;
      padding: 0.85rem 1.15rem;
      font-size: 0.9rem;
      font-weight: 600;
      color: inherit;
      outline: none;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    /* ==========================================================================
       3. SPLIT SCREEN COMPARISON STYLING
       ========================================================================== */
    .split-wrapper {
      max-width: 1440px;
      margin: 0 auto;
      padding: 2rem 1.5rem 5rem;
    }

    .split-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 2rem;
    }

    @media (min-width: 1024px) {
      .split-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    .split-col {
      display: flex;
      flex-direction: column;
      gap: 1.75rem;
    }

    .split-col-header {
      padding: 1rem 1.5rem;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    /* Comparison Scorecard */
    .eval-scorecard {
      margin-top: 3.5rem;
      border-radius: 24px;
      padding: 2.25rem;
      background: rgba(14, 26, 18, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(24px);
    }

    [data-theme="light"] .eval-scorecard {
      background: #ffffff;
      border-color: #cbd5e1;
      box-shadow: 0 12px 36px rgba(0, 0, 0, 0.06);
    }

    .scorecard-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1.5rem;
      font-size: 0.875rem;
    }

    .scorecard-table th {
      text-align: left;
      padding: 0.85rem 1rem;
      border-bottom: 2px solid rgba(255, 255, 255, 0.15);
      font-weight: 800;
      color: #38bdf8;
    }

    [data-theme="light"] .scorecard-table th {
      border-bottom-color: #e2e8e4;
      color: #0E6838;
    }

    .scorecard-table td {
      padding: 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      vertical-align: top;
    }

    [data-theme="light"] .scorecard-table td {
      border-bottom-color: #f1f5f2;
    }
  </style>
</head>
<body>

  <!-- ==========================================================================
       STICKY EVALUATION CONTROLLER BAR
       ========================================================================== -->
  <header class="eval-sticky-header">
    <div class="eval-brand">
      <span class="eval-brand-badge">BPR BANDA RAYA</span>
      <div class="eval-brand-title">Lab Evaluasi Desain UI HRIS</div>
    </div>

    <!-- Center Tab Switcher -->
    <div class="eval-tabs-group">
      <button type="button" class="eval-tab-btn {{ ($design ?? '') === 'material3' ? 'active' : '' }}" onclick="switchView('material3', this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
        </svg>
        <span>1. Material 3 Expressive</span>
      </button>

      <button type="button" class="eval-tab-btn {{ ($design ?? '') === 'liquid-glass' ? 'active liquid-btn' : '' }}" onclick="switchView('liquid-glass', this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
        </svg>
        <span>2. Liquid Glass</span>
      </button>

      <button type="button" class="eval-tab-btn {{ ($design ?? '') === 'split' ? 'active' : '' }}" onclick="switchView('split', this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-10.875 0h17.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125Z" />
        </svg>
        <span>3. Split Screen (Bandingkan)</span>
      </button>
    </div>

    <!-- Controls -->
    <div class="eval-controls">
      <button type="button" class="eval-theme-toggle" onclick="toggleEvalTheme()">
        <span id="theme-btn-text">Mode Gelap</span>
      </button>
      <a href="{{ url('/') }}" style="color: #94a3b8; font-size: 0.8rem; text-decoration: none; font-weight: 600;">
        Ke Halaman Login &rarr;
      </a>
    </div>
  </header>


  <!-- ==========================================================================
       VIEW 1: MURNI MATERIAL 3 EXPRESSIVE (GOOGLE STANDARDS)
       ========================================================================== -->
  <div id="view-material3" class="tab-view-container theme-m3 {{ ($design ?? '') === 'material3' ? 'active' : '' }}">
    <div class="m3-layout">
      <!-- M3 Hero Header -->
      <div style="margin-bottom: 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 1rem;">
        <div>
          <span style="display: inline-block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; background: var(--m3-primary-container); color: var(--m3-on-primary-container); padding: 0.35rem 0.85rem; border-radius: 9999px; margin-bottom: 0.75rem;">
            Desain Opsi 1 &bull; Material 3 Expressive
          </span>
          <h1 style="font-family: 'Outfit', sans-serif; font-size: 2.25rem; font-weight: 800; letter-spacing: -0.02em;">
            Portal Human Capital Bank BPR Banda Raya
          </h1>
          <p style="color: var(--m3-on-surface-variant); font-size: 0.95rem; margin-top: 0.25rem;">
            Fokus pada <strong>permukaan solid bertingkat (elevation layers)</strong>, keterbacaan tinggi, bentuk pil fleksibel, dan interaksi taktil perbankan.
          </p>
        </div>

        <div style="display: flex; gap: 0.65rem;">
          <button type="button" class="m3-pill-btn m3-btn-tonal">
            Ekspor Laporan SDM
          </button>
          <button type="button" class="m3-pill-btn m3-btn-filled">
            + Tambah Pegawai Baru
          </button>
        </div>
      </div>

      <!-- M3 KPI Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="m3-card">
          <div style="font-size: 0.8rem; font-weight: 700; color: var(--m3-on-surface-variant); margin-bottom: 0.5rem;">TOTAL PEGAWAI TETAP & KONTRAK</div>
          <div style="font-size: 2.25rem; font-weight: 800; font-family: 'Outfit', sans-serif; color: var(--m3-primary);">{{ $totalEmployees }} Orang</div>
          <div style="font-size: 0.75rem; color: var(--m3-on-surface-variant); margin-top: 0.5rem; font-weight: 600;">Terbagi di 4 Jaringan Kantor Cabang</div>
        </div>

        <div class="m3-card">
          <div style="font-size: 0.8rem; font-weight: 700; color: var(--m3-on-surface-variant); margin-bottom: 0.5rem;">LOG PRESENSI FINGERPRINT HARI INI</div>
          <div style="font-size: 2.25rem; font-weight: 800; font-family: 'Outfit', sans-serif; color: #0284c7;">{{ $todayAttendances }} Log</div>
          <div style="font-size: 0.75rem; color: #0284c7; margin-top: 0.5rem; font-weight: 700;">Terminal Solution X105 Terhubung</div>
        </div>

        <div class="m3-card">
          <div style="font-size: 0.8rem; font-weight: 700; color: var(--m3-on-surface-variant); margin-bottom: 0.5rem;">PERMOHONAN CUTI / BLOCK LEAVE</div>
          <div style="font-size: 2.25rem; font-weight: 800; font-family: 'Outfit', sans-serif; color: var(--m3-secondary);">{{ $pendingLeaves }} Berkas</div>
          <div style="font-size: 0.75rem; color: var(--m3-secondary); margin-top: 0.5rem; font-weight: 700;">Perlu Approval Pincab & HR</div>
        </div>

        <div class="m3-card">
          <div style="font-size: 0.8rem; font-weight: 700; color: var(--m3-on-surface-variant); margin-bottom: 0.5rem;">PENGGAJIAN BULANAN (NET PAYROLL)</div>
          <div style="font-size: 1.7rem; font-weight: 800; font-family: 'Outfit', sans-serif; color: #16a34a;">Rp {{ number_format($totalPayrolls, 0, ',', '.') }}</div>
          <div style="font-size: 0.75rem; color: #16a34a; margin-top: 0.5rem; font-weight: 700;">Status: Siap Ditransfer</div>
        </div>
      </div>

      <!-- M3 Form Showcase (Cleaning up the Emoji Select Dropdown) -->
      <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.75rem; margin-bottom: 2rem;">
        <div class="m3-card">
          <h2 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.35rem;">Formulir Pengajuan Klaim Operasional (Material 3)</h2>
          <p style="font-size: 0.825rem; color: var(--m3-on-surface-variant); margin-bottom: 1.5rem;">
            Tampilan dropdown select bersih tanpa emoji, menggunakan icon SVG berstandar Material Symbols.
          </p>

          <div style="display: flex; flex-direction: column; gap: 1.2rem;">
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.4rem; color: var(--m3-on-surface);">
                Kategori Klaim Pengeluaran
              </label>
              <div style="position: relative;">
                <select class="m3-select">
                  <option selected>Bensin & Transport Account Officer</option>
                  <option>Survei Lapangan Calon Debitur UMKM</option>
                  <option>Tunjangan Medis / Kacamata</option>
                  <option>Konsumsi Lembur Closing Bulanan</option>
                  <option>Operasional Kas & Alat Tulis Cabang</option>
                </select>
                <div style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--m3-outline);">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </div>
              </div>
            </div>

            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.4rem; color: var(--m3-on-surface);">
                Status Approval (Material 3 Tonal Filter Chips)
              </label>
              <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <span style="background: var(--m3-primary-container); color: var(--m3-on-primary-container); padding: 0.45rem 1rem; border-radius: 9999px; font-size: 0.775rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" style="width: 12px; height: 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                  Menunggu Approval Pincab
                </span>
                <span style="background: var(--m3-surface-container); color: var(--m3-on-surface-variant); padding: 0.45rem 1rem; border-radius: 9999px; font-size: 0.775rem; font-weight: 700;">
                  Disetujui Pincab
                </span>
                <span style="background: var(--m3-surface-container); color: var(--m3-on-surface-variant); padding: 0.45rem 1rem; border-radius: 9999px; font-size: 0.775rem; font-weight: 700;">
                  Sudah Dibayarkan / Kasir
                </span>
              </div>
            </div>

            <div>
              <button type="button" class="m3-pill-btn m3-btn-filled" style="width: 100%; justify-content: center; padding: 0.85rem;">
                Simpan Pengajuan Reimbursement
              </button>
            </div>
          </div>
        </div>

        <div class="m3-card" style="background: var(--m3-surface-container);">
          <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">Karakteristik Material 3 Expressive:</h3>
          <ul style="font-size: 0.85rem; color: var(--m3-on-surface-variant); display: flex; flex-direction: column; gap: 0.65rem; padding-left: 1.25rem;">
            <li><strong>Solid & Tactile</strong>: Menggunakan kontainer latar solid dengan variasi kontras tonal (*surface-container-low/high*).</li>
            <li><strong>Bentuk Sudut Pil</strong>: Tombol FAB, *action chips*, dan *badges* berbentuk kapsul 9999px.</li>
            <li><strong>Keterbacaan Sangat Tinggi</strong>: Sangat nyaman untuk staf operasional cabang (Teller, CS, Kasir) yang bekerja intensif di depan monitor sepanjang hari.</li>
            <li><strong>Respon Cepat</strong>: Tidak membebani kartu grafis karena tidak memerlukan kalkulasi *heavy blur shader*.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>


  <!-- ==========================================================================
       VIEW 2: MURNI LIQUID GLASS (APPLE GLASSMORPHISM)
       ========================================================================== -->
  <div id="view-liquid-glass" class="tab-view-container theme-liquid-glass {{ ($design ?? '') === 'liquid-glass' ? 'active' : '' }}">
    <!-- Dynamic Ambient Fluid Mesh Orbs -->
    <div class="liquid-orb liquid-orb-1"></div>
    <div class="liquid-orb liquid-orb-2"></div>
    <div class="liquid-orb liquid-orb-3"></div>

    <div style="max-width: 1280px; margin: 0 auto; position: relative; z-index: 2;">
      <!-- Glass Hero Header -->
      <div class="glass-panel" style="margin-bottom: 2rem; padding: 2.25rem;">
        <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.725rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(56, 189, 248, 0.18); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); padding: 0.35rem 0.85rem; border-radius: 9999px; margin-bottom: 0.75rem;">
          Desain Opsi 2 &bull; Liquid Glass Architecture
        </span>
        <h1 style="font-family: 'Outfit', sans-serif; font-size: 2.35rem; font-weight: 900; letter-spacing: -0.03em;">
          Translucent Core HRIS Bank BPR Banda Raya
        </h1>
        <p style="font-size: 0.95rem; opacity: 0.85; max-width: 720px; line-height: 1.6; margin-top: 0.4rem;">
          Efek refraksi kaca optik (*backdrop-filter blur 32px*), refleksi cahaya specular 1px pada batas atas kartu, serta orba gradien mengambang dinamis.
        </p>
      </div>

      <!-- Glass KPI Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="glass-panel">
          <div style="font-size: 0.775rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75; margin-bottom: 0.4rem;">Total SDM Terdaftar</div>
          <div style="font-size: 2.25rem; font-weight: 900; font-family: 'Outfit', sans-serif; color: #4ade80;">{{ $totalEmployees }} Pegawai</div>
          <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.4rem;">4 Kantor Cabang Aktif</div>
        </div>

        <div class="glass-panel">
          <div style="font-size: 0.775rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75; margin-bottom: 0.4rem;">Presensi Fingerprint</div>
          <div style="font-size: 2.25rem; font-weight: 900; font-family: 'Outfit', sans-serif; color: #38bdf8;">{{ $todayAttendances }} Log</div>
          <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.4rem;">Solution X105 Sync Realtime</div>
        </div>

        <div class="glass-panel">
          <div style="font-size: 0.775rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75; margin-bottom: 0.4rem;">Block Leave OJK / Cuti</div>
          <div style="font-size: 2.25rem; font-weight: 900; font-family: 'Outfit', sans-serif; color: #fb923c;">{{ $pendingLeaves }} Berkas</div>
          <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.4rem;">Kepatuhan Perbankan OJK</div>
        </div>

        <div class="glass-panel">
          <div style="font-size: 0.775rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75; margin-bottom: 0.4rem;">Estimasi Slip Payroll</div>
          <div style="font-size: 1.7rem; font-weight: 900; font-family: 'Outfit', sans-serif; color: #facc15;">Rp {{ number_format($totalPayrolls, 0, ',', '.') }}</div>
          <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.4rem;">Arsip Gaji Terenkripsi</div>
        </div>
      </div>

      <!-- Glass Form Demo -->
      <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.75rem; margin-bottom: 2rem;">
        <div class="glass-panel">
          <h2 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.25rem;">Formulir Transparan Liquid Glass</h2>
          <p style="font-size: 0.825rem; opacity: 0.8; margin-bottom: 1.5rem;">
            Permukaan input semi-transparan dengan specular top reflection dan icon vektor SVG murni.
          </p>

          <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.4rem;">
                Kategori Klaim Pengeluaran
              </label>
              <select class="glass-select">
                <option style="background: #111e15; color: #ffffff;">Bensin & Transport Account Officer</option>
                <option style="background: #111e15; color: #ffffff;">Survei Lapangan Calon Debitur UMKM</option>
                <option style="background: #111e15; color: #ffffff;">Tunjangan Medis / Kacamata</option>
                <option style="background: #111e15; color: #ffffff;">Konsumsi Lembur Closing Bulanan</option>
                <option style="background: #111e15; color: #ffffff;">Operasional Kas & Alat Tulis Cabang</option>
              </select>
            </div>

            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.4rem;">
                Status Approval (Glowing Neon Pills)
              </label>
              <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <span style="background: rgba(243, 122, 32, 0.2); border: 1px solid rgba(243, 122, 32, 0.5); color: #fb923c; padding: 0.4rem 0.9rem; border-radius: 9999px; font-size: 0.775rem; font-weight: 700; box-shadow: 0 0 12px rgba(243, 122, 32, 0.25);">
                  Menunggu Approval Pincab
                </span>
                <span style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); opacity: 0.7; padding: 0.4rem 0.9rem; border-radius: 9999px; font-size: 0.775rem; font-weight: 700;">
                  Disetujui Pincab
                </span>
                <span style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); opacity: 0.7; padding: 0.4rem 0.9rem; border-radius: 9999px; font-size: 0.775rem; font-weight: 700;">
                  Sudah Dibayarkan / Kasir
                </span>
              </div>
            </div>

            <div>
              <button type="button" class="glass-btn" style="width: 100%; justify-content: center; padding: 0.85rem;">
                Simpan Pengajuan Reimbursement
              </button>
            </div>
          </div>
        </div>

        <div class="glass-panel" style="background: rgba(255, 255, 255, 0.03);">
          <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">Karakteristik Liquid Glass:</h3>
          <ul style="font-size: 0.85rem; opacity: 0.85; display: flex; flex-direction: column; gap: 0.65rem; padding-left: 1.25rem;">
            <li><strong>Ultra Modern & Mewah (Futuristik)</strong>: Memberikan kesan sistem teknologi finansial mutakhir tingkat tinggi (*Fintech-grade*).</li>
            <li><strong>Kedalaman Optik Kaca</strong>: Efek pencahayaan lembut membuat antarmuka terasa hidup dan tidak membosankan.</li>
            <li><strong>Specular Highlight</strong>: Pantulan garis kilau 1px di atas setiap kontainer memberikan tekstur fisik kaca cair.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>


  <!-- ==========================================================================
       VIEW 3: SPLIT SCREEN (BANDINGKAN BERDAMPINGAN SECARA LANGSUNG)
       ========================================================================== -->
  <div id="view-split" class="tab-view-container {{ ($design ?? '') === 'split' ? 'active' : '' }}" style="background: #090e0b; color: #ffffff;">
    <div class="split-wrapper">
      <div style="text-align: center; margin-bottom: 2.5rem;">
        <span style="background: rgba(14, 104, 56, 0.25); color: #34d399; border: 1px solid rgba(14, 104, 56, 0.5); padding: 0.35rem 0.95rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
          Perbandingan Berdampingan (Head-to-Head)
        </span>
        <h1 style="font-size: 2.25rem; font-weight: 900; margin-top: 0.5rem; letter-spacing: -0.02em;">
          Material 3 Expressive vs Liquid Glass
        </h1>
        <p style="color: #94a89d; font-size: 0.95rem; max-width: 650px; margin: 0.4rem auto 0;">
          Lihat perbedaan karakter kedua gaya desain pada komponen yang sama untuk menentukan gaya mana yang paling cocok bagi operasional PT BPR Banda Raya.
        </p>
      </div>

      <!-- Side-by-Side Grid -->
      <div class="split-grid">

        <!-- KOLOM KIRI: MATERIAL 3 EXPRESSIVE -->
        <div class="split-col" style="background: #141c17; padding: 2rem; border-radius: 28px; border: 1.5px solid #28372d;">
          <div class="split-col-header" style="background: #1c2720; border: 1px solid #2e3e33;">
            <div>
              <div style="font-weight: 800; color: #4ade80; font-size: 1.05rem;">1. Material 3 Expressive</div>
              <div style="font-size: 0.75rem; color: #94a3b8;">Google Design System &bull; Tonal Solid Surfaces</div>
            </div>
            <span style="font-size: 0.7rem; font-weight: 800; background: #064e26; color: #86efac; padding: 0.25rem 0.65rem; border-radius: 9999px;">
              SOLID & STRUKTUR
            </span>
          </div>

          <!-- M3 Card Example -->
          <div style="background: #19231c; border-radius: 24px; padding: 1.5rem; border: 1px solid #29382d;">
            <div style="font-size: 0.75rem; font-weight: 700; color: #94a89d; text-transform: uppercase;">Komponen Kartu Metrik</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: #4ade80; margin: 0.25rem 0;">{{ $totalEmployees }} Pegawai</div>
            <span style="font-size: 0.725rem; color: #94a89d;">Permukaan warna solid bertingkat, tidak tembus pandang</span>
          </div>

          <!-- M3 Dropdown Example -->
          <div style="background: #19231c; border-radius: 24px; padding: 1.5rem; border: 1px solid #29382d;">
            <div style="font-size: 0.8rem; font-weight: 700; margin-bottom: 0.5rem; color: #e1e7e2;">Pilihan Kategori (Bebas Emoji)</div>
            <div style="background: #202c24; border: 1.5px solid #36483c; border-radius: 14px; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600; color: #f1f5f3; display: flex; align-items: center; justify-content: space-between;">
              <span>Bensin & Transport AO</span>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px; color: #94a89d;"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
            </div>
          </div>

          <!-- M3 Button Example -->
          <button type="button" class="m3-pill-btn m3-btn-filled" style="justify-content: center; width: 100%; padding: 0.85rem; background: #4ade80; color: #052e16;">
            Tombol Aksi Pil (Material 3)
          </button>
        </div>


        <!-- KOLOM KANAN: LIQUID GLASS -->
        <div class="split-col" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(28px); padding: 2rem; border-radius: 28px; border: 1px solid rgba(255, 255, 255, 0.16); box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
          <div class="split-col-header" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.2);">
            <div>
              <div style="font-weight: 800; color: #38bdf8; font-size: 1.05rem;">2. Liquid Glass</div>
              <div style="font-size: 0.75rem; color: #94a3b8;">Apple Glassmorphism &bull; Refraksi Kaca Transparan</div>
            </div>
            <span style="font-size: 0.7rem; font-weight: 800; background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px;">
              TRANSPARAN & MEWAH
            </span>
          </div>

          <!-- Glass Card Example -->
          <div class="glass-panel" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.7; text-transform: uppercase;">Komponen Kartu Metrik</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: #38bdf8; margin: 0.25rem 0;">{{ $totalEmployees }} Pegawai</div>
            <span style="font-size: 0.725rem; opacity: 0.7;">Permukaan kaca transparan tembus cahaya dengan refleksi specular</span>
          </div>

          <!-- Glass Dropdown Example -->
          <div class="glass-panel" style="padding: 1.5rem;">
            <div style="font-size: 0.8rem; font-weight: 700; margin-bottom: 0.5rem;">Pilihan Kategori (Bebas Emoji)</div>
            <div style="background: rgba(255,255,255,0.08); border: 1.5px solid rgba(255,255,255,0.25); border-radius: 14px; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
              <span>Bensin & Transport AO</span>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px; opacity: 0.7;"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
            </div>
          </div>

          <!-- Glass Button Example -->
          <button type="button" class="glass-btn" style="justify-content: center; width: 100%; padding: 0.85rem;">
            Tombol Aksi Kaca Cair (Liquid Glass)
          </button>
        </div>

      </div>

      <!-- ==========================================================================
           TABEL EVALUASI KOMPARASI (SCORECARD)
           ========================================================================== -->
      <div class="eval-scorecard">
        <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 0.35rem;">Tabel Evaluasi & Rekomendasi untuk PT BPR Banda Raya</h3>
        <p style="font-size: 0.85rem; opacity: 0.8;">
          Gunakan matriks perbandingan ini untuk menentukan desain mana yang akan diaplikasikan ke seluruh modul:
        </p>

        <table class="scorecard-table">
          <thead>
            <tr>
              <th style="width: 25%;">Kriteria Penilaian</th>
              <th style="width: 37.5%; color: #4ade80;">1. Material 3 Expressive</th>
              <th style="width: 37.5%; color: #38bdf8;">2. Liquid Glass</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Kenyamanan Staf (Ergonomi)</strong></td>
              <td><strong>Sangat Tinggi (9.5/10)</strong> — Kontras warna solid tidak membuat mata cepat lelah saat input data nasabah/teller harian.</td>
              <td><strong>Tinggi (8.5/10)</strong> — Estetika luar biasa, namun membutuhkan kontras teks yang tepat di atas latar belakang transparan.</td>
            </tr>
            <tr>
              <td><strong>Kesan Korporat Perbankan</strong></td>
              <td><strong>Resmi & Kredibel (9.2/10)</strong> — Memberikan impresi aplikasi perbankan konvensional yang patuh regulasi dan kokoh.</td>
              <td><strong>Modern & Futuristik (9.7/10)</strong> — Sangat cocok jika BPR ingin mencitrakan diri sebagai bank modern / digital core banking.</td>
            </tr>
            <tr>
              <td><strong>Kinerja & Ringan</strong></td>
              <td><strong>Sangat Cepat</strong> — Sangat ringan di komputer operasional cabang tanpa GPU diskrit.</td>
              <td><strong>Ringan-Sedang</strong> — Menggunakan akselerasi CSS GPU blur shader modern.</td>
            </tr>
            <tr>
              <td><strong>Rekomendasi Pemakaian</strong></td>
              <td>Sangat direkomendasikan untuk <strong>Admin Backoffice Panel (`/admin`)</strong>.</td>
              <td>Sangat direkomendasikan untuk <strong>Portal Karyawan ESS (`/portal`) & Login Page</strong>.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>


  <!-- ==========================================================================
       INTERACTIVE JAVASCRIPT CONTROLLER
       ========================================================================== -->
  <script>
    function switchView(viewName, btnEl) {
      // Deactivate all containers
      document.querySelectorAll('.tab-view-container').forEach(el => {
        el.classList.remove('active');
      });

      // Deactivate all buttons
      document.querySelectorAll('.eval-tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.remove('liquid-btn');
      });

      // Activate target view
      const targetView = document.getElementById(`view-${viewName}`);
      if (targetView) {
        targetView.classList.add('active');
      }

      // Activate clicked button
      if (btnEl) {
        btnEl.classList.add('active');
        if (viewName === 'liquid-glass') {
          btnEl.classList.add('liquid-btn');
        }
      }

      // Update URL hash without reload
      history.replaceState(null, null, `?design=${viewName}`);
    }

    function toggleEvalTheme() {
      const current = document.documentElement.getAttribute('data-theme') || 'dark';
      const target = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', target);
      localStorage.setItem('hris_theme', target);
      document.getElementById('theme-btn-text').textContent = target === 'dark' ? 'Mode Gelap' : 'Mode Terang';
    }

    // Initialize state
    const urlParams = new URLSearchParams(window.location.search);
    const initialDesign = urlParams.get('design') || '{{ $design ?? "material3" }}';
    const initialBtn = document.querySelector(`.eval-tab-btn[onclick*="${initialDesign}"]`);
    if (initialBtn) {
      switchView(initialDesign, initialBtn);
    }
  </script>
</body>
</html>
