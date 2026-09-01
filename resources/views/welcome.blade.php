<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Masuk — Core HRIS BPR Banda Raya</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
<script>
  // Immediate theme detection to prevent flash of wrong theme
  (function() {
    const savedTheme = localStorage.getItem('hris_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
  })();
</script>
<style>
  :root[data-theme="dark"] {
    --bg: #0a120e;
    --bg-2: #0d1712;
    --panel: #0f1a14;
    --line: rgba(255,255,255,0.08);
    --green: #1f9d55;
    --green-soft: rgba(31,157,85,0.14);
    --orange: #e8792c;
    --orange-soft: rgba(232,121,44,0.14);
    --text: #eef2ef;
    --muted: #8a978f;
    --input-bg: #0b1410;
    --input-border: rgba(255,255,255,0.1);
    --card-shadow: 0 20px 40px -15px rgba(0,0,0,0.6);
    --toggle-bg: rgba(255,255,255,0.08);
    --toggle-border: rgba(255,255,255,0.12);
  }

  :root[data-theme="light"] {
    --bg: #f3f6f4;
    --bg-2: #e8efe9;
    --panel: #ffffff;
    --line: rgba(0,0,0,0.08);
    --green: #0e6838;
    --green-soft: rgba(14,104,56,0.1);
    --orange: #d9681c;
    --orange-soft: rgba(217,104,28,0.1);
    --text: #111827;
    --muted: #526156;
    --input-bg: #f8faf8;
    --input-border: #d1d9d3;
    --card-shadow: 0 16px 36px -10px rgba(14,104,56,0.12), 0 4px 12px rgba(0,0,0,0.05);
    --toggle-bg: rgba(0,0,0,0.05);
    --toggle-border: rgba(0,0,0,0.1);
  }

  *{ box-sizing: border-box; }
  html,body{ margin:0; padding:0; }
  body{
    min-height:100vh;
    background:
      radial-gradient(900px 500px at 15% -10%, var(--green-soft), transparent 60%),
      radial-gradient(900px 500px at 100% 110%, var(--orange-soft), transparent 60%),
      var(--bg);
    color: var(--text);
    font-family: 'Segoe UI', ui-sans-serif, system-ui, -apple-system, Roboto, Helvetica, Arial, sans-serif;
    display:flex;
    flex-direction:column;
    align-items:center;
    padding: 32px 20px 32px;
    transition: background 0.25s ease, color 0.25s ease;
    position: relative;
  }

  /* Theme Toggle Floating Button */
  .theme-toggle-bar {
    width: 100%;
    max-width: 920px;
    display: flex;
    justify-content: flex-end;
    margin-bottom: 12px;
  }

  .theme-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--toggle-bg);
    border: 1px solid var(--toggle-border);
    color: var(--text);
    padding: 7px 14px;
    border-radius: 9999px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    backdrop-filter: blur(12px);
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    user-select: none;
  }
  .theme-toggle-btn:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
  }
  .theme-toggle-btn:active {
    transform: translateY(0);
  }
  .theme-toggle-btn .icon {
    font-size: 15px;
    line-height: 1;
  }

  .brandmark{
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
  }
  .brandmark-img{
    height: 96px;
    width: auto;
    max-width: 290px;
    object-fit: contain;
    display: block;
    filter: drop-shadow(0 6px 16px rgba(0,0,0,0.25));
    transition: opacity 0.2s ease;
  }

  .heading{ text-align:center; margin-bottom:32px; max-width:520px; }
  .heading h1{
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 8px;
    letter-spacing: -0.01em;
    color: var(--text);
  }
  .heading p{
    margin:0;
    color: var(--muted);
    font-size: 14px;
    line-height:1.6;
  }

  .stage{
    width:100%;
    max-width: 920px;
  }

  .split{
    display:flex;
    justify-content:center;
  }

  .card{
    width:100%;
    max-width: 440px;
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 20px;
    padding: 32px 28px;
    position:relative;
    overflow:hidden;
    box-shadow: var(--card-shadow);
    transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
  }
  .card::before{
    content:"";
    position:absolute; top:0; left:0; right:0; height:3.5px;
    background: linear-gradient(90deg, var(--green), var(--orange));
  }

  .card-head{ display:flex; align-items:center; gap:12px; margin-bottom: 6px; }
  .icon-box{
    width:38px; height:38px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:18px;
    flex-shrink:0;
    background: var(--green-soft);
    color: var(--green);
  }

  .card h2{ margin:0; font-size:18px; font-weight:700; color: var(--text); }
  .card .sub{ margin: 4px 0 24px; color: var(--muted); font-size: 13px; line-height:1.5; }

  .alert-error {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.35);
    color: #ef4444;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 18px;
    line-height: 1.4;
  }
  :root[data-theme="dark"] .alert-error {
    color: #fca5a5;
  }

  label{
    display:block;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--muted);
    margin-bottom: 6px;
    margin-top: 16px;
  }
  label:first-of-type{ margin-top:0; }

  input[type="email"],
  input[type="password"],
  input[type="text"]{
    width:100%;
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    border-radius: 10px;
    padding: 11px 12px;
    color: var(--text);
    font-size: 14px;
    outline:none;
    transition: border-color .15s ease, box-shadow .15s ease, background .25s ease;
  }
  input::placeholder{ color: var(--muted); opacity: 0.7; }
  input:focus{ border-color: var(--green); box-shadow: 0 0 0 2px var(--green-soft); }

  .row-between{
    display:flex; align-items:center; justify-content:space-between;
    margin-top: 14px;
  }
  .row-between a{
    font-size: 12.5px;
    color: var(--muted);
    text-decoration:none;
    transition: color .15s ease;
  }
  .row-between a:hover{ color: var(--green); }

  .remember{ display:flex; align-items:center; gap:7px; font-size:12.5px; color:var(--muted); cursor:pointer; user-select:none; }
  .remember input[type=checkbox]{ width:14px; height:14px; accent-color: var(--green); cursor:pointer; }

  button.submit{
    width:100%;
    border:none;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 14.5px;
    font-weight: 700;
    cursor:pointer;
    margin-top: 22px;
    transition: filter .15s ease, transform .05s ease;
    background: linear-gradient(180deg, #1f9d55, #127a3f);
    color:#ffffff;
    display:flex;
    align-items:center;
    justify-content:center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(31,157,85,0.25);
  }
  button.submit:active{ transform: translateY(1px); }
  button.submit:hover{ filter: brightness(1.08); }

  .divider{
    display:flex; align-items:center; gap:10px;
    margin: 22px 0 0;
    color: var(--muted); font-size:11.5px;
  }
  .divider::before, .divider::after{
    content:""; flex:1; height:1px; background: var(--line);
  }

  .demo-hint{
    margin-top: 18px;
    font-size: 12px;
    color: var(--muted);
    background: var(--bg-2);
    border: 1px dashed var(--line);
    border-radius: 10px;
    padding: 12px 14px;
    line-height:1.7;
    transition: background 0.25s ease, border-color 0.25s ease;
  }
  .demo-hint code{
    color: var(--green);
    background: var(--green-soft);
    padding: 2px 6px;
    border-radius: 5px;
    font-size:11.5px;
    cursor: pointer;
    font-weight: 600;
    transition: background .15s ease, color .15s ease;
  }
  .demo-hint code:hover{
    background: var(--green);
    color: #ffffff;
  }

  footer{
    margin-top: 40px;
    font-size: 12px;
    color: var(--muted);
    text-align:center;
    opacity: 0.85;
  }

  @media (max-width: 760px){
    .split{ grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

  <!-- Theme Toggle Bar -->
  <div class="theme-toggle-bar">
    <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" id="btn-theme-toggle">
      <span class="icon" id="theme-icon">🌙</span>
      <span id="theme-text">Mode Gelap</span>
    </button>
  </div>

  <div class="brandmark">
    <img id="brand-logo" src="{{ asset('images/logo-dark.svg') }}" alt="Bank Perekonomian Rakyat Banda Raya" class="brandmark-img">
  </div>

  <div class="heading">
    <h1>Masuk ke Core HRIS</h1>
    <p>Pilih akses sesuai peran Anda. Panel Backoffice untuk tim Human Capital dan Pincab, Portal Karyawan untuk staf operasional.</p>
  </div>

  <div class="stage">

    <div class="split">

      <div class="card" id="panel-login">
        <div class="card-head">
          <div class="icon-box">🔐</div>
          <h2>Masuk ke akun Anda</h2>
        </div>
        <div class="sub">Satu pintu masuk untuk semua pegawai. Sistem akan mengarahkan Anda ke Panel HRD atau Portal Karyawan sesuai peran Anda.</div>

        @if ($errors->any())
          <div class="alert-error">
            {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
          @csrf

          {{-- Anti-Bot Honeypot Trap --}}
          <div style="display:none; opacity:0; position:absolute; left:-9999px;" aria-hidden="true">
            <label for="website_hp">Leave this field blank</label>
            <input type="text" name="website_hp" id="website_hp" tabindex="-1" autocomplete="off">
          </div>

          <label for="email">Email kantor</label>
          <input id="email" name="email" type="email" placeholder="nama@bprbandaraya.com" value="{{ old('email') }}" required autofocus autocomplete="username">

          <label for="pass">Kata sandi</label>
          <input id="pass" name="password" type="password" placeholder="Masukkan kata sandi" required autocomplete="current-password">

          <div class="row-between">
            <label class="remember" style="margin:0;">
              <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Ingat saya
            </label>
            <a href="javascript:void(0)" onclick="alert('Silakan hubungi Administrator HR / IT untuk mereset kata sandi Anda.')">Lupa kata sandi?</a>
          </div>

          <button type="submit" class="submit">Masuk</button>
        </form>

        <div class="demo-hint">
          Demo HRD: <code onclick="fillDemo('admin@bprbandaraya.com', 'password')" title="Klik untuk isi otomatis">admin@bprbandaraya.com</code><br>
          Demo Karyawan: <code onclick="fillDemo('teller.botania@bprbandaraya.com', 'password')" title="Klik untuk isi otomatis">teller.botania@bprbandaraya.com</code><br>
          Kata sandi demo (semua akun): <code onclick="fillPassword('password')" title="Klik untuk isi sandi">password</code>
        </div>
      </div>

    </div>
  </div>

  <footer>© 2026 PT Bank Perekonomian Rakyat Banda Raya. Hak Cipta Dilindungi.</footer>

  <script>
    const darkLogo = "{{ asset('images/logo-dark.svg') }}";
    const lightLogo = "{{ asset('images/logo-light.svg') }}";

    function applyTheme(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('hris_theme', theme);

      const themeIcon = document.getElementById('theme-icon');
      const themeText = document.getElementById('theme-text');
      const brandLogo = document.getElementById('brand-logo');

      if (theme === 'light') {
        if (themeIcon) themeIcon.textContent = '☀️';
        if (themeText) themeText.textContent = 'Mode Terang';
        if (brandLogo) brandLogo.src = lightLogo;
      } else {
        if (themeIcon) themeIcon.textContent = '🌙';
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

    // Sync button state on initial load
    document.addEventListener('DOMContentLoaded', () => {
      const savedTheme = localStorage.getItem('hris_theme') || 'dark';
      applyTheme(savedTheme);
    });

    function fillDemo(email, password) {
      document.getElementById('email').value = email;
      document.getElementById('pass').value = password;
      document.getElementById('pass').focus();
    }
    function fillPassword(password) {
      document.getElementById('pass').value = password;
      document.getElementById('pass').focus();
    }
  </script>

</body>
</html>
