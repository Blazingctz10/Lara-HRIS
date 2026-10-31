<x-filament-panels::page>
    <style>
        .m3-import-banner {
            background: linear-gradient(135deg, rgba(14, 104, 56, 0.08) 0%, rgba(243, 122, 32, 0.08) 100%);
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            border-radius: 20px;
            padding: 1.5rem 1.75rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        :is(.dark) .m3-import-banner {
            background: linear-gradient(135deg, rgba(14, 104, 56, 0.2) 0%, rgba(17, 26, 20, 0.7) 100%);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .m3-step-badge {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 1.1rem;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid rgba(14, 104, 56, 0.18);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            color: #0f172a;
        }

        :is(.dark) .m3-step-badge {
            background: #111a14;
            border-color: rgba(255, 255, 255, 0.12);
            color: #f1f5f9;
        }

        .m3-step-num {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: #0E6838;
            color: #ffffff;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .m3-import-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 1.75rem;
            margin-bottom: 2rem;
        }

        .m3-import-card {
            background-color: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            border-radius: 24px;
            padding: 1.75rem 2rem;
            box-shadow: 0 4px 16px -4px rgba(14, 104, 56, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        :is(.dark) .m3-import-card {
            background-color: var(--m3-surface, #111a14);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .m3-import-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -4px rgba(14, 104, 56, 0.14);
        }

        .m3-field-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .m3-field-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            background: rgba(14, 104, 56, 0.06);
            border-bottom: 1px solid var(--m3-outline-variant, #b6cac0);
            font-weight: 700;
        }

        .m3-field-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        :is(.dark) .m3-field-table th {
            background: rgba(255, 255, 255, 0.05);
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        :is(.dark) .m3-field-table td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }
    </style>

    {{-- Banner Panduan Langkah Migrasi --}}
    <div class="m3-import-banner">
        <div style="flex: 1; min-width: 280px;">
            <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.45rem;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(14, 104, 56, 0.15); color: #0E6838; display: flex; align-items: center; justify-content: center;" class="dark:text-emerald-400 dark:bg-emerald-950/60">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0E6838;" class="dark:text-emerald-400">
                    Panduan Cepat Migrasi Data Lama ke Lara-HRIS
                </h3>
            </div>
            <p style="font-size: 0.9rem; color: #475569;" class="dark:text-gray-300">
                Pindahkan arsip data lama dari Microsoft Excel, spreadsheet, atau mesin absensi ke sistem secara massal tanpa input manual satu per satu.
            </p>
        </div>

        {{-- Langkah 1, 2, 3 dengan kontras tajam & jelas di light/dark mode --}}
        <div style="display: flex; gap: 0.85rem; flex-wrap: wrap;">
            <div class="m3-step-badge">
                <span class="m3-step-num">1</span>
                <span style="font-size: 0.85rem; font-weight: 700;">Unduh Template CSV</span>
            </div>
            <div class="m3-step-badge">
                <span class="m3-step-num">2</span>
                <span style="font-size: 0.85rem; font-weight: 700;">Salin Data Lama di Excel</span>
            </div>
            <div class="m3-step-badge">
                <span class="m3-step-num">3</span>
                <span style="font-size: 0.85rem; font-weight: 700;">Unggah & Impor Otomatis</span>
            </div>
        </div>
    </div>

    {{-- Grid Kartu Migrasi --}}
    <div class="m3-import-grid">
        {{-- KARTU 1: MIGRASI MASTER PEGAWAI --}}
        <div class="m3-import-card">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(14, 104, 56, 0.12); color: #0E6838; display: flex; align-items: center; justify-content: center;" class="dark:bg-emerald-950/60 dark:text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 style="font-size: 1.15rem; font-weight: 800;">Migrasi Data Master Pegawai</h4>
                            <p style="font-size: 0.8rem; color: #64748b;" class="dark:text-gray-400">Profil, Jabatan, Cabang, Akun Login & Kuota Cuti</p>
                        </div>
                    </div>
                </div>

                <div style="background: rgba(14, 104, 56, 0.08); padding: 1.25rem; border-radius: 16px; margin-bottom: 1.5rem; font-size: 0.875rem; border: 1px solid rgba(14, 104, 56, 0.2);" class="dark:bg-emerald-950/40 dark:border-emerald-500/40">
                    <p style="margin-bottom: 0.65rem; font-weight: 800; font-size: 0.9rem;" class="text-emerald-800 dark:text-emerald-300">Fitur Otomatisasi Cerdas:</p>
                    <ul style="display: flex; flex-direction: column; gap: 0.5rem; padding-left: 0; list-style: none;" class="text-slate-700 dark:text-slate-200">
                        <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px;" class="text-emerald-600 dark:text-emerald-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>Jika Cabang, Divisi, atau Jabatan belum ada, sistem akan <strong>membuatnya otomatis</strong>.</span>
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px;" class="text-emerald-600 dark:text-emerald-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>Akun login portal langsung dibuat dengan kata sandi default <code>password</code>.</span>
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px;" class="text-emerald-600 dark:text-emerald-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>Jatah saldo cuti tahunan awal (12 hari) otomatis diaktifkan untuk setiap pegawai.</span>
                        </li>
                    </ul>
                </div>

                {{-- Unduh Native (Bukan wire:click agar tidak memicu 419 expired) --}}
                <div style="margin-bottom: 1.25rem; display: flex; gap: 0.65rem; flex-wrap: wrap;">
                    <a
                        href="{{ route('admin.download-template.employee-bpr-cv') }}"
                        download="template_cv_karyawan_bpr_bandaraya.csv"
                        style="display: inline-flex; align-items: center; gap: 0.55rem; padding: 0.65rem 1.15rem; border-radius: 12px; background: #0E6838; color: #ffffff; font-size: 0.85rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 2px 6px rgba(14, 104, 56, 0.25);"
                        onmouseover="this.style.background='#0a4d29'"
                        onmouseout="this.style.background='#0E6838'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Unduh Format Formulir CV BPR (.csv)</span>
                    </a>
                    <a
                        href="{{ route('admin.download-template.employee-xls') }}"
                        download="template_migrasi_pegawai_bpr.xls"
                        style="display: inline-flex; align-items: center; gap: 0.55rem; padding: 0.65rem 1.15rem; border-radius: 12px; background: #064e3b; color: #ffffff; font-size: 0.85rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 2px 6px rgba(6, 78, 59, 0.25);"
                        onmouseover="this.style.background='#022c22'"
                        onmouseout="this.style.background='#064e3b'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Unduh Template Excel (.xls)</span>
                    </a>
                    <a
                        href="{{ route('admin.download-template.employee') }}"
                        download="template_migrasi_pegawai_bpr.csv"
                        style="display: inline-flex; align-items: center; gap: 0.55rem; padding: 0.65rem 1rem; border-radius: 12px; background: #f1f5f9; color: #334155; font-size: 0.82rem; font-weight: 600; text-decoration: none; border: 1px solid #cbd5e1; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#e2e8f0'"
                        onmouseout="this.style.background='#f1f5f9'"
                    >
                        <span>Unduh Template CSV Pegawai (.csv)</span>
                    </a>
                    <a
                        href="{{ route('admin.download-template.recruitment-tracker') }}"
                        download="template_recruitment_tracker_bpr.csv"
                        style="display: inline-flex; align-items: center; gap: 0.55rem; padding: 0.65rem 1rem; border-radius: 12px; background: #eff6ff; color: #1e40af; font-size: 0.82rem; font-weight: 700; text-decoration: none; border: 1px solid #bfdbfe; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#dbeafe'"
                        onmouseout="this.style.background='#eff6ff'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.765Z" />
                        </svg>
                        <span>Unduh Template Recruitment Tracker (.csv)</span>
                    </a>
                </div>

                <div style="border-top: 1px dashed var(--m3-outline-variant, #b6cac0); padding-top: 1.25rem; margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.5rem;">
                        Pilih Berkas Excel (.xlsx, .xls), CSV (.csv), atau PDF (.pdf) Data Pegawai:
                    </label>
                    <input
                        type="file"
                        wire:model="employeeFile"
                        accept=".xls,.xlsx,.csv,.txt,.pdf"
                        style="display: block; width: 100%; font-size: 0.85rem; padding: 0.6rem; border: 1px solid var(--m3-outline-variant, #b6cac0); border-radius: 12px; background: var(--m3-surface, #ffffff);"
                    />
                    @error('employeeFile')
                        <p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="employeeFile" style="font-size: 0.8rem; color: #0E6838; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <svg class="animate-spin" style="width: 14px; height: 14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengunggah berkas ke sistem...</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: stretch;">
                <div style="width: 100%;">
                    {{ $this->importEmployeesAction }}
                </div>
            </div>
        </div>

        {{-- KARTU 2: MIGRASI LOG ABSENSI / FINGERPRINT --}}
        <div class="m3-import-card">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(243, 122, 32, 0.15); color: #F37A20; display: flex; align-items: center; justify-content: center;" class="dark:bg-amber-950/60 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 style="font-size: 1.15rem; font-weight: 800;">Migrasi Log Absensi / Fingerprint</h4>
                            <p style="font-size: 0.8rem; color: #64748b;" class="dark:text-gray-400">Riwayat Kehadiran Mesin Presensi Lama</p>
                        </div>
                    </div>
                </div>

                <div style="background: rgba(243, 122, 32, 0.08); padding: 1.25rem; border-radius: 16px; margin-bottom: 1.5rem; font-size: 0.875rem; border: 1px solid rgba(243, 122, 32, 0.2);" class="dark:bg-amber-950/40 dark:border-amber-500/40">
                    <p style="margin-bottom: 0.65rem; font-weight: 800; font-size: 0.9rem;" class="text-amber-800 dark:text-amber-300">Fleksibilitas Data Absensi:</p>
                    <ul style="display: flex; flex-direction: column; gap: 0.5rem; padding-left: 0; list-style: none;" class="text-slate-700 dark:text-slate-200">
                        <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px;" class="text-amber-600 dark:text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>Mendukung ekspor file log dari mesin Solution, ZKTeco, maupun rekapan bulanan Excel.</span>
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px;" class="text-amber-600 dark:text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>Pencocokan dilakukan otomatis berdasarkan <strong>NIP Pegawai</strong> dan <strong>Tanggal</strong>.</span>
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px;" class="text-amber-600 dark:text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>Jika data tanggal sudah ada, sistem akan memperbarui jam pulang secara akurat.</span>
                        </li>
                    </ul>
                </div>

                {{-- Unduh Native (Bukan wire:click agar tidak memicu 419 expired) --}}
                <div style="margin-bottom: 1.25rem; display: flex; gap: 0.65rem; flex-wrap: wrap;">
                    <a
                        href="{{ route('admin.download-template.attendance-xls') }}"
                        download="template_impor_absensi_bpr.xls"
                        style="display: inline-flex; align-items: center; gap: 0.55rem; padding: 0.65rem 1.15rem; border-radius: 12px; background: #F37A20; color: #ffffff; font-size: 0.85rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 2px 6px rgba(243, 122, 32, 0.25);"
                        onmouseover="this.style.background='#d96512'"
                        onmouseout="this.style.background='#F37A20'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Unduh Template Excel (.xls)</span>
                    </a>
                    <a
                        href="{{ route('admin.download-template.attendance') }}"
                        download="template_impor_absensi_bpr.csv"
                        style="display: inline-flex; align-items: center; gap: 0.55rem; padding: 0.65rem 1rem; border-radius: 12px; background: #f1f5f9; color: #334155; font-size: 0.82rem; font-weight: 600; text-decoration: none; border: 1px solid #cbd5e1; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#e2e8f0'"
                        onmouseout="this.style.background='#f1f5f9'"
                    >
                        <span>Format CSV (.csv)</span>
                    </a>
                </div>

                <div style="border-top: 1px dashed var(--m3-outline-variant, #b6cac0); padding-top: 1.25rem; margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.5rem;">
                        Pilih Berkas Excel (.xlsx, .xls) atau CSV (.csv) Log Presensi:
                    </label>
                    <input
                        type="file"
                        wire:model="attendanceFile"
                        accept=".xls,.xlsx,.csv,.txt"
                        style="display: block; width: 100%; font-size: 0.85rem; padding: 0.6rem; border: 1px solid var(--m3-outline-variant, #b6cac0); border-radius: 12px; background: var(--m3-surface, #ffffff);"
                    />
                    @error('attendanceFile')
                        <p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="attendanceFile" style="font-size: 0.8rem; color: #F37A20; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <svg class="animate-spin" style="width: 14px; height: 14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengunggah berkas ke sistem...</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: stretch;">
                <div style="width: 100%;">
                    {{ $this->importAttendancesAction }}
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Kamus Data Kolom CSV --}}
    <div style="background-color: var(--m3-surface, #ffffff); border: 1px solid var(--m3-outline-variant, #b6cac0); border-radius: 24px; padding: 1.75rem 2rem;" class="dark:bg-[#111a14] dark:border-white/10">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;">
            <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(14, 104, 56, 0.12); color: #0E6838; display: flex; align-items: center; justify-content: center;" class="dark:bg-emerald-950/60 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
            </div>
            <h4 style="font-size: 1.1rem; font-weight: 800;">Kamus Format Kolom CSV Master Pegawai</h4>
        </div>
        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.25rem;" class="dark:text-gray-400">
            Berikut penjelasan format data kolom saat mengisi data lama di Microsoft Excel sebelum diunggah:
        </p>

        <div style="overflow-x: auto;">
            <table class="m3-field-table">
                <thead>
                    <tr>
                        <th>Nama Kolom Header</th>
                        <th>Wajib/Opsional</th>
                        <th>Contoh Nilai</th>
                        <th>Keterangan Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>NIP</strong></td>
                        <td><span style="color: #ef4444; font-weight: 700;">Wajib</span></td>
                        <td><code>BPR-2026-0101</code></td>
                        <td>Nomor Induk Pegawai unik, digunakan sebagai acuan sinkronisasi</td>
                    </tr>
                    <tr>
                        <td><strong>Nama_Lengkap</strong></td>
                        <td><span style="color: #ef4444; font-weight: 700;">Wajib</span></td>
                        <td><code>Hendri Prasetyo</code></td>
                        <td>Nama lengkap pegawai beserta gelar (jika ada)</td>
                    </tr>
                    <tr>
                        <td><strong>Email_Kantor</strong></td>
                        <td>Opsional</td>
                        <td><code>hendri@bprbandaraya.com</code></td>
                        <td>Jika kosong, dibuatkan otomatis <code>pegawai.[nip]@bprbandaraya.com</code></td>
                    </tr>
                    <tr>
                        <td><strong>Email_Pribadi</strong></td>
                        <td>Opsional</td>
                        <td><code>hendri.pribadi@gmail.com</code></td>
                        <td>Email korespondensi pribadi pegawai</td>
                    </tr>
                    <tr>
                        <td><strong>Nomor_Telepon</strong></td>
                        <td>Opsional</td>
                        <td><code>081266554411</code></td>
                        <td>Nomor WhatsApp / HP aktif pegawai</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis_Kelamin</strong></td>
                        <td>Opsional</td>
                        <td><code>Male / Female</code></td>
                        <td>Pilihan: Male (Laki-laki) atau Female (Perempuan)</td>
                    </tr>
                    <tr>
                        <td><strong>Tempat_Lahir & Tanggal_Lahir</strong></td>
                        <td>Opsional</td>
                        <td><code>Batam, 1995-04-23</code></td>
                        <td>Tempat kelahiran dan tanggal lahir format <code>YYYY-MM-DD</code></td>
                    </tr>
                    <tr>
                        <td><strong>Kantor_Cabang</strong></td>
                        <td>Opsional</td>
                        <td><code>Kantor Cabang Botania</code></td>
                        <td>Jika nama cabang baru, sistem otomatis membuat data cabang baru</td>
                    </tr>
                    <tr>
                        <td><strong>Divisi & Jabatan</strong></td>
                        <td>Opsional</td>
                        <td><code>Operasional & Pelayanan / CS</code></td>
                        <td>Otomatis dipetakan ke struktur organisasi BPR</td>
                    </tr>
                    <tr>
                        <td><strong>Grade & Tanggal_Efektif_Grade</strong></td>
                        <td>Opsional</td>
                        <td><code>4, 2024-01-15</code></td>
                        <td>Tingkat grade kepangkatan perbankan (1-15) & tgl efektif SK</td>
                    </tr>
                    <tr>
                        <td><strong>Status_Kerja</strong></td>
                        <td>Opsional</td>
                        <td><code>Permanent / Contract</code></td>
                        <td>Pilihan: Permanent, Contract, Probation, Internship (Default: Permanent)</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal_Masuk & Tanggal_Pengangkatan_Tetap</strong></td>
                        <td>Opsional</td>
                        <td><code>2024-01-15, 2025-01-15</code></td>
                        <td>Tanggal mulai bekerja dan tanggal SK pengangkatan tetap</td>
                    </tr>
                    <tr>
                        <td><strong>Akhir_Kontrak</strong></td>
                        <td>Opsional</td>
                        <td><code>2027-01-15</code></td>
                        <td>Batas waktu perjanjian kerja waktu tertentu (PKWT)</td>
                    </tr>
                    <tr>
                        <td><strong>Jenjang_Pendidikan, Jurusan, Universitas</strong></td>
                        <td>Opsional</td>
                        <td><code>S-1, Manajemen, UIB</code></td>
                        <td>Data riwayat pendidikan terakhir dan universitas</td>
                    </tr>
                    <tr>
                        <td><strong>IPK</strong></td>
                        <td>Opsional</td>
                        <td><code>3.45</code></td>
                        <td>Indeks Prestasi Kumulatif kelulusan</td>
                    </tr>
                    <tr>
                        <td><strong>NIK & NPWP</strong></td>
                        <td>Opsional</td>
                        <td><code>2171012304950001</code></td>
                        <td>Nomor KTP 16 digit dan NPWP resmi pegawai</td>
                    </tr>
                    <tr>
                        <td><strong>No_BPJSTK & No_BPJS_Kes</strong></td>
                        <td>Opsional</td>
                        <td><code>08017700462, 0001617375611</code></td>
                        <td>Nomor kepesertaan BPJS Ketenagakerjaan dan Kesehatan</td>
                    </tr>
                    <tr>
                        <td><strong>Rekening_BPR & Nomor_Rekening_Bank_Lain</strong></td>
                        <td>Opsional</td>
                        <td><code>01.10.05307, 109-00-1514578-2</code></td>
                        <td>Rekening tabungan BPR Banda Raya dan rekening bank eksternal</td>
                    </tr>
                    <tr>
                        <td><strong>Status_PTKP</strong></td>
                        <td>Opsional</td>
                        <td><code>K/1, TK/0, K/2</code></td>
                        <td>Status Penghasilan Tidak Kena Pajak untuk kepatuhan perpajakan</td>
                    </tr>
                    <tr>
                        <td><strong>Kontak_Darurat_Nama, Nomor, Hubungan</strong></td>
                        <td>Opsional</td>
                        <td><code>Dewi Anggraini, 0812..., Istri</code></td>
                        <td>Data kontak darurat (otomatis tersinkron ke modul keluarga pegawai)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
