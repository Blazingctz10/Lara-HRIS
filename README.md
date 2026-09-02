# 🏢 HRIS & ESS Portal - PT BPR Banda Raya

<p align="center">
  <img src="public/images/logo-dark.svg" width="220" alt="BPR Banda Raya Logo">
</p>

<p align="center">
  <strong>Sistem Informasi Manajemen Sumber Daya Manusia (HRIS) & Portal Layanan Mandiri Karyawan (ESS)</strong><br>
  Dirancang khusus untuk operasional multi-cabang perbankan BPR dengan standar keamanan dan kepatuhan tinggi.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Filament-v5.x-F59E0B?style=for-the-badge&logo=filament&logoColor=white" alt="Filament v5">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/TailwindCSS-v4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge" alt="License">
</p>

---

## 🌟 Ringkasan Sistem

Aplikasi ini menyediakan 2 panel terpisah yang terintegrasi:
1. **Admin & HR Panel (`/admin`)**: Untuk Super Admin, HR Officer, dan Pimpinan Cabang (Pincab) dalam mengelola seluruh data kepegawaian, persetujuan, rekap kehadiran, dan penggajian.
2. **Employee Self Service (ESS) Portal (`/portal`)**: Untuk seluruh staf dan karyawan dalam melakukan presensi online berbasis geofencing GPS, pengajuan cuti, klaim reimbursement, lembur (SPL), hingga unduh slip gaji.

---

## 🚀 Fitur Unggulan

### 1. 👥 Manajemen Kepegawaian (HR Core)
- **Data Induk Karyawan:** Profil lengkap, NIK, riwayat pendidikan, keluarga, dan dokumen identitas.
- **Monitoring Kontrak & PKWT:** Notifikasi dan widget peringatan otomatis untuk kontrak karyawan yang akan berakhir.
- **Struktur Organisasi:** Manajemen multi-cabang (Kantor Pusat, Botania, Aviari, Bengkong), departemen, dan jabatan.
- **Pergerakan Karir (Career Movement):** Pencatatan riwayat Mutasi Antar Cabang, Promosi Jabatan, dan Demosi.
- **Surat Peringatan (SP):** Manajemen sanksi disiplin (SP 1, SP 2, SP 3) dengan masa berlaku otomatis.

### 2. 📍 Presensi & Geofencing GPS
- **Presensi Mobile / ESS:** Check-in dan Check-out berbasis koordinat GPS dengan validasi radius lokasi kantor cabang (Geofencing).
- **Integrasi Mesin Fingerprint:** Sinkronisasi log kehadiran dari mesin absensi fisik di kantor.
- **Rekap & Laporan Kehadiran:** Perhitungan otomatis keterlambatan, pulang awal, dan jam kerja efektif.

### 3. 🏖️ Manajemen Cuti & Izin
- **Pengajuan Cuti Online:** Cuti tahunan, melahirkan, menikah, duka, dll.
- **Approval Berjenjang:** Notifikasi dan persetujuan langsung dari Atasan / Pincab / HR.
- **Otomasi Saldo Cuti:** Saldo cuti tahunan otomatis berkurang saat pengajuan disetujui dan otomatis reset per tahun.

### 4. 💰 Penggajian & Reimbursement (Payroll)
- **Kalkulasi Payroll:** Gaji pokok, tunjangan jabatan/transport, lembur, potongan BPJS Ketenagakerjaan & Kesehatan, serta PPh 21.
- **Slip Gaji Digital:** Cetak dan unduh slip gaji langsung dari portal karyawan.
- **Klaim Reimbursement:** Pengajuan klaim biaya kesehatan / operasional dengan upload bukti struk/kwitansi.
- **Surat Perintah Lembur (SPL):** Pengajuan lembur terencana dengan perhitungan upah lembur otomatis.

### 5. 📢 Pengumuman & Dashboard Interaktif
- **Broadcast Pengumuman:** Publikasi surat edaran internal per cabang atau nasional.
- **Dashboard Statistik:** Visualisasi chart kehadiran, rasio gender, komposisi jabatan, dan tren absensi harian.

---

## 🏛️ Jaringan Kantor & Akses Demo Default

> **Semua Password Default Akun Demo:** `password`

| No | Kantor / Cabang | Role | Email Akses | Akses Panel |
|:---|:---|:---|:---|:---|
| **1** | **Kantor Pusat (Nagoya Hill)** | Super Admin | `admin@bprbandaraya.com` | `/admin` (Nasional) |
| | | HR Officer | `ratna.dewi@bprbandaraya.com` | `/admin` |
| **2** | **Kantor Cabang Botania** | Pincab | `pincab.botania@bprbandaraya.com` | `/admin` (Scoped) |
| | | Teller | `teller.botania@bprbandaraya.com` | `/portal` (ESS) |
| | | Customer Service | `cs.botania@bprbandaraya.com` | `/portal` (ESS) |
| | | Account Officer | `ao.botania@bprbandaraya.com` | `/portal` (ESS) |
| **3** | **Kantor Cabang Aviari** | Pincab | `pincab.aviari@bprbandaraya.com` | `/admin` (Scoped) |
| | | Teller | `teller.aviari@bprbandaraya.com` | `/portal` (ESS) |
| **4** | **Kantor Cabang Bengkong** | Pincab | `pincab.bengkong@bprbandaraya.com` | `/admin` (Scoped) |
| | | Teller | `teller.bengkong@bprbandaraya.com` | `/portal` (ESS) |

---

## 🛠️ Panduan Instalasi Lokal

### Prasyarat:
- PHP >= 8.2 (Direkomendasikan PHP 8.3)
- Composer 2.x
- Node.js >= 18.x & NPM
- Database: MySQL / PostgreSQL / SQLite

### Langkah-langkah:

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/ManzZ67/HRIS-BPR.git
   cd HRIS-BPR
   ```

2. **Install Dependensi Backend & Frontend:**
   ```bash
   composer install
   npm install
   ```

3. **Setup Konfigurasi `.env`:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database & Jalankan Migrasi + Seeder:**
   ```bash
   # Buat database terlebih dahulu (contoh: SQLite atau MySQL)
   php artisan migrate:fresh --seed
   ```

5. **Build Asset Frontend:**
   ```bash
   npm run build
   # atau untuk mode development:
   npm run dev
   ```

6. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka di browser:
   - Halaman Utama: [http://localhost:8000](http://localhost:8000)
   - Admin Panel: [http://localhost:8000/admin](http://localhost:8000/admin)
   - Portal Karyawan (ESS): [http://localhost:8000/portal](http://localhost:8000/portal)

---

## ☁️ Deployment ke Cloud (Railway / Render / VPS)

### Environment Variables Wajib untuk Production:
```env
APP_NAME="PT BPR BANDA RAYA HRIS"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:PEp9jkmJ2IOodTtVenZ0gTVfF3RT3OUSwlks0tId5Qg=
APP_URL=https://nama-domain-anda.up.railway.app
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

## 🔒 Keamanan & Praktik Terbaik
- **Role-Based Access Control (RBAC):** Pincab hanya memiliki otorisasi melihat data karyawan di cabangnya masing-masing.
- **OWASP Security Headers:** Proteksi bawaan dari Clickjacking, MIME-sniffing, XSS, dan HSTS via middleware `SecurityHeaders`.
- **Geofencing Haversine Formula:** Kalkulasi jarak real-time dengan presisi tinggi untuk mencegah kecurangan absensi dari luar area kantor.

---

<p align="center">
  Dikembangkan untuk <strong>PT BPR Banda Raya</strong> &copy; 2026. All Rights Reserved.
</p>
