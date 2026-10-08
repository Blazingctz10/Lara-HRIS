# =============================================================================
# PT BPR BANDA RAYA - CORE HRIS ENTERPRISE SERVER LAUNCHER & CONSOLE
# Versi: 4.0 (Enterprise Production, Zero-Flicker, Multi-IP LAN Support)
# =============================================================================

[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$Host.UI.RawUI.WindowTitle = "PT BPR Banda Raya - Core HRIS Server Console"

# 1. Deteksi Direktori Sistem
$CurrentDir = $PSScriptRoot
if (Test-Path (Join-Path $CurrentDir "artisan")) {
    $HrisPath = $CurrentDir
} elseif (Test-Path (Join-Path $CurrentDir "hris-system\artisan")) {
    $HrisPath = Join-Path $CurrentDir "hris-system"
} else {
    Write-Host "`n[GALAT FATAL] Folder sistem Laravel tidak ditemukan!" -ForegroundColor Red
    Write-Host "Pastikan file launcher.ps1 berada di dalam folder Lara-HRIS atau hris-system." -ForegroundColor Yellow
    Read-Host "`nTekan Enter untuk keluar"
    exit 1
}

Set-Location $HrisPath

# Inisialisasi struktur folder sistem
$LogDir = Join-Path $HrisPath "storage\logs"
$FrameworkCache = Join-Path $HrisPath "storage\framework\cache"
$FrameworkSessions = Join-Path $HrisPath "storage\framework\sessions"
$FrameworkViews = Join-Path $HrisPath "storage\framework\views"
$BootstrapCache = Join-Path $HrisPath "bootstrap\cache"
$BackupDir = Join-Path $HrisPath "storage\app\backups"

@($LogDir, $FrameworkCache, $FrameworkSessions, $FrameworkViews, $BootstrapCache, $BackupDir) | ForEach-Object {
    if (-not (Test-Path $_)) {
        New-Item -ItemType Directory -Path $_ -Force | Out-Null
    }
}

$LogFile = Join-Path $LogDir "serve.log"
$DatabaseFile = Join-Path $HrisPath "database\database.sqlite"
if (-not (Test-Path $DatabaseFile)) {
    New-Item -ItemType File -Path $DatabaseFile -Force | Out-Null
}

# Deteksi IP LAN Kantor (untuk akses staf di jaringan lokal BPR)
$LocalLanIp = $null
try {
    $ipObj = Get-NetIPAddress -AddressFamily IPv4 -ErrorAction SilentlyContinue | Where-Object { 
        $_.InterfaceAlias -notlike "*Loopback*" -and 
        $_.InterfaceAlias -notlike "*vEthernet*" -and 
        $_.IPAddress -notlike "169.254*" -and
        $_.IPAddress -ne "127.0.0.1"
    } | Select-Object -First 1
    if ($ipObj) {
        $LocalLanIp = $ipObj.IPAddress
    }
} catch {}

if (-not $LocalLanIp) {
    $LocalLanIp = "127.0.0.1"
}

function Show-Header {
    Clear-Host
    Write-Host "==========================================================================================" -ForegroundColor DarkGreen
    Write-Host "                  PT BPR BANDA RAYA - CORE HRIS SERVER CONSOLE                            " -ForegroundColor Green
    Write-Host "        Platform Enterprise Siklus Hidup Kepegawaian & Presensi Mesin Sidik Jari          " -ForegroundColor DarkCyan
    Write-Host "==========================================================================================" -ForegroundColor DarkGreen
}

Show-Header
Write-Host " [1/3] Memeriksa Lingkungan PHP & Kesiapan Database..." -ForegroundColor Cyan

# Cek ketersediaan PHP (Mendukung PHP Portable Flashdisk atau Sistem PATH)
$phpBinary = "php"
$portablePhpPaths = @(
    Join-Path $CurrentDir "php\php.exe",
    Join-Path $HrisPath "php\php.exe",
    Join-Path $CurrentDir "tools\php\php.exe"
)

$isPortable = $false
foreach ($pPath in $portablePhpPaths) {
    if (Test-Path $pPath) {
        $phpBinary = $pPath
        $isPortable = $true
        break
    }
}

if ($isPortable) {
    $portablePhpDir = Split-Path $phpBinary -Parent
    $env:PATH = "$portablePhpDir;" + $env:PATH
    Write-Host "  -> Menggunakan PHP Portable Flashdisk: $phpBinary" -ForegroundColor Yellow
} else {
    $phpCmd = Get-Command "php" -ErrorAction SilentlyContinue
    if (-not $phpCmd) {
        Write-Host "`n  [GALAT FATAL] PHP tidak ditemukan di sistem Windows maupun di folder Flashdisk!" -ForegroundColor Red
        Write-Host "  SOLUSI UNTUK PENGGUNAAN DI FLASHDISK:" -ForegroundColor Yellow
        Write-Host "  1. Salin folder 'php' portable (berisi php.exe) langsung ke dalam Flashdisk ini, ATAU" -ForegroundColor Gray
        Write-Host "  2. Pasang PHP (versi 8.2+) di komputer ini dan tambahkan ke System PATH." -ForegroundColor Gray
        Read-Host "`nTekan Enter untuk menutup jendela"
        exit 1
    }
}

$phpVer = & $phpBinary -r "echo PHP_VERSION;" 2>$null
Write-Host "  -> PHP Runtime Terpasang  : v$phpVer $(if ($isPortable) {'[PORTABLE USB]'} else {'[SYSTEM]'})" -ForegroundColor Gray
Write-Host "  -> Database Terkonfigurasi: SQLite ($DatabaseFile)" -ForegroundColor Gray

# Validasi port 8000
$ServerPort = 8000
Write-Host " [2/3] Memeriksa Port Jaringan $ServerPort..." -ForegroundColor Cyan

$serverAlreadyRunning = $false
$currentPid = $null

try {
    $conns = Get-NetTCPConnection -LocalPort $ServerPort -ErrorAction SilentlyContinue
    if ($conns) {
        $pids = $conns | Select-Object -ExpandProperty OwningProcess -Unique
        if ($pids) {
            $currentPid = $pids[0]
            try {
                $ping = Invoke-WebRequest -Uri "http://127.0.0.1:$ServerPort" -UseBasicParsing -TimeoutSec 2 -ErrorAction Stop
                if ($ping.StatusCode -eq 200 -or $ping.StatusCode -eq 302) {
                    $serverAlreadyRunning = $true
                }
            } catch {
                $serverAlreadyRunning = $true
            }
        }
    }
} catch {
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
    Write-Host " [3/3] Menjalankan Server Core HRIS (--host=0.0.0.0 --port=$ServerPort)..." -ForegroundColor Cyan
    "[$((Get-Date).ToString('yyyy-MM-dd HH:mm:ss'))] Memulai Server Core HRIS PT BPR Banda Raya..." | Out-File -FilePath $LogFile -Encoding utf8 -Force

    $procInfo = New-Object System.Diagnostics.ProcessStartInfo
    $procInfo.FileName = $phpBinary
    # Menggunakan host 0.0.0.0 agar bisa diakses baik dari localhost maupun LAN kantor
    $procInfo.Arguments = "artisan serve --host=0.0.0.0 --port=$ServerPort"
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
        Write-Host "`n  [GALAT] Gagal meluncurkan proses artisan serve." -ForegroundColor Red
        Read-Host "`nTekan Enter untuk menutup jendela"
        exit 1
    }

    $serverProc.BeginOutputReadLine()
    $serverProc.BeginErrorReadLine()

    Start-Sleep -Seconds 1
    $currentPid = $serverProc.Id
    Write-Host "  -> Server berhasil diluncurkan (PID: $currentPid)!" -ForegroundColor Green
}

