<x-filament-panels::page>
    <style>
        .m3-import-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding-bottom: 1rem;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid var(--m3-outline-variant, #e2e8f0);
        }

        :is(.dark) .m3-import-header {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-step-pills {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .m3-step-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px !important;
            background: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #e2e8f0);
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--m3-on-surface, #0f1e14);
        }

        :is(.dark) .m3-step-badge {
            background: #141E18;
            border-color: rgba(255, 255, 255, 0.1);
            color: #f1f5f9;
        }

        .m3-step-num {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #0E6838;
            color: #ffffff;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.68rem;
            flex-shrink: 0;
        }

        .m3-import-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .m3-import-card {
            background-color: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #e2e8f0);
            border-radius: 16px !important;
            padding: 1.35rem 1.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        :is(.dark) .m3-import-card {
            background-color: #141E18;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .m3-card-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--m3-on-surface, #0f1e14);
            margin: 0;
        }

        :is(.dark) .m3-card-title {
            color: #FFFFFF !important;
        }

        .m3-card-sub {
            font-size: 0.775rem;
            color: #64748b;
            margin: 0.1rem 0 0 0;
        }

        :is(.dark) .m3-card-sub {
            color: #94a3b8 !important;
        }

        .m3-bullet-list {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            padding: 0;
            margin: 1rem 0;
            list-style: none;
            font-size: 0.8rem;
            opacity: 0.85;
        }

        .m3-bullet-item {
            display: flex;
            align-items: flex-start;
            gap: 0.45rem;
        }

        .m3-bullet-item svg {
            width: 15px !important;
            height: 15px !important;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .m3-dl-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 1.1rem;
            border-radius: 9999px !important;
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .m3-dl-btn svg {
            width: 15px !important;
            height: 15px !important;
            flex-shrink: 0;
        }

        .m3-file-input {
            display: block;
            width: 100%;
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--m3-outline-variant, #cbd5e1);
            border-radius: 14px !important;
            background: var(--m3-surface-container, #f8fafc);
            color: var(--m3-on-surface, #0f1e14);
        }

        :is(.dark) .m3-file-input {
            background: #19261E;
            border-color: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
        }

        .m3-field-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .m3-field-table th {
            text-align: left;
            padding: 0.6rem 0.85rem;
            background: rgba(14, 104, 56, 0.05);
            color: #0E6838;
            border-bottom: 1px solid rgba(14, 104, 56, 0.12);
            font-weight: 700;
            font-size: 0.725rem;
            text-transform: uppercase;
        }

        .m3-field-table td {
            padding: 0.55rem 0.85rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        :is(.dark) .m3-field-table th {
            background: rgba(255, 255, 255, 0.03);
            color: #4ADE80;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        :is(.dark) .m3-field-table td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }
    </style>

    {{-- Sleek Header Strip --}}
    <div class="m3-import-header">
        <div>
            <h3 class="m3-card-title" style="font-size: 1.05rem;">
                Panduan Cepat Migrasi Data Lama ke Lara-HRIS
            </h3>
            <p class="m3-card-sub">
                Pindahkan arsip pegawai dari Microsoft Excel atau log mesin absensi secara massal.
            </p>
        </div>

        <div class="m3-step-pills">
            <div class="m3-step-badge">
                <span class="m3-step-num">1</span>
                <span>Unduh Template</span>
            </div>
            <div class="m3-step-badge">
                <span class="m3-step-num">2</span>
                <span>Isi Data di Excel</span>
            </div>
            <div class="m3-step-badge">
                <span class="m3-step-num">3</span>
                <span>Unggah & Impor</span>
            </div>
        </div>
    </div>

    {{-- Grid Kartu Migrasi (Tanpa Nested Box Berlebihan) --}}
    <div class="m3-import-grid">
        {{-- KARTU 1: MIGRASI MASTER PEGAWAI --}}
        <div class="m3-import-card">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding-bottom: 0.85rem; border-bottom: 1px solid rgba(148, 163, 184, 0.15);">
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(14, 104, 56, 0.1); color: #0E6838; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 19px; height: 19px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="m3-card-title">Migrasi Data Master Pegawai</h4>
                            <p class="m3-card-sub">Profil, Jabatan, Cabang, Akun Login & Kuota Cuti</p>
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.download-template.employee-xls') }}"
                        download="template_migrasi_pegawai_bpr.xls"
                        class="m3-dl-btn"
                        style="background: rgba(14, 104, 56, 0.1); color: #0E6838;"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Template (.xls)</span>
                        <span class="sr-only">Unduh Template CSV Pegawai</span>
                    </a>
                </div>

                <ul class="m3-bullet-list">
                    <li class="m3-bullet-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#0E6838">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Cabang, Divisi, atau Jabatan baru akan dibuat otomatis oleh sistem.</span>
                    </li>
                    <li class="m3-bullet-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#0E6838">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Akun login portal & saldo cuti tahunan awal (12 hari) langsung aktif.</span>
                    </li>
                </ul>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.775rem; font-weight: 700; margin-bottom: 0.4rem;">
                        Pilih Berkas (.xlsx, .xls, .csv, .pdf) Data Pegawai:
                    </label>
                    <input
                        type="file"
                        wire:model="employeeFile"
                        accept=".xls,.xlsx,.csv,.txt,.pdf"
                        class="m3-file-input"
                    />
                    @error('employeeFile')
                        <p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.3rem;">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="employeeFile" style="font-size: 0.75rem; color: #0E6838; margin-top: 0.4rem;">
                        Mengunggah berkas ke sistem...
                    </div>
                </div>
            </div>

            <div>
                {{ $this->importEmployeesAction }}
            </div>
        </div>

        {{-- KARTU 2: MIGRASI LOG ABSENSI / FINGERPRINT --}}
        <div class="m3-import-card">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding-bottom: 0.85rem; border-bottom: 1px solid rgba(148, 163, 184, 0.15);">
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(234, 88, 12, 0.12); color: #EA580C; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 19px; height: 19px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="m3-card-title">Migrasi Log Absensi / Fingerprint</h4>
                            <p class="m3-card-sub">Riwayat Kehadiran Mesin Presensi Lama</p>
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.download-template.attendance-xls') }}"
                        download="template_impor_absensi_bpr.xls"
                        class="m3-dl-btn"
                        style="background: rgba(234, 88, 12, 0.12); color: #EA580C;"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Template (.xls)</span>
                    </a>
                </div>

                <ul class="m3-bullet-list">
                    <li class="m3-bullet-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#EA580C">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Mendukung ekspor log mesin Solution, ZKTeco, maupun rekap bulanan Excel.</span>
                    </li>
                    <li class="m3-bullet-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#EA580C">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Pencocokan otomatis berdasarkan NIP Pegawai & Tanggal kehadiran.</span>
                    </li>
                </ul>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.775rem; font-weight: 700; margin-bottom: 0.4rem;">
                        Pilih Berkas (.xlsx, .xls, .csv) Log Presensi:
                    </label>
                    <input
                        type="file"
                        wire:model="attendanceFile"
                        accept=".xls,.xlsx,.csv,.txt"
                        class="m3-file-input"
                    />
                    @error('attendanceFile')
                        <p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.3rem;">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="attendanceFile" style="font-size: 0.75rem; color: #EA580C; margin-top: 0.4rem;">
                        Mengunggah berkas ke sistem...
                    </div>
                </div>
            </div>

            <div>
                {{ $this->importAttendancesAction }}
            </div>
        </div>
    </div>

    {{-- Kamus Data Kolom CSV (Collapsible agar halaman tetap ringkas & rapi) --}}
    <details class="m3-import-card" style="cursor: pointer;">
        <summary style="display: flex; align-items: center; justify-content: space-between; font-weight: 800; font-size: 0.925rem; list-style: none;">
            <span>Kamus Format Kolom CSV Master Pegawai (Klik untuk melihat panduan kolom)</span>
            <span style="font-size: 0.75rem; color: #0E6838; font-weight: 700;">Buka / Tutup</span>
        </summary>

        <div style="margin-top: 1rem; overflow-x: auto; cursor: default;">
            <table class="m3-field-table">
                <thead>
                    <tr>
                        <th>Nama Kolom Header</th>
                        <th>Status</th>
                        <th>Contoh Nilai</th>
                        <th>Keterangan Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>NIP</strong></td>
                        <td><span style="color: #ef4444; font-weight: 700;">Wajib</span></td>
                        <td><code>BPR-2026-0101</code></td>
                        <td>Nomor Induk Pegawai unik sebagai acuan sinkronisasi</td>
                    </tr>
                    <tr>
                        <td><strong>Nama_Lengkap</strong></td>
                        <td><span style="color: #ef4444; font-weight: 700;">Wajib</span></td>
                        <td><code>Hendri Prasetyo</code></td>
                        <td>Nama lengkap pegawai beserta gelar</td>
                    </tr>
                    <tr>
                        <td><strong>Email_Kantor</strong></td>
                        <td>Opsional</td>
                        <td><code>hendri@bprbandaraya.com</code></td>
                        <td>Jika kosong, dibuatkan otomatis <code>pegawai.[nip]@bprbandaraya.com</code></td>
                    </tr>
                    <tr>
                        <td><strong>Kantor_Cabang</strong></td>
                        <td>Opsional</td>
                        <td><code>Kantor Cabang Botania</code></td>
                        <td>Jika nama cabang baru, sistem otomatis membuat cabang baru</td>
                    </tr>
                    <tr>
                        <td><strong>Divisi & Jabatan</strong></td>
                        <td>Opsional</td>
                        <td><code>Operasional & Pelayanan / CS</code></td>
                        <td>Otomatis dipetakan ke struktur organisasi BPR</td>
                    </tr>
                    <tr>
                        <td><strong>Status_Kerja & Tanggal_Masuk</strong></td>
                        <td>Opsional</td>
                        <td><code>Permanent, 2024-01-15</code></td>
                        <td>Pilihan: Permanent, Contract, Probation, Internship</td>
                    </tr>
                    <tr>
                        <td><strong>NIK, NPWP, BPJS & Rekening</strong></td>
                        <td>Opsional</td>
                        <td><code>217101..., 01.10.05307</code></td>
                        <td>Identitas kependudukan, BPJS, dan rekening payroll BPR</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </details>

    <x-filament-actions::modals />
</x-filament-panels::page>
