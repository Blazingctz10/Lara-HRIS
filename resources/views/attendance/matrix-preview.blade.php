<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pratinjau Matriks Presensi Bulanan - {{ $periodName }} | PT BPR Banda Raya</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0E6838;
      --primary-dark: #084022;
      --primary-light: #e8f5e9;
      --accent: #15803d;
      --danger: #b91c1c;
      --warning: #b45309;
      --info: #1d4ed8;
      --gray-50: #f8fafc;
      --gray-100: #f1f5f9;
      --gray-200: #e2e8f0;
      --gray-300: #cbd5e1;
      --gray-600: #475569;
      --gray-700: #334155;
      --gray-800: #1e293b;
      --gray-900: #0f172a;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #0f172a;
      color: var(--gray-800);
      font-size: 11px;
      line-height: 1.4;
      padding: 0;
      margin: 0;
    }

    /* Top Floating Toolbar (No Print) */
    .top-toolbar {
      position: sticky;
      top: 0;
      z-index: 100;
      background: #ffffff;
      border-bottom: 1px solid var(--gray-200);
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      padding: 10px 20px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .toolbar-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-badge {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .brand-logo-icon {
      width: 28px;
      height: 28px;
      background: var(--primary);
      color: #fff;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 14px;
    }

    .brand-title {
      font-size: 13px;
      font-weight: 700;
      color: var(--primary);
      line-height: 1.2;
    }

    .brand-sub {
      font-size: 10px;
      color: var(--gray-600);
      font-weight: 500;
    }

    .toolbar-filters {
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--gray-100);
      padding: 4px 8px;
      border-radius: 8px;
      border: 1px solid var(--gray-200);
    }

    .toolbar-filters select {
      font-family: inherit;
      font-size: 11px;
      padding: 4px 8px;
      border: 1px solid var(--gray-300);
      border-radius: 6px;
      background: #fff;
      color: var(--gray-800);
      outline: none;
      cursor: pointer;
    }

    .toolbar-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-family: inherit;
      font-size: 11px;
      font-weight: 600;
      padding: 7px 14px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.15s ease-in-out;
    }

    .btn-print {
      background: var(--primary);
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(14, 104, 56, 0.3);
    }

    .btn-print:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }

    .btn-excel {
      background: #107c41;
      color: #ffffff;
    }

    .btn-excel:hover {
      background: #0d5f32;
    }

    .btn-csv {
      background: #334155;
      color: #ffffff;
    }

    .btn-csv:hover {
      background: #1e293b;
    }

    .btn-back {
      background: #ffffff;
      color: var(--gray-700);
      border: 1px solid var(--gray-300);
    }

    .btn-back:hover {
      background: var(--gray-100);
    }

    /* Summary KPI Bar */
    .kpi-container {
      max-width: 98%;
      margin: 16px auto 0;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
      gap: 10px;
    }

    .kpi-card {
      background: #ffffff;
      border-radius: 8px;
      padding: 10px 14px;
      border: 1px solid var(--gray-200);
      box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }

    .kpi-label {
      font-size: 10px;
      font-weight: 600;
      color: var(--gray-600);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .kpi-val {
      font-size: 18px;
      font-weight: 700;
      margin-top: 2px;
      line-height: 1.1;
    }

    .val-hadir { color: #047857; }
    .val-cuti { color: #1d4ed8; }
    .val-izin { color: #b45309; }
    .val-telat { color: #b91c1c; }
    .val-denda { color: #b91c1c; font-size: 15px; }
    .val-alpa { color: #be123c; }

    /* Paper & Table Container */
    .preview-wrapper {
      max-width: 98%;
      margin: 14px auto 30px;
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.25);
      padding: 24px 28px;
    }

    .table-scroll-wrapper {
      width: 100%;
      overflow-x: auto;
      margin-top: 14px;
      border: 1px solid var(--gray-300);
      border-radius: 6px;
    }

    /* Official Letterhead Header */
    .report-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2px solid var(--primary);
      padding-bottom: 12px;
      margin-bottom: 12px;
    }

    .report-title-box h1 {
      font-size: 16px;
      font-weight: 800;
      color: var(--primary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .report-title-box h2 {
      font-size: 12px;
      font-weight: 700;
      color: var(--gray-800);
      margin-top: 2px;
    }

    .report-meta {
      font-size: 10px;
      color: var(--gray-600);
      margin-top: 4px;
    }

    .report-meta span {
      margin-right: 14px;
    }

    .report-meta b {
      color: var(--gray-900);
    }

    /* Matrix Table Design */
    table.matrix-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5px;
      background: #ffffff;
    }

    .matrix-table th, .matrix-table td {
      border: 1px solid var(--gray-300);
      padding: 4px 3px;
      text-align: center;
      vertical-align: middle;
      white-space: nowrap;
    }

    /* Headers */
    .th-main {
      background-color: var(--primary);
      color: #ffffff;
      font-weight: 700;
      font-size: 10px;
      padding: 6px 3px;
    }

    .th-sun {
      background-color: #b91c1c;
      color: #ffffff;
      font-weight: 700;
      font-size: 10px;
      padding: 6px 3px;
    }

    .th-sub {
      background-color: #15803d;
      color: #ffffff;
      font-size: 8.5px;
      font-weight: 600;
      padding: 3px 2px;
    }

    .th-sub-sun {
      background-color: #dc2626;
      color: #ffffff;
      font-size: 8.5px;
      font-weight: 600;
      padding: 3px 2px;
    }

    .th-summary {
      background-color: var(--gray-800);
      color: #ffffff;
      font-weight: 700;
      font-size: 10px;
    }

    .th-summary-sub {
      background-color: var(--gray-700);
      color: #ffffff;
      font-size: 8.5px;
      font-weight: 600;
      padding: 3px 4px;
    }

    /* Cell styles */
    .td-nip {
      font-family: 'JetBrains Mono', monospace;
      font-size: 9px;
      color: var(--gray-700);
    }

    .td-name {
      text-align: left !important;
      font-weight: 600;
      min-width: 140px;
      padding-left: 6px !important;
      color: var(--gray-900);
    }

    .td-branch {
      text-align: left !important;
      color: var(--gray-600);
      min-width: 100px;
      padding-left: 5px !important;
    }

    .td-present {
      color: #047857;
      font-weight: 600;
      font-family: 'JetBrains Mono', monospace;
      font-size: 8.5px;
    }

    .td-late {
      color: #b91c1c;
      background-color: #fee2e2;
      font-weight: 700;
      font-family: 'JetBrains Mono', monospace;
      font-size: 8.5px;
    }

    .td-cuti {
      background-color: #dbeafe;
      color: #1e40af;
      font-weight: 700;
      font-size: 8.5px;
    }

    .td-izin {
      background-color: #fef3c7;
      color: #92400e;
      font-weight: 700;
      font-size: 8.5px;
    }

    .td-menunggu {
      background-color: #f3e8ff;
      color: #6b21a8;
      font-style: italic;
      font-size: 8px;
    }

    .td-libur {
      background-color: #f1f5f9;
      color: #94a3b8;
      font-weight: 600;
      font-size: 8.5px;
    }

    .td-alpa {
      background-color: #ffe4e6;
      color: #be123c;
      font-weight: 600;
      font-size: 9px;
    }

    .td-notes {
      text-align: left !important;
      font-size: 8.5px;
      color: var(--gray-600);
      max-width: 250px;
      white-space: normal !important;
      padding: 3px 6px !important;
      line-height: 1.25;
    }

    .sum-val {
      font-weight: 700;
      font-size: 9.5px;
    }

    .tr-even { background-color: #ffffff; }
    .tr-odd { background-color: #f8fafc; }
    .matrix-table tr:hover { background-color: #f1f5f9; }

    /* Footer total */
    .tfoot-total td {
      background-color: #e2e8f0;
      font-weight: 800;
      border-top: 2px solid var(--primary);
      border-bottom: 2px solid var(--primary);
      font-size: 10px;
      padding: 6px 4px;
    }

    /* Signatures */
    .signatures-box {
      margin-top: 24px;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      text-align: center;
      page-break-inside: avoid;
    }

    .sign-col {
      border: 1px solid var(--gray-200);
      border-radius: 6px;
      padding: 12px;
      background: var(--gray-50);
    }

    .sign-title {
      font-weight: 700;
      font-size: 10.5px;
      color: var(--gray-800);
    }

    .sign-space {
      height: 55px;
    }

    .sign-name {
      font-weight: 700;
      font-size: 10px;
      text-decoration: underline;
      color: var(--gray-900);
    }

    .sign-role {
      font-size: 9px;
      color: var(--gray-600);
    }

    /* Banner Panduan Cetak Pas Halaman */
    .print-guide-banner {
      background: linear-gradient(135deg, #f0fdf4 0%, #e8f5e9 100%);
      border: 1px solid #86efac;
      border-radius: 8px;
      padding: 10px 14px;
      margin-bottom: 16px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .print-guide-icon {
      width: 24px;
      height: 24px;
      background: var(--primary);
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 13px;
      flex-shrink: 0;
    }

    .print-guide-content {
      font-size: 11px;
      color: #1e293b;
      line-height: 1.45;
    }

    .print-guide-content b {
      color: var(--primary-dark);
    }

    .print-guide-content ul {
      margin: 4px 0 0 16px;
      padding: 0;
    }

    .print-guide-content li {
      margin-bottom: 2px;
    }

    /* Mode Ringkas CSS (1 Kolom per Tanggal) */
    body.mode-compact .col-sub-out,
    body.mode-compact .col-out {
      display: none !important;
    }

    /* Print Media Rules */
    @media print {
      @page {
        size: landscape;
        margin: 4mm 3mm;
      }

      body {
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 7pt !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      /* Auto-fit Scale Classes for Print */
      body.scale-fit, body:not([class*="scale-"]) {
        zoom: 0.58 !important;
      }
      body.scale-compact {
        zoom: 0.52 !important;
      }
      body.scale-legal {
        zoom: 0.68 !important;
      }
      body.scale-a3 {
        zoom: 0.88 !important;
      }
      body.scale-100 {
        zoom: 1 !important;
      }

      .no-print {
        display: none !important;
      }

      .preview-wrapper {
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
      }

      .table-scroll-wrapper {
        border: none !important;
        overflow: visible !important;
        width: 100% !important;
      }

      table.matrix-table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 5.5pt !important;
        table-layout: auto !important;
      }

      table.matrix-table th,
      table.matrix-table td {
        width: auto !important;
        min-width: 0 !important;
        padding: 1.5px 0.5px !important;
        border: 0.5pt solid #000000 !important;
      }

      /* Header cell sizes in print */
      .th-main, .th-sub {
        background-color: #f1f5f9 !important;
        color: #000000 !important;
        font-size: 5pt !important;
      }

      .th-sun, .th-sub-sun {
        background-color: #fee2e2 !important;
        color: #000000 !important;
        font-size: 5pt !important;
      }

      .th-day-header {
        font-size: 5pt !important;
        padding: 1.5px 0 !important;
        line-height: 1.1 !important;
      }

      .th-day-header small {
        font-size: 4.5pt !important;
        display: block;
      }

      .th-summary, .th-summary-sub {
        background-color: #e2e8f0 !important;
        color: #000000 !important;
        font-size: 5pt !important;
      }

      /* Specific column width limits in print */
      .th-col-no, .td-col-no {
        width: 16px !important;
        font-size: 5.5pt !important;
        padding: 1px !important;
      }

      .th-col-nip, .td-nip {
        font-size: 5pt !important;
        max-width: 48px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        letter-spacing: -0.2px !important;
      }

      .th-col-name, .td-name {
        font-size: 5.5pt !important;
        max-width: 75px !important;
        min-width: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        padding-left: 2px !important;
        text-align: left !important;
      }

      .th-col-branch, .td-branch {
        font-size: 5pt !important;
        max-width: 55px !important;
        min-width: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        padding-left: 2px !important;
        text-align: left !important;
      }

      .td-notes {
        font-size: 4.8pt !important;
        max-width: 90px !important;
        white-space: normal !important;
        line-height: 1.15 !important;
        padding: 1px 2px !important;
      }

      .td-present, .td-late, .td-cuti, .td-izin, .td-menunggu, .td-libur, .td-alpa {
        font-size: 4.8pt !important;
        letter-spacing: -0.3px !important;
        color: #000000 !important;
      }

      .td-late {
        background-color: #fee2e2 !important;
        font-weight: bold !important;
      }

      .td-cuti {
        background-color: #dbeafe !important;
      }

      .sum-val {
        font-size: 5pt !important;
        padding: 1px 1px !important;
      }

      thead {
        display: table-header-group !important;
      }

      tr {
        page-break-inside: avoid !important;
      }

      .signatures-box {
        margin-top: 14px !important;
        page-break-inside: avoid !important;
      }

      .sign-col {
        border: 0.5pt solid #000000 !important;
        background: none !important;
        padding: 6px !important;
      }

      .sign-space {
        height: 42px !important;
      }
    }
  </style>
</head>
<body class="scale-fit">

  <!-- Floating Navigation & Action Bar -->
  <div class="top-toolbar no-print">
    <div class="toolbar-left">
      <div class="brand-badge">
        <div class="brand-logo-icon">BPR</div>
        <div>
          <div class="brand-title">PT BPR BANDA RAYA</div>
          <div class="brand-sub">Sistem Informasi Manajemen Presensi & HRIS Terpadu</div>
        </div>
      </div>

      <!-- Quick Switch Filter Form -->
      <form method="GET" action="{{ route('admin.attendance.matrix-preview') }}" class="toolbar-filters">
        <label for="month" style="font-weight: 600; font-size: 10px; color: var(--gray-600);">Periode:</label>
        <select name="month" id="month" onchange="this.form.submit()">
          @foreach(range(1, 12) as $m)
            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
              {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
            </option>
          @endforeach
        </select>

        <select name="year" id="year" onchange="this.form.submit()">
          @foreach(range(now()->year - 2, now()->year + 2) as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
          @endforeach
        </select>

        <select name="branch_id" id="branch_id" onchange="this.form.submit()">
          <option value="">Semua Kantor Unit Kerja</option>
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
          @endforeach
        </select>
      </form>
    </div>

    <div class="toolbar-actions">
      <!-- Print Scale Selector -->
      <div style="display: flex; align-items: center; gap: 5px; background: var(--gray-100); padding: 3px 8px; border-radius: 6px; border: 1px solid var(--gray-200);">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
        <label for="printScaleSelect" style="font-size: 10px; font-weight: 600; color: var(--gray-700);">Skala Print:</label>
        <select id="printScaleSelect" onchange="changePrintScale(this.value)" style="font-size: 11px; padding: 3px 6px; border-radius: 4px; border: 1px solid var(--gray-300); background: #fff; cursor: pointer;">
          <option value="scale-fit" selected>Auto Fit A4 Landscape (58%)</option>
          <option value="scale-compact">Sangat Rapat A4 (52%)</option>
          <option value="scale-legal">Kertas F4 / Legal (68%)</option>
          <option value="scale-a3">Kertas A3 Besar (88%)</option>
          <option value="scale-100">Ukuran Asli 100% (Manual)</option>
        </select>
      </div>

      <!-- Toggle Mode Ringkas -->
      <button type="button" onclick="toggleCompactMode()" id="btnCompactMode" class="btn" style="background: #ffffff; color: var(--gray-800); border: 1px solid var(--gray-300);" title="Sembunyikan jam Out untuk menghemat lebar kertas cetak">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
        <span id="compactModeText">Mode: Lengkap (In & Out)</span>
      </button>

      <button onclick="window.print()" class="btn btn-print" title="Cetak / Simpan sebagai PDF (Ctrl + P)">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Cetak Laporan / Print (PDF)
      </button>

      <a href="{{ route('admin.attendance.export-matrix', ['year' => $year, 'month' => $month, 'branch_id' => $branchId, 'format' => 'excel']) }}" class="btn btn-excel" title="Unduh Berkas Excel Resmi">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Unduh Excel (.xls)
      </a>

      <a href="{{ route('admin.attendance.export-matrix', ['year' => $year, 'month' => $month, 'branch_id' => $branchId, 'format' => 'csv']) }}" class="btn btn-csv" title="Unduh Berkas CSV Mentah">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Unduh CSV (.csv)
      </a>

      <a href="{{ url('/admin/attendances') }}" class="btn btn-back">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
      </a>
    </div>
  </div>

  <!-- Key Performance Indicator Cards (No Print) -->
  <div class="kpi-container no-print">
    <div class="kpi-card">
      <div class="kpi-label">Total Karyawan</div>
      <div class="kpi-val">{{ count($matrixRows) }} <span style="font-size: 11px; font-weight: 500; color: var(--gray-600);">Orang</span></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Hadir</div>
      <div class="kpi-val val-hadir">{{ number_format($grandTotals['hadir'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Cuti</div>
      <div class="kpi-val val-cuti">{{ number_format($grandTotals['cuti'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Izin / Disp</div>
      <div class="kpi-val val-izin">{{ number_format($grandTotals['izin'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Terlambat</div>
      <div class="kpi-val val-telat">{{ number_format($grandTotals['telat'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Akumulasi Denda</div>
      <div class="kpi-val val-denda">Rp {{ number_format($grandTotals['denda'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Tidak Absen / Alpa</div>
      <div class="kpi-val val-alpa">{{ number_format($grandTotals['alpa'], 0, ',', '.') }}</div>
    </div>
  </div>

  <!-- Printable Paper Wrapper -->
  <div class="preview-wrapper">
    <div class="report-header">
      <div class="report-title-box">
        <h1>PT BPR BANDA RAYA</h1>
        <h2>MATRIKS REKAPITULASI PRESENSI & KEHADIRAN BULANAN (TERPADU: MESIN, CUTI & LOGBOOK)</h2>
        <div class="report-meta">
          <span>Periode: <b>{{ $periodName }}</b></span>
          <span>Kantor Unit: <b>{{ $selectedBranch?->name ?? 'Semua Unit Kerja' }}</b></span>
          <span>Dicetak Pada: <b>{{ now()->format('d/m/Y H:i:s') }} WIB</b></span>
          <span>Status Pegawai: <b>Aktif</b></span>
        </div>
      </div>
      <div style="text-align: right; font-size: 9px; color: var(--gray-600);" class="no-print">
        Tip: Gunakan tombol <b>Cetak Laporan / Print</b> atau pintasan <b>Ctrl + P</b> untuk mencetak atau simpan PDF landscape.
      </div>
    </div>

    <!-- Print Guidance Banner (No Print) -->
    <div class="print-guide-banner no-print">
      <div class="print-guide-icon">💡</div>
      <div class="print-guide-content">
        <b>Tips Agar Baris/Kolom Tidak Terpotong Saat Mencetak:</b>
        <ul>
          <li><b>Auto-Fit Aktif:</b> Skala cetak sudah diset otomatis ke <b>58%</b> sehingga seluruh tanggal 1–31 dan kolom akumulasi (Hadir, Cuti, Izin, Denda, Catatan) muat dalam 1 lembar A4 landscape.</li>
          <li>Pada jendela cetak browser (<b>Ctrl + P</b>), pastikan opsi <b>Tata Letak (Layout)</b> dipilih <b>Lanskap / Landscape</b>.</li>
          <li>Jika batas cetak printer Anda agak sempit, Anda dapat mengganti pilihan di toolbar atas ke <b>Sangat Rapat A4 (52%)</b> atau beralih ke <b>Mode: Ringkas (1 Kolom/Hari)</b>.</li>
          <li>Untuk kantor dengan printer kertas panjang/besar, pilih kertas <b>F4 / Legal</b> atau <b>A3</b> agar teks tampak lebih besar dan lega.</li>
        </ul>
      </div>
    </div>

    <div class="table-scroll-wrapper">
      <table class="matrix-table">
        <thead>
          <tr>
            <th rowspan="2" class="th-main th-col-no" style="width: 30px;">NO</th>
            <th rowspan="2" class="th-main th-col-nip" style="width: 85px;">NIP / NIK</th>
            <th rowspan="2" class="th-main th-col-name" style="width: 160px;">NAMA PEGAWAI</th>
            <th rowspan="2" class="th-main th-col-branch" style="width: 120px;">KANTOR UNIT</th>
            @foreach($daysMeta as $d => $meta)
              <th colspan="2" class="th-day-header {{ $meta['is_sunday'] ? 'th-sun' : 'th-main' }}" style="width: 58px;">
                Tgl {{ $meta['pad'] }}<br><small>{{ $meta['name'] }}</small>
              </th>
            @endforeach
            <th colspan="6" class="th-summary">AKUMULASI KEHADIRAN</th>
            <th rowspan="2" class="th-summary th-col-notes" style="width: 220px;">CATATAN TERPADU (LOGBOOK & CUTI)</th>
          </tr>
          <tr>
            @foreach($daysMeta as $d => $meta)
              <th class="col-sub-in {{ $meta['is_sunday'] ? 'th-sub-sun' : 'th-sub' }}">In</th>
              <th class="col-sub-out {{ $meta['is_sunday'] ? 'th-sub-sun' : 'th-sub' }}">Out</th>
            @endforeach
            <th class="th-summary-sub" style="width: 42px;">Hadir</th>
            <th class="th-summary-sub" style="width: 40px;">Cuti</th>
            <th class="th-summary-sub" style="width: 40px;">Izin</th>
            <th class="th-summary-sub" style="width: 40px;">Telat</th>
            <th class="th-summary-sub" style="width: 75px;">Denda (Rp)</th>
            <th class="th-summary-sub" style="width: 40px;">Alpa</th>
          </tr>
        </thead>
        <tbody>
          @forelse($matrixRows as $row)
            <tr class="{{ $row['no'] % 2 === 0 ? 'tr-even' : 'tr-odd' }}">
              <td class="td-col-no">{{ $row['no'] }}</td>
              <td class="td-nip">{{ $row['employee']->nip }}</td>
              <td class="td-name">{{ $row['employee']->full_name }}</td>
              <td class="td-branch">{{ $row['employee']->branch?->name ?? 'Kantor Pusat' }}</td>
              @foreach($daysMeta as $d => $meta)
                @php
                  $dayInfo = $row['days'][$d] ?? ['type' => 'normal', 'in' => '-', 'out' => '-'];
                @endphp
                @if(in_array($dayInfo['type'], ['cuti', 'izin', 'libur', 'menunggu']))
                  <td colspan="2" class="td-span-2 td-{{ $dayInfo['type'] }}">{{ $dayInfo['in'] }}</td>
                @else
                  <td class="col-in {{ $dayInfo['type'] === 'present' ? 'td-present' : ($dayInfo['type'] === 'late' ? 'td-late' : ($dayInfo['type'] === 'alpa' ? 'td-alpa' : '')) }}">{{ $dayInfo['in'] }}</td>
                  <td class="col-out {{ $dayInfo['type'] === 'present' ? 'td-present' : ($dayInfo['type'] === 'alpa' ? 'td-alpa' : '') }}">{{ $dayInfo['out'] }}</td>
                @endif
              @endforeach
              <td class="sum-val val-hadir">{{ $row['total_hadir'] }}</td>
              <td class="sum-val val-cuti">{{ $row['total_cuti'] }}</td>
              <td class="sum-val val-izin">{{ $row['total_izin'] }}</td>
              <td class="sum-val val-telat">{{ $row['total_telat'] }}</td>
              <td class="sum-val val-denda" style="text-align: right; padding-right: 5px;">{{ number_format($row['total_denda'], 0, ',', '.') }}</td>
              <td class="sum-val val-alpa">{{ $row['total_alpa'] }}</td>
              <td class="td-notes">{{ $row['notes'] }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="{{ 4 + ($daysInMonth * 2) + 7 }}" style="padding: 20px; text-align: center; color: var(--gray-600);">
                Tidak ada data presensi yang ditemukan untuk kriteria filter ini.
              </td>
            </tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr class="tfoot-total">
            <td colspan="4" class="tfoot-label" style="text-align: right; padding-right: 10px;">TOTAL REKAPITULASI:</td>
            <td colspan="{{ $daysInMonth * 2 }}" class="tfoot-day-span" style="text-align: center; color: var(--gray-600); font-style: italic; font-weight: normal;">
              (Akumulasi {{ count($matrixRows) }} Karyawan Aktif)
            </td>
            <td class="sum-val val-hadir">{{ $grandTotals['hadir'] }}</td>
            <td class="sum-val val-cuti">{{ $grandTotals['cuti'] }}</td>
            <td class="sum-val val-izin">{{ $grandTotals['izin'] }}</td>
            <td class="sum-val val-telat">{{ $grandTotals['telat'] }}</td>
            <td class="sum-val val-denda" style="text-align: right; padding-right: 5px;">{{ number_format($grandTotals['denda'], 0, ',', '.') }}</td>
            <td class="sum-val val-alpa">{{ $grandTotals['alpa'] }}</td>
            <td>&nbsp;</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Official Validation / Signatures Block -->
    <div class="signatures-box">
      <div class="sign-col">
        <div class="sign-title">Dibuat Oleh:</div>
        <div class="sign-space"></div>
        <div class="sign-name">( Staf Human Capital )</div>
        <div class="sign-role">Pengelola Presensi & HRIS</div>
      </div>
      <div class="sign-col">
        <div class="sign-title">Diperiksa Oleh:</div>
        <div class="sign-space"></div>
        <div class="sign-name">( HC & GA Division Head )</div>
        <div class="sign-role">Human Capital & General Affair</div>
      </div>
      <div class="sign-col">
        <div class="sign-title">Disetujui Oleh:</div>
        <div class="sign-space"></div>
        <div class="sign-name">( Direktur Operasional )</div>
        <div class="sign-role">PT BPR Banda Raya</div>
      </div>
    </div>
  </div>

  <script>
    function changePrintScale(scaleClass) {
      document.body.classList.remove('scale-fit', 'scale-compact', 'scale-legal', 'scale-a3', 'scale-100');
      document.body.classList.add(scaleClass);
    }

    function toggleCompactMode() {
      const isCompact = document.body.classList.toggle('mode-compact');
      const btn = document.getElementById('btnCompactMode');
      const text = document.getElementById('compactModeText');
      const dayHeaders = document.querySelectorAll('.th-day-header');
      const span2Cells = document.querySelectorAll('.td-span-2');
      const tfootSpan = document.querySelector('.tfoot-day-span');
      const totalDays = {{ $daysInMonth }};

      if (isCompact) {
        text.innerText = 'Mode: Ringkas (1 Kolom/Hari)';
        btn.style.backgroundColor = '#e8f5e9';
        btn.style.color = '#0E6838';
        btn.style.borderColor = '#0E6838';
        dayHeaders.forEach(th => th.setAttribute('colspan', '1'));
        span2Cells.forEach(td => td.setAttribute('colspan', '1'));
        if (tfootSpan) tfootSpan.setAttribute('colspan', totalDays);
      } else {
        text.innerText = 'Mode: Lengkap (In & Out)';
        btn.style.backgroundColor = '#ffffff';
        btn.style.color = 'var(--gray-800)';
        btn.style.borderColor = 'var(--gray-300)';
        dayHeaders.forEach(th => th.setAttribute('colspan', '2'));
        span2Cells.forEach(td => td.setAttribute('colspan', '2'));
        if (tfootSpan) tfootSpan.setAttribute('colspan', totalDays * 2);
      }
    }
  </script>
</body>
</html>