function Print-Dashboard {
    Show-Header
    Write-Host "  STATUS SERVER        : " -NoNewline
    Write-Host "[ AKTIF & BERJALAN ] (PID: $currentPid)" -ForegroundColor Green
    Write-Host "  AKSES LOKAL          : " -NoNewline
    Write-Host "http://127.0.0.1:$ServerPort" -ForegroundColor Cyan
    Write-Host "  AKSES JARINGAN KANTOR: " -NoNewline
    Write-Host "http://$($LocalLanIp):$ServerPort" -ForegroundColor Yellow
    Write-Host "  METODE PRESENSI      : " -NoNewline
    Write-Host "Murni Mesin Sidik Jari Biometrik (Fingerprint Solution X105)" -ForegroundColor Green
    Write-Host "------------------------------------------------------------------------------------------" -ForegroundColor DarkGray
    Write-Host "  PILIHAN MENU & AKSES APLIKASI:" -ForegroundColor Yellow
    Write-Host "  [1] Buka Halaman Login Utama          -> http://127.0.0.1:$ServerPort/login"
    Write-Host "  [2] Buka Panel Backoffice HRD / Admin -> http://127.0.0.1:$ServerPort/admin"
    Write-Host "  [3] Buka Portal Mandiri Karyawan      -> http://127.0.0.1:$ServerPort/portal"
    Write-Host "  [4] Salin Tautan Akses Jaringan LAN Kantor untuk Komputer Staf Lain"
    Write-Host "------------------------------------------------------------------------------------------" -ForegroundColor DarkGray
    Write-Host "  PEMELIHARAAN, PERFORMA & CADANGAN SISTEM:" -ForegroundColor Yellow
    Write-Host "  [O] Optimasi Performa Tinggi (Kompilasi Cache Config, Route, View, Icons)"
    Write-Host "  [C] Bersihkan Seluruh Cache (Reset Cache Aplikasi Saat Update Kode)"
    Write-Host "  [B] Buat Cadangan Database Instan (Backup Snapshot database.sqlite)"
    Write-Host "  [M] Sinkronisasi Migrasi Database (php artisan migrate)"
    Write-Host "  [T] Jalankan Uji Otomatis Sistem (php artisan test - 50 Tests)"
    Write-Host "  [L] Tampilkan 20 Log Aktivitas Server Terakhir"
    Write-Host "  [R] Restart Server (Muat Ulang Layanan di Port $ServerPort)"
    Write-Host "  [5] " -NoNewline
    Write-Host "MATIKAN SERVER & KELUAR SECARA AMAN (GRACEFUL SHUTDOWN)" -ForegroundColor Red
    Write-Host "==========================================================================================" -ForegroundColor DarkGreen
    Write-Host "  CATATAN: Konsol ini aktif terus-menerus dan TIDAK AKAN menutup sendiri." -ForegroundColor DarkGray
}

