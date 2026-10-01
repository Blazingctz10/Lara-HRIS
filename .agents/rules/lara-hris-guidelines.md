---
description: Pedoman desain Material 3 Expressive, penanganan error domain (DB/AUTH/SYS/VAL/HR), konsistensi ikon Heroicons, dan anti-clipping pada Lara-HRIS PT BPR Banda Raya.
globs: ["**/*.php", "**/*.blade.php", "**/*.css", "**/*.js"]
---

# Pedoman Pengembangan Lara-HRIS (PT BPR Banda Raya)

Aturan ini wajib dipatuhi oleh asisten AI dan pengembang saat menambahkan, mengubah, atau mendebug fitur dalam repositori **Lara-HRIS**.

---

## 1. Material 3 Expressive & Mod Mode (Dark & Light)

### A. Palet Warna Resmi BPR Banda Raya
- **Primary (Forest Green):** Light: `#0E6838` / Dark: `#4ADE80` (Aksen utama perbankan)
- **Secondary (Warm Orange):** Light: `#EA580C` / Dark: `#FB923C` (Notifikasi, peringatan kontrak, tombol HRD)
- **Tertiary (Golden Ribbon):** Light: `#CA8A04` / Dark: `#FACC15` (Highlight finansial & lisensi OJK)
- **Dark Surface:** `#0D1410` (Background utama), `#141E18` (Surface card), `#19261E` (Container)
- **Light Surface:** `#F4F6F4` (Background utama), `#FFFFFF` (Surface card), `#F0F5F2` (Container)

### B. Geometri & Radius Bentuk (Squircles & Pills)
- **Dialog / Modal Window:** `border-radius: 28px !important;` (M3 Container)
- **Widget & Section Card:** `border-radius: 24px !important;`
- **Form Input:** `border-radius: 16px !important;`
- **Tombol Aksi, Badges, & Chips:** `border-radius: 9999px !important;` (M3 Full Pill)

### C. Aturan Larangan Inline Style Pada Teks Dark Mode
> [!CRITICAL]
> **JANGAN PERNAH** menggunakan inline style warna statis seperti `style="color: #131a15"` pada teks dinamis atau judul card. Inline style memiliki spesifisitas tertinggi dan akan menimpa varian gelap Tailwind (`dark:text-white`).
> Selalu gunakan CSS class dengan selektor `:is(.dark) .selector { color: #FFFFFF !important; }` atau variabel CSS `--m3-on-surface`.

---

## 2. Standardisasi Kode Error Berbasis Domain

Setiap pesan error, exception handling, JSON API response, dan halaman error HTTP **wajib** menyertakan kode error domain terstruktur:

| Domain | Kode Error | Deskripsi / Kasus Penggunaan |
| :--- | :--- | :--- |
| **Database** | `DB001` | Gagal koneksi ke server database MySQL/PostgreSQL |
| **Database** | `DB002` | Pelanggaran Unique Constraint (cth: NIP/Email/NIK ganda) |
| **Database** | `DB003` | Pelanggaran Foreign Key Constraint (data referensi berelasi masih ada) |
| **Database** | `DB004` | Kesalahan sintaks SQL atau eksekusi query |
| **Database** | `DB005` | Deadlock database atau query execution timeout |
| **Autentikasi** | `AUTH001` | Kredensial tidak valid (email atau kata sandi salah) |
| **Autentikasi** | `AUTH002` | Akun pegawai berstatus non-aktif atau ditangguhkan |
| **Autentikasi** | `AUTH003` | Hak akses ditolak untuk panel backoffice / portal |
| **Autentikasi** | `AUTH004` | Sesi login telah berakhir atau token CSRF kedaluwarsa |
| **Autentikasi** | `AUTH005` | Akun terkunci sementara akibat proteksi brute-force |
| **Keamanan** | `SEC001` | Pelanggaran keamanan bot honeypot atau manipulasi payload |
| **Validasi** | `VAL001` | Data formulir yang dikirimkan tidak memenuhi syarat validasi |
| **HR Domain** | `HR001` | Saldo hak cuti tahunan tidak mencukupi |
| **HR Domain** | `HR002` | Ketidaksesuaian perhitungan komponen slip gaji payroll |
| **HR Domain** | `HR003` | Presensi di luar radius geofencing kantor cabang penempatan |
| **HR Domain** | `HR004` | Gagal sinkronisasi data log mesin fingerprint Solution X105 |
| **Sistem** | `SYS403` | HTTP 403 Forbidden (Akses dilarang) |
| **Sistem** | `SYS404` | HTTP 404 Not Found (Halaman atau dokumen tidak ditemukan) |
| **Sistem** | `SYS419` | HTTP 419 Page Expired (Sesi CSRF token habis) |
| **Sistem** | `SYS500` | HTTP 500 Internal Server Error (Kendala internal server) |

Semua exception yang tidak tertangani otomatis dikonversi oleh `App\Support\HrisErrorCode` dan dirender menggunakan layout terpadu `resources/views/errors/layout.blade.php`.

---

## 3. Penyeragaman Icon Set (Unified Heroicons)

Gunakan **Heroicons** secara konsisten di seluruh bagian backoffice, portal, dan view:
- **Presensi / Kehadiran:** `heroicon-o-clock`
- **Pengajuan Cuti:** `heroicon-o-document-check` (baik di Admin maupun Portal)
- **Pengajuan Lembur:** `heroicon-o-fire`
- **Klaim Biaya (Reimbursement):** `heroicon-o-receipt-percent`
- **Slip Gaji (Payroll):** `heroicon-o-banknotes`
- **Data Pegawai:** `heroicon-o-users`
- **Jaringan Kantor Cabang:** `heroicon-o-building-office-2`
- **Jabatan:** `heroicon-o-identification`
- **Surat Peringatan:** `heroicon-o-shield-exclamation`
- **Mesin Fingerprint:** `heroicon-o-finger-print`
- **Mutasi Karir:** `heroicon-o-arrows-right-left`
- **Pengumuman:** `heroicon-o-megaphone`
- **Profil Staf:** `heroicon-o-user-circle`

---

## 4. Ergonomi Topbar & Anti-Clipping di Portal Karyawan

Pada portal karyawan (`/portal`) yang menggunakan `->topNavigation()`, navigasi berada di satu baris horizontal:
1. `.fi-topbar-end` dan `.fi-user-menu` **wajib** memiliki `flex-shrink: 0 !important;` agar avatar / menu profil tidak pernah tertekan ke kanan.
2. `.fi-topbar-ctn` harus `overflow-x: visible !important;`.
3. Pada komponen `role-indicator.blade.php`, gunakan `@media (max-width: 1320px)` untuk menyembunyikan label nama cabang pada layar menengah/laptop, sehingga tersisa ruang yang cukup lega bagi menu user profile.
4. Pada `.fi-dropdown-header span`, gunakan `white-space: normal !important; word-break: break-word !important;` agar nama akun panjang seperti `Super Administrator BPR Banda Raya` tidak terpotong elipsis.
