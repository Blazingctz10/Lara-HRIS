<x-filament-panels::page>
    @php
        $stats = $this->getDatabaseStats();
        $backupFiles = $this->getBackupFiles();
    @endphp

    <style>
        .m3-backup-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .m3-stat-card {
            background-color: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #e2e8f0);
            border-radius: 16px !important;
            padding: 1rem 1.25rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        :is(.dark) .m3-stat-card {
            background-color: #141E18;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .m3-stat-icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(14, 104, 56, 0.1);
            color: #0E6838;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        :is(.dark) .m3-stat-icon-wrapper {
            background: rgba(74, 222, 128, 0.12);
            color: #4ADE80;
        }

        .m3-stat-icon-wrapper svg {
            width: 20px !important;
            height: 20px !important;
            flex-shrink: 0;
        }

        .m3-action-panel {
            background-color: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #e2e8f0);
            border-radius: 16px !important;
            padding: 1.35rem 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        :is(.dark) .m3-action-panel {
            background-color: #141E18;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .m3-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px !important;
            font-size: 0.725rem;
            font-weight: 700;
        }

        .m3-badge-pill svg {
            width: 13px !important;
            height: 13px !important;
            flex-shrink: 0;
        }

        .m3-table-wrap {
            overflow-x: auto;
            border-radius: 16px !important;
            border: 1px solid var(--m3-outline-variant, #e2e8f0);
            background: var(--m3-surface, #ffffff);
        }

        :is(.dark) .m3-table-wrap {
            background: #141E18;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .m3-backup-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.825rem;
        }

        .m3-backup-table th {
            background-color: rgba(14, 104, 56, 0.05);
            color: #0E6838;
            padding: 0.75rem 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.4px;
            border-bottom: 1px solid rgba(14, 104, 56, 0.12);
        }

        :is(.dark) .m3-backup-table th {
            background-color: rgba(255, 255, 255, 0.03);
            color: #4ADE80;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-backup-table td {
            padding: 0.75rem 1.1rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            color: var(--m3-on-surface, #0f1e14);
        }

        .m3-backup-table tr:last-child td {
            border-bottom: none;
        }

        :is(.dark) .m3-backup-table td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
            color: #e2e8e4;
        }

        .m3-label {
            display: block;
            font-size: 0.725rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            opacity: 0.8;
        }

        .m3-select-input {
            width: 100%;
            border-radius: 14px !important;
            border: 1px solid var(--m3-outline-variant, #cbd5e1);
            background-color: var(--m3-surface-container, #f8fafc);
            color: var(--m3-on-surface, #0f1e14);
            padding: 0.5rem 0.75rem;
            font-size: 0.825rem;
            font-weight: 600;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .m3-select-input:focus {
            outline: none;
            border-color: #0E6838;
            box-shadow: 0 0 0 2px rgba(14, 104, 56, 0.15);
        }

        :is(.dark) .m3-select-input {
            background-color: #19261E;
            border-color: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
        }

        :is(.dark) .m3-select-input:focus {
            border-color: #4ADE80;
            box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.15);
        }

        .m3-helper {
            display: block;
            font-size: 0.7rem;
            opacity: 0.6;
            margin-top: 0.25rem;
        }

        .m3-save-schedule-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background-color: #0E6838;
            color: #FFFFFF;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.5rem 1.15rem;
            border-radius: 9999px !important;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .m3-save-schedule-btn svg {
            width: 15px !important;
            height: 15px !important;
            flex-shrink: 0;
        }

        .m3-save-schedule-btn:hover {
            background-color: #094726;
        }

        :is(.dark) .m3-save-schedule-btn {
            background-color: #4ADE80;
            color: #0D1410;
        }

        :is(.dark) .m3-save-schedule-btn:hover {
            background-color: #38c96e;
        }
    </style>

    <!-- Overview Stat Cards (Ringkas & Proporsional) -->
    <div class="m3-backup-grid">
        <div class="m3-stat-card">
            <div class="m3-stat-icon-wrapper">
                <x-heroicon-o-circle-stack />
            </div>
            <div>
                <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px; opacity: 0.7;">
                    Tipe & Ukuran DB
                </div>
                <div style="font-size: 1.1rem; font-weight: 800; margin-top: 0.1rem;">
                    {{ $stats['connection'] }} <span style="font-size: 0.825rem; font-weight: 600; opacity: 0.7;">({{ $stats['size'] }})</span>
                </div>
            </div>
        </div>

        <div class="m3-stat-card">
            <div class="m3-stat-icon-wrapper" style="background: rgba(234, 88, 12, 0.12); color: #EA580C;">
                <x-heroicon-o-users />
            </div>
            <div>
                <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px; opacity: 0.7;">
                    Data Karyawan
                </div>
                <div style="font-size: 1.1rem; font-weight: 800; margin-top: 0.1rem;">
                    {{ $stats['counts']['employees'] }} <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.7;">Pegawai</span>
                </div>
            </div>
        </div>

        <div class="m3-stat-card">
            <div class="m3-stat-icon-wrapper" style="background: rgba(202, 138, 4, 0.12); color: #CA8A04;">
                <x-heroicon-o-clock />
            </div>
            <div>
                <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px; opacity: 0.7;">
                    Presensi & Cuti
                </div>
                <div style="font-size: 1.1rem; font-weight: 800; margin-top: 0.1rem;">
                    {{ number_format($stats['counts']['attendances'] + $stats['counts']['leave_requests']) }} <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.7;">Log</span>
                </div>
            </div>
        </div>

        <div class="m3-stat-card">
            <div class="m3-stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <x-heroicon-o-arrow-path />
            </div>
            <div>
                <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.4px; opacity: 0.7;">
                    Terakhir Dicadangkan
                </div>
                <div style="font-size: 0.85rem; font-weight: 800; margin-top: 0.2rem; line-height: 1.25;">
                    {{ $stats['last_backup'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Unified Backup Control & Auto-Scheduler Panel -->
    <div class="m3-action-panel">
        <!-- Part 1: Manual Backup -->
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1.25rem; flex-wrap: wrap; padding-bottom: 1.15rem; border-bottom: 1px solid rgba(148, 163, 184, 0.15);">
            <div style="max-width: 580px;">
                <h3 style="font-size: 1rem; font-weight: 800; margin: 0 0 0.2rem 0;">
                    Pencadangan Manual Core HRIS
                </h3>
                <p style="font-size: 0.8rem; opacity: 0.75; margin: 0; line-height: 1.45;">
                    Buat arsip cadangan instan untuk basis data maupun berkas dokumen kepegawaian (foto, SK, dan kebijakan PDF).
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                {{ $this->createDatabaseBackupAction }}
                {{ $this->createFullBackupAction }}
            </div>
        </div>

        <!-- Part 2: Enterprise Auto-Backup Scheduler -->
        <div style="padding-top: 1.15rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 0.975rem; font-weight: 800; margin: 0;">
                        Jadwal Pencadangan Otomatis (Auto-Backup Scheduler)
                    </h3>
                    <p style="font-size: 0.775rem; opacity: 0.7; margin: 0.1rem 0 0 0;">
                        Pencadangan rutin otomatis di latar belakang sesuai kepatuhan DRP Perbankan
                    </p>
                </div>

                <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                    @if($auto_backup_enabled)
                        <span class="m3-badge-pill" style="background: rgba(74, 222, 128, 0.15); color: #0E6838;">
                            <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #16a34a;"></span>
                            Jadwal Aktif: {{ $schedule_time }} WIB ({{ $this->getFrequencyLabel() }})
                        </span>
                    @else
                        <span class="m3-badge-pill" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                            <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #ef4444;"></span>
                            Jadwal Dinonaktifkan
                        </span>
                    @endif
                    {{ $this->runScheduledBackupNowAction }}
                </div>
            </div>

            <form wire:submit.prevent="saveScheduleSettings">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <div>
                        <label class="m3-label" for="auto_backup_enabled">Status Otomatis</label>
                        <select wire:model.defer="auto_backup_enabled" id="auto_backup_enabled" class="m3-select-input">
                            <option value="1">Aktifkan Cadangan Otomatis</option>
                            <option value="0">Nonaktifkan</option>
                        </select>
                        <span class="m3-helper">Eksekusi rutin sesuai jadwal</span>
                    </div>

                    <div>
                        <label class="m3-label" for="schedule_time">Waktu Eksekusi (WIB)</label>
                        <input type="time" wire:model.defer="schedule_time" id="schedule_time" class="m3-select-input" required />
                        <span class="m3-helper">Rekomendasi: 02:00 dini hari</span>
                    </div>

                    <div>
                        <label class="m3-label" for="frequency">Frekuensi</label>
                        <select wire:model.defer="frequency" id="frequency" class="m3-select-input">
                            <option value="daily">Setiap Hari (Rekomendasi)</option>
                            <option value="weekdays">Senin - Sabtu (Hari Kerja)</option>
                            <option value="weekly">Mingguan (Hari Minggu)</option>
                        </select>
                        <span class="m3-helper">Pola perulangan cadangan</span>
                    </div>

                    <div>
                        <label class="m3-label" for="backup_type">Format Arsip</label>
                        <select wire:model.defer="backup_type" id="backup_type" class="m3-select-input">
                            <option value="full">Arsip Lengkap ZIP (DB + Berkas)</option>
                            <option value="db">Basis Data Saja (.sqlite)</option>
                        </select>
                        <span class="m3-helper">ZIP mencakup seluruh foto & SK</span>
                    </div>

                    <div>
                        <label class="m3-label" for="retention_count">Batas Retensi</label>
                        <select wire:model.defer="retention_count" id="retention_count" class="m3-select-input">
                            <option value="10">Simpan 10 Arsip Terbaru</option>
                            <option value="20">Simpan 20 Arsip Terbaru</option>
                            <option value="30">Simpan 30 Arsip Terbaru</option>
                            <option value="50">Simpan 50 Arsip Terbaru</option>
                        </select>
                        <span class="m3-helper">Mencegah disk server penuh</span>
                    </div>
                </div>

                <div style="margin-top: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; padding-top: 0.9rem; border-top: 1px dashed rgba(148, 163, 184, 0.2);">
                    <div style="font-size: 0.775rem; opacity: 0.8;">
                        @php $schedData = $this->getScheduleSettings(); @endphp
                        @if(!empty($schedData['last_run_at']))
                            <strong>Eksekusi Terakhir:</strong> {{ $schedData['last_run_at'] }}
                            &bull; Status: <span style="font-weight: 700; color: {{ ($schedData['last_run_status'] ?? '') === 'Sukses' ? '#16a34a' : '#ea580c' }};">{{ $schedData['last_run_status'] ?? '-' }}</span>
                            @if(!empty($schedData['last_run_file']))
                                &bull; <code>{{ $schedData['last_run_file'] }}</code>
                            @endif
                        @else
                            <em>Jadwal siap berjalan sesuai waktu yang dikonfigurasikan.</em>
                        @endif
                    </div>

                    <button type="submit" class="m3-save-schedule-btn">
                        <x-heroicon-m-check />
                        Simpan Pengaturan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table of Available Archives -->
    <div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <h4 style="font-size: 0.95rem; font-weight: 800; margin: 0;">
                Daftar Arsip Cadangan Tersimpan ({{ count($backupFiles) }})
            </h4>
            <div x-data="{ showLocation: false }" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                <span style="opacity: 0.7;">Lokasi Berkas:</span>
                <template x-if="!showLocation">
                    <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                        <code style="font-family: monospace; padding: 0.1rem 0.4rem; background: rgba(14, 104, 56, 0.08); border-radius: 6px; letter-spacing: 2px;">••••••••••••••••••</code>
                        <button type="button" @click="showLocation = true" title="Tampilkan lokasi direktori server" style="background: none; border: none; cursor: pointer; color: #0E6838; font-size: 0.725rem; text-decoration: underline; font-weight: 700; padding: 0;">
                            Buka
                        </button>
                    </span>
                </template>
                <template x-if="showLocation">
                    <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                        <code style="font-family: monospace; padding: 0.1rem 0.4rem; background: rgba(14, 104, 56, 0.08); border-radius: 6px; font-weight: 700;">storage/app/backups/</code>
                        <button type="button" @click="showLocation = false" title="Sembunyikan lokasi server" style="background: none; border: none; cursor: pointer; opacity: 0.65; font-size: 0.725rem; text-decoration: underline; padding: 0;">
                            Tutup
                        </button>
                    </span>
                </template>
            </div>
        </div>

        <div class="m3-table-wrap">
            <table class="m3-backup-table">
                <thead>
                    <tr>
                        <th>Nama Berkas Cadangan</th>
                        <th>Kategori Arsip</th>
                        <th>Ukuran</th>
                        <th>Waktu Pembuatan</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backupFiles as $backup)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: #0E6838;">
                                {{ $backup['name'] }}
                            </td>
                            <td>
                                @if(str_contains($backup['name'], 'full') || str_ends_with($backup['name'], '.zip'))
                                    <span class="m3-badge-pill" style="background: rgba(14, 104, 56, 0.12); color: #0E6838;">
                                        <x-heroicon-m-archive-box />
                                        Arsip Lengkap (ZIP)
                                    </span>
                                @else
                                    <span class="m3-badge-pill" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                                        <x-heroicon-m-circle-stack />
                                        Database Saja
                                    </span>
                                @endif
                            </td>
                            <td style="font-weight: 600;">
                                {{ $backup['size'] }}
                            </td>
                            <td style="opacity: 0.8;">
                                {{ $backup['created_at'] }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <x-filament::button
                                        tag="a"
                                        href="{{ route('admin.backup.download', ['filename' => $backup['name']]) }}"
                                        size="xs"
                                        color="primary"
                                        icon="heroicon-m-arrow-down-tray"
                                        title="Unduh berkas cadangan ke komputer"
                                    >
                                        Unduh
                                    </x-filament::button>

                                    {{ ($this->deleteBackupAction)(['filename' => $backup['name']]) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2.25rem 1.5rem; opacity: 0.7;">
                                <div style="font-weight: 700; font-size: 0.9rem; margin-bottom: 0.2rem;">Belum Ada File Cadangan Tersimpan</div>
                                <div style="font-size: 0.8rem;">Klik salah satu tombol di atas untuk membuat cadangan database atau arsip lengkap pertama Anda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
