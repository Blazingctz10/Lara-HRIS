# =============================================================================
# PT BPR BANDA RAYA - CORE HRIS SERVER LAUNCHER & CONSOLE
# =============================================================================

$Host.UI.RawUI.WindowTitle = "PT BPR Banda Raya - Core HRIS Server Console"
$HrisPath = $PSScriptRoot

if (-not (Test-Path $HrisPath)) {
    Write-Host "Folder sistem tidak ditemukan di: $HrisPath" -ForegroundColor Red
    Read-Host "Tekan Enter untuk keluar..."
    exit 1
}

Set-Location $HrisPath

# 1. Pastikan folder log tersedia
$LogDir = Join-Path $HrisPath "storage\logs"
if (-not (Test-Path $LogDir)) {
    New-Item -ItemType Directory -Path $LogDir -Force | Out-Null
}
$LogFile = Join-Path $LogDir "serve.log"

# 2. Hentikan sisa proses PHP lama di port 8000
try {
    Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue | ForEach-Object {
        Stop-Process -Id $_.OwningProcess -Force -ErrorAction SilentlyContinue
    }
} catch {
    # Fallback jika Get-NetTCPConnection tidak tersedia
    $portUsers = netstat -ano | Select-String ":8000\s+.*LISTENING\s+(\d+)"
    foreach ($match in $portUsers) {
        if ($match.Matches[0].Groups[1].Value) {
            Stop-Process -Id $match.Matches[0].Groups[1].Value -Force -ErrorAction SilentlyContinue
        }
    }
}

# 3. Inisialisasi file log
"[$((Get-Date).ToString('yyyy-MM-dd HH:mm:ss'))] Memulai Core HRIS Server..." | Out-File -FilePath $LogFile -Encoding utf8 -Force

# 4. Jalankan PHP Built-in Server dengan aman tanpa file locking
$procInfo = New-Object System.Diagnostics.ProcessStartInfo
$procInfo.FileName = "php"
$procInfo.Arguments = "artisan serve --host=127.0.0.1 --port=8000"
$procInfo.WorkingDirectory = $HrisPath
$procInfo.RedirectStandardOutput = $true
$procInfo.RedirectStandardError = $true
$procInfo.UseShellExecute = $false
$procInfo.CreateNoWindow = $true

$serverProc = New-Object System.Diagnostics.Process
$serverProc.StartInfo = $procInfo

# Event handler untuk append log secara aman
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
    Write-Host "Gagal memulai php artisan serve. Periksa instalasi PHP." -ForegroundColor Red
    Read-Host "Tekan Enter untuk keluar..."
    exit 1
}

$serverProc.BeginOutputReadLine()
$serverProc.BeginErrorReadLine()

Start-Sleep -Milliseconds 800

