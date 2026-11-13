<x-filament-panels::page>
    @php
        $files = $this->getLogFiles();
        $entries = $this->getLogEntries();
        $summary = $this->getStorageSummary();
        $realPath = storage_path('logs');
    @endphp

    <style>
        .m3-syslog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .m3-syslog-stat {
            background: var(--glass-surface-card, #ffffff);
            border: 1px solid var(--glass-border, rgba(14, 104, 56, 0.14));
            border-radius: 16px;
            padding: 1rem 1.2rem;
        }
        :is(.dark) .m3-syslog-stat {
            background: #141E18;
            border-color: rgba(255, 255, 255, 0.08);
        }
        .m3-syslog-stat-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 0.35rem;
        }
        :is(.dark) .m3-syslog-stat-label { color: #94a3b8; }

        .m3-syslog-stat-value {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
        }
        :is(.dark) .m3-syslog-stat-value { color: #f8fafc; }

        .m3-syslog-stat-sub {
            font-size: 0.76rem;
            color: #64748b;
            margin-top: 0.2rem;
        }
        :is(.dark) .m3-syslog-stat-sub { color: #94a3b8; }

        .m3-syslog-card {
            background: var(--glass-surface-card, #ffffff);
            border: 1px solid var(--glass-border, rgba(14, 104, 56, 0.14));
            border-radius: 16px;
            padding: 1.25rem;
        }
        :is(.dark) .m3-syslog-card {
            background: #141E18;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .m3-syslog-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.85rem;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }

        .m3-syslog-select, .m3-syslog-input {
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: #f8fafc;
            color: #0f172a;
            font-size: 0.84rem;
            font-weight: 600;
            padding: 0.5rem 0.85rem;
        }
        :is(.dark) .m3-syslog-select,
        :is(.dark) .m3-syslog-input {
            background: #0d1410;
            border-color: rgba(255, 255, 255, 0.12);
            color: #f8fafc;
        }

        .m3-syslog-pill {
            border-radius: 9999px;
            padding: 0.38rem 0.9rem;
            font-size: 0.78rem;
            font-weight: 700;
            border: 1px solid rgba(148, 163, 184, 0.3);
            background: transparent;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        :is(.dark) .m3-syslog-pill { color: #cbd5e1; border-color: rgba(255, 255, 255, 0.14); }
        .m3-syslog-pill.active {
            background: #0E6838;
            color: #ffffff;
            border-color: #0E6838;
        }
        :is(.dark) .m3-syslog-pill.active {
            background: rgba(74, 222, 128, 0.22);
            color: #4ADE80;
            border-color: rgba(74, 222, 128, 0.45);
        }

        .m3-log-entry {
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-left-width: 4px;
            border-radius: 14px;
            padding: 0.85rem 1rem;
            margin-bottom: 0.7rem;
            background: #f8fafc;
        }
        :is(.dark) .m3-log-entry {
            background: #0d1410;
            border-color: rgba(255, 255, 255, 0.08);
        }
        .m3-log-entry.lvl-error { border-left-color: #dc2626; }
        .m3-log-entry.lvl-warning { border-left-color: #ea580c; }
        .m3-log-entry.lvl-info { border-left-color: #0E6838; }

        .m3-log-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.4rem;
        }
        .m3-log-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 800;
            font-family: monospace;
        }
        .m3-log-badge.error { background: #fee2e2; color: #991b1b; }
        .m3-log-badge.warning { background: #ffedd5; color: #9a3412; }
        .m3-log-badge.info { background: #dcfce7; color: #166534; }
        :is(.dark) .m3-log-badge.error { background: rgba(239, 68, 68, 0.22); color: #fca5a5; }
        :is(.dark) .m3-log-badge.warning { background: rgba(249, 115, 22, 0.22); color: #fdba74; }
        :is(.dark) .m3-log-badge.info { background: rgba(34, 197, 94, 0.22); color: #86efac; }

        .m3-log-time {
            font-family: monospace;
            font-size: 0.76rem;
            color: #64748b;
        }
        :is(.dark) .m3-log-time { color: #94a3b8; }

        .m3-log-msg {
            font-size: 0.84rem;
            font-weight: 600;
            color: #0f172a;
            word-break: break-word;
        }
        :is(.dark) .m3-log-msg { color: #f1f5f9; }

        .m3-log-trace {
            margin-top: 0.55rem;
            background: #090e0b;
            color: #86efac;
            font-family: monospace;
            font-size: 0.74rem;
            padding: 0.75rem;
            border-radius: 10px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .m3-path-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: monospace;
            font-size: 0.76rem;
            color: #475569;
            background: rgba(148, 163, 184, 0.14);
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
        }
        :is(.dark) .m3-path-chip { color: #cbd5e1; background: rgba(255, 255, 255, 0.08); }
    </style>

    {{-- Ringkasan Kapasitas & Redundansi Failsafe --}}
    <div class="m3-syslog-grid">
        <div class="m3-syslog-stat">
            <div class="m3-syslog-stat-label">Total Berkas Log</div>
            <div class="m3-syslog-stat-value">{{ $summary['file_count'] }} Berkas</div>
            <div class="m3-syslog-stat-sub">Ukuran total: {{ $summary['total_mb'] }} MB</div>
        </div>
        <div class="m3-syslog-stat">
            <div class="m3-syslog-stat-label">Ruang Disk Tersedia</div>
            <div class="m3-syslog-stat-value">{{ $summary['disk_free_gb'] !== null ? $summary['disk_free_gb'] . ' GB' : 'Tidak Terbaca' }}</div>
            <div class="m3-syslog-stat-sub">Batas auto-trim failsafe: 50 MB/berkas</div>
        </div>
        <div class="m3-syslog-stat">
            <div class="m3-syslog-stat-label">Redundansi & Retensi</div>
            <div class="m3-syslog-stat-value">{{ $summary['daily_days'] }} Hari / {{ $summary['critical_days'] }} Hari</div>
            <div class="m3-syslog-stat-sub">Harian ({{ $summary['daily_days'] }}h) + Arsip Kritis ({{ $summary['critical_days'] }}h)</div>
        </div>
        <div class="m3-syslog-stat">
            <div class="m3-syslog-stat-label">Lokasi Direktori Server</div>
            <div style="display: flex; align-items: center; gap: 0.45rem; margin-top: 0.25rem;">
                <span class="m3-path-chip">{{ $revealPath ? $realPath : '••••••••••••/storage/logs' }}</span>
                <button type="button" wire:click="$toggle('revealPath')" class="m3-syslog-pill" style="padding: 0.2rem 0.55rem; font-size: 0.7rem;">
                    {{ $revealPath ? 'Sembunyikan' : 'Lihat' }}
                </button>
            </div>
        </div>
    </div>

    {{-- Panel Utama Pembaca Log --}}
    <div class="m3-syslog-card">
        <div class="m3-syslog-controls">
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem;">
                <select wire:model.live="selectedFile" class="m3-syslog-select">
                    @forelse ($files as $f)
                        <option value="{{ $f['name'] }}">
                            {{ $f['is_critical'] ? '[ARSIP KRITIS] ' : '' }}{{ $f['name'] }} ({{ $f['size_kb'] }} KB)
                        </option>
                    @empty
                        <option value="">Belum ada berkas log</option>
                    @endforelse
                </select>

                <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Cari pesan error / kode..." class="m3-syslog-input">

                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                    <button type="button" wire:click="$set('levelFilter', 'all')" class="m3-syslog-pill {{ $levelFilter === 'all' ? 'active' : '' }}">Semua</button>
                    <button type="button" wire:click="$set('levelFilter', 'error')" class="m3-syslog-pill {{ $levelFilter === 'error' ? 'active' : '' }}">Error / Kritis</button>
                    <button type="button" wire:click="$set('levelFilter', 'warning')" class="m3-syslog-pill {{ $levelFilter === 'warning' ? 'active' : '' }}">Warning</button>
                    <button type="button" wire:click="$set('levelFilter', 'info')" class="m3-syslog-pill {{ $levelFilter === 'info' ? 'active' : '' }}">Info</button>
                </div>
            </div>

            @if ($selectedFile)
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <button type="button" wire:click="downloadSelectedLog" class="m3-syslog-pill active">
                        Unduh Log
                    </button>

                    @if (! $confirmClear)
                        <button type="button" wire:click="$set('confirmClear', true)" class="m3-syslog-pill">
                            Kosongkan Berkas
                        </button>
                    @else
                        <span class="m3-log-badge error">Yakin kosongkan?</span>
                        <button type="button" wire:click="clearSelectedLog" class="m3-syslog-pill" style="background: #dc2626; color: #ffffff; border-color: #dc2626;">
                            Ya, Kosongkan
                        </button>
                        <button type="button" wire:click="$set('confirmClear', false)" class="m3-syslog-pill">
                            Batal
                        </button>
                    @endif
                </div>
            @endif
        </div>

        @if (empty($entries))
            <div style="text-align: center; padding: 2.5rem 1rem;" class="m3-syslog-stat-sub">
                Tidak ada entri log yang sesuai dengan filter pada berkas <strong>{{ $selectedFile ?? '-' }}</strong>.
            </div>
        @else
            <div style="font-size: 0.76rem; margin-bottom: 0.75rem;" class="m3-syslog-stat-sub">
                Menampilkan maksimal <strong>{{ count($entries) }}</strong> entri terbaru (pembacaan ekor file maks 256 KB agar hemat memori server).
            </div>

            @foreach ($entries as $entry)
                @php
                    $isErr = in_array($entry['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true);
                    $isWarn = $entry['level'] === 'WARNING';
                    $cls = $isErr ? 'error' : ($isWarn ? 'warning' : 'info');
                @endphp
                <div class="m3-log-entry lvl-{{ $cls }}">
                    <div class="m3-log-meta">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="m3-log-badge {{ $cls }}">{{ $entry['level'] }}</span>
                            <span class="m3-log-time">{{ $entry['timestamp'] }}</span>
                        </div>
                        <span class="m3-log-time">{{ $entry['env'] }}</span>
                    </div>
                    <div class="m3-log-msg">{{ $entry['message'] }}</div>
                    @if ($entry['trace'])
                        <details style="margin-top: 0.4rem;">
                            <summary style="cursor: pointer; font-size: 0.75rem; font-weight: 700;" class="m3-log-time">Lihat Detail Stack Trace</summary>
                            <div class="m3-log-trace">{{ $entry['trace'] }}</div>
                        </details>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</x-filament-panels::page>
