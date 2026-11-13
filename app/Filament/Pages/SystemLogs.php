<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

class SystemLogs extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Log Kesalahan Sistem';

    protected static ?string $title = 'Log Kesalahan & Diagnostik Server';

    protected static string | UnitEnum | null $navigationGroup = 'Pengaturan & Sistem';

    protected static ?int $navigationSort = 98;

    protected string $view = 'filament.pages.system-logs';

    public ?string $selectedFile = null;

    public string $levelFilter = 'all';

    public string $searchQuery = '';

    public bool $revealPath = false;

    public bool $confirmClear = false;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $files = $this->getLogFiles();
        if (! empty($files)) {
            $this->selectedFile = $files[0]['name'];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh_logs')
                ->label('Segarkan Log')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    Notification::make()
                        ->title('Log Disegarkan')
                        ->body('Daftar berkas log dan entri terbaru telah dimuat ulang.')
                        ->success()
                        ->send();
                }),

            Action::make('prune_old_logs')
                ->label('Bersihkan Log Kedaluwarsa')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Bersihkan Log Harian Lama')
                ->modalDescription('Menghapus berkas log harian biasa yang lebih tua dari 14 hari (arsip critical tetap dipertahankan 60 hari).')
                ->action(function () {
                    $deleted = $this->pruneExpiredLogs();
                    Notification::make()
                        ->title('Pembersihan Selesai')
                        ->body("{$deleted} berkas log kedaluwarsa berhasil dihapus.")
                        ->success()
                        ->send();
                }),
        ];
    }

    /**
     * @return array<int, array{name: string, size_kb: float, modified: string, is_critical: bool}>
     */
    public function getLogFiles(): array
    {
        $logDir = storage_path('logs');
        if (! is_dir($logDir)) {
            return [];
        }

        $files = glob($logDir . DIRECTORY_SEPARATOR . '*.log') ?: [];
        usort($files, fn ($a, $b) => (@filemtime($b) ?: 0) <=> (@filemtime($a) ?: 0));

        $result = [];
        foreach ($files as $path) {
            if (! is_file($path)) {
                continue;
            }
            $name = basename($path);
            $size = @filesize($path) ?: 0;
            $mtime = @filemtime($path) ?: time();

            $result[] = [
                'name' => $name,
                'size_kb' => round($size / 1024, 2),
                'modified' => date('d M Y, H:i', $mtime),
                'is_critical' => str_starts_with($name, 'critical'),
            ];
        }

        return $result;
    }

    /**
     * Membaca maksimal 256 KB terakhir dari file log secara aman (anti Out-Of-Memory).
     *
     * @return array<int, array{timestamp: string, env: string, level: string, message: string, trace: string}>
     */
    public function getLogEntries(): array
    {
        $path = $this->resolveSafeLogPath($this->selectedFile);
        if (! $path || ! is_readable($path)) {
            return [];
        }

        $fileSize = @filesize($path);
        if (! $fileSize) {
            return [];
        }

        $maxReadBytes = 262144; // 256 KB tail failsafe
        $handle = @fopen($path, 'rb');
        if (! $handle) {
            return [];
        }

        $offset = max(0, $fileSize - $maxReadBytes);
        if ($offset > 0) {
            @fseek($handle, $offset, SEEK_SET);
            fgets($handle); // buang baris pertama yang mungkin terpotong di tengah
        }

        $content = stream_get_contents($handle) ?: '';
        @fclose($handle);

        $pattern = '/^\[(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+([a-zA-Z0-9_-]+)\.([A-Z]+):\s+(.*)$/';
        $lines = preg_split("/\r\n|\n|\r/", $content) ?: [];

        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match($pattern, $line, $m)) {
                if ($current !== null) {
                    $entries[] = $current;
                }
                $current = [
                    'timestamp' => $m[1],
                    'env' => $m[2],
                    'level' => strtoupper($m[3]),
                    'message' => trim($m[4]),
                    'trace' => '',
                ];
            } elseif ($current !== null && trim($line) !== '') {
                if (strlen($current['trace']) < 2000) {
                    $current['trace'] .= ($current['trace'] ? "\n" : '') . $line;
                }
            }
        }

        if ($current !== null) {
            $entries[] = $current;
        }

        // Terbaru di atas
        $entries = array_reverse($entries);

        // Filter level & search query
        $filtered = array_filter($entries, function (array $entry): bool {
            if ($this->levelFilter === 'error' && ! in_array($entry['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true)) {
                return false;
            }
            if ($this->levelFilter === 'warning' && $entry['level'] !== 'WARNING') {
                return false;
            }
            if ($this->levelFilter === 'info' && ! in_array($entry['level'], ['INFO', 'NOTICE', 'DEBUG'], true)) {
                return false;
            }
            if ($this->searchQuery !== '') {
                $q = mb_strtolower($this->searchQuery);
                $haystack = mb_strtolower($entry['message'] . ' ' . $entry['trace'] . ' ' . $entry['timestamp']);
                if (! str_contains($haystack, $q)) {
                    return false;
                }
            }

            return true;
        });

        return array_slice(array_values($filtered), 0, 120);
    }

    public function downloadSelectedLog(): ?BinaryFileResponse
    {
        abort_unless(static::canAccess(), 403);

        $path = $this->resolveSafeLogPath($this->selectedFile);
        if (! $path) {
            Notification::make()
                ->title('[SYS404] Berkas Log Tidak Ditemukan')
                ->danger()
                ->send();

            return null;
        }

        return response()->download($path);
    }

    public function clearSelectedLog(): void
    {
        abort_unless(static::canAccess(), 403);

        $path = $this->resolveSafeLogPath($this->selectedFile);
        if (! $path || ! is_writable($path)) {
            Notification::make()
                ->title('[SYS500] Gagal Mengosongkan Berkas Log')
                ->danger()
                ->send();

            return;
        }

        // Truncate aman (tidak unlink agar tidak bentrok file lock di Windows)
        @file_put_contents($path, '', LOCK_EX);
        $this->confirmClear = false;

        Notification::make()
            ->title('Berkas Log Dikosongkan')
            ->body("Isi berkas {$this->selectedFile} berhasil dikosongkan dengan aman.")
            ->success()
            ->send();
    }

    public function getStorageSummary(): array
    {
        $files = $this->getLogFiles();
        $totalKb = array_sum(array_column($files, 'size_kb'));
        $freeBytes = @disk_free_space(storage_path('logs'));
        $freeGb = $freeBytes !== false ? round($freeBytes / (1024 * 1024 * 1024), 2) : null;

        return [
            'file_count' => count($files),
            'total_mb' => round($totalKb / 1024, 2),
            'disk_free_gb' => $freeGb,
            'log_stack' => config('logging.default') . ' (' . implode(', ', config('logging.channels.stack.channels', [])) . ')',
            'daily_days' => config('logging.channels.daily.days', 14),
            'critical_days' => config('logging.channels.critical_archive.days', 60),
        ];
    }

    protected function pruneExpiredLogs(): int
    {
        $logDir = storage_path('logs');
        if (! is_dir($logDir)) {
            return 0;
        }

        $dailyDays = (int) config('logging.channels.daily.days', 14);
        $criticalDays = (int) config('logging.channels.critical_archive.days', 60);
        $deleted = 0;

        foreach (glob($logDir . DIRECTORY_SEPARATOR . '*.log') ?: [] as $path) {
            if (! is_file($path)) {
                continue;
            }
            $name = basename($path);
            $mtime = @filemtime($path) ?: time();
            $ageDays = (time() - $mtime) / 86400;

            $limit = str_starts_with($name, 'critical') ? $criticalDays : $dailyDays;
            if ($ageDays > $limit && File::delete($path)) {
                $deleted++;
            }
        }

        $files = $this->getLogFiles();
        $this->selectedFile = $files[0]['name'] ?? null;

        return $deleted;
    }

    protected function resolveSafeLogPath(?string $filename): ?string
    {
        if (! $filename) {
            return null;
        }

        $clean = basename($filename);
        if (! preg_match('/^[a-zA-Z0-9._-]+\.log$/', $clean)) {
            return null;
        }

        $fullPath = storage_path('logs' . DIRECTORY_SEPARATOR . $clean);

        return is_file($fullPath) ? $fullPath : null;
    }
}
