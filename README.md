<div align="center">
  <img src="public/images/logo-horizontal-clean.png" alt="PT BPR Banda Raya" width="480">
  <br><br>
  <h1>Core HRIS & Employee Self-Service (ESS) Portal</h1>
  <p><strong>Sistem Informasi Manajemen Sumber Daya Manusia Terpadu PT Bank Perekonomian Rakyat Banda Raya</strong></p>

  [![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![Filament v3](https://img.shields.io/badge/Filament-v3.x-FDAE4B?style=for-the-badge&logo=filament&logoColor=black)](https://filamentphp.com)
  [![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
  [![Design](https://img.shields.io/badge/Design-Material_3_Expressive-0E6838?style=for-the-badge)](https://m3.material.io)
  [![Tests](https://img.shields.io/badge/Tests-19%20Passed%20(100%25)-success?style=for-the-badge&logo=checkmarx&logoColor=white)](tests/)
</div>

---

## 📌 Ringkasan Sistem

**Core HRIS PT BPR Banda Raya** adalah platform terintegrasi untuk pengelolaan administrasi kepegawaian, presensi berbasis GPS geofencing, manajemen cuti, penggajian (payroll), mutasi karir, klaim reimbursement, dan surat peringatan.

Dibangun dengan arsitektur **Dual-Panel** berbasis **Filament v3** dan dirancang menggunakan panduan desain **Material 3 Expressive** dengan palet warna resmi perusahaan:
- 🌲 **Forest Green (`#0E6838`)** — Identitas utama perbankan yang kokoh dan terpercaya.
- 📙 **Warm Orange (`#F37A20`)** — Aksen energi, pelayanan, dan keramahan.
- 🎗️ **Golden Ribbon (`#FDB813`)** — Simbol apresiasi kinerja dan kemakmuran nasabah.

---

## 🏛️ Arsitektur Dual-Panel

| Panel | Rute URL | Akses Peran | Deskripsi |
| :--- | :--- | :--- | :--- |
| **Backoffice HR Panel** | `/admin` | Super Admin, HR Officer, Pimpinan Cabang (Pincab) | Tata kelola master data kantor cabang, departemen, jabatan, pegawai, persetujuan cuti & klaim, modul SP, mutasi kerja, mesin fingerprint, dan rekap payroll. |
| **Portal Mandiri Pegawai (ESS)** | `/portal` | Semua Karyawan (Teller, CS, AO, Staf) | Presensi mandiri (GPS & Geofencing radius kantor), ringkasan saldo cuti tahunan, pengajuan cuti & lembur, slip gaji digital, serta pembaruan profil mandiri. |

---

## ✨ Fitur Unggulan

### 1. 🏢 Manajemen Multi-Cabang & Hierarki Peran
- Mendukung operasional terdistribusi:
  - **Kantor Pusat**: Nagoya Hill, Batam.
  - **Kantor Cabang Botania**: Batam Centre.
  - **Kantor Cabang Aviari**: Batu Aji.
  - **Kantor Cabang Bengkong**: Golden City.
- **Data Scoping Otomatis**: Pimpinan Cabang (Pincab) hanya melihat data cabang masing-masing, sementara Super Admin dan HR Officer mengelola lingkup nasional.
- **Role Indicator Badge**: Menampilkan lencana identitas peran aktif secara visual pada bilah navigasi atas (topbar).

### 2. 📍 Presensi Pintar & Geofencing GPS
- Verifikasi radius lokasi koordinat latitude/longitude masing-masing kantor cabang.
- Integrasi pencatatan jam masuk, jam pulang, toleransi keterlambatan, dan status kehadiran otomatis.
- Widget presensi harian interaktif di beranda Portal Pegawai.

### 3. 🌴 Manajemen Cuti & Kuota Otomatis
- Pelacakan kuota cuti tahunan dan cuti khusus secara real-time.
- Pengurangan otomatis saldo cuti saat HR menyetujui pengajuan (*Observer pattern*).
- Riwayat permohonan transparan dengan status verifikasi bertingkat.

### 4. 👤 Profil Mandiri Karyawan (Employee Self-Service)
- Karyawan dapat memperbarui foto profil, nomor WhatsApp, nomor darurat, alamat domisili, dan mengganti password mandiri.
- Kolom sensitif (NIP, Jabatan, Cabang, Gaji Pokok, Tanggal Masuk) terkunci read-only demi tata kelola audit perbankan.

### 5. 💰 Penggajian (Payroll) & Reimbursement
- Perhitungan Gaji Pokok, Tunjangan Jabatan, Uang Makan, BPJS Ketenagakerjaan & Kesehatan, PPh 21, serta akumulasi lembur.
- Pengajuan klaim kuitansi reimbursement operasional dengan bukti unggahan dokumen.

### 6. 🛡️ Keamanan & Hardening Produksi
- **Honeypot Trap**: Memblokir bot otomatis pada form login tanpa mengganggu pengguna asli.
- **Rate Limiting**: Proteksi serangan brute-force (maksimal 5 kali percobaan gagal per menit).
- **Security Headers**: Dilengkapi CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, dan HSTS.

---

## 🔑 Akun & Kredensial Pengujian

> **Password default untuk semua akun pengujian:** `password`

| Peran | Nama Pegawai | Email Login | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Super Admin** | Administrator Sistem | `admin@bprbandaraya.com` | `/admin` (Penuh) |
| **HR Officer** | Ratna Dewi, S.Psi | `ratna.dewi@bprbandaraya.com` | `/admin` (Nasional) |
| **Pincab Botania** | Hendra Kurniawan | `pincab.botania@bprbandaraya.com` | `/admin` (Scoped Cabang) |
| **Teller Botania** | Siti Rahma | `teller.botania@bprbandaraya.com` | `/portal` (ESS Karyawan) |
| **CS Botania** | Budi Santoso | `cs.botania@bprbandaraya.com` | `/portal` (ESS Karyawan) |
| **AO Botania** | Rizky Pratama | `ao.botania@bprbandaraya.com` | `/portal` (ESS Karyawan) |

---

## 🚀 Panduan Instalasi Lokal

### Prasyarat
- PHP >= 8.2 (ekstensi: `pdo_sqlite`, `intl`, `mbstring`, `fileinfo`, `gd`, `openssl`)
- Composer >= 2.x
- Node.js & NPM (opsional untuk aset frontend)

### Langkah Pemasangan
```bash
# 1. Masuk ke direktori aplikasi
cd hris-system

# 2. Pasang dependensi PHP
composer install

# 3. Siapkan file konfigurasi lingkungan
cp .env.example .env
php artisan key:generate

# 4. Jalankan migrasi dan seeder database awal
php artisan migrate --seed

# 5. Jalankan server lokal
# Opsi A: Menggunakan Console Menu Interaktif (start.bat / run.bat di root)
..\start.bat

# Opsi B: Manual via Artisan CLI
php artisan serve
```

Akses sistem di peramban Anda melalui: **`http://127.0.0.1:8000`**
- **Console Menu (`start.bat`)**: Dilengkapi pilihan aksi [1-5], pemantauan log request secara real-time, dan tombol matikan server langsung di dalam CMD.

---

## 🧪 Pengujian Otomatis

Proyek ini dilengkapi dengan 19 skenario pengujian komprehensif (133 assertions) yang mencakup otentikasi, izin rute, geofencing, observer cuti, dan keamanan:

```bash
php artisan test
```

Hasil uji:
```text
  Tests:    19 passed (133 assertions)
  Duration: 100% Pass
```

---

## 📄 Lisensi & Hak Cipta
Hak Cipta &copy; 2026 **PT Bank Perekonomian Rakyat Banda Raya**. Dilindungi oleh Undang-Undang.