try {
    while ($true) {
        if ($serverProc.HasExited) {
            Write-Host "`nServer berhenti mendadak dengan kode: $($serverProc.ExitCode)" -ForegroundColor Red
            if (Test-Path $LogFile) {
                Get-Content $LogFile -Tail 10 | ForEach-Object { Write-Host "  $_" -ForegroundColor DarkRed }
            }
            Read-Host "Tekan Enter untuk menutup..."
            break
        }

        Clear-Host
        Write-Host "==========================================================================================" -ForegroundColor DarkGreen
        Write-Host "                  PT BPR BANDA RAYA - CORE HRIS SERVER CONSOLE                            " -ForegroundColor Green
        Write-Host "==========================================================================================" -ForegroundColor DarkGreen
        Write-Host "  STATUS SERVER : " -NoNewline
        Write-Host "[ AKTIF ] (PID: $($serverProc.Id))" -ForegroundColor Green
        Write-Host "  ALAMAT URL    : " -NoNewline
        Write-Host "http://127.0.0.1:8000" -ForegroundColor Cyan
        Write-Host "------------------------------------------------------------------------------------------" -ForegroundColor DarkGray
        Write-Host "  PILIHAN MENU KONTROL:" -ForegroundColor Yellow
        Write-Host "  [1] Buka Halaman Login di Browser           (http://127.0.0.1:8000)"
        Write-Host "  [2] Buka Panel HRD Backoffice di Browser    (http://127.0.0.1:8000/admin)"
        Write-Host "  [3] Buka Portal Karyawan di Browser         (http://127.0.0.1:8000/portal)"
        Write-Host "  [4] Jalankan Pengujian Sistem               (php artisan test)"
        Write-Host "  [5] " -NoNewline
        Write-Host "MATIKAN SERVER & KELUAR DARI CMD" -ForegroundColor Red
        Write-Host "  [R] Refresh Tampilan Log (Pembaruan Otomatis Setiap 3 Detik)" -ForegroundColor DarkGray
        Write-Host "------------------------------------------------------------------------------------------" -ForegroundColor DarkGray
        Write-Host "  LOG AKTIVITAS SERVER TERAKHIR:" -ForegroundColor Cyan
        Write-Host "------------------------------------------------------------------------------------------" -ForegroundColor DarkGray

        if (Test-Path $LogFile) {
            $logLines = Get-Content $LogFile -Tail 10 -ErrorAction SilentlyContinue
            if ($logLines) {
                foreach ($line in $logLines) {
                    Write-Host "  $line" -ForegroundColor Gray
                }
            } else {
                Write-Host "  [Server aktif, siap menerima koneksi...]" -ForegroundColor DarkGray
            }
        }
        Write-Host "==========================================================================================" -ForegroundColor DarkGreen
        Write-Host "  Ketik 5 untuk MATIKAN SERVER, atau pilih aksi [1, 2, 3, 4, 5, R]: " -NoNewline -ForegroundColor White

        # Non-blocking input loop (3 detik auto-refresh)
        $inputKey = $null
        $waitIntervals = 30 # 30 x 100ms = 3.0 detik
        for ($i = 0; $i -lt $waitIntervals; $i++) {
            if ([Console]::KeyAvailable) {
                $rawKey = [Console]::ReadKey($true)
                $inputKey = $rawKey.KeyChar.ToString().ToUpper()
                break
            }
            Start-Sleep -Milliseconds 100
        }

        if ($inputKey -eq "1") {
            Start-Process "http://127.0.0.1:8000"
        }
        elseif ($inputKey -eq "2") {
            Start-Process "http://127.0.0.1:8000/admin"
        }
        elseif ($inputKey -eq "3") {
            Start-Process "http://127.0.0.1:8000/portal"
        }
        elseif ($inputKey -eq "4") {
            Clear-Host
            Write-Host "==========================================================================================" -ForegroundColor Yellow
            Write-Host "  MENJALANKAN TEST SUITE (php artisan test)..." -ForegroundColor Yellow
            Write-Host "==========================================================================================`n" -ForegroundColor Yellow
            php artisan test
            Write-Host "`nTekan tombol apa saja untuk kembali ke menu console..." -ForegroundColor Cyan
            [Console]::ReadKey($true) | Out-Null
        }
        elseif ($inputKey -eq "5") {
            break # Keluar ke blok finally
        }
        # Jika R atau timeout, loop berlanjut dan merefresh tampilan
    }
}
finally {
    Clear-Host
    Write-Host "==========================================================================================" -ForegroundColor Yellow
    Write-Host "  MENGHENTIKAN SERVER PT BPR BANDA RAYA..." -ForegroundColor Yellow
    Write-Host "==========================================================================================" -ForegroundColor Yellow
    Write-Host "  [1/2] Menutup proses PHP server (PID: $($serverProc.Id))..." -ForegroundColor Gray
    if ($serverProc -and -not $serverProc.HasExited) {
        try {
            $serverProc.Kill()
            $serverProc.WaitForExit(2000)
        } catch {}
    }

    Write-Host "  [2/2] Memastikan port 8000 bersih..." -ForegroundColor Gray
    try {
        Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue | ForEach-Object {
            Stop-Process -Id $_.OwningProcess -Force -ErrorAction SilentlyContinue
        }
    } catch {}

    Write-Host "`n  SERVER BERHASIL DIMATIKAN SECARA AMAN.`n" -ForegroundColor Green
    Start-Sleep -Milliseconds 800
}
