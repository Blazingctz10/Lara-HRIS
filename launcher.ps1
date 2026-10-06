# =============================================================================
# PT BPR BANDA RAYA - CORE HRIS SERVER CONSOLE & LAUNCHER
# Versi: 3.0 (Zero-Flicker, Infinite Wait, Auto-Detect Port, Robust)
# =============================================================================

[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$Host.UI.RawUI.WindowTitle = "PT BPR Banda Raya - Core HRIS Server Console"

# 1. Deteksi Path Sistem
$CurrentDir = $PSScriptRoot
if (Test-Path (Join-Path $CurrentDir "artisan")) {
    $HrisPath = $CurrentDir
} elseif (Test-Path (Join-Path $CurrentDir "hris-system\artisan")) {
    $HrisPath = Join-Path $CurrentDir "hris-system"
} else {
    Write-Host "`n[GALAT] Folder sistem Laravel tidak ditemukan!" -ForegroundColor Red
    Write-Host "Pastikan file ini berada di folder utama Lara-HRIS atau di dalam hris-system." -ForegroundColor Yellow
    Read-Host "`nTekan Enter untuk menutup jendela"
    exit 1
}

Set-Location $HrisPath

# Inisialisasi folder storage & cache agar tidak terjadi exception Laravel
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

function Show-Header {
    Clear-Host
    Write-Host "==========================================================================================" -ForegroundColor DarkGreen
    Write-Host "                  PT BPR BANDA RAYA - CORE HRIS SERVER CONSOLE                            " -ForegroundColor Green
    Write-Host "             Sistem Informasi Manajemen Kepegawaian & Presensi Biometrik                 " -ForegroundColor DarkCyan
    Write-Host "==========================================================================================" -ForegroundColor DarkGreen
}

Show-Header
Write-Host " [1/3] Memeriksa Instalasi PHP & Database..." -ForegroundColor Cyan

# Validasi PHP
$phpCmd = Get-Command "php" -ErrorAction SilentlyContinue
if (-not $phpCmd) {
    Write-Host "`n  [GALAT FATAL] Perintah 'php' tidak ditemukan dalam PATH Windows!" -ForegroundColor Red
    Write-Host "  Pastikan PHP (versi 8.2+) sudah terpasang dan ditambahkan ke System Environment PATH." -ForegroundColor Yellow
    Read-Host "`nTekan Enter untuk menutup jendela"
    exit 1
}

$phpVer = & php -r "echo PHP_VERSION;" 2>$null
Write-Host "  -> PHP Runtime Terdeteksi : v$phpVer" -ForegroundColor Gray

# Validasi Ekstensi SQLite & Pendukung
$hasSqlite = & php -r "echo extension_loaded('pdo_sqlite') ? '1' : '0';" 2>$null
if ($hasSqlite -ne "1") {
    Write-Host "  [PERINGATAN] Ekstensi pdo_sqlite belum aktif di php.ini." -ForegroundColor Yellow
}

$ServerPort = 8000
Write-Host " [2/3] Memeriksa Status Layanan di Port $ServerPort..." -ForegroundColor Cyan

# Cek apakah server sudah aktif dan berjalan
$serverAlreadyRunning = $false
$currentPid = $null

try {
    $conns = Get-NetTCPConnection -LocalPort $ServerPort -ErrorAction SilentlyContinue
    if ($conns) {
        $pids = $conns | Select-Object -ExpandProperty OwningProcess -Unique
        if ($pids) {
            $currentPid = $pids[0]
            # Cek respon HTTP
            try {
                $ping = Invoke-WebRequest -Uri "http://127.0.0.1:$ServerPort" -UseBasicParsing -TimeoutSec 2 -ErrorAction Stop
                if ($ping.StatusCode -eq 200 -or $ping.StatusCode -eq 302) {
                    $serverAlreadyRunning = $true
                }
            } catch {
                # Ada listener tapi belum respons
                $serverAlreadyRunning = $true
            }
        }
    }
} catch {
    # Fallback netstat
    $netstatMatch = netstat -ano | Select-String ":$ServerPort\s+.*LISTENING\s+(\d+)"
    if ($netstatMatch) {
        $currentPid = $netstatMatch[0].Matches[0].Groups[1].Value
        $serverAlreadyRunning = $true
    }
}

$serverProc = $null

if ($serverAlreadyRunning) {
    Write-Host "  -> Server Core HRIS SUDAH AKTIF dan siap digunakan di port $ServerPort (PID: $currentPid)" -ForegroundColor Green
} else {
    Write-Host " [3/3] Meluncurkan Instance Server Baru..." -ForegroundColor Cyan
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
        Write-Host "  [GALAT] Gagal menjalankan proses artisan serve." -ForegroundColor Red
        Read-Host "`nTekan Enter untuk menutup jendela"
        exit 1
    }

    $serverProc.BeginOutputReadLine()
    $serverProc.BeginErrorReadLine()

    # Beri waktu inisialisasi socket
    Start-Sleep -Seconds 1
    $currentPid = $serverProc.Id
    Write-Host "  -> Server berhasil diluncurkan (PID: $currentPid)!" -ForegroundColor Green
}

