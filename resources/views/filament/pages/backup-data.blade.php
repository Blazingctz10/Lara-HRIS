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

    <!-- Table of Available Archives -->
    <div style="margin-top: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <h4 style="font-size: 1.05rem; font-weight: 800; letter-spacing: -0.01em;">
                Daftar Arsip Cadangan Tersimpan ({{ count($backupFiles) }})
            </h4>
            <span style="font-size: 0.8rem; opacity: 0.75;">
                Lokasi: <code style="font-family: monospace; padding: 0.15rem 0.4rem; background: var(--m3-surface-container, #d6e3d9); border-radius: 6px;">storage/app/backups/</code>
            </span>
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
                                    <!-- Unduh Button -->
                                    <x-filament::button
                                        size="xs"
                                        color="primary"
                                        icon="heroicon-m-arrow-down-tray"
                                        wire:click="downloadBackup('{{ $backup['name'] }}')"
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
