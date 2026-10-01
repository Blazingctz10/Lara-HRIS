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
            background: linear-gradient(135deg, rgba(14, 104, 56, 0.25) 0%, rgba(17, 26, 20, 0.7) 100%);
            border-color: rgba(255, 255, 255, 0.1);
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

        .m3-step-number {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: #0E6838;
            color: #ffffff;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            margin-right: 0.75rem;
            flex-shrink: 0;
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
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <span style="font-size: 1.5rem;">📥</span>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0E6838;" class="dark:text-emerald-400">
                    Panduan Cepat Migrasi Data Lama ke Lara-HRIS
                </h3>
            </div>
            <p style="font-size: 0.9rem; color: #475569;" class="dark:text-gray-300">
                Data lama dari Microsoft Excel, format spreadsheet, ataupun sistem HR lama dapat dimigrasikan secara instan ke sistem ini tanpa input manual satu per satu.
            </p>
        </div>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; background: rgba(255,255,255,0.7); dark:background: rgba(0,0,0,0.3); padding: 0.6rem 1rem; border-radius: 14px; border: 1px solid rgba(14,104,56,0.15);">
                <span class="m3-step-number">1</span>
                <span style="font-size: 0.85rem; font-weight: 600;">Unduh Template CSV</span>
            </div>
            <div style="display: flex; align-items: center; background: rgba(255,255,255,0.7); dark:background: rgba(0,0,0,0.3); padding: 0.6rem 1rem; border-radius: 14px; border: 1px solid rgba(14,104,56,0.15);">
                <span class="m3-step-number">2</span>
                <span style="font-size: 0.85rem; font-weight: 600;">Salin Data Lama di Excel</span>
            </div>
            <div style="display: flex; align-items: center; background: rgba(255,255,255,0.7); dark:background: rgba(0,0,0,0.3); padding: 0.6rem 1rem; border-radius: 14px; border: 1px solid rgba(14,104,56,0.15);">
                <span class="m3-step-number">3</span>
                <span style="font-size: 0.85rem; font-weight: 600;">Unggah & Sistem Otomatis Eksekusi</span>
            </div>
        </div>
    </div>

    {{-- Grid Kartu Migrasi --}}
    <div class="m3-import-grid">
        {{-- KARTU 1: MIGRASI MASTER PEGAWAI --}}
        <div class="m3-import-card">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(14, 104, 56, 0.12); color: #0E6838; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            👥
                        </div>
                        <div>
                            <h4 style="font-size: 1.15rem; font-weight: 800;">Migrasi Data Master Pegawai</h4>
                            <p style="font-size: 0.8rem; color: #64748b;" class="dark:text-gray-400">Profil, Jabatan, Cabang, Akun Login & Kuota Cuti</p>
                        </div>
                    </div>
                </div>

                <div style="background: rgba(14, 104, 56, 0.05); padding: 1rem; border-radius: 14px; margin-bottom: 1.25rem; font-size: 0.85rem;">
                    <p style="margin-bottom: 0.5rem; font-weight: 700; color: #0E6838;" class="dark:text-emerald-400">⚡ Fitur Otomatisasi Cerdas:</p>
                    <ul style="list-style-type: disc; padding-left: 1.25rem; line-height: 1.5; color: #475569;" class="dark:text-gray-300">
                        <li>Jika Kantor Cabang, Divisi, atau Jabatan belum ada, sistem akan <strong>membuatnya otomatis</strong>.</li>
                        <li>Sistem otomatis membuatkan akun login ke <code>/portal</code> dengan password default <code>password</code>.</li>
                        <li>Saldo cuti tahunan awal (12 hari) langsung diaktifkan untuk setiap pegawai.</li>
                    </ul>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <button
                        type="button"
                        wire:click="downloadEmployeeTemplate"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; border-radius: 12px; background: #0E6838; color: #ffffff; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#0a4d29'"
                        onmouseout="this.style.background='#0E6838'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Unduh Template CSV Pegawai
                    </button>
                </div>

                <div style="border-top: 1px dashed var(--m3-outline-variant, #b6cac0); padding-top: 1.25rem; margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.5rem;">
                        Pilih Berkas CSV yang Sudah Diisi:
                    </label>
                    <input
                        type="file"
                        wire:model="employeeFile"
                        accept=".csv,.txt"
                        style="display: block; width: 100%; font-size: 0.85rem; padding: 0.6rem; border: 1px solid var(--m3-outline-variant, #b6cac0); border-radius: 12px; background: var(--m3-surface, #ffffff);"
                    />
                    @error('employeeFile')
                        <p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="employeeFile" style="font-size: 0.8rem; color: #0E6838; margin-top: 0.5rem;">
                        ⏳ Mengunggah berkas ke sistem...
                    </div>
                </div>
            </div>

            <div>
                <button
                    type="button"
                    x-data
                    @click="
                        if (confirm('Konfirmasi Migrasi Pegawai:\n\nApakah Anda yakin ingin memulai proses impor data pegawai dari berkas CSV ini? Akun login dan data profil akan disinkronkan ke dalam sistem.')) {
                            $wire.importEmployees();
                        }
                    "
                    wire:loading.attr="disabled"
                    style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.85rem 1.5rem; border-radius: 14px; background: #0E6838; color: #ffffff; font-weight: 800; font-size: 0.95rem; border: none; cursor: pointer; transition: all 0.2s ease;"
                    onmouseover="this.style.background='#0a4d29'"
                    onmouseout="this.style.background='#0E6838'"
                >
                    <span wire:loading.remove wire:target="importEmployees">🚀 Mulai Impor & Migrasikan Pegawai</span>
                    <span wire:loading wire:target="importEmployees">Memproses Migrasi Pegawai...</span>
                </button>
            </div>
        </div>

        {{-- KARTU 2: MIGRASI LOG ABSENSI / FINGERPRINT --}}
        <div class="m3-import-card">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(243, 122, 32, 0.15); color: #F37A20; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            🕒
                        </div>
                        <div>
                            <h4 style="font-size: 1.15rem; font-weight: 800;">Migrasi Log Absensi / Fingerprint</h4>
                            <p style="font-size: 0.8rem; color: #64748b;" class="dark:text-gray-400">Riwayat Kehadiran Mesin Presensi Lama</p>
                        </div>
                    </div>
                </div>

                <div style="background: rgba(243, 122, 32, 0.08); padding: 1rem; border-radius: 14px; margin-bottom: 1.25rem; font-size: 0.85rem;">
                    <p style="margin-bottom: 0.5rem; font-weight: 700; color: #F37A20;">⚡ Fleksibilitas Data Absensi:</p>
                    <ul style="list-style-type: disc; padding-left: 1.25rem; line-height: 1.5; color: #475569;" class="dark:text-gray-300">
                        <li>Mendukung ekspor file log dari mesin Solution, ZKTeco, maupun rekapan bulanan Excel.</li>
                        <li>Pencocokan dilakukan otomatis berdasarkan <strong>NIP Pegawai</strong> dan <strong>Tanggal</strong>.</li>
                        <li>Jika data tanggal sudah ada, sistem akan memperbarui jam keluar (*clock-out*) secara akurat.</li>
                    </ul>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <button
                        type="button"
                        wire:click="downloadAttendanceTemplate"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; border-radius: 12px; background: #F37A20; color: #ffffff; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#d96512'"
                        onmouseout="this.style.background='#F37A20'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Unduh Template CSV Absensi
                    </button>
                </div>

                <div style="border-top: 1px dashed var(--m3-outline-variant, #b6cac0); padding-top: 1.25rem; margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 700; margin-bottom: 0.5rem;">
                        Pilih Berkas CSV Log Presensi:
                    </label>
                    <input
                        type="file"
                        wire:model="attendanceFile"
                        accept=".csv,.txt"
                        style="display: block; width: 100%; font-size: 0.85rem; padding: 0.6rem; border: 1px solid var(--m3-outline-variant, #b6cac0); border-radius: 12px; background: var(--m3-surface, #ffffff);"
                    />
                    @error('attendanceFile')
                        <p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="attendanceFile" style="font-size: 0.8rem; color: #F37A20; margin-top: 0.5rem;">
                        ⏳ Mengunggah berkas ke sistem...
                    </div>
                </div>
            </div>

            <div>
                <button
                    type="button"
                    x-data
                    @click="
                        if (confirm('Konfirmasi Impor Absensi:\n\nApakah Anda yakin ingin mengimpor riwayat data presensi ini ke kalender kehadiran sistem?')) {
                            $wire.importAttendances();
                        }
                    "
                    wire:loading.attr="disabled"
                    style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.85rem 1.5rem; border-radius: 14px; background: #F37A20; color: #ffffff; font-weight: 800; font-size: 0.95rem; border: none; cursor: pointer; transition: all 0.2s ease;"
                    onmouseover="this.style.background='#d96512'"
                    onmouseout="this.style.background='#F37A20'"
                >
                    <span wire:loading.remove wire:target="importAttendances">⏱️ Mulai Impor Log Absensi</span>
                    <span wire:loading wire:target="importAttendances">Memproses Impor Absensi...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Tabel Kamus Data Kolom CSV --}}
    <div style="background-color: var(--m3-surface, #ffffff); border: 1px solid var(--m3-outline-variant, #b6cac0); border-radius: 24px; padding: 1.75rem 2rem;" class="dark:bg-[#111a14] dark:border-white/10">
        <h4 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📖</span> Kamus Format Kolom CSV Master Pegawai
        </h4>
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
                        <td><strong>Email</strong></td>
                        <td>Opsional</td>
                        <td><code>hendri@bprbandaraya.com</code></td>
                        <td>Jika kosong, dibuatkan otomatis <code>pegawai.[nip]@bprbandaraya.com</code></td>
                    </tr>
                    <tr>
                        <td><strong>Kantor_Cabang</strong></td>
                        <td>Opsional</td>
                        <td><code>Kantor Cabang Botania</code></td>
                        <td>Jika nama cabang baru, sistem otomatis membuat data cabang baru</td>
                    </tr>
                    <tr>
                        <td><strong>Divisi</strong></td>
                        <td>Opsional</td>
                        <td><code>Operasional & Pelayanan</code></td>
                        <td>Jika divisi baru, sistem otomatis membuat divisi baru</td>
                    </tr>
                    <tr>
                        <td><strong>Jabatan</strong></td>
                        <td>Opsional</td>
                        <td><code>Customer Service</code></td>
                        <td>Jika jabatan baru, sistem otomatis membuat jabatan baru</td>
                    </tr>
                    <tr>
                        <td><strong>Status_Kerja</strong></td>
                        <td>Opsional</td>
                        <td><code>Permanent / Contract</code></td>
                        <td>Pilihan: Permanent, Contract, Probation, Internship (Default: Permanent)</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal_Masuk</strong></td>
                        <td>Opsional</td>
                        <td><code>2024-01-15</code></td>
                        <td>Format standar <code>YYYY-MM-DD</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