# Fungsi Menampilkan Menu Dashboard Konsol
function Print-Dashboard {
    Show-Header
    Write-Host "  STATUS SERVER : " -NoNewline
    Write-Host "[ AKTIF & BERJALAN ] (PID: $currentPid)" -ForegroundColor Green
    Write-Host "  ALAMAT URL    : " -NoNewline
    Write-Host "http://127.0.0.1:$ServerPort" -ForegroundColor Cyan
    Write-Host "  METODE ABSEN  : " -NoNewline
    Write-Host "Murni Mesin Sidik Jari Biometrik (Bukan GPS / Selfie)" -ForegroundColor Yellow
    Write-Host "------------------------------------------------------------------------------------------" -ForegroundColor DarkGray
    Write-Host "  PILIHAN MENU & AKSES APLIKASI:" -ForegroundColor Yellow
    Write-Host "  [1] Buka Halaman Login Utama          -> http://127.0.0.1:$ServerPort/login"
    Write-Host "  [2] Buka Panel Backoffice HRD / Admin -> http://127.0.0.1:$ServerPort/admin"
    Write-Host "  [3] Buka Portal Mandiri Karyawan      -> http://127.0.0.1:$ServerPort/portal"
    Write-Host "------------------------------------------------------------------------------------------" -ForegroundColor DarkGray
    Write-Host "  PEMELIHARAAN & PERFORMA SISTEM:" -ForegroundColor Yellow
    Write-Host "  [O] Optimasi Performa (Cache Konfigurasi, Route, View, Icons)"
    Write-Host "  [C] Bersihkan Cache (Reset Cache Aplikasi Saat Update Kode)"
    Write-Host "  [T] Jalankan Uji Otomatis (php artisan test - 50 Tests Lulus)"
    Write-Host "  [L] Tampilkan Log Aktivitas Server Terakhir"
    Write-Host "  [R] Restart Layanan Server di Port $ServerPort"
    Write-Host "  [5] " -NoNewline
    Write-Host "MATIKAN SERVER & KELUAR SECARA AMAN" -ForegroundColor Red
    Write-Host "==========================================================================================" -ForegroundColor DarkGreen
    Write-Host "  CATATAN: Konsol ini TIDAK AKAN menutup sendiri. Anda bebas memilih menu kapan saja." -ForegroundColor DarkGray
}

