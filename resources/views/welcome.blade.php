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
     MATERIAL 3 EXPRESSIVE DESIGN SYSTEM - CORE HRIS BPR BANDA RAYA
     Optimal Ergonomics for Reading, Contrast, and All-Day Usage
     ============================================================================= */

  :root[data-theme="dark"] {
    --m3-bg: #0D1410;
    --m3-surface: #141E18;
    --m3-surface-container: #19261E;
    --m3-surface-container-high: #203026;
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

    --m3-card-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
    --m3-toggle-bg: rgba(255, 255, 255, 0.08);
    --m3-toggle-border: rgba(255, 255, 255, 0.12);
  }

  :root[data-theme="light"] {
    --m3-bg: #F4F6F4;
    --m3-surface: #FFFFFF;
    --m3-surface-container: #F0F5F2;
    --m3-surface-container-high: #E8EFEA;
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

    --m3-card-shadow: 0 12px 32px -8px rgba(14, 104, 56, 0.12), 0 2px 8px rgba(0, 0, 0, 0.04);
    --m3-toggle-bg: rgba(0, 0, 0, 0.05);
    --m3-toggle-border: rgba(0, 0, 0, 0.1);
  }

  *, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    min-height: 100vh;
    background:
      radial-gradient(1000px 600px at 10% -10%, rgba(14, 104, 56, 0.12), transparent 70%),
      radial-gradient(1000px 600px at 95% 110%, rgba(234, 88, 12, 0.1), transparent 70%),
      var(--m3-bg);
    color: var(--m3-on-surface);
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.25rem;
    transition: background 0.25s ease, color 0.25s ease;
    -webkit-font-smoothing: antialiased;
  }

  /* Sticky Top Bar for Switcher */
  .m3-top-bar {
    width: 100%;
    max-width: 480px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
  }

  .m3-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.85rem;
    border-radius: 9999px;
    background: var(--m3-primary-container);
    color: var(--m3-on-primary-container);
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.4px;
    border: 1px solid rgba(14, 104, 56, 0.15);
  }

  .m3-theme-toggle {
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

  .m3-theme-toggle:hover {
    transform: translateY(-1px);
    filter: brightness(1.08);
  }

  .m3-theme-toggle:active {
    transform: translateY(0);
  }

  /* Main Card: M3 Expressive 28px Squircle */
  .m3-login-card {
    width: 100%;
    max-width: 480px;
    background: var(--m3-surface);
    border: 1px solid var(--m3-outline-variant);
    border-radius: 28px; /* M3 Expressive Card Radius */
    padding: 2.5rem 2.25rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--m3-card-shadow);
    transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
  }

  /* M3 Gradient Top Accent Bar */
  .m3-login-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0E6838 0%, #EA580C 65%, #FACC15 100%);
  }

  .m3-brandmark {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
  }

  .m3-brand-img {
    height: 84px;
    width: auto;
    max-width: 270px;
    object-fit: contain;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.15));
    transition: opacity 0.2s ease;
  }

  .m3-heading {
    text-align: center;
    margin-bottom: 1.75rem;
  }

  .m3-heading h1 {
    font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, sans-serif;
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--m3-on-surface);
    letter-spacing: -0.01em;
    margin-bottom: 0.35rem;
  }

  .m3-heading p {
    font-size: 0.875rem;
    color: var(--m3-on-surface-variant);
    line-height: 1.5;
  }

  .alert-error {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.35);
    color: #ef4444;
    padding: 0.85rem 1rem;
    border-radius: 14px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  :root[data-theme="dark"] .alert-error {
    color: #fca5a5;
  }

  .form-group {
    margin-bottom: 1.25rem;
  }

  label {
    display: block;
    font-size: 0.825rem;
    font-weight: 700;
    color: var(--m3-on-surface-variant);
    margin-bottom: 0.45rem;
  }

  .input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
  }

  .input-icon {
    position: absolute;
    left: 1rem;
    width: 20px;
    height: 20px;
    color: var(--m3-outline);
    pointer-events: none;
    transition: color 0.2s ease;
  }

  input[type="email"],
  input[type="password"] {
    width: 100%;
    background: var(--m3-surface-container);
    border: 1px solid var(--m3-outline-variant);
    border-radius: 16px; /* M3 Squircle Input */
    padding: 0.85rem 1rem 0.85rem 2.75rem;
    color: var(--m3-on-surface);
    font-size: 0.925rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.25s ease;
  }

  input:focus {
    border-color: var(--m3-primary);
    box-shadow: 0 0 0 2px var(--m3-primary-container);
    background: var(--m3-surface);
  }

  input:focus + .input-icon,
  .input-wrapper:focus-within .input-icon {
    color: var(--m3-primary);
  }

  input::placeholder {
    color: var(--m3-outline);
    opacity: 0.8;
  }

  .row-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.5rem;
    margin-bottom: 1.5rem;
  }

  .remember-box {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.825rem;
    color: var(--m3-on-surface-variant);
    cursor: pointer;
    user-select: none;
    font-weight: 600;
  }

  .remember-box input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--m3-primary);
    cursor: pointer;
    border-radius: 4px;
  }

  .link-forgot {
    font-size: 0.825rem;
    font-weight: 700;
    color: var(--m3-primary);
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .link-forgot:hover {
    text-decoration: underline;
  }

  /* M3 Pill Button */
  button.m3-btn-submit {
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

  :root[data-theme="dark"] button.m3-btn-submit {
    background: linear-gradient(135deg, #4ADE80 0%, #22c55e 100%);
    color: #003A19;
    box-shadow: 0 4px 16px rgba(74, 222, 128, 0.3);
  }

  button.m3-btn-submit:hover {
    filter: brightness(1.08);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(14, 104, 56, 0.4);
  }

  button.m3-btn-submit:active {
    transform: translateY(0);
  }

  /* Demo Credentials Section */
  .m3-demo-box {
    margin-top: 1.5rem;
    background: var(--m3-surface-container);
    border: 1px dashed var(--m3-outline-variant);
    border-radius: 18px;
    padding: 1.15rem 1.25rem;
    font-size: 0.8rem;
    color: var(--m3-on-surface-variant);
    line-height: 1.6;
    transition: background 0.25s ease;
  }

  .m3-demo-header {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--m3-outline);
    margin-bottom: 0.5rem;
  }

  .m3-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: var(--m3-primary-container);
    color: var(--m3-on-primary-container);
    padding: 0.2rem 0.6rem;
    border-radius: 9999px; /* M3 Pill Chip */
    font-size: 0.775rem;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid rgba(14, 104, 56, 0.15);
    transition: all 0.15s ease;
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
  }

  .m3-chip:hover {
    background: var(--m3-primary);
    color: var(--m3-on-primary);
    transform: translateY(-1px);
  }

  footer {
    margin-top: 2rem;
    font-size: 0.8rem;
    color: var(--m3-on-surface-variant);
    text-align: center;
    opacity: 0.85;
  }
</style>
</head>
<body>

  <!-- Top Status & Theme Bar -->
  <div class="m3-top-bar">
    <div class="m3-pill-badge">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 14px; height: 14px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
      </svg>
      <span>Material 3 Expressive</span>
    </div>

    <button type="button" class="m3-theme-toggle" onclick="toggleTheme()" id="btn-theme-toggle" aria-label="Ganti mode tampilan">
      <span id="theme-icon" style="display: flex; align-items: center;">
        <svg id="svg-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 15px; height: 15px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
        </svg>
        <svg id="svg-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 15px; height: 15px; display: none;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
        </svg>
      </span>
      <span id="theme-text">Mode Gelap</span>
    </button>
  </div>

  <!-- Main Material 3 Expressive Card -->
  <div class="m3-login-card">
    <div class="m3-brandmark">
      <img id="brand-logo" src="{{ asset('images/logo-horizontal-clean.png') }}" alt="PT Bank Perekonomian Rakyat Banda Raya" class="m3-brand-img">
    </div>

    <div class="m3-heading">
      <h1>Core HRIS BPR Banda Raya</h1>
      <p>Satu pintu masuk terpadu untuk Panel HRD Backoffice dan Portal Mandiri Pegawai</p>
    </div>

    @if ($errors->any())
      <div class="alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px; flex-shrink: 0;">
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

      <div class="form-group">
        <label for="email">Alamat Email Kantor</label>
        <div class="input-wrapper">
          <input id="email" name="email" type="email" placeholder="nama@bprbandaraya.com" value="{{ old('email') }}" required autofocus autocomplete="username">
          <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
          </svg>
        </div>
      </div>

      <div class="form-group">
        <label for="pass">Kata Sandi</label>
        <div class="input-wrapper">
          <input id="pass" name="password" type="password" placeholder="Masukkan kata sandi akun" required autocomplete="current-password">
          <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
          </svg>
        </div>
      </div>

      <div class="row-meta">
        <label class="remember-box">
          <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
          <span>Ingat sesi saya</span>
        </label>
        <a href="javascript:void(0)" class="link-forgot" onclick="alert('Silakan hubungi Administrator HR / IT BPR Banda Raya untuk bantuan reset kata sandi.')">Lupa sandi?</a>
      </div>

      <button type="submit" class="m3-btn-submit">
        <span>Masuk ke Sistem</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 18px; height: 18px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
        </svg>
      </button>
    </form>

    <div class="m3-demo-box">
      <div class="m3-demo-header">Akun Akses Cepat (Uji Coba):</div>
      <div style="margin-bottom: 0.4rem;">
        HRD / Backoffice:
        <span class="m3-chip" onclick="fillDemo('admin@bprbandaraya.com', 'password')" title="Klik untuk isi otomatis">
          admin@bprbandaraya.com
        </span>
      </div>
      <div>
        Pegawai / Portal:
        <span class="m3-chip" onclick="fillDemo('teller.botania@bprbandaraya.com', 'password')" title="Klik untuk isi otomatis">
          teller.botania@bprbandaraya.com
        </span>
      </div>
    </div>
  </div>

  <footer>
    &copy; 2026 PT Bank Perekonomian Rakyat Banda Raya. Hak Cipta Dilindungi Undang-Undang.
  </footer>

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

    function fillDemo(email, password) {
      document.getElementById('email').value = email;
      document.getElementById('pass').value = password;
      document.getElementById('pass').focus();
    }
  </script>

</body>
</html>