try {
    Print-Dashboard

    # Infinite Loop Murni Tanpa Auto-Close / Timeout
    while ($true) {
        Write-Host "`n  Pilih menu [1-4, O, C, B, M, T, L, R, 5]: " -NoNewline -ForegroundColor White
        $choice = Read-Host

        if ([string]::IsNullOrWhiteSpace($choice)) {
            continue
        }

        switch ($choice.Trim().ToUpper()) {
            "1" {
                Write-Host "  -> Membuka Halaman Login Utama di Browser..." -ForegroundColor Cyan
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
            "4" {
                $lanUrl = "http://$($LocalLanIp):$ServerPort"
                Write-Host "`n  =================================================================" -ForegroundColor Cyan
                Write-Host "  INFORMASI AKSES JARINGAN LOKAL (LAN) KANTOR BPR BANDA RAYA" -ForegroundColor Yellow
                Write-Host "  =================================================================" -ForegroundColor Cyan
                Write-Host "  Bagikan alamat URL berikut kepada staf lain di kantor yang terhubung" -ForegroundColor Gray
                Write-Host "  ke jaringan WiFi/LAN BPR Banda Raya yang sama:`n" -ForegroundColor Gray
                Write-Host "  Tautan Portal Staf : $lanUrl/portal" -ForegroundColor Green
                Write-Host "  Tautan Login Utama : $lanUrl/login" -ForegroundColor Green
                Write-Host "  Tautan Admin HRD   : $lanUrl/admin" -ForegroundColor Green
                Write-Host "  =================================================================" -ForegroundColor Cyan
                try {
                    Set-Clipboard -Value "$lanUrl/login"
                    Write-Host "  [INFO] URL '$lanUrl/login' telah otomatis disalin ke Clipboard!" -ForegroundColor Gray
                } catch {}
            }
            "O" {
                Write-Host "`n  [OPTIMASI] Mengompilasi konfigurasi, route, dan view untuk akselerasi performa..." -ForegroundColor Yellow
                & $phpBinary artisan optimize
                Write-Host "`n  [SUKSES] Sistem telah berjalan dalam mode performa tinggi!" -ForegroundColor Green
                Write-Host "  Tekan Enter untuk kembali ke dashboard..." -ForegroundColor Gray
                Read-Host
                Print-Dashboard
            }
            "C" {
                Write-Host "`n  [RESET CACHE] Membersihkan seluruh cache aplikasi..." -ForegroundColor Yellow
                & $phpBinary artisan optimize:clear
                Write-Host "`n  [SUKSES] Seluruh cache berhasil di-reset!" -ForegroundColor Green
                Write-Host "  Tekan Enter untuk kembali ke dashboard..." -ForegroundColor Gray
                Read-Host
                Print-Dashboard
            }
            "B" {
                Write-Host "`n  [BACKUP] Membuat salinan cadangan database SQLite..." -ForegroundColor Yellow
                $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
                $destBackup = Join-Path $BackupDir "manual_backup_$timestamp.sqlite"
                Copy-Item -Path $DatabaseFile -Destination $destBackup -Force
                $backupSize = (Get-Item $destBackup).Length / 1KB
                Write-Host "  [SUKSES] Cadangan database berhasil disimpan di:" -ForegroundColor Green
                Write-Host "  -> $destBackup ($([math]::Round($backupSize, 2)) KB)" -ForegroundColor Cyan
                Write-Host "  Tekan Enter untuk kembali ke dashboard..." -ForegroundColor Gray
                Read-Host
                Print-Dashboard
            }
            "M" {
                Write-Host "`n  [MIGRASI] Memeriksa dan menjalankan migrasi database..." -ForegroundColor Yellow
                & $phpBinary artisan migrate --force
                Write-Host "`n  [SUKSES] Sinkronisasi struktur tabel database selesai!" -ForegroundColor Green
                Write-Host "  Tekan Enter untuk kembali ke dashboard..." -ForegroundColor Gray
                Read-Host
                Print-Dashboard
            }
            "T" {
                Write-Host "`n  [TEST SUITE] Menyiapkan lingkungan dan menjalankan pengujian otomatis..." -ForegroundColor Yellow
                & $phpBinary artisan optimize:clear | Out-Null
                & $phpBinary artisan test
                Write-Host "`n  Tekan Enter untuk kembali ke dashboard..." -ForegroundColor Gray
                Read-Host
                Print-Dashboard
            }
            "L" {
                Write-Host "`n  === 20 LOG AKTIVITAS SERVER TERAKHIR ===" -ForegroundColor Cyan
                if (Test-Path $LogFile) {
                    Get-Content $LogFile -Tail 20 | ForEach-Object { Write-Host "  $_" -ForegroundColor Gray }
                } else {
                    Write-Host "  (Belum ada data log aktivitas)" -ForegroundColor DarkGray
                }
                Write-Host "  =======================================" -ForegroundColor Cyan
            }
            "R" {
                Write-Host "`n  [RESTART] Me-restart proses server di port $ServerPort..." -ForegroundColor Yellow
                try {
                    Get-NetTCPConnection -LocalPort $ServerPort -ErrorAction SilentlyContinue | ForEach-Object {
                        Stop-Process -Id $_.OwningProcess -Force -ErrorAction SilentlyContinue
                    }
                } catch {}
                Start-Sleep -Seconds 1

                $procInfo = New-Object System.Diagnostics.ProcessStartInfo
                $procInfo.FileName = $phpBinary
                $procInfo.Arguments = "artisan serve --host=0.0.0.0 --port=$ServerPort"
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
                Write-Host "`n  Mematikan server dan menutup konsol secara aman..." -ForegroundColor Yellow
                break
            }
            default {
                Write-Host "  Pilihan '$choice' tidak valid. Silakan pilih menu yang tersedia." -ForegroundColor Yellow
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

    Write-Host "`n  SERVER CORE HRIS TELAH DIMATIKAN SECARA AMAN.`n" -ForegroundColor Green
    Write-Host "Tekan Enter untuk menutup jendela konsol ini..." -ForegroundColor Cyan
    Read-Host
}
