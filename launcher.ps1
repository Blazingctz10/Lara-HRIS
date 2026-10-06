# =============================================================================
# PT BPR BANDA RAYA - CORE HRIS PRODUCTION & DEVELOPMENT SERVER LAUNCHER
# Versi: 2.5 (Stable, Pure PowerShell, Zero-Flicker, High Reliability)
# =============================================================================

[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$Host.UI.RawUI.WindowTitle = "PT BPR Banda Raya - Core HRIS Server Console"

# Deteksi root aplikasi
$ScriptDir = $PSScriptRoot
if (Test-Path (Join-Path $ScriptDir "artisan")) {
    $HrisPath = $ScriptDir
} elseif (Test-Path (Join-Path $ScriptDir "hris-system\artisan")) {
    $HrisPath = Join-Path $ScriptDir "hris-system"
} else {
    Write-Host "[GALAT] Direktori proyek Laravel tidak ditemukan." -ForegroundColor Red
    Read-Host "Tekan Enter untuk keluar"
    exit 1
}

Set-Location $HrisPath

# Inisialisasi folder penting
$StoragePath = Join-Path $HrisPath "storage"
$LogDir = Join-Path $HrisPath "storage\logs"
$FrameworkCache = Join-Path $HrisPath "storage\framework\cache"
$FrameworkSessions = Join-Path $HrisPath "storage\framework\sessions"
$FrameworkViews = Join-Path $HrisPath "storage\framework\views"
$BootstrapCache = Join-Path $HrisPath "bootstrap\cache"

@($LogDir, $FrameworkCache, $FrameworkSessions, $FrameworkViews, $BootstrapCache) | ForEach-Object {
    if (-not (Test-Path $_)) {
        New-Item -ItemType Directory -Path $_ -Force | Out-Null
    }
}

$LogFile = Join-Path $LogDir "serve.log"
$DatabaseFile = Join-Path $HrisPath "database\database.sqlite"
if (-not (Test-Path $DatabaseFile)) {
    New-Item -ItemType File -Path $DatabaseFile -Force | Out-Null
}

function Write-Header {
    Clear-Host
    Write-Host "==========================================================================================" -ForegroundColor DarkGreen
    Write-Host "                  PT BPR BANDA RAYA - CORE HRIS SERVER CONSOLE                            " -ForegroundColor Green
    Write-Host "               Sistem Informasi Manajemen Sumber Daya Manusia Terpadu                    " -ForegroundColor DarkCyan
    Write-Host "==========================================================================================" -ForegroundColor DarkGreen
}

# 1. Pre-flight Checks (Pemeriksaan Prasyarat)
Write-Header
Write-Host " [1/3] Memeriksa Lingkungan PHP & Prasyarat Sistem..." -ForegroundColor Cyan

$phpExe = Get-Command "php" -ErrorAction SilentlyContinue
if (-not $phpExe) {
    Write-Host "  [GALAT] PHP tidak terdeteksi dalam PATH sistem!" -ForegroundColor Red
    Write-Host "  Silakan pastikan PHP 8.2+ terpasang dan ditambahkan ke Environment Variable PATH." -ForegroundColor Yellow
    Read-Host "`nTekan Enter untuk keluar"
    exit 1
}

$phpVer = & php -r "echo PHP_VERSION;" 2>$null
Write-Host "  -> PHP Terdeteksi : v$phpVer" -ForegroundColor Gray

# Cek ekstensi krusial
$missingExt = @()
@("pdo_sqlite", "mbstring", "openssl", "curl") | ForEach-Object {
    $hasExt = & php -r "echo extension_loaded('$_') ? '1' : '0';" 2>$null
    if ($hasExt -ne "1") { $missingExt += $_ }
}

if ($missingExt.Count -gt 0) {
    Write-Host "  [PERINGATAN] Ekstensi PHP berikut belum aktif di php.ini: $($missingExt -join ', ')" -ForegroundColor Yellow
}

# 2. Port Management & Clean Startup
$ServerPort = 8000
Write-Host " [2/3] Memeriksa Port Jaringan ($ServerPort)..." -ForegroundColor Cyan

$existingProcess = $null
try {
    $conns = Get-NetTCPConnection -LocalPort $ServerPort -ErrorAction SilentlyContinue
    if ($conns) {
        $existingPids = $conns | Select-Object -ExpandProperty OwningProcess -Unique
        foreach ($pidItem in $existingPids) {
            $proc = Get-Process -Id $pidItem -ErrorAction SilentlyContinue
            if ($proc -and $proc.ProcessName -like "*php*") {
                Write-Host "  -> Menutup sisa proses PHP lama di port $ServerPort (PID: $pidItem)..." -ForegroundColor Gray
                Stop-Process -Id $pidItem -Force -ErrorAction SilentlyContinue
            }
        }
    }
} catch {
    # Fallback netstat jika Get-NetTCPConnection dibatasi hak akses
    $netstatMatch = netstat -ano | Select-String ":$ServerPort\s+.*LISTENING\s+(\d+)"
    foreach ($line in $netstatMatch) {
        $foundPid = $line.Matches[0].Groups[1].Value
        if ($foundPid) {
            $p = Get-Process -Id $foundPid -ErrorAction SilentlyContinue
            if ($p -and $p.ProcessName -like "*php*") {
                Stop-Process -Id $foundPid -Force -ErrorAction SilentlyContinue
            }
        }
    }
}

Start-Sleep -Milliseconds 400

# 3. Jalankan Server PHP
Write-Host " [3/3] Meluncurkan Core HRIS Server..." -ForegroundColor Cyan
"[$((Get-Date).ToString('yyyy-MM-dd HH:mm:ss'))] Memulai Core HRIS Server PT BPR Banda Raya..." | Out-File -FilePath $LogFile -Encoding utf8 -Force

$procInfo = New-Object System.Diagnostics.ProcessStartInfo
$procInfo.FileName = "php"
$procInfo.Arguments = "artisan serve --host=127.0.0.1 --port=$ServerPort"
$procInfo.WorkingDirectory = $HrisPath
$procInfo.RedirectStandardOutput = $true
$procInfo.RedirectStandardError = $true
$procInfo.UseShellExecute = $false
$procInfo.CreateNoWindow = $true

$serverProc = New-Object System.Diagnostics.Process
$serverProc.StartInfo = $procInfo

$serverProc.add_OutputDataReceived({
    param($s, $e)
    if ($e.Data) {
        try {
            [System.IO.File]::AppendAllText($LogFile, "$($e.Data)`r`n", [System.Text.Encoding]::UTF8)
        } catch {}
    }
})
$serverProc.add_ErrorDataReceived({
    param($s, $e)
    if ($e.Data) {
        try {
            [System.IO.File]::AppendAllText($LogFile, "$($e.Data)`r`n", [System.Text.Encoding]::UTF8)
        } catch {}
    }
})

$started = $serverProc.Start()
if (-not $started) {
    Write-Host "  [GALAT] Gagal menjalankan perintah artisan serve." -ForegroundColor Red
    Read-Host "Tekan Enter untuk keluar"
    exit 1
}

$serverProc.BeginOutputReadLine()
$serverProc.BeginErrorReadLine()

Start-Sleep -Milliseconds 700

# 4. Interactive Console Dashboard Loop
function Show-Dashboard {
    Write-Header
    Write-Host "  STATUS SERVER : " -NoNewline
    if ($serverProc -and -not $serverProc.HasExited) {
        Write-Host "[ AKTIF & BERJALAN ] (PID: $($serverProc.Id))" -ForegroundColor Green
    } else {
        Write-Host "[ TERHENTI ]" -ForegroundColor Red
    }
    Write-Host "  URL AKSES     : " -NoNewline
    Write-Host "http://127.0.0.1:$ServerPort" -ForegroundColor Cyan
    Write-Host "  DATA PRESENSI : " -NoNewline
    Write-Host "[ Mesin Sidik Jari Biometrik Terpadu ]" -ForegroundColor Yellow
    Write-Host "------------------------------------------------------------------------------------------" -ForegroundColor DarkGray
    Write-Host "  PILIHAN NAVIGASI & AKSES CEPAT:" -ForegroundColor Yellow
    Write-Host "  [1] Buka Halaman Login Utama          -> http://127.0.0.1:$ServerPort/login"
    Write-Host "  [2] Buka Panel Backoffice HRD / Admin -> http://127.0.0.1:$ServerPort/admin"
    Write-Host "  [3] Buka Portal Mandiri Karyawan      -> http://127.0.0.1:$ServerPort/portal"
    Write-Host "------------------------------------------------------------------------------------------" -ForegroundColor DarkGray
    Write-Host "  MANAJEMEN PERFORMA & PEMELIHARAAN SISTEM:" -ForegroundColor Yellow
    Write-Host "  [O] Optimasi Performa (Cache Config, Routes, Views - Mempercepat Load Time)"
    Write-Host "  [C] Bersihkan Seluruh Cache Aplikasi (Clear Cache - Saat Update Fitur)"
    Write-Host "  [T] Jalankan Automated Test Suite (50 Tests / 306 Assertions)"
    Write-Host "  [L] Tampilkan 15 Baris Log Server Terakhir"
    Write-Host "  [5] " -NoNewline
    Write-Host "MATIKAN SERVER & KELUAR SECARA AMAN (GRACEFUL SHUTDOWN)" -ForegroundColor Red
    Write-Host "==========================================================================================" -ForegroundColor DarkGreen
}

try {
    Show-Dashboard
    while ($true) {
        if ($serverProc.HasExited) {
            Write-Host "`n[PERINGATAN] Server berhenti secara mendadak (Kode: $($serverProc.ExitCode))" -ForegroundColor Red
            if (Test-Path $LogFile) {
                Write-Host "Log Terakhir:" -ForegroundColor Yellow
                Get-Content $LogFile -Tail 10 | ForEach-Object { Write-Host "  $_" -ForegroundColor DarkRed }
            }
            Read-Host "`nTekan Enter untuk menutup..."
            break
        }

        Write-Host "`n  Pilih menu [1-3, O, C, T, L, 5]: " -NoNewline -ForegroundColor White
        $choice = Read-Host

        switch ($choice.Trim().ToUpper()) {
            "1" {
                Write-Host "  Membuka Halaman Login..." -ForegroundColor Cyan
                Start-Process "http://127.0.0.1:$ServerPort/login"
            }
            "2" {
                Write-Host "  Membuka Panel Backoffice HRD..." -ForegroundColor Cyan
                Start-Process "http://127.0.0.1:$ServerPort/admin"
            }
            "3" {
                Write-Host "  Membuka Portal Mandiri Pegawai..." -ForegroundColor Cyan
                Start-Process "http://127.0.0.1:$ServerPort/portal"
            }
            "O" {
                Write-Host "`n  [OPTIMASI] Mengompilasi dan meng-cache konfigurasi, route, dan template..." -ForegroundColor Yellow
                & php artisan optimize
                Write-Host "  [SUKSES] Sistem kini beroperasi dalam mode performa tinggi!" -ForegroundColor Green
                Start-Sleep -Seconds 1
                Show-Dashboard
            }
            "C" {
                Write-Host "`n  [CLEAR CACHE] Membersihkan cache konfigurasi, route, dan view..." -ForegroundColor Yellow
                & php artisan optimize:clear
                Write-Host "  [SUKSES] Seluruh cache sistem berhasil di-reset!" -ForegroundColor Green
                Start-Sleep -Seconds 1
                Show-Dashboard
            }
            "T" {
                Write-Host "`n  [PENGUJIAN] Menjalankan Test Suite Otomatis..." -ForegroundColor Yellow
                & php artisan test
                Write-Host "`nTekan Enter untuk kembali ke dashboard utama..." -ForegroundColor Gray
                Read-Host
                Show-Dashboard
            }
            "L" {
                Write-Host "`n  --- 15 LOG AKTIVITAS SERVER TERAKHIR ---" -ForegroundColor Cyan
                if (Test-Path $LogFile) {
                    Get-Content $LogFile -Tail 15 | ForEach-Object { Write-Host "  $_" -ForegroundColor Gray }
                } else {
                    Write-Host "  (Belum ada data log)" -ForegroundColor DarkGray
                }
                Write-Host "  ----------------------------------------" -ForegroundColor Cyan
            }
            "5" {
                break
            }
            default {
                Write-Host "  Pilihan tidak valid. Silakan ketik 1, 2, 3, O, C, T, L, atau 5." -ForegroundColor Yellow
            }
        }
    }
}
finally {
    Write-Host "`n==========================================================================================" -ForegroundColor Yellow
    Write-Host "  MENGHENTIKAN SERVER PT BPR BANDA RAYA..." -ForegroundColor Yellow
    Write-Host "==========================================================================================" -ForegroundColor Yellow
    if ($serverProc -and -not $serverProc.HasExited) {
        Write-Host "  [1/2] Menghentikan proses PHP Server (PID: $($serverProc.Id))..." -ForegroundColor Gray
        try {
            $serverProc.Kill()
            $serverProc.WaitForExit(2000)
        } catch {}
    }

    Write-Host "  [2/2] Memastikan port $ServerPort bersih dari proses zombie..." -ForegroundColor Gray
    try {
        Get-NetTCPConnection -LocalPort $ServerPort -ErrorAction SilentlyContinue | ForEach-Object {
            Stop-Process -Id $_.OwningProcess -Force -ErrorAction SilentlyContinue
        }
    } catch {}

    Write-Host "`n  SERVER CORE HRIS BERHASIL DIMATIKAN DENGAN BERSIH.`n" -ForegroundColor Green
}
