<x-filament-panels::page>
    @php
        $stats = $this->getDatabaseStats();
        $backupFiles = $this->getBackupFiles();
    @endphp

    <style>
        .m3-backup-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }

        .m3-stat-card {
            background-color: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            border-radius: 20px;
            padding: 1.35rem 1.5rem;
            box-shadow: 0 2px 8px -2px rgba(14, 104, 56, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s ease;
        }

        .m3-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(14, 104, 56, 0.12);
        }

        .m3-stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--m3-primary-container, #cce8d7);
            color: var(--m3-on-primary-container, #03361b);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        :is(.dark) .m3-stat-card {
            background-color: var(--m3-surface, #111a14);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
        }

        :is(.dark) .m3-stat-icon-wrapper {
            background: #0b4f27;
            color: #4ade80;
        }

        .m3-action-panel {
            background-color: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            border-radius: 24px;
            padding: 1.75rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px -2px rgba(14, 104, 56, 0.08);
        }

        :is(.dark) .m3-action-panel {
            background-color: var(--m3-surface, #111a14);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .m3-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .m3-table-wrap {
            overflow-x: auto;
            border-radius: 20px;
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            background: var(--m3-surface, #ffffff);
        }

        :is(.dark) .m3-table-wrap {
            background: var(--m3-surface, #111a14);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .m3-backup-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        .m3-backup-table th {
            background-color: var(--m3-surface-container, #d6e3d9);
            color: var(--m3-on-surface, #0f1e14);
            padding: 0.95rem 1.25rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--m3-outline-variant, #b6cac0);
        }

        :is(.dark) .m3-backup-table th {
            background-color: var(--m3-surface-container-low, #141e18);
            color: var(--m3-on-surface-variant, #9eaba3);
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-backup-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--m3-outline-variant, #b6cac0);
            color: var(--m3-on-surface, #0f1e14);
        }

        :is(.dark) .m3-backup-table td {
            border-bottom-color: rgba(255, 255, 255, 0.06);
            color: var(--m3-on-surface, #e2e8e4);
        }

        .m3-backup-table tr:hover td {
            background-color: var(--m3-surface-container-low, #dfeae2);
        }

        :is(.dark) .m3-backup-table tr:hover td {
            background-color: var(--m3-surface-container-high, #203026);
        }

        .m3-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.85;
        }

        .m3-select-input {
            width: 100%;
            border-radius: 14px;
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            background-color: var(--m3-surface-container, #f0f5f2);
            color: var(--m3-on-surface, #0f1e14);
            padding: 0.65rem 0.85rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .m3-select-input:focus {
            outline: none;
            border-color: #0E6838;
            box-shadow: 0 0 0 3px rgba(14, 104, 56, 0.2);
        }

        :is(.dark) .m3-select-input {
            background-color: #19261E;
            border-color: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
        }

        :is(.dark) .m3-select-input:focus {
            border-color: #4ADE80;
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
        }

        .m3-helper {
            display: block;
            font-size: 0.725rem;
            opacity: 0.65;
            margin-top: 0.35rem;
        }

        .m3-save-schedule-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #0E6838;
            color: #FFFFFF;
            font-size: 0.875rem;
            font-weight: 700;
            padding: 0.65rem 1.4rem;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(14, 104, 56, 0.25);
            transition: all 0.15s ease;
        }

        .m3-save-schedule-btn:hover {
            background-color: #0a4f2b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 104, 56, 0.35);
        }

        :is(.dark) .m3-save-schedule-btn {
            background-color: #4ADE80;
            color: #0D1410;
        }

        :is(.dark) .m3-save-schedule-btn:hover {
            background-color: #38c96e;
        }
    </style>

    <!-- Overview Stat Cards -->
    <div class="m3-backup-grid">
        <div class="m3-stat-card">
            <div class="m3-stat-icon-wrapper">
                <x-heroicon-o-circle-stack class="w-6 h-6" />
            </div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; opacity: 0.8;">
                    Tipe & Ukuran DB
                </div>
                <div style="font-size: 1.35rem; font-weight: 800; margin-top: 0.15rem;">
                    {{ $stats['connection'] }} <span style="font-size: 0.95rem; font-weight: 600; opacity: 0.75;">({{ $stats['size'] }})</span>
                </div>
            </div>
        </div>

        <div class="m3-stat-card">
            <div class="m3-stat-icon-wrapper" style="background: rgba(234, 88, 12, 0.15); color: #ea580c;">
                <x-heroicon-o-users class="w-6 h-6" />
            </div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; opacity: 0.8;">
                    Data Karyawan & Akun
                </div>
                <div style="font-size: 1.35rem; font-weight: 800; margin-top: 0.15rem;">
                    {{ $stats['counts']['employees'] }} <span style="font-size: 0.85rem; font-weight: 600; opacity: 0.75;">Pegawai</span>
                </div>
            </div>
        </div>

        <div class="m3-stat-card">
            <div class="m3-stat-icon-wrapper" style="background: rgba(202, 138, 4, 0.15); color: #ca8a04;">
                <x-heroicon-o-clock class="w-6 h-6" />
            </div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; opacity: 0.8;">
                    Presensi & Pengajuan
                </div>
                <div style="font-size: 1.35rem; font-weight: 800; margin-top: 0.15rem;">
                    {{ number_format($stats['counts']['attendances'] + $stats['counts']['leave_requests']) }} <span style="font-size: 0.85rem; font-weight: 600; opacity: 0.75;">Log</span>
                </div>
            </div>
        </div>

        <div class="m3-stat-card">
            <div class="m3-stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                <x-heroicon-o-arrow-path class="w-6 h-6" />
            </div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; opacity: 0.8;">
                    Terakhir Dicadangkan
                </div>
                <div style="font-size: 0.925rem; font-weight: 800; margin-top: 0.35rem; line-height: 1.3;">
                    {{ $stats['last_backup'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Action Section: Generate Backups with Confirmation Popups -->
    <div class="m3-action-panel">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
            <div style="max-width: 600px;">
                <h3 style="font-size: 1.2rem; font-weight: 800; letter-spacing: -0.01em; margin-bottom: 0.4rem;">
                    Pencadangan Data Resmi Core HRIS
                </h3>
                <p style="font-size: 0.875rem; opacity: 0.85; line-height: 1.55;">
                    Sesuai kepatuhan tata kelola teknologi informasi perbankan, administrator wajib melakukan pencadangan berkala terhadap seluruh data transaksi presensi, riwayat cuti, struktur organisasi, dan profil kepegawaian karyawan.
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                {{ $this->createDatabaseBackupAction }}
                {{ $this->createFullBackupAction }}
            </div>
        </div>
    </div>

    <!-- Enterprise Auto-Backup Scheduler Section -->
    <div class="m3-action-panel">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <div class="m3-stat-icon-wrapper" style="background: rgba(14, 104, 56, 0.15); color: #0E6838;">
                    <x-heroicon-o-clock class="w-6 h-6" />
                </div>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; letter-spacing: -0.01em;">
                        Jadwal Pencadangan Otomatis (Auto-Backup Scheduler)
                    </h3>
                    <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 0.15rem;">
                        Pencadangan rutin otomatis di latar belakang tanpa intervensi manual (Kepatuhan POJK & DRP Bank)
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                @if($auto_backup_enabled)
                    <span class="m3-badge-pill" style="background: rgba(74, 222, 128, 0.18); color: #0E6838;">
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #16a34a;"></span>
                        Jadwal Aktif: {{ $schedule_time }} WIB ({{ $this->getFrequencyLabel() }})
                    </span>
                @else
                    <span class="m3-badge-pill" style="background: rgba(239, 68, 68, 0.15); color: #dc2626;">
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
                        Jadwal Dinonaktifkan
                    </span>
                @endif
                {{ $this->runScheduledBackupNowAction }}
            </div>
        </div>

        <form wire:submit.prevent="saveScheduleSettings">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-top: 1rem;">
                <!-- 1. Status Toggle -->
                <div>
                    <label class="m3-label" for="auto_backup_enabled">Status Otomatis</label>
                    <select wire:model.defer="auto_backup_enabled" id="auto_backup_enabled" class="m3-select-input">
                        <option value="1">Aktifkan Cadangan Otomatis</option>
                        <option value="0">Nonaktifkan</option>
                    </select>
                    <span class="m3-helper">Eksekusi rutin sesuai jadwal</span>
                </div>

                <!-- 2. Jam Eksekusi -->
                <div>
                    <label class="m3-label" for="schedule_time">Waktu Eksekusi (WIB)</label>
                    <input type="time" wire:model.defer="schedule_time" id="schedule_time" class="m3-select-input" required />
                    <span class="m3-helper">Rekomendasi: 02:00 dini hari</span>
                </div>

                <!-- 3. Frekuensi -->
                <div>
                    <label class="m3-label" for="frequency">Frekuensi Pelaksanaan</label>
                    <select wire:model.defer="frequency" id="frequency" class="m3-select-input">
                        <option value="daily">Setiap Hari (Rekomendasi)</option>
                        <option value="weekdays">Senin - Sabtu (Hari Kerja BPR)</option>
                        <option value="weekly">Mingguan (Hari Minggu)</option>
                    </select>
                    <span class="m3-helper">Pola perulangan cadangan</span>
                </div>

                <!-- 4. Format Cadangan -->
                <div>
                    <label class="m3-label" for="backup_type">Format Arsip</label>
                    <select wire:model.defer="backup_type" id="backup_type" class="m3-select-input">
                        <option value="full">Arsip Lengkap ZIP (DB + Berkas + JSON)</option>
                        <option value="db">Basis Data Saja (.sqlite)</option>
                    </select>
                    <span class="m3-helper">ZIP mencakup seluruh foto & SK</span>
                </div>

                <!-- 5. Retensi Berkas -->
                <div>
                    <label class="m3-label" for="retention_count">Retensi / Batas Arsip</label>
                    <select wire:model.defer="retention_count" id="retention_count" class="m3-select-input">
                        <option value="10">Simpan 10 Arsip Terbaru</option>
                        <option value="20">Simpan 20 Arsip Terbaru</option>
                        <option value="30">Simpan 30 Arsip Terbaru</option>
                        <option value="50">Simpan 50 Arsip Terbaru</option>
                    </select>
                    <span class="m3-helper">Mencegah hard disk server penuh</span>
                </div>
            </div>

            <div style="margin-top: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding-top: 1.25rem; border-top: 1px dashed var(--m3-outline-variant, #b6cac0);">
                <div style="font-size: 0.825rem; opacity: 0.85;">
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
                    <x-heroicon-m-check class="w-4 h-4" />
                    Simpan Pengaturan Jadwal
                </button>
            </div>
        </form>
    </div>

    <!-- Table of Available Archives -->
    <div style="margin-top: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <h4 style="font-size: 1.05rem; font-weight: 800; letter-spacing: -0.01em;">
                Daftar Arsip Cadangan Tersimpan ({{ count($backupFiles) }})
            </h4>
            <div x-data="{ showLocation: false }" style="font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                <span style="opacity: 0.75;">Lokasi Berkas:</span>
                <template x-if="!showLocation">
                    <span style="display: inline-flex; align-items: center; gap: 0.45rem;">
                        <code style="font-family: monospace; padding: 0.15rem 0.45rem; background: var(--m3-surface-container, #d6e3d9); border-radius: 6px; letter-spacing: 2px;">••••••••••••••••••</code>
                        <button type="button" @click="showLocation = true" title="Tampilkan lokasi direktori server" style="background: none; border: none; cursor: pointer; color: var(--m3-primary, #0E6838); font-size: 0.75rem; text-decoration: underline; font-weight: 700; padding: 0;">
                            Buka
                        </button>
                    </span>
                </template>
                <template x-if="showLocation">
                    <span style="display: inline-flex; align-items: center; gap: 0.45rem;">
                        <code style="font-family: monospace; padding: 0.15rem 0.45rem; background: var(--m3-surface-container, #d6e3d9); border-radius: 6px; font-weight: 700;">storage/app/backups/</code>
                        <button type="button" @click="showLocation = false" title="Sembunyikan lokasi server" style="background: none; border: none; cursor: pointer; opacity: 0.65; font-size: 0.75rem; text-decoration: underline; padding: 0;">
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
                        <th>Ukuran Berkas</th>
                        <th>Waktu Pembuatan</th>
                        <th style="text-align: right;">Aksi Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backupFiles as $backup)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: var(--m3-primary, #0E6838);">
                                {{ $backup['name'] }}
                            </td>
                            <td>
                                @if(str_contains($backup['name'], 'full') || str_ends_with($backup['name'], '.zip'))
                                    <span class="m3-badge-pill" style="background: rgba(14, 104, 56, 0.15); color: #0E6838;">
                                        <x-heroicon-m-archive-box class="w-3.5 h-3.5" />
                                        Arsip Lengkap (ZIP)
                                    </span>
                                @else
                                    <span class="m3-badge-pill" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                                        <x-heroicon-m-circle-stack class="w-3.5 h-3.5" />
                                        Database Saja
                                    </span>
                                @endif
                            </td>
                            <td style="font-weight: 600;">
                                {{ $backup['size'] }}
                            </td>
                            <td style="opacity: 0.85;">
                                {{ $backup['created_at'] }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <!-- Unduh Button via Native Route -->
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

                                    <!-- Hapus Button dengan Modal Filament Resmi -->
                                    {{ ($this->deleteBackupAction)(['filename' => $backup['name']]) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem 1.5rem; opacity: 0.75;">
                                <x-heroicon-o-archive-box-x-mark style="width: 48px; height: 48px; margin: 0 auto 0.75rem auto; opacity: 0.5;" />
                                <div style="font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem;">Belum Ada File Cadangan Tersimpan</div>
                                <div style="font-size: 0.85rem;">Klik salah satu tombol di atas untuk membuat cadangan database atau arsip lengkap pertama Anda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Security & Compliance Guidelines -->
    <div style="margin-top: 2rem; border-radius: 20px; border: 1px solid var(--m3-outline-variant, #b6cac0); background: var(--m3-surface-container-low, #dfeae2); padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
        <x-heroicon-m-shield-check style="width: 26px; height: 26px; color: #0E6838; flex-shrink: 0; margin-top: 0.2rem;" />
        <div style="font-size: 0.825rem; line-height: 1.55;">
            <strong>Kepatuhan Standar Keamanan Data Perbankan (PT BPR Banda Raya):</strong>
            Cadangan data memuat informasi rahasia kepegawaian, nomor KTP/NPWP, serta data legalitas karyawan. File arsip cadangan yang diunduh wajib disimpan dalam media penyimpanan terenkripsi yang memenuhi regulasi Otoritas Jasa Keuangan (OJK) serta Bank Indonesia.
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
