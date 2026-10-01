<div align="center">
  <img src="public/images/logo-horizontal-clean.png" alt="PT BPR Banda Raya" width="480">
  <br><br>
  <h1>Core HRIS & Employee Self-Service (ESS) Portal</h1>
  <p><strong>Sistem Informasi Manajemen Sumber Daya Manusia Terpadu PT Bank Perekonomian Rakyat Banda Raya</strong></p>

  [![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![Filament v3](https://img.shields.io/badge/Filament-v3.x-FDAE4B?style=for-the-badge&logo=filament&logoColor=black)](https://filamentphp.com)
  [![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
  [![Design](https://img.shields.io/badge/Design-Material_3_Expressive-0E6838?style=for-the-badge)](https://m3.material.io)
  [![Tests](https://img.shields.io/badge/Tests-23%20Passed%20(141%20assertions)-success?style=for-the-badge&logo=checkmarx&logoColor=white)](tests/)
</div>

---

## 📌 Ringkasan Sistem

**Core HRIS PT BPR Banda Raya** adalah platform enterprise terpadu untuk tata kelola siklus hidup kepegawaian (*employee lifecycle*), presensi pintar berbasis radius geofencing GPS, persetujuan cuti berjenjang, manajemen pergerakan karir & promosi, penegakan disiplin (Surat Peringatan), slip gaji (payroll) digital, klaim reimbursement, serta manajemen arsip cadangan data sistem.

Aplikasi dirancang dengan arsitektur **Dual-Panel (Backoffice HR & Portal Mandiri Karyawan)** dan mengadopsi standar **Material 3 Expressive Tonal Container**:
- 🌲 **Deep Emerald Green (`#0E6838`)** — Identitas korporat perbankan yang stabil, aman, dan berintegritas.
- 🍃 **Warm Sage-Mint (`#E8EFEA` / `#DFEAE2`)** — Ergonomi visual mode terang tanpa silau (*zero eye strain*) dengan rasio kontras tinggi.
- 🌑 **Slate-Emerald (`#0D1410` / `#141E18`)** — Mode gelap sejuk yang nyaman untuk mata saat bertugas malam.
- 📙 **Warm Orange (`#EA580C`)** & 🎗️ **Golden Amber (`#CA8A04`)** — Aksen indikator status operasional dan apresiasi kinerja.

---

## 🏛️ Arsitektur Dual-Panel & Hak Akses

```mermaid
graph TD
    User([Pengguna Masuk]) --> Login[Halaman Masuk Terpadu]
    Login --> AuthCheck{Pemeriksaan Peran / Role}
    
    AuthCheck -->|super_admin| AdminPanel[Panel Backoffice HRD /admin]
    AuthCheck -->|hr_admin| AdminPanel
    AuthCheck -->|branch_manager| AdminScoped[Panel Backoffice Scoped Cabang /admin]
    AuthCheck -->|employee| PortalPanel[Portal Mandiri Pegawai ESS /portal]
    
    PortalPanel -.->|Akses Ditolak 403| AdminPanel
```

| Panel | Rute URL | Akses Peran | Cakupan Fungsional |
| :--- | :--- | :--- | :--- |
| **Backoffice HR Panel** | `/admin` | Super Admin, HR Officer, Pimpinan Cabang (Pincab) | Tata kelola master cabang, departemen, jabatan, master pegawai, persetujuan cuti & klaim, surat peringatan, mutasi kerja, mesin fingerprint, payroll, dan pencadangan data sistem. |
| **Portal Mandiri Pegawai (ESS)** | `/portal` | Karyawan / Staf (Teller, CS, AO, Security, Umum) | Presensi GPS Geofencing harian, riwayat kehadiran, pengajuan cuti & lembur, slip gaji mandiri, dan pembaruan biodata kontak. |

---

## 🔄 Alur Proses Bisnis Utama (Core Workflows)

### 1. Alur Presensi Pintar & Geofencing GPS
```mermaid
sequenceDiagram
    autonumber
    actor Staf as Pegawai (ESS)
    participant App as Portal Pegawai
    participant Geo as GeofencingService
    participant DB as Basis Data (Attendances)

    Staf->>App: Buka Widget Presensi Hari Ini
    App->>Staf: Minta Izin Koordinat Geolocation Browser/HP
    Staf->>App: Koordinat GPS (Latitude, Longitude)
    App->>Geo: Validasi Jarak ke Kantor Penempatan (Haversine Formula)
    alt Di dalam radius kantor (Misal <= 50 meter)
        Geo-->>App: Valid (Status: Present / Late)
        App->>DB: Rekam Jam Masuk, Koordinat & Device Info
        App-->>Staf: Presensi Berhasil Diterima
    else Di luar radius kantor
        Geo-->>App: Invalid (Jarak > 50 meter)
        App-->>Staf: Presensi Ditolak (Di Luar Radius Kantor Cabang)
    end
```

### 2. Alur Pengajuan & Persetujuan Cuti Berjenjang (Dual-Approval)
```mermaid
sequenceDiagram
    autonumber
    actor Staf as Pegawai
    participant ESS as Portal Cuti
    actor Pincab as Pemimpin Cabang
    actor HR as Human Resources
    participant Obs as LeaveRequestObserver
    participant Saldo as EmployeeLeaveBalance

    Staf->>ESS: Ajukan Permohonan Cuti (Tanggal, Jenis, Alasan)
    ESS->>ESS: Status = 'Pending'
    Pincab->>ESS: Tinjau Pengajuan di /admin (Scoped Cabang)
    Pincab->>ESS: Setujui Cabang (Status = 'Approved_Pincab')
    HR->>ESS: Tinjau Final Approval di /admin (HR Nasional)
    HR->>ESS: Setujui Final HR (Status = 'Approved_HR')
    ESS->>Obs: Trigger Event Updated
    Obs->>Saldo: Potong 'used_days' & Kurangi 'remaining_days'
    Saldo-->>Staf: Kuota Cuti Tahunan Berkurang Otomatis
```

### 3. Alur Pergerakan Karir (Mutasi & Promosi)
1. HR Admin menerbitkan SK Karir melalui modul `/admin/career-movements`.
2. Sistem menampilkan popup konfirmasi untuk mencegah kekeliruan nomor SK atau cabang penempatan.
3. Saat disimpan, sistem secara otomatis memperbarui relasi `branch_id` dan `position_id` pada profil pegawai terkait.

### 4. Alur Penegakan Disiplin (Surat Peringatan / SP)
1. Penerbitan SP1, SP2, atau SP3 dilakukan melalui `/admin/warning-letters` dilengkapi masa berlaku otomatis (6 bulan).
2. Surat peringatan terpantau secara transparan oleh HR Officer untuk evaluasi kontrak kerja berkala.

### 5. Alur Pencadangan & Kepatuhan Data (Backup Data)
1. Super Admin mengakses menu `/admin/backup-data`.
2. Memilih opsi **"Cadangkan Database Saja"** atau **"Cadangkan Arsip Lengkap (ZIP: DB + Berkas Dokumen)"**.
3. Sistem memunculkan popup konfirmasi otorisasi perbankan.
4. Arsip disimpan di `storage/app/backups/` dengan penamaan timestamp unik dan dapat diunduh langsung ke komputer lokal.

### 6. Alur Rekrutmen & Integrasi Email Masuk (cPanel IMAP)
```mermaid
sequenceDiagram
    autonumber
    actor Pelamar as Pelamar Kerja
    participant cPanel as Mail Server cPanel (karir@bprbandaraya.com)
    participant IMAP as ImapRecruitmentService (Port 993 SSL)
    participant DB as Modul Rekrutmen (/admin/job-applicants)
    actor HR as Tim HRD & Perekrutan

    Pelamar->>cPanel: Kirim Surat Lamaran & Lampiran CV (PDF/DOCX)
    HR->>DB: Klik "Tarik Email (cPanel IMAP)"
    DB->>IMAP: Eksekusi koneksi TLS/SSL ke cPanel Webmail
    IMAP->>cPanel: Baca INBOX, unduh email baru & file lampiran
    IMAP->>DB: Simpan pelamar, ekstrak posisi, & arsip CV ke storage
    DB-->>HR: Tampilkan daftar pelamar, preview CV, dan kelola status seleksi
```
1. **Otomatisasi Tanpa Buka Webmail**: Email pelamar yang masuk ke `karir@bprbandaraya.com` disedot langsung oleh sistem Lara-HRIS.
2. **Ekstraksi CV & Posisi Otomatis**: Sistem secara otomatis mengekstrak berkas lampiran (PDF/DOCX) ke direktori privat `storage/app/public/recruitment_cv` dan mendeteksi posisi yang dilamar dari subjek email.
### 7. Alur Migrasi & Impor Data Lama (Excel / CSV Migration Center)
1. **Menu Akses**: Super Admin dan HR Admin dapat mengakses menu `/admin/import-data` (Grup *Pengaturan & Sistem*).
2. **Template CSV Siap Unduh**:
   * **Template Master Pegawai**: Memuat kolom lengkap (`NIP`, `NIK`, `Nama_Lengkap`, `Email`, `Nomor_Telepon`, `Jenis_Kelamin`, `Tanggal_Lahir`, `Alamat`, `Kantor_Cabang`, `Divisi`, `Jabatan`, `Status_Kerja`, `Tanggal_Masuk`, `Nomor_Rekening`, dll) beserta contoh data valid.
   * **Template Log Presensi**: Memuat kolom (`NIP`, `Tanggal`, `Jam_Masuk`, `Jam_Keluar`, `Status`, `Catatan`) untuk memindahkan riwayat absensi dari mesin fingerprint lama (Solution, ZKTeco, dsb).
3. **Fitur Auto-Discovery & Smart Mapping Cerdas**:
   * **Auto-Create Organisasi**: Jika Cabang, Divisi, atau Jabatan dari file lama belum terdaftar di sistem, sistem otomatis membuatkannya.
   * **Auto-Generate Akun Login**: Akun pengguna (`User`) langsung dibuatkan otomatis dengan domain resmi `@bprbandaraya.com` dan kata sandi default `password`.
   * **Inisialisasi Kuota Cuti Otomatis**: Setiap pegawai baru yang diimpor langsung diberikan saldo kuota cuti tahunan (12 hari).
   * **Dukungan Format Excel Regional**: Sistem otomatis mendeteksi pemisah koma (`,`) maupun titik koma (`;`) yang biasa dihasilkan Microsoft Excel berbahasa Indonesia.

---

## 🗄️ Diagram Relasi Basis Data (Entity Relationship Diagram - ERD)

```mermaid
erDiagram
    BRANCHES ||--o{ EMPLOYEES : "penempatan di"
    BRANCHES ||--o{ FINGERPRINT_DEVICES : "memiliki mesin"
    BRANCHES ||--o{ ANNOUNCEMENTS : "target cabang"
    
    DEPARTMENTS ||--o{ POSITIONS : "memiliki jabatan"
    DEPARTMENTS ||--o{ EMPLOYEES : "divisi kerja"
    POSITIONS ||--o{ EMPLOYEES : "jabatan resmi"
    
    USERS ||--|| EMPLOYEES : "terhubung akun"
    
    EMPLOYEES ||--o{ ATTENDANCES : "catatan log"
    EMPLOYEES ||--o{ LEAVE_REQUESTS : "mengajukan cuti"
    EMPLOYEES ||--|| EMPLOYEE_LEAVE_BALANCES : "kuota saldo cuti"
    EMPLOYEES ||--o{ PAYROLLS : "slip gaji"
    EMPLOYEES ||--o{ OVERTIMES : "pengajuan lembur"
    EMPLOYEES ||--o{ REIMBURSEMENTS : "klaim operasional"
    EMPLOYEES ||--o{ CAREER_MOVEMENTS : "riwayat mutasi/promosi"
    EMPLOYEES ||--o{ WARNING_LETTERS : "catatan sanksi SP"
    
    LEAVE_TYPES ||--o{ LEAVE_REQUESTS : "kategori cuti"
    USERS ||--o{ LEAVE_REQUESTS : "disetujui oleh"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "super_admin, hr_admin, branch_manager, employee"
        bigint branch_id FK
        timestamps created_at
    }

    EMPLOYEES {
        bigint id PK
        bigint user_id FK
        bigint branch_id FK
        bigint department_id FK
        bigint position_id FK
        string nip UK
        string nik UK
        string first_name
        string last_name
        string phone
        text address
        enum employment_status "Permanent, Contract, Probation, Internship"
        enum status "Active, On Leave, Resigned, Terminated"
        date join_date
        date contract_end_date
        decimal basic_salary
        string bank_account_number
        string bank_account_holder
        string npwp
    }

    BRANCHES {
        bigint id PK
        string name
        string code UK
        text address
        decimal latitude
        decimal longitude
        integer radius_meters
    }

    DEPARTMENTS {
        bigint id PK
        string name
        string code UK
    }

    POSITIONS {
        bigint id PK
        bigint department_id FK
        string name
        string level
    }

    ATTENDANCES {
        bigint id PK
        bigint employee_id FK
        date date
        time check_in
        time check_out
        decimal check_in_latitude
        decimal check_in_longitude
        enum status "Present, Late, Absent, On Leave"
        enum source "GPS_Mobile, Fingerprint, Manual_HR"
        string device_info
    }

    LEAVE_TYPES {
        bigint id PK
        string name
        integer days_allocated
        boolean is_paid
    }

    LEAVE_REQUESTS {
        bigint id PK
        bigint employee_id FK
        bigint leave_type_id FK
        date start_date
        date end_date
        integer total_days
        text reason
        enum status "Pending, Approved_Pincab, Approved_HR, Rejected"
        bigint approved_by FK
        text rejection_reason
    }

    EMPLOYEE_LEAVE_BALANCES {
        bigint id PK
        bigint employee_id FK
        integer year
        integer allocated_days
        integer used_days
        integer remaining_days
    }

    PAYROLLS {
        bigint id PK
        bigint employee_id FK
        integer period_month
        integer period_year
        decimal basic_salary
        decimal allowances
        decimal deductions
        decimal overtime_pay
        decimal net_salary
        enum status "Draft, Approved, Paid"
    }

    CAREER_MOVEMENTS {
        bigint id PK
        bigint employee_id FK
        enum type "Mutasi Cabang, Promosi Jabatan, Pengangkatan Tetap, Demosi"
        string sk_number
        bigint from_branch_id FK
        bigint to_branch_id FK
        bigint from_position_id FK
        bigint to_position_id FK
        date effective_date
        text notes
    }

    WARNING_LETTERS {
        bigint id PK
        bigint employee_id FK
        string sp_number UK
        enum level "SP1, SP2, SP3"
        date effective_date
        date valid_until
        text reason
        enum status "Active, Expired, Revoked"
    }

    ANNOUNCEMENTS {
        bigint id PK
        string title
        text content
        enum priority "normal, important, urgent"
        bigint branch_id FK "nullable for all"
        datetime published_at
        datetime expires_at
    }

    JOB_APPLICANTS {
        bigint id PK
        string name
        string email
        string phone
        string position_applied
        string subject
        text message
        string cv_path
        string cv_filename
        string email_uid UK
        datetime email_date
        enum status "Baru, Screening, Interview, Diterima, Ditolak"
        text notes
        timestamps created_at
    }
```

---

## 👥 Matriks Kasus Penggunaan (Use Case Matrix)

| Fitur / Modul | Staf Pegawai (ESS) | Pemimpin Cabang (Pincab) | HR Officer | Super Admin (IT) |
| :--- | :---: | :---: | :---: | :---: |
| **Login Mandiri & Ganti Tema** | ✅ | ✅ | ✅ | ✅ |
| **Presensi GPS Geofencing** | ✅ Mandiri | ❌ | ❌ | ❌ |
| **Lihat Riwayat & Slip Gaji** | ✅ Milik Sendiri | ❌ | ✅ Semua Pegawai | ✅ Semua Pegawai |
| **Pengajuan Cuti & Lembur** | ✅ Ajukan | ❌ | ❌ | ❌ |
| **Persetujuan Cuti Tahap 1 (Pincab)** | ❌ | ✅ Cabang Sendiri | ❌ | ❌ |
| **Persetujuan Final Cuti (HR)** | ❌ | ❌ | ✅ Nasional | ✅ Nasional |
| **Kelola Data Pegawai & Gaji** | ❌ | ❌ | ✅ Kelola | ✅ Kelola |
| **Terbitkan SP & Mutasi Karir** | ❌ | ❌ | ✅ Terbitkan | ✅ Terbitkan |
| **Kelola Master Cabang & Mesin** | ❌ | ❌ | ❌ | ✅ Kelola |
| **Backup & Unduh Arsip Sistem** | ❌ | ❌ | ❌ | ✅ Khusus Super Admin |

---

## 🛡️ Arsitektur Keamanan Siber (Security Hardening)

Lara-HRIS telah diperkuat dengan standar perlindungan perbankan berlapis:

1. **Honeypot Trap:**
   Formulir login dilengkapi input tersembunyi `bpr_hp_verification`. Bot/scraper otomatis yang mengisi kolom ini langsung digagalkan tanpa respons error verbose.
2. **Login Rate Limiter:**
   Membatasi maksimal 5 percobaan login gagal berturut-turut per menit. Melindungi akun dari serangan kamus dan *brute-force dictionary attacks*.
3. **HTTP Security Headers Lengkap:**
   - `X-Frame-Options: SAMEORIGIN` (Mencegah serangan *Clickjacking* / pencurian klik).
   - `X-Content-Type-Options: nosniff` (Mencegah eksploitasi interpretasi tipe berkas MIME).
   - `X-XSS-Protection: 1; mode=block` (Mencegah serangan skrip lintas situs).
   - `Strict-Transport-Security` (HSTS: Memaksa koneksi HTTPS terenkripsi).
   - `Referrer-Policy: strict-origin-when-cross-origin` (Melindungi privasi rujukan URL).
4. **Isolasi Panel & Hak Akses (RBAC):**
   - Karyawan staf non-admin otomatis diblokir saat mencoba mengakses rute `/admin` (HTTP 403 Forbidden).
   - Akun Super Admin langsung dialihkan (*redirect*) ke panel administrasi `/admin`.
   - Modul pencadangan data (`/admin/backup-data`) dilindungi verifikasi eksplisit `canAccess(): bool`.
5. **Modal Konfirmasi Berdampak Tinggi:**
   Penyimpanan profil, perubahan rekening, persetujuan pemotongan cuti, penerbitan SP, SK mutasi, dan pencadangan database wajib melalui dialog konfirmasi persetujuan sadar pengguna (*conscious consent modal*).
6. **Integritas Audit Trail:**
   Kolom data formal kepegawaian (NIP, NIK, Cabang, Status Karyawan) dikunci *read-only* pada Portal Pegawai untuk menjamin integritas rekam jejak audit OJK/Bank Indonesia.

---

## 🔑 Kredensial Pengujian (Demo Accounts)

> **Kata sandi default untuk seluruh akun demo:** `password`

| Peran | Nama Pegawai | Email Login | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Super Admin** | Administrator Sistem | `admin@bprbandaraya.com` | `/admin` (Akses Penuh + Backup) |
| **HR Officer** | Ratna Dewi, S.Psi | `ratna.dewi@bprbandaraya.com` | `/admin` (Manajemen HR Nasional) |
| **Pincab Botania** | Hendra Kurniawan | `pincab.botania@bprbandaraya.com` | `/admin` (Khusus Cabang Botania) |
| **Teller Botania** | Siti Rahma | `teller.botania@bprbandaraya.com` | `/portal` (Portal Mandiri Pegawai) |
| **CS Botania** | Budi Santoso | `cs.botania@bprbandaraya.com` | `/portal` (Portal Mandiri Pegawai) |
| **AO Botania** | Fajar Nugraha | `fajar.nugraha@bprbandaraya.com` | `/portal` (Portal Mandiri Pegawai) |

---

## 🚀 Panduan Menjalankan Sistem

### 1. Prasyarat Lingkungan
- PHP >= 8.2 (ekstensi: `pdo_sqlite`, `intl`, `mbstring`, `fileinfo`, `gd`, `zip`, `openssl`)
- Composer >= 2.x
- Web Browser modern (Chrome, Edge, Firefox, Safari)

### 2. Cara Menjalankan Server
```bash
# Cara Praktis (Rekomendasi di Windows):
# Jalankan start.bat di folder utama proyek
..\start.bat
```
Menu interaktif otomatis menguji port `8000`, menjalankan background task worker, dan menampilkan log realtime dengan opsi matikan server bersih `[X]`.

Atau via command prompt manual:
```bash
cd hris-system
php artisan serve
```
Akses di peramban: **`http://127.0.0.1:8000`**

---

## 🧪 Hasil Pengujian Otomatis (Automated Tests)

Seluruh modul telah diuji dengan **23 Test Suites (141 Assertions)** tanpa kegagalan:

```bash
php artisan test
```

```text
   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Feature\BackupTest
   PASS  Tests\Feature\ComprehensiveResourceTest
   PASS  Tests\Feature\ExampleTest
   PASS  Tests\Feature\HrisTest
   PASS  Tests\Feature\PanelRoutesTest
   PASS  Tests\Feature\SecurityTest

  Tests:    23 passed (141 assertions)
  Duration: 100% Pass
```

---

## 📄 Lisensi & Hak Cipta
Hak Cipta &copy; 2026 **PT Bank Perekonomian Rakyat Banda Raya**. Dilindungi Undang-Undang.