try {
    Print-Dashboard

    # Infinite Loop Murni Tanpa Timeout Otomatis - Konsol Tetap Terbuka Selamanya Sampai User Memilih Keluar
    while ($true) {
        Write-Host "`n  Pilih menu [1-3, O, C, T, L, R, 5]: " -NoNewline -ForegroundColor White
        $choice = Read-Host

        if ([string]::IsNullOrWhiteSpace($choice)) {
            continue
        }

        switch ($choice.Trim().ToUpper()) {
            "1" {
                Write-Host "  -> Membuka Halaman Login di Browser..." -ForegroundColor Cyan
                Start-Process "http://127.0.0.1:$ServerPort/login"
            }
            "2" {
                Write-Host "  -> Membuka Panel HRD Backoffice di Browser..." -ForegroundColor Cyan
                Start-Process "http://127.0.0.1:$ServerPort/admin"
            }
            "3" {
                Write-Host "  -> Membuka Portal Mandiri Pegawai di Browser..." -ForegroundColor Cyan
                Start-Process "http://127.0.0.1:$ServerPort/portal"
            }
            "O" {
                Write-Host "`n  [OPTIMASI] Mengompilasi konfigurasi, route, dan view untuk akselerasi performa..." -ForegroundColor Yellow
                & php artisan optimize
                Write-Host "`n  [SUKSES] Sistem telah berjalan dalam mode performa tinggi!" -ForegroundColor Green
                Write-Host "  Tekan Enter untuk kembali ke dashboard..." -ForegroundColor Gray
                Read-Host
                Print-Dashboard
            }
            "C" {
                Write-Host "`n  [RESET CACHE] Membersihkan seluruh cache aplikasi..." -ForegroundColor Yellow
                & php artisan optimize:clear
                Write-Host "`n  [SUKSES] Seluruh cache berhasil di-reset!" -ForegroundColor Green
                Write-Host "  Tekan Enter untuk kembali ke dashboard..." -ForegroundColor Gray
                Read-Host
                Print-Dashboard
            }
            "T" {
                Write-Host "`n  [TEST SUITE] Menjalankan seluruh pengujian otomatis..." -ForegroundColor Yellow
                & php artisan test
                Write-Host "`n  Tekan Enter untuk kembali ke dashboard..." -ForegroundColor Gray
                Read-Host
                Print-Dashboard
            }
            "L" {
                Write-Host "`n  === LOG SERVER TERAKHIR ===" -ForegroundColor Cyan
                if (Test-Path $LogFile) {
                    Get-Content $LogFile -Tail 20 | ForEach-Object { Write-Host "  $_" -ForegroundColor Gray }
                } else {
                    Write-Host "  (Belum ada catatan log)" -ForegroundColor DarkGray
                }
                Write-Host "  ===========================" -ForegroundColor Cyan
            }
            "R" {
                Write-Host "`n  [RESTART] Me-restart proses server di port $ServerPort..." -ForegroundColor Yellow
                try {
                    Get-NetTCPConnection -LocalPort $ServerPort -ErrorAction SilentlyContinue | ForEach-Object {
                        Stop-Process -Id $_.OwningProcess -Force -ErrorAction SilentlyContinue
                    }
                } catch {}
                Start-Sleep -Seconds 1

                # Luncurkan kembali
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
                $serverProc.Start() | Out-Null
                $serverProc.BeginOutputReadLine()
                $serverProc.BeginErrorReadLine()
                Start-Sleep -Seconds 1
                $currentPid = $serverProc.Id
                Write-Host "  [SUKSES] Server berhasil dimulai ulang (PID: $currentPid)!" -ForegroundColor Green
                Start-Sleep -Seconds 1
                Print-Dashboard
            }
            "5" {
                Write-Host "`n  Mematikan server dan menutup konsol..." -ForegroundColor Yellow
                break
            }
            default {
                Write-Host "  Pilihan '$choice' tidak dikenali. Ketik 1, 2, 3, O, C, T, L, R, atau 5." -ForegroundColor Yellow
            }
        }
    }
}
finally {
    Write-Host "`n==========================================================================================" -ForegroundColor Yellow
    Write-Host "  MENGHENTIKAN LAYANAN SERVER PT BPR BANDA RAYA..." -ForegroundColor Yellow
    Write-Host "==========================================================================================" -ForegroundColor Yellow

    if ($serverProc -and -not $serverProc.HasExited) {
        Write-Host "  [1/2] Menutup proses server bawaan (PID: $($serverProc.Id))..." -ForegroundColor Gray
        try {
            $serverProc.Kill()
            $serverProc.WaitForExit(2000)
        } catch {}
    }

    Write-Host "  [2/2] Membersihkan port $ServerPort..." -ForegroundColor Gray
    try {
        Get-NetTCPConnection -LocalPort $ServerPort -ErrorAction SilentlyContinue | ForEach-Object {
            Stop-Process -Id $_.OwningProcess -Force -ErrorAction SilentlyContinue
        }
    } catch {}

    Write-Host "`n  SERVER CORE HRIS BERHASIL DIMATIKAN SECARA AMAN.`n" -ForegroundColor Green
    Write-Host "Tekan Enter untuk menutup jendela konsol ini..." -ForegroundColor Cyan
    Read-Host
}
